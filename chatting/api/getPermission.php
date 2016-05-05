<?php
include_once("db.config.php");

try {
    $db = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

$username = isset($_POST['username'])   ? $_POST['username']  : "";
$token =  isset($_POST['token'])        ? $_POST['token']     : "";

if($username!=""&&$token!=""){
  $q = $db->prepare("SELECT * FROM user WHERE username = :username AND token = :token");
  $q->bindParam(":username", $username);
  $q->bindParam(":token", $token);
  $q->execute();
  if($q->rowCount() == "1") {
      $msg = array();
      $msg["status"] = "success";
      echo json_encode($msg);
  }
  else{
      $msg = array();
      $msg["status"] = "failed";
      echo json_encode($msg);
  }

}
