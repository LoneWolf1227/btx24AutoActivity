<?php

class BTX24{

    function method($method, $params){
        $response = '';
        //if (array_key_exists('saved', $_REQUEST)) {
            //$this->defaults = $_REQUEST;
            $c = curl_init('https://your_bitrix24_subdomain.bitrix24.ru/rest/1/you_api_key/' . $method);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($c, CURLOPT_POST, true);
            curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query($params));
            $response = curl_exec($c);
            $response = json_decode($response, true);
        //}

        return $response;
    }
}

