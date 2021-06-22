<?php

    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/eagrolib.php';
    if (isset($_POST['tipekaryawan'])) {
        $str = 'select id,tipe from '.$dbname.'.sdm_5tipekaryawan';
        $grr = mysql_query($str);
        while ($bar = mysql_fetch_object($grr)) {
            $tkar[$bar->id] = $bar->tipe;
        }
        $tip = $_POST['tipekaryawan'];
        $lok = $_POST['lokasitugas'];
        if ('HO' == substr($_SESSION['empl']['lokasitugas'], 2, 2)) {
            $str = ' select nik,karyawanid,namakaryawan,bagian,subbagian,lokasitugas,tipekaryawan from '.$dbname.".datakaryawan\r\n       where tanggalkeluar is NULL and tipekaryawan=5  and lokasitugas='".$lok."' order by namakaryawan";
        } else {
            if ('KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
                if ('%' == $tip) {
                    $tip = 'tipekaryawan in(1,2,3,4,5,6)';
                } else {
                    if ('0' == $tip) {
                        exit(' Error: you don`t have permission');
                    }

                    $tip = "tipekaryawan='".$tip."'";
                }

                if ('%' == $lok) {
                    $lok = 'left(lokasitugas,4) in(select kodeunit from '.$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
                } else {
                    $lok = "lokasitugas='".$lok."'";
                }

                $whr = $tip.' and '.$lok;
                $str = ' select nik,karyawanid,namakaryawan,bagian,subbagian,lokasitugas,tipekaryawan from '.$dbname.".datakaryawan\r\n       where   ".$whr;
            } else {
                if ('%' == $tip) {
                    $tip = 'tipekaryawan in(1,2,3,4,5,6,7,8)';
                } else {
                    if ('0' == $tip) {
                        exit('Error: you don`t have permission');
                    }

                    $tip = "tipekaryawan='".$tip."'";
                }

                $str = ' select karyawanid,nik,namakaryawan,bagian,subbagian,lokasitugas,tipekaryawan from '.$dbname.".datakaryawan\r\n       where isduplicate=0 and left(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n  and ".$tip."\r\n    order by namakaryawan";
            }
        }

        $optkar = "<option value=''></option>";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $optkar .= "<option value='".$bar->karyawanid."'>".$bar->nik.' | '.$bar->namakaryawan.' | '.$tkar[$bar->tipekaryawan].' | '.$bar->lokasitugas.' | '.$bar->subbagian.'</option>';
        }
        echo $optkar;
        echo $str;
        exit();
    }

    $limit = 20;
    $page = 0;
    if (isset($_POST['tex'])) {
        $notransaksi .= $_POST['tex'];
    }

    $str = 'select count(*) as jlhbrs from '.$dbname.".sdm_suratperingatan a
            left join 
                ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
            where 
                b.namakaryawan like '%".$notransaksi."%'
            and
                updateby = '".$_SESSION['empl']['karyawanid']."'
            order by 
                jlhbrs desc";

    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $jlhbrs = $bar->jlhbrs;
    }
    if (isset($_POST['page'])) {
        $page = $_POST['page'];
        if ($page < 0) {
            $page = 0;
        }
    }

    $offset = $page * $limit;
    
    $str = '    select 
                    a.*,b.tipekaryawan 
                from 
                    '.$dbname.".sdm_suratperingatan a 
                left join 
                    ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                where 
                    b.namakaryawan like '%".$notransaksi."%'
                and
                    updateby = '".$_SESSION['empl']['karyawanid']."'
                limit 
                    ".$offset.',20';


    $res = mysql_query($str);
    $no = $page * $limit;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $namakaryawan = '';
        $strx = 'select namakaryawan from '.$dbname.'.datakaryawan where karyawanid='.$bar->karyawanid;
        $resx = mysql_query($strx);
        while ($barx = mysql_fetch_object($resx)) {
            $namakaryawan = $barx->namakaryawan;
        }
        $namapembuat = '';
        $stry = 'select namakaryawan from '.$dbname.'.datakaryawan where karyawanid='.$bar->updateby;
        $resy = mysql_query($stry);
        while ($bary = mysql_fetch_object($resy)) {
            $namapembuat = $bary->namakaryawan;
        }
        echo "  <tr class=rowcontent>
                    <td>".$no."</td>
                    <td>".$bar->nomor."</td>
                    <td>".$namakaryawan."</td>
                    <td>".tanggalnormal($bar->tanggal)."</td>
                    <td>".tanggalnormal($bar->sampai)."</td>
                    <td>".$bar->jenissp."</td>
                    <td>".$namapembuat."</td>
                    <td align=center>";
        if ('KANWIL' == $_SESSION['empl']['tipelokasitugas'] && '5' == $bar->tipekaryawan) {
            echo "      <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewSP('".$bar->nomor."',event);\">";
        } else {
            echo "      <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewSP('".$bar->nomor."',event);\"> &nbsp 
                        <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delSP('".$bar->nomor."','".$bar->karyawanid."');\"> &nbsp 
                        <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editSP('".$bar->nomor."','".$bar->karyawanid."');\">";
        }

        echo "      </td>
                </tr>";
    }
    echo "  <tr>
                <td colspan=11 align=center>
                    ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs." <br>
                    <button class=mybutton onclick=cariSP(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>
                    <button class=mybutton onclick=cariSP(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>
                </td>
            </tr>";

?>