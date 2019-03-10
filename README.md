# ArrayHelper
ArrayHelper for PHP 5.4+

# Code examples
### toInt
Transform some properties to int
```
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
```
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
```
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
```
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
```
$array = [
    ['foo' => 'bar'],
    ['foo' => 'bar']
];
ArrayHelper::isMulti($array);
```

### Paginate
Extract a slice of the array
```
$array = [1, 2, 3, 4, 5, 6];
ArrayHelper::paginate($array, 1, 3)
result: [1, 2, 3]
```

### Shuffle an array
```
ArrayHelper::shuffle([1, 2, 3]);
result: [3, 1, 2]
```

### Random
Pick one or more random elements out of an array
```
ArrayHelper::random([1, 2, 3])
result: 1 or 2 or 3

ArrayHelper::random([1, 2, 3], 2);
result: [1, 3]
```