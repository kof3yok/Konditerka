<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    $adminloggedin = true;
    $userId = $_SESSION['userId'];
} else {
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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="../scripts/site.js?v=1.0.0"></script>
        <script src="../scripts/product.js"></script>
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
                                                    <li class="ng-tns-c2-0 ng-star-inserted active">
                                                        <a class="ng-tns-c2-0 ng-star-inserted" target="_self" href="./catalog.php"><span class="pcoded-micon">
                                                                <i class="ti-truck"></i></span>
                                                            <span class="pcoded-mtext">Категории</span><span class="pcoded-mcaret"></span>
                                                        </a>
                                                    </li>
                                                </ul>
                                                <ul class="pcoded-item pcoded-left-item ng-tns-c2-0 ng-star-inserted" item-border="none" item-border-style="solid" subitem-border="solid">
                                                    <li class="ng-tns-c2-0 ng-star-inserted">
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
                                                <div class="col-sm-12"></div>
                                                <div class="col-md-12">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h5>Все категории </h5>
                                                        </div>
                                                        <div class="card-body reset-table p-t-0">
                                                            <div class="form-group row">
                                                                <div class="col-sm-12">
                                                                    <a class="btn btn-danger btn-round" href="./catalogedit.php?id=0">Создать новую</a>
                                                                </div>
                                                            </div>
                                                            <div class="table-responsive">
                                                                <table class="table table-hover">
                                                                    <thead>
                                                                        <tr class="text-uppercase">
                                                                            <th>Название категории</th>
                                                                            <th>Действие</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php

                                                                        require_once '../src/System/DatabaseConnector.php';
                                                                        require_once 'Service/_catalogpartial.php';
                                                                        $all_catalog = GetAll('');

                                                                        $num = $all_catalog->rowCount();
                                                                        if ($num > 0) {
                                                                            while ($row = $all_catalog->fetch(PDO::FETCH_ASSOC)) {
                                                                                echo '<tr class="ng-star-inserted">';
                                                                                echo '<td><div style="width: 100px;white-space: pre-wrap;">' . $row['Name'] . '</div></td>';
                                                                                echo '<td><a class="btn btn-success btn-round btn-outline-success" href="./catalogedit.php?id=' . $row["ID"] . '">Редактировать</a></td>';

                                                                                echo '</tr>';
                                                                            }
                                                                        } else {
                                                                            echo '<tr class="ng-star-inserted">';
                                                                            echo '<td colspan="8">No Catalog</td>';
                                                                            echo '</tr>';
                                                                        }
                                                                        // }
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