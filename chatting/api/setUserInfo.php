<?php
include_once("db.config.php");

try {
    $db = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

$sex = isset($_POST['sex'])             ? $_POST['sex']       : "";
$nickname = isset($_POST['nickname'])   ? $_POST['nickname']  : "";
$username = isset($_POST['username'])   ? $_POST['username']  : "";
$token = isset($_POST['token'])         ? $_POST['token']     : "";

if($username != "" && $token != "" && $sex!="" && $nickname!=""){

    //檢查token是否正確
    $q = $db->prepare("SELECT * FROM user WHERE username = :username AND token = :token");
    $q->bindParam(":username", $username);
    $q->bindParam(":token", $token);
    $q->execute();
    if($q->rowCount() != "1") {
        $msg            = array();
        $msg["status"]  = "failed";
      echo json_encode($msg);
    }
    else{
       //更新個人資料
    $q = $db->prepare("UPDATE chatroom.user SET sex = :sex,nickname = :nickname WHERE username = :username AND token = :token");
    $q->bindParam(":sex", $sex);
    $q->bindParam(":nickname", $nickname);
    $q->bindParam(":username", $username);
    $q->bindParam(":token", $token);
    $q->execute();

    $msg            = array();
    $msg["status"]  = "success";
    echo json_encode($msg);
    }
   
}
else{
    $msg            = array();
    $msg["status"]  = "failed";
    $msg["msg"]     = "all field must be fill";
    echo json_encode($msg);
}
