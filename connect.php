<?php
    $host = "mysql2.small.pl";
    $login = "m2358_adminb";
    $pass = "zaq1@WSX";
    $db = "m2358_bitwear";
    
    $conn = mysqli_connect($host, $login, $pass, $db);
    if (!$conn) {
        die("Błąd podczas łączenia z bazą danych '$db'.");
    }
?>