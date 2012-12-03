<?php
namespace NGS;

abstract class Connector
{
    const URL = 'https://api.dsl-platform.com/alpha/';

    public static function call(array $dsls, $overwrite = false)
    {
        $action = $overwrite === true ? 'overwrite' : 'update';
        $curl = curl_init(self::URL.$action.'/'.Project::$ID);

        $body = json_encode($dsls);
        $headers = array(
            'Accept: application/json; charset=UTF-8',
            'Content-type: application/json; charset=UTF-8'
        );

        if (\Bootstrap::canCompress()) {
            $body = gzdeflate($body);
            curl_setopt($curl, CURLOPT_ENCODING, 'deflate');
            $headers[] ='Content-Encoding: deflate';
        }

        curl_setopt_array($curl, array(
            CURLOPT_CAINFO => \Bootstrap::ensureCertPath(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_POST => true
        ));

        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        $ok = $status >= 200 && $status < 300;

        if (strlen($error) > 0) {
            $response = $error;
        }

        return array(
            'ok' => $ok,
            'status' => $status,
            'data' => $response
        );
    }
}
