<?php
$host     = "postgres.railway.internal";
$port     = "5432";
$dbname   = "railway";
$user     = "postgres";
$password = "txccDihzEGeuqPYsPIYTVtfJOxCeKkWX";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password sslmode=require");

if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}
?>
