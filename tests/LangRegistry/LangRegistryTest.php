<?php
    namespace LaravelCommode\Localization\LangRegistry;

    class LangRegistryTest extends \PHPUnit_Framework_TestCase
    {
        protected function getInstance()
        {
            return new LangRegistry();
        }

        public function testAll()
        {
            $instance = $this->getInstance();

            $this->assertEmpty($instance->getAllRepositories());
            $this->assertNull($instance->getRepository(uniqid()));
            $this->assertFalse($instance->repositoryExists(uniqid()));

            $name = uniqid();
            $path = uniqid();

            $instance->addRepository($name, $path);

            $this->assertArrayHasKey($name, $instance->getAllRepositories());
            $this->assertSame([$name => $path], $instance->getAllRepositories());
        }
    }
