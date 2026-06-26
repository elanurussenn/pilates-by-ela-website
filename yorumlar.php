<?php
session_start();
require_once "includes/db.php";

$giris_yapildi = isset($_SESSION["user_id"]);
$baglan = new mysqli("localhost", "root", "", "db_pilates");

if ($baglan->connect_error) {
    die("Bağlantı hatası: " . $baglan->connect_error);
}
if (!$giris_yapildi) {
    header("Location: giris.php");
    exit;
}
/*giriş yapılmadıysa bu sayfaya girmemesi lazım */

$user_id = (int)$_SESSION["user_id"];
if (!isset($_GET["egitmen_id"])) {
    echo "Eğitmen seçilmedi.";
    exit;
}
$egitmen_id = (int)$_GET["egitmen_id"];
$egitmen_bilgi = [];
$sorgu_eg = "
    SELECT u.ad, u.soyad, e.profil_resmi
    FROM egitmenler e
    JOIN users u ON e.user_id = u.id
    WHERE e.id = $egitmen_id
";

$sonuc_eg = $baglan->query($sorgu_eg);
if ($sonuc_eg && $sonuc_eg->num_rows > 0) {
    $egitmen_bilgi = $sonuc_eg->fetch_assoc();
    $egitmen_adi = $egitmen_bilgi["ad"] . " " . $egitmen_bilgi["soyad"];
    $egitmen_resim = !empty($egitmen_bilgi["profil_resmi"]) ? "resimler/" . $egitmen_bilgi["profil_resmi"] : "resimler/default.jpg";
} else {
    $egitmen_adi = "Bilinmeyen Eğitmen";
    $egitmen_resim = "resimler/default.jpg";
}

$mesaj = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $puan  = isset($_POST["puan"]) ? (int)$_POST["puan"] : 0;
    $yorum = isset($_POST["yorum"]) ? trim($_POST["yorum"]) : "";

    if ($puan < 1 || $puan > 5 || $yorum == "") {
        $mesaj = "❤︎ Lütfen yorum yazın.";
    } else {
        $yorum = $baglan->real_escape_string($yorum);
        $ekle = "
            INSERT INTO yorumlar (user_id, egitmen_id, puan, yorum, olusturulma_tarihi)
            VALUES ($user_id, $egitmen_id, $puan, '$yorum', NOW())
        ";
        if ($baglan->query($ekle)) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?egitmen_id=" . $egitmen_id);
            exit();
        } else {
            $mesaj = "Yorum kaydedilirken hata oluştu: " . $baglan->error;
        }
    }
}
$yorumlar = [];
$sorgu_yorum = "
    SELECT y.puan, y.yorum, y.olusturulma_tarihi, u.ad, u.soyad
    FROM yorumlar y
    JOIN users u ON y.user_id = u.id
    WHERE y.egitmen_id = $egitmen_id
    ORDER BY y.olusturulma_tarihi DESC
";
$sonuc_yorum = $baglan->query($sorgu_yorum);
if ($sonuc_yorum) {
    while ($satir = $sonuc_yorum->fetch_assoc()) {
        $yorumlar[] = $satir;
    }
}
?>




<!doctype html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($egitmen_adi); ?> | Yorumlar</title>
    <link rel="icon" href="resimler/logo.jpg" type="image/jpg">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="stylehakk.css">
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

<div class="yorum-sayfa">
    <div class="egitmen-kart">
        <img src="<?php echo $egitmen_resim; ?>" alt="Eğitmen" class="egitmen-resim">
        <h2 class="egitmen-ad"><?php echo htmlspecialchars($egitmen_adi); ?></h2>
        <p class="egitmen-aciklama">Bu eğitmen hakkında yorum yapabilir ve diğer kullanıcıların deneyimlerini görebilirsiniz.</p>
    </div>

    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'musteri'): ?>
    <div class="yorum-form">
        <h3>Yorum Yap</h3>
        <?php if ($mesaj != ""): ?>
            <p class="uyari"><?php echo $mesaj; ?></p>
        <?php endif; ?>
        <form method="post" action="">
            <label>Puan (1-5):</label>
            <select name="puan">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5" selected>5</option>
            </select>
            <textarea name="yorum" placeholder="Yorumunuzu yazın..." rows="4"></textarea>
            <button type="submit">Gönder</button>
        </form>
    </div>
<?php else: ?>
    <div class="yorum-form">
        <h3>Yorum Yap</h3>
        <p style="text-align:center; color:#451032; font-family:'Oranienbaum', serif;">
            ❤︎ Yalnızca üyeler yorum yapabilir. ❤︎
        </p>
    </div>
<?php endif; ?>


    <div class="yorumlar">
        <h3>❤︎ Diğer Üyelerin Yaptığı Yorumlar ❤︎</h3>
        <?php if (count($yorumlar) == 0): ?>
            <p class="mesajjj">Henüz yorum yapılmamış.</p>
        <?php else: ?>
            <?php foreach ($yorumlar as $y): ?>
                <div class="yorum-kutu">
                    <div class="yorum-icerik">
                        <strong><?php echo htmlspecialchars($y["ad"] . " " . $y["soyad"]); ?></strong>
                        <?php 
                          $puan = (int)$y["puan"];
                          echo '<div class="yildizlar">';
                          for ($i = 1; $i <= 5; $i++) {
                          echo $i <= $puan ? '⭐' : '☆';
                          }
                          echo '</div>';
                        ?>
                        <p><?php echo nl2br(htmlspecialchars($y["yorum"])); ?></p>
                        <small><?php echo $y["olusturulma_tarihi"]; ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
