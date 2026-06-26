<?php
session_start();
require_once "includes/db.php";

if (isset($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];
} else {
    $user_id = 0;
}

if (isset($_GET["ders_id"])) {
    $ders_id = intval($_GET["ders_id"]);
} else {
    $ders_id = 0;
}
 

if (!$user_id) {
    header("Location: giris.php");
    exit;
}
$mesaj     = "";
$mesaj_tur = "error";

$paket_sql = "SELECT kp.*, p.ders_sayisi 
              FROM kullanici_paketleri kp
              JOIN paketler p ON kp.paket_id = p.id
              WHERE kp.user_id = $user_id
              ORDER BY kp.id DESC LIMIT 1";

$paket = mysqli_fetch_assoc(mysqli_query($conn, $paket_sql));
if (!$paket || $paket["kalan_ders"] <= 0) {
    $mesaj = "Ders hakkınız kalmamış! Yeni paket satın alın.";
}
else
 {
    $ders_sorgu = mysqli_query($conn, "SELECT tarih FROM ders_programi WHERE id = $ders_id");
    if (mysqli_num_rows($ders_sorgu) == 0) { /*donen satır sayısını verıcek bu sorgu bana */
        $mesaj = "Ders bulunamadı!";
    } else {
        $ders = mysqli_fetch_assoc($ders_sorgu);
        $ders_tarihi = $ders['tarih'];   
        if (empty($ders_tarihi) || $ders_tarihi == '0000-00-00 00:00:00') 
            {
            $mesaj = "Ders tarihi geçersiz!";
        } else
         {
            $ders_gun    = date('Y-m-d', strtotime($ders_tarihi));
            $bugun_gun   = date('Y-m-d');
            if ($ders_gun < $bugun_gun) 
                {
                $mesaj = "Bu dersin tarihi geçtiği için katılamazsınız!";
            } else
             {
                $kontrol = mysqli_query($conn, "SELECT id FROM derskayit WHERE user_id = $user_id AND ders_id = $ders_id");
                if (mysqli_num_rows($kontrol) > 0) {
                    $mesaj = "Bu derse zaten katıldınız!";
                } else
                 {
                    mysqli_query($conn, "INSERT INTO derskayit (user_id, ders_id) VALUES ($user_id, $ders_id)");
                    mysqli_query($conn, "UPDATE ders_programi SET doluluk = doluluk + 1 WHERE id = $ders_id");
                    $yeni_hak = $paket["kalan_ders"] - 1;
                    mysqli_query($conn, "UPDATE kullanici_paketleri SET kalan_ders = $yeni_hak WHERE id = ".$paket["id"]);
                    $mesaj     = "Derse başarıyla katıldınız!";
                    $mesaj_tur = "success";
                }
            }
        }
    }
}

$_SESSION['mesaj']     = $mesaj;
$_SESSION['mesaj_tur'] = $mesaj_tur;
header("Location: dersler.php"); 
exit;
?>