# ArrayHelper
ArrayHelper for PHP 5.4+

Supported versions:
 PHP 5.4
 PHP 5.5
 PHP 5.6
 PHP 7.0
 PHP 7.1
 PHP 7.2
 PHP 7.3

# Install
You can install ArrayHelper via composer:
```bash
composer require dimsog/arrayhelper
```
Packagist link [here](https://packagist.org/packages/dimsog/arrayhelper)

# Available methods
* [camelCaseKeys](#camel-case-keys)
* [except](#except)
* [getValue](#get-value)
* [isAssoc](#isassoc)
* [isMulti](#ismulti)
* [only](#only)
* [paginate](#paginate)
* [random](#random)
* [replaceKey](#replace-key)
* [shuffle](#shuffle-an-array)
* [toInt](#toint)

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
```