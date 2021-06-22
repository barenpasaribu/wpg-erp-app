<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/zLib.php';
$kodeorg = $_POST['kodeorg'];
$tipe = $_POST['tipe'];
$kodeasset = $_POST['kodeasset'];
$kodebarang = $_POST['kodebarang'];
$namaaset = $_POST['namaaset'];
$tahunperolehan = $_POST['tahunperolehan'];
$nilaiperolehan = $_POST['nilaiperolehan'];
$jumlahbulan = $_POST['jumlahbulan'];
$bulanawal = $_POST['bulanawal'];
$keterangan = $_POST['keterangan'];
$status = $_POST['status'];
$method = $_POST['method'];
$leasing = $_POST['leasing'];
$penambah = $_POST['penambah'];
$pengurang = $_POST['pengurang'];
$refbayar = $_POST['refbayar'];
$nodokpengadaan = $_POST['nodokpengadaan'];
$persendecline = $_POST['persendecline'];
$posisiasset = $_POST['posisiasset'];
$optTpasset = makeOption($dbname, 'sdm_5tipeasset', 'kodetipe,metodepenyusutan');
$kamusleasing[0] = 'Not Leasing';
$kamusleasing[1] = 'Leasing';
if ('' == $penambah) {
    $penambah = 0;
}

if ('' == $pengurang) {
    $pengurang = 0;
}

if ('' != $jumlahbulan && '' != $jumlahbulan && 0 < $jumlahbulan) {
    $bulanan = $nilaiperolehan / $jumlahbulan;
} else {
    $bulanan = 0;
}

$tex = '';
if (isset($_POST['txtcari'])) {
    $tex = " and kodeasset like '%".$_POST['txtcari']."%' or namasset like '%".$_POST['txtcari']."%'";
}

$dmn = "char_length(kodeorganisasi)='4'";
$orgOption = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $dmn, '2');
$limit = 20;
$page = 0;
if (isset($_POST['page'])) {
    $page = $_POST['page'];
    if ($page < 0) {
        $page = 0;
    }
}

$offset = $page * $limit;
$str = "select a.*\t\t  \r\n\t\t  from ".$dbname.".sdm_daftarasset a\r\n\t\t  where kodeorg='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n\t\t  ".$tex;
$res = mysql_query($str);
$jlhbrs = mysql_num_rows($res);
switch ($method) {
    case 'update':
        if ('' == $jumlahbulan || '0' == $jumlahbulan) {
            exit('error: '.$_SESSION['lang']['jumlahbulanpenyusutan']." can't empty or zero");
        }

        if ('double' == $optTpasset[$tipe] && ('' == $persendecline || '0' == $persendecline)) {
            exit("error: percentage can't empty or zero");
        }

        if ('' == $bulanawal || '0' == $bulanawal) {
            exit('error: '.$_SESSION['lang']['awalpenyusutan']." can't empty or zero");
        }

        if ('' == $nilaiperolehan || '0' == $nilaiperolehan) {
            exit('error: '.$_SESSION['lang']['hargaperolehan']." can't empty or zero");
        }

        if ('' == $tahunperolehan || '0' == $tahunperolehan) {
            exit('error: '.$_SESSION['lang']['tahunperolehan']." can't empty or zero");
        }

        if ('' == $namaaset || '0' == $namaaset) {
            exit('error: '.$_SESSION['lang']['namaaset']." can't empty or zero");
        }

        $str = 'update '.$dbname.".sdm_daftarasset set \r\n\t       tipeasset='".$tipe."',\r\n\t\t   kodebarang='".$kodebarang."',\r\n\t\t   namasset='".$namaaset."',\r\n\t\t   tahunperolehan=".$tahunperolehan.",\r\n\t\t   status=".$status.",\r\n\t\t   leasing=".$leasing.",\r\n\t\t   hargaperolehan=".$nilaiperolehan.",\r\n\t\t   jlhblnpenyusutan=".$jumlahbulan.",\r\n\t\t   awalpenyusutan='".$bulanawal."',\r\n\t\t   keterangan='".$keterangan."',\r\n\t\t   user=".$_SESSION['standard']['userid'].",\r\n\t\t   bulanan=".$bulanan.",\r\n\t\t   penambah=".$penambah.",\r\n\t\t   pengurang=".$pengurang.",\r\n\t\t\trefbayar='".$refbayar."',\r\n\t\t\tdokpengadaan='".$nodokpengadaan."',\r\n\t\t\tpersendecline=".$persendecline.",\r\n                        posisiasset='".$posisiasset."'\r\n\t       where kodeasset='".$kodeasset."'\r\n\t\t   and kodeorg='".$kodeorg."'";
        if (mysql_query($str)) {
            break;
        }

        echo ' Gagal,'.addslashes(mysql_error($conn));
        exit(0);
    case 'insert':
        if (4 == strlen($tipe)) {
            $kodeasset = str_pad($kodeasset, 6, '0', STR_PAD_LEFT);
        } else {
            if (3 == strlen($tipe)) {
                $kodeasset = str_pad($kodeasset, 7, '0', STR_PAD_LEFT);
            } else {
                if (2 == strlen($tipe)) {
                    $kodeasset = str_pad($kodeasset, 8, '0', STR_PAD_LEFT);
                } else {
                    $kodeasset = str_pad($kodeasset, 8, '0', STR_PAD_LEFT);
                }
            }
        }
		
		#echo $kodeasset;exit();
		
        if ('' == $jumlahbulan || '0' == $jumlahbulan) {
            exit('error: '.$_SESSION['lang']['jumlahbulanpenyusutan']." can't empty or zero");
        }

        if ('double' == $optTpasset[$tipe] && ('' == $persendecline || '0' == $persendecline)) {
            exit("error: percentage can't empty or zero");
        }

        if ('' == $bulanawal || '0' == $bulanawal) {
            exit('error: '.$_SESSION['lang']['awalpenyusutan']." can't empty or zero");
        }

        if ('' == $nilaiperolehan || '0' == $nilaiperolehan) {
            exit('error: '.$_SESSION['lang']['hargaperolehan']." can't empty or zero");
        }

        if ('' == $tahunperolehan || '0' == $tahunperolehan) {
            exit('error: '.$_SESSION['lang']['tahunperolehan']." can't empty or zero");
        }

        if ('' == $namaaset || '0' == $namaaset) {
            exit('error: '.$_SESSION['lang']['namaaset']." can't empty or zero");
        }

        $str = 'insert into '.$dbname.".sdm_daftarasset(tipeasset,kodeorg,kodebarang,namasset,tahunperolehan,status,hargaperolehan,jlhblnpenyusutan,awalpenyusutan,keterangan,kodeasset,user,bulanan,leasing,penambah,pengurang,refbayar,dokpengadaan,persendecline,posisiasset) values ('".$tipe."','".$kodeorg."','".$kodebarang."','".$namaaset."',".$tahunperolehan.','.$status.','.$nilaiperolehan.','.$jumlahbulan.",'".$bulanawal."','".$keterangan."','".$kodeasset."',".$_SESSION['standard']['userid'].','.$bulanan.','.$leasing.','.$penambah.','.$pengurang.",'".$refbayar."','".$nodokpengadaan."',".$persendecline.",'".$posisiasset."')";
        if (mysql_query($str)) {
            break;
        }

        echo ' Gagal,'.addslashes(mysql_error($conn)).$str;
        exit(0);
    case 'delete':
        $str = 'delete from '.$dbname.".sdm_daftarasset \r\n\twhere kodeasset='".$kodeasset."'";
        if (mysql_query($str)) {
            break;
        }

        echo ' Gagal,'.addslashes(mysql_error($conn));
        exit(0);
    default:
        break;
}
if ('EN' == $_SESSION['language']) {
    $ads = 'b.namatipe1 as namatipe';
} else {
    $ads = 'b.namatipe as namatipe';
}

$str = 'select a.*,'.$ads.", \r\n\t      CASE a.status\r\n\t\t  when 0 then 'rusak tidak dapat dipakai/ pensiun'  when 1 then '".$_SESSION['lang']['aktif']."' \r\n\t\t  when 2 then 'Dijual' \r\n\t\t  when 3 then '".$_SESSION['lang']['hilang']."' \r\n\t\t  else 'Unknown'\r\n          END as stat\t\t  \r\n\t\t  from ".$dbname.".sdm_daftarasset a\r\n\t      left join  ".$dbname.".sdm_5tipeasset b\r\n\t      on a.tipeasset=.b.kodetipe\r\n\t\t  where kodeorg='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."' ".$tex." \r\n\t\t  order by tahunperolehan desc,awalpenyusutan desc,namatipe asc\r\n\t\t   limit ".$offset.','.$limit;
$res = mysql_query($str);
$no = $offset;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    echo "<tr class=rowcontent>\r\n\t          <td>".$no."</td>\r\n\t\t      <td>".$orgOption[$bar->kodeorg]."</td>\r\n                          <td>".$orgOption[$bar->posisiasset]."</td>\r\n\t\t\t  <td>".$bar->namatipe."</td>\r\n\t\t\t  <td>".$bar->kodeasset."</td>\r\n\t\t\t  <td>".$bar->namasset."</td>\r\n\t\t\t  <td align=right>".$bar->tahunperolehan."</td>\r\n\t\t\t  <td>".$bar->stat."</td>\r\n\t\t\t  <td align=right>".number_format($bar->hargaperolehan, 2, '.', ',')."</td>\r\n\t\t\t  <td align=right>".$bar->jlhblnpenyusutan."</td>\r\n\t\t\t  <td align=right>".$bar->persendecline."</td>\r\n\t\t\t  <td align=center>".substr($bar->awalpenyusutan, 5, 2).'-'.substr($bar->awalpenyusutan, 0, 4)."</td>\r\n\t\t\t  <td>".$bar->keterangan."</td>\r\n\t\t\t  <td>".$kamusleasing[$bar->leasing]."</td>\r\n\t\t\t  <td>\r\n\t\t\t   <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editAsset('".$bar->kodeorg."','".$bar->tipeasset."','".$bar->kodeasset."','".$bar->namasset."','".$bar->kodebarang."','".$bar->tahunperolehan."','".$bar->status."','".$bar->hargaperolehan."','".$bar->jlhblnpenyusutan."','".$bar->awalpenyusutan."','".$bar->keterangan."','".$bar->leasing."','".$bar->penambah."','".$bar->pengurang."','".$bar->refbayar."','".$bar->dokpengadaan."','".$bar->persendecline."','".$bar->posisiasset."');\">\r\n\t\t      &nbsp <!--<img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delAsset('".$bar->kodeorg."','".$bar->kodeasset."');\">-->\r\n\t\t\t  </td>\r\n\t\t   </tr>\r\n\t\t   </tr>";
}
echo "<tr><td colspan=12 align=center>\r\n       ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."\r\n\t   <br>\r\n       <button class=mybutton onclick=cariAsset(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t   <button class=mybutton onclick=cariAsset(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t   </td>\r\n\t   </tr>";

?>