<?php
// Set timezone to Thailand
date_default_timezone_set('Asia/Bangkok');

// Show errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "db";
$username = "user";
$password = "user_password"; //ถ้าไม่ได้ตั้งรหัสผ่านให้ลบ user_password ออก

try {
  $condb = new PDO("mysql:host=$servername;dbname=inventory_db;charset=utf8", $username, $password);
  // set the PDO error mode to exception
  $condb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  //echo "Connected successfully";
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
?>
