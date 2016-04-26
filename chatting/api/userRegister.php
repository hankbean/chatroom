<?php

include_once("db.config.php");

try {
    $db = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

$username = isset($_POST["username"])   ? $_POST["username"]    : "";
$password = isset($_POST["password"])   ? $_POST["password"]    : "";
$sex      = isset($_POST["sex"])        ? $_POST["sex"]         : "";
$email    = isset($_POST["email"])      ? $_POST["email"]       : "";
$nickname = isset($_POST["nickname"])   ? $_POST["nickname"]    : "";
$password_hash = password_hash($password, PASSWORD_DEFAULT);


if($username != "" && $password != "" && $sex != "" && $email != "" && $nickname != ""){
   
   
    $q = $db->prepare("SELECT * FROM user WHERE username = :username LIMIT 1");
    $q->bindParam(":username", $username);
    $q->execute();

    if($q->rowCount() == "1") {
        $msg = array();
        $msg["status"] = "failed";
        $msg["msg"] = "Can't register beacuse user has been exist";
        echo json_encode($msg);
        return ;
    }

    $result = $q->fetch(PDO::FETCH_ASSOC);

    $q = $db->prepare("INSERT INTO user (username, password, sex, email, nickname) VALUES (:username, :password, :sex, :email, :nickname)");
    $q->bindParam(":username", $username);
    $q->bindParam(":password", $password_hash);
    $q->bindParam(":sex", $sex);
    $q->bindParam(":email", $email);
    $q->bindParam(":nickname", $nickname);
    $q->execute();

    if($q){
        $msg = array();
        $msg["status"] = "success";
        $msg["msg"] = "register successful";
        echo json_encode($msg);
    }else{
        $msg = array();
        $msg["status"] = "failed";
        $msg["msg"] = "register failed";
        echo json_encode($msg);
    }
}else{
    $msg = array();
    $msg["status"] = "failed";
    $msg["msg"] = "all field must be fill";
    echo json_encode($msg);
}
