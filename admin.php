<?php
session_start();

if (!isset($_SESSION["rol"]) && isset($_COOKIE["login_token"])) {
    $token = $_COOKIE["login_token"];
    require_once "includes/db.php";
    
    $result = $conn->query("SELECT * FROM users WHERE remember_t='$token' LIMIT 1");
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["rol"] = $user["rol"];

        if ($user["rol"] == "admin") {
            header("Location: admin.php");
            exit;
        }
        header("Location: uye.php");
        exit;
    }
} /*cookıe kontrolu yapılıyor burda  */

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != "admin") {
    header("Location: giris.php");
    exit;
}
require_once "includes/db.php";
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="resimler/logo.jpg" type="image/jpg">
    <link rel="stylesheet" href="style_admin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oranienbaum&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Admin Paneli | Pilates By Ela</title>

</head>
<body>

<header>
        <div class="logo">
            <a href="index.php">
                <img src="RESIMLER/logo.jpg" alt="Logo" style="height:60px;">
            </a>
        </div>
        <div class="title">
            <h1>PİLATES BY ELA</h1>
        </div>
        <div class="menu">
            <a href="index.php" class="menueleman">Anasayfa
                <span class="altefekt"></span>
            </a>
            <a href="paketler.php" class="menueleman">Paketler
                <span class="altefekt"></span>
            </a>
            <a href="egitmenler.php" class="menueleman">Eğitmenler
                <span class="altefekt"></span>
            </a>
            <a href="beslenme.php" class="menueleman">Beslenme
                <span class="altefekt"></span>
            </a>
            <a href="hakkimizda.php" class="menueleman">Hakkımızda
                <span class="altefekt"></span>
            </a>
        </div>
   
    </header>

<div class="container">
    <h2 class="paragraf">Admin Paneli</h2>
    <p class="mesaj">Hoş geldin sevgili Admin! Aşağıdan yönetmek istediğin bölümü seç</p>

    <div class="admin-grid">
        <a href="admin_beslenme.php" class="admin-card card-beslenme">
            <i class="fas fa-apple-alt"></i>
            <h3>Beslenme Yönetimi</h3>
            <p>Kategoriler ve besin öğelerini düzenle</p>
        </a>

        <a href="admin_egitmen.php" class="admin-card card-egitmen">
            <i class="fas fa-user-tie"></i>
            <h3>Eğitmenler</h3>
            <p>Eğitmen ekle, düzenle, sil</p>
        </a>

        <a href="admin_paket.php" class="admin-card card-paket">
            <i class="fas fa-box-open"></i>
            <h3>Paketler</h3>
            <p>Eğitim paketlerini yönet</p>
        </a>

        <a href="admin_yorum.php" class="admin-card card-yorum">
            <i class="fas fa-comments"></i>
            <h3>Müşteri Yorumları</h3>
            <p>Yorumları düzenle ve onayla</p>
        </a>

        <a href="admin_ders_programi.php" class="admin-card card-ders">
            <i class="fas fa-calendar-alt"></i>
            <h3>Ders Programı</h3>
            <p>Haftalık ders takvimini düzenle</p>
        </a>

    </div>

    <a href="cikis.php" class="logout-btn">Çıkış Yap</a>
</div>

</body>
</html>