<?php
    namespace LaravelCommode\Localization\XMLReader;


    use LaravelCommode\Localization\XMLReader\Writer;
    use LaravelCommode\Localization\Commands\XMLLang;
    use LaravelCommode\Localization\Interfaces\IProcessor;
    use LaravelCommode\Localization\Interfaces\IWorker;
    use LaravelCommode\Localization\Processor;
    use LaravelCommode\Localization\LaraReader\Writer as LaraWriter;
    use LaravelCommode\Localization\LaraReader\Reader as LaraReader;

    class Worker implements IWorker
    {
        private $mode;
        private $path;

        /**
         * @var \LaravelCommode\Localization\Interfaces\IProcessor|null
         */
        private $processor = null;

        public function __construct($mode, $path)
        {
            $this->mode = $mode;
            $this->path = $path;
        }

        private function makeXMLToLara()
        {
            $reader = new \LaravelCommode\Localization\XMLReader\Reader($this->path);
            return new Processor($reader, new LaraWriter());
        }

        private function makeLaraToXML()
        {
            $reader = new LaraReader();
            return new Processor($reader, new Writer(null, $this->path));
        }

        /**
         * @return \LaravelCommode\Localization\Interfaces\IProcessor
         */
        public function getProcessor()
        {
            if (is_null($this->processor))
            {
                switch($this->mode)
                {
                    case XMLLang::MODE_READ_MAP:
                        $this->processor = $this->makeXMLToLara();
                        break;
                    case XMLLang::MODE_READ_LARA:
                        $this->processor = $this->makeLaraToXML();
                        break;
                }
            }

            return $this->processor;
        }
    }