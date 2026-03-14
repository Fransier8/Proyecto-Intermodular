<?php
define("USER_DB", "root");
define("PASSWORD", "");
define("DSN", "mysql:host=localhost;dbname=protectora");
function get_conexion()
{
    try {
        $con = new PDO(DSN, USER_DB, PASSWORD);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
    return $con;
}
?>