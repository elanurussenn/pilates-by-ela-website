<?php
session_start();
require_once "includes/db.php";
$giris_yapildi = isset($_SESSION["user_id"]);
$baglan = new mysqli("localhost", "root", "", "db_pilates");

if ($baglan->connect_error) {
    die("Bağlantı hatası: " . $baglan->connect_error);
}

$baslik = "Tüm Paketler";
$filtre = "";

if (isset($_GET['seviye'])) {
    $seviyeID = intval($_GET['seviye']);

    if ($seviyeID > 0) {
        $seviyeSorgu = $conn->query("SELECT seviye_adi FROM seviyeler WHERE id = $seviyeID");
        if ($seviyeSorgu && $seviyeSorgu->num_rows > 0) {
            $seviye = $seviyeSorgu->fetch_assoc();
            $baslik = $seviye['seviye_adi'] . " Seviye Paketler";
        }

        $filtre = "WHERE seviye = $seviyeID";
    }
}

$sorgu = $conn->query("SELECT p.*, u.ad AS egitmen_ad, u.soyad AS egitmen_soyad
                       FROM paketler p
                       LEFT JOIN egitmenler e ON p.egitmen_id = e.id
                       LEFT JOIN users u ON e.user_id = u.id
                       $filtre
                       ORDER BY p.id ASC");
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
<h2 class="baslik"><?php echo '❤︎ '.$baslik . ' ❤︎'; ?></h2>
<div class="paket">
    <?php
       $kullanici_rol = $_SESSION['rol'] ?? ''; 
       while($paket = $sorgu->fetch_assoc())
        {
          $resimYol = "resimler/" . $paket['resim'];
          $egitmenAdSoyad = $paket['egitmen_ad'] . ' ' . $paket['egitmen_soyad'];
           if ($kullanici_rol === 'admin' || $kullanici_rol === 'egitmen') {
            $satinalma_link = '#';
        } 
        else {
          $satinalma_link = 'paket_satinal.php?id=' . $paket['id']; 
        }
    echo '
    <div class="paketkart">
        <a href="'.$satinalma_link.'">
        <img src="'.$resimYol.'" alt="'.$paket['paket_adi'].'">
        <div class="paketmetin">
            <h3>'.$paket['paket_adi'].'</h3>
            <p>'.$paket['slogan'].'</p>
            <span>'.$paket['ders_sayisi'].' Ders</span>
            <strong>'.$paket['fiyat'].' TL</strong>
            <p>'.$paket['aciklama'].'</p>
            <p><strong>Eğitmen:</strong> '.$egitmenAdSoyad.'</p>
        </div></a>
    </div>
    ';
}
?>
</div>
<div class="paket-bilgi-kutusu">
    <div class="paket-bilgi-icerik">
        <p>
            <strong>LOTUS Pilates</strong>’te her seviye için özel olarak tasarlanmış paketlerimiz bulunmaktadır. 
            Her paket, o seviyeye uygun sertifikalı <strong>eğitmenlerimizden biri</strong> tarafından yönetilir.
        </p>
        <p>
            Satın aldığınız paket kapsamında belirtilen ders sayısına kadar, pakete dahil olan eğitmenin haftalık ders programına göre rezervasyon yapabilirsiniz. 
            ❤︎ <strong>Paket hakkınız bitmeden veya iptal edilmeden yeni paket alınamaz.</strong> ❤︎
        </p>
        <p>
            Dersler birebir özel ders şeklinde gerçekleşir ve eğitmeninizin uzmanlığına göre hareketler sizin seviyenize, hedeflerinize ve fiziksel durumunuza göre kişiselleştirilir. 
            Bu sayede en güvenli ve etkili şekilde ilerlersiniz.
        </p>
        <p class="son-cumle">
            Siz sadece kendinizi bize bırakın, gerisini eğitmenimiz halleder ❤︎
        </p>
    </div>
</div>
</body>
</html>
