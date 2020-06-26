<?php
require __DIR__ . '/libs/BTX24.php';
$BT24 = new BTX24();

//Сущности и их ID для генерации ссылок
$OWNER_TYPE_ID = array(
    'lead' => 1,
    'deal' => 2,
    'contact' => 3,
    'company' => 4
);

//$subdomain = 'b24-ymflsh';
//Токен для авторизации приходящих вебхуков.
$application_token = '';

//Проверка токена
if ($_POST['auth']['application_token'] === $application_token)
{
    //Получаем дела по его ID
    $res = $BT24->method('crm.activity.get', array('id' => $_POST['data']['FIELDS']['ID']));

    $findMe = 'onlinePBX';
    $searchResult = strpos($res['result']['DESCRIPTION'], $findMe);

    if ($searchResult) {

        //Генерируем ссылку для дела
        $OWNER_TYPE = array_search($res['result']['OWNER_TYPE_ID'], $OWNER_TYPE_ID);
        $path = 'https://bitrix24.uley-tmn.ru/crm/'.$OWNER_TYPE.'/details/'.$res['result']['OWNER_ID'];

        //Берём всех пользователей с фильтром employee
        $users = $BT24->method('user.get', array(
            'FILTER' => array(
                'USER_TYPE' => 'employee'
            )
        ));

        //Делаем цыкал для количество всех найденных работников
        for ($i = 0; $i < $users['total']; ++$i) {

            //Берём количество отделей в которых работает работник
            $depCount = count($users['result'][$i]['UF_DEPARTMENT']);

            //Если работник работает на одном отделе то выполняется блок If
            //а если работник работает на нескольких отделах срабатывает блок else
            if ($depCount === 1) {

                //Проверяем отдел в котором работает работника
                //если 9 то в техническом отделе
                //если 5 то в Отделе продаж
                if ($users['result'][$i]['UF_DEPARTMENT']['0'] === 9 /*5*/) {

                    //Отправляем оповещение о пропущенных звонках
                    $BT24->method('im.notify', array(
                        'to' => $users['result'][$i]['ID'],
                        'message' => 'Пропущен звонок! Подробнее: <a href="'.$path.'/">посмотреть</a>'
                    ));
                }
            } else {
                //Цикл будет обрабатывать все отделы в котором работает работник
                for ($j = 0; $j < $depCount; ++$j) {
                    //Проверяем отдел в котором работает работника
                    //если 9 то в техническом отделе
                    //если 5 то в Отделе продаж
                    if ($users['result'][$i]['UF_DEPARTMENT'][$j] === 9 /*5*/) {

                        //Отправляем оповещение о пропущенных звонках
                        $BT24->method('im.notify', array(
                            'to' => $users['result'][$i]['ID'],
                            'message' => 'Пропущен звонок! Подробнее: <a href="'.$path.'/">посмотреть</a>'
                        ));
                    }
                }
            }
        }
    }
}
//function log_data($data)
//{
//    $file = __DIR__ . '/log.txt';
//    file_put_contents($file, var_export($data, true), FILE_APPEND);
//}