<?php
    namespace LaravelCommode\Localization;

    class LangCatTest extends \PHPUnit_Framework_TestCase
    {
        protected function getInstance($catName)
        {
            return new LangCat($catName);
        }

        protected function getICatContainerMock()
        {
            return $this->getMock('LaravelCommode\Localization\Interfaces\ICatContainer', [], [], '', 0);
        }

        protected function getLangSourceMock($constructorArgs = [])
        {
            return $this->getMock(
                'LaravelCommode\Localization\LangSource', [], $constructorArgs, '', count($constructorArgs) > 0
            );
        }

        public function testConstruct()
        {
            $name = uniqid();
            $instance = $this->getInstance($name);

            $this->assertSame($name, $instance->getName());
        }

        public function testName()
        {
            $instance = $this->getInstance($name = uniqid());
            $this->assertSame($name, $instance->getName());
            $newName = uniqid();
            $instance->setName($newName);
            $this->assertSame($newName, $instance->getName());
        }

        public function testCats()
        {
            $instance = $this->getInstance($name = uniqid());

            $this->assertFalse($instance->hasCats());
            $this->assertNull($instance->getCat(uniqid()));

            $cat1 = $this->getICatContainerMock();
            $cat1->expects($this->once())->method('getName')->will($this->returnValue($catName1 = uniqid()));

            $instance->setCats([$cat1]);
            $this->assertArrayHasKey($catName1, $instance->getCats());
            $this->assertSame($cat1, $instance->getCat($catName1));

            $cat2 = $this->getICatContainerMock();
            $cat2->expects($this->once())->method('getName')->will($this->returnValue($catName2 = uniqid()));
            $instance->addCat($cat2);
            $this->assertSame($cat2, $instance->getCat($catName2));

            $this->assertSame([$catName1 => $cat1, $catName2 => $cat2], $instance->getCats());
        }

        public function testSources()
        {
            $langs = ['en', 'ru'];

            $instance = $this->getInstance($name = uniqid());

            $this->assertEmpty($instance->getSources());
            $this->assertNull($instance->getSource(uniqid()));
            $this->assertFalse($instance->hasSource(uniqid()));

            $source1 = $this->getLangSourceMock([$sourceName1 = uniqid(), $sourcePath1 = uniqid(), $langs]);
            $source1->expects($this->once())->method('getName')->will($this->returnValue($sourceName1));

            $instance->addSource($source1);
            $this->assertSame($source1, $instance->getSource($sourceName1));

            $source2 = $this->getLangSourceMock([$sourceName2 = uniqid(), $sourcePath2 = uniqid(), $langs]);
            $source2->expects($this->once())->method('getName')->will($this->returnValue($sourceName2));

            $instance->setSources([$source2]);
            $this->assertSame($source2, $instance->getSource($sourceName2));

            $this->assertSame([$sourceName1 => $source1, $sourceName2 => $source2], $instance->getSources());

        }
    }
