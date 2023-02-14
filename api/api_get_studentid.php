<?php
require('../kanri/login_pass.php');

if (isset($_GET['DEPERTMENT_CODE'])) {
    $DEPERTMENT_CODE = $_GET['DEPERTMENT_CODE'];
    if (isset($_GET['STUDENT_CODE'])) {
        $STUDENT_CODE = $_GET['STUDENT_CODE'];

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
            $stmt = $pdo->prepare('SELECT * FROM STUDENT_MASTER WHERE DEPERTMENT_CODE=? AND STUDENT_CODE=?');
            $data[] = $DEPERTMENT_CODE;
            $data[] = $STUDENT_CODE;

            // SQL実行
            $stmt->execute($data);

            while (true) {
                $rec = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($rec == false) {
                    break;
                }

                $person = [
                    'login' => true,
                    'student_no' => $rec['STUDENT_NO'],
                ];
                $person = json_encode($person, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                echo $person . PHP_EOL;
                exit;
            }

            $person = [
                'login' => false,
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
} else {
    $person = [
        'error info' => 'not parameter',
    ];
    $person = json_encode($person, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    echo $person . PHP_EOL;
}
