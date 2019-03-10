<?php
namespace dimsog\arrayhelper\tests;

use dimsog\arrayhelper\ArrayHelper;
use PHPUnit\Framework\TestCase;

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
}