<?php
    namespace LaravelCommode\Localization\Commands;

    use LaravelCommode\Localization\Interfaces\IWorker;
    use LaravelCommode\Localization\XMLReader\Worker;
    use LaravelCommode\Localization\Commands\AbstractConverter;

    class XMLLang extends AbstractConverter
    {

        /**
         * The console command name.
         *
         * @var string
         */
        protected $name = 'locs:xml';

        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'Converts XML to LaraLang and backwards.';

        /**
         * @param $mode
         * @param $path
         * @return \LaravelCommode\Localization\Interfaces\IWorker
         */
        public function getWorker($mode, $path)
        {
            return new Worker($mode, $path);
        }
    }
