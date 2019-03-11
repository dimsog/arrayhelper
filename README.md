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

# Code examples
### toInt
Transform some properties to int
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

### Camel Case Keys
Convert snak_case keys to camelCase
```php
$data = ArrayHelper::camelCaseKeys([
    'demo_field' => 100
]);

// result ($data):
[
     'demoField' => 100
]
```

### Replace key
Replace the key from an array.
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

### Get value
Retrieves the value of an array.
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

### Check if an array is multidimensional
```php
$array = [
    ['foo' => 'bar'],
    ['foo' => 'bar']
];
ArrayHelper::isMulti($array);
```

### Paginate
Extract a slice of the array
```php
$array = [1, 2, 3, 4, 5, 6];
ArrayHelper::paginate($array, 1, 3)
result: [1, 2, 3]
```

### Shuffle an array
```php
ArrayHelper::shuffle([1, 2, 3]);
result: [3, 1, 2]
```

### Random
Pick one or more random elements out of an array
```php
ArrayHelper::random([1, 2, 3])
result: 1 or 2 or 3

ArrayHelper::random([1, 2, 3], 2);
result: [1, 3]
```

### isAssoc
Determine whether array is assoc or not
```php
ArrayHelper::isAssoc([1, 2, 3]);
result: false

ArrayHelper::isAssoc(['foo' => 'bar']);
result: true
```

### Only
Get a subset of the items from the given array
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

### Except
Get a subset of the items from the given array except $keys
```php
ArrayHelper::except(['a', 'b', 'c'], ['a', 'b']);
result: ['c']
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