<?php
    namespace LaravelCommode\Localization;

    class StructuredTest extends \PHPUnit_Framework_TestCase
    {
        protected function getInstance()
        {
            return new Structured();
        }

        protected function getICatContainerMock()
        {
            return $this->getMock('LaravelCommode\Localization\Interfaces\ICatContainer');
        }

        protected function getLangSourceMock()
        {
            return $this->getMock('LaravelCommode\Localization\LangSource', [], [], '', 0);
        }

        public function testLangs()
        {
            $instance = $this->getInstance();

            $this->assertEmpty($instance->getLangs());

            $langs = ['en', 'ru'];

            $instance->setLangs($langs);

            $this->assertSame($langs, $instance->getLangs());

            $instance->addLang('fr');

            $this->assertSame(array_merge($langs, ['fr']), $instance->getLangs());
        }

        public function testCats()
        {
            $instance = $this->getInstance();

            $this->assertEmpty($instance->getCats());
            $this->assertFalse($instance->hasCats());
            $this->assertFalse($instance->getLastCat());

            $cat1 = $this->getICatContainerMock();
            $cat2 = $this->getICatContainerMock();

            $cat1->expects($this->once())->method('getName')->will($this->returnValue($name1 = uniqid()));
            $cat2->expects($this->once())->method('getName')->will($this->returnValue($name2 = uniqid()));

            $instance->addCat($cat1);
            $this->assertCount(1, $instance->getCats());
            $this->assertArrayHasKey($name1, $instance->getCats());
            $this->assertTrue($instance->hasCat($name1));
            $this->assertSame($cat1, $instance->getCat($name1));
            $this->assertSame($cat1, $instance->getLastCat());

            $instance->addCat($cat2);
            $this->assertCount(2, $instance->getCats());
            $this->assertArrayHasKey($name2, $instance->getCats());
            $this->assertTrue($instance->hasCat($name2));
            $this->assertSame($cat2, $instance->getCat($name2));
            $this->assertSame($cat2, $instance->getLastCat());

            $this->assertTrue($instance->hasCats());

            $cat3 = $this->getICatContainerMock();
            $cat4 = $this->getICatContainerMock();
            $cat3->expects($this->once())->method('getName')->will($this->returnValue($name3 = uniqid()));
            $cat4->expects($this->once())->method('getName')->will($this->returnValue($name4 = uniqid()));

            $instance->setCats([$cat3, $cat4]);
            $this->assertSame(
                [$name3 => $cat3, $name4 => $cat4], $instance->getCats()
            );

        }

        public function testSources()
        {
            $instance = $this->getInstance();

            $this->assertEmpty($instance->getSources());

            $source1 = $this->getLangSourceMock();
            $source1->expects($this->exactly(2))->method('getName')->will($this->returnValue($name1 = uniqid()));
            $instance->addSource($source1);
            $this->assertArrayHasKey($name1, $instance->getSources());
            $this->assertCount(1, $instance->getSources());
            $this->assertSame($source1, $instance->getSource($name1));
            $this->assertNull($instance->getSource(uniqid()));

            $source2 = $this->getLangSourceMock();
            $source2->expects($this->once())->method('getName')->will($this->returnValue($name2 = uniqid()));

            $instance->setSources($expected = [$name1 => $source1, $name2 => $source2]);
            $this->assertSame($expected, $instance->getSources());
        }

        public function testGetName()
        {
            $instance = $this->getInstance();
            $this->assertEmpty($instance->getName());
        }
    }
