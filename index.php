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
    <title>LOTUS PİLATES SALONU</title>
    <link rel="icon" href="resimler/logo.jpg" type="image/jpg">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oranienbaum&display=swap" rel="stylesheet">
</head>
<body>
    <div class="backgrounddiv">
        <div class="metin">
            <p>"KENDİNİZİ DAHA İYİ HİSSEDİN, DAHA GÜÇLÜ HİSSEDİN"</p>
        </div>
    </div>
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
    <div class="metin2">
        Akıllı hareketlerle hayatınızı değiştirin. Klasik yönteme dayanan ve amaca yönelik olarak tasarlanan ELANUR USŞEN tarafından 2025'te kurulan LOTUS Pilates, içten dışa güç veren; kendinizi daha iyi hissetmenizi, daha iyi hareket etmenizi ve gelecekteki benliğinize adım atmanızı sağlayan bir uygulamadır.
    </div>
    <div class="resimler">
        <div class="resimlerdiv">
            <img src="resimler/div.jpg" alt="Foundation">
            <div class="resimlerdivmetin">YAŞAM</div>
        </div>
        <div class="resimlerdiv">
            <img src="resimler/div1.jpg" alt="Strength">
            <div class="resimlerdivmetin">GÜÇ</div>
        </div>
        <div class="resimlerdiv">
            <img src="resimler/div2.jpg" alt="Transform">
            <div class="resimlerdivmetin">DEĞİŞİM</div>
        </div>
    </div>
    <div class="metin3">
       Lotus Pilates’te bedeninizi yeniden keşfederken; dinginlik, güç ve dengeyi bir arada bulacaksınız. 
       Modern ekipmanlar, uzman eğitmenler ve kişiye özel yaklaşım ile kendinizi daha güçlü ve daha özgüvenli hissetmeye davet ediyoruz.
   </div>
   <div class="videodiv">
     <video controls autoplay muted loop>
        <source src="resimler/video1.mp4" type="video/mp4">
        Tarayıcınız video elementini desteklemiyor.
     </video>
     <div class="videometin">LOTUS</div>
    </div>
    <div class="metin4">
      <h2>KISACA BİZ</h2>
      <p>
        Pilates’e tutkuyla bağlı, alanında uzman eğitmenlerden oluşan bir ekibiz. 
        Amacımız; bedeninizi güvenle güçlendirmek, zihninizi özgürleştirmek ve 
        kendinize ayırdığınız bu özel zamanda size ilham vermektir. Her üyemize 
        birebir ilgi gösterir, bedenin içindeki sporcuyu ortaya çıkarırken 
        sınırlarınızı güvenle keşfetmenizi sağlarız.
      </p>
      <p>
        Modern tasarımın, ritmin ve hareketin birleştiği stüdyomuzda; sizi 
        hedeflerinize ulaştıracak farklı yaklaşımlar sunuyoruz. Güçlü, dengeli 
        ve daha farkında bir “siz” yaratma yolculuğunuzda yanınızda olmaktan 
        heyecan duyuyoruz.
      </p>
      <div class="incele-btn-container">
        <a href="hakkimizda.php" class="incele-btn">Detaylı İncele</a>
      </div>
   </div>
   <div class="one-cikan-paketler" id="paketler">
    <h2 class="baslik">-SİZE UYGUN SEVİYEYİ KEŞFEDİN-</h2>

    <div class="slider-wrapper">
    <div class="one-cikan-slider">
        <?php
        $sorgu = $baglan->query("SELECT * FROM seviyeler ORDER BY id ASC");
        while ($seviye = $sorgu->fetch_assoc()) {
            $resimYol = "resimler/" . $seviye['resim'];
            echo '
            <div class="one-cikan-card">
                <a href="paketler.php?seviye='.$seviye['id'].'">
                    <img src="'.$resimYol.'" alt="'.$seviye['seviye_adi'].'">
                    <div class="one-cikan-text">
                        <h3>'.$seviye['seviye_adi'].'</h3>
                        <span>'.$seviye['aciklama'].'</span>
                    </div>
                </a>
            </div>
            ';
        }
        ?>
    </div>
    </div>
    <button class="slider-btn left">&#10094;</button>
    <button class="slider-btn right">&#10095;</button>
    </div>
<footer class="footer">
    <div class="footer-icerik">
        <div class="footer-sol">
            <div class="footer-logo">
                <img src="RESIMLER/logo.jpg" alt="Lotus Pilates Logo" style="height:60px;">
                <h3>PİLATES BY ELA </h3>
            </div>
            <p class="footer-aciklama">
                Kendinizi daha iyi hissedin, daha güçlü hissedin.<br>
                2025’te Elanur Usşen tarafından kurulan LOTUS Pilates.
            </p>
        </div>
        <div class="footer-orta">
            <h4>İLETİŞİM</h4>
            <ul class="iletisim-liste">
                <li>
                    <i class="fas fa-map-marker-alt"></i>
                    Nişantaşı, Vali Konağı Cd. No:62 Kat:3<br>
                    Şişli / İstanbul
                </li>
                <li>
                    <i class="fas fa-phone"></i>
                    <a href="tel:+905432112233">0543 211 22 33</a>
                </li>
                <li>
                    <i class="fab fa-whatsapp"></i>
                    <a  target="_blank">WhatsApp ile Ulaş</a>
                </li>
                <li>
                    <i class="fas fa-envelope"></i>
                    <a href="mailto:info@lotuspilates.com">info@lotuspilates.com</a>
                </li>
            </ul>
            <p class="calisma-saatleri">
                <strong>Çalışma Saatleri</strong><br>
                Pazartesi – Cumartesi: 07:00 – 22:00<br>
                Pazar: 09:00 – 18:00
            </p>
        </div>
        <div class="footer-sag">
            <h4>NERDEYİZ?</h4>
            <div class="harita">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3009.458467889412!2d28.989547!3d41.053421!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cab1f4e8f2b3b7%3A0x8f8e9c8b9a9b8c7d!2sVali%20Kona%C4%9F%C4%B1%20Cd.%2062%2C%2034367%20%C5%9Ei%C5%9Fli%2F%C4%B0stanbul!5e0!3m2!1str!2str!4v1734000000000" 
                    width="100%" 
                    height="180" 
                    style="border:0; border-radius:10px;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>
            <div class="sosyal-medya">
                <a  target="_blank" class="sosyal-ikon">
                    <i class="fab fa-instagram"></i>
                </a>
                <a target="_blank" class="sosyal-ikon">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <a  target="_blank" class="sosyal-ikon">
                    <i class="fab fa-tiktok"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="footer-alt">
        <p>© 2025 LOTUS Pilates by Ela – Tüm hakları saklıdır.</p>
    </div>
</footer>
<script src="script.js"></script>
</body>
</html>
