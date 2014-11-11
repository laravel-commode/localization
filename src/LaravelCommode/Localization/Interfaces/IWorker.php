<?php
    namespace LaravelCommode\Localization\Interfaces;
    use LaravelCommode\Localization\Interfaces\IProcessor;

    /**
     * Created by PhpStorm.
     * User: madman
     * Date: 01.10.14
     * Time: 17:51
     */
    interface IWorker
    {
        public function __construct($mode, $path);

        /**
         * @return IProcessor
         */
        public function getProcessor();
    } 