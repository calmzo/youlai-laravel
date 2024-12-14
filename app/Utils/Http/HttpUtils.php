<?php

namespace App\Utils\Http;

class HttpUtils
{

    /**
     * Get Url 数据
     * @param $url
     * @return bool|string
     */
    public static function sendGet($url, $res = "json")
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $output = curl_exec($ch);
        curl_close($ch);
        if ($res == "json") {
            return json_decode($output, true);
        }
        return $output;
    }


    public static function sendPost($url, $postData, $timeout = 10)
    {
        $jsonStr = json_encode($postData, JSON_UNESCAPED_UNICODE);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonStr),
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);

        return json_decode($data, true);
    }

    public static function sendClientGet($url, $options = [])
    {

        $client = new \GuzzleHttp\Client($options);
        try {
            $response = $client->get($url);
            if (200 != $response->getStatusCode()) {
                return false;
            }

            $response = $response->getBody()->getContents();
            return json_decode($response, true);
        } catch (\Exception $e) {
            return false;
        }
    }

}
