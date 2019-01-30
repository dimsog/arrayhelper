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
     * @param array $source
     * @param array $keys
     * @return array
     */
    public static function toInt(array $source, array $keys)
    {
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
}