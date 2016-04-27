<?php

include_once("db.config.php");

date_default_timezone_set("Asia/Taipei");

try {
    $db = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

$username = isset($_POST["username"])   ? $_POST["username"]    : "";
$password = isset($_POST["password"])   ? $_POST["password"]    : "";

if($username != "" && $password != ""){
    $q = $db->prepare("SELECT * FROM user WHERE username = :username LIMIT 1");
    $q->bindParam(":username", $username);
    $q->execute();

    $result = $q->fetch(PDO::FETCH_ASSOC);
    
    if(password_verify($password, $result["password"])){
        $token = $session_token = md5($username . date("Y-m-d H:i:s"));;
        $q = $db->prepare("UPDATE user SET token = :token WHERE id = :uid LIMIT 1");
        $q->bindParam(":token", $token);
        $q->bindParam(":uid", $result["id"]);
        $q->execute();

        $msg = array();
        $msg["status"] = "success";
        $msg["username"] = $result["username"];
        $msg["nickname"] = $result["nickname"];
        $msg["token"] = $token;
        $msg["msg"] = "login successful";
        echo json_encode($msg);
        //add session
        session_start();
        $_SESSION['id'] = $result["id"];
        $_SESSION['token'] = $token;

    }else{
        $msg = array();
        $msg["status"] = "failed";
        $msg["msg"] = "username or password wrong";
        echo json_encode($msg);
    }
}else{
    $msg = array();
    $msg["status"] = "failed";
    $msg["msg"] = "require username and password";
    echo json_encode($msg);
}
