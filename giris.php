<?php
session_start();
require_once "includes/db.php";

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $sifre = $_POST["sifre"];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $sonuc = $conn->query($sql);
    if ($sonuc->num_rows > 0) {
        $kullanici = $sonuc->fetch_assoc();
        if (password_verify($sifre, $kullanici["sifre"])) {
           /*sıfre kontrolunu sagladım */
            $_SESSION["user_id"] = $kullanici["id"];
            $_SESSION["rol"] = $kullanici["rol"];

            if (isset($_POST['remember'])) {
                $token = bin2hex(random_bytes(32));
                $user_id = $kullanici["id"];
                setcookie("login_token", $token, time() + 86400 * 30, "/", "", false, true);
                $conn->query("UPDATE users SET remember_t='$token' WHERE id=$user_id");
            }
            if ($_SESSION["rol"] == "admin") {
                header("Location: admin.php");
            } else {
                header("Location: uye.php");
            }
            exit;

        } else {
            $mesaj = "Şifre yanlış!";
        }

    } else {
        $mesaj = "E-posta bulunamadı!";
    }
}
?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link rel="icon" href="resimler/logo.jpg" type="image/jpg">
    <link rel="stylesheet" href="style3.css">
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
        <div class="login-icons">
            <a href="giris.php" class="login-link">
                <i class="fas fa-user"></i>
            </a>
        </div>
    </header>
    <div class="login-container">
        <h2>Giriş Yap</h2>
       <form method="POST" id="login-form" class="login-form">
    <div class="form-group">
        <label for="email"></label>
        <input type="email" id="email" name="email" required placeholder="E-posta adresinizi girin">
    </div>

    <div class="form-group">
        <label for="sifre"></label>
        <input type="password" id="sifre" name="sifre" required placeholder="Şifrenizi girin">
        <label>
</label>
    </div>
    <div class="form-footer">
        <input type="checkbox" name="remember"> Beni Hatırla
        <button type="submit" id="login-btn">Giriş Yap</button>
        <p class="signup-link">Henüz hesabınız yok mu? <a href="kayit.php">Kayıt olun</a></p>
    </div>
</form>

    </div>
</body>
</html>
