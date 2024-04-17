// Этот код - класс Login, который содержит функции для работы с аутентификацией и регистрацией пользователей в базе данных.
<?php
require_once 'base.php';

class Login
{
    private $conn;
    private $table_name = "user";

    public $ID;
    public $username;
    public $password;
    public $email;
    public $phone;
    // то конструктор класса, который принимает объект базы данных и устанавливает его в приватное свойство $conn
    public function __construct($db)
    {
        $this->conn = $db;
    }
    // Это метод для проверки аутентификации пользователя. Он выполняет запрос к базе данных, чтобы найти пользователя по его имени пользователя (Username). 
    // Возвращает данные пользователя, если пользователь найден, в противном случае возвращает null
    function Login()
    {
        $this->username = htmlspecialchars(strip_tags($this->username));

        $query = "SELECT ID, Username, Password FROM " . $this->table_name .
            " WHERE Username = :username LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $this->username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    //  Этот метод аналогичен методу Login, но также проверяет, является ли пользователь администратором (путем проверки поля IsAdmin)
    function AdminLogin()
    {
        $this->username = htmlspecialchars(strip_tags($this->username));

        $query = "SELECT ID, Username, Password FROM " . $this->table_name .
            " WHERE IsAdmin = 1 AND Username = :username LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $this->username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Этот метод регистрирует нового пользователя. Он хэширует пароль с использованием password_hash, чтобы сохранить его безопасным в базе данных, затем вставляет новую запись в таблицу пользователей.
    function Register()
    {
        $this->username = htmlspecialchars(strip_tags($this->username));
        // $password = htmlspecialchars(strip_tags($password));
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));

        $query = "INSERT INTO USER(ID, Username, Password,CreationDate,EMail,phone) VALUES
        (?,?,?,?,?,?);";
        // (':ID',':username',':password',':creationdate',':email');";

        $stmt = $this->conn->prepare($query)->execute([GUID(), $this->username, $this->password, date("Y-m-d H:i:s"), $this->email, $this->phone]);
        // $stmt->bindParam(':ID', GUID());
        // $stmt->bindParam(':username', $this->$username);
        // $stmt->bindParam(':password', $this->$password);
        // $stmt->bindParam(':email', $this->$email);
        // $stmt->bindParam(':creationdate', date("Y-m-d H:i:s"));
        // $data = [
        //     'ID' => GUID(),
        //     'username' => $username,
        //     'password' => $password,
        //     'email' => $email,
        //     'creationdate' => date("Y-m-d H:i:s")
        // ];
        // $stmt->execute($data);
        $result = $this->Login();
        return $result;
    }
    // Этот метод обновляет информацию о пользователе в базе данных. Он обновляет имя пользователя, адрес электронной почты и номер телефона. При необходимости обновляет пароль, хэшируя его снова
    function Update()
    {
        $this->username = htmlspecialchars(strip_tags($this->username));
        // $password = htmlspecialchars(strip_tags($password));
        if (!empty($this->password))
            $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));

        $query = "UPDATE " . $this->table_name .
            " SET " .
            (!empty($this->password) ? "Password = :password ," : "")
            . " EMail =:email,
            Phone =:phone
            WHERE ID = :ID;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ID', $this->ID);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        if (!empty($this->password))
            $stmt->bindParam(':password', $this->password);

        if ($stmt->execute()) return true;
        return false;
    }
}
