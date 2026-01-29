<?php
// Configurazione Database
$host = 'localhost';
$port = '5432';
$dbname = 'db_tw'; // Assicurati che il nome sia lo stesso creato in postgres
$user = 'postgres';
$password = '123456'; // Inserisci la tua password

// Stringa di connessione
$connection_string = "host=$host port=$port dbname=$dbname user=$user password=$password";

// Connessione con gestione errore
$db = pg_connect($connection_string) or die('Impossibile connettersi: ' . pg_last_error());
?>