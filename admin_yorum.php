<?php
session_start();
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != "admin") {
    header("Location: giris.php");
    exit;
}
require_once "includes/db.php";

function p($key) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : '';
}

if (isset($_POST['ekle_yorum'])) {
    $user_id  = (int)p('user_id');
    $egitmen_id = (int)p('egitmen_id');
    $puan     = (int)p('puan');
    $yorum    = p('yorum');

    $conn->query("
        INSERT INTO yorumlar (user_id, egitmen_id, puan, yorum, olusturulma_tarihi)
        VALUES ($user_id, $egitmen_id, $puan, '$yorum', NOW())
    ");
    header("Location: admin_yorum.php");
    exit;
}

if (isset($_POST['guncelle_yorum'])) {
    $id   = (int)p('id');
    $puan = (int)p('puan');
    $yorum = p('yorum');

    $conn->query("
        UPDATE yorumlar SET 
        puan=$puan,
        yorum='$yorum'
        WHERE id=$id
    ");

    header("Location: admin_yorum.php");
    exit;
}

if (isset($_POST['sil_yorum'])) {
    $id = (int)p('sil_id');
    $conn->query("DELETE FROM yorumlar WHERE id=$id");
    header("Location: admin_yorum.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Yorum Yönetimi | Admin</title>
<link rel="stylesheet" href="admin_yorum.css">
<link rel="icon" href="resimler/logo.jpg">
<link href="https://fonts.googleapis.com/css2?family=Oranienbaum&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <div class="logo">PİLATES BY ELA</div>
    <a href="admin.php" class="back">Geri Dön</a>
</header>

<div class="container">

<h1 style="text-align:center;color:#d4af37;margin-bottom:30px;">Yorum Yönetimi</h1>

<div class="kutu">
    <h2 style="color:#d4af37;margin-bottom:15px;">Tüm Yorumlar</h2>

<?php
$yorumlar = $conn->query("
    SELECT 
        yorumlar.*,
        users.ad AS kullanici_ad,
        users.soyad AS kullanici_soyad,
        e_users.ad AS egitmen_ad,
        e_users.soyad AS egitmen_soyad
        FROM yorumlar
        LEFT JOIN users 
        ON users.id = yorumlar.user_id
        LEFT JOIN egitmenler 
        ON egitmenler.id = yorumlar.egitmen_id
        LEFT JOIN users AS e_users
        ON e_users.id = egitmenler.user_id
        ORDER BY yorumlar.id DESC
");

while($y = $yorumlar->fetch_assoc()):
?>
    <div class="yorum">
        <strong><?= $y['kullanici_ad'] . " " . $y['kullanici_soyad'] ?></strong>
        <div style="color:#bbb; font-size:14px; margin-top:5px;">
            Eğitmen: <?= $y['egitmen_ad'] . " " . $y['egitmen_soyad'] ?>
        </div>
        <span class="yildiz">
            <?php for($i=1;$i<=5;$i++) echo ($i <= $y['puan'] ? "★" : "☆"); ?>
        </span>
        <div style="margin-top:10px;"><?= nl2br(htmlspecialchars($y['yorum'])) ?></div>
        <small style="color:#999;"><?= $y['olusturulma_tarihi'] ?></small>
        <div style="margin-top:12px;display:flex;gap:8px;">
            <button class="btn btn-duzen" onclick="document.getElementById('edit<?= $y['id'] ?>').style.display='block'">Düzenle</button>
            <form method="POST">
                <input type="hidden" name="sil_id" value="<?= $y['id'] ?>">
                <button class="btn btn-sil" name="sil_yorum" onclick="return confirm('Silinsin mi?')">Sil</button>
            </form>
        </div>
        <div id="edit<?= $y['id'] ?>" style="display:none;margin-top:15px;">
            <form method="POST">
                <input type="hidden" name="id" value="<?= $y['id'] ?>">
                <label style="color:#d4af37;">Puan:</label>
                <select name="puan">
                    <?php for($i=1;$i<=5;$i++): ?>
                        <option value="<?= $i ?>" <?= ($i==$y['puan']) ? 'selected' : '' ?>><?= $i ?> Yıldız</option>
                    <?php endfor; ?>
                </select>
                <label style="color:#d4af37;">Yorum:</label>
                <textarea name="yorum" rows="3"><?= htmlspecialchars($y['yorum']) ?></textarea>
                <button name="guncelle_yorum" class="btn btn-kaydet" style="margin-top:10px;">Kaydet</button>
            </form>
        </div>

    </div>
<?php endwhile; ?>
</div>
</div>
</body>
</html>
