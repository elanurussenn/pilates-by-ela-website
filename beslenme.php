<?php
session_start();
require_once "includes/db.php";  

$giris_yapildi = isset($_SESSION["user_id"]);
$baglan = new mysqli("localhost", "root", "", "db_pilates");
if ($baglan->connect_error) {
    die("Bağlantı hatası: " . $baglan->connect_error);
}

$sql = "SELECT * FROM beslenme_kategorileri";
$result = $baglan->query($sql);
$secim = isset($_GET['kategori_id']) ? $_GET['kategori_id'] : null;
$items = [];

if ($secim) {
    $sql_items = "SELECT * FROM beslenme_ogeleri WHERE kategori_id = $secim";
    $items_result = $baglan->query($sql_items);
    while ($item = $items_result->fetch_assoc()) {
        $items[] = $item;
    }
}
?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beslenme Bölümü</title>
    <link rel="icon" href="resimler/logo.jpg" type="image/jpg">
    <link rel="stylesheet" href="style_beslenme.css">
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
        <a href="index.php" class="menueleman">Anasayfa<span class="altefekt"></span></a>
        <a href="paketler.php" class="menueleman">Paketler<span class="altefekt"></span></a>
        <a href="egitmenler.php" class="menueleman">Eğitmenler<span class="altefekt"></span></a>
        <a href="beslenme.php" class="menueleman">Beslenme<span class="altefekt"></span></a>
        <a href="hakkimizda.php" class="menueleman">Hakkımızda<span class="altefekt"></span></a>
    </div>
    <div class="login-icons">
        <?php if ($giris_yapildi): ?>
            <?php
            if ($_SESSION['rol'] == 'admin') {
                echo '<a href="admin.php" class="login-link"><i class="fas fa-user"></i></a>';
            } else {
                echo '<a href="uye.php" class="login-link"><i class="fas fa-user"></i></a>';
            }
            ?>
        <?php else: ?>
            <a href="giris.php" class="login-link"><i class="fas fa-user"></i></a>
        <?php endif; ?>
    </div>
</header>
<div class="content-container">
    <h1 class="bilgi">❤︎ Önerilen Beslenme Kategorileri ❤︎</h1>
    <div class="incele-btn-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $kategori_id = $row['id']; 
                $kategori_adi = $row['kategori_adi']; 
                echo '<a href="beslenme.php?kategori_id=' . $kategori_id . '" class="incele-btn">' . $kategori_adi . '</a>';
            }
        } else {
            echo "Kategori bulunamadı.";
        }
        ?>
    </div>
    <?php if ($secim): ?>
    <div class="ogeler-container">
        <?php
        if (count($items) > 0) {
            foreach ($items as $item) {
                echo '<div class="oge">'; 
                if (!empty($item['resim'])) {
                    echo '<div class="resim">';
                    echo '<img src="resimler/' . $item['resim'] . '" alt="' . $item['adi'] . '" />';
                    echo '</div>';
                }
                echo '<div class="oge-kutu">';
                echo '<h3>' . $item['adi'] . '</h3>';
                if (!empty($item['açıklama'])) {
                    echo '<p>' . $item['açıklama'] . '</p>';
                }
                if (!empty($item['kalori'])) {
                    echo '<span class="kalori">Kalori: ' . $item['kalori'] . '</span>';
                }
                echo '</div>'; 
                echo '</div>'; 
            }
        } else {
            echo '<p class="bilgi">Bu kategoriye ait öğe bulunmamaktadır.</p>';
        }
        ?>
    </div>
<?php endif; ?>
</div>
</body>
</html>
<?php
$baglan = null;
?>
