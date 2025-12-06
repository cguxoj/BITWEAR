<?php
require_once("init.php");

$komunikat = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $komunikat = "Podaj adres e-mail.";
    } else {
        $stmt = $conn->prepare("SELECT id_uzytkownika, imie FROM uzytkownicy WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $imie);
            $stmt->fetch();

            $token = bin2hex(random_bytes(32));
            $expires = date("Y-m-d H:i:s", time() + 3600);

            $stmt2 = $conn->prepare("UPDATE uzytkownicy SET token_resetu_hasla = ?, token_wygasa = ? WHERE id_uzytkownika = ?");
            $stmt2->bind_param("ssi", $token, $expires, $id);
            $stmt2->execute();

            $reset_link = "https://bitwear.dawsel.smallhost.pl/nowe_haslo.php?token=" . $token;

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
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'BITWEAR - Resetowanie hasła';
                $mail->Body    = "
                    <h2>Cześć $imie!</h2>
                    <p>Otrzymaliśmy prośbę o zresetowanie hasła do Twojego konta BITWEAR.</p>
                    <p>Kliknij poniższy link, aby ustawić nowe hasło (link ważny przez 1 godzinę):</p>
                    <p><a href='$reset_link' style='padding:10px 20px; background:#000; color:#fff; text-decoration:none; border-radius:5px;'>Ustaw nowe hasło</a></p>
                    <p>Jeśli to nie Ty prosiłeś o reset hasła – zignoruj tę wiadomość.</p>
                    <br>
                    <p>Pozdrawiamy,<br>Zespół BITWEAR</p>
                ";
                $mail->AltBody = "Cześć $imie! Kliknij link aby zresetować hasło: $reset_link (ważny 1h)";

                $mail->send();
                $komunikat = "Link do resetowania hasła został wysłany na Twój e-mail!";
            } catch (Exception $e) {
                $komunikat = "Błąd wysyłania e-maila. Spróbuj później.";
            }
        } else {
            $komunikat = "Jeśli podany e-mail istnieje w naszej bazie, otrzymasz link do resetowania hasła.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reset hasła – BITWEAR</title>
        <link rel="stylesheet" href="style/for_all.css">
        <link rel="stylesheet" href="style/login.css">
        <link rel="icon" href="zdjecia/logo.png">
      <style>
      form {
          text-align: center;
      }
      form a {
          text-decoration: none;
          color: lightblue;   
      }
      form a:hover {
          text-decoration: underline;
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
            <div id="login-div">
                <form method="POST">
                    <h3>Resetowanie hasła</h3>

                    <?php if (!empty($komunikat)): ?>
                        <p style="color: <?= strpos($komunikat, 'wysłany') !== false ? 'green' : 'red'; ?>; font-weight:bold;">
                            <?= htmlspecialchars($komunikat) ?>
                        </p>
                    <?php endif; ?>

                    <div>
                        <label>Podaj e-mail powiązany z kontem:</label>
                        <input type="email" name="email" class="login-input" required placeholder="example@mail.com">
                    </div><br>

                    <button type="submit" class="submit-btn">Wyślij link resetujący</button><br><br>
                    <a href="logowanie.php">← Powrót do logowania</a>
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