<?php
require_once("init.php");

$is_guest = !isset($_SESSION['user_id']);
$komunikat = $_SESSION['komunikat'] ?? '';
unset($_SESSION['komunikat']);

$koszyk = [];
$suma_brutto = 0;

if ($is_guest) {
    if (empty($_SESSION['koszyk'])) {
        header("Location: koszyk.php");
        exit;
    }

    foreach ($_SESSION['koszyk'] as $klucz => $item) {
        list($id_produktu, $id_rozmiaru) = explode('|', $klucz);
        $id_produktu = (int)$id_produktu;
        $id_rozmiaru = (int)$id_rozmiaru;

        $stmt = mysqli_prepare($conn, "
            SELECT p.id_produktu, p.nazwa, p.cena, r.nazwa AS rozmiar,
                   (SELECT sciezka FROM zdjecia_produktow WHERE id_produktu = p.id_produktu AND typ = 'main' LIMIT 1) AS sciezka
            FROM produkty p
            JOIN rozmiary r ON r.id_rozmiaru = ?
            WHERE p.id_produktu = ?
        ");
        mysqli_stmt_bind_param($stmt, "ii", $id_rozmiaru, $id_produktu);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($row) {
            $row['ilosc'] = $item['ilosc'];
            $row['id_rozmiaru'] = $id_rozmiaru;
            $koszyk[] = $row;
            $suma_brutto += $row['cena'] * $row['ilosc'];
        }
    }
} else {
    $id_uzytkownika = (int)$_SESSION['user_id'];
    $stmt = mysqli_prepare($conn, "SELECT imie, email, telefon, poczta, kod_pocztowy, adres FROM uzytkownicy WHERE id_uzytkownika = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_uzytkownika);
    mysqli_stmt_execute($stmt);
    $uzytkownik = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    $result = mysqli_query($conn, "
        SELECT k.id_produktu, k.id_rozmiaru, k.ilosc, p.nazwa, p.cena, r.nazwa AS rozmiar,
               (SELECT sciezka FROM zdjecia_produktow WHERE id_produktu = p.id_produktu AND typ = 'main' LIMIT 1) AS sciezka
        FROM koszyk k
        JOIN produkty p ON k.id_produktu = p.id_produktu
        JOIN rozmiary r ON k.id_rozmiaru = r.id_rozmiaru
        WHERE k.id_uzytkownika = $id_uzytkownika
    ");

    while ($item = mysqli_fetch_assoc($result)) {
        $koszyk[] = $item;
        $suma_brutto += $item['cena'] * $item['ilosc'];
    }
}

if (empty($koszyk)) {
    header("Location: koszyk.php");
    exit;
}

$kod_rabatowy = '';
$rabat = 0;
$suma_po_rabacie = $suma_brutto;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sprawdz_kod'])) {
    $kod_rabatowy = trim($_POST['kod_rabatowy'] ?? '');
    if (!empty($kod_rabatowy)) {
        $stmt = mysqli_prepare($conn, "SELECT * FROM kody_rabatowe WHERE kod = ? AND (data_waznosci >= CURDATE() OR data_waznosci IS NULL)");
        mysqli_stmt_bind_param($stmt, "s", $kod_rabatowy);
        mysqli_stmt_execute($stmt);
        $kod = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($kod && ($kod['jednorazowy'] == 0 || $kod['uzyty_przez'] === null) && $suma_brutto >= $kod['minimalna_wartosc']) {
            $rabat = ($kod['typ'] === 'procent') ? $suma_brutto * ($kod['wartosc'] / 100) : $kod['wartosc'];
            $suma_po_rabacie = $suma_brutto - $rabat;
            $_SESSION['kod_rabatowy_info'] = $kod;
            $_SESSION['rabat_obliczony'] = $rabat;
            $_SESSION['kod_uzyty'] = $kod_rabatowy;
        } else {
            $_SESSION['kod_blad'] = $kod ? "Nie spełniasz warunków kodu" : "Kod nieprawidłowy";
        }
    }
}

if (isset($_SESSION['kod_uzyty']) && !isset($_POST['sprawdz_kod'])) {
    $kod_rabatowy = $_SESSION['kod_uzyty'];
    $rabat = $_SESSION['rabat_obliczony'] ?? 0;
    $suma_po_rabacie = $suma_brutto - $rabat;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizuj'])) {
    if ($is_guest) {
        $uzytkownik = [
            'imie' => trim($_POST['imie'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'telefon' => trim($_POST['telefon'] ?? ''),
            'kod_pocztowy' => trim($_POST['kod_pocztowy'] ?? ''),
            'poczta' => trim($_POST['poczta'] ?? ''),
            'adres' => trim($_POST['adres'] ?? '')
        ];

        if (empty($uzytkownik['imie']) || empty($uzytkownik['email']) || empty($uzytkownik['kod_pocztowy']) || empty($uzytkownik['poczta']) || empty($uzytkownik['adres'])) {
            $_SESSION['komunikat'] = "Wypełnij wszystkie pola dostawy!";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
        $_SESSION['dane_gosc'] = $uzytkownik;
    }

    $cena_koncowa = $suma_po_rabacie > 0 ? $suma_po_rabacie : $suma_brutto;

    mysqli_begin_transaction($conn);

    try {
        $stmt = mysqli_prepare($conn, "INSERT INTO zamowienia (id_uzytkownika, cena_calkowita, kod_rabatowy, rabat, status) VALUES (?, ?, ?, ?, 'nowe')");
        $id_uzyt = $is_guest ? null : $id_uzytkownika;
        mysqli_stmt_bind_param($stmt, "isss", $id_uzyt, $cena_koncowa, $kod_rabatowy, $rabat);
        mysqli_stmt_execute($stmt);
        $id_zamowienia = mysqli_insert_id($conn);

        if ($is_guest) {
            $stmt = mysqli_prepare($conn, "INSERT INTO zamowienia_dane_goscie (id_zamowienia, imie, email, telefon, kod_pocztowy, poczta, adres) VALUES (?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "issssss", $id_zamowienia, $uzytkownik['imie'], $uzytkownik['email'], $uzytkownik['telefon'], $uzytkownik['kod_pocztowy'], $uzytkownik['poczta'], $uzytkownik['adres']);
            mysqli_stmt_execute($stmt);
        }

        foreach ($koszyk as $item) {
            $stmt = mysqli_prepare($conn, "INSERT INTO zamowienia_produkty (id_zamowienia, id_produktu, id_rozmiaru, ilosc, cena_jedn) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "iiiid", $id_zamowienia, $item['id_produktu'], $item['id_rozmiaru'], $item['ilosc'], $item['cena']);
            mysqli_stmt_execute($stmt);

            $stmt2 = mysqli_prepare($conn, "UPDATE produkty_rozmiary SET ilosc = ilosc - ? WHERE id_produktu = ? AND id_rozmiaru = ? AND ilosc >= ?");
            mysqli_stmt_bind_param($stmt2, "iiii", $item['ilosc'], $item['id_produktu'], $item['id_rozmiaru'], $item['ilosc']);
            mysqli_stmt_execute($stmt2);
            if (mysqli_stmt_affected_rows($stmt2) == 0) throw new Exception("Brak wystarczającej ilości towaru");
        }

        if (!empty($kod_rabatowy) && isset($_SESSION['kod_rabatowy_info']) && $_SESSION['kod_rabatowy_info']['jednorazowy'] == 1) {
            $stmt = mysqli_prepare($conn, "UPDATE kody_rabatowe SET uzyty_przez = ? WHERE kod = ?");
            mysqli_stmt_bind_param($stmt, "is", $id_uzyt, $kod_rabatowy);
            mysqli_stmt_execute($stmt);
        }

        if ($is_guest) {
            unset($_SESSION['koszyk'], $_SESSION['dane_gosc']);
        } else {
            mysqli_query($conn, "DELETE FROM koszyk WHERE id_uzytkownika = $id_uzytkownika");
        }

        unset($_SESSION['kod_uzyty'], $_SESSION['kod_rabatowy_info'], $_SESSION['rabat_obliczony']);

        mysqli_commit($conn);

        require_once 'PHPMailer/src/PHPMailer.php';
        require_once 'PHPMailer/src/SMTP.php';
        require_once 'PHPMailer/src/Exception.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'mail2.small.pl';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'bitwearshop@bitwear.dawsel.smallhost.pl';
            $mail->Password   = 'zaq1@WSX';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom('bitwearshop@bitwear.dawsel.smallhost.pl', 'BITWEAR');
            $mail->addAddress($is_guest ? $uzytkownik['email'] : $uzytkownik['email']);

            $mail->isHTML(true);
            $mail->Subject = "Potwierdzenie zamówienia #$id_zamowienia – BITWEAR";

            $produkty_html = '';
            foreach ($koszyk as $item) {
                $produkty_html .= "<tr>
                    <td style='padding:8px;border-bottom:1px solid #eee;'>{$item['nazwa']}</td>
                    <td style='padding:8px;border-bottom:1px solid #eee;text-align:center;'>{$item['rozmiar']}</td>
                    <td style='padding:8px;border-bottom:1px solid #eee;text-align:center;'>{$item['ilosc']}</td>
                    <td style='padding:8px;border-bottom:1px solid #eee;text-align:right;'>".number_format($item['cena']*$item['ilosc'],2,',',' ')." zł</td>
                </tr>";
            }

            $imie = $is_guest ? $uzytkownik['imie'] : ($uzytkownik['imie'] ?? '');
            $adres_info = $is_guest 
                ? $uzytkownik['adres']."<br>".$uzytkownik['kod_pocztowy']." ".$uzytkownik['poczta']
                : ($uzytkownik['adres'] ?? '')."<br>".($uzytkownik['kod_pocztowy'] ?? '')." ".($uzytkownik['poczta'] ?? '');

            $mail->Body = "
            <h2 style='color:#000;'>Dziękujemy za zamówienie #$id_zamowienia!</h2>
            <p>Cześć ".htmlspecialchars($imie).",</p>
            <p>Twoje zamówienie zostało przyjęte i jest w trakcie realizacji.</p>

            <table width='100%' style='margin:20px 0;font-size:14px;'>
                <tr style='background:#f8f8f8;'>
                    <th style='padding:12px;text-align:left;'>Produkt</th>
                    <th style='padding:12px;text-align:center;'>Rozmiar</th>
                    <th style='padding:12px;text-align:center;'>Ilość</th>
                    <th style='padding:12px;text-align:right;'>Cena</th>
                </tr>
                $produkty_html
            </table>

            <p style='font-size:16px;'>
                <strong>Razem do zapłaty: ".number_format($cena_koncowa,2,',',' ')." zł</strong>
                ".($rabat>0 ? "<br><small>Rabat: -".number_format($rabat,2,',',' ')." zł</small>" : '')."
            </p>

            <hr style='border:none;border-top:1px solid #eee;margin:30px 0;'>

            <h3>Dane do wysyłki</h3>
            <p>
                ".htmlspecialchars($imie)."<br>
                ".htmlspecialchars($adres_info)."
            </p>

            <p style='margin-top:30px;color:#555;'>
                W razie pytań odpisz na tę wiadomość.<br>
                Pozdrawiamy,<br>Zespół BITWEAR
            </p>
            ";

            $mail->send();
        } catch (Exception $e) {}

        $_SESSION['komunikat'] = "Zamówienie #$id_zamowienia złożone pomyślnie! Sprawdź e-mail z potwierdzeniem.";
        header("Location: zamowienia.php");
        exit;

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['komunikat'] = "Błąd: " . $e->getMessage();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizacja zamówienia – BITWEAR</title>
    <link rel="stylesheet" href="style/for_all.css">
    <link rel="stylesheet" href="style/zamowienie_finalizacja.css">
    <link rel="icon" href="zdjecia/logo.png">
</head>
<body>
    <nav>
        <a href="strona_glowna.php" id="logo-a">
            <img src="zdjecia/logo.png" id="logo-img" alt="logo.png">
            <h1 id="logo-h1">BITWEAR</h1>
        </a>
        <div id="category-links">
            <div class="dropdown"><p>ONA</p><div class="dropdown-content">
                <a href="produkty.php?plec=ONA&kategoria=Jeansy">Jeansy</a>
                <a href="produkty.php?plec=ONA&kategoria=Sneakersy">Sneakersy</a>
                <a href="produkty.php?plec=ONA&kategoria=Skarpety">Dodatki</a>
            </div></div>
            <div class="dropdown"><p>ON</p><div class="dropdown-content">
                <a href="produkty.php?plec=ON&kategoria=Jeansy">Jeansy</a>
                <a href="produkty.php?plec=ON&kategoria=Sneakersy">Sneakersy</a>
                <a href="produkty.php?plec=ON&kategoria=Skarpety">Dodatki</a>
            </div></div>
            <div class="dropdown"><p>DZIECKO</p><div class="dropdown-content">
                <a href="produkty.php?plec=DZIECKO&kategoria=Jeansy">Jeansy</a>
                <a href="produkty.php?plec=DZIECKO&kategoria=Sneakersy">Sneakersy</a>
                <a href="produkty.php?plec=DZIECKO&kategoria=Skarpety">Dodatki</a>
            </div></div>
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
        <a href="menu.php" id="hamburger-menu"><span></span><span></span><span></span></a>
    </nav>

    <main>
        <?php if (!empty($komunikat)): ?>
            <script>alert("<?= addslashes($komunikat) ?>");</script>
        <?php endif; ?>

        <h2 id="finalizacja-tytul">Finalizacja zamówienia</h2>

        <div class="finalizacja-container">
            <div class="podsumowanie-koszyka">
                <h3>Podsumowanie koszyka</h3>
                <div class="koszyk-lista">
                    <?php foreach ($koszyk as $item): ?>
                        <div class="koszyk-item">
                            <img src="<?= htmlspecialchars($item['sciezka'] ?? 'zdjecia/brak.jpg') ?>" alt="<?= htmlspecialchars($item['nazwa']) ?>">
                            <div class="koszyk-info">
                                <h4><?= htmlspecialchars($item['nazwa']) ?></h4>
                                <p><strong>Rozmiar:</strong> <?= htmlspecialchars($item['rozmiar']) ?></p>
                                <p><strong>Ilość:</strong> <?= $item['ilosc'] ?></p>
                                <p><strong>Cena:</strong> <?= number_format($item['cena'] * $item['ilosc'], 2, ',', ' ') ?> zł</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <p>Suma: <strong><?= number_format($suma_brutto, 2, ',', ' ') ?> zł</strong></p>

                <div class="kod-rabatowy-box">
                    <form method="POST" style="display:inline;">
                        <label><strong>Masz kod rabatowy?</strong></label><br>
                        <input type="text" name="kod_rabatowy" value="<?= htmlspecialchars($kod_rabatowy) ?>" placeholder="Wpisz kod" style="width:150px;padding:8px;">
                        <button type="submit" name="sprawdz_kod" class="submit-btn" style="padding:8px 12px;">Zatwierdź</button>
                    </form>

                    <?php if (isset($_SESSION['kod_blad'])): ?>
                        <p class="kod-error"><?= $_SESSION['kod_blad'] ?><?php unset($_SESSION['kod_blad']); ?></p>
                    <?php elseif ($rabat > 0): ?>
                        <p class="kod-success">Kod zastosowany! Rabat: -<?= number_format($rabat, 2, ',', ' ') ?> zł</p>
                    <?php endif; ?>

                    <?php if ($rabat > 0): ?>
                        <p class="rabat-info"><strong>Do zapłaty: <?= number_format($suma_po_rabacie, 2, ',', ' ') ?> zł</strong></p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($is_guest): ?>
            <div class="dane-dostawy dane-guest">
                <h3>Podaj dane do wysyłki</h3>
                <form method="POST" id="form-guest">
                    <input type="text" name="imie" class="input-guest" placeholder="Imię i nazwisko" value="<?= htmlspecialchars($_SESSION['dane_gosc']['imie'] ?? '') ?>" required><br>
                    <input type="email" name="email" class="input-guest" placeholder="E-mail" value="<?= htmlspecialchars($_SESSION['dane_gosc']['email'] ?? '') ?>" required><br>
                    <input type="text" name="telefon" class="input-guest" placeholder="Telefon (opcjonalnie)" value="<?= htmlspecialchars($_SESSION['dane_gosc']['telefon'] ?? '') ?>"><br>
                    <input type="text" name="kod_pocztowy" class="input-guest" placeholder="Kod pocztowy" value="<?= htmlspecialchars($_SESSION['dane_gosc']['kod_pocztowy'] ?? '') ?>" required><br>
                    <input type="text" name="poczta" class="input-guest" placeholder="Miejscowość" value="<?= htmlspecialchars($_SESSION['poczta'] ?? '') ?>" required><br>
                    <input type="text" name="adres" class="input-guest" id="last-input-guest" placeholder="Adres (ulica, numer domu/mieszkania)" value="<?= htmlspecialchars($_SESSION['dane_gosc']['adres'] ?? '') ?>" required><br>
            <?php else: ?>
            <div class="dane-dostawy">
                <h3>Dane dostawy</h3>
                <p><strong>Imię i nazwisko:</strong> <?= htmlspecialchars($uzytkownik['imie']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($uzytkownik['email']) ?></p>
                <p><strong>Telefon:</strong> <?= htmlspecialchars($uzytkownik['telefon'] ?? 'Brak') ?></p>
                <p><strong>Adres:</strong> <?= htmlspecialchars($uzytkownik['adres'] ?? 'Brak') ?>, <?= htmlspecialchars($uzytkownik['kod_pocztowy'] ?? '') ?> <?= htmlspecialchars($uzytkownik['poczta'] ?? '') ?></p>
                <center><a href="edytuj_dane.php" class="edytuj-link">Edytuj dane</a></center><br><br>
            <?php endif; ?>

                <div class="platnosc">
                    <h3>Metoda płatności</h3>
                    <label><input type="radio" name="metoda_platnosci" value="karta" required> Karta kredytowa/debetowa</label><br>
                    <label><input type="radio" name="metoda_platnosci" value="przelew"> Przelew bankowy</label><br>
                    <label><input type="radio" name="metoda_platnosci" value="blik"> BLIK</label><br><br>

                    <center>
                        <button type="submit" name="finalizuj" class="submit-btn">
                            Zapłać <?= number_format($suma_po_rabacie > 0 ? $suma_po_rabacie : $suma_brutto, 2, ',', ' ') ?> zł i złóż zamówienie
                        </button>
                    </center>
                </div>
            </form>
        </div>

        <center><a href="koszyk.php" class="powrot-link">Powrót do koszyka</a></center>
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
</body>
</html>