<?php
    namespace LaravelCommode\Localization\Interfaces;
    use LaravelCommode\Localization\Interfaces\ISourceContainer;

    /**
 * Created by PhpStorm.
 * User: madman
 * Date: 10/5/14
 * Time: 2:51 AM
 */
    interface ICatContainer extends ISourceContainer
    {
        public function getName();

        /**
         * @return ICatContainer[]
         */
        public function getCats();

        /**
         * @param ICatContainer[] $cats
         */
        public function setCats(array $cats);

        /**
         * @return bool
         */
        public function hasCats();

        /**
         * @param ICatContainer $cat
         * @return $this
         */
        public function addCat(ICatContainer $cat);

        /**
         * @param $name
         * @return bool
         */
        public function hasCat($name);

        /**
         * @param $name
         * @return ICatContainer
         */
        public function getCat($name);
    } 