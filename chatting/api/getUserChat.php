<?php

include_once("db.config.php");

try {
    $db = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

$id = isset($_POST["id"]) ? $_POST["id"] : "1";
$token = isset($_SESSION['token']) ? $_SESSION['token']:"";

if($id != ""&&$token!=""){
  
    //檢查token是否正確
    $q = $db->prepare("SELECT * FROM user WHERE id = :id AND token = :token");
    $q->bindParam(":id", $id);
    $q->bindParam(":token", $token);
    $q->execute();
    if($q->rowCount() != "1") { 
      $msg = array();
      $msg["status"] = "failed";
      $msg["msg"] = "tokenError";
      echo json_encode($msg);
      return;
    }

    $q = $db->prepare("SELECT * FROM chat WHERE from_id = :fid OR to_id = :fid OR to_id = 1");
    $q->bindParam(":fid", $id);
    $q->bindParam(":tid", $id);
    $q->execute();

    $result = $q->fetchall(PDO::FETCH_ASSOC);
    echo json_encode($result);
}else{
    $msg = array();
    $msg["status"] = "failed";
    $msg["msg"] = "all field must be fill";
    echo json_encode($msg);
}
