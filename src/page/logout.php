<?php
session_start();
session_unset();
session_destroy(); // Termina la sessione ed elimina le variabili associate
header("Location: home.php"); // Reindirizza l'utente alla home dopo l'operazione di logout
exit();
?>