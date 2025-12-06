<?php require_once("init.php"); ?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Menu - BITWEAR</title>
        <link rel="stylesheet" href="style/for_all.css">
        <link rel="stylesheet" href="style/main.css">
        <link rel="stylesheet" href="style/menu.css">
        <link rel="icon" href="zdjecia/logo.png">
    </head>
    <body>
        <a href="strona_glowna.php" class="back-to-home">← BITWEAR</a>

        <div class="mobile-menu-container">
            <a href="produkty.php?plec=ONA&kategoria=Jeansy" class="mobile-menu-link category">ONA</a>
            <a href="produkty.php?plec=ONA&kategoria=Jeansy" class="mobile-menu-link">Jeansy</a>
            <a href="produkty.php?plec=ONA&kategoria=Sneakersy" class="mobile-menu-link">Sneakersy</a>
            <a href="produkty.php?plec=ONA&kategoria=Skarpety" class="mobile-menu-link">Dodatki</a>

            <a href="produkty.php?plec=ON&kategoria=Jeansy" class="mobile-menu-link category">ON</a>
            <a href="produkty.php?plec=ON&kategoria=Jeansy" class="mobile-menu-link">Jeansy</a>
            <a href="produkty.php?plec=ON&kategoria=Sneakersy" class="mobile-menu-link">Sneakersy</a>
            <a href="produkty.php?plec=ON&kategoria=Skarpety" class="mobile-menu-link">Dodatki</a>

            <a href="produkty.php?plec=DZIECKO&kategoria=Jeansy" class="mobile-menu-link category">DZIECKO</a>
            <a href="produkty.php?plec=DZIECKO&kategoria=Jeansy" class="mobile-menu-link">Jeansy</a>
            <a href="produkty.php?plec=DZIECKO&kategoria=Sneakersy" class="mobile-menu-link">Sneakersy</a>
            <a href="produkty.php?plec=DZIECKO&kategoria=Skarpety" class="mobile-menu-link">Dodatki</a>

            <?php if (isset($_SESSION['rola']) && $_SESSION['rola'] === 'admin'): ?>
                <a href="admin_panel.php" class="mobile-menu-link category" style="color: rgb(255,130,130);">ADMIN PANEL</a>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="konto.php" class="mobile-menu-link">Moje konto</a>
                <a href="ulubione.php" class="mobile-menu-link">Ulubione</a>
            <?php else: ?>
                <a href="logowanie.php" class="mobile-menu-link">Zaloguj się</a>
            <?php endif; ?>

            <a href="koszyk.php" class="mobile-menu-link">Koszyk</a>
        </div>
    </body>
</html>