<?php
    require_once("init.php");

    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['rola'], ['admin', 'staff'])) {
        header("Location: admin_panel.php");
        exit;
    }

    if (isset($_POST['dodaj_produkt'])) {
        $nazwa       = trim($_POST['nazwa']);
        $cena        = (float)$_POST['cena'];
        $opis        = trim($_POST['opis']);
        $id_kategorii = (int)$_POST['id_kategorii'];
        $plec        = trim($_POST['plec']);

        $stmt = mysqli_prepare($conn, "INSERT INTO produkty (nazwa, cena, opis, id_kategorii, plec) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sdsis", $nazwa, $cena, $opis, $id_kategorii, $plec);
        mysqli_stmt_execute($stmt);

        header("Location: admin_produkty.php?dodano=1");
        exit;
    }

    if (isset($_POST['edytuj_produkt'])) {
        $id           = (int)$_POST['id'];
        $nazwa        = trim($_POST['nazwa']);
        $cena         = (float)$_POST['cena'];
        $opis         = trim($_POST['opis']);
        $id_kategorii = (int)$_POST['id_kategorii'];
        $plec         = trim($_POST['plec']);

        $stmt = mysqli_prepare($conn, "UPDATE produkty SET nazwa = ?, cena = ?, opis = ?, id_kategorii = ?, plec = ? WHERE id_produktu = ?");
        mysqli_stmt_bind_param($stmt, "sdsisi", $nazwa, $cena, $opis, $id_kategorii, $plec, $id);
        mysqli_stmt_execute($stmt);

        header("Location: admin_produkty.php?edytowano=1");
        exit;
    }

    if (isset($_GET['usun'])) {
        $id = (int)$_GET['usun'];
        $stmt = mysqli_prepare($conn, "DELETE FROM produkty WHERE id_produktu = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        header("Location: admin_produkty.php?usunieto=1");
        exit;
    }

    $wynik = mysqli_query($conn, "SELECT p.*, k.nazwa as nazwa_kategorii FROM produkty p LEFT JOIN kategorie k ON p.id_kategorii = k.id_kategorii ORDER BY p.id_produktu DESC");

    $produkt_do_edycji = null;
    if (isset($_GET['edytuj'])) {
        $id = (int)$_GET['edytuj'];
        $stmt = mysqli_prepare($conn, "SELECT * FROM produkty WHERE id_produktu = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $produkt_do_edycji = mysqli_fetch_assoc($result);
    }
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Zarządzanie Produktami – BITWEAR</title>
        <link rel="stylesheet" href="style/for_all.css">
        <link rel="stylesheet" href="style/admin.css">
        <link rel="icon" href="zdjecia/logo.png">
        <style>
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
            <h3>Produkty</h3>

            <center>
                <?php if(isset($_GET['dodano'])) echo '<p class="success">Produkt dodany!</p>'; ?>
                <?php if(isset($_GET['edytowano'])) echo '<p class="success">Produkt zapisany!</p>'; ?>
            </center>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Nazwa</th>
                    <th>Cena</th>
                    <th>Kategoria ID</th>
                    <th>Płeć</th>
                    <th>Akcje</th>
                </tr>
                <?php
                $wynik = mysqli_query($conn, "SELECT * FROM produkty ORDER BY id_produktu DESC");
                while ($prod = mysqli_fetch_assoc($wynik)):
                ?>
                    <tr>
                        <td><?= $prod['id_produktu'] ?></td>
                        <td><?= htmlspecialchars($prod['nazwa']) ?></td>
                        <td><?= number_format($prod['cena'], 2) ?> zł</td>
                        <td><?= $prod['id_kategorii'] ?></td>
                        <td><?= htmlspecialchars($prod['plec']) ?></td>
                        <td>
                            <a href="?edytuj=<?= $prod['id_produktu'] ?>" class="edytuj-a">Edytuj</a> |
                            <a href="?usun=<?= $prod['id_produktu'] ?>" onclick="return confirm('Na pewno usunąć produkt?')" class="usun-a">Usuń</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>

            <?php if ($produkt_do_edycji): ?>
                <div class="form-div">
                    <h4>Edytuj produkt #<?= $produkt_do_edycji['id_produktu'] ?></h4>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $produkt_do_edycji['id_produktu'] ?>">

                        <label>Nazwa: <input type="text" name="nazwa" value="<?= htmlspecialchars($produkt_do_edycji['nazwa']) ?>" required></label><br>
                        <label>Cena: <input type="number" step="0.01" name="cena" value="<?= $produkt_do_edycji['cena'] ?>" required></label><br>
                        <label>Opis: <textarea name="opis"><?= htmlspecialchars($produkt_do_edycji['opis'] ?? '') ?></textarea></label><br>
                        <label>ID Kategorii: <input type="number" name="id_kategorii" value="<?= $produkt_do_edycji['id_kategorii'] ?>" required></label><br>
                        <label>Płeć: 
                            <select name="plec" required>
                                <option value="on" <?= $produkt_do_edycji['plec']=='on'?'selected':'' ?>>ON</option>
                                <option value="ona" <?= $produkt_do_edycji['plec']=='ona'?'selected':'' ?>>ONA</option>
                                <option value="dziecko" <?= $produkt_do_edycji['plec']=='dziecko'?'selected':'' ?>>DZIECKO</option>
                                <option value="uni" <?= $produkt_do_edycji['plec']=='uni'?'selected':'' ?>>UNI</option>
                            </select>
                        </label><br>
                        <button type="submit" name="edytuj_produkt" class="submit-btn">Zapisz zmiany</button>
                    </form>
                </div>
            <?php endif; ?>

            <div class="form-div">
                <h4>Dodaj nowy produkt</h4>
                <form method="POST">
                    <label>Nazwa: <input type="text" name="nazwa" required></label><br>
                    <label>Cena: <input type="number" step="0.01" name="cena" required></label><br>
                    <label>Opis: <textarea name="opis"></textarea></label><br>
                    <label>ID Kategorii: <input type="number" name="id_kategorii" placeholder="1=Jeansy, 2=Sneakersy, 3=Skarpety" required></label><br>
                    <label>Płeć: 
                        <select name="plec" required>
                            <option value="on">ON</option>
                            <option value="ona">ONA</option>
                            <option value="dziecko">DZIECKO</option>
                            <option value="uni">UNI</option>
                        </select>
                    </label><br>
                    <button type="submit" name="dodaj_produkt" class="submit-btn">Dodaj produkt</button>
                </form>

                <div class="powrot-div">
                    <a href="admin_panel.php" class="powrot-a">← Powrót</a>
                </div>
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