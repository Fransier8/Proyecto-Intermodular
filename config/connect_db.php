<?php
define("USER_DB", "root");
define("PASSWORD", "");
define("DSN", "mysql:host=localhost;dbname=protectora;charset=utf8mb4");
function get_conexion()
{
    try {
        $con = new PDO(DSN, USER_DB, PASSWORD,
            [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
    return $con;
}
?>