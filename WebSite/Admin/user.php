// Старница, отображающая всех зарегистрированных пользователей приложения
<?php
// Начало сессии: session_start() используется для инициализации сессии и доступа к данным сессии.
session_start();
// Проверка авторизации: С помощью условного оператора if проверяется, авторизован ли пользователь. Если в сессии установлен ключ loggedin и его значение равно true, то пользователь считается авторизованным. 
// В противном случае пользователь считается неавторизованным.
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
// Отображение контента в зависимости от авторизации: Если пользователь авторизован ($adminloggedin == true), то отображается содержимое страницы с данными о пользователях. 
// В противном случае пользователь перенаправляется на страницу входа.
    $adminloggedin = true;
    $userId = $_SESSION['userId'];
} else {
    $adminloggedin = false;
    $userId = 0;
}

if ($adminloggedin) {
// Формирование HTML-разметки страницы с данными о пользователях: В этой разметке определены стили, скрипты и содержимое страницы с данными о пользователях. 
// Данные о пользователях получаются из базы данных и выводятся в виде таблицы. Также предусмотрена возможность фильтрации пользователей по их логину.
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Product Page</title>
        <link rel="stylesheet" href="../css/styles.css">
        <link rel="stylesheet" href="../css/site.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="../scripts/site.js?v=1.0.0"></script>
    </head>

    <body>
        <div class="pcoded iscollapsed" id="pcoded" theme-layout="vertical" vertical-layout="wide" vertical-placement="left" vnavigation-view="view1" pcoded-device-type="desktop" vertical-nav-type="expanded" vertical-effect="shrink">
            <div class="pcoded-overlay-box"></div>
            <div class="pcoded-container navbar-wrapper">
                <nav class="navbar header-navbar pcoded-header" header-theme="theme4" pcoded-header-position="fixed">
                    <div class="navbar-wrapper">
                        <div class="navbar-logo" navbar-theme="theme4"><a class="mobile-menu" onclick="showMenu()" href="javascript:;" id="mobile-collapse">
                                <i class="ti-menu"></i></a><a class="ng-tns-c2-0" href="/">
                                <img class="img-fluid" alt="Theme-Logo" src="../images/sidemenu.png"></a>
                            <a class="mobile-options">
                                <i class="ti-more"></i>
                            </a>
                        </div>
                        <div class="navbar-container">
                            <div class="ng-tns-c2-0">
                                <ul class="nav-left">
                                    <li class="ng-tns-c2-0">
                                        <div class="sidebar_toggle"><a class="ng-tns-c2-0" href="javascript:;"><i class="ti-menu f-18"></i></a></div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
                <div class="pcoded-main-container" style="margin-top: 56px;">
                    <div class="pcoded-wrapper">
                        <nav class="pcoded-navbar" active-item-style="style0" active-item-theme="theme4" id="main_navbar" navbar-theme="themelight1" pcoded-header-position="fixed" pcoded-navbar-position="fixed" sub-item-theme="theme2">
                            <div class="sidebar_toggle"><a class="ng-tns-c2-0" href="javascript:;"><i class="icon-close icons"></i></a></div>
                            <div class="pcoded-inner-navbar main-menu" appaccordion="">
                                <div style="position: static;" class="ps">
                                    <div class="ps-content">
                                        <div class="ng-tns-c2-0">
                                            <div class="ng-tns-c2-0 ng-star-inserted">
                                                <div class="pcoded-navigatio-lavel" menu-title-theme="theme5">Меню </div>
                                                <ul class="pcoded-item pcoded-left-item ng-tns-c2-0 ng-star-inserted" item-border="none" item-border-style="solid" subitem-border="solid">
                                                    <li class="ng-tns-c2-0 ng-star-inserted">
                                                        <a class="ng-tns-c2-0 ng-star-inserted" target="_self" href="./dashboard.php">
                                                            <span class="pcoded-micon">
                                                                <i class="ti-home"></i></span>
                                                            <span class="pcoded-mtext">Отчеты</span>
                                                            <span class="pcoded-mcaret"></span>
                                                        </a>
                                                    </li>
                                                </ul>
                                                <ul class="pcoded-item pcoded-left-item ng-tns-c2-0 ng-star-inserted" item-border="none" item-border-style="solid" subitem-border="solid">
                                                    <li class="ng-tns-c2-0 ng-star-inserted ">
                                                        <a class="ng-tns-c2-0 ng-star-inserted" target="_self" href="./product.php"><span class="pcoded-micon"><i class="ti-notepad"></i></span>
                                                            <span class="pcoded-mtext">Товары </span><span class="pcoded-mcaret"></span>
                                                        </a>
                                                    </li>
                                                </ul>
                                                <ul class="pcoded-item pcoded-left-item ng-tns-c2-0 ng-star-inserted" item-border="none" item-border-style="solid" subitem-border="solid">
                                                    <li class="ng-tns-c2-0 ng-star-inserted active">
                                                        <a class="ng-tns-c2-0 ng-star-inserted" target="_self" href="./user.php">
                                                            <span class="pcoded-micon"><i class="ti-user"></i></span>
                                                            <span class="pcoded-mtext">Пользователи</span><span class="pcoded-mcaret"></span>
                                                        </a>
                                                    </li>
                                                </ul>
                                                <ul class="pcoded-item pcoded-left-item ng-tns-c2-0 ng-star-inserted" item-border="none" item-border-style="solid" subitem-border="solid">
                                                    <li class="ng-tns-c2-0 ng-star-inserted">
                                                        <a class="ng-tns-c2-0 ng-star-inserted" target="_self" href="./catalog.php"><span class="pcoded-micon">
                                                                <i class="ti-truck"></i></span>
                                                            <span class="pcoded-mtext">Категории</span><span class="pcoded-mcaret"></span>
                                                        </a>
                                                    </li>
                                                </ul>
                                                <ul class="pcoded-item pcoded-left-item ng-tns-c2-0 ng-star-inserted" item-border="none" item-border-style="solid" subitem-border="solid">
                                                    <li class="ng-tns-c2-0 ng-star-inserted ">
                                                        <a class="ng-tns-c2-0 ng-star-inserted" target="_self" href="./order.php">
                                                            <span class="pcoded-micon">
                                                                <i class="ti-shopping-cart"></i>
                                                            </span>
                                                            <span class="pcoded-mtext">Заказы</span>
                                                            <span class="pcoded-mcaret"></span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </nav>
                        <div class="pcoded-content">
                            <div class="pcoded-inner-content">
                                <div class="main-body">
                                    <div class="page-wrapper">
                                        <div class="page-body">
                                            <div class="row">
                                                <div class="col-sm-12"></div>
                                                <div class="col-md-12">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h5>Все пользователи </h5>
                                                        </div>
                                                        <div class="card-body reset-table p-t-0">
                                                            <form action="" method="get">
                                                                <div class="form-group row"><label class="col-sm-2 col-form-label"></label>
                                                                    <div class="col-sm-4">
                                                                        <input name="filter" class="form-control form-control-round" type="text" placeholder="Поиск Пользователя" value="<?php if (isset($_GET['filter'])) {
                                                                                                                                                                                            echo $_GET['filter'];
                                                                                                                                                                                        } else {
                                                                                                                                                                                            echo "";
                                                                                                                                                                                        } ?>">
                                                                    </div>
                                                                    <div class="col-sm-2">
                                                                        <button class="btn btn-warning btn-round" style="float: right;" type="submit">Поиск</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                            <div class="table-responsive">
                                                                <table class="table table-hover">
                                                                    <thead>
                                                                        <tr class="text-uppercase">
                                                                            <th>ID</th>
                                                                            <th>Логин</th>
                                                                            <th>EMail</th>
                                                                            <th>Телефон</th>
                                                                            <th>Дата/время создания</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
// Подключение необходимых файлов: Сначала подключаются файлы DatabaseConnector.php и _userpartial.php с помощью функции require_once. 
// Это, вероятно, файлы, содержащие функции для работы с базой данных и отображения пользовательской информации соответственно.
                                                                        require_once '../src/System/DatabaseConnector.php';
                                                                        require_once 'Service/_userpartial.php';
                                                                        if (isset($_GET["filter"]))
// Получение списка пользователей: Далее код проверяет, был ли установлен GET-параметр "filter". Если да, то вызывается функция GetAll($_GET["filter"]), предположительно для получения списка пользователей, отфильтрованных по заданному критерию. 
// В противном случае вызывается та же функция без аргументов, чтобы получить список всех пользователей.
                                                                            $all_user = GetAll($_GET["filter"]);
                                                                        else $all_user = GetAll('');
                                                                        $num = $all_user->rowCount();
                                                                        if ($num > 0) {
// Отображение пользователей: Полученный список пользователей перебирается в цикле while. Для каждого пользователя выводятся данные в виде строки таблицы HTML. 
// Каждая строка содержит ID пользователя, имя пользователя, адрес электронной почты, номер телефона и дату создания пользователя.
                                                                            while ($row = $all_user->fetch(PDO::FETCH_ASSOC)) {
                                                                                echo '<tr class="ng-star-inserted">';
                                                                                echo '    <td>' . $row['ID'] . '</td>';
                                                                                echo '    <td>' . $row['Username'] . '</td>';
                                                                                echo '    <td>' . $row['EMail'] . '</td>';
                                                                                echo '    <td>' . $row['Phone'] . '</td>';
                                                                                echo '    <td>' . $row['CreationDate'] . '</td>';
                                                                                echo '</tr>';
                                                                            }
                                                                        } else {
// Обработка отсутствия пользователей: Если список пользователей пуст (количество строк меньше или равно 0), выводится сообщение "No User" в виде строки таблицы HTML.
                                                                            echo '<tr class="ng-star-inserted">';
                                                                            echo '<td colspan="5">No User</td>';
                                                                            echo '</tr>';
                                                                        }

                                                                        ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

    </html>
<?php
} else {
    header("location: index.php");
}
?>
