<?php
    namespace LaravelCommode\Localization\XMLReader;



    use LaravelCommode\Localization\Interfaces\ICatContainer;
    use LaravelCommode\Localization\Interfaces\IReader;
    use LaravelCommode\Localization\Interfaces\ISourceContainer;
    use LaravelCommode\Localization\LangCat;
    use LaravelCommode\Localization\LangSource;
    use LaravelCommode\Localization\Structured;
    use Exception;
    use File;
    use SimpleXMLElement;

    class Reader implements IReader
    {
        /**
         * @var SimpleXMLElement
         */
        private $xml;
        private $filePath;
        /**
         * @var \LaravelCommode\Localization\Structured
         */
        private $structured = null;
        private $langs = [];

        /**
         * @param $filePath
         */
        public function __construct($filePath)
        {
            $this->filePath = realpath($filePath);
        }

        private function validateLangs(SimpleXMLElement $xml)
        {
            if (count($xml->langs) == 0 || count($xml->langs->lang) == 0)
            {
                throw new Exception('No langs specified');
            }

            if (count($xml->data) == 0)
            {
                throw new Exception('No data specified');
            }
        }

        public function validate()
        {
            try {
                $this->validateLangs($this->xml);
            } catch (Exception $e) {
                return false;
            }

            return true;
        }

        protected  function readFolder($path)
        {
            $folderName = realpath($path);

            foreach (glob($folderName."/*.xml") as $fileName) {
                $this->readFile($fileName);
            }
        }


        protected function readFile($fileName)
        {
            $this->xml = new SimpleXMLElement(File::get($fileName));


            $this->validateLangs($this->xml);

            foreach($this->xml->langs->children() as $lang)
            {
                $this->structured->addLang($lang->__toString());
            }


            $this->extractData($this->xml->data->cats, $this->structured->getLangs(), $this->structured);
            $this->readSources($this->xml->data->sources, $this->structured->getLangs(), null, "");
        }

        /**
         * @return \LaravelCommode\Localization\Structured
         */
        public function read()
        {
            $this->structured = new Structured();


            if (File::isDirectory($this->filePath)) {
                $this->readFolder($this->filePath);
            } else {
                $this->readFile($this->filePath);
            }

            return $this->structured;
        }

        /**
         * @return mixed
         */
        public function getXml()
        {
            return $this->xml;
        }

        /**
         * @param mixed $xml
         */
        public function setXml($xml)
        {
            $this->xml = $xml;
        }

        protected  function readSubSources($dataSource, $langs, LangSource $source = null, $path = null)
        {
            if (count($dataSource->subsource))
            {
                foreach($dataSource->subsource as $subSource)
                {
                    $subSourceName = $subSource['name']."";
                    $tmpPath = trim($path.'.'.$subSourceName, '.');

                    $classSource = new LangSource($subSourceName, $tmpPath, $langs, $source);

                    if (!is_null($subSource['inherits']))
                    {
                        $classSource->setInherited($subSource['inherits']->__toString());
                    }

                    foreach($subSource->value as $value)
                    {
                        $valKey = $value['name']."";

                        foreach($langs as $lang)
                        {
                            $val = $value->{$lang}."";
                            $classSource->setValue($lang, $valKey, $val);
                        }
                    }

                    if (!is_null($source))
                    {
                        foreach($langs as $lang)
                        {
                            $source->addSubsource($lang, $subSourceName, $classSource);
                        }
                    }

                    $this->readSubSources($subSource, $langs, $classSource, $tmpPath);
                }
            }
        }

        public function readSources($sources, $langs, ISourceContainer $sourceContainer = null, $tmpPath = null)
        {
            if ($sourceContainer == null) {
                $sourceContainer = $this->structured;
            }

            foreach($sources->source as $source)
            {
                $sourceName = $source['name']."";

                if ($sourceContainer->hasSource($sourceName)) {
                    $classSource = $sourceContainer->getSource($sourceName);
                } else {
                    $classSource = new LangSource($sourceName, $tmpPath, $this->langs);
                }


                if (!is_null($source['inherits']))
                {
                    $classSource->setInherited($source['inherits']->__toString());
                }


                foreach($source->value as $value)
                {
                    $valKey = $value['name']."";

                    foreach($langs as $lang)
                    {
                        $val = $value->{$lang}."";
                        $classSource->setValue($lang, $valKey, $val);
                    }
                }

                $this->readSubSources($source, $langs, $classSource, $tmpPath);
                $sourceContainer->addSource($classSource);
            }
        }

        protected function extractData($data, $langs, ICatContainer $parentCat = null, $path = null)
        {
            foreach($data->cat as $cat)
            {
                $catName = $cat['name']."";
                $tmpPath = trim($path.'.'.$catName, '.');

                if (!is_null($parentCat)) {
                    if ($parentCat->hasCat($catName)) {
                        $curCat = $parentCat->getCat($catName);
                    } else {
                        $curCat = new LangCat($catName, $parentCat);
                    }
                } else {
                    $curCat = new LangCat($catName, $parentCat instanceof LangCat ? $parentCat : null);
                }

                $this->readSources($cat->sources, $langs, $curCat, $tmpPath);

                if (isset($cat->cats) && count($cat->cats->cat) > 0) {
                    $this->extractData($cat->cats, $this->structured->getLangs(), $curCat, $tmpPath);
                }

                if (!($parentCat instanceof LangCat)) {
                    $this->structured->addCat($curCat);
                } else {
                    $parentCat->addCat($curCat);
                }
            }



            return $this->structured;
        }
    } 