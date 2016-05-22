<?php

include_once("db.config.php");

try {
    $db = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

$id     = isset($_POST["id"])      ?   $_POST["id"]     :  "1";
$token  = isset($_POST['token'])   ?   $_POST['token']  :  "";

if($id != "" && $token != ""){
  
    //檢查token是否正確
    $q = $db->prepare("SELECT * FROM user WHERE id = :id AND token = :token");
    $q->bindParam(":id", $id);
    $q->bindParam(":token", $token);
    $q->execute();
    if($q->rowCount() != "1") { 

      $msg            = array();
      $msg["status"]  = "failed";
      $msg["msg"]     = "token error";
      echo json_encode($msg);
      return;
    }

    $q = $db->prepare("DELETE FROM chat where from_id = :fid or to_id = :tid ");
    $q->bindParam(":fid", $id);
    $q->bindParam(":tid", $id);
    $q->execute();

    $msg            = array();
    $msg["status"]  = "success";
    $msg["msg"]     = "chat delete success";
    echo json_encode($msg);

}else{
    $msg            = array();
    $msg["status"]  = "failed";
    $msg["msg"]     = "chat delete error";
    echo json_encode($msg);
}
