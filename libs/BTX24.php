<?php

class BTX24{

    //public $defaults = array('first_name' => '', 'phone' => '', 'dolgnost' => '', 'email' => '');

    function method($method, $params){
        $response = '';
        //if (array_key_exists('saved', $_REQUEST)) {
            //$this->defaults = $_REQUEST;
            $c = curl_init('https://b24-ymflsh.bitrix24.ru/rest/1/vyf8418tywover3t/' . $method);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($c, CURLOPT_POST, true);
            curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query($params));
            $response = curl_exec($c);
            $response = json_decode($response, true);
        //}

        return $response;
    }
}

