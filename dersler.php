<?php
session_start();
require_once "includes/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: giris.php");
    exit;
}

$kullanici_id = $_SESSION["user_id"];

$sql_kullanici = "SELECT * FROM users WHERE id = $kullanici_id";
$kullanici_sonuc = $conn->query($sql_kullanici);

if ($kullanici_sonuc && $kullanici_sonuc->num_rows > 0) {
    $kullanici = $kullanici_sonuc->fetch_assoc();
} else {
    echo "Kullanıcı bilgileri bulunamadı.";
    exit;
}

$egitmen_ozet = [
    "ders_sayisi" => 0,
    "toplam_kapasite" => 0,
    "toplam_doluluk" => 0
];
/* eğer giriş yapan kişi eğitmense dersleri, kapasitesi falan çekiyorum */
if ($kullanici['rol'] === 'egitmen') {
    $sql_egitmen_id = "SELECT id FROM egitmenler WHERE user_id = $kullanici_id LIMIT 1";
    $egitmen_sonuc = $conn->query($sql_egitmen_id);
    if ($egitmen_sonuc && $egitmen_sonuc->num_rows > 0) {
    $satir = $egitmen_sonuc->fetch_assoc();
    $egitmen_id = $satir['id'];
    } else {
    $egitmen_id = 0;
    }

    $sql_istatistik = "SELECT 
                          COUNT(*) AS ders_sayisi,
                          COALESCE(SUM(kapasite),0) AS toplam_kapasite,
                          COALESCE(SUM(doluluk),0) AS toplam_doluluk
                      FROM ders_programi
                      WHERE egitmen_id = $egitmen_id";
    /*coalesce kullanmamın sebebı null gelırse 0 koyması ıcın, guvenlık ıcın koydum  */
    $istatistik_sonuc = $conn->query($sql_istatistik);
    if ($istatistik_sonuc && $istatistik_sonuc->num_rows > 0) {
        $egitmen_ozet = $istatistik_sonuc->fetch_assoc();
    }

    $sql_dersler = "SELECT dp.*, d.ders_adi
                    FROM ders_programi dp
                    JOIN dersler d ON dp.ders_id = d.id
                    WHERE dp.egitmen_id = $egitmen_id
                    ORDER BY dp.tarih, dp.baslangic_saati";
}
 else {
$sql_paketler = "SELECT DISTINCT p.egitmen_id
                 FROM kullanici_paketleri kp
                 JOIN paketler p ON kp.paket_id = p.id
                 WHERE kp.user_id = $kullanici_id
                   AND kp.kalan_ders > 0";

$paket_sonuc = $conn->query($sql_paketler);

$egitmen_idler = [];
if ($paket_sonuc && $paket_sonuc->num_rows > 0) {
    while ($satir = $paket_sonuc->fetch_assoc()) {
        $egitmen_idler[] = $satir['egitmen_id'];
    }
}

if (count($egitmen_idler) > 0) {
    $egitmen_idler_str = implode(',', $egitmen_idler); /*tuple gıbı yapıyoruz aslında bu fonk baya ısımı gordu */
    $sql_dersler = "SELECT dp.*, d.ders_adi
                    FROM ders_programi dp
                    JOIN dersler d ON dp.ders_id = d.id
                    WHERE dp.egitmen_id IN ($egitmen_idler_str)
                    ORDER BY dp.tarih, dp.baslangic_saati";
} else {
    $sql_dersler = null;
}

}

$ders_sonuc = $sql_dersler ? $conn->query($sql_dersler) : null;
$giris_yapildi = isset($_SESSION["user_id"]);
$baglan = new mysqli("localhost", "root", "", "db_pilates");

if ($baglan->connect_error) {
    die("Bağlantı hatası: " . $baglan->connect_error);
}

$sql_profil = "SELECT profil_resmi FROM egitmenler WHERE user_id = $kullanici_id LIMIT 1";
$profil_sonuc = $conn->query($sql_profil);
$profil_resim = ($profil_sonuc && $profil_sonuc->num_rows > 0) 
    ? $profil_sonuc->fetch_assoc()['profil_resmi'] 
    : null;

if (isset($_SESSION['mesaj'])) {
    $mesaj = $_SESSION['mesaj'];
} else {
    $mesaj = "";
}

if (isset($_SESSION['mesaj_tur'])) {
    $mesaj_tur = $_SESSION['mesaj_tur'];
} else {
    $mesaj_tur = "";
}

unset($_SESSION['mesaj'], $_SESSION['mesaj_tur']);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ders Programı</title>
    <link rel="icon" href="resimler/logo.jpg" type="image/jpg">
    <link rel="stylesheet" href="style_dersler.css?v=2">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
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
<?php if($mesaj): ?>
    <div class="mesaj <?php echo $mesaj_tur; ?>">
        <?php echo htmlspecialchars($mesaj); ?>
        <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
    </div>
<?php endif; ?>

<div class="icerik-kapsayici">
    <?php if ($kullanici['rol'] === 'egitmen'): ?>
    <div class="egitmen-ozet">
        <div class="profil-kapsayici">
    <?php if ($profil_resim): ?>
        <img src="resimler/<?php echo htmlspecialchars($profil_resim); ?>" alt="Profil Resmi" class="profil-resim">
    <?php else: ?>
        <i class="fas fa-chalkboard-teacher profil-icon"></i>
    <?php endif; ?>
</div>
        <div class="bilgi-kisim">
            <h2><?php echo htmlspecialchars($kullanici["ad"] . " " . $kullanici["soyad"]); ?></h2>
            <p>
                Toplam Ders: <strong><?php echo $egitmen_ozet["ders_sayisi"]; ?></strong> |
                Kapasite: <strong><?php echo $egitmen_ozet["toplam_kapasite"]; ?></strong> |
                Doluluk: <strong><?php echo $egitmen_ozet["toplam_doluluk"]; ?></strong>
            </p>
        </div>
    </div>
    <?php endif; ?>

    <h2 class="sayfa-baslik">Ders Programı</h2>

    <?php if ($ders_sonuc && $ders_sonuc->num_rows > 0): ?>
        <table class="ders-tablo">
            <tr>
                <th>Ders Adı</th>
                <th>Tarih</th>
                <th>Başlangıç</th>
                    <th>Bitiş</th>

                <th>Kapasite</th>
                <th>Doluluk</th>
                <?php if ($kullanici['rol'] !== 'egitmen'): ?><th>İşlem</th><?php endif; ?>
            </tr>

            <?php while ($ders = $ders_sonuc->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($ders["ders_adi"]); ?></td>
                <td><?php echo htmlspecialchars($ders["tarih"]); ?></td>
                <td><?php echo htmlspecialchars($ders["baslangic_saati"]); ?></td>
                    <td><?php echo htmlspecialchars($ders["bitis_saati"]); ?></td>

                <td><?php echo htmlspecialchars($ders["kapasite"]); ?></td>
                <td><?php echo htmlspecialchars($ders["doluluk"]); ?></td>
                <?php if ($kullanici['rol'] !== 'egitmen'): ?>
                    <td>
                        <?php if ($ders['doluluk'] < $ders['kapasite']): ?>
<a href="derse_katil.php?ders_id=<?php echo $ders['id']; ?>" class="katil-buton">Katıl</a>                        <?php else: ?>
                            <span class="ders-dolu">Ders Dolu</span>
                        <?php endif; ?>
                    </td>
                <?php endif; ?>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p class="bilgi-yazi">Şu anda gösterilecek ders bulunmamaktadır.</p>
    <?php endif; ?>

    <div class="alt-butonlar">
        <?php if ($kullanici['rol'] === 'egitmen'): ?>
            <a href="uye.php" class="panel-buton">Eğitmen Paneline Dön</a>
        <?php else: ?>
            <a href="uye.php" class="panel-buton">Üye Paneline Dön</a>
        <?php endif; ?>
        <a href="index.php" class="panel-buton ikincil">Anasayfaya Dön</a>
    </div>
</div>

</body>
</html>
