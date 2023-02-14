<?php
require('../kanri/login_pass.php');
require('../kanri/app_setting.php');

// セッションの有効期限を5分に設定
session_set_cookie_params(60 * 5);

// セッション管理開始
session_start();

$errormsg = '';

if (isset($_POST['name'])) {
    $name = $_POST['name'];
    if (isset($_POST['pass'])) {
        $pass = $_POST['pass'];
        try {

            $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
            $pass = htmlspecialchars($pass, ENT_QUOTES, 'UTF-8');

            //MYSQL接続設定
            $dsn = 'mysql:dbname=' . $db_name . ';host=' . $db_host . ';charset=utf8;';
            $user = $db_user;
            $password = $db_pass;
            $dbh = new PDO($dsn, $user, $password);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //SQL実行
            $sql = 'SELECT MASTER_CODE,NAME,PASSWORD FROM MASTER_USER WHERE NAME=? AND PASSWORD=?';
            $stmt = $dbh->prepare($sql);
            $data[] = $name;
            $data[] = $pass;

            $stmt->execute($data);

            $dbh = null;

            $rec = $stmt->fetch(PDO::FETCH_ASSOC);

            if (
                $rec == false
            ) {
                $errormsg = '名前かパスワードが間違っています。';
            } else {
                $_SESSION['login_master_code'] = $rec['MASTER_CODE'];
                header('Location:../top_user.php');
            }
        } catch (Exception $e) {
            $errormsg = 'ただいま障害により大変ご迷惑をお掛けしております。';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $app_name; ?></title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
    <link rel="stylesheet" href="login.css">
    <script src="https://kit.fontawesome.com/c11911e687.js" crossorigin="anonymous"></script>
</head>

<body>
    <nav class="navbar navbar-light bg-light p-3">
        <div class="d-flex col-12 col-md-3 col-lg-2 mb-2 mb-lg-0 flex-wrap flex-md-nowrap justify-content-between">
            <a class="navbar-brand" href="#">
                <?php echo $app_name; ?>
            </a>
            <button class="navbar-toggler d-md-none collapsed mb-3" type="button" data-toggle="collapse" data-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <form method="post" action="login.php">
        <h1>ログイン</h1>
        <p>
            <font color="#ff0000"><?php echo $errormsg; ?></font>
        </p>
        <input placeholder="名前" type="text" name="name" />
        <input placeholder="パスワード" type="password" name="pass" />
        <input type="submit" class="btn" value="ログイン" />
    </form>
</body>

</html>