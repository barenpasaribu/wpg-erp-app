<?php



function setEmplSession($conn, $userid, $dbname)
{
    $strses1 = 'select * from '.$dbname.'.datakaryawan where karyawanid='.$userid;
    $resses1 = mysql_query($strses1);
    if (0 < mysql_num_rows($resses1)) {
        while ($barses1 = mysql_fetch_object($resses1)) {
            $_SESSION['empl']['karyawanid'] = $userid;
            $_SESSION['empl']['name'] = $barses1->namakaryawan;
            $_SESSION['empl']['sex'] = $barses1->jeniskelamin;
            $_SESSION['empl']['birthday'] = $barses1->tanggallahir;
            $_SESSION['empl']['birthplace'] = $barses1->tempatlahir;
            $_SESSION['empl']['address'] = $barses1->alamat;
            $_SESSION['empl']['noktp'] = $barses1->noktp;
            $_SESSION['empl']['nopaspor'] = $barses1->nopaspor;
            $_SESSION['empl']['nationality'] = $barses1->warganegara;
            $_SESSION['empl']['religion'] = $barses1->agama;
            $_SESSION['empl']['statusperkawinan'] = $barses1->statusperkawinan;
            $_SESSION['empl']['jabatan'] = $barses1->jabatan;
            $_SESSION['empl']['kodegolongan'] = $barses1->kodegolongan;
            $_SESSION['empl']['kodeorganisasi'] = $barses1->kodeorganisasi;
            $_SESSION['empl']['lokasitugas'] = $barses1->lokasitugas;
            $_SESSION['empl']['poh'] = $barses1->lokasipenerimaan;
            $_SESSION['empl']['signdate'] = $barses1->tanggalmasuk;
            $_SESSION['empl']['resigndate'] = $barses1->tanggalkeluar;
            $_SESSION['empl']['sistemgaji'] = $barses1->sistemgaji;
            $_SESSION['empl']['email'] = $barses1->email;
            $_SESSION['empl']['phone'] = $barses1->noteleponrumah;
            $_SESSION['empl']['tipekaryawan'] = $barses1->tipekaryawan;
            $_SESSION['empl']['bagian'] = $barses1->bagian;
            $_SESSION['empl']['golongan'] = $barses1->golongan;
            $_SESSION['empl']['kodejabatan'] = $barses1->kodejabatan;
            $_SESSION['empl']['subbagian'] = $barses1->subbagian;
            $strx = 'select tipe,namaorganisasi,induk from  '.$dbname.".organisasi where kodeorganisasi='".$barses1->lokasitugas."'";
            $resx = mysql_query($strx);
            $_SESSION['empl']['tipelokasitugas'] = '';
            $_SESSION['empl']['induklokasitugas'] = '';
            while ($barx = mysql_fetch_object($resx)) {
                $_SESSION['empl']['tipelokasitugas'] = $barx->tipe;
                $_SESSION['empl']['namalokasitugas'] = $barx->namaorganisasi;
                $_SESSION['empl']['induklokasitugas'] = $barx->induk;
            }
        }
        $strx = 'select regional from  '.$dbname.".bgt_regional_assignment where kodeunit='".$_SESSION['empl']['lokasitugas']."'";
        $resx = mysql_query($strx);
        $_SESSION['empl']['regional'] = '';
        while ($barx = mysql_fetch_object($resx)) {
            $_SESSION['empl']['regional'] = $barx->regional;
        }
        $strgdg = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where induk='".$_SESSION['empl']['lokasitugas']."' and tipe like 'GUDANG%'";
        $resgdg = mysql_query($strgdg);
        $_SESSION['empl']['kdgudang'] = '';
        while ($bargdg = mysql_fetch_object($resgdg)) {
            $_SESSION['empl']['kdgudang'] = $bargdg->kodeorganisasi;
        }
    }
}

function getPrivillageType($conn, $dbname)
{
    $strses2 = 'select access_name from '.$dbname.".tipeakses\r\n\t          where status=1";
    $resses2 = mysql_query($strses2);
    if (0 < mysql_num_rows($resses2)) {
        while ($barses2 = mysql_fetch_object($resses2)) {
            $_SESSION['access_type'] = $barses2->access_name;
        }
        if (isset($_SESSION['access_type'])) {
            return true;
        }

        return false;
    }

    return false;
}

function getPrivillages($conn, $username, $dbname)
{
    $strses3 = 'select * from '.$dbname.".auth\r\n\t          where namauser='".$username."'\r\n\t\t\t  and status=1";
    $resses3 = mysql_query($strses3);
    $c_o='';
    if (0 < mysql_num_rows($resses3)) {
        $x = 0;
        while ($barses3 = mysql_fetch_object($resses3)) {
            if (0 === $x) {
                $c_o = $barses3->menuid;
            } else {
                $c_o .= ','.$barses3->menuid;
            }

            $_SESSION['priv'][$x] = $barses3->menuid;
            $_SESSION['priv'][$barses3->menuid.'detail'] = $barses3->detail;
            ++$x;
        }
        $_SESSION['allpriv'] = $c_o;
        if (0 < count($_SESSION['priv'])) {
            return true;
        }

        return false;
    }

    return false;
}

function setEmployer($conn, $dbname)
{
    $_SESSION['theme'] = 'skyblue';
    $strses4 = 'select * from '.$dbname.".organisasi\r\n          where kodeorganisasi='".$_SESSION['empl']['kodeorganisasi']."'";
    $resses4 = mysql_query($strses4);
    if (0 < mysql_num_rows($resses4)) {
        while ($barses4 = mysql_fetch_object($resses4)) {
            $_SESSION['org']['kodeorganisasi'] = $barses4->kodeorganisasi;
            $_SESSION['org']['namaorganisasi'] = $barses4->namaorganisasi;
            $_SESSION['org']['tipeorganisasi'] = $barses4->tipe;
            $_SESSION['org']['alamat'] = $barses4->alamat;
            $_SESSION['org']['telepon'] = $barses4->telepon;
            $_SESSION['org']['wilayahkota'] = $barses4->wilayahkota;
            $_SESSION['org']['induk'] = $barses4->induk;
            $_SESSION['org']['tipeinduk'] = '';
//            $_SESSION['org']['logo'] = $barses4->logo;
            $_SESSION['org']['logo'] = 'images/logomps.jpg';
;
        }
        $strses4a = 'select * from '.$dbname.".organisasi\r\n                  where kodeorganisasi='".$_SESSION['org']['induk']."'";
        $resses4a = mysql_query($strses4a);
        if (0 < mysql_num_rows($resses4a)) {
            while ($barses4a = mysql_fetch_object($resses4a)) {
                $_SESSION['org']['tipeinduk'] = $barses4a->tipe;
            }
        }
    } else {
        $_SESSION['org'] = null;
    }

    $strses5 = 'select * from '.$dbname.".setup_periodeakuntansi where kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=0";
    $resses5 = mysql_query($strses5);
    if (0 < mysql_num_rows($resses5)) {
        while ($barses5 = mysql_fetch_object($resses5)) {
            $tmpPeriod = str_replace('-', '', $barses5->periode);
            $tmpPeriod = str_replace('/', '', $tmpPeriod);
            $_SESSION['org']['period']['start'] = str_replace('-', '', $barses5->tanggalmulai);
            $_SESSION['org']['period']['end'] = str_replace('-', '', $barses5->tanggalsampai);
            $_SESSION['org']['period']['bulan'] = substr($tmpPeriod, 4, 2);
            $_SESSION['org']['period']['tahun'] = substr($tmpPeriod, 0, 4);
        }
    } else {
        $_SESSION['org']['period'] = '';
    }

    $strses6 = 'select a.* from '.$dbname.'.setup_periodeakuntansi a left join '.$dbname.".organisasi b on a.kodeorg=b.kodeorganisasi where b.tipe like 'GUDANG%' and tutupbuku=0";
    $resses6 = mysql_query($strses6);
    if (0 < mysql_num_rows($resses6)) {
        while ($barses6 = mysql_fetch_object($resses6)) {
            $tmpPeriod = str_replace('-', '', $barses6->periode);
            $tmpPeriod = str_replace('/', '', $tmpPeriod);
            $_SESSION['gudang'][$barses6->kodeorg]['start'] = str_replace('-', '', $barses6->tanggalmulai);
            $_SESSION['gudang'][$barses6->kodeorg]['end'] = str_replace('-', '', $barses6->tanggalsampai);
            $_SESSION['gudang'][$barses6->kodeorg]['bulan'] = substr($tmpPeriod, 4, 2);
            $_SESSION['gudang'][$barses6->kodeorg]['tahun'] = substr($tmpPeriod, 0, 4);
        }
    } else {
        $_SESSION['period'] = '';
    }

    $str = 'select namaorganisasi from '.$dbname.".organisasi where tipe='HOLDING' and (induk='' or induk is null) limit 1";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $_SESSION['org']['holding'] = trim($bar->namaorganisasi);
    }
}

?>