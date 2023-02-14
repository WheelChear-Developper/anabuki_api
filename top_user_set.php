<?php
require('./kanri/login_pass.php');
require('./kanri/app_setting.php');

// セッションの有効期限を5分に設定
session_set_cookie_params(60 * 5);

// セッション管理開始
session_start();

//ログインチェック
$login_master_code = '';
if (!isset($_SESSION['login_master_code'])) {
    header('Location:login/login.php');
} else {
    $login_master_code = $_SESSION['login_master_code'];
}

//EXIT
if (isset($_GET['EXIT'])) {
    unset($_SESSION['login_master_code']);
    header('Location:login/login.php');
}

$errormsg = '';

$DEPERTMENT_CODE = '';
$STUDENT_CODE = '';
$NAME = '';
$MAIL = '';

if (isset($_GET['DEPERTMENT_CODE'])) {
    $DEPERTMENT_CODE = $_GET['DEPERTMENT_CODE'];
    if (isset($_GET['STUDENT_CODE'])) {
        $STUDENT_CODE = $_GET['STUDENT_CODE'];
        if (isset($_GET['NAME'])) {
            $NAME = $_GET['NAME'];
            if (isset($_GET['MAIL'])) {
                $MAIL = $_GET['MAIL'];
                try {

                    //文字数チェク
                    if (mb_strlen($MAIL) == 0) {
                        $errormsg = "メールアドレスを入力にしてください。";
                    } else if (mb_strlen($MAIL) > 100) {
                        $errormsg = "メールアドレスを指定の文字数で入力にしてください。";
                    }
                    if (mb_strlen($NAME) == 0) {
                        $errormsg = "名前を入力にしてください。";
                    } else if (mb_strlen($NAME) > 50) {
                        $errormsg = "名前を指定の文字数で入力にしてください。";
                    }
                    if (mb_strlen($STUDENT_CODE) == 0) {
                        $errormsg = "学籍番号を入力にしてください。";
                    } else if (mb_strlen($STUDENT_CODE) > 12) {
                        $errormsg = "学籍番号を指定の文字数で入力にしてください。";
                    }
                    if (mb_strlen($DEPERTMENT_CODE) == 0) {
                        $errormsg = "学科コードを入力にしてください。";
                    } else if (mb_strlen($DEPERTMENT_CODE) > 10) {
                        $errormsg = "学科コードを指定の文字数で入力にしてください。";
                    }

                    if (mb_strlen($errormsg) == 0) {

                        $DEPERTMENT_CODE = htmlspecialchars($DEPERTMENT_CODE, ENT_QUOTES, 'UTF-8');
                        $STUDENT_CODE = htmlspecialchars($STUDENT_CODE, ENT_QUOTES, 'UTF-8');
                        $NAME = htmlspecialchars($NAME, ENT_QUOTES, 'UTF-8');
                        $MAIL = htmlspecialchars($MAIL, ENT_QUOTES, 'UTF-8');

                        //MYSQL接続設定
                        $dsn = 'mysql:dbname=' . $db_name . ';host=' . $db_host . ';charset=utf8;';
                        $user = $db_user;
                        $password = $db_pass;
                        $dbh = new PDO($dsn, $user, $password);
                        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        //SQL実行
                        $sql = 'INSERT INTO STUDENT_MASTER (DEPERTMENT_CODE,STUDENT_CODE,NAME,MAIL,MASTER_CODE,UPLOAD_DATE) VALUES (?,?,?,?,?,?)';
                        $stmt = $dbh->prepare($sql);
                        $data[] = $DEPERTMENT_CODE;
                        $data[] = $STUDENT_CODE;
                        $data[] = $NAME;
                        $data[] = $MAIL;
                        $data[] = $login_master_code;
                        $data[] = date('Y-m-d H:i:s');

                        $stmt->execute($data);
                        $dbh = null;

                        header('Location: top_user.php');
                    }
                } catch (Exception $e) {
                    $errormsg = 'ただいま障害により大変ご迷惑をお掛けしております。';
                }
            }
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
    <link rel="stylesheet" href="style.css">
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
        <div class="col-12 col-md-5 col-lg-8 d-flex align-items-center justify-content-md-end mt-3 mt-md-0">
            <div class="dropdown">
                <a class="dropdown-item" href="top_user.php?EXIT=1">Sign out</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <!-- sidebar content goes in here -->
                <div class="position-sticky pt-md-5">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="top_user.php">
                                <i class="fa-solid fa-children"></i>
                                <span class="ml-2">生徒マスター</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="top_point.php">
                                <i class="fa-solid fa-boxes-stacked"></i>
                                <span class="ml-2">ポイント配布場所マスター</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="top_pointcheck.php">
                                <i class="fa-solid fa-boxes-stacked"></i>
                                <span class="ml-2">生徒別ポイント集計チェック</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="top_hit.php">
                                <i class="fa-solid fa-boxes-stacked"></i>
                                <span class="ml-2">抽選結果</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4 py-4">
                <h1 class="h2">生徒マスター</h1>
                <p>生徒の追加</p>

                <p>
                    <font color="#ff0000"><?php echo $errormsg; ?></font>
                </p>

                <div class="row">
                    <div class="col-12 col-xl-12 mb-4 mb-lg-0">
                        <div class="card">
                            <div class="card-body">

                                <form action="top_user_set.php">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="inputGroup-sizing-default">学科コード</span>
                                        <input name="DEPERTMENT_CODE" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" style="ime-mode:active" placeholder="GK1234567890（12文字まで）" value=<?= $DEPERTMENT_CODE ?>>
                                    </div>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="inputGroup-sizing-default">学籍番号</span>
                                        <input name="STUDENT_CODE" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" style="ime-mode: active" placeholder="GS1234567890（12文字まで）" value=<?= $STUDENT_CODE ?>>
                                    </div>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="inputGroup-sizing-default">名前</span>
                                        <input name="NAME" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" style="ime-mode: active" placeholder="あいうえお（50文字まで）" value=<?= $NAME ?>>
                                    </div>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="inputGroup-sizing-default">メールアドレス</span>
                                        <input name="MAIL" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" style="ime-mode: active" placeholder="abc@dmain.com（100文字まで）" value=<?= $MAIL ?>>
                                    </div>
                                    <input type="button" class="btn btn-lg btn-secondary" onclick="history.back()" value="戻る">
                                    <input type="submit" class="btn btn-lg btn-primary" value="追加" />
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js" integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/" crossorigin="anonymous"></script>

</html>