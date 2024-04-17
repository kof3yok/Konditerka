// Этот код определяет функцию GetAll(), которая используется для получения различных данных из базы данных и их последующего форматирования в виде массива для дальнейшего использования.
// Сначала код подключает необходимые файлы для работы с базой данных и сервисами.
// Затем создаются объекты для работы с пользователями и заказами.
// Получаются последние 10 заказов, все заказы для панели управления и всех пользователей из базы данных.
// Далее происходит форматирование данных о последних 10 заказах в виде ассоциативного массива, содержащего ID заказа, ID пользователя, имя пользователя, цену, количество, статус и дату создания заказа.
// Если в базе данных есть данные о последних 10 заказах, они добавляются в массив $all["records"]. Если данных нет, массив $all["records"] будет пустым.
// Затем в массив $all добавляются общее количество заказов, общая сумма заказов и общее количество пользователей.
// В конце функция возвращает массив $all, содержащий все собранные данные.
<?php
function GetAll()
{
    // Get user input
    require_once '../src/System/DatabaseConnector.php';
    require_once '../Service/userorder.php';
    require_once '../Service/user.php';
    $database = new Database();
    $db = $database->getConnection();
    $uo = new UserOrder($db);
    $u = new User($db);
    $last10 = $uo->GetLast10();
    $allOrder = $uo->GetAllForDashboard();
    $allUser = $u->GetAll();

    $num = $last10->rowCount();
    $all = array();
    $all["records"] = array();
    if ($num > 0) {
        while ($row = $last10->fetch(PDO::FETCH_ASSOC)) {
            $all_item = array(
                "ID" =>   $row['ID'],
                "UserID" => $row['UserID'],
                "Username" => $row['Username'],
                "Price" => $row['Price'],
                "Quantity" => $row['Quantity'],
                "Status" => $row['Status'],
                "CreationDate" => $row['CreationDate'],
            );
            array_push($all["records"], $all_item);
        }
    } else {
        $all["records"] = null;
    }
    $all["OrderCount"] = $allOrder['Count'];
    $all["TotalPrice"] = $allOrder['Price'];
    $all["UserCount"] = $allUser['Count'];
    return $all;
}
