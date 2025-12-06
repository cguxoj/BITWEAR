<?php
    require_once("init.php");

    if (!isset($_SESSION['user_id'])) {
        header("Location: logowanie.php");
        exit;
    }

    $id = (int)$_SESSION['user_id'];
    $komunikat = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['zapisz_dane'])) {
            $imie = trim($_POST['imie'] ?? '');
            $telefon = trim($_POST['telefon'] ?? '');
            $adres = trim($_POST['adres'] ?? '');
            $kod_pocztowy = trim($_POST['kod_pocztowy'] ?? '');
            $poczta = trim($_POST['poczta'] ?? '');

            if (strlen($imie) < 2) {
                $komunikat = "Imię musi mieć co najmniej 2 znaki.";
            } elseif ($kod_pocztowy !== '' && !preg_match("/^[0-9]{2}-[0-9]{3}$/", $kod_pocztowy)) {
                $komunikat = "Kod pocztowy musi być w formacie XX-XXX";
            } else {
                $stmt = mysqli_prepare($conn, "UPDATE uzytkownicy SET imie=?, telefon=?, adres=?, kod_pocztowy=?, poczta=? WHERE id_uzytkownika=?");
                mysqli_stmt_bind_param($stmt, "sssssi", $imie, $telefon, $adres, $kod_pocztowy, $poczta, $id);
                mysqli_stmt_execute($stmt);
                $komunikat = "Dane zaktualizowane pomyślnie!";
                $_SESSION['komunikat'] = $komunikat;
                header("Location: edytuj_dane.php");
                exit;
            }
        }

        if (isset($_POST['zapisz_haslo'])) {
            $obecne_haslo = $_POST['obecne_haslo'] ?? '';
            $nowe_haslo = $_POST['nowe_haslo'] ?? '';
            $powtorz_haslo = $_POST['powtorz_haslo'] ?? '';

            $stmt = mysqli_prepare($conn, "SELECT haslo_hash FROM uzytkownicy WHERE id_uzytkownika = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
            $hash_z_bazy = $user['haslo_hash'];

            if (empty($obecne_haslo) || empty($nowe_haslo) || empty($powtorz_haslo)) {
                $komunikat = "Wszystkie pola hasła są wymagane.";
            } elseif (!password_verify($obecne_haslo, $hash_z_bazy)) {
                $komunikat = "Obecne hasło jest nieprawidłowe.";
            } elseif ($nowe_haslo !== $powtorz_haslo) {
                $komunikat = "Nowe hasła nie są takie same.";
            } elseif (strlen($nowe_haslo) < 6) {
                $komunikat = "Nowe hasło musi mieć co najmniej 6 znaków.";
            } else {
                $nowy_hash = password_hash($nowe_haslo, PASSWORD_DEFAULT);
                $stmt = mysqli_prepare($conn, "UPDATE uzytkownicy SET haslo_hash = ? WHERE id_uzytkownika = ?");
                mysqli_stmt_bind_param($stmt, "si", $nowy_hash, $id);
                mysqli_stmt_execute($stmt);

                $komunikat = "Hasło zostało zmienione pomyślnie!";
                $_SESSION['komunikat'] = $komunikat;
                header("Location: edytuj_dane.php");
                exit;
            }
        }
    }

    $stmt = mysqli_prepare($conn, "SELECT imie, telefon, adres, kod_pocztowy, poczta, email FROM uzytkownicy WHERE id_uzytkownika=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $uzytkownik = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edytuj dane – BITWEAR</title>
        <link rel="stylesheet" href="style/for_all.css">
        <link rel="stylesheet" href="style/konto.css">
        <style>
            #dane-konta {
                height: 52vw;
            }
            @media (max-width: 1400px) {
                #dane-konta {
                    margin-bottom: 250px;
                    width: 80vw;
                }
                #dane-konta::before, #dane-konta::after {
                    height: max(75vw, 900px);
                    width: 100%;
                }
            }
            @media (max-width: 900px) {
                footer {
                    margin-top: 400px;
                }
            }
        </style>
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
            <div id="dane-konta">
                <h2>Edytuj dane konta</h2>

                <?php if (!empty($_SESSION['komunikat'] ?? '')): ?>
                    <p style="color:green; font-weight:bold; margin-bottom:15px;">
                        <?= htmlspecialchars($_SESSION['komunikat']) ?>
                    </p>
                    <?php unset($_SESSION['komunikat']); ?>
                <?php endif; ?>

                <?php if (!empty($komunikat) && !str_contains($komunikat, 'pomyślnie')): ?>
                    <p style="color:red; margin-bottom:15px;"><?= htmlspecialchars($komunikat) ?></p>
                <?php endif; ?>

                <form method="POST" class="edytuj-form">
                    <div>
                        <label>Imię i nazwisko:</label>
                        <input type="text" name="imie" value="<?= htmlspecialchars($uzytkownik['imie']) ?>" required>
                    </div>

                    <div>
                        <label>E-mail (nie można zmienić):</label>
                        <input type="email" class="input-disabled" value="<?= htmlspecialchars($uzytkownik['email']) ?>" disabled>
                    </div>

                    <div>
                        <label>Telefon:</label>
                        <input type="text" name="telefon" value="<?= htmlspecialchars($uzytkownik['telefon'] ?? '') ?>">
                    </div>

                    <div>
                        <label>Adres:</label>
                        <textarea name="adres" rows="3"><?= htmlspecialchars($uzytkownik['adres'] ?? '') ?></textarea>
                    </div>

                    <div>
                        <label>Kod pocztowy:</label>
                        <input type="text" name="kod_pocztowy" placeholder="12-345" value="<?= htmlspecialchars($uzytkownik['kod_pocztowy'] ?? '') ?>">
                    </div>

                    <div>
                        <label>Miejscowość:</label>
                        <input type="text" name="poczta" value="<?= htmlspecialchars($uzytkownik['poczta'] ?? '') ?>">
                    </div>

                    <div id="button-div">
                        <button type="submit" name="zapisz_dane" class="submit-btn">Zapisz zmiany danych</button>
                        <a href="konto.php" style="margin-left:20px;">Anuluj</a>
                    </div>
                </form>

                <h3 style="margin-top:30px;">Zmień hasło</h3>
                <form method="POST" class="edytuj-form">
                    <div>
                        <label>Obecne hasło:</label>
                        <input type="password" name="obecne_haslo" required>
                    </div>

                    <div>
                        <label>Nowe hasło:</label>
                        <input type="password" name="nowe_haslo" required minlength="6">
                    </div>

                    <div>
                        <label>Powtórz nowe hasło:</label>
                        <input type="password" name="powtorz_haslo" required minlength="6">
                    </div>

                    <div id="button-div">
                        <button type="submit" name="zapisz_haslo" class="submit-btn">Zmień hasło</button>
                    </div>
                </form>

                <hr style="margin: 40px 0; border-color: #444;">

                <div style="margin-top:30px;">
                    <a href="konto.php" id="powrot-do-konta">← Powrót do konta</a>
                </div>
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
    </body>
</html>