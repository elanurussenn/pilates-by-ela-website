<?php
require_once "includes/db.php";

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $ad = $_POST["ad"];
    $soyad = $_POST["soyad"];
    $email = $_POST["email"];
    $sifre = password_hash($_POST["sifre"], PASSWORD_DEFAULT); 
    $kontrol = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($kontrol->num_rows > 0) {
        $mesaj = "Bu e-posta ile daha önce kayıt yapılmış!";
    } else {
        $sql = "INSERT INTO users (ad, soyad, email, sifre) 
                VALUES ('$ad', '$soyad', '$email', '$sifre')";

        if ($conn->query($sql)) {
            $mesaj = "Kayıt başarılı! Giriş yapabilirsiniz.";
        } else {
            $mesaj = "Hata oluştu!";
        }
    }
}
?>

<!doctype html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol</title>
    <link rel="icon" href="resimler/logo.jpg" type="image/jpg">
    <link rel="stylesheet" href="style4.css">
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
            <a href="#paketler" class="menueleman">Seviyeler
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
        <div class="login-icons">
            <a href="giris.php" class="login-link">
                <i class="fas fa-user"></i>
            </a>
        </div>
    </header>
    <div class="login-container">
        <h2>Kayıt Ol</h2>

        <p style="color:red;"><?php echo $mesaj; ?></p>

        <form action="" method="POST">
            <div class="form-group">
                <label for="ad"></label>
                <input type="text" name="ad" id="ad" required placeholder="Adınızı girin">
            </div>

            <div class="form-group">
                <label for="soyad"></label>
                <input type="text" name="soyad" id="soyad" required placeholder="Soyadınızı girin">
            </div>

            <div class="form-group">
                <label for="email"></label>
                <input type="email" name="email" id="email" required placeholder="E-posta adresinizi girin">
            </div>

            <div class="form-group">
                <label for="sifre"></label>
                <input type="password" name="sifre" id="sifre" required placeholder="Şifrenizi girin">
            </div>

            <button type="submit" id="login-btn">Kayıt Ol</button>
        </form>
        <p class="login-prompt">Zaten hesabınız var mı? <a href="giris.php">Giriş yapın</a></p>
    </div>
</body>
</html>
