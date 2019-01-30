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
}