<?php
require('../kanri/login_pass.php');

if (isset($_GET['student_no'])) {
    $STUDENT_NO = $_GET['student_no'];

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
        $stmt = $pdo->prepare('SELECT DEPERTMENT_CODE,STUDENT_CODE,NAME FROM STUDENT_MASTER WHERE STUDENT_NO=?');
        $data[] = $STUDENT_NO;

        // SQL実行
        $stmt->execute($data);

        $all = array();
        while (true) {
            $rec = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($rec == false) {
                break;
            }

            $person = array(
                'depertment_code' => $rec['DEPERTMENT_CODE'],
                'student_code' => $rec['STUDENT_CODE'],
                'name' => $rec['NAME'],
            );
            array_push($all, $person);
        }

        $person2 = array(
            'user_list' => $all,
        );

        $person = json_encode($person2, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        echo $person . PHP_EOL;
    } catch (Exception $e) {
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
