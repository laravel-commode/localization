<?php
    namespace LaravelCommode\Localization\LaraReader;

    use Illuminate\Filesystem\Filesystem;
    use LaravelCommode\Localization\Interfaces\IWriter;
    use LaravelCommode\Localization\LangCat;
    use LaravelCommode\Localization\LangSource;
    use LaravelCommode\Localization\Structured;

    /**
     * Created by PhpStorm.
     * User: madman
     * Date: 01.10.14
     * Time: 2:35
     */
    class Writer implements IWriter
    {
        /**
         * @var \LaravelCommode\Localization\Structured|null
         */
        private $structured = null;

        protected $delayedWrite = [];

        /**
         * @var Filesystem
         */
        private $filesystem;

        public function __construct(Structured $structured = null)
        {
            $this->structured = $structured;
            $this->filesystem = new Filesystem();
        }

        public function setStructured(Structured $structured)
        {
            $this->structured = $structured;
            return $this;
        }

        public function write()
        {
            if (is_null($this->structured)) {
                throw new \Exception("No structure initialized");
            }

            foreach($this->structured->getLangs() as $lang)
            {
                $this->readCatalogs($this->structured->getCats(), $lang);
                $this->readSources($this->structured->getSources(), $lang, "");
            }

            $this->writeInherited();

            return $this->structured;
        }

        protected function readSubSources(LangSource $source, $lang, &$array = [])
        {
            if (count($sourceValues = $source->getStrings()))
            {
                foreach($sourceValues[$lang] as $sourceKey => $sourceValue)
                {
                    if ($sourceValue instanceof LangSource) {
                        $array[$sourceKey] = [];
                        $this->readSubSources($sourceValue, $lang, $array[$sourceKey]);
                    } else {
                        $array[$sourceKey] = $sourceValue;
                    }
                }
            }

            return $array;
        }

        /**
         * @param LangSource[] $sources
         * @param $lang
         * @param $curPath
         */
        protected function readSources($sources, $lang, $curPath = '')
        {
            foreach($sources as $source)
            {
                $sourceName = $source->getName();

                if (count($inh = $source->getAllInherits())) {
                    foreach($inh as $key => $value)
                    {
                        $keyName = $sourceName.$key;
                        $inh[$keyName] = $value;
                        unset($inh[$key]);
                    }

                    $this->delayedWrite = array_merge($this->delayedWrite, $inh);
                }

                $sourcePath = app_path("lang/{$lang}/".
                    ($curPath == '' ? '' : $curPath.'/')."{$sourceName}.php");

                $resArray = $source->getLangValues($lang);


                $export = !empty($resArray) ?
                    "\n\t".$this->format_php_export(var_export($resArray, 1)) :
                    "array()";

                if (!$this->filesystem->exists($dir = dirname($sourcePath))) {
                    $this->filesystem->makeDirectory($dir, 511, true, true);
                }


                $this->filesystem->put($sourcePath, $vr = "<?php return {$export};");
            }
        }

        protected function format_php_export($arrayRep)
        {
            $arrayRep = preg_replace('/[ ]{2}/', "\t", $arrayRep);
            $arrayRep = preg_replace("/\\=\\>[ \n\t]+array[ ]+\\(/", '=> array(', $arrayRep);
            return $arrayRep = preg_replace("/\n/", "\n\t", $arrayRep);
        }

        /**
         * @param LangCat[] $cats
         * @param $lang
         * @param null $path
         */
        protected function readCatalogs($cats, $lang, $path = null)
        {
            foreach ($cats as $cat)
            {
                $catName = $cat->getName();
                $curPath = trim($path."/".$catName, "/");
                $langPath = app_path("lang/{$lang}/{$curPath}");

                $this->filesystem->makeDirectory($langPath, 511, true, true);

                if ($cat->hasCats())
                {
                    $this->readCatalogs($cat->getCats(), $lang, $curPath);
                }

                if (count($sources = $cat->getSources()) > 0)
                {
                    $this->readSources($sources, $lang, $curPath);
                }
            }
        }
        private function setInherited(&$array, $path, $value)
        {
            $path = explode('.', $path);
            $cpath = array_pop($path);

            if (count($path)) {
                $this->setInherited($array[$cpath], $cpath, $value);
            } else {
                $array = array_merge($array, $value);
            }

            return $array;
        }

        private function writeInherited()
        {
            foreach($this->delayedWrite as $inheritant => $inheritantLocation)
            {
                foreach($this->structured->getLangs() as $lang) {
                    $dName = $sourcePath = explode('.', $inheritant)[0];

                    $_inheritantLocation = trans($inheritantLocation, [], 'messages', $lang);
                    $_dName = trans($dName, [], 'messages', $lang);

                    $this->setInherited($_dName, $inheritant, $_inheritantLocation);



                    $export = !empty($_dName) ?
                        "\n\t".$this->format_php_export(var_export($_dName, 1)) :
                        "array()";


                    $sourcePath = $dName.".php";
                    $sourcePath = app_path('lang/'.$lang."/".$sourcePath);


                    if (!$this->filesystem->exists($dir = dirname($sourcePath))) {
                        $this->filesystem->makeDirectory($dir, 511, true, true);
                    }


                    $this->filesystem->put($sourcePath, $vr = "<?php return {$export};");
                }

            }
        }
    } 