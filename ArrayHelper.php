<?php
namespace dimsog\arrayhelper;

class ArrayHelper
{
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
     * @param array $source
     * @param array $keys
     * @return array
     */
    public static function toInt(array $source, array $keys = [])
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
     * ArrayHelper::except(['a', 'b', 'c'], ['a', 'b'])
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
     * ArrayHelper::except($array, ['foo2'])
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
            return array_diff($array, $keys);
        }

        if ($isMulti) {
            return array_map(function($item) use ($keys) {
                return array_diff_key($item, array_flip($keys));
            }, $array);
        }
        return array_diff_key($array, array_flip($keys));
    }
}