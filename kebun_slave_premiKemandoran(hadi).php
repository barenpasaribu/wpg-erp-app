<?php

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
('' != $_GET['aksi'] ? $_POST['aksi'] : $_POST['aksi']);
('' != $_GET['tanggal'] ? $_POST['tanggal'] : $_POST['tanggal']);
('' != $_GET['proses'] ? $_POST['proses'] : $_POST['proses']);

$str = "select a.karyawanid,a.namakaryawan,a.subbagian,b.tipe,a.tipekaryawan,a.kodejabatan,d.namajabatan from ".$dbname.'.datakaryawan a left join '.$dbname.'.sdm_5tipekaryawan b on a.tipekaryawan=b.id left join '.$dbname.".sdm_5jabatan d on a.kodejabatan=d.kodejabatan   where a.lokasitugas='".$kodeorg."' and tipekaryawan!=5 and b.tipe!='BHL'";
$res = mysql_query($str);
$kamusKar = [];
while ($bar = mysql_fetch_object($res)) {
    $kamusKar[$bar->karyawanid]['id'] = $bar->karyawanid;
    $kamusKar[$bar->karyawanid]['nama'] = $bar->namakaryawan;
    $kamusKar[$bar->karyawanid]['subbagian'] = $bar->subbagian;
    $kamusKar[$bar->karyawanid]['tipekaryawan'] = $bar->tipekaryawan;
    $kamusKar[$bar->karyawanid]['namatipe'] = $bar->tipe;
    $kamusKar[$bar->karyawanid]['jabatan'] = $bar->namajabatan;
}
switch ($_POST['aksi']) {
    case 'ambilMandor':
        $str = 'select a.karyawanid as nikmandor,a.namakaryawan,a.nik,a.subbagian,b.namajabatan from '.$dbname.'.datakaryawan a '.'left join '.$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan where (b.alias like '%Mandor%') and lokasitugas='".$_SESSION['empl']['lokasitugas']."' and (tanggalkeluar is NULL or tanggalkeluar='0000-00-00' or tanggalkeluar > '".$_SESSION['org']['period']['start']."') and tipekaryawan!=5 order by a.nik asc";
        $res = mysql_query($str);
        $optkar = "<option value=''></option>";
        while ($bar = mysql_fetch_object($res)) {
            $optkar .= "<option value='".$bar->nikmandor."'>".$bar->namakaryawan.' ['.$bar->subbagian.']</option>';
        }
        echo $optkar;

        break;
    case 'ambilMandorMK':
        $str = 'select a.karyawanid as nikmandor1,a.namakaryawan,a.nik,a.subbagian,b.namajabatan from '.$dbname.'.datakaryawan a '.'left join '.$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan where (b.alias like '%Mandor%') and lokasitugas='".$_SESSION['empl']['lokasitugas']."' and (tanggalkeluar is NULL or tanggalkeluar='0000-00-00' or tanggalkeluar > '".$_SESSION['org']['period']['start']."') and tipekaryawan!=5 order by a.nik asc";
        $res = mysql_query($str);
        $optkar = "<option value=''></option>";
        while ($bar = mysql_fetch_object($res)) {
            $optkar .= "<option value='".$bar->nikmandor1."'>".$bar->namakaryawan.' ['.$bar->subbagian.']</option>';
        }
        echo $optkar;

        break;
    case 'ambilKerani':
        $str = 'select a.karyawanid as keranimuat,a.namakaryawan,a.nik,a.subbagian,b.namajabatan from '.$dbname.'.datakaryawan a '.'left join '.$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan where (b.alias like '%Kerani Divisi%') and lokasitugas='".$_SESSION['empl']['lokasitugas']."' and (tanggalkeluar is NULL or tanggalkeluar='0000-00-00' or tanggalkeluar > '".$_SESSION['org']['period']['start']."') and tipekaryawan!=5 order by a.nik asc";
        $res = mysql_query($str);
        $optkar = "<option value=''></option>";
        while ($bar = mysql_fetch_object($res)) {
            $arrku['id'][$bar->keranimuat] = $bar->keranimuat;
            $arrku['nama'][$bar->keranimuat] = $bar->namakaryawan;
            $arrku['subbagian'][$bar->keranimuat] = $bar->subbagian;
        }
        if (0 < count($arrku)) {
            foreach ($arrku['id'] as $id => $val) {
                $optkar .= "<option value='".$id."'>".$arrku['nama'][$id].' ['.$arrku['subbagian'][$id].']</option>';
            }
        }

        echo $optkar;

        break;
    case 'ambilKeraniPanen':
        $str = 'select a.karyawanid as keranimuat,a.namakaryawan,a.nik,a.subbagian,b.namajabatan from '.$dbname.'.datakaryawan a '.'left join '.$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan where (b.alias like '%Kerani Panen%') and lokasitugas='".$_SESSION['empl']['lokasitugas']."' and (tanggalkeluar is NULL or tanggalkeluar='0000-00-00' or tanggalkeluar > '".$_SESSION['org']['period']['start']."') and tipekaryawan!=5 order by a.nik asc";
       
        $res = mysql_query($str);
        $optkar = "<option value=''></option>";
        $arrku = [];
        while ($bar = mysql_fetch_object($res)) {
            $arrku['id'][$bar->keranimuat] = $bar->keranimuat;
            $arrku['nama'][$bar->keranimuat] = $bar->namakaryawan;
            $arrku['subbagian'][$bar->keranimuat] = $bar->subbagian;
        }
        if (0 < count($arrku)) {
            foreach ($arrku['id'] as $id => $val) {
                $optkar .= "<option value='".$id."'>".$arrku['nama'][$id].' ['.$arrku['subbagian'][$id].']</option>';
            }
        }

        echo $optkar;

        break;
    case 'ambilPremiPanen':
        $str = 'select sum(a.upahpremi-a.rupiahpenalty) as premi,a.nik  from '.$dbname.".kebun_prestasi a left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi where a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and b.tanggal=".tanggalsystem($_POST['tanggal'])." and b.nikmandor='".$_POST['nikmandor']."' group by a.nik";
        $res = mysql_query($str);
        $jlhpemanen = mysql_num_rows($res);
        $tPremiPanen = 0;
        while ($bar = mysql_fetch_object($res)) {
            $tPremiPanen += $bar->premi;
        }
        $str = 'select nilai from '.$dbname.".setup_parameterappl where kodeparameter='STMD'";
        $res = mysql_query($str);
        $jlh = '0';
        while ($bar = mysql_fetch_object($res)) {
            $jlh = $bar->nilai;
        }
        echo $jlhpemanen.'#'.number_format($tPremiPanen, 0, '.', ',').'#'.$jlh;

        break;
    case 'ambilList':
        if ('ALLLIST' == $_POST['tipe']) {
            $str = "select a.karyawanid,a.tanggal,b.namakaryawan,a.pembagi,a.premisumber,a.premikomputer,premiinput,a.posting,a.jabatan\r\n                     from ".$dbname.".kebun_premikemandoran a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                     where a.tanggal like '".$_POST['tanggal']."%' and a.kodeorg='".$_SESSION['empl']['lokasitugas']."'";
            $res = mysql_query($str);
            $stream = "<table class=sortable cellspacing=1 border=0>\r\n                            <thead>\r\n                            <tr class=rowheader>\r\n                             <td>".$_SESSION['lang']['nomor']."</td>\r\n                             <td>".$_SESSION['lang']['tanggal']."</td>\r\n                             <td>".$_SESSION['lang']['namakaryawan']."</td> \r\n                             <td>".$_SESSION['lang']['jabatan']."</td>     \r\n                             <td>Devider(Pembagi)</td>  \r\n                             <td>".$_SESSION['lang']['premi'].' '.$_SESSION['lang']['sumber']."</td> \r\n                             <td>Computer Calculation</td>\r\n                             <td>".$_SESSION['lang']['premi']."</td>\r\n                             <td>".$_SESSION['lang']['status']."</td>\r\n                             <td>".$_SESSION['lang']['action']."</td>\r\n                             </tr> \r\n                            </thead>\r\n                            <tbody>";
            $no = 0;
            $ttkom = 0;
            $ttpremi = 0;
            while ($bar = mysql_fetch_object($res)) {
                ++$no;
                $stream .= "<tr class=rowcontent>\r\n                                <td>".$no."</td>\r\n                                <td>".tanggalnormal($bar->tanggal)."</td>\r\n                                <td>".$bar->namakaryawan."</td>\r\n                                <td>".substr($bar->jabatan, 0, 7)."</td>    \r\n                                <td>".$bar->pembagi."</td>\r\n                                <td align=right>".number_format($bar->premisumber, 0, '.', ',')."</td>\r\n                                <td align=right>".number_format($bar->premikomputer, 0, '.', ',')."</td> \r\n                                <td align=right>".number_format($bar->premiinput, 0, '.', ',')."</td>\r\n                                <td>".(('1' == $bar->posting ? 'Posted' : 'Open'))."</td>\r\n                                <td>".(('1' == $bar->posting ? '' : "<img src='images/skyblue/posting.png' style='cursor:pointer;' title='Posting' onclick=\"postingPremi('".$bar->karyawanid."','".$_SESSION['empl']['lokasitugas']."','".$bar->tanggal."','".$bar->jabatan."')\""))."</td>    \r\n                                </tr>";
                $ttkom += $bar->premikomputer;
                $ttpremi += $bar->premiinput;
            }
            $stream .= "</tbody><tfoot>\r\n                       <tr class=rowheader>\r\n                       <td colspan=6>".$_SESSION['lang']['total']."</td>\r\n                       <td align=right>".number_format($ttkom, 0, '.', ',')."</td>\r\n                       <td align=right>".number_format($ttpremi, 0, '.', ',')."</td>\r\n                        <td colspan=2></td>\r\n                       </tr>\r\n                     </tfoot></table>";
        } else {
            $str = "select a.karyawanid,a.tanggal,b.namakaryawan,a.pembagi,a.premisumber,a.premikomputer,premiinput,a.posting\r\n                     from ".$dbname.".kebun_premikemandoran a\r\n                     left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                     where a.tanggal=".tanggalsystem($_POST['tanggal'])."\r\n                     and a.jabatan='".$_POST['tipe']."'    \r\n                     and a.kodeorg='".$_SESSION['empl']['lokasitugas']."'";
            $bd = 0;
            
            $res = mysql_query($str);
            if ('excel' == $_POST['proses']) {
                $bg = ' bgcolor=#DEDEDE';
                $bd = 1;
                $str = "select a.karyawanid,a.tanggal,b.namakaryawan,a.pembagi,a.premisumber,a.premikomputer,premiinput,a.posting,a.jabatan\r\n                     from ".$dbname.".kebun_premikemandoran a\r\n                     left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                     where a.tanggal like '".$_POST['tanggal']."%'   \r\n                     and a.kodeorg='".$_SESSION['empl']['lokasitugas']."'";
                $res = mysql_query($str);
            }

            $stream = '<table class=sortable cellspacing=1 border='.$bd.">\r\n                            <thead>\r\n                            <tr class=rowheader>\r\n                             <td ".$bg.'>'.$_SESSION['lang']['nomor']."</td>\r\n                             <td ".$bg.'>'.$_SESSION['lang']['tanggal']."</td>\r\n                             <td ".$bg.'>'.$_SESSION['lang']['namakaryawan'].'</td>';
            if ('excel' == $_POST['proses']) {
                $stream .= '<td '.$bg.'>'.$_SESSION['lang']['subbagian'].'</td><td '.$bg.'>'.$_SESSION['lang']['tipekaryawan'].'</td>';
            }

            $stream .= '<td '.$bg.">Devider(Pembagi)</td>  \r\n                             <td ".$bg.'>'.$_SESSION['lang']['premi'].' '.$_SESSION['lang']['sumber']."</td> \r\n                             <td ".$bg.">Computer Calculation</td>\r\n                             <td ".$bg.'>'.$_SESSION['lang']['premi']."</td>\r\n                             <td ".$bg.'>'.$_SESSION['lang']['status'].'</td>';
            if ('excel' != $_POST['proses']) {
                $stream .= '<td '.$bg.'>'.$_SESSION['lang']['action'].'</td>';
            }

            $stream .= " </tr> \r\n                            </thead>\r\n                            <tbody>";
            $no = 0;
            while ($bar = mysql_fetch_object($res)) {
                ++$no;
                $stream .= "<tr class=rowcontent>\r\n                                <td>".$no.'</td>';
                if ('excel' == $_POST['proses']) {
                    $stream .= '<td>'.$bar->tanggal.'</td>';
                } else {
                    $stream .= '<td>'.tanggalnormal($bar->tanggal).'</td>';
                }

                $stream .= ' <td>'.$bar->namakaryawan.'</td>';
                if ('excel' == $_POST['proses']) {
                    $sAdd = 'select distinct subbagian,tipekaryawan from '.$dbname.".datakaryawan where karyawanid='".$bar->karyawanid."'";
                    $qAdd = mysql_query($sAdd) ;
                    $rAdd = mysql_fetch_object($qAdd);
                    $optTipe = makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
                    $stream .= '<td>'.$rAdd->subbagian."</td>\r\n                                            <td>".$optTipe[$rAdd->tipekaryawan].'</td>';
                }

                $stream .= '  <td>'.$bar->pembagi."</td>\r\n                                <td align=right>".number_format($bar->premisumber, 0, '.', ',')."</td>\r\n                                <td align=right>".number_format($bar->premikomputer, 0, '.', ',')."</td> \r\n                                <td align=right>".number_format($bar->premiinput, 0, '.', ',').'</td>';
                $stream .= '<td>'.(('1' == $bar->posting ? 'Posted' : 'Open')).'</td>';
                if ('excel' != $_POST['proses']) {
                    $stream .= '<td>'.(('1' == $bar->posting ? '' : "<img src='images/skyblue/delete.png' style='cursor:pointer;' title='Delete' onclick=\"deletePremi('".$bar->karyawanid."','".$_SESSION['empl']['lokasitugas']."','".$bar->tanggal."','".$_POST['tipe']."')\""))."</td>    \r\n                                    ";
                }

                $stream .= '</tr>';
            }
            $stream .= '</tbody><tfoot></tfoot></table>';
        }

        if ('excel' == $_POST['proses']) {
            if (0 == $no) {
                $stream .= 'Data Kosong.<br>';
            }

            $tab = $stream.'Print Time:'.date('Y-m-d H:i:s').'<br />By:'.$_SESSION['empl']['name'];
            $dte = date('YmdHis');
            $nop_ = 'premiKemandoran_'.$_POST['tanggal'];
            if (0 < strlen($tab)) {
                if ($handle = opendir('tempExcel')) {
                    while (false != ($file = readdir($handle))) {
                        if ('.' != $file && '..' != $file) {
                            @unlink('tempExcel/'.$file);
                        }
                    }
                    closedir($handle);
                }

                $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
                if (!fwrite($handle, $tab)) {
                    echo "<script language=javascript1.2>\r\n                        parent.window.alert('Can't convert to excel format');\r\n                        </script>";
                    exit();
                }

                echo "<script language=javascript1.2>\r\n                        window.location='tempExcel/".$nop_.".xls';\r\n                        </script>";
                closedir($handle);
            }
        } else {
            echo $stream;
        }

        break;
    case 'simpan':
        $str = 'select * from '.$dbname.".kebun_premikemandoran where kodeorg= '".$_SESSION['empl']['lokasitugas']."' and tanggal='".tanggalsystem($_POST['tanggal'])."' and jabatan!='MANDORPANEN'";
        if ('MANDORPANEN' == $_POST['jabatan']) {
            $res = mysql_query($str);
            if (0 < mysql_num_rows($res)) {
				
// FA - Jika ada mandor panen yg blm input, tetap bisa input walaupun kerani produksi sudah input (MIG Jan 2019)
/*  
				if ('EN' == $_SESSION['language']) {
                    exit('Error: Foreman premium and/or clerk premium has been recorded on this date, it can not be done unless you remove it first');
                }
                exit('Error: Premi MK dan Kerani sudah diinput pada tanggal tsb, premi Mandor Panen tidak dapat diinput kecuali menghapus terlebih dahulu Premi MK dan Premi Kerani');
 */
			}
        }

        $str = 'insert into '.$dbname.".kebun_premikemandoran (kodeorg, tanggal, karyawanid, jabatan, pembagi, premisumber, premikomputer, premiinput, updateby, postingby, posting)\r\n                         values(\r\n                         '".$_SESSION['empl']['lokasitugas']."',\r\n                         '".tanggalsystem($_POST['tanggal'])."',\r\n                         ".$_POST['karyawanid'].",\r\n                         '".$_POST['jabatan']."',\r\n                         ".$_POST['pembagi'].", \r\n                         ".$_POST['sumber'].",\r\n                         ".$_POST['komputer'].",\r\n                         ".$_POST['premi'].",\r\n                         ".$_SESSION['standard']['userid'].",\r\n                         0,\r\n                         0\r\n                         )";
        if (mysql_query($str)) {
            break;
        }

        exit(' Error:'.addslashes(mysql_error($conn)));
    case 'ambilPremiMandor':
        $str = 'select sum(a.premiinput) as premi,count(*) as jlhmandor  from '.$dbname.".kebun_premikemandoran a                      \r\n                   where a.kodeorg ='".$_SESSION['empl']['lokasitugas']."' \r\n                   and a.tanggal=".tanggalsystem($_POST['tanggal'])."\r\n                   and karyawanid in(\r\n                   select distinct b.nikmandor from ".$dbname.'.kebun_aktifitas b where b.tanggal='.tanggalsystem($_POST['tanggal'])."\r\n                   and b.nikmandor1= '".$_POST['nikmandor1']."')\r\n                   and a.jabatan='MANDORPANEN'";
        $res = mysql_query($str);
        $jlhpemanen = mysql_num_rows($res);
        $tPremiPanen = 0;
        $jumlahmandor = 0;
        while ($bar = mysql_fetch_object($res)) {
            $tPremiPanen = $bar->premi;
            $jumlahmandor = $bar->jlhmandor;
        }
        echo $jumlahmandor.'#'.number_format($tPremiPanen, 0, '.', ',');

        break;
    case 'ambilPremiKerani':
        $str = 'select sum(a.premiinput) as premi,count(*) as jlhmandor  from '.$dbname.".kebun_premikemandoran a                      \r\n                   where a.kodeorg ='".$_SESSION['empl']['lokasitugas']."' \r\n                   and a.tanggal=".tanggalsystem($_POST['tanggal'])."\r\n                   and karyawanid in(\r\n                   select distinct b.nikmandor from ".$dbname.'.kebun_aktifitas b where b.tanggal='.tanggalsystem($_POST['tanggal'])."\r\n                   and b.nikasisten= '".$_POST['nikkerani']."')\r\n                   and a.jabatan='MANDORPANEN'";

        $res = mysql_query($str);
        $jlhpemanen = mysql_num_rows($res);
        $tPremiPanen = 0;
        $jumlahmandor = 0;
        while ($bar = mysql_fetch_object($res)) {
            $tPremiPanen = $bar->premi;
            $jumlahmandor = $bar->jlhmandor;
        }
        echo $jumlahmandor.'#'.number_format($tPremiPanen, 0, '.', ',');

        break;
    case 'ambilPremiKeraniPanen':
        $str = 'select sum(a.premiinput) as premi,count(*) as jlhmandor   from '.$dbname.".kebun_premikemandoran a  where a.kodeorg ='".$_SESSION['empl']['lokasitugas']."' \r\n                   and a.tanggal=".tanggalsystem($_POST['tanggal'])."\r\n                   and karyawanid in(\r\n                   select distinct b.nikmandor from ".$dbname.'.kebun_aktifitas b where b.tanggal='.tanggalsystem($_POST['tanggal'])."\r\n                   and b.keranimuat= '".$_POST['nikkeraniPanen']."')\r\n                   and a.jabatan='MANDORPANEN'";
        
        $res = mysql_query($str);
        $jlhpemanen = mysql_num_rows($res);
        $tPremiPanen = 0;
        $jumlahmandor = 0;
        while ($bar = mysql_fetch_object($res)) {
            $tPremiPanen = $bar->premi;
            $jumlahmandor = $bar->jlhmandor;
        }
        echo $jumlahmandor.'#'.number_format($tPremiPanen, 0, '.', ',');

        break;
    case 'delete':
        $str = 'select * from '.$dbname.".kebun_premikemandoran where \r\n                          kodeorg= '".$_SESSION['empl']['lokasitugas']."' and tanggal='".$_POST['tanggal']."'\r\n                          and jabatan!='MANDORPANEN'";
        if ('MANDORPANEN' == $_POST['jabatan']) {
            $res = mysql_query($str);
            if (0 < mysql_num_rows($res)) {
                exit('Error: Premi MK dan Kerani sudah diinput pada tanggal tsb, premi Mandor Panen tidak dapat dihapus, kecuali menghapus terlebih dahulu Premi MK dan Premi Kerani');
            }
        }

        $str = 'select * from '.$dbname.".kebun_premikemandoran where \r\n                          kodeorg= '".$_SESSION['empl']['lokasitugas']."' and tanggal='".$_POST['tanggal']."'\r\n                          and karyawanid=".$_POST['karyawanid'].' and posting=1';
        $res = mysql_query($str);
        if (0 < mysql_num_rows($res)) {
            exit('Error: Maaf, data tersebut sudah diposting, tidak dapat dihapus');
        }

        $str = 'delete from '.$dbname.".kebun_premikemandoran where \r\n                          kodeorg= '".$_SESSION['empl']['lokasitugas']."' and tanggal='".$_POST['tanggal']."'\r\n                          and karyawanid=".$_POST['karyawanid'];
        if (mysql_query($str)) {
        } else {
            echo 'Error '.addslashes(mysql_error($conn));
        }

        break;
    case 'posting':
        $str = 'update  '.$dbname.".kebun_premikemandoran \r\n                    set posting=1,postingby=".$_SESSION['standard']['userid']."    \r\n                    where kodeorg= '".$_POST['kodeorg']."' and tanggal='".$_POST['tanggal']."'\r\n                    and karyawanid=".$_POST['karyawanid'];
        if (mysql_query($str)) {
        } else {
            echo 'Error '.addslashes(mysql_error($conn));
        }

        break;
}

?>