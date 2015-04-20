<?php
    namespace LaravelCommode\Localization\LaraReader;

    use Illuminate\Filesystem\Filesystem;
    use LaravelCommode\Localization\Interfaces\ICatContainer;
    use LaravelCommode\Localization\Interfaces\IReader;
    use LaravelCommode\Localization\Interfaces\ISourceContainer;
    use LaravelCommode\Localization\LangCat;
    use LaravelCommode\Localization\LangSource;
    use LaravelCommode\Localization\Structured;

    /**
     * Created by PhpStorm.
     * User: madman
     * Date: 01.10.14
     * Time: 2:35
     */
    class Reader implements IReader
    {
        private $baseFolder;

        /**
         * @var \LaravelCommode\Localization\Structured
         */
        private $structured;

        /**
         * @var Filesystem
         */
        private $filesystem;

        public function __construct($file = null)
        {
            $this->baseFolder = app_path('lang');
            $this->filesystem = new Filesystem();
        }

        public function validate()
        {
            return true;
        }

        protected function readLangs()
        {
            $langs = [];

            foreach(glob($this->baseFolder.'/*') as $langFolder)
            {
                $langs[] = basename($langFolder);
            }

            return $langs;
        }

        public function setSource(ISourceContainer $sourceContainer, $lang, $values, $name, $path)
        {
            $langSource = new LangSource($name, $path, $this->structured->getLangs());
            $langSource->buildForLang($lang, $values);
            $sourceContainer->addSource($langSource);
        }

        protected function setCats(ICatContainer $catContainer, $lang, $path = "")
        {
            $cPath = realpath($this->baseFolder."/{$lang}/{$path}");

            $contents = glob($cPath.'/*');

            foreach($contents as $item)
            {
                $fileName = basename($item);
                $newPath = trim($path.'/'.basename($item), '/');

                if ($this->filesystem->isDirectory($item)) {

                    if ($catContainer->hasCat($fileName)) {
                        $langCat = $catContainer->getCat($fileName);
                    } else {
                        $langCat = new LangCat($fileName);
                        $catContainer->addCat($langCat);
                    }

                    $this->setCats($langCat, $lang, $newPath);
                }
            }

        }

        protected function scanSources(ISourceContainer $sourceContainer, $langs, $path)
        {

            $sources = $sourceContainer->getSources();

            foreach($langs as $lang) {
                $pPath = str_replace('.', '/', $path);
                $contents = glob(app_path("lang/{$lang}/{$pPath}/*.php"));


                foreach($contents as $file) {
                    $sourceName = str_replace('.php', '', basename($file));

                    if (!isset($sources[$sourceName])) {
                        $sources[$sourceName] = new LangSource($sourceName, $path, $langs);
                    }

                    $sources[$sourceName]->buildForLang($lang, include $file);
                }

            }

            $sourceContainer->setSources($sources);
        }


        protected function readCats(ICatContainer $catContainer, $langs, $path = "")
        {
            foreach($catContainer->getCats() as $cat)
            {
                $oPath = trim("{$path}.{$cat->getName()}", ".");

                if ($cat->hasCats()) {
                    $this->readCats($cat, $langs, $oPath);
                }

                $this->scanSources($cat, $langs, $oPath);
            }

        }

        /**
         * @return Structured
         */
        public function read()
        {
            $this->structured = new Structured();

            $this->structured->setLangs($this->readLangs());

            foreach($langs = $this->structured->getLangs() as $lang)
            {
                $this->setCats($this->structured, $lang);
            }


            $this->readCats($this->structured, $langs);
            $this->scanSources($this->structured, $langs, "");


            return $this->structured;
        }
    }