<?php
    require_once("init.php");
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Strona główna - BITWEAR</title>
        <link rel="stylesheet" href="style/for_all.css">
        <link rel="stylesheet" href="style/main.css">
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
            <div class="two-sides-div">
                <div class="left-side-div">
                    <img src="zdjecia/przystawka do banera.png" id="side-baner-img" alt="baner.png">
                    <p id="side-baner-p">
                        Wejdź w świat streetwearu, gdzie każdy detal ma znaczenie. <span class="bitwear-span">BITWEAR</span> to nowo powstały sklep, gdzie swoje miejsce znajdą młodzi, 
                        którzy chcą wyrazić siebie poprzez sneakersy i jeansy, które robią wrażenie. Tu każdy Twój krok staje się bitwą stylu, a moda przestaje być tylko 
                        ubiorem – staje się manifestem. Dołącz do społeczności, która nie boi się być sobą. <span class="bitwear-span">BITWEAR</span> – bijemy na ulicy, modą i charakterem.
                        <br><br>
                        Tak jak obiecaliśmy - minął miesiąc otwarcia, więc ruszamy z dropem jeansów! Z kodem <i>DENIMDROP</i> zakupisz wszystkie jeansy 15% taniej!
                    </p>
                </div>
                <div class="left-side-div">
                    <img src="zdjecia/baner.png" id="baner-img" alt="baner.png">
                </div>
            </div>
            <br><br>

            <center><p>PRZEGLĄDAJ <span class="bitwear-span">BITWEAR</span></p></center>
            <div class="produkty-grid">
                <?php
                    $kategorie = [1, 2, 3];

                    foreach ($kategorie as $id_kat) {
                        $sql = "
                            SELECT 
                                p.id_produktu, 
                                p.nazwa, 
                                p.cena,
                                z.sciezka
                            FROM produkty p
                            LEFT JOIN zdjecia_produktow z ON p.id_produktu = z.id_produktu AND z.typ = 'main'
                            WHERE p.id_kategorii = ?
                            ORDER BY p.id_produktu DESC
                            LIMIT 1
                        ";

                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, "i", $id_kat);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        $p = mysqli_fetch_assoc($result);

                        if (!$p) continue;

                        if (empty($p['sciezka'])) {
                            $fallback = mysqli_fetch_assoc(mysqli_query($conn, "
                                SELECT sciezka FROM zdjecia_produktow 
                                WHERE id_produktu = " . (int)$p['id_produktu'] . " 
                                LIMIT 1
                            "));
                            $zdjecie = $fallback['sciezka'] ?? 'zdjecia/brak.jpg';
                        } else {
                            $zdjecie = $p['sciezka'];
                        }

                        echo '
                        <div class="produkt">
                            <a href="produkt.php?id=' . $p['id_produktu'] . '">
                                <img src="' . htmlspecialchars($zdjecie) . '" alt="' . htmlspecialchars($p['nazwa']) . '">
                                <h4>' . htmlspecialchars($p['nazwa']) . '</h4>
                                <p class="cena">' . number_format($p['cena'], 2, ',', ' ') . ' zł</p>
                            </a>
                        </div>';
                    }
                ?>

            </div>

            <br>
            <div id="zobacz-wiecej-div">
                <a href="produkty.php">ZOBACZ WIĘCEJ</a>
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