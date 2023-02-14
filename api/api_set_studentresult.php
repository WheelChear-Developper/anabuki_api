<?php
require('../kanri/login_pass.php');

// セッション管理開始
session_start();

$errormsg = '';

$MASTER_CODE = '';
$PASSWORD = '';

$STUDENT_NUMBER = '';
$LOTTERY_RESULT = '';

if (isset($_GET['STUDENT_NUMBER'])) {
    $STUDENT_NUMBER = $_GET['STUDENT_NUMBER'];
    if (isset($_GET['LOTTERY_RESULT'])) {
        $LOTTERY_RESULT = $_GET['LOTTERY_RESULT'];

        if (isset($_GET['MASTER_CODE'])) {
            $MASTER_CODE = $_GET['MASTER_CODE'];
            if (isset($_GET['PASSWORD'])) {
                $PASSWORD = $_GET['PASSWORD'];

                try {

                    $STUDENT_NUMBER = htmlspecialchars($STUDENT_NUMBER, ENT_QUOTES, 'UTF-8');
                    $LOTTERY_RESULT = htmlspecialchars($LOTTERY_RESULT, ENT_QUOTES, 'UTF-8');

                    $MASTER_CODE = htmlspecialchars($MASTER_CODE, ENT_QUOTES, 'UTF-8');
                    $PASSWORD = htmlspecialchars($PASSWORD, ENT_QUOTES, 'UTF-8');

                    $MASTER_USER_ON = false;
                    $STUDENT_NUMBER_ON = false;

                    //MYSQL接続設定
                    $dsn = 'mysql:dbname=' . $db_name . ';host=' . $db_host . ';charset=utf8;';
                    $user = $db_user;
                    $password = $db_pass;
                    $dbh = new PDO($dsn, $user, $password);
                    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    //SQL実行(Master)
                    $sql = 'SELECT * FROM MASTER_USER WHERE MASTER_CODE=? AND PASSWORD=?';
                    $stmt_master = $dbh->prepare($sql);
                    $data_master[] = $MASTER_CODE;
                    $data_master[] = $PASSWORD;

                    $stmt_master->execute($data_master);

                    $rec = $stmt_master->fetch(PDO::FETCH_ASSOC);

                    if (
                        $rec == false
                    ) {
                        $MASTER_USER_ON = false;
                    } else {
                        $MASTER_USER_ON = true;
                    }

                    //SQL実行(Student)
                    $sql = 'SELECT * FROM STUDENT_MASTER WHERE STUDENT_NO=?';
                    $stmt_student = $dbh->prepare($sql);
                    $data_student[] = $STUDENT_NUMBER;

                    $stmt_student->execute($data_student);

                    $rec = $stmt_student->fetch(PDO::FETCH_ASSOC);

                    if (
                        $rec == false
                    ) {
                        $STUDENT_NUMBER_ON = false;
                    } else {
                        $STUDENT_NUMBER_ON = true;
                    }

                    if ($MASTER_USER_ON == true) {
                        if ($STUDENT_NUMBER_ON == true) {

                            $sql = 'INSERT INTO STUDENTRESULT_TABLE (STUDENT_NO,LOTTERY_RESULT,MASTER_CODE,UPLOAD_DATE) VALUES (?,?,?,?)';
                            $stmt_pointtable = $dbh->prepare($sql);
                            $data_pointtable[] = $STUDENT_NUMBER;
                            $data_pointtable[] = $LOTTERY_RESULT;
                            $data_pointtable[] = $MASTER_CODE;
                            $data_pointtable[] = date('Y-m-d H:i:s');

                            $stmt_pointtable->execute($data_pointtable);
                            $dbh = null;

                            $person = [
                                'DB' => 'setdata',
                            ];
                        } else {
                            $person = [
                                'DB Error' =>
                                'Nohit(Student_number)',
                            ];
                        }
                    } else {
                        $person = [
                            'DB Error' =>
                            'Nohit(Master)',
                        ];
                    }
                } catch (Exception $e) {
                    $person = [
                        'DB Error' => 'already registered',
                    ];
                }
            } else {
                $person = [
                    'DB Error' =>
                    'parameter offset',
                ];
            }
        } else {
            $person = [
                'DB Error' =>
                'parameter offset',
            ];
        }
    } else {
        $person = [
            'DB Error' =>
            'parameter offset',
        ];
    }
} else {
    $person = [
        'DB Error' =>
        'parameter offset',
    ];
}

$person = json_encode($person, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
echo $person . PHP_EOL;
