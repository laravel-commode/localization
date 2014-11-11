<?php
    namespace LaravelCommode\Localization;

    use Illuminate\Support\Contracts\ArrayableInterface;
    use Illuminate\Support\Contracts\JsonableInterface;

    class LangSource implements ArrayableInterface, JsonableInterface
    {
        protected $path;
        protected $name;

        /**
         * @var LangSource|null
         */
        protected $parent = null;

        protected $strings = [];

        protected $isSubSource = false;


        protected $inherited = null;



        /**
         * @var array
         */
        private $langs = [];

        public function __construct($name, $path, array $langs, LangSource $parent = null)
        {
            $this->name = $name;
            $this->path = $path;

            foreach ($langs as $lang) {
                $this->strings[$lang] = [];
            }

            $this->parent = $parent;
            $this->isSubSource = is_null($parent);
        }

        public function isInherited()
        {
            return !is_null($this->inherited);
        }

        /**
         * @return string
         */
        public function getInherited()
        {
            return $this->inherited;
        }

        /**
         * @return string
         */
        public function getAllInherits()
        {
            $inherits = [];

            if ($this->isInherited()) {
                $path = (is_null($this->parent) ? "" : ".".$this->parent->getParent()).$this->getPath();
                $inherits[$path] = $this->getInherited();
            }

            foreach($this->strings as $lang => $values)
            {
                foreach($values as $value)
                {
                    if ($value instanceof LangSource) {
                        $inherits = array_merge($inherits, $value->getAllInherits());
                    }
                }
            }

            return $inherits;
        }

        /**
         * @param string $inherited
         */
        public function setInherited($inherited)
        {
            $this->inherited = $inherited;
        }

        public function isSubSource()
        {
            return $this->isSubSource;
        }

        public function getParent()
        {
            return $this->parent;
        }

        /**
         * @return mixed
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
         * @return mixed
         */
        public function getPath()
        {
            return $this->path;
        }

        /**
         * @param mixed $path
         */
        public function setPath($path)
        {
            $this->path = $path;
        }

        /**
         * @return array
         */
        public function getStrings()
        {
            return $this->strings;
        }

        public function getLangValues($lang)
        {
            $result = [];

            if (!isset($this->strings[$lang])) {
                return $result;
            }

            foreach($this->strings[$lang] as $key => $value)
            {
                if ($value instanceof LangSource) {
                    $result[$key] = $value->getLangValues($lang);
                } else {
                    $result[$key] = $value;
                }
            }

            return $result;
        }

        public function toArray($lang = null)
        {
            $result = [];

            if (is_null($lang)) {

                foreach(array_keys($this->strings) as $lang)
                {
                    $result[$lang] = $this->getLangValues($lang);
                }

                return $result;
            }

            return $this->getLangValues($lang);
        }

        public function toJson($options = 0)
        {
            return json_encode($this->toArray());
        }

        /**
         * @param $lang
         * @param $key
         * @param $value
         * @internal param array $strings
         */
        public function setValue($lang, $key, $value)
        {
            if (is_string($value)) {
                $value = mb_convert_encoding($value, 'UTF-8');
            }
            $this->strings[$lang][$key] = $value;
        }

        /**
         * @param $values
         * @internal param $lang
         * @internal param $key
         * @internal param $value
         */
        public function setValues($values)
        {
            foreach ($values as $lang => $keyPairs) {
                foreach ($keyPairs as $key => $value)
                {
                    $this->setValue($lang, $key, $value);
                }
            }
        }

        public function buildForLang($lang, $array = [])
        {
            foreach($array as $k => $v) {
                if (!is_array($v)) {
                    $this->setValue($lang, $k, $v);
                } else {
                    $langSource = new self($k, trim($this->path.'.'.$k, '.'), $this->langs, $this);
                    $this->addSubsource($lang, $k, $langSource);
                    $langSource->buildForLang($lang, $v);
                }
            }
        }

        public function addSubsource($lang, $name, LangSource $langSource)
        {
            $this->setValue($lang, $name, $langSource);
        }

    }