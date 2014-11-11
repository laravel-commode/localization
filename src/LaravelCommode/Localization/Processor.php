<?php
    namespace LaravelCommode\Localization;

    use LaravelCommode\Localization\Structured;
    use LaravelCommode\Localization\Interfaces\IProcessor;
    use LaravelCommode\Localization\Interfaces\IReader;
    use LaravelCommode\Localization\Interfaces\IWriter;

    /**
     * Created by PhpStorm.
     * User: madman
     * Date: 01.10.14
     * Time: 16:49
     */
    class Processor implements IProcessor
    {
        /**
         * @var \LaravelCommode\Localization\Interfaces\IReader|null
         */
        private $reader = null;
        /**
         * @var \LaravelCommode\Localization\Interfaces\IWriter|null
         */
        private $writer = null;

        /**
         * @var Structured
         */
        private $structured = null;

        public function __construct(IReader $reader, IWriter $writer)
        {
            $this->reader = $reader;
            $this->writer = $writer;
        }

        /**
         * @return Structured
         */
        public function extractStructure()
        {
            if (is_null($this->structured))
            {
                $this->structured = $this->reader->read();
            }

            return $this->structured;
        }

        /**
         * @return Structured|bool
         */
        public function process()
        {
            return $this->writer->setStructured($this->extractStructure())->write();
        }
    } 