<?php
    namespace LaravelCommode\Localization\LangRegistry;
    use LaravelCommode\Localization\Interfaces\ILangRegistry;

    /**
     * Created by PhpStorm.
     * User: madman
     * Date: 11/8/14
     * Time: 6:47 PM
     */
    class LangRegistry implements ILangRegistry
    {
        protected $repositories = [];

        public function addRepository($name, $path)
        {
            $this->repositories[$name] = $path;
            return $this;
        }

        public function getRepository($name)
        {
            return isset($this->repositories[$name]) ? $this->repositories[$name] : null;
        }

        public function repositoryExists($name)
        {
            return isset($this->repositories[$name]) ? $this->repositories[$name] : null;
        }

        public function getAllRepositories()
        {
            return $this->repositories;
        }
    }