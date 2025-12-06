<?php
require_once("init.php");

$token = $_GET['token'] ?? '';
$komunikat = "";
$sukces = false;

if (empty($token)) {
    header("Location: logowanie.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $haslo1 = $_POST['haslo1'] ?? '';
    $haslo2 = $_POST['haslo2'] ?? '';

    if ($haslo1 !== $haslo2) {
        $komunikat = "Hasła nie są takie same.";
    } elseif (strlen($haslo1) < 6) {
        $komunikat = "Hasło musi mieć co najmniej 6 znaków.";
    } else {
        $stmt = $conn->prepare("SELECT id_uzytkownika FROM uzytkownicy WHERE token_resetu_hasla = ? AND token_wygasa > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id);
            $stmt->fetch();

            $nowy_hash = password_hash($haslo1, PASSWORD_DEFAULT);

            $stmt2 = $conn->prepare("UPDATE uzytkownicy SET haslo_hash = ?, token_resetu_hasla = NULL, token_wygasa = NULL WHERE id_uzytkownika = ?");
            $stmt2->bind_param("si", $nowy_hash, $id);
            $stmt2->execute();

            $komunikat = "Hasło zostało zmienione! Możesz się teraz zalogować.";
            $sukces = true;
        } else {
            $komunikat = "Link jest nieprawidłowy lub wygasł.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nowe hasło – BITWEAR</title>
        <link rel="stylesheet" href="style/for_all.css">
        <link rel="stylesheet" href="style/login.css">
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
                <form method="POST">
                    <h3>Ustaw nowe hasło</h3>

                    <?php if (!empty($komunikat)): ?>
                        <p style="color: <?= $sukces ? 'green' : 'red' ?>; font-weight:bold;">
                            <?= htmlspecialchars($komunikat) ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!$sukces): ?>
                    <div>
                        <label>Nowe hasło:</label>
                        <input type="password" name="haslo1" required minlength="6" class="login-input">
                    </div>
                    <div>
                        <label>Powtórz hasło:</label>
                        <input type="password" name="haslo2" required minlength="6" class="login-input">
                    </div><br>
                    <button type="submit" class="submit-btn">Zmień hasło</button>
                    <?php else: ?>
                    <a href="logowanie.php" class="submit-btn" style="display:inline-block; text-align:center; padding:12px 30px; text-decoration:none;">Przejdź do logowania</a>
                    <?php endif; ?>
                </form>
            </div>
        </main>
        
        <footer>
            <hr>
            <center>
                <a href="strona_glowna.php"><img src="zdjecia/logo.png" id="footer-logo-img" alt="logo"></a>
                <p>Zawartość tej strony jest chroniona prawem autorskim i należy do spółki <b>BitComp</b>.</p>
                <div id="footer-logo-div">
                    <a href="https://www.youtube.com"><img src="zdjecia/yt logo.png" class="footer-social-media" alt="youtube"></a>
                    <a href="https://instagram.com"><img src="zdjecia/ig logo.png" class="footer-social-media" alt="instagram"></a>
                </div>
            </center>
        </footer>
    </body>
</html>