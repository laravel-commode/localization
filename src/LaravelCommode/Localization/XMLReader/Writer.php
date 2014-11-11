<?php
    namespace LaravelCommode\Localization\XMLReader;
    use LaravelCommode\Localization\Interfaces\ICatContainer;
    use LaravelCommode\Localization\Interfaces\ISourceContainer;
    use LaravelCommode\Localization\Interfaces\IWriter;
    use LaravelCommode\Localization\Structured;
    use SimpleXMLElement;

    /**
     * Created by PhpStorm.
     * User: madman
     * Date: 01.10.14
     * Time: 2:34
     */
    class Writer implements IWriter
    {
        protected $xml;
        /**
         * @var \LaravelCommode\Localization\Structured
         */
        private $structured;
        /**
         * @var string
         */
        private $path;

        public function __construct(Structured $structured = null, $path = '')
        {
            $this->structured = $structured;
            $this->path = $path;
        }

        /**
         * @param \LaravelCommode\Localization\Structured $structured
         * @return $this
         */
        public function setStructured(Structured $structured)
        {
            $this->structured = $structured;
            return $this;
        }

        protected function setLanguages(SimpleXMLElement $xml)
        {
            $xml->addChild('langs');

            foreach($this->structured->getLangs() as $lang)
            {
                $xml->langs->addChild('lang', $lang);
            }
        }

        /**
         * @return \LaravelCommode\Localization\Structured|bool
         */
        public function write()
        {
            $this->xml = new SimpleXMLElement("<locals></locals>");
            $this->setLanguages($this->xml);
            $this->setData($this->xml);

            $path = realpath($this->path);

            if (!\File::exists($dirpath = dirname($path)))
            {
                \File::makeDirectory($dirpath, 777, true);
            }

            $dom = dom_import_simplexml($this->xml)->ownerDocument;
            $dom->formatOutput = true;
            //$dom->preserveWhiteSpace = false;
            $doc = html_entity_decode($dom->saveXML(), ENT_NOQUOTES, 'UTF-8');

            $doc = (preg_replace_callback('/(\n)([\ ]{2,})/is', function ($t) {
                list($template, $newLine, $spaces) = $t;
                return $newLine.$spaces.$spaces;
            }, $doc));

            \File::put($path."/default.xml", $doc);
        }

        private function setData(SimpleXMLElement $xml)
        {
            $xml->addChild('data');
            $xml->data->addChild('cats');
            $xml->data->addChild('sources');
            $this->scanCatsTo($this->structured, $xml->data->cats);
            $this->scanSourcesTo($this->structured, $xml->data->sources);

        }

        private function scanSourcesTo(ISourceContainer $sourceContainer, SimpleXMLElement $sources)
        {
            foreach($sourceContainer->getSources() as $source) {
                if (count($path = $sources->xpath('//sources/source[@name="'.$source->getName().'"]'))) {
                    $newSource = $path[0];
                } else {
                    $newSource = $sources->addChild('source');
                    $newSource->addAttribute('name', $source->getName());
                }

                foreach($this->structured->getLangs() as $lang) {
                    $this->setValues($newSource, $lang, $source->getLangValues($lang));
                }
            }
        }

        private function scanCatsTo(ICatContainer $catContainer, SimpleXMLElement $cats)
        {
            foreach($catContainer->getCats() as $cat) {

                $xp = $cats->xpath('//cats/cat[@name="'.$cat->getName().'"]');

                if (count($xp) == 0) {
                    $newCat = $cats->addChild('cat');
                    $newCat->addAttribute('name', $cat->getName());
                    $newCat->addChild('cats');
                    $newCat->addChild('sources');
                } else {
                    $newCat = $xp[0];
                }

                if ($cat->hasCats()) {
                    $this->scanCatsTo($cat, $newCat->cats);
                }

                $this->scanSourcesTo($cat, $newCat->sources);
            }

        }

        private function setValues(SimpleXMLElement $newSource, $lang, $values)
        {
            $elementName = $newSource->getName();
            $_nName = $newSource['name']->__toString();

            foreach($values as $k => $v)
            {

                if (!is_array($v)) {
                    $xpPath = '//'.$elementName.'[@name="'.$_nName.'"]/value[@name="'.$k.'"]' ;
                    $xpLPath = $xpPath.'/'.$lang;
                    $xp  = $newSource->xpath($xpPath);
                    $xpLang  = $newSource->xpath($xpLPath);

                    $ss = $newSource->asXML();

                    if (count($xp) == 0) {
                        $value = $newSource->addChild('value');
                        $value->addAttribute('name', $k);
                    } else {
                        $value = $xp[0];
                    }

                    if (count($xpLang) == 0) {
                        $value->addChild($lang, $v);
                    }

                } else {

                    switch($elementName)
                    {
                        case 'source':
                            $xp = $newSource->xpath('//source/subsource[@name="'.$k.'"]');

                            if (count($xp) == 0) {
                                $subcource = $newSource->addChild('subsource');
                                $subcource->addAttribute('name', $k);
                            } else {
                                $subcource = $xp[0];
                            }

                            $this->setValues($subcource, $lang, $v);

                            break;
                        case 'subsource':
                            $xp = $newSource->xpath('//subsource/subsource[@name="'.$k.'"]');


                            if (count($xp) == 0) {
                                $subcource = $newSource->addChild('subsource');
                                $subcource->addAttribute('name', $k);
                            } else {
                                $subcource = $xp[0];
                            }

                            $this->setValues($subcource, $lang, $v);

                            break;
                    }
                }
            }
        }
    }