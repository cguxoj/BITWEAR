<?php 
    require_once("init.php");

    if (!isset($_SESSION['user_id'])) {
        header("Location: logowanie.php");
        exit;
    }
    $id_uzytkownika = (int)$_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Moje zamówienia – BITWEAR</title>
        <link rel="stylesheet" href="style/for_all.css">
        <link rel="stylesheet" href="style/konto.css">
    </head>
    <style>
        .zamowienie {
            margin-top: 75px;
        }
    </style>
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
            <h2>Twoje zamówienia</h2>

            <?php
            $stmt = mysqli_prepare($conn, "
                SELECT z.id_zamowienia, z.data_zamowienia, z.status, z.cena_calkowita
                FROM zamowienia z
                WHERE z.id_uzytkownika = ?
                ORDER BY z.data_zamowienia DESC
            ");
            mysqli_stmt_bind_param($stmt, "i", $id_uzytkownika);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 0): ?>
                <p>Nie masz jeszcze żadnych zamówień.</p>
            <?php else: ?>
                <div class="zamowienia-lista">
                    <?php while ($zam = mysqli_fetch_assoc($result)): ?>
                        <div class="zamowienie">
                            <h3>Zamówienie #<?= $zam['id_zamowienia'] ?></h3>
                            <p><strong>Data:</strong> <?= date("d.m.Y H:i", strtotime($zam['data_zamowienia'])) ?></p>
                            <p><strong>Status:</strong> 
                                <span style="color: <?= $zam['status']=='zakończone'?'green':($zam['status']=='wysłane'?'blue':'orange') ?>;">
                                    <?= ucfirst(str_replace('_', ' ', $zam['status'])) ?>
                                </span>
                            </p>
                            <p><strong>Wartość:</strong> <?= number_format($zam['cena_calkowita'], 2, ',', ' ') ?> zł</p>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
            
            <br>
            <center><a href="konto.php" id="powrot-do-konta">← Powrót do konta</a></center>
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