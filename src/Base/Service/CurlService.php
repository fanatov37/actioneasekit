<?php

declare(strict_types=1);

namespace ActionEaseKit\Base\Service;

use ActionEaseKit\Base\Exception\App404Exception;
use ActionEaseKit\Base\Exception\CurlResponseException;

final class CurlService
{
    public function postJson(string $url, array $data=[], array $options = [], string $proxy='', int $timeout = 300) : array
    {
        $response = json_decode($this->post($url, $data, $options, $proxy, $timeout), true);

        if (json_last_error() !== 0) {
            throw new App404Exception('Wrong json result');
        }

        return $response;
    }

    public function post(string $url, array $data=[], array $options = [], string $proxy='', int $timeout = 300)
    {
        $postfields = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($ch, CURLOPT_HTTPHEADER,['Content-Type: application/json', 'Content-Length: ' . strlen($postfields)]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        foreach ($options as $option=>$value){
            curl_setopt($ch, $option, $value);
        }

        if ($proxy) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }

        $result = curl_exec($ch);

        if ($curlError = curl_error($ch)) {
            throw new CurlResponseException("Response fail. Curl error: {$curlError}");
        }

        curl_close($ch);

        return $result;
    }

    public function get(string $url, array $data)
    {
        $i = 0;
        foreach ($data as $key => $value) {
            if ($i === 0) {
                $url .= '?';
            } else {
                $url .= '&';
            }

            $url .= $key;

            if ($value && (trim($value) != '')) {
                $url .='='.$value;
            }

            ++$i;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }
}
