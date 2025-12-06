<?php
    require_once("init.php");

    if (!isset($_SESSION['user_id']) || $_SESSION['rola'] !== 'admin') {
        header("Location: admin_panel.php");
        exit;
    }

    if (isset($_POST['edytuj_klienta'])) {
        $id = (int)$_POST['id'];
        $imie = trim($_POST['imie']);
        $email = trim($_POST['email']);
        $telefon = trim($_POST['telefon']);
        $poczta = trim($_POST['poczta']);
        $kod_pocztowy = trim($_POST['kod_pocztowy']);
        $adres = trim($_POST['adres']);

        $stmt = mysqli_prepare($conn, "UPDATE uzytkownicy SET imie = ?, email = ?, telefon = ?, poczta = ?, kod_pocztowy = ?, adres = ? WHERE id_uzytkownika = ? AND rola = 'klient'");
        mysqli_stmt_bind_param($stmt, "ssssssi", $imie, $email, $telefon, $poczta, $kod_pocztowy, $adres, $id);
        mysqli_stmt_execute($stmt);

        header("Location: admin_klienci.php");
        exit;
    }

    if (isset($_GET['usun'])) {
        $id = (int)$_GET['usun'];
        mysqli_query($conn, "DELETE FROM uzytkownicy WHERE id_uzytkownika = $id AND rola = 'klient'");
        header("Location: admin_klienci.php");
        exit;
    }

    $wynik = mysqli_query($conn, "SELECT * FROM uzytkownicy WHERE rola = 'klient'");

    $klient_do_edycji = null;
    if (isset($_GET['edytuj'])) {
        $id = (int)$_GET['edytuj'];
        $stmt = mysqli_prepare($conn, "SELECT * FROM uzytkownicy WHERE id_uzytkownika = ? AND rola = 'klient'");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $klient_do_edycji = mysqli_fetch_assoc($result);
    }
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Zarządzanie Klientami – BITWEAR</title>
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
            <h3>Klienci</h3>
            <table>
                <tr><th>ID</th><th>Imię</th><th>Email</th><th>Telefon</th><th>Akcje</th></tr>
                <?php while ($klient = mysqli_fetch_assoc($wynik)): ?>
                    <tr>
                        <td><?= $klient['id_uzytkownika'] ?></td>
                        <td><?= htmlspecialchars($klient['imie']) ?></td>
                        <td><?= htmlspecialchars($klient['email']) ?></td>
                        <td><?= htmlspecialchars($klient['telefon'] ?? '-') ?></td>
                        <td>
                            <a href="?edytuj=<?= $klient['id_uzytkownika'] ?>" class="edytuj-a">Edytuj</a> |
                            <a href="?usun=<?= $klient['id_uzytkownika'] ?>" onclick="return confirm('Usunąć?')" class="usun-a">Usuń</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>

            <?php if ($klient_do_edycji): ?>
                <div class="form-div">
                    <h4>Edytuj klienta #<?= $klient_do_edycji['id_uzytkownika'] ?></h4>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $klient_do_edycji['id_uzytkownika'] ?>">
                        <label>Imię: <input type="text" name="imie" value="<?= htmlspecialchars($klient_do_edycji['imie']) ?>" required></label><br>
                        <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($klient_do_edycji['email']) ?>" required></label><br>
                        <label>Telefon: <input type="text" name="telefon" value="<?= htmlspecialchars($klient_do_edycji['telefon'] ?? '') ?>"></label><br>
                        <label>Poczta: <input type="text" name="poczta" value="<?= htmlspecialchars($klient_do_edycji['poczta'] ?? '') ?>"></label><br>
                        <label>Kod pocztowy: <input type="text" name="kod_pocztowy" value="<?= htmlspecialchars($klient_do_edycji['kod_pocztowy'] ?? '') ?>"></label><br>
                        <label>Adres: <textarea name="adres"><?= htmlspecialchars($klient_do_edycji['adres'] ?? '') ?></textarea></label><br>
                        <button type="submit" name="edytuj_klienta" class="submit-btn">Zapisz zmiany</button>
                    </form>
                </div>
            <?php endif; ?>

            <div class="powrot-div">
                <a href="admin_panel.php" class="powrot-a">← Powrót</a>
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