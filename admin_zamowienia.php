<?php
require_once("init.php");

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['rola'], ['admin', 'staff'])) {
    header("Location: admin_panel.php");
    exit;
}

if (isset($_POST['zmien_status'])) {
    $id_zamowienia = (int)$_POST['id_zamowienia'];
    $nowy_status   = $_POST['status'];

    $stmt = mysqli_prepare($conn, "UPDATE zamowienia SET status = ? WHERE id_zamowienia = ?");
    mysqli_stmt_bind_param($stmt, "si", $nowy_status, $id_zamowienia);
    mysqli_stmt_execute($stmt);

    if (!empty($_POST['id_pracownika'])) {
        $id_prac = (int)$_POST['id_pracownika'];
        mysqli_query($conn, "UPDATE zamowienia SET id_pracownika = $id_prac WHERE id_zamowienia = $id_zamowienia");
    }

    if ($nowy_status === 'wysłane') {
        $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT email_wyslano FROM zamowienia WHERE id_zamowienia = $id_zamowienia"));

        if (!$check['email_wyslano']) {
            $klient = mysqli_fetch_assoc(mysqli_query($conn, "
                SELECT u.email, u.imie 
                FROM zamowienia z
                LEFT JOIN uzytkownicy u ON z.id_uzytkownika = u.id_uzytkownika
                WHERE z.id_zamowienia = $id_zamowienia
            "));

            if (!$klient || empty($klient['email'])) {
                $gosc = mysqli_fetch_assoc(mysqli_query($conn, "SELECT email, imie FROM zamowienia_dane_goscie WHERE id_zamowienia = $id_zamowienia"));
                $email = $gosc['email'] ?? '';
                $imie  = $gosc['imie'] ?? 'Klient';
            } else {
                $email = $klient['email'];
                $imie  = $klient['imie'];
            }

            if (!empty($email)) {
                $subject = "Twoje zamówienie nr $id_zamowienia zostało wysłane!";
                $message = "Witaj $imie,\n\nTwoje zamówienie o numerze #$id_zamowienia zostało właśnie wysłane.\nWkrótce otrzymasz je pod wskazany adres.\n\nDziękujemy za zakupy w BITWEAR!\n\nPozdrawiamy,\nZespół BITWEAR";

                $headers  = "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
                $headers .= "From: BITWEAR <no-reply@bitwear.pl>\r\n";
                $headers .= "Reply-To: no-reply@bitwear.pl\r\n";

                mail($email, '=?UTF-8?B?'.base64_encode($subject).'?=', $message, $headers);

                mysqli_query($conn, "UPDATE zamowienia SET email_wyslano = 1 WHERE id_zamowienia = $id_zamowienia");
            }
        }
    }

    header("Location: admin_zamowienia.php");
    exit;
}

$wynik = mysqli_query($conn, "
    SELECT z.*, u.imie, pu.imie AS pracownik_imie, z.email_wyslano
    FROM zamowienia z
    LEFT JOIN uzytkownicy u ON z.id_uzytkownika = u.id_uzytkownika
    LEFT JOIN pracownicy p ON z.id_pracownika = p.id_pracownika
    LEFT JOIN uzytkownicy pu ON p.id_uzytkownika = pu.id_uzytkownika
    WHERE z.id_uzytkownika IS NOT NULL OR EXISTS (SELECT 1 FROM zamowienia_dane_goscie g WHERE g.id_zamowienia = z.id_zamowienia)
    ORDER BY z.data_zamowienia DESC
");

$pracownicy = mysqli_query($conn, "SELECT id_pracownika, u.imie FROM pracownicy p JOIN uzytkownicy u ON p.id_uzytkownika = u.id_uzytkownika");
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzanie Zamówieniami – BITWEAR</title>
    <link rel="stylesheet" href="style/for_all.css">
    <link rel="stylesheet" href="style/admin.css">
    <link rel="icon" href="zdjecia/logo.png">
</head>
<body>
    <nav>
        <a href="strona_glowna.php" id="logo-a">
            <img src="zdjecia/logo.png" id="logo-img" alt="logo.png">
            <h1 id="logo-h1">BITWEAR</h1>
        </a>

        <div id="category-links">
            <div class="dropdown">
                <p>ONA</p>
                <div class="dropdown-content">
                    <a href="produkty.php?plec=ONA&kategoria=Jeansy">Jeansy</a>
                    <a href="produkty.php?plec=ONA&kategoria=Sneakersy">Sneakersy</a>
                    <a href="produkty.php?plec=ONA&kategoria=Skarpety">Dodatki</a>
                </div>
            </div>

            <div class="dropdown">
                <p>ON</p>
                <div class="dropdown-content">
                    <a href="produkty.php?plec=ON&kategoria=Jeansy">Jeansy</a>
                    <a href="produkty.php?plec=ON&kategoria=Sneakersy">Sneakersy</a>
                    <a href="produkty.php?plec=ON&kategoria=Skarpety">Dodatki</a>
                </div>
            </div>

            <div class="dropdown">
                <p>DZIECKO</p>
                <div class="dropdown-content">
                    <a href="produkty.php?plec=DZIECKO&kategoria=Jeansy">Jeansy</a>
                    <a href="produkty.php?plec=DZIECKO&kategoria=Sneakersy">Sneakersy</a>
                    <a href="produkty.php?plec=DZIECKO&kategoria=Skarpety">Dodatki</a>
                </div>
            </div>

            <?php if (isset($_SESSION['rola']) && $_SESSION['rola'] === 'admin'): ?>
                <a href="admin_panel.php" class="admin-panel-a">ADMIN PANEL</a>
            <?php endif; ?>
        </div>

        <div id="nav-icons">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="konto.php"><img src="zdjecia/konto.png" class="nav-icon" alt="Konto"></a>
                <a href="ulubione.php"><img src="zdjecia/ulubione.png" class="nav-icon" alt="Ulubione"></a>
            <?php else: ?>
                <a href="logowanie.php"><img src="zdjecia/konto.png" class="nav-icon" alt="Logowanie"></a>
            <?php endif; ?>
            <a href="koszyk.php"><img src="zdjecia/koszyk.png" class="nav-icon" alt="Koszyk"></a>
        </div>

        <a href="menu.php" id="hamburger-menu">
            <span></span><span></span><span></span>
        </a>
    </nav>

    <main>
        <h3>Zamówienia</h3>
        <?php while ($zam = mysqli_fetch_assoc($wynik)): ?>
            <div class="zamowienie-admin">
                <h4>Zamówienie #<?= $zam['id_zamowienia'] ?> - Klient: <?= htmlspecialchars($zam['imie'] ?? 'Gość') ?></h4>
                <p>Data: <?= $zam['data_zamowienia'] ?></p>
                <p>Wartość: <?= number_format($zam['cena_calkowita'], 2) ?> zł</p>
                <p>Przypisany pracownik: <?= htmlspecialchars($zam['pracownik_imie'] ?? 'Brak') ?></p>
                <form method="POST">
    <input type="hidden" name="id_zamowienia" value="<?= $zam['id_zamowienia'] ?>">
    <label>Status:
        <select name="status" <?= $zam['status'] === 'wysłane' ? 'disabled' : '' ?>>
            <option value="nowe" <?= $zam['status']=='nowe' ? 'selected' : '' ?>>Nowe</option>
            <option value="w realizacji" <?= $zam['status']=='w realizacji' ? 'selected' : '' ?>>W realizacji</option>
            <option value="wysłane" <?= $zam['status']=='wysłane' ? 'selected' : '' ?>>Wysłane</option>
        </select>
    </label>
    <label>Przypisz pracownika:
        <select name="id_pracownika">
            <option value="">Brak</option>
            <?php mysqli_data_seek($pracownicy, 0); ?>
            <?php while ($pr = mysqli_fetch_assoc($pracownicy)): ?>
                <option value="<?= $pr['id_pracownika'] ?>" <?= ($zam['id_pracownika'] ?? 0) == $pr['id_pracownika'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($pr['imie']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </label>
    <button type="submit" name="zmien_status" class="submit-btn" <?= $zam['status'] === 'wysłane' ? 'disabled' : '' ?>>
        Zapisz
    </button>
    <?php if ($zam['status'] === 'wysłane'): ?>
        <span style="color:#2e7d32;font-weight:bold;margin-left:15px;">E-mail o wysyłce wysłany</span>
    <?php endif; ?>
</form>
            </div>
        <?php endwhile; ?>
        <div class="powrot-div">
            <a href="admin_panel.php" class="powrot-a">Powrót</a>
        </div>
    </main>

    <footer>
        <hr>
        <center>
            <a href="strona_glowna.php"><img src="zdjecia/logo.png" id="footer-logo-img" alt="logo.png"></a>
            <p>Zawartość tej strony jest chroniona prawem autorskim i należy do spółki <b>BitComp</b>.</p>
            <div id="footer-logo-div">
                <a href="https://www.youtube.com"><img src="zdjecia/yt logo.png" class="footer-social-media" alt="youtube logo"></a>
                <a href="https://instagram.com"><img src="zdjecia/ig logo.png" class="footer-social-media" alt="instagram logo"></a>
            </div>
        </center>
    </footer>
</footer>
</body>
</html>