<?php
require_once("init.php");

if (isset($_POST['register-submit'])) {
    $imie = trim($_POST['imie']);
    $email = trim($_POST['email']);
    $haslo = $_POST['haslo'];
    $telefon = trim($_POST['telefon']);
    $poczta = trim($_POST['poczta']);
    $kod_pocztowy = trim($_POST['kod_pocztowy']);
    $adres = trim($_POST['adres']);
    $rola = 'klient';

    $haslo_hash = password_hash($haslo, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT id_uzytkownika FROM uzytkownicy WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Ten email jest już używany!";
        exit;
    }
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO uzytkownicy (imie, email, haslo_hash, telefon, poczta, kod_pocztowy, adres, rola) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $imie, $email, $haslo_hash, $telefon, $poczta, $kod_pocztowy, $adres, $rola);

    if ($stmt->execute()) {
        
        $subject = "BITWEAR - konto zostało utworzone!";
        
        $message = "
        <html>
        <body style='font-family: Arial, sans-serif; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; border-radius: 10px;'>
                <h2>Cześć {$imie}!</h2>
                <p>Twoje konto w sklepie <strong>BITWEAR</strong> zostało pomyślnie utworzone.</p>
                <p>Możesz już się zalogować i robić zakupy!</p>
                <p style='text-align: center; margin: 30px 0;'>
                    <a href='https://bitwear.dawsel.smallhost.pl/logowanie.php' style='background: #000; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 5px;'>
                        ZALOGUJ SIĘ
                    </a>
                </p>
                <p>Dziękujemy za zaufanie!<br>Zespół BITWEAR</p>
            </div>
        </body>
        </html>
        ";

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: BITWEAR <bitwearshop@bitwear.dawsel.smallhost.pl>\r\n";

        mail($email, $subject, $message, $headers);
      
        header("Location: logowanie.php");
        exit;
    } else {
        echo "Błąd podczas rejestracji: " . $conn->error;
    }
    
    $stmt->close();
}
$conn->close();
?>