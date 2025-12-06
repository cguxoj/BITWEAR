<?php
    require_once("init.php");
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rejestracja - BITWEAR</title>
        <link rel="stylesheet" href="style/for_all.css">
        <link rel="stylesheet" href="style/login.css">
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
            <div id="login-div">
                <form method="POST" action="rejestracja_action.php">
                    <h3>Zarejestruj się</h3>
                    <div>
                        <label for="imie">Imię: </label>
                        <input type="text" name="imie" class="login-input" placeholder="Jan" required>
                    </div>
                    <div>
                        <label for="email">Email: </label>
                        <input type="text" name="email" class="login-input" placeholder="example@mail.com" required>
                    </div>
                    <div>
                        <label for="haslo">Hasło: </label>
                        <input type="password" name="haslo" class="login-input" placeholder="coolpass123" required>
                    </div>
                    <div>
                        <label for="telefon">Telefon: </label>
                        <input type="text" name="telefon" class="login-input" placeholder="123456789" min="9" max="9" required>
                    </div>
                    <div>
                        <label for="poczta">Poczta: </label>
                        <input type="text" name="poczta" class="login-input" placeholder="Warszawa" required>
                    </div>
                    <div>
                        <label for="kod_pocztowy">Kod pocztowy: </label>
                        <input type="text" name="kod_pocztowy" class="login-input" placeholder="00-010" required>
                    </div>
                    <div>
                        <label for="adres">Adres: </label>
                        <input type="text" name="adres" class="login-input" placeholder="ul. Stołeczna 19" required>
                    </div>
                    <br>
                    <button type="submit" name="register-submit" class="submit-btn">Utwórz konto</button><br>
                    <p id="register-p">Masz już konto?</p>
                    <a href="logowanie.php" id="register-a">Zaloguj się.</a>
                </form>
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