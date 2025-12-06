<?php
    require_once("init.php");

    if (!isset($_SESSION['user_id']) || $_SESSION['rola'] !== 'admin') {
        header("Location: admin_panel.php");
        exit;
    }

    if (isset($_POST['dodaj_pracownika'])) {
        $imie = trim($_POST['imie']);
        $email = trim($_POST['email']);
        $haslo = $_POST['haslo'];
        $typ = $_POST['typ'];
        $rola = ($typ === 'szef') ? 'admin' : 'staff';
        $haslo_hash = password_hash($haslo, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conn, "INSERT INTO uzytkownicy (imie, email, haslo_hash, rola) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $imie, $email, $haslo_hash, $rola);
        mysqli_stmt_execute($stmt);
        $id_uzytkownika = mysqli_insert_id($conn);

        $stmt = mysqli_prepare($conn, "INSERT INTO pracownicy (id_uzytkownika, typ) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "is", $id_uzytkownika, $typ);
        mysqli_stmt_execute($stmt);

        header("Location: admin_pracownicy.php");
        exit;
    }

    if (isset($_POST['edytuj_pracownika'])) {
        $id_pracownika = (int)$_POST['id_pracownika'];
        $imie = trim($_POST['imie']);
        $email = trim($_POST['email']);
        $typ = $_POST['typ'];
        $rola = ($typ === 'szef') ? 'admin' : 'staff';

        $stmt = mysqli_prepare($conn, "UPDATE uzytkownicy SET imie = ?, email = ?, rola = ? WHERE id_uzytkownika = (SELECT id_uzytkownika FROM pracownicy WHERE id_pracownika = ?)");
        mysqli_stmt_bind_param($stmt, "sssi", $imie, $email, $rola, $id_pracownika);
        mysqli_stmt_execute($stmt);

        if (!empty($_POST['haslo'])) {
            $haslo_hash = password_hash($_POST['haslo'], PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "UPDATE uzytkownicy SET haslo_hash = ? WHERE id_uzytkownika = (SELECT id_uzytkownika FROM pracownicy WHERE id_pracownika = ?)");
            mysqli_stmt_bind_param($stmt, "si", $haslo_hash, $id_pracownika);
            mysqli_stmt_execute($stmt);
        }

        $stmt = mysqli_prepare($conn, "UPDATE pracownicy SET typ = ? WHERE id_pracownika = ?");
        mysqli_stmt_bind_param($stmt, "si", $typ, $id_pracownika);
        mysqli_stmt_execute($stmt);

        header("Location: admin_pracownicy.php");
        exit;
    }

    if (isset($_GET['usun'])) {
        $id = (int)$_GET['usun'];
        mysqli_query($conn, "DELETE FROM pracownicy WHERE id_pracownika = $id");
        header("Location: admin_pracownicy.php");
        exit;
    }

    $wynik = mysqli_query($conn, "
        SELECT p.id_pracownika, u.imie, u.email, p.typ 
        FROM pracownicy p JOIN uzytkownicy u ON p.id_uzytkownika = u.id_uzytkownika
    ");

    $pracownik_do_edycji = null;
    if (isset($_GET['edytuj'])) {
        $id = (int)$_GET['edytuj'];
        $stmt = mysqli_prepare($conn, "
            SELECT p.id_pracownika, u.imie, u.email, p.typ 
            FROM pracownicy p JOIN uzytkownicy u ON p.id_uzytkownika = u.id_uzytkownika 
            WHERE p.id_pracownika = ?
        ");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $pracownik_do_edycji = mysqli_fetch_assoc($result);
    }
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Panel Admina – BITWEAR</title>
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
            <h3>Pracownicy</h3>
            <table>
                <tr><th>ID</th><th>Imię</th><th>Email</th><th>Typ</th><th>Akcje</th></tr>
                <?php while ($prac = mysqli_fetch_assoc($wynik)): ?>
                    <tr>
                        <td><?= $prac['id_pracownika'] ?></td>
                        <td><?= htmlspecialchars($prac['imie']) ?></td>
                        <td><?= htmlspecialchars($prac['email']) ?></td>
                        <td><?= ucfirst($prac['typ']) ?></td>
                        <td>
                            <a href="?edytuj=<?= $prac['id_pracownika'] ?>" class="edytuj-a">Edytuj</a> |
                            <a href="?usun=<?= $prac['id_pracownika'] ?>" onclick="return confirm('Usunąć?')" class="usun-a">Usuń</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>

            <?php if ($pracownik_do_edycji): ?>
                <div class="form-div">
                    <h4>Edytuj pracownika #<?= $pracownik_do_edycji['id_pracownika'] ?></h4>
                    <form method="POST">
                        <input type="hidden" name="id_pracownika" value="<?= $pracownik_do_edycji['id_pracownika'] ?>">
                        <label>Imię: <input type="text" name="imie" value="<?= htmlspecialchars($pracownik_do_edycji['imie']) ?>" required></label><br>
                        <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($pracownik_do_edycji['email']) ?>" required></label><br>
                        <label>Hasło: <input type="password" name="haslo" placeholder="zostaw puste, jeśli bez zmian"></label><br>
                        <label>Typ: 
                            <select name="typ">
                                <option value="szef" <?= $pracownik_do_edycji['typ'] === 'szef' ? 'selected' : '' ?>>Szef</option>
                                <option value="pracownik" <?= $pracownik_do_edycji['typ'] === 'pracownik' ? 'selected' : '' ?>>Pracownik</option>
                            </select>
                        </label><br>
                        <button type="submit" name="edytuj_pracownika" class="submit-btn">Zapisz zmiany</button>
                    </form>
                </div>
            <?php endif; ?>

            <div class="form-div">
                <h4>Dodaj nowego pracownika</h4>
                <form method="POST">
                    <label>Imię: <input type="text" name="imie" required></label><br>
                    <label>Email: <input type="email" name="email" required></label><br>
                    <label>Hasło: <input type="password" name="haslo" required></label><br>
                    <label>Typ: 
                        <select name="typ">
                            <option value="szef">Szef</option>
                            <option value="pracownik">Pracownik</option>
                        </select>
                    </label><br>
                    <button type="submit" name="dodaj_pracownika" class="submit-btn">Dodaj</button>
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