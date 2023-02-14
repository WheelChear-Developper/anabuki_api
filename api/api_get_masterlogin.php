<?php
require('../kanri/login_pass.php');

if (isset($_GET['name'])) {
    $name = $_GET['name'];
    if (isset($_GET['password'])) {
        $password = $_GET['password'];

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
            $stmt = $pdo->prepare('SELECT * FROM MASTER_USER WHERE NAME=? AND PASSWORD=?');
            $data[] = $name;
            $data[] = $password;

            // SQL実行
            $stmt->execute($data);

            while (true) {
                $rec = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($rec == false) {
                    break;
                }

                $person = [
                    'login' => true,
                    'MasterCode' => $rec['MASTER_CODE'],
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
