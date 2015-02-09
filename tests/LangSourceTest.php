<?php
    namespace LaravelCommode\Localization;

    class LangSourceTest extends \PHPUnit_Framework_TestCase
    {
        protected function getInstance($name, $path, array $langs, LangSource $parent = null)
        {
            return new LangSource($name, $path, $langs, $parent);
        }

        public function testConstruct()
        {
            $parentInstance = $this->getInstance($parentName = uniqid(), $parentPath = uniqid(), $lang = ['en']);

            $instance = $this->getInstance($name = uniqid(), $path = uniqid(), $lang);

            $this->assertSame($name, $instance->getName());
            $this->assertSame($path, $instance->getPath());
            $this->assertNull($instance->getParent());
            $this->assertFalse($instance->isSubSource());

            $newName = uniqid();
            $instance->setName($newName);
            $this->assertSame($newName, $instance->getName());
            $newPath = uniqid();
            $instance->setPath($newPath);
            $this->assertSame($newPath, $instance->getPath());

            $instance = $this->getInstance($name = uniqid(), $path = uniqid(), $lang, $parentInstance);
            $this->assertSame($parentInstance, $instance->getParent());
            $this->assertTrue($instance->isSubSource());
        }

        public function testValues()
        {
            $instance = $this->getInstance($name = uniqid(), $path = uniqid(), $lang = ['en', 'ru']);

            $expected = [
                'en' => [
                    'val1' => 'val1en',
                    'val2' => 'val2en'
                ],
                'ru' => [
                    'val1' => 'val1ru',
                    'val2' => 'val2ru'
                ],
            ];

            $instance->setValue('en', 'val1', 'val1en');
            $instance->setValue('ru', 'val1', 'val1ru');
            $instance->setValues([
                'en' => ['val2' => 'val2en'],
                'ru' => ['val2' => 'val2ru']
            ]);

            $this->assertSame($expected, $instance->getStrings());
        }

        public function testBuildForLang()
        {
            $instance = $this->getInstance($name = uniqid(), $path = uniqid(), $lang = ['en', 'ru']);

            $expected = [
                'en' => [
                    'val1' => 'val1en',
                    'val2' => 'val2en'
                ],
                'ru' => [
                    'val1' => 'val1ru',
                    'val2' => 'val2ru'
                ],
            ];

            $instance->buildForLang('en', $expected['en']);
            $instance->buildForLang('ru', $expected['ru']);

            $this->assertSame($expected, $instance->getStrings());
            $this->assertSame($expected['ru'], $instance->getLangValues('ru'));

            $inputs = [
                'en' => [
                    'vals1' => 'val1en',
                    'vals2' => [
                        'subVal' => 'subVal'
                    ]
                ]
            ];

            $instance = $this->getInstance($name = uniqid(), $path = uniqid(), $lang = ['en', 'ru']);
            $instance->buildForLang('en', $inputs['en']);

            $subLangSource = $instance->getStrings();
            $subLangSource = $subLangSource['en']['vals2'];

            $this->assertTrue($subLangSource  instanceof LangSource);
            $enBuild = $instance->getLangValues('en');
            $this->assertSame($inputs['en'], $enBuild);

            $this->assertEmpty($instance->getLangValues('fr'));
        }

        public function testToArrayToJson()
        {
            $instance = $this->getInstance($name = uniqid(), $path = uniqid(), $lang = ['en', 'ru']);

            $expected = [
                'en' => [
                    'val1' => $v1e = uniqid(),
                    'val2' => $v2e = uniqid()
                ],
                'ru' => [
                    'val1' => $v1r = uniqid(),
                    'val2' => $v2r = uniqid()
                ],
            ];

            $instance->setValues($expected);

            $this->assertSame($expected, $instance->toArray());
            $this->assertSame($expected['en'], $instance->toArray('en'));
            $this->assertSame(json_encode($expected), $instance->toJson());
        }

        public function testInherited()
        {
            $instance = $this->getInstance($name = uniqid(), $path = uniqid(), $lang = ['en', 'ru']);

            $expected = [
                'en' => [
                    'val1' => $v1e = uniqid(),
                    'val2' => $v2e = uniqid(),
                    'val3' => [
                        'val1' => $v3e = uniqid(),
                        'val2' => $v4e = uniqid()
                    ]
                ],
                'ru' => [
                    'val1' => $v1r = uniqid(),
                    'val2' => $v2r = uniqid(),
                    'val3' => [
                        'val1' => $v3r = uniqid(),
                        'val2' => $v4r = uniqid()
                    ]
                ],
            ];

            $instance->setValues($expected);
            $this->assertFalse($instance->isInherited());


            $inherits = uniqid('inherits');

            $instance->setInherited($inherits);

            $this->assertSame($inherits, $instance->getInherited());

            $this->assertSame([$path => $inherits], $instance->getAllInherits());
        }
    }
