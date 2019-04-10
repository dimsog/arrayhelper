<?php
namespace dimsog\arrayhelper;

class ArrayHelper
{
    public static function fluent(array $array)
    {
        return new Fluent($array);
    }

    /**
     * Transform some properties to int
     *
     * ```php
     * $source = [
     *  "id" => "100",
     *  "created_at" => "10000",
     *  "name" => "Foo Bar",
     * ];
     *
     * $source = ArrayHelper::toInt($source, ["id" "created_at"]);
     *
     * // result:
     * [
     *      "id" => 100,
     *      "created_at" => 10000,
     *      "name" => "Foo Bar"
     * ]
     * ```
     *
     * Convert all values:
     * $source = ArrayHelper::toInt($source);
     *
     * Since 1.1.0:
     * ```php
     * $source = [
     *      [
     *          'id' => '1',
     *          'name' => 'Dmitry R'
     *      ],
     *      [
     *          'id' => '2',
     *          'name' => 'Dmitry R2'
     *      ]
     * ];
     *
     * $source = ArrayHelper::toInt($source, ['id']);
     * // result:
     * [
     *      [
     *          'id' => 1,
     *          'name' => 'Dmitry R'
     *      ],
     *      [
     *          'id' => 2,
     *          'name' => 'Dmitry R2'
     *      ]
     * ]
     * ```
     *
     * @param array $source
     * @param array $keys
     * @return array
     */
    public static function toInt(array $source, array $keys = [])
    {
        $keys = array_values($keys);

        if (static::isMulti($source, true)) {
            return array_map(function($item) use ($keys) {
                return static::toIntPartOfArray($item, $keys);
            }, $source);
        }
        return static::toIntPartOfArray($source, $keys);
    }

    private static function toIntPartOfArray(array $source, array $keys = [])
    {
        if (empty($keys)) {
            // transform all
            return array_map(function($item) {
                return (int) $item;
            }, $source);
        }

        $keys = array_values($keys);

        foreach ($keys as $key) {
            if (array_key_exists($key, $source) === false) {
                continue;
            }
            $source[$key] = (int) $source[$key];
        }

        return $source;
    }

    /**
     * Convert snak_case keys to camelCase
     *
     * ```php
     * $data = [
     *      'demo_field' => 100
     * ];
     *
     * $data = ArrayHelper::camelCaseKeys($data);
     *
     * // result:
     * [
     *      'demoField' => 100
     * ]
     *
     * ```
     *
     * @param array $source
     * @return array
     */
    public static function camelCaseKeys(array $source)
    {
        $destination = [];
        foreach ($source as $key => $value) {
            $key = lcfirst(implode('', array_map('ucfirst', explode('_', $key))));
            $destination[$key] = $value;
        }
        return $destination;
    }

    /**
     * Replace the key from an array.
     *
     * ```php
     * $array = [
     *      'foo' => 'bar'
     * ];
     * ArrayHelper::replaceKey('foo', 'baz', $array);
     * ```
     *
     * result:
     * ```php
     * [
     *      'baz' => 'bar'
     * ]
     * ```
     *
     * @param $oldKey
     * @param $newKey
     * @param $array
     */
    public static function replaceKey($oldKey, $newKey, &$array)
    {
        if (array_key_exists($oldKey, $array) === false) {
            return;
        }
        $array[$newKey] = $array[$oldKey];
        unset($array[$oldKey]);
    }

    /**
     * Retrieves the value of an array.
     *
     * For example:
     * ```php
     * // simple demo
     * ArrayHelper::getValue($user, 'id');
     *
     * // with callback default value
     * ArrayHelper::getValue($user, 'name', function() {
     *      return "Dmitry R";
     * });
     *
     * // Retrivies the value of a sub-array
     * $user = [
     *      'photo' => [
     *          'big'   => '/path/to/image.jpg'
     *      ]
     * ]
     * ArrayHelper::getValue($user, 'photo.big');
     * ```
     *
     * @param array $array
     * @param $key
     * @param null|\Closure $defaultValue
     * @return mixed
     */
    public static function getValue(array $array, $key, $defaultValue = null)
    {
        if ($defaultValue instanceof \Closure) {
            $defaultValue = call_user_func($defaultValue);
        }
        if (($position = strrpos($key, '.')) !== false) {
            $array = static::getValue($array, substr($key, 0, $position), $defaultValue);
            $key = substr($key, $position + 1);
        }
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }
        return $defaultValue;
    }

    /**
     * Check, if this array is multidimensional
     *
     * For example:
     *
     * ```php
     * ArrayHelper:isMulti([
     *  'foo' => 'bar'
     * ]);
     * -> false
     * ```
     * ```php
     * ArrayHelper:isMulti([
     *  ['foo' => 'bar'],
     *  ['foo' => 'bar']
     * ]);
     *```
     * -> true
     *
     *
     * @param array $array
     * @param bool $strongCheck
     * @return bool
     */
    public static function isMulti(array $array, $strongCheck = false)
    {
        if ($strongCheck) {
            foreach ($array as $key => $item) {
                if (is_int($key) == false || is_array($item) == false) {
                    return false;
                }
            }
            return true;
        }
        return isset($array[0]) && is_array($array[0]);
    }

    /**
     * Extract a slice of the array
     *
     * For example:
     * ```php
     * $array = [1, 2, 3, 4, 5, 6];
     * ArrayHelper::paginate($array, 1, 3)
     * -> [1, 2, 3]
     * ```
     *
     * @param array $array
     * @param int $page - current page
     * @param int $limit - limit of values
     *
     * @return array
     */
    public static function paginate(array $array, $page, $limit)
    {
        $offset = max(0, ($page - 1) * $limit);
        return array_slice($array, $offset, $limit, true);
    }

    /**
     * Shuffle an array
     *
     * ```php
     * ArrayHelper::shuffle([1, 2, 3]);
     * -> [3, 1, 2]
     * ```
     *
     * @param array $array
     * @return array
     */
    public static function shuffle(array $array)
    {
        $keys = array_keys($array);
        shuffle($keys);

        $newArray = [];
        foreach ($keys as $key) {
            $newArray[$key] = $array[$key];
        }
        return $newArray;
    }

    /**
     * Pick one or more random elements out of an array
     *
     * ```php
     * ArrayHelper::random([1, 2, 3])
     * -> 1 or 2 or 3
     *
     * ArrayHelper::random([1, 2, 3], 2);
     * -> [1, 3]
     * ```
     *
     * @param array $array
     * @param int $count
     * @return array|mixed
     */
    public static function random(array $array, $count = 1)
    {
        $total = count($array);
        $count = max(1, (int) $count);

        if ($count > $total) {
            return static::shuffle($array);
        }

        if ($count == 1) {
            return $array[array_rand($array)];
        }

        $keys = array_rand($array, $count);
        $newArray = [];
        foreach ($keys as $key) {
            $newArray[$key] = $array[$key];
        }

        return $newArray;
    }

    /**
     * Determine whether array is assoc or not
     * ```php
     * ArrayHelper::isAssoc([1, 2, 3]);
     * -> false
     *
     * ArrayHelper::isAssoc(['foo' => 'bar']);
     * -> true
     * ```
     * @param array $array
     * @return bool
     */
    public static function isAssoc(array $array)
    {
        if (empty($array)) {
            return false;
        }
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Get a subset of the items from the given array.
     *
     * For example:
     * ```php
     * ArrayHelper::only(['a', 'b', 'c'], ['a', 'b']);
     * -> ['a', 'b'];
     *```
     *
     * With assoc array
     * ```php
     * ArrayHelper::only([
     *      'foo'   => 'bar',
     *      'foo2'  => 'bar2',
     *      'foo3'  => 'bar3'
     * ], ['foo2']);
     * ->
     * [
     *  'foo2' => 'bar2'
     * ]
     * ```
     *
     * With multi array:
     * ```php
     * $array = [
     *      [
     *          'foo' => 'bar',
     *          'foo2' => 'bar2'
     *      ],
     *      [
     *          'foo' => 'bar',
     *          'foo2' => 'bar2'
     *      ]
     * ]
     * ArrayHelper::only($array, ['foo']);
     * ->
     * [
     *   ['foo' => 'bar'],
     *   ['foo' => 'bar']
     * ]
     * ```
     *
     * @param array $array
     * @param array $keys
     * @return array
     */
    public static function only(array $array, array $keys)
    {
        $isMulti = static::isMulti($array, true);
        if (static::isAssoc($array) === false && $isMulti === false) {
            return array_values(array_intersect($array, $keys));
        }

        if ($isMulti === true) {
            return array_map(function($arrayItem) use ($keys) {
                return array_intersect_key($arrayItem, array_flip($keys));
            }, $array);
        }
        return array_intersect_key($array, array_flip($keys));
    }

    /**
     * Get a subset of the items from the given array except $keys
     *
     * For example:
     * ```php
     * ArrayHelper::except(['a', 'b', 'c'], ['a', 'b']);
     * -> ['c']
     *```
     *
     *```php
     *
     * With assoc array:
     * ```php
     * $array = [
     *      'foo' => 'bar',
     *      'foo2' => 'bar2'
     * ];
     * ArrayHelper::except($array, ['foo2']);
     * -> ['foo' => 'bar']
     * ```
     *
     * With multi array:
     * ```php
     * $array = [
     *      [
     *          'foo' => 'bar',
     *          'foo2' => 'bar2'
     *      ],
     *      [
     *          'foo' => 'bar',
     *          'foo2' => 'bar2'
     *      ]
     *  ];
     * ArrayHelper::except($array, ['foo2']);
     * ->
     * [
     *   ['foo' => 'bar'],
     *   ['foo' => 'bar']
     * ]
     *
     * ```
     *
     * @param array $array
     * @param array $keys
     * @return array
     */
    public static function except(array $array, array $keys)
    {
        $isMulti = static::isMulti($array, true);

        if (static::isAssoc($array) === false && $isMulti === false) {
            return array_values(array_diff($array, $keys));
        }

        if ($isMulti) {
            return array_map(function($item) use ($keys) {
                return array_diff_key($item, array_flip($keys));
            }, $array);
        }
        return array_diff_key($array, array_flip($keys));
    }

    /**
     * Return the values from a single column in the input array
     * ```php
     * $array = [
     *      [
     *          'id' => 1,
     *          'name' => 'test1'
     *      ],
     *      [
     *          'id' => 2,
     *          'name' => 'test2'
     *      ],
     *      [
     *          'id' => 3,
     *          'name' => 'test3'
     *      ],
     *      [
     *          'id' => 4,
     *          'name' => 'test4'
     *      ]
     * ]
     *
     * ArrayHelper::column($array, 'id');
     *
     * -> [1, 2, 3, 4]
     * ```
     *
     * @param array $array
     * @param $key
     * @return array
     */
    public static function column(array $array, $key)
    {
        if (function_exists('array_column')) {
            return array_column($array, $key);
        }
        $newArray = [];
        foreach ($array as $item) {
            if (is_array($item) == false) {
                continue;
            }
            if (array_key_exists($key, $item)) {
                $newArray[] = $item[$key];
            }
        }
        return $newArray;
    }

    /**
     * Filter an array
     * Simple example:
     * ```php
     * $array = [
     *      [
     *          'id' => 1,
     *          'category_id' => 5,
     *          'name' => 'test1'
     *      ],
     *      [
     *          'id' => 3,
     *          'category_id' => 1,
     *          'name' => 'test3'
     *      ],
     * ];
     * ArrayHelper::filter($array, ['category_id' => 5])
     * -> [
     *      [
     *          'id' => 1,
     *          'category_id' => 5,
     *          'name' => 'test1'
     *      ],
     * ]
     * ```
     *
     * With callback function:
     * ```php
     * ArrayHelper::filter($array, function($item) {
     *      return $item['category_id'] == 5;
     * })
     * ```
     *
     * @param array $array
     * @param array|\Closure $condition
     * @param bool $preserveKeys if set to TRUE numeric keys are preserved. Non-numeric keys are not affected by this setting and will always be preserved.
     * @return array
     */
    public static function filter(array $array, $condition, $preserveKeys = false)
    {
        if (is_callable($condition)) {
            $array = array_filter($array, $condition);
            if ($preserveKeys == false) {
                $array = static::reindex($array);
            }
            return $array;
        }

        if (static::isMulti($array) == false) {
            return [];
        }

        if (is_array($condition) == false || empty($condition)) {
            return [];
        }

        $array = array_filter($array, function($item) use ($condition) {
            foreach ($condition as $key => $conditionItem) {
                if (array_key_exists($key, $item) == false) {
                    return false;
                }
                if ($item[$key] != $conditionItem) {
                    return false;
                }
            }
            return true;
        });
        if ($preserveKeys == false) {
            $array = static::reindex($array);
        }
        return $array;
    }

    /**
     * Reindex all the keys of an array
     *
     * ```php
     * $array = [
     *  1 => 10,
     *  2 => 20
     * ];
     * ArrayHelper::reindex($array);
     * -> [10, 20]
     * ```
     *
     * @param $array
     * @return array
     */
    public static function reindex($array)
    {
        return array_values($array);
    }

    /**
     * Insert a new column to exist array
     *
     * For example:
     * ```php
     * $array = [
     *      'id' => 1,
     *      'name' => 'Dmitry R'
     * ];
     * ArrayHelper::insertColumn($array, 'country', 'Russia');
     * ->
     * [
     *      'id' => 1,
     *      'name' => 'Dmitry R',
     *      'country' => 'Russia'
     * ]
     *
     * $array = [
     *      [
     *          'id' => 1,
     *          'name' => 'Dmitry R'
     *      ],
     *      [
     *          'id' => 1,
     *          'name' => 'Dmitry R'
     *      ]
     * ];
     * ArrayHelper::insertColumn($array, 'foo', 'bar');
     * ->
     * [
     *      [
     *          'id' => 1,
     *          'name' => 'Dmitry R',
     *          'foo' => 'bar'
     *      ],
     *      [
     *          'id' => 1,
     *          'name' => 'Dmitry R',
     *          'foo' => 'bar'
     *      ]
     * ]
     * ```
     *
     * @param $array
     * @param $key
     * @param null $value
     */
    public static function insert(&$array, $key, $value = null)
    {
        if (static::isMulti($array, true)) {
            foreach ($array as &$item) {
                $item[$key] = $value;
            }
            unset($item);
        } else {
            $array[$key] = $value;
        }
    }

    /**
     * Split a given string to array
     *
     * ```php
     * $string = 'Ab Cd';
     * ArrayHelper::strToArray($string);
     * // ['A', 'b', ' ', 'C', 'd']
     * ```
     *
     * @param $str
     * @return array[]|false|string[]
     */
    public static function splitString($str)
    {
        if (empty($str) || is_string($str) == false) {
            return [];
        }
        return preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
    }

    private static function toArrayItem($data)
    {
        if ($data instanceof \stdClass) {
            return json_decode(json_encode($data), true);
        }
        if (is_object($data)) {
            return get_object_vars($data);
        }
        return $data;
    }

    /**
     * Convert a mixed data to array recursively
     *
     * ```php
     * $data = [stdClassInstance, stdClassInstance, someMixedClass];
     * ArrayHelper::toArray($data);
     * ```
     *
     * ```php
     * ArrayHelper::toArray('{"foo":{"bar":123}}');
     * ```
     *
     * @param $data
     * @return array
     */
    public static function toArray($data)
    {
        if (is_string($data) === true) {
            $jsonArray = json_decode($data, true);
            if (is_array($jsonArray)) {
                return $jsonArray;
            }
        }
        if (is_object($data) === true && $data instanceof \Traversable === false) {
            $data = static::toArrayItem($data);
        }
        if (is_array($data) === true || $data instanceof \stdClass === true || $data instanceof \Traversable === true) {
            $newItems = [];
            foreach ($data as $key => $item) {
                $newItems[$key] = static::toArray($item);
            }
            return $newItems;
        }
        return static::toArrayItem($data);
    }

    /**
     * Convert a multidimensional array to key-value array
     *
     * For example:
     * ```php
     * $array = [
     *      [
     *          'key' => 'name',
     *          'value' => 'Dmitry'
     *      ],
     *      [
     *          'key' => 'country',
     *          'value' => 'Russia'
     *      ],
     *      [
     *          'key' => 'city',
     *          'value' => 'Oryol (eagle)'
     *      ]
     * ];
     *
     * $array = ArrayHelper::keyValue($array, 'key', 'value');
     * // result:
     * $array = [
     *      'name' => 'Dmitry',
     *      'country' => 'Russia',
     *      'city' => 'Oryol (eagle)'
     * ];
     * ```
     *
     * @param array $items
     * @param mixed $keyField
     * @param mixed $valueField
     * @return array
     */
    public static function keyValue(array $items, $keyField = 'key', $valueField = 'value')
    {
        if (static::isMulti($items, true) === false) {
            return $items;
        }
        $newArray = [];
        foreach ($items as $item) {
            if (array_key_exists($keyField, $item) === false || array_key_exists($valueField, $item) === false) {
                continue;
            }
            $newArray[$item[$keyField]] = $item[$valueField];
        }
        return $newArray;
    }

    /**
     * Collapse an array of arrays into a single array
     *
     * ```php
     * $result = ArrayHelper::collapse([[1, 2, 3], [4, 5, 6]]);
     * // result: [1, 2, 3, 4, 5, 6]
     * ```
     *
     * ```php
     * $result = ArrayHelper::collapse([1, 2, 3, [4], [5, 6]]);
     * // result: [1, 2, 3, 4, 5, 6]
     * ```
     *
     * @param array $array
     * @return array
     */
    public static function collapse(array $array)
    {
        return array_reduce($array, function($prev, $item) {
            if (is_array($item) == false) {
                $item = [$item];
            }
            return array_merge($prev, $item);
        }, []);
    }
}