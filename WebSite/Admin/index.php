<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html,
        body {
            height: 100%;
        }

        .form-signin {
            max-width: 330px;
            padding: 1rem;
        }

        .form-signin .form-floating:focus-within {
            z-index: 2;
        }

        .form-signin input[type="email"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }

        .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
    </style>

</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary">
    <main class="form-signin w-100 m-auto">
        <form action="Service/_loginpartial.php" method="post">
            <img class="mb-4" src="../images/login.png" alt="" width="250" height="57">
            <h1 class="h3 mb-3 fw-normal text-center">Admin Panel</h1>

            <div class="form-floating">
                <input id="username" name="username" type="text" class="form-control" id="floatingInput" placeholder="Логин">
                <label for="floatingInput">Логин</label>
            </div>
            <div class="form-floating">
                <input  id="password" name="password"  type="password" class="form-control" id="floatingPassword" placeholder="Пароль">
                <label for="floatingPassword">Пароль</label>
            </div>
            <div id="diverror" class="<?php if(isset($_GET['loginsuccess']) && $_GET['loginsuccess']=="false") echo ''; else echo 'd-none'; ?>
            alert alert-danger" role="alert">
                Логин или Пароль неверные!
            </div>
            <button class="btn btn-primary w-100 py-2" type="submit">Войти</button>
        </form>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
