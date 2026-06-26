<?php
session_start();
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != "admin") {
    header("Location: giris.php");
    exit;
}

require_once "includes/db.php";

function p($key)
{
    return isset($_POST[$key]) ? trim($_POST[$key]) : '';
} /*degerler sureklı hatalı donuyordu o yuzden boyle bır fonks ekledim hatalı bır deger gelıyorsa trım yap*/


if (isset($_POST['ekle_ders']))
   {
    $tarih       = p('tarih');
    $baslangic   = p('baslangic_saati');
    $bitis       = p('bitis_saati');
    $egitmen_id  = (int)p('egitmen_id');
    $ders_id     = (int)p('ders_id');
    $doluluk     = (int)p('doluluk');
    $kapasite    = (int)p('kapasite');
    /*her paketın bır egıtmenı var o nedenle egıtmenın aıt oldugu paketi sorguluyorum*/
    $paket = $conn->query("SELECT id FROM paketler WHERE egitmen_id=$egitmen_id LIMIT 1")->fetch_assoc();
    if (isset($paket['id'])) 
        {
          $paket_id = (int)$paket['id'];
        } 
        else
        {
         $paket_id = 0;
        }

    $conn->query("
        INSERT INTO ders_programi (egitmen_id, tarih, baslangic_saati, bitis_saati, ders_id, doluluk, kapasite, paket_id)
        VALUES ($egitmen_id, '$tarih', '$baslangic', '$bitis', $ders_id, $doluluk, $kapasite, $paket_id)");
    header("Location: admin_ders_programi.php");
    exit;
}

if (isset($_POST['guncelle_ders']))
     {
    $id          = (int)p('id');
    $tarih       = p('tarih');
    $baslangic   = p('baslangic_saati');
    $bitis       = p('bitis_saati');
    $egitmen_id  = (int)p('egitmen_id');
    $ders_id     = (int)p('ders_id');
    $doluluk     = (int)p('doluluk');
    $kapasite    = (int)p('kapasite');

    // yine eğitmene ait paketi buluyorum
    $paket = $conn->query("SELECT id FROM paketler WHERE egitmen_id=$egitmen_id LIMIT 1")->fetch_assoc();
    if (isset($paket['id'])) {          
    $paket_id = (int)$paket['id'];  
    } else 
    {
    $paket_id = 0;                   
    }
    $conn->query("
        UPDATE ders_programi SET 
            tarih='$tarih',
            baslangic_saati='$baslangic',
            bitis_saati='$bitis',
            egitmen_id=$egitmen_id,
            ders_id=$ders_id,
            doluluk=$doluluk,
            kapasite=$kapasite,
            paket_id=$paket_id
            WHERE id=$id
    ");
    header("Location: admin_ders_programi.php");
    exit;
}

if (isset($_POST['sil_ders'])) {
    $id = (int)$_POST['sil_id'];
    $conn->query("DELETE FROM ders_programi WHERE id=$id");
    header("Location: admin_ders_programi.php");
    exit;
}

$egitmenler = $conn->query("
    SELECT e.id, u.ad, u.soyad
    FROM egitmenler e
    LEFT JOIN users u ON u.id = e.user_id
    ORDER BY u.ad
");
$ders_listesi = $conn->query("
    SELECT id, ders_adi 
    FROM dersler 
    ORDER BY ders_adi ASC
");
?>



<!DOCTYPE html>
<html lang="tr">
<head>
<link rel="stylesheet" href="admin_ders.css">
<link rel="icon" href="resimler/logo.jpg" type="image/jpg">
<meta charset="UTF-8">
<title>Ders Programı Yönetimi | Admin</title>
</head>
<body>

<header>
    <div class="logo">PİLATES BY ELA</div>
    <a href="admin.php" class="back">Admin Paneline Geri Dön</a>
</header>

<div class="container">
<h1 style="text-align:center;">❤︎ Ders Programı ❤︎ </h1>
<?php
$dersler = $conn->query("
    SELECT dp.*, 
           u.ad AS egitmen_ad, 
           u.soyad AS egitmen_soyad,
           d.ders_adi
    FROM ders_programi dp
    LEFT JOIN egitmenler e ON e.id = dp.egitmen_id
    LEFT JOIN users u ON u.id = e.user_id
    LEFT JOIN dersler d ON d.id = dp.ders_id
    ORDER BY dp.tarih ASC, dp.baslangic_saati ASC
");
/*tum derslerı sorguladım*/
$current_date = "";
while ($d = $dersler->fetch_assoc()):
    if ($current_date != $d['tarih']):
        if ($current_date != "") { echo "</div>"; }
        $current_date = $d['tarih']; /*gunlere gore grupladim */
?>
    <h2 style="margin-top:30px; color:#d4af37;"><?php echo $current_date; ?></h2>
    <div class="kutu">
<?php endif; ?>

        <div class="ders-karti">
            <strong><?php echo $d['ders_adi']; ?></strong><br>
            <small><?php echo $d['baslangic_saati']; ?> - <?php echo $d['bitis_saati']; ?></small><br>
            <small>Eğitmen: <?php echo $d['egitmen_ad']." ".$d['egitmen_soyad']; ?></small><br>
            <small>Doluluk: <?php echo $d['doluluk']; ?></small><br>
            <small>Kapasite: <?php echo $d['kapasite']; ?></small><br>
            <small>Paket ID: <?php echo $d['paket_id']; ?></small>
            <div style="margin-top:10px;">
                <button class="btn btn-yesil"
                        onclick="document.getElementById('duzenle<?php echo $d['id']; ?>').style.display='block';">
                    Düzenle
                </button>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="sil_id" value="<?php echo $d['id']; ?>">
                    <button class="btn btn-kirmizi" name="sil_ders"
                            onclick="return confirm('Silinsin mi?');">Sil</button>
                </form>
            </div>
           
            <div id="duzenle<?php echo $d['id']; ?>" style="display:none; margin-top:15px;">
                <form method="POST">
                    <input type="hidden" name="id" value="<?php echo $d['id']; ?>">
                    <label>Tarih</label>
                    <input type="date" name="tarih" value="<?php echo $d['tarih']; ?>" required>
                    <label>Başlangıç</label>
                    <input type="time" name="baslangic_saati" value="<?php echo $d['baslangic_saati']; ?>" required>
                    <label>Bitiş</label>
                    <input type="time" name="bitis_saati" value="<?php echo $d['bitis_saati']; ?>" required>
                    <label>Ders</label>
                    <select name="ders_id" required>
                        <?php 
                        $ders_listesi->data_seek(0);
                        while($ds = $ders_listesi->fetch_assoc()):
                        ?>
                            <option value="<?php echo $ds['id']; ?>"
                                <?php echo ($ds['id'] == $d['ders_id']) ? 'selected' : ''; ?>>
                                <?php echo $ds['ders_adi']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <label>Eğitmen</label>
                    <select name="egitmen_id">
                        <?php
                        $egitmenler->data_seek(0); /*aynı verı setını tekrardan kullanmak ıcın gereklıymıs*/
                        while($e = $egitmenler->fetch_assoc()):
                        ?>
                        <option value="<?php echo $e['id']; ?>"
                            <?php echo ($e['id']==$d['egitmen_id'])?'selected':''; ?>>
                            <?php echo $e['ad']." ".$e['soyad']; ?>
                        </option>
                        <?php endwhile; ?>
                    </select>

                    <label>Doluluk</label>
                    <input type="number" name="doluluk" value="<?php echo $d['doluluk']; ?>" required>
                    <label>Kapasite</label>
                    <input type="number" name="kapasite" value="<?php echo $d['kapasite']; ?>" required>
                    <button class="btn btn-yesil" name="guncelle_ders" style="margin-top:10px;">Kaydet</button>
                </form>
            </div>
        </div>

<?php endwhile; ?>
</div>
<div class="kutu">
    <h2 style="color:#d4af37;">Yeni Ders Ekle</h2>

    <form method="POST">
        <label>Tarih</label>
        <input type="date" name="tarih" required>
        <label>Başlangıç Saati</label>
        <input type="time" name="baslangic_saati" required>
        <label>Bitiş Saati</label>
        <input type="time" name="bitis_saati" required>
        <label>Ders</label>
        <select name="ders_id">
            <?php 
            $ders_listesi->data_seek(0);
            while($ds = $ders_listesi->fetch_assoc()):
            ?>
                <option value="<?php echo $ds['id']; ?>">
                    <?php echo $ds['ders_adi']; ?>
                </option>
            <?php endwhile; ?>
        </select>
        <label>Eğitmen</label>
        <select name="egitmen_id">
            <?php 
            $egitmenler->data_seek(0);
            while($e = $egitmenler->fetch_assoc()):
            ?>
                <option value="<?php echo $e['id']; ?>">
                    <?php echo $e['ad']." ".$e['soyad']; ?>
                </option>
            <?php endwhile; ?>
        </select>
        <label>Doluluk</label>
        <input type="number" name="doluluk" value="0" required>
        <label>Kapasite</label>
        <input type="number" name="kapasite" value="10" required>
        <button class="btn btn-yesil" name="ekle_ders">Dersi Ekle</button>
    </form>
</div>
</body>
</html>
