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
                            <a class="nav-link active" href="top_hit.php">
                                <i class="fa-solid fa-boxes-stacked"></i>
                                <span class="ml-2">抽選結果</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4 py-4">
                <h1 class="h2">抽選結果</h1>

                <div class="row">
                    <div class="col-12 col-xl-12 mb-4 mb-lg-0">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">学籍番号</th>
                                                <th scope="col">生徒名</th>
                                                <th scope="col">抽選結果（未抽選:ハズレ：０、当たり：１）</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            try {

                                                // DB接続
                                                $pdo = new PDO(
                                                    // ホスト名、データベース名
                                                    'mysql:dbname=' . $db_name . ';host=' . $db_host . ';charset=utf8;',
                                                    // ユーザー名
                                                    $db_user,
                                                    // パスワード
                                                    $db_pass,
                                                    // レコード列名をキーとして取得させる
                                                    [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
                                                );

                                                // SQL文をセット
                                                //$stmt = $pdo->prepare('SELECT STUDENT_NUMBER,LOTTERY_RESULT,STUDENT_MASTER.NAME FROM STUDENTRESULT_TABLE INNER JOIN STUDENT_MASTER WHERE 1 ORDER BY STUDENT_NUMBER DESC');
                                                $sql =
                                                    'SELECT SM.STUDENT_CODE, ST.LOTTERY_RESULT, SM.NAME ' .
                                                    'FROM STUDENTRESULT_TABLE ST ' .
                                                    'LEFT JOIN STUDENT_MASTER SM ON SM.STUDENT_NO=ST.STUDENT_NO ' .
                                                    'WHERE 1 ' .
                                                    'ORDER BY STUDENT_CODE DESC;';
                                                $stmt = $pdo->prepare($sql);

                                                // SQL実行
                                                $stmt->execute();

                                                $pdo = null;

                                                while (true) {
                                                    $rec = $stmt->fetch(PDO::FETCH_ASSOC);

                                                    if ($rec == false) {
                                                        break;
                                                    }
                                                    print '<form action="top_point_edit.php">';
                                                    print '<tr>';
                                                    print '<td>' . $rec['STUDENT_CODE'] . '</td>';
                                                    print '<td>' . $rec['NAME'] . '</td>';
                                                    print '<td>' . $rec['LOTTERY_RESULT'] . '</td>';
                                                    print '</tr>';
                                                    print '</form>';
                                                }
                                            } catch (Exception $e) {
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-4">
                        <!-- Another widget will go here -->
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js" integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/" crossorigin="anonymous"></script>

</html>