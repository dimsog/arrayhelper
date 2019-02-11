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
}