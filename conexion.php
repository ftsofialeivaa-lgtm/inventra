<?php
$url = parse_url("postgresql://postgres:txccDihzEGeuqPYsPIYTVtfJOxCeKkWX@shuttle.proxy.rlwy.net:16824/railway");

$host     = $url["host"];
$port     = $url["port"];
$dbname   = ltrim($url["path"], "/");
$user     = $url["user"];
$password = $url["pass"];

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password sslmode=require");

if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}
?>
