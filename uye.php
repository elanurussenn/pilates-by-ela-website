<?php
session_start();
require_once "includes/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: giris.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$rol = isset($_SESSION["rol"]) ? $_SESSION["rol"] : null;

$sql_kullanici = "SELECT ad, soyad FROM users WHERE id = $user_id";
$kullanicisonuc = $conn->query($sql_kullanici);
$kullanici = $kullanicisonuc->fetch_assoc();

$sql_paket = "SELECT kp.*, p.paket_adi, p.fiyat 
              FROM kullanici_paketleri kp
              JOIN paketler p ON kp.paket_id = p.id
              WHERE kp.user_id = $user_id";
$paket_sonuc = $conn->query($sql_paket);


$yorumlar = [];

if ($rol === 'egitmen') {
    $sql_egitmen = "SELECT id FROM egitmenler WHERE user_id = $user_id";
    $egitmen_sonuc = $conn->query($sql_egitmen);
    if ($egitmen_sonuc && $egitmen_sonuc->num_rows > 0) {
        $egitmen = $egitmen_sonuc->fetch_assoc();
        $egitmen_id = $egitmen["id"];

        $sql_yorum = "
            SELECT y.*, u.ad, u.soyad 
            FROM yorumlar y
            JOIN users u ON y.user_id = u.id
            WHERE y.egitmen_id = $egitmen_id
            ORDER BY y.id DESC
        ";
        $yorum_sonuc = $conn->query($sql_yorum);
        if ($yorum_sonuc && $yorum_sonuc->num_rows > 0) {
            while ($y = $yorum_sonuc->fetch_assoc()) {
                $yorumlar[] = $y;
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["paket_iptal_id"])) {
    $iptal_id = intval($_POST["paket_iptal_id"]);
    $sql_sil = "DELETE FROM kullanici_paketleri 
                WHERE id = $iptal_id AND user_id = $user_id";

    if ($conn->query($sql_sil)) {
        $mesaj = "Paket başarıyla iptal edildi.";
    } else {
        $mesaj = "Paket iptal edilirken bir hata oluştu.";
    }
    header("Location: uye.php");
    exit;
}



?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOTUS PİLATES SALONU</title>
    <link rel="icon" href="resimler/logo.jpg" type="image/jpg">
    <link rel="stylesheet" href="style_uye.css">
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
</header>

<div class="mesaj">
    <h2>❤︎ Hoş geldiniz, <span><?php echo $kullanici["ad"] . " " . $kullanici["soyad"]; ?></span>❤︎</h2>
</div>

<?php if ($rol !== 'egitmen'): ?> 
    <h3 class="mesajj" >
            ❤︎ Üye Paneli ❤︎
        </h3>
    <div class="paketbilgisi">
        <h3>Satın Aldığınız Paketler</h3>
        <table>
            <tr>
                <th>Paket Adı</th>
                <th>Kalan Ders</th>
                <th>Paket Fiyatı</th>
                <th>İşlem</th>

            </tr>
            <?php if ($paket_sonuc->num_rows > 0) { ?>
                <?php while($satir = $paket_sonuc->fetch_assoc()) { ?>
                <tr>
                   <td><?php echo $satir["paket_adi"]; ?></td>
                   <td><?php echo $satir["kalan_ders"]; ?></td>
                   <td><?php echo number_format($satir["fiyat"], 2); ?> TL</td>
        <td>
        <?php if ($satir["kalan_ders"] > 0): ?>
            <form method="post" onsubmit="return confirm('Bu paketi iptal etmek istediğinize emin misiniz?');">
                <input type="hidden" name="paket_iptal_id" value="<?php echo $satir['id']; ?>">
                <button type="submit" class="btn" style="background:#c0392b; color:white;">
                    İptal Et
                </button>
            </form>
        <?php else: ?>
            <span style="color:gray;">İptal Edilemez</span>
        <?php endif; ?>
       </td>
     </tr>
       <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="2">Henüz satın aldığınız bir paket yok.</td>
                </tr>
            <?php } ?>
    </table>
</div>

    <div class="panel">
        <a href="paketler.php" class="btn">Paket Satın Al</a>
        <a href="dersler.php" class="btn">Ders Programını Gör</a>
        <a href="cikis.php" class="btn">Çıkış Yap</a>
    </div>

<?php else: ?>
    <div class="paketbilgisi">
        <h3 class="mesajj" style="color:#451032;">
            ❤︎ Eğitmen Paneli ❤︎
        </h3>
    </div>
    <div class="panel" style="text-align:center;">
        <a href="dersler.php" class="btn">Ders Programını Gör</a>
        <a href="cikis.php" class="btn">Çıkış Yap</a>
        <a href="#yorumlar" class="btn">Hakkında Yapılan Yorumları Gör</a>
    </div>

    <div id="yorumlar" class="yorumlar">
        <h3 class="yorum-baslik">❤︎ Hakkınızda Yapılan Yorumlar</h3>
        <?php if (count($yorumlar) > 0): ?>
            <?php foreach ($yorumlar as $y): ?>
              <div class="yorum" >
                <h4><?php echo htmlspecialchars($y["ad"] . " " . $y["soyad"]); ?></h4>
                <p><?php echo htmlspecialchars($y["yorum"]); ?></p>
                <small>Puan: <?php echo $y["puan"]; ?>/5</small>
             </div>
            <?php endforeach; ?>
            <?php else: ?>
               <p style="text-align:center;">Henüz bu eğitmen hakkında yorum yapılmamış.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <script>
           document.querySelector("a[href='#yorumlar']").addEventListener("click", function() {
           const yorumDiv = document.getElementById("yorumlar");
           yorumDiv.style.display = yorumDiv.style.display === "none" || yorumDiv.style.display === "" ? "block" : "none"; });
        </script>
</body>
</html>
