<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once 'base.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

class RenewPassword
{
  private $conn;
  private $table_name = "renewpassword";


  public $Username;
  public $Code;
  public $CreationDate;

  public function __construct($db)
  {
    $this->conn = $db;
  }
  function SendCode($to)
  {

    $mail = new PHPMailer();
    $mail->CharSet = 'UTF-8';
    $mail->isSMTP();
    // $mail->SMTPDebug = 2;
    $yourEmail = 'info-food-app@yandex.ru'; // ваш email на яндексе
    $password = 'obmfzhmonilhwsfh'; // ваш пароль к яндексу или пароль приложения
    //https://phpstack.ru/php/smtp-ot-yandex-kak-otpravit-pisma-iz-php-primer-nastroek.html
    //https://id.yandex.ru/security/app-passwords?retpath=https%3A%2F%2Fmail.yandex.ru%2F&uid=1965521206&scope=mail
    //$password = 'InfoFoodApp@1234'; // ваш пароль к яндексу или пароль приложения
    // настройки SMTP
    $mail->IsHTML(true);
    $mail->Mailer = 'smtp';
    $mail->Host = 'smtp.yandex.com';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    $mail->SMTPAuth = true;
    $mail->Username = $yourEmail; // ваш email - тот же что и в поле From:
    $mail->Password = $password; // ваш пароль;


    // формируем письмо

    // от кого: это поле должно быть равно вашему email иначе будет ошибка
    $mail->setFrom($yourEmail, 'Food App Сброс Пароля');

    // кому - получатель письма
    $mail->addAddress($to, $to);  // кому

    $mail->Subject = 'Код для сброса пароля';  // тема письма
    $code = generateRandomString();

    $query = "INSERT INTO " . $this->table_name . " (
            Username,Code,CreationDate) VALUES
        (:Username,:Code,:Creationdate);";
    $q = $this->conn->prepare($query);
    $data = [
      'Username' => $this->Username,
      'Code' => $code,
      'Creationdate' => date("Y-m-d H:i:s"),
    ];
    $q->execute($data);

    $mail->msgHTML("<html><body>
				<h3>Ваш Код:</h3>
				<p><h1>" . $code . "</h1></p>
				</html></body>");


    if ($mail->send()) { // отправляем письмо
      //  echo 'Письмо отправлено!';
    } else {
      //  echo 'Ошибка: ' . $mail->ErrorInfo;
    }
  }
  function SendCode2($to, $body)
  {

    $mail = new PHPMailer();
    $mail->CharSet = 'UTF-8';
    $mail->isSMTP();
    // $mail->SMTPDebug = 2;
    $yourEmail = 'info-food-app@yandex.ru'; // ваш email на яндексе
    $password = 'obmfzhmonilhwsfh'; // ваш пароль к яндексу или пароль приложения
    //https://phpstack.ru/php/smtp-ot-yandex-kak-otpravit-pisma-iz-php-primer-nastroek.html
    //https://id.yandex.ru/security/app-passwords?retpath=https%3A%2F%2Fmail.yandex.ru%2F&uid=1965521206&scope=mail
    //$password = 'InfoFoodApp@1234'; // ваш пароль к яндексу или пароль приложения
    // настройки SMTP
    $mail->IsHTML(true);
    $mail->Mailer = 'smtp';
    $mail->Host = 'smtp.yandex.com';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    $mail->SMTPAuth = true;
    $mail->Username = $yourEmail; // ваш email - тот же что и в поле From:
    $mail->Password = $password; // ваш пароль;


    // формируем письмо

    // от кого: это поле должно быть равно вашему email иначе будет ошибка
    $mail->setFrom($yourEmail, 'Food App Заказ');

    // кому - получатель письма
    $mail->addAddress($to, $to);  // кому

    $mail->Subject = 'Ваш Заказ';  // тема письма

    $mail->Body = $body;
    $mail->AltBody = $body;


    if ($mail->send()) { // отправляем письмо
      //  echo 'Письмо отправлено!';
    } else {
      //  echo 'Ошибка: ' . $mail->ErrorInfo;
    }
  }
  function CheckCode(): bool
  {
    $query = "SELECT Username,Code,CreationDate FROM " . $this->table_name . " 
        WHERE Username=:Username ORDER BY CreationDate DESC Limit 0,1;";
    $q = $this->conn->prepare($query);

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':Username', $this->Username);
    $stmt->execute();
    $num = $stmt->rowCount();
    if ($num > 0) {
      $Code = "";
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $Code = $row["Code"];
        break;
      }
      if ($Code == $this->Code) return true;
      return false;
    } else return false;
  }
  function SendPreparingEMail($to, $OrderDetail, $type, $driver)
  {
    $body = "<html>";
    $body = $body . $this->style;
    if ($type == 1) {
      $body = $body . "<p><h2 style=\"background-color:#ffd97d\">ВАШ ЗАКАЗ:Готовится</h2></p>";
    } else if ($type == 2) {
      $body = $body . "<p><h2 style=\"background-color:#caffbf\">ВАШ ЗАКАЗ:В пути</h2></p>";
      $body = $body . "<p><h2 style=\"background-color:#caffbf\">Курьер:" . $driver . "</h2></p>";
    } else if ($type == 3) {
      $body = $body . "<p><h2 style=\"background-color:#60d394\">ВАШ ЗАКАЗ:Доставлен</h2></p>";
    } else if ($type == 4) {
      $body = $body . "<p><h2 style=\"background-color:#ee6055\">ВАШ ЗАКАЗ:Отменён</h2></p>";
    }
    $total = 0;
    $body = $body . "<body>";
    $body = $body . $this->header;
    $body = $body . "<tbody>";
    foreach ($OrderDetail as $row) {
      $rowtmp = $this->mailrow;
      $rowtmp = str_replace("cell1", $row["ImageData"], $rowtmp);
      $rowtmp = str_replace("cell2", $row["Catalog"], $rowtmp);
      $rowtmp = str_replace("cell3", $row["Name"], $rowtmp);
      $rowtmp = str_replace("cell4", $row["Quantity"], $rowtmp);
      $rowtmp = str_replace("cell5", $row["Price"], $rowtmp);
      $rowtmp = str_replace("cell6", $row["Total"], $rowtmp);
      $total += $row["Total"];
      $body = $body . $rowtmp;
    }
    $footertmp = $this->footer;
    $body = $body . str_replace("foot6", $total, $footertmp);
    $body .= "</body></html>";
    $this->SendCode2($to, $body);
  }
  private $style = "<style>table.cinereousTable {
        border: 6px solid #948473;
        background-color: #FFE3C6;
        width: 100%;
        text-align: center;
      }
      table.cinereousTable td, table.cinereousTable th {
        border: 1px solid #948473;
        padding: 4px 4px;
      }
      table.cinereousTable tbody td {
        font-size: 13px;
      }
      table.cinereousTable thead {
        background: #948473;
        background: -moz-linear-gradient(top, #afa396 0%, #9e9081 66%, #948473 100%);
        background: -webkit-linear-gradient(top, #afa396 0%, #9e9081 66%, #948473 100%);
        background: linear-gradient(to bottom, #afa396 0%, #9e9081 66%, #948473 100%);
      }
      table.cinereousTable thead th {
        font-size: 17px;
        font-weight: bold;
        color: #F0F0F0;
        text-align: left;
        border-left: 2px solid #948473;
      }
      table.cinereousTable thead th:first-child {
        border-left: none;
      }
      
      table.cinereousTable tfoot {
        font-size: 16px;
        font-weight: bold;
        color: #F0F0F0;
        background: #948473;
        background: -moz-linear-gradient(top, #afa396 0%, #9e9081 66%, #948473 100%);
        background: -webkit-linear-gradient(top, #afa396 0%, #9e9081 66%, #948473 100%);
        background: linear-gradient(to bottom, #afa396 0%, #9e9081 66%, #948473 100%);
      }
      table.cinereousTable tfoot td {
        font-size: 16px;
      }</style>";

  private $header = "<table class=\"cinereousTable\">
    <thead>
      <tr>
        <th>КАРТИНКА</th>
        <th>КАТЕГОРИЯ</th>
        <th>ТОВАР</th>
        <th>КОЛ-ВО ПОЗИЦИЙ</th>
        <th>ЦЕНА</th>
        <th>ИТОГ</th>
      </tr>
    </thead>";
  private $footer = "</tbody>
    <tfoot>
      <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>foot6</td>
      </tr>
    </tfoot>
  </table>";
  private $mailrow = "<tr>
      <td><img height=\"64\" width=\"64\" src=\"data:image/png;base64,cell1\"></img></td>
      <td>cell2</td>
      <td>cell3</td>
      <td>cell4</td>
      <td>cell5</td>
      <td>cell6</td>
      </tr>";
}
