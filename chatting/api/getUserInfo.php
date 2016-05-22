<?php

include_once("db.config.php");

try {
    $db = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

$username   = isset($_POST['username'])   ?   $_POST['username']  : "";
$token      = isset($_POST['token'])      ?   $_POST['token']     : "";

if($username != "" && $token != ""){
  
    //檢查token是否正確
    $q = $db->prepare("SELECT * FROM user WHERE username = :username AND token = :token");
    $q->bindParam(":username", $username);
    $q->bindParam(":token", $token);
    $q->execute();
   
    if($q->rowCount() != "1"){
        $msg            = array();
        $msg["status"]  = "failed";
        echo json_encode($msg);
    }

    $q = $db->prepare("SELECT email,sex,nickname FROM user WHERE username = :username AND token = :token");
    $q->bindParam(":token", $token);
    $q->bindParam(":username", $username);
    $q->execute();

    $result = $q->fetchall(PDO::FETCH_ASSOC);
    echo json_encode($result);

}
else{
    $msg            = array();
    $msg["status"]  = "failed";
    $msg["msg"]     = "all field must be fill";
    echo json_encode($msg);
}
