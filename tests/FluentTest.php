<?php

use dimsog\arrayhelper\ArrayHelper;
use dimsog\arrayhelper\Arr;

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

    public function testPrepend()
    {
        $result = ArrayHelper::fluent([1, 2, 3])
            ->prepend(-1)
            ->get();
        $this->assertEquals($result, [-1, 1, 2, 3]);

        $result = ArrayHelper::fluent([
            'key' => 'value'
        ])
        ->prepend('key1', 'value1')
        ->get();

        $this->assertEquals($result, [
            'key1' => 'value1',
            'key'  => 'value'
        ]);
    }

    public function testFluentWithRemove()
    {
        $array = [
            [
                'foo' => 'bar',
                'total' => 5
            ],
            [
                'foo' => 'bar',
                'total' => 10
            ]
        ];
        $result = Arr::fluent($array)
            ->remove('foo')
            ->map(function($item) {
                $item['total'] = $item['total'] + 1;
                return $item;
            })
            ->sum('total');

        $this->assertEquals(17, $result);
    }

    public function testExist()
    {
        $array = [
            [
                'foo' => 'bar'
            ],
            [
                'foo' => 'baz'
            ],
            [
                'test' => 123
            ]
        ];
        $exist = ArrayHelper::fluent($array)
            ->exist(['foo' => 'bar']);
        $this->assertTrue($exist);
    }
}