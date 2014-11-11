<?php
    namespace LaravelCommode\Localization\Interfaces;
    use LaravelCommode\Localization\Structured;

    /**
     * Created by PhpStorm.
     * User: madman
     * Date: 01.10.14
     * Time: 2:53
     */
    interface IWriter
    {
        public function __construct(Structured $structured = null);

        /**
         * @param \LaravelCommode\Localization\Structured $structured
         * @return $this
         */
        public function setStructured(Structured $structured);

        /**
         * @return \LaravelCommode\Localization\Structured|bool
         */
        public function write();
    } 