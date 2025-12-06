<?php 
    require_once("init.php");

    if (!isset($_SESSION['user_id'])) {
        header("Location: logowanie.php");
        exit;
    }
    $id_uzytkownika = (int)$_SESSION['user_id'];

    if (isset($_GET['usun']) && is_numeric($_GET['usun'])) {
        $id_produktu_do_usuniecia = (int)$_GET['usun'];

        $stmt = mysqli_prepare($conn, "DELETE FROM ulubione WHERE id_uzytkownika = ? AND id_produktu = ?");
        mysqli_stmt_bind_param($stmt, "ii", $id_uzytkownika, $id_produktu_do_usuniecia);
        mysqli_stmt_execute($stmt);

        $_SESSION['komunikat'] = "Usunięto z ulubionych!";
        header("Location: ulubione.php");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ulubione – BITWEAR</title>
        <link rel="stylesheet" href="style/for_all.css">
        <link rel="stylesheet" href="style/produkty.css">
        <link rel="stylesheet" href="style/ulubione.css">
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
            <h2>Ulubione produkty</h2>

            <?php if (isset($_SESSION['komunikat'])): ?>
                <p class="komunikat"><?=htmlspecialchars($_SESSION['komunikat'])?></p>
                <?php unset($_SESSION['komunikat']); ?>
            <?php endif; ?>

            <?php
                $sql = "SELECT p.id_produktu, p.nazwa, p.cena, 
                            COALESCE(zp.sciezka, 'zdjecia/brak.jpg') AS sciezka
                        FROM ulubione u
                        JOIN produkty p ON u.id_produktu = p.id_produktu
                        LEFT JOIN zdjecia_produktow zp 
                            ON p.id_produktu = zp.id_produktu 
                            AND zp.id_zdjecia = (SELECT MIN(id_zdjecia) 
                                                FROM zdjecia_produktow zp2 
                                                WHERE zp2.id_produktu = p.id_produktu)
                        WHERE u.id_uzytkownika = ?
                        ORDER BY p.nazwa";

                $stmt = mysqli_prepare($conn, $sql);
                if ($stmt === false) {
                    die("Błąd przygotowania zapytania: " . mysqli_error($conn));
                }

                mysqli_stmt_bind_param($stmt, "i", $id_uzytkownika);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
            ?>

            <div class="produkty-grid">
                <?php if (mysqli_num_rows($result) == 0): ?>
                    <p style="grid-column: 1 / -1; text-align:center; margin:40px 0;">
                        Nie masz jeszcze żadnych ulubionych produktów.<br><br><br>
                        <a href="strona_glowna.php" id="przejdz-do-sklepu">Przejdź do sklepu →</a>
                    </p>
                <?php endif; ?>

                <?php while ($prod = mysqli_fetch_assoc($result)): ?>
                    <div class="produkt">
                        <a href="produkt.php?id=<?= $prod['id_produktu'] ?>">
                            <img src="<?= htmlspecialchars($prod['sciezka']) ?>" 
                                alt="<?= htmlspecialchars($prod['nazwa']) ?>">
                            <h4><?= htmlspecialchars($prod['nazwa']) ?></h4>
                            <p class="cena"><?= number_format($prod['cena'], 2, ',', ' ') ?> zł</p>
                        </a>

                        <a href="ulubione.php?usun=<?= $prod['id_produktu'] ?>" 
                        class="usun-btn"
                        onclick="return confirm('Na pewno usunąć „<?= addslashes(htmlspecialchars($prod['nazwa'])) ?>” z ulubionych?')">
                            Usuń z ulubionych
                        </a>
                    </div>
                <?php endwhile; ?>
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