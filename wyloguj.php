<?php
    require_once("init.php");
    session_destroy();
    $_SESSION = [];
    header("Location: strona_glowna.php");
    exit;
?>