# ArrayHelper
ArrayHelper for PHP 5.4+

Supported PHP versions:
* PHP 5.4
* PHP 5.5
* PHP 5.6
* PHP 7.0
* PHP 7.1
* PHP 7.2
* PHP 7.3

# Install
You can install ArrayHelper via composer:
```bash
composer require dimsog/arrayhelper
```
Packagist link [here](https://packagist.org/packages/dimsog/arrayhelper)

# Available methods
* [camelCaseKeys](#camel-case-keys)
* [collapse](#collapse)
* [column](#column)
* [except](#except)
* [filter](#filter)
* [getValue](#get-value)
* [insert](#insert)
* [isAssoc](#isassoc)
* [isMulti](#ismulti)
* [keyValue](#keyvalue)
* [only](#only)
* [paginate](#paginate)
* [random](#random)
* [reindex](#reindex)
* [replaceKey](#replace-key)
* [shuffle](#shuffle-an-array)
* [splitString](#split-string)
* [toArray](#to-array)
* [toInt](#toint)

# Fluent interface
```php
ArrayHelper::fluent($sourceArray)
    ->toInt(['id', 'parent_id'])
    ->except(['some_field'])
    ->filter(['user_id' => 100])
    ->get();
```

# Short code
You can use Arr instead ArrayHelper.
```php
Arr::collapse([[1, 2, 3], [4, 5, 6]]);
// or
ArrayHelper::collapse([[1, 2, 3], [4, 5, 6]]);
```

# Code examples

### Camel Case Keys
Convert snak_case keys to camelCase
```php
ArrayHelper::camelCaseKeys(array $source)
```
##### Demo:
```php
$data = ArrayHelper::camelCaseKeys([
    'demo_field' => 100
]);

// result ($data):
[
     'demoField' => 100
]
```

### Collapse
Collapse an array of arrays into a single array
```php
ArrayHelper::collapse(array $array)
```
##### Demo:
```php
$result = ArrayHelper::collapse([[1, 2, 3], [4, 5, 6]]);
result: [1, 2, 3, 4, 5, 6]

$result = ArrayHelper::collapse([1, 2, 3, [4], [5, 6]]);
result: [1, 2, 3, 4, 5, 6]
```

### Column
Return the values from a single column in the input array
```php
ArrayHelper::column(array $array, $key)
```
##### Demo:
```php
$array = [
    [
        'id' => 1,
        'name' => 'test1'
    ],
    [
        'id' => 2,
        'name' => 'test2'
    ]
];
ArrayHelper::column($array, 'id');
result: [1, 2]
```

### Except
Get a subset of the items from the given array except $keys
```php
ArrayHelper::except(array $array, array $keys)
```
##### Demo:
```php
ArrayHelper::except(['a', 'b', 'c'], ['a', 'b']);
result: ['c']
```

### Filter
Filter an array
```php
ArrayHelper::filter(array $array, $condition, $preserveKeys = false)
```

##### Demo:
```php
$array = [
      [
          'id' => 1,
          'category_id' => 5,
          'name' => 'test1'
      ],
      [
          'id' => 3,
          'category_id' => 1,
          'name' => 'test3'
      ],
 ];
 
 ArrayHelper::filter($array, ['category_id' => 5]);
 // OR 
 ArrayHelper::filter($array, function($item) {
     return $item['category_id'] == 5;
 });
 
 result:
 [
      [
          'id' => 1,
          'category_id' => 5,
          'name' => 'test1'
      ]
 ]
```

### Insert
Insert a new column to exist array
```php
ArrayHelper::insert(&$array, $key, $value = null)
```
##### Demo:
```php
$array = [
    'id' => 1,
    'name' => 'Dmitry R'
];
ArrayHelper::insert($array, 'country', 'Russia');
result:
[
    'id' => 1,
    'name' => 'Dmitry R',
    'country' => 'Russia'
]
```

```php
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
ArrayHelper::insert($array, 'foo', 'bar');
result:
[
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
]
```

### Get value
Retrieves the value of an array.
```php
ArrayHelper::getValue(array $array, $key, $defaultValue = null)
```

##### Demo:
```php
ArrayHelper::getValue($user, 'id');

// with callback default value
ArrayHelper::getValue($user, 'name', function() {
     return "Dmitry R";
});

// Retrivies the value of a sub-array
$user = [
    'photo' => [
        'big'   => '/path/to/image.jpg'
    ]
]
ArrayHelper::getValue($user, 'photo.big');
```

### isAssoc
Determine whether array is assoc or not
```php
ArrayHelper::isAssoc(array $array)
```
##### Demo:
```php
ArrayHelper::isAssoc([1, 2, 3]);
result: false

ArrayHelper::isAssoc(['foo' => 'bar']);
result: true
```

### isMulti
Check if an array is multidimensional
```php
ArrayHelper::isMulti(array $array, $strongCheck = false)
```

```php
$array = [
    ['foo' => 'bar'],
    ['foo' => 'bar']
];
ArrayHelper::isMulti($array);
```

### KeyValue
Convert a multidimensional array to key-value array
```php
ArrayHelper::keyValue(array $items, $keyField = 'key', $valueField = 'value')
```

##### Demo:
```php
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

result:
[
    'name' => 'Dmitry',
    'country' => 'Russia',
    'city' => 'Oryol (eagle)'
];
```

### Only
Get a subset of the items from the given array
```php
ArrayHelper::only(array $array, array $keys)
```
##### Demo:
```php
ArrayHelper::only(['a', 'b', 'c'], ['a', 'b']);
result: ['a', 'b'];
```
With assoc array:
```php
$array = [
    'foo'   => 'bar',
    'foo2'  => 'bar2',
    'foo3'  => 'bar3'
];
ArrayHelper::only($array, ['foo2']);

result:
[
    'foo2' => 'bar2'
]
```
With multi array:
```php
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
ArrayHelper::only($array, ['foo']);
result: 
[
    ['foo' => 'bar'],
    ['foo' => 'bar']
]
```

With assoc array:
```php
$array = [
    'foo' => 'bar',
    'foo2' => 'bar2'
];
ArrayHelper::except($array, ['foo2']);
result: ['foo' => 'bar']
 ```

With multi array:
 ```php
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
ArrayHelper::except($array, ['foo2']);
result:
[
   ['foo' => 'bar'],
   ['foo' => 'bar']
]
```

### Paginate
Extract a slice of the array
```php
ArrayHelper::paginate(array $array, $page, $limit)
```
##### Demo:
```php
$array = [1, 2, 3, 4, 5, 6];
ArrayHelper::paginate($array, 1, 3)
result: [1, 2, 3]
```

### Random
Pick one or more random elements out of an array
```php
ArrayHelper::random(array $array, $count = 1)
```
##### Demo:
```php
ArrayHelper::random([1, 2, 3])
result: 1 or 2 or 3

ArrayHelper::random([1, 2, 3], 2);
result: [1, 3]
```

### Reindex
Reindex all the keys of an array
```php
ArrayHelper::reindex($array)
```
##### Demo:
```php
$array = [
    1 => 10,
    2 => 20
];
ArrayHelper::reindex($array);
result: [10, 20]
```

### Replace key
Replace the key from an array.
```php
ArrayHelper::replaceKey($oldKey, $newKey, &$array)
```
##### Demo:
```php
$array = [
     'foo' => 'bar'
];

ArrayHelper::replaceKey('foo', 'baz', $array);

// result ($array):
[
     'baz' => 'bar'
]
```

### Shuffle an array
```php
ArrayHelper::shuffle(array $array)
```
##### Demo:
```php
ArrayHelper::shuffle([1, 2, 3]);
result: [3, 1, 2]
```

### Split string
Split a given string to array
```php
ArrayHelper::splitString($str)
```
##### Demo:
```php
$string = 'Ab Cd';
ArrayHelper::splitString($string);
result: ['A', 'b', ' ', 'C', 'd']
```

### To array
Convert a mixed data to array recursively

#### Demo:
```php
ArrayHelper::toArray([stdClassInstance, stdClassInstance, someMixedClass]);
```

```php
ArrayHelper::toArray('{"foo":{"bar":123}}');
```

##### Crazy example:
```php
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
    // ...
    private $array = [];


    public function __construct()
    {
        $this->position = 0;
        $std = new \stdClass();
        $std->f = (new \stdClass());
        $std->f->b = [1, 2];
        $this->array = [1, 2, 3, 4, 'asd', $std, new SimpleClass(), ['yeah' => [1, 2]]];
    }

    // ...
}
ArrayHelper::toArray(new SimpleIteratorTestClass());

result: 
[
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
]
```

### toInt
Transform some properties to int
```php
ArrayHelper::toInt(array $source, array $keys = [])
```
##### Demo:
```php
$source = [
    "id" => "100",
    "created_at" => "10000",
    "name" => "Foo Bar"
];

$source = ArrayHelper::toInt($source, ["id" "created_at"]);

// result:
[
    "id" => 100,
    "created_at" => 10000,
    "name" => "Foo Bar"
]

// Convert all values:
$source = ArrayHelper::toInt($source);

// Since 1.1.0
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
ArrayHelper::toInt($source, ['id', 'created_at', 'other']);
```