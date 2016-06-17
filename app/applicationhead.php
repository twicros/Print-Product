<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Создание формы обратной связи</title>
</head>
<body>
<?php
 
$sendto   = "twicros@yandex.ru"; // почта, на которую будет приходить письмо
$usertelhead = $_POST['telephonehead']; // сохраняем в переменную данные полученные из поля c телефонным номером
 
// Формирование заголовка письма
$subject  = "Заказ звонка";
$headers  = "From: mail@printproduct3d.ru \r\n";
$headers .= "Reply-To: mail@printproduct3d.ru";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html;charset=utf-8 \r\n";
 
// Формирование тела письма
$msg  = "<html><body style='font-family:Arial,sans-serif;'>";
$msg .= "<h2 style='font-weight:bold;border-bottom:1px dotted #ccc;'>Cообщение с сайта</h2>\r\n";
$msg .= "<p><strong>Номер телефона:</strong> ".$usertelhead."</p>\r\n";
$msg .= "</body></html>";
 
// отправка сообщения
if(@mail($sendto, $subject, $msg, $headers)) {
    header("Location: thankyou.html");
    exit();
} else {
   header("Location: error.html");
   exit();
}
 
?>

</body>
</html>