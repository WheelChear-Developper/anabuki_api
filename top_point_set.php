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
$POINTFIELD_NO = '';
$POINTFIELD_NAME = '';
$POINTFIELD_QRCODE = '';

if (isset($_GET['POINTFIELD_NO'])) {
    $POINTFIELD_NO = $_GET['POINTFIELD_NO'];
    if (isset($_GET['POINTFIELD_NAME'])) {
        $POINTFIELD_NAME = $_GET['POINTFIELD_NAME'];
        if (isset($_GET['POINTFIELD_QRCODE'])) {
            $POINTFIELD_QRCODE = $_GET['POINTFIELD_QRCODE'];

            //文字数チェク
            if (mb_strlen($POINTFIELD_QRCODE) == 0) {
                $errormsg = "ポイントのQRコードを入力にしてください。";
            } else if (mb_strlen($POINTFIELD_QRCODE) > 30) {
                $errormsg = "ポイントのQRコードを指定の文字数で入力にしてください。";
            }
            if (mb_strlen($POINTFIELD_NAME) == 0) {
                $errormsg = "ポイント配布場所の名前を入力にしてください。";
            } else if (mb_strlen($POINTFIELD_NAME) > 100) {
                $errormsg = "ポイント配布場所の名前を指定の文字数で入力にしてください。";
            }
            if (intval($POINTFIELD_NO) == 0) {
                $errormsg = "ポイント配布場所番号を入力にしてください。";
            }

            if (mb_strlen($errormsg) == 0) {
                try {
                    $POINTFIELD_NO = htmlspecialchars($POINTFIELD_NO, ENT_QUOTES, 'UTF-8');
                    $POINTFIELD_NAME = htmlspecialchars($POINTFIELD_NAME, ENT_QUOTES, 'UTF-8');
                    $POINTFIELD_QRCODE = htmlspecialchars($POINTFIELD_QRCODE, ENT_QUOTES, 'UTF-8');

                    $dsn = 'mysql:dbname=' . $db_name . ';host=' . $db_host . ';charset=utf8;';
                    $user = $db_user;
                    $password = $db_pass;
                    $dbh = new PDO($dsn, $user, $password);
                    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $sql = 'INSERT INTO POINTPOSITION_MASTER (POINTFIELD_NO,POINTFIELD_NAME,POINTFIELD_QRCODE,MASTER_CODE,UPLOAD_DATE) VALUES (?,?,?,?,?)';
                    $stmt = $dbh->prepare($sql);
                    $data[] = $POINTFIELD_NO;
                    $data[] = $POINTFIELD_NAME;
                    $data[] = $POINTFIELD_QRCODE;
                    $data[] = $login_master_code;
                    $data[] = date('Y-m-d H:i:s');

                    $stmt->execute($data);
                    $dbh = null;

                    header('Location: top_point.php');
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
                            <a class="nav-link" aria-current="page" href="top_user.php">
                                <i class="fa-solid fa-children"></i>
                                <span class="ml-2">生徒マスター</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="top_point.php">
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
                <h1 class="h2">ポイント配布場所マスター</h1>
                <p>ポイント配布場所の追加</p>

                <p>
                    <font color="#ff0000"><?php echo $errormsg; ?></font>
                </p>

                <div class="row">
                    <div class="col-12 col-xl-12 mb-4 mb-lg-0">
                        <div class="card">
                            <div class="card-body">

                                <form action="top_point_set.php">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="inputGroup-sizing-default">ポイント配布場所番号</span>
                                        <input name="POINTFIELD_NO" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" style="ime-mode:active" placeholder="ポイント配布場所番号" value=<?= $POINTFIELD_NO ?>>
                                    </div>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="inputGroup-sizing-default">ポイント配布場所の名前</span>
                                        <input name="POINTFIELD_NAME" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" style="ime-mode: active" placeholder="配布場所の名前（100文字まで）" value=<?= $POINTFIELD_NAME ?>>
                                    </div>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="inputGroup-sizing-default">ポイントのQRコード</span>
                                        <input name="POINTFIELD_QRCODE" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" style="ime-mode: active" placeholder="QRコード（30文字まで）" value=<?= $POINTFIELD_QRCODE ?>>
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