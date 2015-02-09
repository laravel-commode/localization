<?php
    namespace LaravelCommode\Localization;

    class ProcessorTest extends \PHPUnit_Framework_TestCase
    {
        protected function getReaderMock()
        {
            return $this->getMock('LaravelCommode\Localization\Interfaces\IReader');
        }

        protected function getWriterMock()
        {
            return $this->getMock('LaravelCommode\Localization\Interfaces\IWriter');
        }

        protected function getStructured()
        {
            return new Structured();
        }

        protected function getInstance($reader, $writer)
        {
            return new Processor($reader, $writer);
        }

        public function testExtractStructure()
        {
            $reader = $this->getReaderMock();
            $writer = $this->getWriterMock();

            $instance = $this->getInstance($reader, $writer);

            $reader->expects($this->exactly(1))->method('read')->will(
                $this->returnValue($structured = $this->getStructured())
            );

            $structure = $instance->extractStructure();

            $this->assertSame($structured, $structure);
            $this->assertSame($structure, $instance->extractStructure());
        }

        public function testProcess()
        {
            $reader = $this->getReaderMock();
            $writer = $this->getWriterMock();

            $instance = $this->getInstance($reader, $writer);

            $reader->expects($this->exactly(1))->method('read')->will(
                $this->returnValue($structured = $this->getStructured())
            );

            $writer->expects($this->once())->method('setStructured')->with(
                $this->callback(function ($input) use ($structured) {
                    $this->assertSame($structured, $input);
                    return $input === $structured;
                })
            )->will($this->returnValue($writer));

            $writer->expects($this->once())->method('write')->will($this->returnValue($structured));

            $this->assertSame($structured, $instance->process());
        }
    }
