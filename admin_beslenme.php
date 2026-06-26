<?php
session_start();
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != "admin") {
    header("Location: giris.php");
    exit;
} /*bu sayfaya yalnızca admin girebilir ondan bu kontrolu sagladım*/

require_once "includes/db.php";

if (isset($_POST['ekle_besin']))
   {
    $adi = $_POST['adi'];
    $kat = $_POST['kategori_id'];
    $ack = $_POST['aciklama'];
    $kal = $_POST['kalori'];
    $resim_adi = "default.jpg";
    
    if (!empty($_FILES['resim']['name']) && $_FILES['resim']['error'] === UPLOAD_ERR_OK) 
        {
        $dosya  = $_FILES['resim'];
        $uzanti = pathinfo($dosya['name'], PATHINFO_EXTENSION);
        $yeni_ad = time() . "_" . rand(1, 9999) . "." . strtolower($uzanti);
        if (move_uploaded_file($dosya['tmp_name'], "resimler/" . $yeni_ad))
        {
            $resim_adi = $yeni_ad; 
        }
    }
    $sql = "INSERT INTO beslenme_ogeleri 
            (adi, kategori_id, açıklama, resim, kalori)
            VALUES ('$adi', '$kat', '$ack', '$resim_adi', '$kal')";
    if (!$conn->query($sql)) {
        die("Ekleme hatası: " . $conn->error);
    }
    header("Location: " . $_SERVER['PHP_SELF']); /*sayfayı yenıleyınce tekrardan post istegi gonderılmesın*/
    exit;
}

if (isset($_POST['guncelle_kat'])) {
    $id  = $_POST['id'];
    $adi = $_POST['kategori_adi'];
    $sql = "UPDATE beslenme_kategorileri SET kategori_adi='$adi' WHERE id=$id";
    if (!$conn->query($sql)) {
        die("Kategori güncelleme hatası: " . $conn->error);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['sil_kat'])) {
    $id = $_POST['id'];

    $sql = "DELETE FROM beslenme_kategorileri WHERE id=$id";
    if (!$conn->query($sql)) {
        die("Kategori silme hatası: " . $conn->error);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['ekle_kat'])) {
    $adi = $_POST['yeni_kategori'];
    $sql = "INSERT INTO beslenme_kategorileri (kategori_adi) VALUES ('$adi')";
    if (!$conn->query($sql)) {
        die("Kategori ekleme hatası: " . $conn->error);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


if (isset($_POST['guncelle_besin'])) {

    $id   = $_POST['id'];
    $adi  = $_POST['adi'];
    $kat  = $_POST['kategori_id'];
    $ack  = $_POST['aciklama'] ?? '';
    $kal  = $_POST['kalori'];
    $eski_resim = $_POST['eski_resim'];  
    $resim_adi = $eski_resim;
    if (!empty($_FILES['yeni_resim']['name']) && $_FILES['yeni_resim']['error'] === UPLOAD_ERR_OK) 
        {
        $dosya  = $_FILES['yeni_resim'];
        $uzanti = pathinfo($dosya['name'], PATHINFO_EXTENSION);
        $yeni_ad = time() . "_" . rand(1, 9999) . "." . strtolower($uzanti);
        if (move_uploaded_file($dosya['tmp_name'], "resimler/" . $yeni_ad)) {
            $resim_adi = $yeni_ad; 
        }
    }
    $sql = "UPDATE beslenme_ogeleri 
            SET adi='$adi',
                kategori_id='$kat',
                açıklama='$ack',
                resim='$resim_adi',
                kalori='$kal'
            WHERE id=$id";
    if (!$conn->query($sql)) 
    {
        die("Besin güncelleme hatası: " . $conn->error);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['sil_besin'])) {
    $id = $_POST['id'];

    $sql = "DELETE FROM beslenme_ogeleri WHERE id=$id";
    if (!$conn->query($sql)) {
        die("Besin silme hatası: " . $conn->error);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}



?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Beslenme Yönetimi | Admin</title>
<link rel="stylesheet" href="admin_bes.css">
<link rel="icon" href="resimler/logo.jpg" type="image/jpg">
<link href="https://fonts.googleapis.com/css2?family=Oranienbaum&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>
<header>
    <div class="logo">PİLATES BY ELA</div>
    <a href="admin.php" class="back">Admin Paneline Geri Dön</a>
</header>
<div class="container">
<h1>❤︎ Beslenme Yönetimi ❤︎</h1>
<div class="kategori">
    <h2>Kategoriler</h2>
    <div class="liste">
        <?php
        $sorgu = $conn->query("SELECT * FROM beslenme_kategorileri ORDER BY kategori_adi");
        while($k = $sorgu->fetch_assoc()):
        ?>
        <form method="POST" class="item">
            <input type="hidden" name="id" value="<?= $k['id'] ?>">
            <input type="text" name="kategori_adi" value="<?= $k['kategori_adi'] ?>">

            <button type="submit" name="guncelle_kat" class="btn btn-yesil">Güncelle</button>
            <button type="submit" name="sil_kat" class="btn btn-kirmizi"
                onclick="return confirm('Kategori silinsin mi?')">Sil</button>
        </form>
        <?php endwhile; ?>

        <form method="POST" class="yeni">
            <input type="text" name="yeni_kategori" placeholder="Yeni kategori adı..." required>
            <button type="submit" name="ekle_kat">Ekle</button>
        </form>

    </div>
</div>

<div class="kategori">
    <h2>Besin Öğeleri</h2>
    <div class="liste">

        <?php
        $sorgu = $conn->query("SELECT bo.*, bk.kategori_adi FROM beslenme_ogeleri bo
                               LEFT JOIN beslenme_kategorileri bk ON bo.kategori_id = bk.id
                               ORDER BY bo.adi");
        while($b = $sorgu->fetch_assoc()):
        ?>
        <form method="POST" enctype="multipart/form-data" class="item">
            <input type="hidden" name="id" value="<?= $b['id'] ?>">
            <input type="hidden" name="eski_resim" value="<?= $b['resim'] ?>">
            <input type="text" name="adi" value="<?= $b['adi'] ?>">
            <select name="kategori_id">
                <?php
                $kat2 = $conn->query("SELECT * FROM beslenme_kategorileri");
                while($k = $kat2->fetch_assoc()){
                    $secili = ($k['id'] == $b['kategori_id']) ? "selected" : "";
                    echo "<option value='".$k['id']."' $secili>".$k['kategori_adi']."</option>";
                }
                ?>
            </select>
            <input type="text" name="aciklama" value="<?= $b['açıklama'] ?>">
            <div style="color:#d4af37; width:100%">
                Mevcut resim: <?= $b['resim'] ?>
            </div>
            <input type="file" name="yeni_resim" accept="image/*">
            <input type="number" name="kalori" value="<?= $b['kalori'] ?>">
            <button type="submit" name="guncelle_besin" class="btn btn-yesil">Güncelle</button>
            <button type="submit" name="sil_besin" class="btn btn-kirmizi" onclick="return confirm('Silinsin mi?')">Sil</button>
        </form>
        <?php endwhile; ?>

        <form method="POST" enctype="multipart/form-data" class="yeni">
            <input type="text" name="adi" placeholder="Besin adı" required>
            
            <select name="kategori_id" required>
                <option value="">Kategori seç</option>
                <?php
                $kat3 = $conn->query("SELECT * FROM beslenme_kategorileri");
                while($k = $kat3->fetch_assoc()){
                    echo "<option value='".$k['id']."'>".$k['kategori_adi']."</option>";
                }
                ?>
            </select>
            <input type="text" name="aciklama" placeholder="Açıklama">
            <input type="number" name="kalori" placeholder="Kalori" required>
            <input type="file" name="resim" accept="image/*" required>
            <button type="submit" name="ekle_besin">Besin Ekle</button>
        </form>
    </div>
</div>
</div>
</body>
</html>
