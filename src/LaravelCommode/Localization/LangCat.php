<?php
    namespace LaravelCommode\Localization;

    use LaravelCommode\Localization\LangSource;
    use LaravelCommode\Localization\Interfaces\ICatContainer;
    use LaravelCommode\Localization\Interfaces\ISourceContainer;

    class LangCat implements ISourceContainer, ICatContainer
    {
        /**
         * @var string
         */
        protected $name;

        /**
         * @var LangCat[]
         */
        protected $cats = [];

        /**
         * @var LangCat|null
         */
        protected $parent;

        /**
         * @var LangSource[]
         */
        protected $sources = [];

        public function __construct($name, ICatContainer $parent = null)
        {
            $this->name = $name;
            $this->parent = $parent;
        }

        /**
         * @return string
         */
        public function getName()
        {
            return $this->name;
        }

        /**
         * @param mixed $name
         */
        public function setName($name)
        {
            $this->name = $name;
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
         */
        public function setCats(array $cats)
        {
            foreach($cats as $cat)
            {
                $this->addCat($cat);
            }
        }

        public function hasCats()
        {
            return !empty($this->cats) > 0;
        }

        public function addCat(ICatContainer $cat)
        {
            $this->cats[$cat->getName()] = $cat;
            return $this;
        }

        /**
         * @return LangSource[]
         */
        public function getSources()
        {
            return $this->sources;
        }

        /**
         * @param LangSource[] $sources
         * @return $this
         */
        public function setSources(array $sources)
        {
            foreach($sources as $source)
            {
                $this->addSource($source);
            }

            return $this;
        }

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
         * @param $name
         * @return bool
         */
        public function hasCat($name)
        {
            return isset($this->cats[$name]);
        }

        /**
         * @param $name
         * @return \LaravelCommode\Localization\Interfaces\ICatContainer
         */
        public function getCat($name)
        {
            return $this->hasCat($name) ? $this->cats[$name] : null;
        }
    }