<?php
    require_once("init.php");
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Produkty - BITWEAR</title>
        <link rel="stylesheet" href="style/for_all.css">
        <link rel="stylesheet" href="style/produkty.css">
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

        <section id="aside-main">
            <aside>
                <h3>Filtry</h3>

                <form method="GET" action="produkty.php">
                    <?php if (!empty($_GET['plec'])): ?>
                        <input type="hidden" name="plec" value="<?php echo htmlspecialchars($_GET['plec']); ?>">
                    <?php endif; ?>

                    <?php if (!empty($_GET['kategoria'])): ?>
                        <input type="hidden" name="kategoria" value="<?php echo htmlspecialchars($_GET['kategoria']); ?>">
                    <?php endif; ?>

                    <p><b>Cena:</b></p>
                    <label>Od:</label>
                    <input type="number" name="cena_od" step="0.01" value="<?php echo $_GET['cena_od'] ?? ''; ?>"><br>
                    <label>Do:</label>
                    <input type="number" name="cena_do" step="0.01" value="<?php echo $_GET['cena_do'] ?? ''; ?>">
                    <br><br>

                    <p><b>Materiał:</b></p>
                    <?php
                        $sql = "SELECT id_materialu, nazwa FROM materialy";
                        $query = mysqli_query($conn, $sql);
                        while ($m = mysqli_fetch_assoc($query)) {
                            $checked = (isset($_GET['material']) && $_GET['material'] == $m['id_materialu']) ? "checked" : "";
                            echo "<label>
                                    <input type='radio' name='material' value='".$m['id_materialu']."' $checked>
                                    ".$m['nazwa']."
                                  </label><br>";
                        }
                    ?>
                    <br><br>

                    <div style="text-align: center;">
                        <button type="submit" class="submit-btn">Zastosuj filtr</button>
                        <?php
                        $reset_url = 'produkty.php';
                        $params = [];
                        if (!empty($_GET['plec']))      $params['plec'] = $_GET['plec'];
                        if (!empty($_GET['kategoria'])) $params['kategoria'] = $_GET['kategoria'];
                        if (!empty($params)) {
                            $reset_url .= '?' . http_build_query($params);
                        }
                        ?>
                        <a href="<?php echo htmlspecialchars($reset_url); ?>">
                            <button type="button" class="reset-btn">Zresetuj filtry</button>
                        </a>
                    </div>
                </form>
            </aside>

            <main>
                <h2>Produkty</h2>

                <?php if (!empty($_GET['plec'])): ?>
                <p style="margin: -20px 0 40px; font-size: 0.85em; text-align: left; color: #ccc;">
                    <?php
                    $plec = strtoupper(htmlspecialchars($_GET['plec']));
                    $all_for_gender_url = 'produkty.php?plec=' . urlencode($_GET['plec']);
                    ?>
                    <a href="<?php echo $all_for_gender_url; ?>" 
                    style="color: #aaa; text-decoration: underline; font-weight: 500; font-size: 0.93em;">
                        >> Pokaż wszystkie <?php echo $plec; ?>
                    </a>
                </p>
                <?php endif; ?>

                <?php
                    $sql = "SELECT p.id_produktu, p.nazwa, p.cena, p.plec, k.nazwa AS kategoria,
                                zp.sciezka AS zdjecie
                            FROM produkty p
                            LEFT JOIN kategorie k ON p.id_kategorii = k.id_kategorii
                            LEFT JOIN zdjecia_produktow zp 
                                ON p.id_produktu = zp.id_produktu AND zp.typ = 'main'";

                    $conditions = [];

                    if (!empty($_GET['plec'])) {
                        $plec = mysqli_real_escape_string($conn, $_GET['plec']);
                        $conditions[] = "p.plec = '$plec'";
                    }

                    if (!empty($_GET['kategoria'])) {
                        $kat = mysqli_real_escape_string($conn, $_GET['kategoria']);
                        $conditions[] = "k.nazwa = '$kat'";
                    }

                    if (!empty($_GET['cena_od'])) {
                        $c_od = floatval($_GET['cena_od']);
                        $conditions[] = "p.cena >= $c_od";
                    }

                    if (!empty($_GET['cena_do'])) {
                        $c_do = floatval($_GET['cena_do']);
                        $conditions[] = "p.cena <= $c_do";
                    }

                    if (!empty($_GET['material'])) {
                        $material = intval($_GET['material']);
                        $conditions[] = "p.id_materialu = $material";
                    }

                    if (count($conditions) > 0) {
                        $sql .= " WHERE " . implode(" AND ", $conditions);
                    }

                    $sql .= " ORDER BY p.nazwa ASC";

                    $query = mysqli_query($conn, $sql);
                ?>

                <div class="produkty-grid">
                    <?php
                        if (mysqli_num_rows($query) > 0) {
                            while($p = mysqli_fetch_assoc($query)) {
                                $img = !empty($p['zdjecie']) ? $p['zdjecie'] : "zdjecia/brak.jpg";

                                echo '
                                <div class="produkt">
                                    <a href="produkt.php?id=' . $p['id_produktu'] . '">
                                        <img src="' . $img . '" alt="' . htmlspecialchars($p['nazwa']) . '">
                                        <h4>' . htmlspecialchars($p['nazwa']) . '</h4>
                                        <p class="cena">' . number_format($p['cena'], 2, ',', ' ') . ' zł</p>
                                    </a>
                                </div>';
                            }
                        } else {
                            echo "<p>Brak produktów spełniających kryteria.</p>";
                        }
                    ?>
                </div>
            </main>
        </section>

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