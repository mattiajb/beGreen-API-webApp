<?php
// Configurazione Database
$host = 'localhost';
$port = '5432';
$dbname = 'TW'; 
$user = 'www';
$password = 'www'; 

// Stringa di connessione
$connection_string = "host=$host port=$port dbname=$dbname user=$user password=$password";

// Connessione con gestione errore
$db = pg_connect($connection_string);

if (!$db) {
    // In produzione non stampare l'errore esatto all'utente, ma loggalo
    die('Errore critico di connessione al Database.');
}
?>