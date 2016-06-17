<?php

/**
 * @author    Samoylov Nikolay
 * 
 * @project   Sendmail Script
 * @copyright 2014 <samoylovnn@gmail.com>
 */

### Настойки ###################################################################

error_reporting(0); // По-умолчанию 'выключено'
$SiteName      = 'Yoursite.com'; // Имя сайта (указывается в письме)
$MailSubject   = 'Сообщение с сайта '.$SiteName; // Тема письма 'по-умолчанию'
$BackToSiteUrl = 'http://yoursite.com/'; // Ссылка для возврата на сайт, откуда 
                                         //   пришло письмо (отображается только
                                         //   в html-версии письма)

// Массив - кому отправлять письмо с сообщением, его формат следующий:
//    'to'      - email-адрес (произвольная строка)
//    'subject' - тема (заголовок) письма (произвольная строка)
//    'type'    - тип письма (строка, 'html' или 'text')

$sendTo = array(
    array(
        'to' => 'your@default.email',
        'subject' => $MailSubject,
        'type' => 'html'
    ),
    array(
        'to' => 'your@notification.email',
        'subject' => $MailSubject,
        'type' => 'text'
    ),
);

// НАСТРОЙКА СОДЕРЖИМОГО ПИСЕМ производится ниже (~100 и ~210 строки)

################################################################################

$Ajax = true; // Указатель на то, каким образом был отправлен запрос

// Массив для хранения результатов работы в формате {msg: 'message', status: 0}
// Описание статусов:
//   -1 - скрипт запущен, ошибок нет
//    0 - произошла ошибка
//    1 - скрипт завершил работу без ошибок
$result = array('status' => -1, 'msg' => 'Нет входящих данных');

// Если пустые оба массива - у нас директивный запуск скрипта
if(empty($_POST) and empty($_GET)) {
    header("HTTP/1.0 503 Service Unavailable");
    die();
}

// Наш AJAX отправляет запрос методом GET, а сама html форма - методом POST
//   Поэтому, если у нас есть хоть что-нибудь в глобальном $_POST и 
//   пусто-пусто в $_GET - запрос выполнен без AJAX
if((count($_POST) > 0) and (count($_GET) === 0)) {
    $Ajax = false;
    // Переносим данные из $_POST в $_GET
    $_GET = $_POST;
    // Очищаем $_POST
    unset($_POST);
}

//$result['input'] = $_GET; // Для отладоньки

if(!empty($_GET['message_text'])) {
//if(1==1){ // Для отладоньки

    // Получаем данные и сохраняем в массив
    $formData = array(
        'name'   => $_GET['name'],
        'email'  => $_GET['email'],
        'phone'  => $_GET['phone'],
        'message_text'  => $_GET['message_text']
    );
    // Делаем легкую чистку от всяких гадостей всех элементов массива
    foreach ($formData as $key => $value) {
        $formData[$key] = htmlspecialchars(strip_tags($value));
    }
    
    // Проверка на валидность E-mail адреса
    if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $formData['email'])){
		$result['msg'] = 'Некорректный e-mail адрес';
		$result['status'] = 0;
    }
    
    // Проверка на заполненность поля
    if (empty($formData['message_text'])){
		$result['msg'] = 'Отсутствует сообщение';
		$result['status'] = 0;
    }

    // Если ошибок не было - готовим отправку
    if($result['status'] === -1){
        // Тело HTML письма
        // Шаблон письма взят отсюда: <http://tedgoas.github.io/Cerberus/>
        $mailBodyHtml = <<<EOB
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>{$MailSubject}</title>
    <style type="text/css">
    body,#bodyTable{
        height:100%!important;width:100%!important;margin:0;padding:0}
    body,table,td,p,a,li,blockquote{
        -ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}
    .thread-item.expanded .thread-body .body,.msg-body{
        width:100%!important;display:block!important}
    .ReadMsgBody,.ExternalClass{width:100%;background-color:#f4f4f4}
    .ExternalClass,.ExternalClass p,.ExternalClass span,.ExternalClass font,
    .ExternalClass td,.ExternalClass div{line-height:100%}
    table{border-spacing:0}
    table,td{border-collapse:collapse;mso-table-lspace:0;mso-table-rspace:0}
    img{-ms-interpolation-mode:bicubic}
    img,a img{border:0;outline:none;text-decoration:none}
    .yshortcuts a{border-bottom:none!important}
    .small {font-size: 70% !important}
    a[href]{color:#444!important}
    @media only screen and (min-width: 601px) {
        .email-container{width:600px!important}
    }
    </style>
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"
    bgcolor="#f4f4f4" style="margin:0;padding:0;-webkit-text-size-adjust:none;
        -ms-text-size-adjust:none;">
    <table cellpadding="0" cellspacing="0" border="0" height="100%" width="100%"
        bgcolor="#f4f4f4" id="bodyTable">
        <tr>
            <td>
                <!--[if (gte mso 9)|(IE)]>
                  <table width="600" align="center" cellpadding="0"
                    cellspacing="0" border="0">
                    <tr>
                      <td>
                  <![endif]-->
                <table border="0" width="100%" cellpadding="0" cellspacing="0"
                    align="center" style="max-width: 600px;margin: auto;"
                    class="email-container">
                    <tr>
                        <td>
                            <!-- Logo Left, Nav Right : BEGIN -->
                            <table border="0" width="100%" cellpadding="0"
                                cellspacing="0">
                                <tr>
                                    <td height="40" style="font-size: 0;
                                        line-height: 0;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td valign="middle"
                                        style="padding-left:30px;
                                        text-align:left;"></td>
                                    <td valign="middle"
                                        style="padding-right:40px;
                                        text-align: right;">
                                      <a href="{$BackToSiteUrl}"
                                        target="_blank"
                                        style="color:#888888;
                                        font-family:sans-serif;
                                        white-space: nowrap;">
                                            Перейти на сайт</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="40"
                                        style="font-size:0;
                                        line-height:0;">&nbsp;</td>
                                </tr>
                            </table>
                            <!-- Logo Left, Nav Right : END -->
                            <table border="0" width="100%" cellpadding="0"
                                cellspacing="0" bgcolor="#ffffff">
                                <!-- Full Width, Fluid Column : BEGIN -->
                                <tr>
                                    <td style="padding: 40px;
                                        font-family:sans-serif;
                                        font-size:20px;
                                        line-height:27px;
                                        color:#666666;">
                                        {$MailSubject}:
                                        <br />
                                    </td>
                                </tr>
                                <tr>
                                    <td bgcolor="#f4f4f4" style="padding: 40px;
                                        font-family:sans-serif;
                                        font-size: 20px;
                                        line-height: 27px;
                                        color: #666666;">
                                        <small class="small">
                                            Имя:
                                        </small><br />
                                            {$formData['name']}<br /><br />
                                        <small class="small">
                                            E-Mail:
                                        </small><br />
                                        <a href="mailto:{$formData['email']}">
                                            {$formData['email']}
                                        </a><br /><br />
                                        <small class="small">
                                            Номер телефона:
                                        </small><br />
                                            {$formData['phone']}<br /><br />
                                        <small class="small">
                                            Сообщение:
                                        </small><br />
                                            {$formData['message_text']}
                                        <br />
                                        </td>
                                </tr>
                                <tr>
                                    <td></td>
                                </tr>
                                <!-- Full Width, Fluid Column : END -->
                            </table>
                        </td>
                    </tr>
                    <!-- Footer : BEGIN -->
                    <tr>
                        <td style="text-align:center;padding:40px 0;
                            font-family: sans-serif; font-size: 12px;
                            line-height: 18px;color: #888888;">
                                Почтовый робот &copy; {$SiteName}
                            <br />
                            <br />
                        </td>
                    </tr>
                    <!-- Footer : END -->
                </table>
                <!--[if (gte mso 9)|(IE)]>
                  </td>
                </tr>
              </table>
              <![endif]-->
            </td>
        </tr>
    </table>
</body>

</html>
EOB;
        // Тело TEXT письма
        $mailBodyText = <<<EOB
{$MailSubject} [$BackToSiteUrl]

Имя:
{$formData['name']}

E-Mail:
{$formData['email']}

Номер телефона:
{$formData['phone']}

Сообщение:
{$formData['message_text']}


--
Почтовый робот @ {$SiteName}
EOB;

        $errorTrySendingMail = false;
        foreach($sendTo as $client) {
            // Заголовочки
            $headers = "MIME-Version: 1.0\r\n";
            
            switch($client['type']) {
              case 'html':
                $headers .= "Content-Type: text/html; charset=\"utf-8\""."\r\n";
                $mailBody = $mailBodyHtml;
                break;
              case 'text':
                $headers .= "Content-Type: text/plain; charset=\"utf-8\""."\r\n";
                $mailBody = $mailBodyText;
                break;
            }
            
            $headers .= "From: ".$formData['email']."\r\n".
                        "Reply-To: ".$formData['email']."\r\n".
                        "Return-Path: ".$formData['email']."\r\n";
        
            // И сама отправка
        	if (!mail(
                    $client['to'], 
                    '=?UTF-8?B?'.base64_encode($client['subject']).'?=', 
                    $mailBody, 
                    $headers
                )) {
                    $errorTrySendingMail = true;
                    break;
        	}
        }
        if(!$errorTrySendingMail) {
    		$result['msg'] = 'Сообщение успешно отправлено';
    		$result['status'] = 1;
        } else {
    		$result['msg'] = 'Что-то пошло не так (ошибка сервера)';
            $result['status'] = 0;
        }
        
    }
}

// Выводим результат работы в удобочитаемом виде, если запрос был не AJAX
if(!$Ajax){
    header('Content-Type: text/html; charset=UTF-8');
    $html = <<<EOD
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style type="text/css">
            html,body{padding:0;margin:0;background:#eee;font-family:Tahoma,
            Verdana,Arial}ul{position:absolute;width:500px;height:300px;top:50%;
            left:50%;margin:-160px 0 0 -260px;background:#e5e5e5;
            -webkit-border-radius:20px;-moz-border-radius:20px;
            border-radius:20px;list-style:none;padding:20px;overflow:auto}
            ul li{margin:10px 0}ul li.title{display:block;text-align:center;
            font-size:2em;margin:10px 0 20px;color:#444}ul li span.key{
            font-weight:700;display:block;float:left;width:17%;
            text-transform:uppercase}
        </style>
    </head>
<body>
    <ul>
      <li class="title">Отправка сообщения:</li>
EOD;
    while($item = current($result)) {
        $html .= "\r\n".'      <li><span class="key">'.key($result).
            '</span> <span class="msg">'.$item.'</span></li>';
        next($result);
    }
    
    $html .= <<<EOD
    </ul>
</html></body>
EOD;
    die($html);
}


// В любых иных ситуациях выводим результат в JSON виде
header('Content-Type: application/json');
echo(json_encode($result));
