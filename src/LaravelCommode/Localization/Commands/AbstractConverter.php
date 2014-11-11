<?php
    namespace LaravelCommode\Localization\Commands;

    use LaravelCommode\Localization\Interfaces\ILangRegistry;
    use LaravelCommode\Localization\Interfaces\IWorker;
    use Illuminate\Console\Command;
    use Symfony\Component\Console\Input\InputArgument;
    use Symfony\Component\Console\Input\InputOption;

    /**
     * Created by PhpStorm.
     * User: madman
     * Date: 01.10.14
     * Time: 18:18
     */
    abstract class AbstractConverter extends Command
    {
        const MODE_READ_MAP = 1;
        const MODE_READ_LARA = 2;
        /**
         * @var \LaravelCommode\Localization\Interfaces\ILangRegistry
         */
        private $registry;

        /**
         * Get the console command options.
         *
         * @return array
         */
        protected function getOptions()
        {
            return array(
                array('mode', 'm', InputOption::VALUE_OPTIONAL, 'Mode. 1 - read repository; 2 - read laravel', self::MODE_READ_MAP),
            );
        }

        /**
         * Get the console command arguments.
         *
         * @return array
         */
        protected function getArguments()
        {
            return [
                array('repoName', InputArgument::REQUIRED, 'Repository name.', null)
            ];
        }

        /**
         * Create a new command instance.
         *
         * @param \LaravelCommode\Localization\Interfaces\ILangRegistry $registry
         * @return AbstractConverter
         */
        public function __construct(ILangRegistry $registry)
        {
            parent::__construct();
            $this->registry = $registry;
        }

        /**
         * @param $mode
         * @param $path
         * @return IWorker
         */
        abstract public function getWorker($mode, $path);

        /**
         * Execute the console command.
         *
         * @return mixed
         */
        public function fire()
        {
            if (!$this->registry->repositoryExists($repositoryName = $this->argument('repoName'))) {
                $this->error('Lang repository "'.$repositoryName.'" is not registered. Check out the manual');
                return;
            }

            $repositoryPath = $this->registry->getRepository($repositoryName);
            $mode = (int)$this->option('mode');

            if (!in_array($mode, [self::MODE_READ_LARA, self::MODE_READ_MAP]))
            {
                $this->error('Unknown mode');
                return;
            }

            $worker = $this->getWorker($mode, $repositoryPath);
            $processor = $worker->getProcessor();
            $processor->process();
        }
    
    } 