<?php
    require_once("init.php");

    if (!isset($_SESSION['user_id'])) {
        header("Location: logowanie.php?return=produkt.php?id=" . ($_POST['id_produktu'] ?? ''));
        exit;
    }

    if (!isset($_POST['id_produktu'])) {
        die("Brak ID produktu.");
    }

    $id_produktu = (int)$_POST['id_produktu'];
    $id_uzytkownika = $_SESSION['user_id'];

    $result = mysqli_query($conn, "SELECT id_produktu FROM produkty WHERE id_produktu = $id_produktu");
    if (mysqli_num_rows($result) === 0) {
        die("Produkt nie istnieje.");
    }

    $stmt = mysqli_prepare($conn, "
        INSERT IGNORE INTO ulubione (id_uzytkownika, id_produktu) 
        VALUES (?, ?)
    ");
    mysqli_stmt_bind_param($stmt, "ii", $id_uzytkownika, $id_produktu);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $_SESSION['komunikat'] = "Dodano do ulubionych!";
    } else {
        mysqli_query($conn, "DELETE FROM ulubione WHERE id_uzytkownika = $id_uzytkownika AND id_produktu = $id_produktu");
        $_SESSION['komunikat'] = "Usunięto z ulubionych";
    }

    header("Location: produkt.php?id=$id_produktu");
    exit;
?>