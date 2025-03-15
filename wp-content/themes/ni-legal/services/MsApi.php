<?php

class MsApi
{

    protected static function getHost() {
        if (!defined('NI_LEGAL_MS_API_HOST')) {
            throw new Exception('Missing NI_LEGAL_MS_API_HOST');
        }

        return NI_LEGAL_MS_API_HOST;
    }
    public static function getTransactionDetails($email, $accessToken)
    {
        static::getHost();
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => NI_LEGAL_MS_API_HOST . '/api/v1/transaction/details?' . http_build_query(['email' => $email]),
//					CURLOPT_URL => 'https://wn.azurewebsites.net/api/Matter/GetClientTransactionDetails',
            //CURLOPT_URL => 'https://wnpreprod.focisportal.co.uk/API/api/Matter/GetClientTransactionDetails',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
//					 CURLOPT_POSTFIELDS => '"JosephBloggs@TestAccount.co.uk"',
            // CURLOPT_POSTFIELDS => '"phigginsontestbroker1@wilson-nesbitt.co.uk"',
            // CURLOPT_POSTFIELDS => '"testing104@wilson-nesbitt.co.uk"',
//					CURLOPT_POSTFIELDS => '"'.$username.'"',
            // CURLOPT_POSTFIELDS => '"'.$email_value.'"',

            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }
}