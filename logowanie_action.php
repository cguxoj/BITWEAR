<?php
    require_once("init.php");

    if (isset($_POST['login-submit'])) {
        $email = trim($_POST['email']);
        $haslo = $_POST['password'];

        $stmt = $conn->prepare("SELECT id_uzytkownika, imie, haslo_hash, rola FROM uzytkownicy WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $imie, $hash, $rola);
        $stmt->fetch();

        if ($stmt->num_rows == 1 && password_verify($haslo, $hash)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['imie'] = $imie;
            $_SESSION['email'] = $email;
            $_SESSION['rola'] = $rola;

            header("Location: konto.php");
            exit;
        } else if ($stmt->num_rows < 1) {
            echo "<script> alert('Wpisz prawidłowe dane.') </script>";
        } else {
            echo "<script> alert('Niepoprawny email lub hasło!') </script>";
        }
    }
?>