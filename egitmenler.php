<?php
session_start();
require_once "includes/db.php";
$giris_yapildi = isset($_SESSION["user_id"]);
$baglan = new mysqli("localhost", "root", "", "db_pilates");

if ($baglan->connect_error) {
    die("Bağlantı hatası: " . $baglan->connect_error);
}
$baslik = "Eğitmenlerimiz";

$sql = "SELECT e.*, u.ad, u.soyad
        FROM egitmenler e
        JOIN users u ON e.user_id = u.id
        ORDER BY u.ad, u.soyad";
$result = $conn->query($sql);
?>



<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $baslik; ?></title>

    <link rel="stylesheet" href="style1.css">
    <link rel="icon" href="resimler/logo.jpg" type="image/jpg">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oranienbaum&display=swap" rel="stylesheet">
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
        <a href="index.php" class="menueleman">Anasayfa <span class="altefekt"></span></a>
        <a href="paketler.php" class="menueleman">Paketler <span class="altefekt"></span></a>
        <a href="egitmenler.php" class="menueleman">Eğitmenler <span class="altefekt"></span></a>
        <a href="beslenme.php" class="menueleman">Beslenme <span class="altefekt"></span></a>
        <a href="hakkimizda.php" class="menueleman">Hakkımızda <span class="altefekt"></span></a>
    </div>

    <div class="login-icons">
        <?php if ($giris_yapildi): ?>
            <?php
            if ($_SESSION['rol'] == 'admin') {
                echo '<a href="admin.php" class="login-link">
                        <i class="fas fa-user"></i> 
                      </a>';
            } else {
                echo '<a href="uye.php" class="login-link">
                        <i class="fas fa-user"></i> 
                      </a>';
            }
            ?>
        <?php else: ?>
            <a href="giris.php" class="login-link">
                <i class="fas fa-user"></i> 
            </a>
        <?php endif; ?>
    </div>
</header>
<h2 class="baslik"><?php echo '❤︎ '.$baslik .' ❤︎'; ?></h2>
<div class="egitmenlerdiv">
    <?php while ($row = $result->fetch_assoc()) { ?>
        <?php
        $hedef = $giris_yapildi 
            ? "yorumlar.php?egitmen_id=" . $row['id'] 
            : "giris.php";
        ?>
        <div class="egitmen">
            <div class="egitmenresim">
                <img src="resimler/<?php echo $row['profil_resmi']; ?>" alt="<?php echo $row['ad']; ?>" />
            </div>

            <div class="egitmenbilgi" onclick="window.location.href='<?php echo $hedef; ?>';" style="cursor:pointer;">
                <h3><?php echo $row['ad'] . ' ' . $row['soyad']; ?></h3>
                <p><strong>Uzmanlık:</strong> <?php echo $row['uzmanlik']; ?></p>
                <p><?php echo $row['bio']; ?></p>
            </div>
            <?php
            $egitmen_id = $row['id'];
            $puan_sorgu = $conn->query("SELECT AVG(puan) AS ort_puan, COUNT(puan) AS yorum_sayisi 
                                        FROM yorumlar 
                                        WHERE egitmen_id = $egitmen_id");
            $puan_veri = $puan_sorgu->fetch_assoc();
            $ort_puan = $puan_veri['ort_puan'] ? round($puan_veri['ort_puan'], 1) : 0;
            $yorum_sayisi = $puan_veri['yorum_sayisi'] ?? 0;
            ?>

            <?php if ($yorum_sayisi > 0): ?>
            <div class="egitmen-puan">
                <?php if ($yorum_sayisi > 0): ?>
                    <div class="yildizlar">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?php if ($i <= $ort_puan): ?>
                                <i class="fas fa-star"></i>
                            <?php elseif ($i - 0.5 <= $ort_puan): ?>
                                <i class="fas fa-star-half-alt"></i>
                            <?php else: ?>
                                <i class="far fa-star"></i>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                <?php else: ?>
                    <span class="henüz-yorum-yok">Henüz yorum yapılmadı</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    <?php } ?>
</div>
</body>
</html>
