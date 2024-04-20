// Страница с редактированием категорий товаров
    <?php
// Начало сессии: Сначала код запускает сессию с помощью session_start(). Сессии используются для отслеживания состояния пользователя между разными запросами к серверу.
// Проверка авторизации: После того как сессия начата, код проверяет, авторизован ли пользователь. Если в сессии установлен ключ 'loggedin' и его значение равно true, то пользователь считается авторизованным, и переменная $adminloggedin устанавливается в true. 
// В этом случае также извлекается идентификатор пользователя ($userId), сохраненный в сессии.
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
    // Отображение HTML-кода в зависимости от авторизации: Если пользователь авторизован (т.е. $adminloggedin == true), то отображается HTML-разметка для страницы создания/обновления категории товаров. 
    // В противном случае пользователь перенаправляется на страницу входа (index.php).
    // Отображение формы создания/обновления категории: Внутри HTML-разметки страницы отображается форма, которая позволяет пользователю создавать новую категорию товаров или обновлять существующую. Форма содержит поле для ввода названия категории. 
    // Если параметр id передан в URL и больше нуля, то происходит попытка получить данные категории из базы данных для предварительного заполнения формы. В противном случае форма отображается пустой.
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
                                                        <li class="ng-tns-c2-0 ng-star-inserted active">
                                                            <a class="ng-tns-c2-0 ng-star-inserted" target="_self" href="./catalog.php"><span class="pcoded-micon">
                                                                    <i class="ti-truck"></i></span>
                                                                <span class="pcoded-mtext">Catalogs</span><span class="pcoded-mcaret"></span>
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
                                                    <div class="col-md-12">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <h5><!---->
                                                                    <span class="ng-star-inserted">Создать/обновить категорию</span>
                                                                </h5>
                                                            </div>
                                                            <?php
                                                            // Обработка отправки формы: После заполнения и отправки формы данные передаются обработчику (Service/_catalogpartial.php) методом POST. 
                                                            // Обработчик выполняет сохранение или обновление категории в базе данных в зависимости от данных, полученных из формы.
                                                            require_once 'Service/_catalogpartial.php';
                                                            if (isset($_GET["id"]) && $_GET["id"] > 0) {
                                                                $catalog = Get($_GET["id"]);;
                                                            } else {
                                                                $catalog = null;
                                                            }
                                                            ?>
                                                            <form action="./Service/_catalogpartial.php" method="post" enctype="multipart/form-data">
    
                                                                <div class="card-body reset-table p-t-0">
                                                                    <h4 class="sub-title">Details</h4>
                                                                    <input type="hidden" name="ID" value="<?= $_GET["id"] ?>"></input>
                                                                    <div class="form-group row">                                                                    
                                                                        <div class="col-sm-12"><label>Название категории</label><input name="Name" value="<?= $catalog == null ? "" : $catalog['Name'] ?>" class="form-control ng-untouched ng-pristine ng-valid" type="text" placeholder="Name"></div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <div class="col-sm-12">
                                                                            <button class="btn btn-success btn-round col-12 btn-lg" name="save_update" type="submit">Сохранить</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
    
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
    // Переадресация в случае неавторизованного доступа: Если пользователь не авторизован, его перенаправляют на страницу входа (index.php).
    } else {
        header("location: index.php");
    }
    ?>
