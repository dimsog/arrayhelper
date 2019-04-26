<?php
namespace dimsog\arrayhelper\tests;

use dimsog\arrayhelper\ArrayHelper;
use PHPUnit\Framework\TestCase;

class SimpleClass
{
    public $test = null;

    public function __construct()
    {
        $std = new \stdClass();
        $std->f = (new \stdClass());
        $std->f->b = [1, 2];
        $this->test = [$std, ['a', 'b']];
    }
}

class SimpleIteratorTestClass implements \Iterator
{
    private $position = 0;

    private $array = [];


    public function __construct()
    {
        $this->position = 0;
        $std = new \stdClass();
        $std->f = (new \stdClass());
        $std->f->b = [1, 2];

        $this->array = [1, 2, 3, 4, 'asd', $std, new SimpleClass(), ['yeah' => [1, 2]]];
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->array[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->array[$this->position]);
    }
}

class ArrayHelperTest extends TestCase
{
    public function testToInt()
    {
        $source = [
            'id' => '100',
            'created_at' => '1000',
            'name' => 'FooBar',
            'other' => '200'
        ];
        $source = ArrayHelper::toInt($source, ['id', 'created_at', 'not_exists_field']);
        $this->assertEquals(100, $source['id']);
        $this->assertEquals(1000, $source['created_at']);
        $this->assertEquals('FooBar', $source['name']);
        $this->assertEquals('200', $source['other']);

        $source2 = [
            'id' => '100',
            'test' => '200',
            'foo'   => 'bar'
        ];

        $source2 = ArrayHelper::toInt($source2);
        $this->assertEquals(100, $source2['id']);
        $this->assertEquals(200, $source2['test']);
        $this->assertEquals(0, $source2['foo']);

        $source = [
            [
                'id' => '100',
                'created_at' => '1000',
                'name' => 'FooBar',
                'other' => '200'
            ],
            [
                'id' => '200',
                'created_at' => '2000',
                'name' => 'FooBar',
                'other' => '300'
            ]
        ];
        $source = ArrayHelper::toInt($source, ['id', 'created_at', 'other']);
        $this->assertEquals(100, $source[0]['id']);
        $this->assertEquals(1000, $source[0]['created_at']);
        $this->assertEquals(200, $source[0]['other']);

        $this->assertEquals(200, $source[1]['id']);
        $this->assertEquals(2000, $source[1]['created_at']);
        $this->assertEquals(300, $source[1]['other']);
    }



    public function testToCamelCase()
    {
        $data = [
            'id' => '100',
            'created_at' => '1000',
            'some_field' => 'FooBar',
            'some_another_field_2' => '200',
            'anotherKey' => 'test'
        ];
        $data = ArrayHelper::camelCaseKeys($data);

        $this->assertEquals(true, array_key_exists('id', $data));
        $this->assertEquals(true, array_key_exists('createdAt', $data));
        $this->assertEquals(true, array_key_exists('someField', $data));
        $this->assertEquals(true, array_key_exists('someAnotherField2', $data));
        $this->assertEquals(true, array_key_exists('anotherKey', $data));

        $this->assertEquals(false, array_key_exists('created_at', $data));
        $this->assertEquals(false, array_key_exists('some_field', $data));
        $this->assertEquals(false, array_key_exists('some_another_field_2', $data));

        $source = [100, 200, 300];
        $this->assertEquals($source, ArrayHelper::camelCaseKeys($source));
    }

    public function testReplaceKey()
    {
        $array = [
            'foo' => 'bar',
            'test' => 123,
            'test2' => 321
        ];
        ArrayHelper::replaceKey('foo', 'baz', $array);
        ArrayHelper::replaceKey('test', 'test_new', $array);
        ArrayHelper::replaceKey('unknown_key', 'test123', $array);

        $this->assertEquals('bar', $array['baz']);
        $this->assertEquals('123', $array['test_new']);
        $this->assertEquals('321', $array['test2']);
    }

    public function testGetValue()
    {
        $array = [
            'foo' => 'bar'
        ];

        $this->assertEquals('bar', ArrayHelper::getValue($array, 'foo'));
        $this->assertEquals(null, ArrayHelper::getValue($array, 'test'));
        $this->assertEquals('test2', ArrayHelper::getValue($array, 'test2', function() {
            return 'test2';
        }));

        $array2 = [
            'foo' => [
                'bar' => [
                    'baz' => 123
                ]
            ]
        ];
        $this->assertEquals(123, ArrayHelper::getValue($array2, 'foo.bar.baz'));
    }

    public function testIsMultiArray()
    {
        $array = [123];
        $this->assertFalse(ArrayHelper::isMulti($array));

        $array = [
            ['foo' => 'bar'],
            ['foo2' => 'bar']
        ];
        $this->assertTrue(ArrayHelper::isMulti($array));

        $array = [
            ['foo' => 'bar'],
            123,
            456
        ];
        $this->assertFalse(ArrayHelper::isMulti($array, true));
        $this->assertTrue(ArrayHelper::isMulti($array));
    }

    public function testPaginate()
    {
        $array = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $expected = [3 => 4, 4 => 5, 5 => 6];
        $this->assertEquals($expected, ArrayHelper::paginate($array, 2, 3));
        $this->assertEquals([1, 2, 3], ArrayHelper::paginate($array, -1, 3));
        $this->assertEquals([1, 2, 3], ArrayHelper::paginate($array, 1, 3));
        $this->assertEquals([8 => 9, 9 => 10], ArrayHelper::paginate($array, 2, 8));

        $array = [
            'foo' => 'bar',
            'foo2' => 'bar2'
        ];
        $this->assertEquals(['foo' => 'bar'], ArrayHelper::paginate($array, 1, 1));
        $this->assertEquals([], ArrayHelper::paginate($array, 100, 200));
    }

    public function testShuffle()
    {
        $array = [1, 2, 3, 4];
        $this->assertTrue(is_array(ArrayHelper::shuffle($array)));
        $this->assertEquals(4, count(ArrayHelper::shuffle($array)));
    }

    public function testRandom()
    {
        $array = [1, 2, 3, 4];
        $this->assertTrue(is_int(ArrayHelper::random($array)));
        $this->assertEquals(2, count(ArrayHelper::random($array, 2)));
        $this->assertEquals(4, count(ArrayHelper::random($array, 1000)));
        $this->assertTrue(is_int(ArrayHelper::random($array, -1)));

        $array = [
            new \stdClass(),
            new \stdClass(),
            new \stdClass()
        ];
        $this->assertEquals(3, count(ArrayHelper::random($array, 3)));
        $this->assertInstanceOf('stdClass', ArrayHelper::random($array));
    }

    public function testIsAssoc()
    {
        $this->assertFalse(ArrayHelper::isAssoc([1, 2, 3]));
        $this->assertFalse(ArrayHelper::isAssoc([[1], [2], [3]]));
        $this->assertTrue(ArrayHelper::isAssoc(['foo' => 'baz', 'foo2' => 'bar']));
        $this->assertTrue(ArrayHelper::isAssoc(['123', 456, 'foo' => 'bar']));
    }

    public function testOnly()
    {
        $array = ['foo', 'bar', 'baz'];
        $this->assertEquals(['bar', 'baz'], ArrayHelper::only($array, ['bar', 'baz']));

        $array = [
            'foo' => 'bar',
            'foo2' => 'bar2',
            'foo3' => 'bar3'
        ];
        $expected = [
            'foo2' => 'bar2',
            'foo3' => 'bar3'
        ];
        $this->assertEquals($expected, ArrayHelper::only($array, ['foo2', 'foo3']));

        // check multi
        $array = [
            [
                'foo' => 'bar',
                'foo2' => 'bar2',
                'foo3' => 'bar3'
            ],
            [
                'foo' => 'bar',
                'foo2' => 'bar2',
                'foo3' => 'bar3'
            ],
            [
                'foo' => 'bar',
                'foo2' => 'bar2',
                'foo3' => 'bar3'
            ]
        ];
        $expected = [
            [
                'foo2' => 'bar2',
                'foo3' => 'bar3'
            ],
            [
                'foo2' => 'bar2',
                'foo3' => 'bar3'
            ],
            [
                'foo2' => 'bar2',
                'foo3' => 'bar3'
            ]
        ];
        $this->assertEquals($expected, ArrayHelper::only($array, ['foo2', 'foo3']));
        $this->assertEquals(['a', 'b'], ArrayHelper::only(['a', 'b', 'c'], ['a', 'b']));

        $array = [
            'foo'   => 'bar',
            'foo2'  => 'bar2',
            'foo3'  => 'bar3'
        ];
        $this->assertEquals(['foo2' => 'bar2'], ArrayHelper::only($array, ['foo2']));
    }

    public function testExcept()
    {
        $array = ['a', 'b', 'c', 'd'];
        $this->assertEquals(['a', 'b'], ArrayHelper::except($array, ['c', 'd', 'KKKK']));

        $array = [
            'foo' => 'bar',
            'foo2' => 'bar2'
        ];
        $this->assertEquals(['foo' => 'bar'], ArrayHelper::except($array, ['foo2']));
        $this->assertEquals([], ArrayHelper::except($array, ['foo', 'foo2']));

        $array = [
            [
                'foo' => 'bar',
                'foo2' => 'bar2'
            ],
            [
                'foo' => 'bar',
                'foo2' => 'bar2'
            ]
        ];
        $expected = [
            ['foo' => 'bar'],
            ['foo' => 'bar']
        ];
        $this->assertEquals($expected, ArrayHelper::except($array, ['foo2']));

        $array = [
            ['foo' => 'bar'],
            'test' => 'test2',
            'test2' => function() {

            }
        ];
        $this->assertEquals([['foo' => 'bar']], ArrayHelper::except($array, ['test', 'test2']));
    }

    public function testColumn()
    {
        $array = [
            [
                'id' => 1,
                'name' => 'test1'
            ],
            [
                'id' => 2,
                'name' => 'test2'
            ],
            [
                'id' => 3,
                'name' => 'test3'
            ],
            [
                'id' => 4,
                'name' => 'test4'
            ]
        ];
        $this->assertEquals([1, 2, 3, 4], ArrayHelper::column($array, 'id'));
        $this->assertEquals(['test1', 'test2', 'test3', 'test4'], ArrayHelper::column($array, 'name'));
        $this->assertEquals([], ArrayHelper::column(['id' => '123', 'test' => 'name'], 'id'));
    }

    public function testFilter()
    {
        // fake test
        $array = ['a', 'b', 'c'];
        $this->assertEquals([], ArrayHelper::filter($array, ['id' => 5]));

        $array = [
            [
                'id' => 1,
                'category_id' => 5,
                'name' => 'test1'
            ],
            [
                'id' => 2,
                'category_id' => 5,
                'name' => 'test2'
            ],
            [
                'id' => 3,
                'category_id' => 1,
                'name' => 'test3'
            ],
            [
                'category_id' => 5,
                'id' => 4,
                'name' => 'test4'
            ]
        ];
        $result1 = [
            0 => [
                'id' => 1,
                'category_id' => 5,
                'name' => 'test1'
            ],
            1 => [
                'id' => 2,
                'category_id' => 5,
                'name' => 'test2'
            ],
            3 => [
                'category_id' => 5,
                'id' => 4,
                'name' => 'test4'
            ]
        ];
        $result2 = [
            2 => [
                'id' => 3,
                'category_id' => 1,
                'name' => 'test3'
            ]
        ];
        $this->assertEquals($result1, ArrayHelper::filter($array, ['category_id' => 5], true));
        $this->assertEquals($result2, ArrayHelper::filter($array, ['category_id' => 1], true));

        $this->assertEquals($result1, ArrayHelper::filter($array, function($item) {
            return $item['category_id'] == 5;
        }, true));

        $this->assertEquals($result2, ArrayHelper::filter($array, function($item) {
            return $item['category_id'] == 1;
        }, true));

        $result3 = [
            [
                'id' => 3,
                'category_id' => 1,
                'name' => 'test3'
            ]
        ];
        $this->assertEquals($result3, ArrayHelper::filter($array, ['category_id' => 1]));
    }

    public function testReindex()
    {
        $array = [
            1 => ['asd'],
            5 => 25
        ];
        $expected = [
            ['asd'], 25
        ];
        $this->assertEquals($expected, ArrayHelper::reindex($array));
    }

    public function testInsert()
    {
        $array = [
            'id' => 1,
            'name' => 'Dmitry R'
        ];
        $expected = [
            'id' => 1,
            'name' => 'Dmitry R',
            'country' => 'Russia'
        ];
        ArrayHelper::insert($array, 'country', 'Russia');
        $this->assertEquals($expected, $array);

        $array = [
           [
               'id' => 1,
               'name' => 'Dmitry R'
           ],
           [
               'id' => 1,
               'name' => 'Dmitry R'
           ]
        ];
        $expected = [
            [
                'id' => 1,
                'name' => 'Dmitry R',
                'foo' => 'bar'
            ],
            [
                'id' => 1,
                'name' => 'Dmitry R',
                'foo' => 'bar'
            ]
        ];
        ArrayHelper::insert($array, 'foo', 'bar');
        $this->assertEquals($expected,  $array);
    }

    public function testStrToArray()
    {
        $test = 'Ab Cd';
        $this->assertEquals(['A', 'b', ' ', 'C', 'd'], ArrayHelper::splitString($test));

        $test = 'Водка и Медведь'; // russian?
        $this->assertEquals(['В', 'о', 'д', 'к', 'а', ' ', 'и', ' ', 'М', 'е', 'д', 'в', 'е', 'д', 'ь'], ArrayHelper::splitString($test));
    }

    public function testToArray()
    {
        $test1 = ['a', 'b', 'c'];
        $this->assertEquals($test1, ArrayHelper::toArray($test1));

        $test2 = null;
        $this->assertEquals($test2, ArrayHelper::toArray($test2));

        $array = ['russian' => 'vodka', 'russian2' => 'bear'];
        $std = (object) $array;
        $this->assertEquals($array, ArrayHelper::toArray($std));

        $stdArray = [clone $std, clone $std, clone $std];
        $array = [$array, $array, $array];
        $this->assertEquals($array, ArrayHelper::toArray($stdArray));

        $array = [
            'foo' => [
                'bar' => [
                    'baz' => 1
                ]
            ]
        ];

        $std = json_decode(json_encode($array));
        $this->assertEquals($array, ArrayHelper::toArray($std));
        $this->assertEquals("foobar", ArrayHelper::toArray("foobar"));

        $std = new \stdClass();
        $std->f = (new \stdClass());
        $std->f->b = [1, 2];
        $this->test = [$std, ['a', 'b']];

        $expected = [
            'test' => [
                [
                    'f' => [
                        'b' => [1, 2]
                    ]
                ],
                [
                    'a', 'b'
                ]
            ]
        ];
        $this->assertEquals($expected, ArrayHelper::toArray(new SimpleClass()));

        $expected = [
            'foo' => ['bar'],
            'foo2' => ['test' => 1]
        ];

        $std = new \stdClass();
        $std2 = new \stdClass();
        $std2->test = 1;
        $std->foo = ['bar'];
        $std->foo2 = $std2;
        $this->assertEquals($expected, ArrayHelper::toArray($std));

        $expected = [
            1, 2, 3, 4, 'asd',
            [
                'f' => [
                    'b' => [1, 2]
                ]
            ],
            [
                'test' => [
                    [
                        'f' => [
                            'b' => [1, 2]
                        ]
                    ],
                    [
                        'a', 'b'
                    ]
                ]
            ],
            [
                'yeah' => [1, 2]
            ]
        ];

        $this->assertEquals($expected, ArrayHelper::toArray(new SimpleIteratorTestClass()));

        // test string
        $str = '{"foo":{"bar":123}}';
        $expected = [
            'foo' => [
                'bar' => 123
            ]
        ];
        $this->assertEquals($expected, ArrayHelper::toArray($str));
    }

    public function testKeyValue()
    {
        $array = [1, 2, 3];
        $this->assertEquals($array, ArrayHelper::keyValue($array, 'key', 'value'));

        $array = [
            [
                'foo' => 'bar'
            ],
            1,
            2,
            3
        ];

        $this->assertEquals($array, ArrayHelper::keyValue($array, 'foo', 'value'));

        $array = [
            [
                'key' => 'name',
                'value' => 'Dmitry'
            ],
            [
                'key' => 'country',
                'value' => 'Russia'
            ],
            [
                'key' => 'city',
                'value' => 'Oryol (eagle)'
            ]
        ];
        $array = ArrayHelper::keyValue($array, 'key', 'value');
        $expected = [
            'name' => 'Dmitry',
            'country' => 'Russia',
            'city' => 'Oryol (eagle)'
        ];

        $this->assertEquals($expected, $array);

        $array = [
            [
                'value' => 'Dmitry'
            ],
            [
                'key' => 'country',
                'value' => 'Russia'
            ],
            [
                'key' => 'city'
            ]
        ];
        $array = ArrayHelper::keyValue($array, 'key', 'value');
        $expected = [
            'country' => 'Russia'
        ];
        $this->assertEquals($expected, $array);
    }

    public function testCollapse()
    {
        $this->assertEquals([1, 2, 3, 4, 5, 6], ArrayHelper::collapse([[1, 2, 3], [4, 5, 6]]));
        $this->assertEquals([1, 2, 3, 4, 5, 6], ArrayHelper::collapse([1, 2, 3, [4], [5, 6]]));
        $this->assertEquals([1, 2, 3], ArrayHelper::collapse([1, 2, 3]));
        $this->assertEquals([1, 2, 3], ArrayHelper::collapse([[1], [2], [3]]));
    }

    public function testFlat()
    {
        $array = [
            'name' => 'Dmitry R',
            'country' => 'Russia',
            'skills' => ['PHP', 'JS'],
            [
                'identifier' => 'vodka medved balalayka'
            ]
        ];
        $this->assertEquals(['Dmitry R', 'Russia', 'PHP', 'JS', 'vodka medved balalayka'], ArrayHelper::values($array));

        $array = [
            ['foo'], ['bar'], ['baz']
        ];
        $this->assertEquals(['foo', 'bar', 'baz'], ArrayHelper::values($array));

        $array = [
            'foo' => 'bar',
            ['baz']
        ];
        $this->assertEquals(['bar', 'baz'], ArrayHelper::values($array));
    }

    public function testSum()
    {
        $array = [
            [
                'name' => 'entity1',
                'total' => 5
            ],
            [
                'name' => 'entity2',
                'total' => 6
            ]
        ];
        $this->assertEquals(11, ArrayHelper::sum($array, 'total'));

        $this->assertEquals(5, ArrayHelper::sum(['total' => 5], 'total'));
    }

    public function testMap()
    {
        $expected = ArrayHelper::map([1, 2, 3], function($item) {
            return $item * 2;
        });
        $this->assertEquals([2, 4, 6], $expected);
    }

    public function testRemove()
    {
        $array = [
            'foo' => 'bar'
        ];
        ArrayHelper::remove($array, 'foo');
        $this->assertEquals([], $array);

        $array = [
            'foo' => [
                'bar' => 'baz'
            ],
            'foo1' => 123
        ];
        ArrayHelper::remove($array, 'foo.bar');
        $this->assertEquals(['foo' => [], 'foo1' => 123], $array);

        $array = [
            'foo' => [
                'bar' => 'baz'
            ],
            'foo1' => 123
        ];
        ArrayHelper::remove($array, 'foo');
        $this->assertEquals(['foo1' => 123], $array);

        $array = [
            [
                'foo' => 'bar',
                'test' => 'test1'
            ],
            [
                'foo' => 'bar',
                'test' => 'test2'
            ]
        ];
        $expected = [
            ['test' => 'test1'],
            ['test' => 'test2']
        ];
        ArrayHelper::remove($array, 'foo');
        $this->assertEquals($expected, $array);

        $array = [
            [
                'foo' => [
                    'bar' => [
                        'baz' => 1
                    ]
                ],
                'test' => 'test',
                'test2' => '123',
                'only' => true
            ],
            [
                'foo' => [
                    'bar' => [
                        'baz' => 2
                    ]
                ],
                'test' => 'test',
                'test2' => 123
            ]
        ];

        ArrayHelper::remove($array, ['foo.bar.baz', 'test', 'only']);
        $expected = [
            [
                'foo' => [
                    'bar' => []
                ],
                'test2' => '123'
            ],
            [
                'foo' => [
                    'bar' => []
                ],
                'test2' => 123
            ]
        ];
        $this->assertEquals($expected, $array);
    }

    public function testHas()
    {
        $array = [
            'foo' => 'bar'
        ];
        $this->assertTrue(ArrayHelper::has($array, 'foo'));

        $array = [
            'foo' => [
                'bar' => 10
            ]
        ];
        $this->assertTrue(ArrayHelper::has($array, 'foo.bar'));
        $this->assertFalse(ArrayHelper::has($array, 'foo.bar1'));

        $array = [
            'foo' => [
                'bar' => [0, 1, 2, 'a']
            ]
        ];
        $this->assertTrue(ArrayHelper::has($array, 'foo.bar.0'));
        $this->assertTrue(ArrayHelper::has($array, 'foo.bar.1'));
        $this->assertTrue(ArrayHelper::has($array, 'foo.bar.2'));
        $this->assertTrue(ArrayHelper::has($array, 'foo.bar.3'));
        $this->assertFalse(ArrayHelper::has($array, 'foo.bar.4'));

        $array = [
            ['a'], ['b'], ['c']
        ];
        $this->assertTrue(ArrayHelper::has($array, 1));
        $this->assertFalse(ArrayHelper::has($array, 'foo.bar.baz.5'));
    }

    public function testSet()
    {
        $array = [];
        ArrayHelper::set($array, 'foo.bar', 123);
        $expected = [
            'foo' => [
                'bar' => 123
            ]
        ];
        $this->assertEquals($expected, $array);

        $array = [
            'product' => [
                'name' => 'Some name',
                'price' => 500
            ]
        ];
        ArrayHelper::set($array, 'product.price', 600);
        $expected = [
            'product' => [
                'name' => 'Some name',
                'price' => 600
            ]
        ];
        $this->assertEquals($expected, $array);

        $array = [
            [
                'name' => 'Some product 1',
                'price' => 600
            ],
            [
                'name' => 'Some product 2',
                'price' => 500
            ]
        ];

        ArrayHelper::set($array, 'price', 700);
        $expected = [
            [
                'name' => 'Some product 1',
                'price' => 700
            ],
            [
                'name' => 'Some product 2',
                'price' => 700
            ]
        ];
        $this->assertEquals($array, $expected);
        ArrayHelper::set($array, 'total', 5);
        $expected = [
            [
                'name' => 'Some product 1',
                'price' => 700,
                'total' => 5
            ],
            [
                'name' => 'Some product 2',
                'price' => 700,
                'total' => 5
            ]
        ];
        $this->assertEquals($array, $expected);
    }

    public function testOnlyWithKey()
    {
        $array = [
            [
                'a' => 1,
                'b' => 2
            ],
            [
                'a' => 1,
                'b' => 2
            ],
            [
                'b' => 2
            ]
        ];
        $expected = [
            [
                'a' => 1,
                'b' => 2
            ],
            [
                'a' => 1,
                'b' => 2
            ],
        ];
        $this->assertEquals($expected, ArrayHelper::onlyWithKey($array, 'a'));

        $array = [
            'a' => [
                'a' => 1,
                'b' => 2
            ],
            'b' => [
                'a' => 1
            ],
            1,
            2,
            3,
            'c' => [
                'a' => 1,
                'b' => 4
            ]
        ];
        $expected = [
            'a' => [
                'a' => 1,
                'b' => 2
            ],
            'c' => [
                'a' => 1,
                'b' => 4
            ]
        ];
        $this->assertEquals($expected, ArrayHelper::onlyWithKey($array, 'b'));
    }

    public function testFirstKey()
    {
        $array = [
            'a' => 1,
            'b' => 2,
            'c' => 3
        ];
        $this->assertEquals('a', ArrayHelper::firstKey($array));
    }

    public function testLastKey()
    {
        $array = [
            'a' => 1,
            'b' => 2,
            'c' => 3
        ];
        $this->assertEquals('c', ArrayHelper::lastKey($array));
    }

    public function testUnique()
    {
        $array = [
            'a', 'a', 'b', 'b'
        ];
        $this->assertEquals([0 => 'a', 2 => 'b'], ArrayHelper::unique($array));
        $array = [
            ['a', 'a', 'b', 'b'],
            ['a', 'a', 'b', 'b']
        ];
        $this->assertEquals([[0 => 'a', 2 => 'b'], [0 => 'a', 2 => 'b']], ArrayHelper::unique($array));

        $array = [
            [
                'id' => 100,
                'name' => 'Product 1'
            ],
            [
                'id' => 200,
                'name' => 'Product 2'
            ],
            [
                'id' => 100,
                'name' => 'Product 3'
            ],
            [
                'name' => 'Product 4'
            ]
        ];
        $expected = [
            [
                'id' => 100,
                'name' => 'Product 1'
            ],
            [
                'id' => 200,
                'name' => 'Product 2'
            ]
        ];
        $this->assertEquals($expected, ArrayHelper::unique($array, 'id'));
    }
}