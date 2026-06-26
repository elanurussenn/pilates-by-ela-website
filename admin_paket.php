<?php
session_start();
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != "admin") {
    header("Location: giris.php");
    exit;
}

require_once "includes/db.php";

function p($key) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : '';
} /*bunu dırekt trım ıle de yaparım aslında bunu sılebılırım aklımda bulunsun */
$seviyeler = $conn->query("SELECT * FROM seviyeler ORDER BY id ASC");

$egitmenler = $conn->query("
    SELECT e.id AS egitmen_id, u.ad, u.soyad 
    FROM egitmenler e
    JOIN users u ON e.user_id = u.id
    ORDER BY u.ad ASC
");

if (isset($_POST['ekle_paket'])) {

    $baslik       = p('baslik');
    $fiyat        = (float)p('fiyat');
    $seans_sayisi = (int)p('seans_sayisi');
    $aciklama     = p('aciklama');
    $slogan       = p('slogan');
    $seviye       = (int)p('seviye');
    $egitmen_id   = (int)p('egitmen_id');
    $one_cikan    = (int)p('one_cikan');

    $resim_adi = "default_paket.jpg";

    if (!empty($_FILES['resim']['name'])) {
        $dosya  = $_FILES['resim'];
        $uzanti = strtolower(pathinfo($dosya['name'], PATHINFO_EXTENSION));
        $yeni_ad = "paket_" . time() . "_" . rand(1,9999) . "." . $uzanti;

        move_uploaded_file($dosya['tmp_name'], "resimler/" . $yeni_ad);
        $resim_adi = $yeni_ad;
    }

    $conn->query("
        INSERT INTO paketler 
        (paket_adi, fiyat, ders_sayisi, aciklama, resim, seviye, slogan, egitmen_id, one_cikan)
        VALUES 
        ('$baslik', '$fiyat', '$seans_sayisi', '$aciklama', '$resim_adi', '$seviye', '$slogan', '$egitmen_id', '$one_cikan')
    ");

    header("Location: admin_paket.php");
    exit;
}

if (isset($_POST['guncelle_paket'])) {

    $id           = (int)p('id');
    $baslik       = p('baslik');
    $fiyat        = (float)p('fiyat');
    $seans_sayisi = (int)p('seans_sayisi');
    $aciklama     = p('aciklama');
    $eski_resim   = p('eski_resim');
    $slogan       = p('slogan');
    $seviye       = (int)p('seviye');
    $egitmen_id   = (int)p('egitmen_id');
    $one_cikan    = (int)p('one_cikan');

    $resim_adi = $eski_resim;

    if (!empty($_FILES['resim']['name'])) {
        $dosya  = $_FILES['resim'];
        $uzanti = strtolower(pathinfo($dosya['name'], PATHINFO_EXTENSION));
        $yeni_ad = "paket_" . time() . "_" . rand(1,9999) . "." . $uzanti;
        move_uploaded_file($dosya['tmp_name'], "resimler/" . $yeni_ad);

        $resim_adi = $yeni_ad;

        if ($eski_resim && $eski_resim !== "default_paket.jpg" && file_exists("resimler/".$eski_resim)) {
            unlink("resimler/".$eski_resim);
        }
    }

    $conn->query("
        UPDATE paketler SET 
            paket_adi='$baslik',
            fiyat='$fiyat',
            ders_sayisi='$seans_sayisi',
            aciklama='$aciklama',
            resim='$resim_adi',
            seviye='$seviye',
            slogan='$slogan',
            egitmen_id='$egitmen_id',
            one_cikan='$one_cikan'
        WHERE id=$id
    ");

    header("Location: admin_paket.php");
    exit;
}

if (isset($_POST['sil_paket'])) {
    $id = (int)$_POST['sil_id'];

    $res = $conn->query("SELECT resim FROM paketler WHERE id=$id")->fetch_assoc()['resim'] ?? '';
    if ($res && $res !== "default_paket.jpg" && file_exists("resimler/".$res)) {
        unlink("resimler/".$res);
    }

    $conn->query("DELETE FROM paketler WHERE id=$id");
    header("Location: admin_paket.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="admin_yor.css">
    <link rel="icon" href="resimler/logo.jpg">
    <title>Paket Yönetimi | Admin</title>
</head>
<body>

<header>
    <div class="logo">PİLATES BY ELA</div>
    <a href="admin.php" class="back">Admin Paneline Dön</a>
</header>

<div class="container">

    <h1>❤︎ Paket Yönetimi ❤︎</h1>

    <div class="kategori">
        <h2>Mevcut Paketler</h2>

        <div class="liste">
            <?php
            $sorgu = $conn->query("SELECT * FROM paketler ORDER BY fiyat ASC");
            while($p = $sorgu->fetch_assoc()):
            ?>
            <div class="item">

                <?php if(!empty($p['resim']) && file_exists("resimler/".$p['resim'])): ?>
                    <img src="resimler/<?= $p['resim'] ?>">
                <?php else: ?>
                    <div class="no-image">Resim yok</div>
                <?php endif; ?>

                <strong><?= htmlspecialchars($p['paket_adi']) ?></strong>
                <div class="fiyat"><?= number_format($p['fiyat'], 2, ',', '.') ?> ₺</div>
                <div class="seans"><?= $p['ders_sayisi'] ?> Ders</div>

                <small><?= htmlspecialchars(substr($p['aciklama'], 0, 80)) ?>...</small>

                <div class="btnlar">
                    <button onclick="document.getElementById('form-<?= $p['id'] ?>').style.display='block';" 
                            class="btn btn-yesil">Düzenle</button>

                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="sil_id" value="<?= $p['id'] ?>">
                        <button type="submit" name="sil_paket" class="btn btn-kirmizi"
                                onclick="return confirm('Bu paket silinsin mi?')">Sil</button>
                    </form>
                </div>

            </div>

            <div id="form-<?= $p['id'] ?>" class="duzenle-form">

                <?php 
                $seviyeler2 = $conn->query("SELECT * FROM seviyeler");
                $egitmenler2 = $conn->query("
                    SELECT e.id AS egitmen_id, u.ad, u.soyad 
                    FROM egitmenler e
                    JOIN users u ON e.user_id = u.id
                    ORDER BY u.ad ASC
                ");
                ?>

                <form method="POST" enctype="multipart/form-data">

                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <input type="hidden" name="eski_resim" value="<?= $p['resim'] ?>">
                    <input type="text" name="baslik" value="<?= htmlspecialchars($p['paket_adi']) ?>" required>
                    <input type="number" name="fiyat" value="<?= $p['fiyat'] ?>" required>
                    <input type="number" name="seans_sayisi" value="<?= $p['ders_sayisi'] ?>" required>
                    <select name="seviye" required>
                        <?php while($s = $seviyeler2->fetch_assoc()): ?>
                            <option value="<?= $s['id'] ?>" <?= $p['seviye']==$s['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['seviye_adi']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <input type="text" name="slogan" value="<?= htmlspecialchars($p['slogan']) ?>" required>
                    <select name="egitmen_id" required>
                        <?php while($e = $egitmenler2->fetch_assoc()): ?>
                            <option value="<?= $e['egitmen_id'] ?>" 
                                <?= $p['egitmen_id']==$e['egitmen_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($e['ad'] . " " . $e['soyad']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <textarea name="aciklama" required><?= htmlspecialchars($p['aciklama']) ?></textarea>
                    <input type="file" name="resim" accept="image/*">
                    <button type="submit" name="guncelle_paket" class="gnc" style=" padding: 10px 20px;">Güncelle</button>
                </form>
            </div>
            <?php endwhile; ?>

        </div>
    </div>
    <div class="kategori">
        <h2>Yeni Paket Ekle</h2>

        <form method="POST" enctype="multipart/form-data" class="yeni">
            <input type="text" name="baslik" placeholder="Paket Başlığı" required>
            <input type="number" name="fiyat" placeholder="Fiyat (TL)" required>
            <input type="number" name="seans_sayisi" placeholder="Seans Sayısı" required>
            <select name="seviye" required>
                <option value="">Seviye Seçin</option>
                <?php 
                /* NOT: data_seek kullanmak zorundayım çünkü aynı result set'i 2 kere döneceğim.
                   Aslında bunu baştan array'e çekip foreach ile çözmek daha temiz olur unutma ba bır */
                $seviyeler->data_seek(0);
                while($s = $seviyeler->fetch_assoc()): ?>
                    <option value="<?= $s['id'] ?>">
                        <?= htmlspecialchars($s['seviye_adi']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <input type="text" name="slogan" placeholder="Slogan" required>
            <select name="egitmen_id" required>
                <option value="">Eğitmen Seçin</option>
                <?php 
                $egitmenler->data_seek(0);
                while($e = $egitmenler->fetch_assoc()): ?>
                    <option value="<?= $e['egitmen_id'] ?>">
                        <?= htmlspecialchars($e['ad'] . " " . $e['soyad']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <textarea name="aciklama" placeholder="Paket açıklaması..." required></textarea>
            <input type="file" name="resim" accept="image/*">
            <button type="submit" name="ekle_paket">Paket Ekle</button>
        </form>
    </div>
</div>
</body>
</html>
