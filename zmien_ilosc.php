<?php
    require_once("init.php");

    if ($_SERVER['REQUEST_METHOD'] !== 'POST'
        || !isset($_POST['id_produktu'])
        || !isset($_POST['id_rozmiaru'])
        || !isset($_POST['akcja'])) 
    {
        header("Location: koszyk.php");
        exit;
    }

    $id_produktu = (int)$_POST['id_produktu'];
    $id_rozmiaru = (int)$_POST['id_rozmiaru'];
    $akcja       = $_POST['akcja'];

    $obecna_ilosc = 0;

    if (isset($_SESSION['user_id'])) {
        $user_id = (int)$_SESSION['user_id'];

        $sql = "SELECT ilosc FROM koszyk 
                WHERE id_uzytkownika = ? AND id_produktu = ? AND id_rozmiaru = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $user_id, $id_produktu, $id_rozmiaru);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($res)) {
            $obecna_ilosc = (int)$row['ilosc'];
        }
    } else {
        $klucz = "$id_produktu|$id_rozmiaru";
        if (isset($_SESSION['koszyk'][$klucz])) {
            $obecna_ilosc = $_SESSION['koszyk'][$klucz]['ilosc'];
        }
    }

    if ($obecna_ilosc <= 0) {
        header("Location: koszyk.php");
        exit;
    }

    if ($akcja === "minus") {

        if ($obecna_ilosc == 1) {
            $_SESSION['komunikat'] = "Aby usunąć produkt, kliknij usuń.";
            header("Location: koszyk.php");
            exit;
        }

        mysqli_query($conn, "
            UPDATE produkty_rozmiary
            SET ilosc = ilosc + 1
            WHERE id_produktu = $id_produktu AND id_rozmiaru = $id_rozmiaru
        ");

        if (isset($_SESSION['user_id'])) {
            $sql = "UPDATE koszyk SET ilosc = ilosc - 1
                    WHERE id_uzytkownika = ? AND id_produktu = ? AND id_rozmiaru = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iii", $user_id, $id_produktu, $id_rozmiaru);
            mysqli_stmt_execute($stmt);
        } else {
            $_SESSION['koszyk'][$klucz]['ilosc']--;
        }

        $_SESSION['komunikat'] = "Zmniejszono ilość.";
        header("Location: koszyk.php");
        exit;
    }
    if ($akcja === "plus") {
        $q = mysqli_query($conn, "
            SELECT ilosc FROM produkty_rozmiary
            WHERE id_produktu = $id_produktu AND id_rozmiaru = $id_rozmiaru
        ");
        $stan = mysqli_fetch_assoc($q)['ilosc'];

        if ($stan <= 0) {
            $_SESSION['komunikat'] = "Brak produktu na magazynie.";
            header("Location: koszyk.php");
            exit;
        }

        mysqli_query($conn, "
            UPDATE produkty_rozmiary
            SET ilosc = ilosc - 1
            WHERE id_produktu = $id_produktu AND id_rozmiaru = $id_rozmiaru
        ");

        if (isset($_SESSION['user_id'])) {
            $sql = "UPDATE koszyk SET ilosc = ilosc + 1
                    WHERE id_uzytkownika = ? AND id_produktu = ? AND id_rozmiaru = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iii", $user_id, $id_produktu, $id_rozmiaru);
            mysqli_stmt_execute($stmt);
        } else {
            $_SESSION['koszyk'][$klucz]['ilosc']++;
        }

        $_SESSION['komunikat'] = "Zwiększono ilość.";
        header("Location: koszyk.php");
        exit;
    }

    header("Location: koszyk.php");
    exit;
?>