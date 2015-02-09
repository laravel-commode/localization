<?php
    namespace LaravelCommode\Localization;

    class LocalizationServiceProviderTest extends \PHPUnit_Framework_TestCase
    {
        protected function getAppMock(array $methods = [])
        {
            return $this->getMock('Illuminate\Foundation\Application', $methods);
        }

        protected function getServiceMock($app, array $methods = [])
        {
            return $this->getMock('LaravelCommode\Localization\LocalizationServiceProvider', $methods, [$app]);
        }

        protected function getServiceInstance($app)
        {
            return new LocalizationServiceProvider($app);
        }

        public function testProvides()
        {
            $instance = $this->getServiceInstance($app = $this->getAppMock(), ['provides']);

            $this->assertSame(
                ['commode.localization.registry', 'LaravelCommode\Localization\Interfaces\ILangRegistry'],
                $instance->provides()
            );
        }

        public function testBoot()
        {
            $instance = $this->getServiceMock($app = $this->getAppMock(), ['package', 'commands']);

            $instance->expects($this->once())->method('commands');
            $instance->expects($this->once())->method('package');

            $instance->boot();
        }

        public function testRegistering()
        {
            $instance = $this->getServiceInstance($app = $this->getAppMock(['singleton', 'bindShared', 'make']));

            $app->expects($this->once())->method('singleton')->with(
                $registryInterface = 'LaravelCommode\Localization\Interfaces\ILangRegistry',
                'LaravelCommode\Localization\LangRegistry\LangRegistry'
            );

            $makeReturn = uniqid();

            $app->expects($this->once())->method('bindShared')->with(
                'commode.localization.registry',
                $this->callback(function(\Closure $callable) use ($makeReturn) {
                    $this->assertSame($makeReturn, $callable());
                    return $callable instanceof \Closure;
                })
            );

            $app->expects($this->exactly(3))->method('make')->with($registryInterface)->will(
                $this->returnValue($makeReturn)
            );

            $reflectionInstance = new \ReflectionClass($instance);
            $reflectionMethod = $reflectionInstance->getMethod('registering');
            $reflectionMethod->setAccessible(true);

            $reflectionMethod->invoke($instance);
        }

        public function testLaunching()
        {
            $instance = $this->getServiceInstance($app = $this->getAppMock());

            $reflectionInstance = new \ReflectionClass($instance);
            $reflectionMethod = $reflectionInstance->getMethod('launching');
            $reflectionMethod->setAccessible(true);

            $reflectionMethod->invoke($instance);
        }
    }
