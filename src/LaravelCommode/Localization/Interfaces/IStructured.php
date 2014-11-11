<?php
    namespace LaravelCommode\Localization\Interfaces;
    use LaravelCommode\Localization\Interfaces\ICatContainer;
    use LaravelCommode\Localization\Interfaces\ISourceContainer;

    /**
     * Created by PhpStorm.
     * User: madman
     * Date: 01.10.14
     * Time: 17:43
     */
    interface IStructured extends ISourceContainer, ICatContainer
    {
        public function getLangs();
        public function setLangs(array $langs);

        public function addLang($lang);
    } 