<?php
session_start();
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != "admin") {
    header("Location: giris.php");
    exit;
}

require_once "includes/db.php";

if (isset($_POST['guncelle_egitmen'])) {
    /*type casting yapmam iyi oldu, güvenlik için bir tık daha iyi */
    $id          = (int)$_POST['id']; 
    $user_id     = (int)$_POST['user_id']; 
    $ad          = trim($_POST['ad']);
    $soyad       = trim($_POST['soyad']);
    $email       = trim($_POST['email']);
    $sifre_raw   = trim($_POST['sifre']); 
    $uzmanlik    = trim($_POST['uzmanlik']);
    $bio         = trim($_POST['bio']);
    $eski_resim  = trim($_POST['eski_resim']);
    $resim_adi = $eski_resim;

    if (!empty($_FILES['resim']['name'])) {
        $dosya = $_FILES['resim'];
        $uzanti = strtolower(pathinfo($dosya['name'], PATHINFO_EXTENSION));
        $yeni_ad = "e" . (time() + rand(1,999)) . "." . $uzanti;
        if (move_uploaded_file($dosya['tmp_name'], "resimler/$yeni_ad")) {
            $resim_adi = $yeni_ad;

            if ($eski_resim && file_exists("resimler/$eski_resim")) {
                unlink("resimler/$eski_resim");
            }
        }
    }
    if (!empty($sifre_raw)) {
        $sifre = password_hash($sifre_raw, PASSWORD_DEFAULT);
        $conn->query("
            UPDATE users 
            SET ad='$ad', soyad='$soyad', email='$email', sifre='$sifre'
            WHERE id=$user_id
        ");
    } else {
        $conn->query("
            UPDATE users 
            SET ad='$ad', soyad='$soyad', email='$email'
            WHERE id=$user_id
        ");
    }

    $conn->query("
        UPDATE egitmenler SET
            uzmanlik='$uzmanlik',
            bio='$bio',
            profil_resmi='$resim_adi'
        WHERE id=$id
    ");
    header("Location: admin_egitmen.php");
    exit;
}

if (isset($_POST['ekle_egitmen'])) {

    $ad         = trim($_POST['ad']); /*aslında fonks kullnamayadabılırmısım bu daha basıt unutma  */
    $soyad      = trim($_POST['soyad']);
    $email      = trim($_POST['email']);
    $sifre_raw  = trim($_POST['sifre']);
    $uzmanlik   = trim($_POST['uzmanlik']);
    $bio        = trim($_POST['bio']);
    $resim_adi  = "default.jpg";
    $sifre = password_hash($sifre_raw, PASSWORD_DEFAULT);

    if (!empty($_FILES['resim']['name'])) {
        $dosya = $_FILES['resim'];
        $uzanti = strtolower(pathinfo($dosya['name'], PATHINFO_EXTENSION));
        $yeni_ad = "e" . (time() + rand(1,999)) . "." . $uzanti;

        if (move_uploaded_file($dosya['tmp_name'], "resimler/$yeni_ad")) {
            $resim_adi = $yeni_ad;
        }
    }
    $sql_user = "
        INSERT INTO users (ad, soyad, email, sifre, rol)
        VALUES ('$ad', '$soyad', '$email', '$sifre', 'egitmen')
    ";
    if (!$conn->query($sql_user)) {
        die("USER oluşturulamadı: " . $conn->error);
    }
    $user_id = $conn->insert_id;
    $sql_e = "
        INSERT INTO egitmenler (user_id, uzmanlik, bio, profil_resmi)
        VALUES ($user_id, '$uzmanlik', '$bio', '$resim_adi')
    ";
    $conn->query($sql_e);
    header("Location: admin_egitmen.php");
    exit;
}

if (isset($_POST['sil_egitmen'])) {
    /*popup eklemeyı unutma */
    $id = (int)$_POST['sil_id'];
    $row = $conn->query("SELECT user_id, profil_resmi FROM egitmenler WHERE id=$id")
                ->fetch_assoc();
    $user_id = $row['user_id'];
    $resim   = $row['profil_resmi'];
    if ($resim && file_exists("resimler/$resim"))
     {
        unlink("resimler/$resim");
    }
    $conn->query("DELETE FROM egitmenler WHERE id=$id");
    $conn->query("DELETE FROM users WHERE id=$user_id");
    header("Location: admin_egitmen.php");
    exit;
}

$sorgu = $conn->query("
    SELECT e.*, u.ad, u.soyad, u.email 
    FROM egitmenler e
    LEFT JOIN users u ON u.id = e.user_id
    ORDER BY e.id DESC
");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<link rel="icon" href="resimler/logo.jpg" type="image/jpg">
<link rel="stylesheet" href="admin_egit.css">
<title>Eğitmen Yönetimi | Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Oranienbaum&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>
<header>
    <div class="logo">PİLATES BY ELA</div>
    <a href="admin.php" class="back">Admin Paneline Geri Dön</a>
</header>
<div class="container">
<h1>❤︎ Eğitmen Yönetimi ❤︎</h1>
<div class="kategori">
<h2>Mevcut Eğitmenler</h2>
<div class="liste">
<?php while($e = $sorgu->fetch_assoc()): ?>
    <div class="item">
        <?php if(file_exists("resimler/".$e['profil_resmi'])): ?>
            <img src="resimler/<?= $e['profil_resmi'] ?>">
        <?php else: ?>
            <div style="width:80px;height:80px;background:#333;border-radius:50%;border:3px solid #d4af37;"></div>
        <?php endif; ?>
        <div class="info">
            <strong><?= htmlspecialchars($e['ad']." ".$e['soyad']) ?> (<?= $e['uzmanlik'] ?>)</strong>
            <small><?= $e['email'] ?></small>
            <small><?= substr($e['bio'],0,120) ?>...</small>
        </div>
        <button onclick="document.getElementById('form-<?= $e['id'] ?>').style.display='block';" 
                class="btn btn-yesil">Düzenle</button>
        <form method="POST">
            <input type="hidden" name="sil_id" value="<?= $e['id'] ?>">
            <button class="btn btn-kirmizi" name="sil_egitmen">Sil</button>
        </form>
    </div>

    <div id="form-<?= $e['id'] ?>" class="duzenle-form" style="display:none;">
        <form method="POST" enctype="multipart/form-data" style="width:100%;">
            <input type="hidden" name="id" value="<?= $e['id'] ?>">
            <input type="hidden" name="user_id" value="<?= $e['user_id'] ?>">
            <input type="hidden" name="eski_resim" value="<?= $e['profil_resmi'] ?>">
            <input type="text" name="ad" value="<?= $e['ad'] ?>" placeholder="Ad" required>
            <input type="text" name="soyad" value="<?= $e['soyad'] ?>" placeholder="Soyad" required>
            <input type="email" name="email" value="<?= $e['email'] ?>" required>
            <input type="password" name="sifre" placeholder="Yeni şifre">
            <input type="text" name="uzmanlik" value="<?= $e['uzmanlik'] ?>" required>
            <textarea name="bio" required><?= $e['bio'] ?></textarea>
            <input type="file" name="resim" accept="image/*">
            <small style="color:#d4af37;width:100%;">Mevcut resim: <?= $e['profil_resmi'] ?></small>
            <button type="submit" name="guncelle_egitmen" class="gunc">Güncelle</button>
        </form>

    </div>
<?php endwhile; ?>
</div>

</div>
<div class="kategori">
<h2>Yeni Eğitmen Ekle</h2>

<form method="POST" enctype="multipart/form-data" class="yeni">
    <input type="text" name="ad" placeholder="Ad" required>
    <input type="text" name="soyad" placeholder="Soyad" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="sifre" placeholder="Şifre" required>
    <input type="text" name="uzmanlik" placeholder="Uzmanlık" required>
    <textarea name="bio" placeholder="Biyografi" required></textarea>
    <input type="file" name="resim" accept="image/*">
    <button type="submit" name="ekle_egitmen">Eğitmen Ekle</button>
</form>
</div>
</div>
</body>
</html>
