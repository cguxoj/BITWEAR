<?php
require_once("init.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST' 
    || !isset($_POST['id_produktu']) 
    || !isset($_POST['rozmiar'])
    || !isset($_POST['ilosc'])) 
{
    header("Location: strona_glowna.php");
    exit;
}

$id_produktu   = (int)$_POST['id_produktu'];
$rozmiar_nazwa = trim($_POST['rozmiar']);
$ilosc_zamowiona = max(1, (int)$_POST['ilosc']);

$stmt = mysqli_prepare($conn, "
    SELECT pr.ilosc, r.id_rozmiaru 
    FROM produkty_rozmiary pr
    JOIN rozmiary r ON pr.id_rozmiaru = r.id_rozmiaru
    WHERE pr.id_produktu = ? AND r.nazwa = ?
");
mysqli_stmt_bind_param($stmt, "is", $id_produktu, $rozmiar_nazwa);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    $_SESSION['komunikat'] = "Wybrany rozmiar jest niedostępny.";
    header("Location: produkt.php?id=$id_produktu");
    exit;
}

$row = mysqli_fetch_assoc($result);
$id_rozmiaru = $row['id_rozmiaru'];
$ilosc_magazyn = (int)$row['ilosc'];

if ($ilosc_zamowiona > $ilosc_magazyn) {
    $_SESSION['komunikat'] = "Nie można dodać $ilosc_zamowiona szt. – dostępnych jest tylko $ilosc_magazyn.";
    header("Location: produkt.php?id=$id_produktu");
    exit;
}

$stmt2 = mysqli_prepare($conn, "SELECT nazwa, cena FROM produkty WHERE id_produktu = ?");
mysqli_stmt_bind_param($stmt2, "i", $id_produktu);
mysqli_stmt_execute($stmt2);
$prod = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2));

$klucz = "$id_produktu|$id_rozmiaru";

if (!isset($_SESSION['koszyk'])) {
    $_SESSION['koszyk'] = [];
}

if (isset($_SESSION['koszyk'][$klucz])) {
    $_SESSION['koszyk'][$klucz]['ilosc'] += $ilosc_zamowiona;
} else {
    $_SESSION['koszyk'][$klucz] = [
        'id_produktu' => $id_produktu,
        'id_rozmiaru' => $id_rozmiaru,
        'nazwa'       => $prod['nazwa'],
        'cena'        => $prod['cena'],
        'rozmiar'     => $rozmiar_nazwa,
        'ilosc'       => $ilosc_zamowiona
    ];
}

if (isset($_SESSION['user_id'])) {

    $user_id = (int)$_SESSION['user_id'];

    $check = mysqli_prepare($conn, "
        SELECT ilosc FROM koszyk 
        WHERE id_uzytkownika = ? AND id_produktu = ? AND id_rozmiaru = ?
    ");
    mysqli_stmt_bind_param($check, "iii", $user_id, $id_produktu, $id_rozmiaru);
    mysqli_stmt_execute($check);
    $exists = mysqli_stmt_get_result($check);

    if (mysqli_num_rows($exists) > 0) {
        $update = mysqli_prepare($conn, "
            UPDATE koszyk SET ilosc = ilosc + ? 
            WHERE id_uzytkownika = ? AND id_produktu = ? AND id_rozmiaru = ?
        ");
        mysqli_stmt_bind_param($update, "iiii", $ilosc_zamowiona, $user_id, $id_produktu, $id_rozmiaru);
        mysqli_stmt_execute($update);

    } else {
        $insert = mysqli_prepare($conn, "
            INSERT INTO koszyk (id_uzytkownika, id_produktu, id_rozmiaru, ilosc) 
            VALUES (?, ?, ?, ?)
        ");
        mysqli_stmt_bind_param($insert, "iiii", $user_id, $id_produktu, $id_rozmiaru, $ilosc_zamowiona);
        mysqli_stmt_execute($insert);
    }
}

$update_magazyn = mysqli_prepare($conn, "
    UPDATE produkty_rozmiary 
    SET ilosc = ilosc - ? 
    WHERE id_produktu = ? AND id_rozmiaru = ?
");
mysqli_stmt_bind_param($update_magazyn, "iii", $ilosc_zamowiona, $id_produktu, $id_rozmiaru);
mysqli_stmt_execute($update_magazyn);

$_SESSION['komunikat'] = "Dodano $ilosc_zamowiona szt. do koszyka.";
header("Location: produkt.php?id=$id_produktu");
exit;
?>
