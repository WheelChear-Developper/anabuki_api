<?php
require('../kanri/login_pass.php');

if (isset($_GET['STUDENT_NO'])) {
    $STUDENT_NO = $_GET['STUDENT_NO'];

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
        $sql =
            'SELECT * ' .
            'FROM POINTPOSITION_MASTER';
        $stmt_master = $pdo->prepare($sql);

        // SQL実行
        $stmt_master->execute();

        $master_count = 0;
        while (true) {
            $rec = $stmt_master->fetch(PDO::FETCH_ASSOC);

            if ($rec == false) {
                break;
            }

            $master_count = $master_count + 1;
        }

        // SQL文をセット
        $sql =
            'SELECT sm.STUDENT_CODE, sm.NAME, pm.POINTFIELD_NAME, pm.POINTFIELD_QRCODE, QR_CODE ' .
            'FROM POINT_TABLE pt ' .
            'LEFT JOIN STUDENT_MASTER sm ON sm.STUDENT_NO = pt.STUDENT_NO ' .
            'LEFT JOIN POINTPOSITION_MASTER pm ON pm.POINTFIELD_NO = pt.POINTFIELD_NO ' .
            'WHERE sm.STUDENT_NO =? ' .
            'ORDER BY sm.STUDENT_NO ASC, pm.POINTFIELD_NAME ASC;';
        $stmt = $pdo->prepare($sql);
        $data[] = $STUDENT_NO;

        // SQL実行
        $stmt->execute($data);

        $count = 0;
        while (true) {
            $rec = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($rec == false) {
                break;
            }

            $count = $count + 1;
        }

        $person = [
            'master_count' => $master_count,
            'point_count' => $count,
        ];
        $person = json_encode($person, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        echo $person . PHP_EOL;
    } catch (Exception $e) {
        $person = [
            'error info' => 'db error',
        ];
        $person = json_encode($person, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        echo $person . PHP_EOL;
    }
} else {
    $person = [
        'error info' => 'not parameter',
    ];
    $person = json_encode($person, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    echo $person . PHP_EOL;
}
