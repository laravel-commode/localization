<?php
    namespace LaravelCommode\Localization\Interfaces;

    /**
     * Created by PhpStorm.
     * User: madman
     * Date: 11/8/14
     * Time: 6:46 PM
     */
    interface ILangRegistry
    {
        public function addRepository($name, $path);
        public function getRepository($name);
        public function repositoryExists($name);
        public function getAllRepositories();
    } 