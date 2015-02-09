<?php
    namespace LaravelCommode\Localization;
    use LaravelCommode\Localization\Interfaces\ICatContainer;
    use LaravelCommode\Localization\Interfaces\IStructured;
    use LaravelCommode\Localization\LangCat;
    use LaravelCommode\Localization\LangSource;
    use string;

    /**
     * Created by PhpStorm.
     * User: madman
     * Date: 30.09.14
     * Time: 0:53
     */
    class Structured implements IStructured
    {
        /**
         * @var string[]
         */
        private $langs = [];

        /**
         * @var LangCat[]
         */
        private $cats = [];

        /**
         * @var LangSource[]
         */
        private $sources = [];

        /**
         * @return array
         */
        public function getLangs()
        {
            return $this->langs;
        }

        /**
         * @param string[] $langs
         * @return $this
         */
        public function setLangs(array $langs)
        {
            $this->langs = $langs;
            return $this;
        }

        public function addLang($lang)
        {
            $this->langs[] = $lang;
            return $this;
        }

        public function getSources()
        {
            return $this->sources;
        }

        /**
         * @param LangSource[] $sources
         * @return array
         */
        public function setSources(array $sources)
        {
            foreach($sources as $source)
            {
                $this->addSource($source);
            }

            return $this;
        }

        /**
         * @param LangSource $source
         * @return array
         */
        public function addSource(LangSource $source)
        {
            $this->sources[$source->getName()] = $source;
            return $this;
        }

        public function getSource($sourceName)
        {
            return $this->hasSource($sourceName) ? $this->sources[$sourceName] : null;
        }

        public function hasSource($sourceName)
        {
            return isset($this->sources[$sourceName]);
        }

        /**
         * @return LangCat[]
         */
        public function getCats()
        {
            return $this->cats;
        }

        /**
         * @param LangCat[] $cats
         * @return $this
         */
        public function setCats(array $cats)
        {
            $this->cats = [];

            foreach($cats as $cat)
            {
                $this->cats[$cat->getName()] = $cat;
            }

            return $this;
        }

        public function addCat(ICatContainer $cat)
        {
            $this->cats[$cat->getName()] = $cat;
            return $this;
        }

        public function getLastCat()
        {
            return end($this->cats);
        }

        public function getName()
        {
            return "";
        }

        /**
         * @return bool
         */
        public function hasCats()
        {
            return count($this->getCats()) > 0;
        }

        /**
         * @param $name
         * @return bool
         */
        public function hasCat($name)
        {
            return isset($this->cats[$name]);
        }

        /**
         * @param $name
         * @return ICatContainer
         */
        public function getCat($name)
        {
            return $this->hasCat($name) ? $this->cats[$name] : null;
        }
    }