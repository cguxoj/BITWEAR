<?php
    require_once("init.php");

    if (!isset($_SESSION['user_id'])) {
        header("Location: logowanie.php");
    }

    $id = (int)$_SESSION['user_id'];

    $stmt = mysqli_prepare($conn, "SELECT imie, email, telefon, poczta, kod_pocztowy, adres FROM uzytkownicy WHERE id_uzytkownika = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $uzytkownik = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Moje konto – BITWEAR</title>
        <link rel="stylesheet" href="style/for_all.css">
        <link rel="stylesheet" href="style/konto.css">
        <style>
            @media (max-width: 1100px) {
                #dane-konta {
                    width: 75vw;
                }
                #dane-konta::before, #dane-konta::after {
                    height: min(85vw, 400px);
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
                <h2>Witaj, <?= htmlspecialchars($uzytkownik['imie']) ?>!</h2>

                <h3>Twoje dane</h3>
                <div class="konto-info">
                    <p><strong>E-mail:</strong> <?= htmlspecialchars($uzytkownik['email']) ?></p>
                    <p><strong>Telefon:</strong> <?= htmlspecialchars($uzytkownik['telefon'] ?? '–') ?></p>
                    <p><strong>Adres:</strong><br>
                        <?= nl2br(htmlspecialchars($uzytkownik['adres'] ?? 'Nie podano')) ?><br>
                        <?= htmlspecialchars($uzytkownik['kod_pocztowy'] ?? '') ?> <?= htmlspecialchars($uzytkownik['poczta'] ?? '') ?>
                    </p>
                </div>

                <div class="konto-linki">
                    <a href="zamowienia.php" class="konto-link">Moje zamówienia</a><br><br>
                    <a href="ulubione.php" class="konto-link">Ulubione</a><br><br>
                    <a href="edytuj_dane.php" class="konto-link">Edytuj dane konta</a><br><br>
                    <a href="wyloguj.php" class="wyloguj-sie-a">Wyloguj się</a>
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