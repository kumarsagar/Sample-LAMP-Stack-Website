<?php
$servername = "localhost:3306";
$username = "sagar";
$password = "pass123";

try {
    $conn = new PDO("mysql:host=$servername", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "CREATE DATABASE 6470";
    $conn->exec($sql);
    echo "Database created successfully<br>";
    createTable($servername, $username, $password);
  } catch(PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
  }

function createTable($servername, $username, $password){
    try{
        $conn = new PDO("mysql:host=$servername;dbname=6470", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "CREATE TABLE 6470exerciseusers (
                                                USERNAME VARCHAR(100) UNIQUE,
                                                PASSWORD_HASH CHAR(40),
                                                PHONE VARCHAR(10) 
                                                )";
        $conn->exec($sql);
        echo "Table 6470exerciseusers created successfully";
      }catch(PDOException $e) {
        echo $sql . "<br>" . $e->getMessage();
      }
    }
?>