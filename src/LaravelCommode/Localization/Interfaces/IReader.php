<?php
    namespace LaravelCommode\Localization\Interfaces;

    use LaravelCommode\Localization\Structured;

    /**
     * Created by PhpStorm.
     * User: madman
     * Date: 01.10.14
     * Time: 2:53
     */
    interface IReader 
    {
        public function __construct($file);

        /**
         * @return \LaravelCommode\Localization\Structured
         */
        public function read();

        public function validate();
    } 