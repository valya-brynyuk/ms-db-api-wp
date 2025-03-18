<?php

class MsApi
{

    protected static function getHost() {
        if (!defined('NI_LEGAL_MS_API_HOST')) {
            throw new Exception('Missing NI_LEGAL_MS_API_HOST');
        }

        return NI_LEGAL_MS_API_HOST;
    }

    protected static function request($path, $data, $accessToken) {
        $curl = null;

        $host = static::getHost();
        $response = null;

        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $host . $path . http_build_query($data),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',

                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $accessToken,
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $response = json_decode($response);
        } catch (\Exception $e) {
        } finally {
            curl_close($curl);

            return $response;
        }
    }

    public static function getTransactionDetails($email, $accessToken)
    {
        return static::request(
            '/api/v1/transaction/details?',
            ['email' => $email],
            $accessToken
        );
    }

    public static function getBrokerListing($email, $accessToken)
    {
        return static::request(
            '/api/v1/broker?',
            ['email' => $email],
            $accessToken
        );
    }

    public static function getMatterDetails($matterId, $accessToken)
    {
        return static::request(
            '/api/v1/matter/' . $matterId,
            [],
            $accessToken
        );
    }

    public static function getMatterHistory($matterId, $accessToken)
    {
        return static::request(
            '/api/v1/matter/'.$matterId.'/history/',
            [],
            $accessToken
        );
    }

    public static function getMilestoneDates($matterId, $accessToken)
    {
        return static::request(
            '/api/v1/matter/'.$matterId.'/milestone-dates',
            [],
            $accessToken
        );
    }
}