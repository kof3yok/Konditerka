// Проверяет, был ли запрос методом POST и была ли отправлена переменная 'save_update'. Если это так, код обрабатывает данные формы, которые, предположительно, были отправлены, для обновления или создания записи в базе данных. 
// Для этого он подключает файлы DatabaseConnector.php и catalog.php, создает экземпляр класса Database, устанавливает соединение с базой данных и создает экземпляр класса Catalog, который отвечает за работу с данными каталога. 
// Затем он устанавливает значения свойств ID и Name объекта Catalog на основе данных формы, и в зависимости от значения ID либо вызывает метод Update() для обновления существующей записи, либо вызывает метод Create() для создания новой записи в базе данных.
// После завершения обработки запроса код перенаправляет пользователя на страницу catalog.php.
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_update'])) {

    include_once '../../src/System/DatabaseConnector.php';
    include_once '../../Service/catalog.php';
    $database = new Database();
    $db = $database->getConnection();
    $catalog = new Catalog($db);
    $catalog->ID = $_POST["ID"];
    $catalog->Name = $_POST["Name"];
    if ($catalog->ID != 0) {
        $catalog->Update();
    } else {
        $result = $catalog->Create();
        $catalog->ID = $result["ID"];
    }
    header('Location: ../catalog.php');
}
// Есть две функции, GetAll() и Get($id), которые, предположительно, используются для получения данных о каталоге. 
// Они также подключают необходимые файлы и создают экземпляры классов Database и Catalog, устанавливают соединение с базой данных,
// а затем вызывают соответствующие методы объекта Catalog для получения всех записей каталога или конкретной записи по заданному ID.
function GetAll()
{
    // Get user input
    require_once '../src/System/DatabaseConnector.php';
    require_once '../Service/catalog.php';
    $database = new Database();
    $db = $database->getConnection();
    $catalog = new Catalog($db);
    return $catalog->GetAll();
}
function Get($id)
{
    // Get user input
    require_once '../src/System/DatabaseConnector.php';
    require_once '../Service/catalog.php';
    $database = new Database();
    $db = $database->getConnection();
    $catalog = new Catalog($db);
    $catalog->ID = $id;
    return $catalog->Get();
}

