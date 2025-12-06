<?php 
    require_once("init.php"); 
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Koszyk – BITWEAR</title>
        <link rel="stylesheet" href="style/for_all.css">
        <link rel="stylesheet" href="style/produkty.css">
        <link rel="stylesheet" href="style/koszyk.css">
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
            <h2 style="text-align:center; margin:40px 0;">Twój koszyk</h2>

            <?php if (isset($_SESSION['komunikat'])): ?>
                <p class="komunikat"><?= htmlspecialchars($_SESSION['komunikat']) ?></p>
                <?php unset($_SESSION['komunikat']); ?>
            <?php endif; ?>

            <?php
                $koszyk = [];
                $suma = 0;

                if (isset($_SESSION['user_id'])) {
                    $id_uzytkownika = (int)$_SESSION['user_id'];
                    $sql = "SELECT 
                                p.id_produktu, p.nazwa, p.cena,
                                r.nazwa AS rozmiar,
                                k.id_rozmiaru,
                                k.ilosc,
                                COALESCE(zp.sciezka, 'zdjecia/brak.jpg') AS zdjecie
                            FROM koszyk k
                            JOIN produkty p ON k.id_produktu = p.id_produktu
                            JOIN rozmiary r ON k.id_rozmiaru = r.id_rozmiaru
                            LEFT JOIN zdjecia_produktow zp ON p.id_produktu = zp.id_produktu 
                                AND zp.id_zdjecia = (SELECT MIN(id_zdjecia) FROM zdjecia_produktow WHERE id_produktu = p.id_produktu)
                            WHERE k.id_uzytkownika = ?
                            ORDER BY p.nazwa";

                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "i", $id_uzytkownika);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    while ($row = mysqli_fetch_assoc($result)) {
                        $koszyk[] = $row;
                        $suma += $row['cena'] * $row['ilosc'];
                    }
                } else {
                    if (!empty($_SESSION['koszyk'])) {
                        foreach ($_SESSION['koszyk'] as $klucz => $item) {
                            list($id_produktu, $id_rozmiaru) = explode('|', $klucz);
                            $id_produktu = (int)$id_produktu;
                            $id_rozmiaru = (int)$id_rozmiaru;

                            $sql = "SELECT 
                                    p.id_produktu, 
                                    p.nazwa, 
                                    p.cena,
                                    r.nazwa AS rozmiar,
                                    COALESCE(zp.sciezka, 'zdjecia/brak.jpg') AS zdjecie
                                FROM produkty p
                                JOIN rozmiary r ON r.id_rozmiaru = ?
                                LEFT JOIN zdjecia_produktow zp ON p.id_produktu = zp.id_produktu 
                                    AND zp.id_zdjecia = (SELECT MIN(id_zdjecia) FROM zdjecia_produktow WHERE id_produktu = p.id_produktu)
                                WHERE p.id_produktu = ?";


                            $stmt = mysqli_prepare($conn, $sql);
                            if (!$stmt) {
                                die("SQL error: " . mysqli_error($conn));
                            }
                            mysqli_stmt_bind_param($stmt, "ii", $id_rozmiaru, $id_produktu);
                            mysqli_stmt_execute($stmt);
                            $res = mysqli_stmt_get_result($stmt);
                            if ($row = mysqli_fetch_assoc($res)) {
                                $row['ilosc'] = $item['ilosc'];
                                $row['id_rozmiaru'] = $id_rozmiaru;
                                $koszyk[] = $row;
                                $suma += $row['cena'] * $item['ilosc'];
                            }
                        }
                    }
                }
            ?>

            <?php if (empty($koszyk)): ?>
                <p style="text-align:center; font-size:1.2em; margin:60px 0;">
                    Twój koszyk jest pusty.<br><br>
                    <a href="strona_glowna.php" id="przejdz-do-sklepu">Przejdź do sklepu →</a>
                </p>
            <?php else: ?>
                <div class="koszyk-grid">
                    <?php foreach ($koszyk as $item): ?>
                        <div class="produkt-koszyk">
                            <a href="produkt.php?id=<?= $item['id_produktu'] ?>">
                                <img src="<?= htmlspecialchars($item['zdjecie']) ?>" 
                                    alt="<?= htmlspecialchars($item['nazwa']) ?>">
                                <h4><?= htmlspecialchars($item['nazwa']) ?></h4>
                            </a>
                            <div class="info-koszyk">
                                <p><strong>Rozmiar:</strong> <?= htmlspecialchars($item['rozmiar']) ?></p>

                                <form method="POST" action="zmien_ilosc.php" class="ilosc-form">
                                    <input type="hidden" name="id_produktu" value="<?= $item['id_produktu'] ?>">
                                    <input type="hidden" name="id_rozmiaru" value="<?= $item['id_rozmiaru'] ?? 0 ?>">
                                    
                                    <button type="submit" name="akcja" value="minus" class="ilosc-btn">−</button>

                                    <span class="ilosc-value"><?= $item['ilosc'] ?></span>

                                    <button type="submit" name="akcja" value="plus" class="ilosc-btn">+</button>
                                </form>

                                <p><strong>Cena:</strong> <?= number_format($item['cena'] * $item['ilosc'], 2, ',', ' ') ?> zł</p>
                            </div>
                            <a href="usun_z_koszyka.php?id=<?= $item['id_produktu'] ?>&rozmiar=<?= urlencode($item['rozmiar']) ?>"
                            class="usun-btn"
                            onclick="return confirm('Usunąć ten produkt z koszyka?')">
                                Usuń
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="podsumowanie">
                    <p>Razem do zapłaty: <strong><?= number_format($suma, 2, ',', ' ') ?> zł</strong></p>
                    <button onclick="location.href='zamowienie_finalizacja.php'">Przejdź do płatności</button>
                </div>
            <?php endif; ?>
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