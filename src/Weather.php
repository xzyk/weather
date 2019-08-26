<?php
/**
 * Created by PhpStorm.
 * Title：
 * User: yaokai
 * Date: 2019/8/6 0006
 * Time: 10:45
 */

namespace Xzyk\Weather;


use GuzzleHttp\Client;
use Xzyk\Weather\Exceptions\HttpException;
use Xzyk\Weather\Exceptions\InvalidArgumentException;

class Weather
{
    protected $key;

    protected $guzzleOptions = [];

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }

    public function getWeather($city, string $type = 'base', string $format = 'json')
    {
        $url = 'https://restapi.amap.com/v3/weather/weatherInfo';

        // 1. 对 `$format` 与 `$type` 参数进行检查，不在范围内的抛出异常。
        if (!\in_array(\strtolower($format), [ 'xml', 'json' ])) {
            throw new InvalidArgumentException('Invalid response format: ' . $format);
        }

        if (!\in_array(\strtolower($type), [ 'base', 'all' ])) {
            throw new InvalidArgumentException('Invalid type value(base/all): ' . $type);
        }

        // 封装 query 参数，并对空值进行过滤。
        $query = array_filter([
            'key'        => $this->key,
            'city'       => $city,
            'output'     => $format,
            'extensions' => $type,
        ]);

        try {
            // 3. 调用 getHTTPClient 获取实例，并调用该实例的 `get` 方法，
            // 传递参数为两个 ： $url、['query' => $query],
            $response = $this->getHttpClient()->get($url, [
                'query' => $query,
            ])->getBody()->getContents();

            // 4. 返回值根据 $format 返回不同的格式，
            // 当 $format 为 json 时，返回数组格式，否则为 xml。
            return 'json' === $format ? \json_decode($response, true) : $response;
        } catch (\Exception $e) {
            // 5. 当调用出现异常时捕获并抛出，消息为捕获到的异常消息，
            // 并将调用异常作为 $previousException 传入。
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }


    }

}