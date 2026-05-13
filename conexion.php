<?php
$host     = "localhost";
$port     = "5432";
$dbname   = "inventario_db";
$user     = "postgres";
$password = "47460"; // 
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}
?>