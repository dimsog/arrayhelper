<?php

use dimsog\arrayhelper\ArrayHelper;

class FluentTest extends \PHPUnit\Framework\TestCase
{
    public function testFluent()
    {
        $source = [
            'foo' => '111',
            'bar' => '222'
        ];

        $result = ArrayHelper::fluent($source)
            ->toInt()
            ->only(['bar'])
            ->get();

        $this->assertEquals(['bar' => 222], $result);

        $result = ArrayHelper::fluent($source)
            ->insert('test', 'testvalue')
            ->insert('test2', 'test2value')
            ->except(['foo'])
            ->insert('some_key', 'value')
            ->camelCaseKeys()
            ->toInt(['bar'])
            ->get();

        $except = [
            'test' => 'testvalue',
            'test2' => 'test2value',
            'someKey' => 'value',
            'bar' => 222
        ];

        $this->assertEquals($except, $result);

        $expected = ArrayHelper::fluent([[1], [2], [3]])
            ->collapse()
            ->map(function($item) {
                return $item * 2;
            })
            ->get();

        $this->assertEquals([2, 4, 6], $expected);
    }
}