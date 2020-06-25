<?php
require __DIR__ . '/libs/BTX24.php';
$BT24 = new BTX24();


$OWNER_TYPE_ID = array(
    'lead' => 1,
    'deal' => 2,
    'contact' => 3,
    'company' => 4
);

$subdomain = 'your_bitrix24_dubdomain';

if ($_POST['auth']['application_token'] === 'your_application_token')
{
    $res = $BT24->method('crm.activity.get', array('id' => $_POST['data']['FIELDS']['ID']));
    $OWNER_TYPE = array_search($res['result']['OWNER_TYPE_ID'], $OWNER_TYPE_ID);
    $path = 'https://'.$subdomain.'.bitrix24.ru/crm/'.$OWNER_TYPE.'/details/'.$res['result']['OWNER_ID'];
    $findMe = 'onlinePBX';
    $searchResult = strpos($res['result']['DESCRIPTION'], $findMe);

    if ($searchResult) {
        $users = $BT24->method('user.get', array(
            'FILTER' => array(
                'USER_TYPE' => 'employee'
            )
        ));

        for ($i = 0; $i < $users['total']; ++$i) {
            $depCount = count($users['result'][$i]['UF_DEPARTMENT']);
            if ($depCount === 1) {
                if ($users['result'][$i]['UF_DEPARTMENT']['0'] === 5) {
                    $BT24->method('im.notify', array(
                        'to' => $users['result'][$i]['ID'],
                        'message' => $res['result']['SUBJECT'].'#BR#'.$path.'/',
                        'type' => 'SYSTEM'
                    ));
                }
            } else {
                for ($j = 0; $j < $depCount; ++$j) {
                    if ($users['result'][$i]['UF_DEPARTMENT'][$j] === 5) {
                        $BT24->method('im.notify', array(
                            'to' => $users['result'][$i]['ID'],
                            'message' => $res['result']['SUBJECT'].'#BR#'.$path,
                            'type' => 'SYSTEM'
                        ));
                    }
                }
            }
        }
    }
}
