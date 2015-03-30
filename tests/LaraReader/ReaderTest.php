<?php
    namespace LaravelCommode\Localization\LaraReader;

    use Illuminate\Support\Facades\Facade;
    use LaravelCommode\Localization\Interfaces\ISourceContainer;
    use PHPUnit_Framework_MockObject_MockObject as MockObject;

    class ReaderTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @var Reader
         */
        private $instance;

        /**
         * @var MockObject
         */
        private $appMock;

        /**
         * @var string
         */
        private $fakePath;

        /**
         * @var ISourceContainer|MockObject
         */
        private $sourceMock;

        /**
         * @var array
         */
        private $validation;

        /**
         * @return MockObject
         */
        protected function getAppMock()
        {
            $app = $this->getMock(
                'Illuminate\Foundation\Application',
                ['make']
            );

            Facade::setFacadeApplication($app);

            return $app;
        }

        protected function setUp()
        {
            $this->sourceMock = $this->getSourceMock();
            $this->appMock = $this->getAppMock();
            $this->fakePath = realpath(__DIR__."/../FakeLaraApp");
            $this->validation = include __DIR__."/../FakeLaraApp/lang/en/validation.php";
        }

        protected function getSourceMock()
        {
            return $this->getMock('LaravelCommode\Localization\Interfaces\ISourceContainer');
        }

        protected function getInstance()
        {
            return $this->instance = $this->instance ?: new Reader();
        }

        public function testConstruct()
        {
            $this->appMock->expects($this->once())
                ->method('make')
                ->with($this->callback(function ($path) {
                    return $path === 'path';
                }))->will($this->returnValue($this->fakePath));

            $this->getInstance();
        }

        public function testRead()
        {
            $this->appMock->expects($this->atLeastOnce())
                ->method('make')
                ->with($this->callback(function ($path) {
                    return $path === 'path';
                }))->will($this->returnValue($this->fakePath));

            $rootStructure = $this->getInstance()->read();

            $this->assertCount(2, $langs = $rootStructure->getLangs());
            $this->assertSame('en', $langs[0]);

            $this->assertTrue($rootStructure->hasSource('validation'));
            $this->assertTrue($rootStructure->hasCat('fakesub'));

            $root_FakeSubCat = $rootStructure->getCat('fakesub');
            $root_ValidationSourceValues = $rootStructure->getSource('validation')->getLangValues($langs[0]);

            $this->assertSame($this->validation, $root_ValidationSourceValues);
            $this->assertTrue($root_FakeSubCat->hasCat('fake'));

            /** dulpicate **/
            $this->assertTrue($root_FakeSubCat->hasSource('validation'));
            $this->assertTrue($root_FakeSubCat->hasCat('fake'));

            $root_FakeSubValidationSourceValues = $root_FakeSubCat->getSource('validation')->getLangValues($langs[0]);
            $this->assertSame($this->validation, $root_FakeSubValidationSourceValues);
        }

        public function testStructure()
        {
            /*
            $this->appMock->expects($this->atLeastOnce())
                ->method('make')
                ->with($this->callback(function ($path) {
                    return $path === 'path';
                }))->will($this->returnValue($this->fakePath));
            */
            //dd($this->getInstance()->read());
        }
    }
