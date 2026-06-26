<?php
session_start();
require_once "includes/db.php";


$giris_yapildi = isset($_SESSION["user_id"]);
$baglan = new mysqli("localhost", "root", "", "db_pilates");

if ($baglan->connect_error) {
    die("Bağlantı hatası: " . $baglan->connect_error);
}
?>

<!doctype html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HAKKIMIZDA</title>
    <link rel="icon" href="resimler/logo.jpg" type="image/jpg">
    <link rel="stylesheet" href="stylehakk.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
    <h2>❤︎ LOTUS PİLATES SALONU ❤︎</h2>
    <div class="hakkimizda-galeri">
        <div class="hakkimizda-kutu">
            <img src="resimler/salon1.jpg" alt="Salon Fotoğrafı 1">
        </div>
        <div class="hakkimizda-kutu">
            <img src="resimler/salon2.jpg" alt="Salon Fotoğrafı 2">
        </div>
        <div class="hakkimizda-kutu">
            <img src="resimler/salon3.jpg" alt="Salon Fotoğrafı 3">
        </div>
    </div>
<div class="hakkimizda-icerik">
        <h3>Neden LOTUS PİLATES SALONU?</h3>

        <div class="hakkimizda-ozetler">
            <div class="hakk-kutu">
                <h4>❤︎ 15 Uzman Eğitmen</h4>
                <p>
                    Her biri kendi alanında uzman, ulusal ve uluslararası 
                    sertifikalara sahip 15 eğitmen ile çalışıyoruz. 
                    Her seviye için ayrı programlar, kişiye özel ilgi,
                    sakatlık ve duruş bozukluklarına özel destek sunuyoruz.
                </p>
            </div>

            <div class="hakk-kutu">
                <h4>❤︎ 15 Farklı Stüdyo Alanı</h4>
                <p>
                    Geniş grup ders salonları, butik reformer stüdyoları, 
                    birebir özel ders odaları ve esneme–relaks alanları ile 
                    toplam 15 farklı salon seçeneği sunuyoruz. Her salonda 
                    doğal ışık, havalandırma ve konforlu zemin kaplamaları bulunur.
                </p>
            </div>

            <div class="hakk-kutu">
                <h4>❤︎ Profesyonel Ekipmanlar</h4>
                <p>
                    Reformer, Cadillac, Chair, Barrel, mat pilates alanları, 
                    pilates topları, direnç bantları, circle ring, foam roller 
                    ve daha fazlası… Tüm ekipmanlarımız profesyonel stüdyo 
                    standartlarındadır ve düzenli olarak yenilenir.
                </p>
            </div>

            <div class="hakk-kutu">
                <h4>❤︎ Esnek Ders Programları</h4>
                <p>
                    Sabah, öğle ve akşam saatlerinde, hafta içi ve hafta sonu 
                    seçenekleriyle esnek ders saatleri sunuyoruz. 
                    Katıldığınız paketlere göre eğitmenlerimizin programını 
                    inceleyebilir, size en uygun saatte derse katılabilirsiniz.
                </p>
            </div>

            <div class="hakk-kutu">
                <h4>❤︎ Konforlu Salon Deneyimi</h4>
                <p>
                    Klima, ferah soyunma odaları, duş alanları, kilitli dolaplar, 
                    bekleme ve dinlenme alanları ile tüm detaylar sizin için düşünüldü. 
                    Sıcak bir karşılama, güler yüzlü ekip ve huzurlu bir atmosfer 
                    LOTUS PİLATES SALONU'nun vazgeçilmezidir.
                </p>
            </div>

            <div class="hakk-kutu">
                <h4>❤︎ Hedef Odaklı Çalışma</h4>
                <p>
                    Kilo verme, sıkılaşma, esneme, duruş bozukluğu, bel–boyun 
                    rahatsızlıkları, doğum sonrası toparlanma ve daha birçok hedef 
                    için size özel programlar oluşturmaktayız. Düzenli ölçüm ve 
                    gelişim takibi ile süreci birlikte yönetiyoruz.
                </p>
            </div>
        </div>
    </div>

<script src="script.js"></script>
</body>
</html>
