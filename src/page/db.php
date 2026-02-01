<?php
$host = 'localhost';
$port = '5432';
$dbname = 'TW'; 
$user = 'www';
$password = 'www'; 

$connection_string = "host=$host port=$port dbname=$dbname user=$user password=$password";

$db = pg_connect($connection_string);

if (!$db) {
    die('Errore critico di connessione al Database.');
}
?>