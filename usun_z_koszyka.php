<?php
    require_once("init.php");

    if (!isset($_GET['id']) || !isset($_GET['rozmiar'])) {
        header("Location: koszyk.php");
        exit;
    }

    $id_produktu = (int)$_GET['id'];
    $rozmiar = $_GET['rozmiar'];

    $id_rozmiaru = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_rozmiaru FROM rozmiary WHERE nazwa = '" . mysqli_real_escape_string($conn, $rozmiar) . "'"))['id_rozmiaru'] ?? 0;

    if (isset($_SESSION['user_id'])) {
        $id_uzytkownika = (int)$_SESSION['user_id'];
        $ilosc = mysqli_fetch_assoc(mysqli_query($conn, "SELECT ilosc FROM koszyk WHERE id_uzytkownika = $id_uzytkownika AND id_produktu = $id_produktu AND id_rozmiaru = $id_rozmiaru"))['ilosc'] ?? 0;
        
        mysqli_query($conn, "DELETE FROM koszyk WHERE id_uzytkownika = $id_uzytkownika AND id_produktu = $id_produktu AND id_rozmiaru = $id_rozmiaru");
        if ($ilosc > 0) {
            mysqli_query($conn, "UPDATE produkty_rozmiary SET ilosc = ilosc + $ilosc WHERE id_produktu = $id_produktu AND id_rozmiaru = $id_rozmiaru");
        }
    } else {
        $klucz = "$id_produktu|$id_rozmiaru";
        $ilosc = $_SESSION['koszyk'][$klucz]['ilosc'] ?? 0;
        unset($_SESSION['koszyk'][$klucz]);
        if ($ilosc > 0) {
            mysqli_query($conn, "UPDATE produkty_rozmiary SET ilosc = ilosc + $ilosc WHERE id_produktu = $id_produktu AND id_rozmiaru = $id_rozmiaru");
        }
    }

    $_SESSION['komunikat'] = "Usunięto z koszyka";
    header("Location: koszyk.php");
    exit;
?>