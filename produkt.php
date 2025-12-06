<?php
    require_once("init.php");
    ini_set('display_errors', 1); error_reporting(E_ALL);

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        die('<p style="color:white;text-align:center;">Brak ID produktu.</p>');
    }
    $id_produktu = (int)$_GET['id'];
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Szczegóły produktu - BITWEAR</title>
        <link rel="stylesheet" href="style/for_all.css">
        <link rel="stylesheet" href="style/produkt.css">
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
            <?php
                $sql = "
                    SELECT 
                        p.nazwa,
                        p.opis,
                        p.cena,
                        p.plec,
                        m.nazwa AS material
                    FROM produkty p
                    JOIN materialy m ON p.id_materialu = m.id_materialu
                    WHERE p.id_produktu = ?
                ";

                $stmt = mysqli_prepare($conn, $sql);
                if (!$stmt) {
                    die("Błąd zapytania: " . mysqli_error($conn));
                }
                mysqli_stmt_bind_param($stmt, "i", $id_produktu);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) === 0) {
                    echo '<p style="color:white;text-align:center;">Produkt nie został znaleziony.</p>';
                    exit;
                }
                $produkt = mysqli_fetch_assoc($result);

                $zdjecia = [];
                $sql_zdj = "SELECT sciezka FROM zdjecia_produktow WHERE id_produktu = ? ORDER BY id_zdjecia ASC";
                if ($stmt_zdj = mysqli_prepare($conn, $sql_zdj)) {
                    mysqli_stmt_bind_param($stmt_zdj, "i", $id_produktu);
                    mysqli_stmt_execute($stmt_zdj);
                    $res = mysqli_stmt_get_result($stmt_zdj);
                    while ($row = mysqli_fetch_assoc($res)) {
                        $zdjecia[] = $row['sciezka'];
                    }
                }
                if (empty($zdjecia)) $zdjecia[] = 'zdjecia/brak.jpg';

                $atrybuty = [];
                $sql_atr = "SELECT nazwa_atrybutu, wartosc_atrybutu FROM atrybuty_produktow WHERE id_produktu = ?";
                if ($stmt_atr = mysqli_prepare($conn, $sql_atr)) {
                    mysqli_stmt_bind_param($stmt_atr, "i", $id_produktu);
                    mysqli_stmt_execute($stmt_atr);
                    $res = mysqli_stmt_get_result($stmt_atr);
                    while ($row = mysqli_fetch_assoc($res)) {
                        $atrybuty[$row['nazwa_atrybutu']] = $row['wartosc_atrybutu'];
                    }
                }

                $rozmiary = [];
                $sql_roz = "
                    SELECT r.nazwa AS rozmiar
                    FROM rozmiary r
                    JOIN produkty_rozmiary pr ON r.id_rozmiaru = pr.id_rozmiaru
                    WHERE pr.id_produktu = ? AND pr.ilosc > 0
                ";
                if ($stmt_roz = mysqli_prepare($conn, $sql_roz)) {
                    mysqli_stmt_bind_param($stmt_roz, "i", $id_produktu);
                    mysqli_stmt_execute($stmt_roz);
                    $res = mysqli_stmt_get_result($stmt_roz);
                    while ($row = mysqli_fetch_assoc($res)) {
                        $rozmiary[] = $row['rozmiar'];
                    }
                }

                $dostepnosc = 0;
                $sql_sum = "SELECT COALESCE(SUM(ilosc),0) FROM produkty_rozmiary WHERE id_produktu = ?";
                if ($stmt_sum = mysqli_prepare($conn, $sql_sum)) {
                    mysqli_stmt_bind_param($stmt_sum, "i", $id_produktu);
                    mysqli_stmt_execute($stmt_sum);
                    mysqli_stmt_bind_result($stmt_sum, $dostepnosc);
                    mysqli_stmt_fetch($stmt_sum);
                    mysqli_stmt_close($stmt_sum);
                }
            ?>

            <div id="produkt-details">
                <div class="zdjecia">
                    <?php foreach ($zdjecia as $zdj): ?>
                        <img src="<?= htmlspecialchars($zdj) ?>" alt="<?= htmlspecialchars($produkt['nazwa']) ?>">
                    <?php endforeach; ?>
                </div>

                <div class="info">
                    <h2><?= htmlspecialchars($produkt['nazwa']) ?></h2>
                    <p class="cena"><?= number_format($produkt['cena'], 2, ',', ' ') ?> zł</p>

                    <p><strong>Materiał:</strong> <?= htmlspecialchars($produkt['material']) ?></p>
                    <?php if (!empty($atrybuty['kolor'])): ?>
                        <p><strong>Kolor:</strong> <?= htmlspecialchars($atrybuty['kolor']) ?></p>
                    <?php endif; ?>
                    <p><strong>Dostępność:</strong> <?= $dostepnosc ?> szt.</p>
                    <p><strong>Płeć:</strong> <?= ucfirst(htmlspecialchars($produkt['plec'])) ?></p>

                    <?php if (!empty($produkt['opis'])): ?>
                        <p><strong>Opis:</strong><br><?= nl2br(htmlspecialchars($produkt['opis'])) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($rozmiary)): ?>
                        <form method="POST" action="add_to_cart.php">
                        <input type="hidden" name="id_produktu" value="<?= $id_produktu ?>">

                        <label for="rozmiar"><strong>Rozmiar:</strong></label><br>
                        <select name="rozmiar" id="rozmiar" required>
                            <?php foreach ($rozmiary as $roz): ?>
                                <option value="<?= htmlspecialchars($roz) ?>"><?= htmlspecialchars($roz) ?></option>
                            <?php endforeach; ?>
                        </select><br><br>

                        <label for="ilosc"><strong>Ilość:</strong></label><br>
                        <input 
                            type="number" 
                            id="ilosc" 
                            name="ilosc" 
                            value="1" 
                            min="1" 
                            max="<?= $dostepnosc ?>" 
                            required
                        >
                        <br><br>

                        <button type="submit" class="submit-btn">Dodaj do koszyka</button>
                    </form>
                    <?php else: ?>
                        <p style="color:#ff6666;">Produkt niedostępny</p>
                    <?php endif; ?>
                    <br>

                    <form method="POST" action="add_to_fav.php" style="display:inline;">
                        <input type="hidden" name="id_produktu" value="<?= $id_produktu ?>">
                        <button type="submit" class="submit-btn" 
                            <?= !isset($_SESSION['user_id']) ? 'onclick="alert(\'Zaloguj się, aby dodać do ulubionych!\'); return false;"' : '' ?>>
                            Dodaj do ulubionych [Heart]
                        </button>
                    </form>
                </div>
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

        <script>
            document.querySelectorAll(".dropdown p").forEach(item => {
                item.addEventListener("click", function() {
                    this.parentElement.classList.toggle("clicked");
                });
            });
        </script>
    </body>
</html>