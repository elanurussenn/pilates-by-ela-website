<?php
session_start();
require_once "includes/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: giris.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$paket_id = 0;

if (isset($_GET["id"])) {
    $paket_id = intval($_GET["id"]);
} else {
    if (isset($_POST["paket_id"])) {
        $paket_id = intval($_POST["paket_id"]);
    } else {
        $paket_id = 0;
    }
}


$sql = "SELECT * FROM paketler WHERE id = $paket_id";
$paket_sonuc = $conn->query($sql);
$paket = $paket_sonuc ? $paket_sonuc->fetch_assoc() : null;


$ders_hakki = (int)$paket["ders_sayisi"];
$sql_aktif = "SELECT kp.*, p.paket_adi
              FROM kullanici_paketleri kp
              JOIN paketler p ON kp.paket_id = p.id
              WHERE kp.user_id = $user_id
                AND kp.kalan_ders > 0
              LIMIT 1";

$aktif_sonuc  = $conn->query($sql_aktif);
$aktif_paket = null;

if ($aktif_sonuc && $aktif_sonuc->num_rows > 0) {
    $aktif_paket = $aktif_sonuc->fetch_assoc();
} else {
    $aktif_paket = null;
}

$sql_kullanici_paketi = "SELECT * FROM kullanici_paketleri 
                          WHERE user_id = $user_id AND paket_id = $paket_id 
                          LIMIT 1";

$kullanici_paketi_sonuc = $conn->query($sql_kullanici_paketi);
$kullanici_paketi = null;

if ($kullanici_paketi_sonuc && $kullanici_paketi_sonuc->num_rows > 0) {
    $kullanici_paketi = $kullanici_paketi_sonuc->fetch_assoc();
} else {
    $kullanici_paketi = null;
}
         
if ($aktif_paket) {
    $mesaj = "Şu anda aktif bir paketiniz var: ". htmlspecialchars($aktif_paket['paket_adi']) . ". Kalan ders sayınız: " . (int)$aktif_paket['kalan_ders']. ". Bu paket bittikten sonra yeni paket satın alabilirsiniz.";
} else {
    $mesaj = "Paket satın almaya hazırsınız. Onaylamak için aşağıdaki butona tıklayın.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($aktif_paket) {
        $mesaj = "Aktif paketiniz varken yeni paket satın alamazsınız. "
               . "Kalan ders sayınız: " . (int)$aktif_paket['kalan_ders'];
    } else {
        $sql2 = "INSERT INTO kullanici_paketleri (user_id, paket_id, kalan_ders) 
                 VALUES ('$user_id', '$paket_id', '$ders_hakki')";

        if ($conn->query($sql2)) {
            $mesaj = "Satın alma başarılı! Ders hakkınız: $ders_hakki";
        } else {
            $mesaj = "Bir hata oluştu! Lütfen tekrar deneyin.";
        }
    }
}

$giris_yapildi = isset($_SESSION["user_id"]);
$baglan = new mysqli("localhost", "root", "", "db_pilates");

if ($baglan->connect_error) {
    die("Bağlantı hatası: " . $baglan->connect_error);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    
    <meta charset="UTF-8">
    <title>Paket Satın Al</title>
    <link rel="icon" href="resimler/logo.jpg" type="image/jpg">
    <link href="https://fonts.googleapis.com/css2?family=Oranienbaum&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="paket.css?v=2">

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

<div class="satinal-sayfa">
    <h2 class="satinal-baslik">
        ❤︎ Paket Satın Alma ❤︎
    </h2>

    <div class="satinal-icerik">
        <!-- Paket kartı -->
        <div class="paketkart tek-paket">
            <img src="resimler/<?php echo htmlspecialchars($paket['resim']); ?>"
                 alt="<?php echo htmlspecialchars($paket['paket_adi']); ?>">

            <div class="paketmetin">
                <h3><?php echo htmlspecialchars($paket['paket_adi']); ?></h3>
                <p><?php echo htmlspecialchars($paket['slogan']); ?></p>
                <span><?php echo (int)$paket['ders_sayisi']; ?> Ders</span>
                <strong><?php echo number_format($paket['fiyat'], 2); ?> TL</strong>
            </div>
        </div>

         <div class="satinal-panel">
    <div class="satinal-mesaj
        <?php
            if ($aktif_paket) {
                echo ' hata';
            } elseif ($_SERVER["REQUEST_METHOD"] === "POST" && !$aktif_paket) {
                echo ' basari';
            } else {
                echo ' bilgi';
            }
        ?>">
        <?php echo $mesaj; ?>
    </div>

    <?php if (!$aktif_paket): ?>
        <button id="acButton" class="satinal-button">Bu Paketi Satın Al</button>

        <div id="formDiv" class="satinal-form-gizli">
            <form method="post" class="satinal-form">
                <input type="hidden" name="paket_id" value="<?php echo $paket_id; ?>">
                <label>Kart Sahibi Adı</label>
                <input type="text" name="adsoyad" required>
                <label>Kart Numarası</label>
                <input type="text" name="kartno" maxlength="16" required>
                <label>Son Kullanma (AA/YY)</label>
                <input type="text" name="sonkullanma" maxlength="5" required>
                <label>CVV</label>
                <input type="text" name="cvv" maxlength="3" required>
                <button type="submit" class="satinal-button">Satın Almayı Onayla</button>
            </form>
        </div>
    <?php else: ?>
        <p class="satinal-uyari">
            Mevcut paketin dersleri bittikten sonra yada paketi iptal ettikten sonra yeni paket satın alabilirsiniz.
        </p>
    <?php endif; ?>

    <div class="satinal-alt-linkler">
        <a href="uye.php" class="action-button">Üye Paneline Dön</a>
        <a href="index.php" class="action-button ikincil">Anasayfaya Dön</a>
    </div>
</div>
    </div>
</div>
<script>
document.getElementById("acButton").addEventListener("click", function() {
    const formDiv = document.getElementById("formDiv");
    formDiv.classList.toggle("aktif");
});
</script>


</body>
</html>
