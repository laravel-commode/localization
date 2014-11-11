<?php
    namespace LaravelCommode\Localization\Interfaces;
    use LaravelCommode\Localization\Interfaces\IWriter;
    use LaravelCommode\Localization\Interfaces\IStructured;
    use LaravelCommode\Localization\Interfaces\IReader;

    /**
     * Created by PhpStorm.
     * User: madman
     * Date: 01.10.14
     * Time: 17:43
     */
    interface IProcessor
    {
        public function __construct(IReader $reader, IWriter $writer);

        /**
         * @return IStructured
         */
        public function extractStructure();

        /**
         * @return IStructured|bool
         */
        public function process();
    } 