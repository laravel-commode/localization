<?php
    namespace LaravelCommode\Localization\Interfaces;
    use LaravelCommode\Localization\LangSource;

    /**
     * Created by PhpStorm.
     * User: madman
     * Date: 10/5/14
     * Time: 2:26 AM
     */
    interface ISourceContainer
    {
        /**
         * @return LangSource[]
         */
        public function getSources();

        /**
         * @param \LaravelCommode\Localization\LangSource[] $sources
         * @return $this
         */
        public function setSources(array $sources);

        public function addSource(LangSource $source);

        public function getSource($sourceName);
        public function hasSource($sourceName);
    } 