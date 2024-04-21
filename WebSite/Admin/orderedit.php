// Страница, отвечающая за детали заказа и за взаимодействия с ним
<?php
// Начало сессии: session_start() запускает сессию, чтобы можно было сохранять и использовать данные сессии между различными запросами.    
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    // Проверка авторизации: Проверяет, авторизован ли пользователь. 
    // Если в сессии установлен ключ 'loggedin' и его значение равно true, то переменная $adminloggedin устанавливается в true, а $userId присваивается значение из сессии. 
    $adminloggedin = true;
    $userId = $_SESSION['userId'];
} else {
    //  // В противном случае $adminloggedin устанавливается в false, а $userId устанавливается в 0.
    $adminloggedin = false;
    $userId = 0;
}

if ($adminloggedin) {
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Product Page</title>
        <link rel="stylesheet" href="../css/styles.css">
        <link rel="stylesheet" href="../css/site.css">
        <link rel="stylesheet" href="../css/slick.css">
        <link rel="stylesheet" href="../css/slick-theme.css">
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
                                                    <li class="ng-tns-c2-0 ng-star-inserted">
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
                                                    <li class="ng-tns-c2-0 ng-star-inserted active">
                                                        <a class="ng-tns-c2-0 ng-star-inserted" target="_self" href="./order.php">
                                                            <span class="pcoded-micon">
                                                                <i class="ti-shopping-cart"></i>
                                                            </span>
                                                            <span class="pcoded-mtext">Заказы </span>
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
                                                <div class="col-md-12">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h5><!---->
                                                                <span class="ng-star-inserted">Заказ</span>
                                                            </h5>
                                                        </div>
                                                        <?php
    // Формирование HTML-разметки страницы продукта: Здесь определены стили, скрипты и содержимое страницы, такие как меню, форма для отображения и управления заказом. 
    // Для отображения деталей заказа используется форма, которая отправляет данные на тот же файл (_orderpartial.php) для обработки.
                                                        require_once 'Service/_orderpartial.php';
                                                        if (isset($_GET["id"]) && $_GET["id"] > 0) {
                                                            $result = Get($_GET["id"]);
                                                            $order = $result['order'];
                                                            $orderdetail = $result['orderdetail'];
                                                        } else {
                                                            $order = null;
                                                            $orderdetail = null;
                                                        }
                                                        ?>
                                                        <form action="./Service/_orderpartial.php" method="post" enctype="multipart/form-data">
                                                            <div class="card-body reset-table p-t-0">
                                                                <h4 class="sub-title">Детали</h4>
                                                                <input type="hidden" name="ID" value="<?= $_GET["id"] ?>"></input>
                                                                <div class="form-group row">
                                                                    <div class="col-sm-12"><label>Адрес</label><textarea readonly="readonly" class="form-control ng-untouched ng-pristine ng-valid" cols="5" rows="5"><?= $order == null ? "" : $order['Address'] ?></textarea></div>
                                                                    <div class="col-sm-12"><label>Курьер</label><textarea name="Driver" class="form-control ng-untouched ng-pristine ng-valid" cols="5" rows="5"><?= $order == null ? "" : $order['Driver'] ?></textarea></div>
                                                                    <div class="col-sm-12"><label>Итог</label><label class="col-12"><strong><?= $order == null ? "" : number_format($order['Price'], 2, ',', ' ') ?> ₽</strong></label></div>
                                                                    <div class="col-sm-12"><label>Статус</label>
                                                                        <?php
                                                                        if ($order != null) {
                                                                            if ($order['Status'] == 0) {
                                                                        ?>
                                                                                <button disabled="" type="button" style="background-color:#a594f9" class="btn btn-info btn-round">В обработке</button>
                                                                            <?php
                                                                            } else if ($order['Status'] == 1) {
                                                                            ?>
                                                                                <button disabled="" type="button" style="background-color:#ffd97d" class="btn btn-warning btn-round">Готовится</button>
                                                                            <?php
                                                                            } else if ($order['Status'] == 2) {
                                                                            ?>
                                                                                <button disabled="" type="button" style="background-color:#caffbf;color:#000000;" class="btn btn-danger btn-round">В пути</button>
                                                                            <?php
                                                                            } else if ($order['Status'] == 3) {
                                                                            ?>
                                                                                <button disabled="" type="button" style="background-color:#60d394" class="btn btn-success btn-round">Доставлен</button>
                                                                            <?php
                                                                            } else if ($order['Status'] == 4) {
                                                                            ?>
                                                                                <button disabled="" type="button" style="background-color:#ee6055" class="btn btn-success btn-round">Отменён</button>
                                                                        <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <div class="col-sm-12">
                                                                        <?php
    // Управление статусом заказа: В зависимости от текущего статуса заказа отображаются соответствующие кнопки для изменения его состояния. 
    // Например, для заказов в статусе "В обработке" выводятся кнопки "Принять заказ" и "Отменить заказ".
    // Отображение деталей заказанных товаров: Для каждой позиции в заказе выводятся её изображение (если есть), категория, наименование, количество, цена и итоговая сумма. 
    // Если заказ не содержит позиций, выводится соответствующее сообщение.
                                                                        if ($order != null) {
                                                                            if ($order['Status'] == 0) {
                                                                                echo '<button class="btn btn-success col-12 btn-round ng-star-inserted" name="accept" type="submit">Принять заказ</button>';
                                                                                echo '<button class="btn btn-danger col-12 btn-round ng-star-inserted mt-1" name="reject" type="submit">Отменить заказ</button>';
                                                                            } else if ($order['Status'] == 1)
                                                                                echo '<button class="btn btn-success col-12 btn-round ng-star-inserted" name="driver" type="submit">Отправить курьера</button>';
                                                                            else if ($order['Status'] == 2)
                                                                                echo '<button class="btn btn-success col-12 btn-round ng-star-inserted" style="background-color:#60d394" name="delivered" type="submit">Доставлен</button>';
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="card-body reset-table p-t-0">
                                                            <h4 class="sub-title">Детали</h4>
                                                            <div class="table-responsive">
                                                                <table class="table table-hover">
                                                                    <thead>
                                                                        <tr class="text-uppercase">
                                                                            <th>Картинка</th>
                                                                            <th>Категория</th>
                                                                            <th>Товар</th>
                                                                            <th>Кол-во позиций</th>
                                                                            <th>Цена</th>
                                                                            <th>Итог</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php

                                                                        if ($orderdetail != null) {
                                                                            foreach ($orderdetail as $row) {
                                                                                echo '<tr class="ng-star-inserted">';
                                                                                if ($row['ImageData'] != null)
                                                                                    echo '<td><a href="javascript:;"><img class="img-responsive" style="height: 40px;border-radius: 50%;" src="data:image/png;base64,' . $row['ImageData'] . '"></a></td>';
                                                                                else
                                                                                    echo '<td><a href="javascript:;"><img class="img-responsive" style="height: 40px;border-radius: 50%;" src="../images/nophoto.png"></a></td>';
                                                                                echo '<td><label>' . $row['Catalog'] . '</label></td>';
                                                                                echo '<td><div style="width: 100px;white-space: pre-wrap;"><a href="./productedit.php?id=' . $row['ProductID'] . '" >' . $row['Name'] . '</a></div></td>';
                                                                                echo '<td>' . $row['Quantity'] . '</td>';
                                                                                echo '<td>' . $row['Price'] . '</td>';
                                                                                echo '<td>' . $row['Total'] . '</td>';
                                                                                echo '</tr>';
                                                                            }
                                                                        } else {
                                                                            echo '<tr class="ng-star-inserted">';
                                                                            echo '<td colspan="7">No Waiting Order</td>';
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
                                            <ng2-toasty>
                                                <div id="toasty" class="toasty-position-bottom-right"></div>
                                            </ng2-toasty>
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
    // Переадресация в случае неавторизованного доступа: Если пользователь не авторизован, его перенаправляют на страницу входа (index.php).
    header("location: index.php");
}
?>
