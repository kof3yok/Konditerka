// API для взаимодействия с данными для входа в приложение между приложением и БД
<?php
// Установка заголовков CORS: Устанавливает заголовки CORS для обработки запросов из другого источника. Это позволяет клиентскому приложению обращаться к этому API из других доменов.
// Включение заголовков запроса: Устанавливает разрешенные методы и максимальное время жизни запроса.
// Включение необходимых заголовков: Указывает разрешенные заголовки, которые могут быть использованы в запросах.
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// Подключение необходимых файлов и классов: Включает файлы с определением классов и функций, которые будут использоваться для обработки запросов.
include_once '../src/System/DatabaseConnector.php';
include_once '../Service/login.php';
include_once '../Service/user.php';
include_once '../Service/address.php';
include_once '../Service/mail.php';
include_once '../Service/sendJson.php';
// Обработка POST-запросов: Проверяет тип запроса (POST) и обрабатывает данные в зависимости от метода, переданного в параметре method GET-запроса.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_GET['method'];
    switch ($method) {
// Обработка метода login: Проверяет, заполнены ли все необходимые поля в запросе, затем производит аутентификацию пользователя и отправляет токен пользователя в ответ.
        case 'login':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->username) ||
                    !isset($data->password) ||
                    empty(trim($data->username)) ||
                    empty(trim($data->password))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['username', 'password']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $login = new Login($db);

                    $login->username = trim($data->username);
                    $login->password = trim($data->password);

                    $result = $login->Login();
                    if ($result === false) {
                        sendJson(200, 'User not found!');
                    } else {
                        if (!password_verify($login->password, $result['Password'])) sendJson(200, 'Incorrect Password!');
                        else
                            sendJson(200, '', [
                                'token' => $result['ID']
                            ]);
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Обработка метода register: Проверяет, заполнены ли все необходимые поля в запросе, затем регистрирует нового пользователя и отправляет токен пользователя в ответ.
        case 'register':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->username) ||
                    !isset($data->password) ||
                    empty(trim($data->username)) ||
                    empty(trim($data->password))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['username', 'password']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $login = new Login($db);

                    $login->username = trim($data->username);
                    $login->password = trim($data->password);

                    $result = $login->Login();
                    if ($result === false) {
                        sendJson(200, 'User not found!');
                    } else {
                        if (!password_verify($login->password, $result['Password'])) sendJson(200, 'Incorrect Password!');
                        else
                            sendJson(200, '', [
                                'token' => $result['ID']
                            ]);
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Обработка метода update: Проверяет, заполнены ли все необходимые поля в запросе, затем обновляет данные пользователя и адреса.
        case 'update':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->token) ||
                    !isset($data->username) ||
                    !isset($data->password) ||
                    !isset($data->email) ||
                    !isset($data->phone) ||
                    !isset($data->address) ||
                    empty(trim($data->token)) ||
                    empty(trim($data->username)) ||
                    empty(trim($data->email)) ||
                    empty(trim($data->phone)) ||
                    empty(trim($data->address))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['username', 'password', 'email', 'phone', 'address']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $login = new Login($db);

                    $login->ID = trim($data->token);
                    $login->username = trim($data->username);
                    $login->password = trim($data->password);
                    $login->email = trim($data->email);
                    $login->phone = trim($data->phone);

                    $result = $login->Update();
                    if ($result === null) {
                        sendJson(200, 'User cannot update!');
                    } else {
                        $address = new Address($db);
                        $address->Address = $data->address;
                        $address->UserID = $data->token;
                        $address->CreateOrUpdate();
                        sendJson(200, '', [
                            'token' => $data->token
                        ]);
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Обработка метода getbyid: Проверяет, заполнены ли все необходимые поля в запросе, затем получает данные пользователя по его идентификатору и отправляет их в ответ.
        case 'getbyid':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->token) ||
                    empty(trim($data->token))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['token']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $pp = new User($db);

                    $pp->ID = trim($data->token);
                    $result = $pp->GetByID();
                    $num = $result->rowCount();

                    $ua = new Address($db);
                    $ua->UserID = trim($data->token);
                    $resultAddr = $ua->GetByUserID();
                    $numAddr = $resultAddr->rowCount();
                    $address = "";
                    if ($numAddr > 0) {
                        while ($row = $resultAddr->fetch(PDO::FETCH_ASSOC)) {
                            $address = $row["Address"];
                        }
                    }

                    if ($num > 0) {
                        $pp_arr = array();
                        $pp_arr["records"] = array();
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            // extract($row);
                            $pp_item = array(
                                "ID" => $row['ID'],
                                "Username" => $row['Username'],
                                "EMail" => $row['EMail'],
                                "Phone" => $row['Phone'],
                                "Address" => $address,
                            );
                            array_push($pp_arr["records"], $pp_item);
                        }
                        sendJson(200, '', $pp_arr);
                    } else {
                        sendJson(200, 'Unexcepted Error');
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Обработка метода sendcode: Проверяет, заполнены ли все необходимые поля в запросе, затем отправляет код восстановления пароля пользователю по его имени пользователя.
        case 'sendcode':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->username) ||
                    empty(trim($data->username))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['username']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $user = new User($db);
                    $mail = new RenewPassword($db);

                    $user->username = trim($data->username);
                    $result = $user->GetByUsername();
                    $num = $result->rowCount();
                    if ($num > 0) {
                        $mail->Username = $data->username;
                        $to = "";
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            $to = $row["EMail"];
                            break;
                        }
                        $mail->SendCode($to);
                        sendJson(200,'');
                    } else {
                        sendJson(200, 'User not found!');
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Обработка метода changepwd: Проверяет, заполнены ли все необходимые поля в запросе, затем проверяет код восстановления пароля и, если он верен, изменяет пароль пользователя.    
        case 'changepwd':
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (
                    !isset($data->username) ||
                    !isset($data->code) ||
                    !isset($data->password) ||
                    empty(trim($data->password)) ||
                    empty(trim($data->code)) ||
                    empty(trim($data->username))
                ) {
                    sendJson(422, 'Please fill all fields', ['required_fields' => ['username']]);
                } else {
                    $database = new Database();
                    $db = $database->getConnection();
                    $user = new User($db);
                    $mail = new RenewPassword($db);

                    $user->username = trim($data->username);
                    $result1 = $user->GetByUsername();
                    $num = $result1->rowCount();
                    if ($num > 0) {
                        $mail->Username = $data->username;
                        while ($row = $result1->fetch(PDO::FETCH_ASSOC)) {
                            $user->ID = $row["ID"];
                            break;
                        }
                        $mail->Code = $data->code;

                        $result2 = $mail->CheckCode();
                        if ($result2) {
                            $user->password = $data->password;
                            $result3 = $user->ChangePwd();
                            if ($result3)
                                sendJson(200, '');
                            else  sendJson(200, 'User not found!');
                        } else sendJson(200, 'User not found!');
                    } else {
                        sendJson(200, 'User not found!');
                    }
                }
            } catch (\Throwable $th) {
                sendJson(500, 'Internal Server Error');
            }
            break;
// Отправка ответов в формате JSON: Формирует ответы в формате JSON с соответствующими HTTP-статусами и сообщениями в случае успеха или ошибки.
        default:
            sendJson(405, 'Invalid Request Method. HTTP method should be POST');
            break;
    }
}
sendJson(405, 'Invalid Request Method. HTTP method should be POST');
