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
        <link rel="stylesheet" href="../css/slick.css">
        <link rel="stylesheet" href="../css/slick-theme.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="../scripts/site.js?v=1.0.0"></script>
        <script src="../scripts/product.js"></script>
        <script src="../scripts/slick.min.js"></script>
        <style>
            .slick-arrow {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                font-size: 0;
                height: 40px;
                width: 40px;
                background-color: rgba(0, 0, 0, 0.6);
                border-radius: 10px;
                cursor: pointer;
                background-size: 20px;
                z-index: 1;
                background-repeat: no-repeat;
                background-position: center;
            }

            .slider-item img {
                margin: 0 auto;
                text-align: center;
            }
        </style>
        <script>
            function loadslick() {
                $('.product-slider').on("init", function(event, slick) {
                    document.querySelector('#chkpictureid').value = slick.$slides.get(0).attributes['tag-id'].value;
                    if (slick.$slides.get(0).attributes['tag'].value === "1") {
                        document.querySelector('#chkpicture').checked = true;
                        document.querySelector('#btnPictureDefault').style.visibility = 'hidden';
                    } else {
                        document.querySelector('#chkpicture').checked = false;
                        document.querySelector('#btnPictureDefault').style.visibility = 'visible';
                    }
                });
                $('.product-slider').slick({
                    dots: true,
                    infinite: true,
                    speed: 500,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    autoplay: false,
                    autoplaySpeed: 2000,
                    centerMode: true,
                    arrows: true,
                });
                $('.product-slider').on("afterChange", function(event, slick, currentSlide) {
                    document.querySelector('#chkpictureid').value = slick.$slides.get(currentSlide).attributes['tag-id'].value;
                    if (slick.$slides.get(currentSlide).attributes['tag'].value === "1") {
                        document.querySelector('#chkpicture').checked = true;
                        document.querySelector('#btnPictureDefault').style.visibility = 'hidden';
                    } else {
                        document.querySelector('#chkpicture').checked = false;
                        document.querySelector('#btnPictureDefault').style.visibility = 'visible';
                    }
                });
            }
            $(document).ready(function() {

                // $('.slider-for').slick({
                //     slidesToShow: 1,
                //     slidesToScroll: 1,
                //     arrows: false,
                //     fade: true,
                //     asNavFor: '.slider-nav'
                // });
                // $('.slider-nav').slick({
                //     slidesToShow: 3,
                //     slidesToScroll: 1,
                //     asNavFor: '.slider-for',
                //     dots: true,
                //     centerMode: true,
                //     focusOnSelect: true
                // });
            });
        </script>
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
                                            <div class="ng-tns-c2-0 ">
                                                <div class="pcoded-navigatio-lavel" menu-title-theme="theme5">Меню </div>
                                                <ul class="pcoded-item pcoded-left-item ng-tns-c2-0 " item-border="none" item-border-style="solid" subitem-border="solid">
                                                    <li class="ng-tns-c2-0 ">
                                                        <a class="ng-tns-c2-0 " target="_self" href="./dashboard.php">
                                                            <span class="pcoded-micon">
                                                                <i class="ti-home"></i></span>
                                                            <span class="pcoded-mtext">Отчеты</span>
                                                            <span class="pcoded-mcaret"></span>
                                                        </a>
                                                    </li>
                                                </ul>
                                                <ul class="pcoded-item pcoded-left-item ng-tns-c2-0 " item-border="none" item-border-style="solid" subitem-border="solid">
                                                    <li class="ng-tns-c2-0  active">
                                                        <a class="ng-tns-c2-0 " target="_self" href="./product.php"><span class="pcoded-micon"><i class="ti-notepad"></i></span>
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
                                                <ul class="pcoded-item pcoded-left-item ng-tns-c2-0 " item-border="none" item-border-style="solid" subitem-border="solid">
                                                    <li class="ng-tns-c2-0 ">
                                                        <a class="ng-tns-c2-0 " target="_self" href="./catalog.php"><span class="pcoded-micon">
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
                                                <div class="col-md-12">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h5><!---->
                                                                <span class="">Создать/редактировать товары</span>
                                                            </h5>
                                                        </div>
                                                        <?php
                                                        require_once 'Service/_productpartial.php';
                                                        if (isset($_GET["id"]) && $_GET["id"] > 0) {
                                                            $catalog = GetCatalog();
                                                            $product = GetProduct($_GET["id"]);
                                                            $image = GetImages($_GET["id"]);
                                                            $prices = GetPrices($_GET["id"]);
                                                            $orders = GetOrders($_GET["id"]);
                                                        } else {
                                                            $catalog = GetCatalog();
                                                            $product = null;
                                                            $image = null;
                                                            $prices = null;
                                                            $orders = null;
                                                        }
                                                        ?>
                                                        <form action="./Service/_productpartial.php" method="post" enctype="multipart/form-data">

                                                            <div class="card-body reset-table p-t-0">
                                                                <h4 class="sub-title">Детали</h4>
                                                                <input type="hidden" name="ID" value="<?= $_GET["id"] ?>"></input>
                                                                <div class="form-group row">
                                                                    <div class="col-sm-12"><label>Категория</label>
                                                                        <select class="form-control ng-untouched ng-pristine ng-valid" name="catalog">
                                                                            <?php
                                                                            if ($catalog !=null)
                                                                                foreach ($catalog as $row) {
                                                                                    if ($product == null || $product["CatalogID"] != $row['ID'])
                                                                                        echo '<option value="' . $row['ID'] . '" class="">' . $row['Name'] . '</option>';
                                                                                    else echo '<option selected="selected" value="' . $row['ID'] . '" class="">' . $row['Name'] . '</option>';
                                                                                }
                                                                            ?>
                                                                            <!-- <option value="202318965" class="">Vadodara</option>
                                                                            <option value="476734171" class="">Bhavnagar</option>
                                                                            <option value="520202930" class="">Ahmedabad</option> -->
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-sm-12"><label>Название</label><input name="Name" value="<?= $product == null ? "" : $product['Name'] ?>" class="form-control ng-untouched ng-pristine ng-valid" type="text" placeholder="Введите название"></div>
                                                                    <div class="col-sm-12"><label>Описание</label><textarea name="Description" class="form-control ng-untouched ng-pristine ng-valid" cols="5" rows="5" placeholder="Введите описание"><?= $product == null ? "" : $product['Description'] ?></textarea></div>
                                                                    <div class="col-sm-12"><label>Состав</label><textarea name="Ingredients" class="form-control ng-untouched ng-pristine ng-valid" cols="5" rows="5" placeholder="Введите состав"><?= $product == null ? "" : $product['Ingredients'] ?></textarea></div>
                                                                    <div class="col-sm-12"><label>КБЖУ</label><textarea name="NutritionalValue" class="form-control ng-untouched ng-pristine ng-valid" cols="5" rows="5" placeholder="Введите КБЖУ"><?= $product == null ? "" : $product['NutritionalValue'] ?></textarea></div>
                                                                    <div class="col-sm-12"><label>Статус</label>
                                                                        <!-- <form action="Service/_productpartial.php" method="post"> -->
                                                                        <?php
                                                                        if ($product != null) {
                                                                            if ($product['Status'] == true) {
                                                                        ?>
                                                                                <button class="btn btn-danger btn-square btn-mini" name="product_passive" type="submit">Скрыть</button>
                                                                            <?php
                                                                            } else {
                                                                            ?>
                                                                                <button class="btn btn-success btn-square btn-mini" name="product_active" type="submit">Активировать</button>
                                                                        <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                        <!-- </form> -->
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row">
                                                                    <div class=" col-sm-8">
                                                                        <div class="form-group row">
                                                                            <label class="col-sm-12 col-form-label">Загрузить фотографию</label>
                                                                            <div class="col-sm-6">
                                                                                <input name="file" id="file" accept=".png" class="form-control" type="file">
                                                                            </div>
                                                                            <button id="btnPictureUpload" class="btn btn-warning btn-square btn-mini" name="picture_upload" type="submit">Загрузить файл</button>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <div class="product-slider text-center col-12">
                                                                                <?php
                                                                                if ($image != null) {
                                                                                    foreach ($image as $row) {
                                                                                        echo '<div class="slider-item" tag-id="' . $row["ID"] . '" tag="' . $row["First"] . '"><img height="200" alt="" src="data:image/png;base64,' . $row['ImageData'] . '"></div>';
                                                                                    }
                                                                                    // echo '<script> loadslick();</script>';
                                                                                }
                                                                                ?>
                                                                                <!-- <div class='slider-item' tag-id="1" tag="false"><img alt="" src="../images/user.png"></div> -->
                                                                                <!-- <div class='slider-item' tag-id="2" tag="true"><img alt="" src="../images/user.png"></div> -->
                                                                                <!-- <div class='slider-item' tag-id="3" tag="false"><img alt="" src="../images/user.png"></div> -->
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class=" col-sm-4">
                                                                        <div class="card">
                                                                            <div class="card-body-big card-status">
                                                                                <h5>Доход по товару</h5>
                                                                                <div class="card-body text-center">
                                                                                    <h2 class="text-primary">ЗАКАЗЫ: <?= $orders == null ? 0 : $orders['Count'] ?></h2>
                                                                                </div>
                                                                                <div class="row">
                                                                                    <div class="col-6">
                                                                                        <p class="f-16 text-muted m-0">Итог: <?= $orders == null ? 0 : $orders['Sum'] ?></p>
                                                                                    </div>
                                                                                    <div class="col-6"></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <div class="col-sm-4"><label>Главная фотография</label>
                                                                        <input disabled="disabled" id="chkpicture" name="chkpicture" class="form-control ng-untouched ng-pristine ng-valid" type="checkbox">
                                                                        <input id="chkpictureid" name="pictureid" type="hidden">
                                                                        <button id="btnPictureDefault" class="btn btn-success btn-square btn-mini" name="picture_default" type="submit">Сделать главной</button>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <div class="col-sm-12">
                                                                        <button class="btn btn-success btn-round col-12" name="save_update" type="submit">Сохранить</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="card-body reset-table p-t-0">
                                                                <h4 class="sub-title">ЦЕНЫ</h4>
                                                                <div class="form-group row">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-hover">
                                                                            <thead>
                                                                                <tr class="text-uppercase">
                                                                                    <th>Размер</th>
                                                                                    <th>Описание</th>
                                                                                    <th>Цена</th>
                                                                                    <th>Статус</th>
                                                                                    <th>Действие</th>
                                                                                    <th>
                                                                                        <a class="btn btn-success btn-square btn-mini" href="./productprice.php?productid=<?= $_GET["id"] ?>&id=0">Добавить размер</a>
                                                                                    </th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php
                                                                                if ($prices != null) {
                                                                                    foreach ($prices as $row) {
                                                                                        echo '<tr class="">';
                                                                                        echo '<td><div style="width: 100px;white-space: pre-wrap;">' . $row['Name'] . '</div></td>';
                                                                                        echo '<td><div style="width: 100px;white-space: pre-wrap;">' . $row['Description'] . '</div></td>';
                                                                                        echo '<td>' . $row['Price'] . '</td>';
                                                                                        if ($row['Status'] == true)
                                                                                            echo '<td><button type="button" class="btn btn-success btn-square btn-mini">Активный</button></td>';
                                                                                        else
                                                                                            echo '<td><button type="button" class="btn btn-danger btn-round">Скрытый</button></td>';
                                                                                        echo '<td><a class="btn btn-success btn-square btn-mini btn-outline-success" href="productprice.php?productid=' . $_GET["id"] . '&id=' . $row["ID"] . '">Редактировать</a></td>';

                                                                                        echo '</tr>';
                                                                                    }
                                                                                }
                                                                                ?>
                                                                            </tbody>
                                                                        </table>
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
} else {
    header("location: index.php");
}
?>
<script>
    $(document).ready(function() {
        loadslick();
    });
</script>