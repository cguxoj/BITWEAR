<?php
    require_once("init.php");

    $komunikat = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login-submit'])) {
        $email = trim($_POST['email']);
        $haslo = $_POST['password'];

        $stmt = $conn->prepare("SELECT id_uzytkownika, imie, haslo_hash, rola FROM uzytkownicy WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $imie, $hash, $rola);
        $stmt->fetch();

        if ($stmt->num_rows === 1 && password_verify($haslo, $hash)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['imie'] = $imie;
            $_SESSION['email'] = $email;
            $_SESSION['rola'] = $rola;

            header("Location: konto.php");
            exit;
        } else {
            $komunikat = "Niepoprawny email lub hasło!";
        }
        $stmt->close();
    }
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Logowanie - BITWEAR</title>
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
                <form method="POST" action="">
                    <h3>Zaloguj się</h3>

                    <?php if (!empty($komunikat)): ?>
                        <script>
                            alert("<?= addslashes($komunikat) ?>");
                        </script>
                    <?php endif; ?>

                    <div>
                        <label for="email">Email: </label>
                        <input type="text" name="email" class="login-input" placeholder="example@mail.com">
                    </div>
                    <div>
                        <label for="password">Hasło: </label>
                        <input type="password" name="password" class="login-input" placeholder="coolpass123">
                    </div><br>
                    <button type="submit" name="login-submit" class="submit-btn">Potwierdź</button><br>
                    <p id="register-p">Nie masz konta? <a href="rejestracja.php" id="register-a">Zarejestruj się.</a></p>
                    <p id="register-p">Nie pamiętasz hasła? <a href="resetuj_haslo.php" id="reset-a">Zresetuj hasło</a></p>
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