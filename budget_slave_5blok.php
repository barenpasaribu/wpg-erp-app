<?php



session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$thnAngrn = $_POST['thnAngrn'];
$afdId = $_POST['afdId'];
('' === $_POST['proses'] ? ($proses = $_GET['proses']) : ($proses = $_POST['proses']));
$haThnLalu = $_POST['haThnLalu'];
$kdBlok = $_POST['kdBlok'];
$haThnIni = $_POST['haThnIni'];
$pkkThnLalu = $_POST['pkkThnLalu'];
$pokokThnIni = $_POST['pokokThnIni'];
$statBlok = $_POST['statBlok'];
$topoGrafi = $_POST['topoGrafi'];
$thnTmn = $_POST['thnTmn'];
$haNon = $_POST['haNon'];
$jmlh = $_POST['jmlh'];
$pkkProduktif = $_POST['pkkProduktif'];
$pkkProdukBr = $_POST['pkkProdukBr'];
$thnAngBr = $_POST['thnAngBr'];
$afdIdBr = $_POST['afdIdBr'];
$kdBlokBr = $_POST['kdBlokBr'];
$haThnIniBr = $_POST['haThnIniBr'];
$topoGrafiBr = $_POST['topoGrafiBr'];
$thnTmnBr = $_POST['thnTmnBr'];
$pokokThnIniBr = $_POST['pokokThnIniBr'];
$haNonBr = $_POST['haNonBr'];
$statBlokBr = $_POST['statBlokBr'];
$sumber = $_POST['sumber'];
$arrStatusBlok = getEnum($dbname, 'setup_blok', 'statusblok');
$optTopografi = makeOption($dbname, 'setup_topografi', 'topografi,keterangan');
$optTopografi2 = makeOption($dbname, 'setup_topografi', 'keterangan,topografi');
$sumber = $_POST['sumber'];
$thnAngrnOld = $_POST['thnAngrnOld'];
$oldBlok = $_POST['oldBlok'];
$topoGrafOld = $_POST['topoGrafOld'];
$lcThnini = $_POST['lcThnini'];
$lcThnBr = $_POST['lcThnBr'];
$a = 1;
$b = 1;
foreach ($arrStatusBlok as $brs) {
    $dtBlok[$a] = $brs;
    ++$a;
}
foreach ($optTopografi2 as $brsTp) {
    $dtTpgr[$b] = $brsTp;
    ++$b;
}
$tot = count($dtBlok);
$totTpGraf = count($dtTpgr);
if (1 <= $jmlh) {
    if ('' !== $thnAngBr) {
        $sCekOpt = 'select tahunbudget,kodeblok,statusblok,topografi from '.$dbname.".bgt_blok  \r\n        where kodeblok like '%".$afdIdBr."%' and tahunbudget='".$thnAngBr."' and sumber='BARU' order by statusblok desc";
    } else {
        $sCekOpt = 'select tahunbudget,kodeblok,statusblok from '.$dbname.".bgt_blok  \r\n        where kodeblok like '%".$afdId."%' and tahunbudget='".$thnAngrn."' and sumber='LAMA' order by statusblok desc";
    }

    $qCek = mysql_query($sCekOpt) || exit(mysql_error($conns));
    $rowCekBrs = mysql_num_rows($qCek);
    if (0 === $rowCekBrs || '' !== $thnAngBr) {
        for ($c = 1; $c <= $totTpGraf; ++$c) {
            $arrOptTopo[$afdIdBr] .= "<option value='".$dtTpgr[$c]."' >".$optTopografi[$dtTpgr[$c]].'</option>';
        }
        for ($x = 1; $x <= $tot; ++$x) {
            $arrOptBlok[$afdIdBr] .= "<option value='".$dtBlok[$x]."' >".$dtBlok[$x].'</option>';
        }
    } else {
        while ($rCek = mysql_fetch_assoc($qCek)) {
            if ('' !== $thnAngBr) {
                for ($c = 1; $c <= $totTpGraf; ++$c) {
                    if ($dtTpgr[$c] === $rCek['topografi']) {
                        $arrOptTopo[$afdIdBr] .= "<option value='".$dtTpgr[$c]."' selected>".$optTopografi[$dtTpgr[$c]].'</option>';
                    } else {
                        $arrOptTopo[$afdIdBr] .= "<option value='".$dtTpgr[$c]."' >".$optTopografi[$dtTpgr[$c]].'</option>';
                    }
                }
                for ($x = 1; $x <= $tot; ++$x) {
                    if ($dtBlok[$x] === $rCek['statusblok']) {
                        $arrOptBlok[$afdIdBr] .= "<option value='".$dtBlok[$x]."' selected>".$dtBlok[$x].'</option>';
                    } else {
                        $arrOptBlok[$afdIdBr] .= "<option value='".$dtBlok[$x]."' >".$dtBlok[$x].'</option>';
                    }
                }
            }

            for ($x = 1; $x <= $tot; ++$x) {
                if ($dtBlok[$x] === $rCek['statusblok']) {
                    $arrOptBlok[$rCek['tahunbudget']][$rCek['kodeblok']] .= "<option value='".$dtBlok[$x]."' selected>".$dtBlok[$x].'</option>';
                } else {
                    $arrOptBlok[$rCek['tahunbudget']][$rCek['kodeblok']] .= "<option value='".$dtBlok[$x]."' >".$dtBlok[$x].'</option>';
                }
            }
            if ($x === $tot) {
                $x = 1;
            }

            if ($c === $totTpGrafot) {
                $c = 1;
            }
        }
    }
} else {
    if (0 === $jmlh) {
        if ('' !== $thnAngBr) {
            $sCekOpt = 'select tahunbudget,kodeblok,statusblok,topografi from '.$dbname.".bgt_blok \r\n        where kodeblok like '%".$afdIdBr."%' and sumber='BARU' and tahunbudget='".$thnAngBr."' order by statusblok desc";
        } else {
            $sCekOpt = 'select tahuntanam as tahunbudget,kodeorg as kodeblok,statusblok from '.$dbname.".setup_blok \r\n        where kodeorg like '%".$afdId."%' order by statusblok desc";
        }

        $qCek = mysql_query($sCekOpt) || exit(mysql_error($conns));
        while ($rCek = mysql_fetch_assoc($qCek)) {
            if ('' !== $thnAngBr) {
                for ($c = 1; $c <= $totTpGraf; ++$c) {
                    if ($dtTpgr[$c] === $rCek['topografi']) {
                        $arrOptTopo[$afdIdBr] .= "<option value='".$dtTpgr[$c]."' selected>".$optTopografi[$dtTpgr[$c]].'</option>';
                    } else {
                        $arrOptTopo[$afdIdBr] .= "<option value='".$dtTpgr[$c]."' >".$optTopografi[$dtTpgr[$c]].'</option>';
                    }
                }
                for ($x = 1; $x <= $tot; ++$x) {
                    if ($dtBlok[$x] === $rCek['statusblok']) {
                        $arrOptBlok[$afdIdBr] .= "<option value='".$dtBlok[$x]."' selected>".$dtBlok[$x].'</option>';
                    } else {
                        $arrOptBlok[$afdIdBr] .= "<option value='".$dtBlok[$x]."' >".$dtBlok[$x].'</option>';
                    }
                }
            } else {
                for ($x = 1; $x <= $tot; ++$x) {
                    if ($dtBlok[$x] === $rCek['statusblok']) {
                        $arrOptBlok[$rCek['tahunbudget']][$rCek['kodeblok']] .= "<option value='".$dtBlok[$x]."' selected>".$dtBlok[$x].'</option>';
                    } else {
                        $arrOptBlok[$rCek['tahunbudget']][$rCek['kodeblok']] .= "<option value='".$dtBlok[$x]."' >".$dtBlok[$x].'</option>';
                    }
                }
            }

            if ($x === $tot) {
                $x = 1;
            }

            if ($c === $totTpGrafot) {
                $c = 1;
            }
        }
    }
}

switch ($proses) {
    case 'cekData':
        if ('' === $thnAngrn || '' === $afdId) {
            exit('Error:Required field is missing');
        }

        $sThnCek = 'select distinct tahunbudget from '.$dbname.".bgt_blok where tahunbudget='".$thnAngrn."' and kodeblok like '".$afdId."%' and closed=1 and sumber='LAMA'";
        $qThnCek = mysql_query($sThnCek) || exit(mysql_error($conns));
        $rThnCek = mysql_num_rows($qThnCek);
        if ($rThnCek < 1) {
            $thn = date('Y');
            if ('' === $thnAngrn) {
                exit('Error:Budget year required');
            }

            if (strlen($thnAngrn) < 4) {
                exit('Error:Budget year incorrect');
            }

            if (substr($thn, 0, 1) !== substr($thnAngrn, 0, 1)) {
                exit('Error:Budget year incorrect');
            }

            $sCek = 'select * from '.$dbname.".bgt_blok where tahunbudget='".$thnAngrn."' and kodeblok like '%".$afdId."%' and sumber='LAMA'";
            $qCek = mysql_query($sCek) || exit(mysql_error($conns));
            $rCek = mysql_num_rows($qCek);
            echo $rCek;

            break;
        }

        exit('Error:Budget has been closed');
    case 'getPreview':
        $arrPlasma = ['I' => 'Inti', 'P' => 'Plasma'];
        $optPlasma = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        foreach ($arrPlasma as $lsPlasma => $txtPlasma) {
            $optPlasma .= "<option value='".$lsPlasma."'>".$txtPlasma.'</option>';
        }
        $tab = "<table cellpadding=1 cellspacing=1 border=0 class=sortable>\r\n            <thead>\r\n            <tr class=rowheader>\r\n            <td>No</td>\r\n            <td>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td>".$_SESSION['lang']['blok']."</td>\r\n            <td>".$_SESSION['lang']['hathnlalu']."</td>\r\n            <td>".$_SESSION['lang']['hathnini']."</td>\r\n            <td>".$_SESSION['lang']['pokokthnlalu']."</td>\r\n            <td>".$_SESSION['lang']['pokokthnini']."</td>\r\n            <td>".$_SESSION['lang']['statusblok']."</td>\r\n            <td>".$_SESSION['lang']['topografi']."</td>\r\n            <td>".$_SESSION['lang']['thntnm']."</td>\r\n            <td>".$_SESSION['lang']['lcthnini']."</td>\r\n            <td>".$_SESSION['lang']['hanonproduktif']."</td>\r\n            <td>".$_SESSION['lang']['pkkproduktif']."</td>\r\n            <td>Intiplasma</td>\r\n            </tr>\r\n            </thead><tbody>";
        if ($jmlh < 1) {
            $sCek2 = 'select tahuntanam as tahunbudget,statusblok,luasareaproduktif,jumlahpokok,topografi,kodeorg,tahuntanam,intiplasma from '.$dbname.".setup_blok where kodeorg like '%".$afdId."%' ";
        } else {
            if (1 < $jmlh) {
                $sCek2 = "select tahunbudget, kodeblok as kodeorg, hathnlalu as luasareaproduktif, hathnini, pokokthnlalu as jumlahpokok, pokokthnini , \r\n                    statusblok, topografi,thntnm as tahuntanam,hanonproduktif,lcthnini,intiplasma,pokokproduksi\r\n                    from ".$dbname.".bgt_blok\r\n                    where kodeblok like '%".$afdId."%' and tahunbudget='".$thnAngrn."' and sumber='LAMA'";
            }
        }

        $tot = count($arrStatusBlok);
        $b = 1;
        $qCek2 = mysql_query($sCek2) || exit(mysql_error($conns));
        $rCek = mysql_num_rows($qCek2);
        while ($res = mysql_fetch_assoc($qCek2)) {
            ++$no;
            $statDtPlsma = '';
            if ('P' === $res['intiplasma']) {
                $statDtPlsma = 'checked';
            }

            if ('' === $res['pokokthnini']) {
                $res['pokokthnini'] = $res['jumlahpokok'];
            }

            if ('' === $res['pokokproduksi']) {
                $res['pokokproduksi'] = $res['pokokthnini'];
            }

            if ('' === $res['hathnini']) {
                $res['hathnini'] = $res['luasareaproduktif'];
            }

            $tab .= '<tr class=rowcontent id=rew_'.$no.'>';
            $tab .= '<td>'.$no.'</td>';
            $tab .= '<td>'.$thnAngrn.'</td><td id=kdBlok_'.$no.'>'.$res['kodeorg']."</td><td align=right onclick='getData(".$no.")' style='cursor:pointer;' id=luas_".$no.'>'.$res['luasareaproduktif'].'</td>';
            $tab .= '<td><input type=text id=hathnIni_'.$no."  class=myinputtextnumber onkeypress='return angka_doang(event)' value='".$res['hathnini']."'  /></td>";
            $tab .= "<td align=right onclick='getDatab(".$no.")' style='cursor:pointer;' id=pkk_".$no.'>'.$res['jumlahpokok'].'</td>';
            $tab .= '<td><input type=text id=pokokThnINi_'.$no." class=myinputtextnumber onkeypress='return angka_doang(event)' value='".$res['pokokthnini']."' onblur='cekThis(".$no.")' /></td>";
            $tab .= '<td><select id=statBlok_'.$no.'>'.$arrOptBlok[$res['tahunbudget']][$res['kodeorg']].'</select></td>';
            $tab .= "<td id='topoGrafi_".$no."'>".$res['topografi'].'-'.$optTopografi[$res['topografi']]."</td><td id='thnTmn_".$no."' align='center'>".$res['tahuntanam'].'</td>';
            $tab .= '<td><input type=text id=lcThn_'.$no."  class=myinputtextnumber onkeypress='return angka_doang(event)' value='".$res['lcthnini']."'  /></td>";
            $tab .= '<td><input type=text id=haNon_'.$no."  class=myinputtextnumber onkeypress='return angka_doang(event)' value='".$res['hanonproduktif']."'  /></td>";
            $tab .= '<td><input type=text id=pkkProduk_'.$no."  class=myinputtextnumber onkeypress='return angka_doang(event)' value='".$res['pokokproduksi']."' onblur='cekThis(".$no.")'  /></td>";
            $tab .= "<td align=center><input type=checkbox  id='statPlasma_".$no."' title='clik jika plasma' ".$statDtPlsma.' /></td>';
            $tab .= '</tr>';
        }
        $tab .= "<tr><td colspan='12' align=center><button class=mybutton  onclick=saveAll(1)  style=cursor:pointer;>".$_SESSION['lang']['save'].' '.$_SESSION['lang']['all'].'</button></td></tr>';
        $tab .= '</tbody></table><input type=hidden id=jmlhRow value='.$no.' />';
        echo $tab;

        break;
    case 'insertAll':
        $thn = date('Y');
        if ('' === $thnAngrn) {
            exit('Error:Budget year required');
        }

        if (strlen($thnAngrn) < 4) {
            exit('Error:Budget year incorrect');
        }

        if (substr($thn, 0, 1) !== substr($thnAngrn, 0, 1)) {
            exit('Error:Budget year incorrect');
        }

        $sCek = 'select * from '.$dbname.".bgt_blok where tahunbudget='".$thnAngrn."' and kodeblok='".$kdBlok."' and topografi='".$topoGrafi."' and sumber='LAMA' and closed=1";
        $qCek = mysql_query($sCek) || exit(mysql_error($conns));
        $rCek = mysql_num_rows($qCek);
        ('' === $haNon ? ($haNon = 0) : ($haNon = $haNon));
        $hamutasi = $haThnIni - $haThnLalu;
        $pokokMutasi = $pokokThnIni - $pkkThnLalu;
        if ('' === $pokokThnIni || '' === $haThnIni) {
            $sDel = 'delete from '.$dbname.".bgt_blok where tahunbudget='".$thnAngrn."' and substr(kodeblok,1,6)='".substr($kdBlok, 0, 6)."' and sumber='LAMA'";
            if (mysql_query($sDel)) {
                exit('Error:Ha this year and ha last year are required');
            }

            echo 'DB Error : '.$sDel."\n".mysql_error($conn);
        }

        if (1 !== $rCek) {
            $sCkTopo = 'select topografi from '.$dbname.".setup_topografi where topografi='".$topoGrafi."'";
            $qCkTopo = mysql_query($sCkTopo) || exit(mysql_error($conns));
            $rCktopo = mysql_fetch_assoc($qCkTopo);
            if ('' !== $rCktopo['topografi']) {
                $tmbhn = '';
                $tmbhn2 = '';
                if ('P' === $_POST['statPlsma']) {
                    $tmbhn = ',`intiplasma`';
                    $tmbhn2 = ",'".$_POST['statPlsma']."'";
                }

                $sCek2 = 'select * from '.$dbname.".bgt_blok where tahunbudget='".$thnAngrn."' and kodeblok='".$kdBlok."' and topografi='".$topoGrafi."' and sumber='LAMA'";
                $qCek2 = mysql_query($sCek2) || exit(mysql_error($conns));
                $rCek2 = mysql_num_rows($qCek2);
                if (0 < $rCek2) {
                    $sDel = 'delete from '.$dbname.".bgt_blok where tahunbudget='".$thnAngrn."' and kodeblok='".$kdBlok."' and topografi='".$topoGrafi."' and sumber='LAMA'";
                    if (mysql_query($sDel)) {
                        $sInsert = 'insert into '.$dbname.".bgt_blok (tahunbudget, kodeblok, hathnlalu, hathnini, pokokthnlalu, pokokthnini, statusblok, topografi, \r\n                                  thntnm, hanonproduktif, sumber, updateby,hamutasi,pokokmutasi,pokokproduksi,lcthnini".$tmbhn.") \r\n                                  value ('".$thnAngrn."','".$kdBlok."','".$haThnLalu."','".$haThnIni."','".$pkkThnLalu."','".$pokokThnIni."','".$statBlok."','".$topoGrafi."'\r\n                                   ,'".$thnTmn."','".$haNon."','LAMA','".$_SESSION['standard']['userid']."','".$hamutasi."','".$pokokMutasi."','".$pkkProduktif."','".$lcThnini."'".$tmbhn2.')';
                        if (!mysql_query($sInsert)) {
                            echo 'DB Error : '.$sInsert."\n".mysql_error($conn);
                        }
                    } else {
                        echo 'DB Error : '.$sDel."\n".mysql_error($conn);
                    }
                } else {
                    $sInsert = 'insert into '.$dbname.'.bgt_blok (tahunbudget, kodeblok, hathnlalu, hathnini, pokokthnlalu, pokokthnini, statusblok, topografi, thntnm, hanonproduktif, sumber, updateby,hamutasi,pokokmutasi,pokokproduksi,lcthnini'.$tmbhn.") \r\n                        value ('".$thnAngrn."','".$kdBlok."','".$haThnLalu."','".$haThnIni."','".$pkkThnLalu."','".$pokokThnIni."','".$statBlok."','".$topoGrafi."','".$thnTmn."','".$haNon."','LAMA','".$_SESSION['standard']['userid']."','".$hamutasi."','".$pokokMutasi."','".$pkkProduktif."','".$lcThnini."'".$tmbhn2.')';
                    if (!mysql_query($sInsert)) {
                        echo 'DB Error : '.$sInsert."\n".mysql_error($conn);
                    }
                }
            } else {
                $sDel = 'delete from '.$dbname.".bgt_blok where tahunbudget='".$thnAngrn."' and substr(kodeblok,1,6)='".substr($kdBlok, 0, 6)."' and sumber='LAMA'";
                if (mysql_query($sDel)) {
                    exit('Error:Topography required, please input topography from Setup Block menu.');
                }

                echo 'DB Error : '.$sDel."\n".mysql_error($conn);
            }

            break;
        }

        exit('Error:Budget has been closed');
    case 'cekDataBr':
        if ('' === $thnAngBr || '' === $afdIdBr) {
            exit('Error:Required field are missing');
        }

        $sThnCek = 'select distinct tahunbudget from '.$dbname.".bgt_blok where tahunbudget='".$thnAngrn."' and kodeblok like '".$afdIdBr."%' and closed=1 and sumber='BARU'";
        $qThnCek = mysql_query($sThnCek) || exit(mysql_error($conns));
        $rThnCek = mysql_num_rows($qThnCek);
        if ($rThnCek < 1) {
            $thn = date('Y');
            if ('' === $thnAngBr) {
                exit('Error:Budget year required');
            }

            if (strlen($thnAngBr) < 4) {
                exit('Error:Budget year incorrect');
            }

            if (substr($thn, 0, 1) !== substr($thnAngBr, 0, 1)) {
                exit('Error:Budget year incorrect');
            }

            $tab = "<table cellpadding=1 cellspacing=1 border=0 class=sortable>\r\n            <thead>\r\n            <tr class=rowheader>\r\n            <td>No</td>\r\n            <td>".$_SESSION['lang']['tahun']."</td>\r\n            <td>".$_SESSION['lang']['kebun']."</td>\r\n            <td>".$_SESSION['lang']['afdeling']."</td>\r\n            <td>".$_SESSION['lang']['blok']."</td>\r\n            <td>".$_SESSION['lang']['hathnini']."</td>\r\n            <td>".$_SESSION['lang']['pokokthnini']."</td>\r\n            <td>".$_SESSION['lang']['statusblok']."</td>\r\n            <td>".$_SESSION['lang']['topografi']."</td>\r\n            <td>".$_SESSION['lang']['thntnm']."</td>\r\n            <td>".$_SESSION['lang']['lcthnini']."</td>\r\n            <td>".$_SESSION['lang']['hanonproduktif']."</td>\r\n            <td>".$_SESSION['lang']['pkkproduktif']."</td>\r\n            <td>Intiplasma</td>\r\n            <td>Action</td>\r\n            </tr>\r\n            </thead><tbody>";
            $b = 1;
            $kbn = substr($afdIdBr, 0, 4);
            $afd = substr($afdIdBr, 4, 2);
            ++$no;
            $tab .= '<tr class=rowcontent id=rewBr_'.$no.'>';
            $tab .= '<td>'.$no.'</td>';
            $tab .= '<td>'.$thnAngBr.'</td>';
            $tab .= '<td id=kdKbn_'.$no.'>'.$kbn.'</td>';
            $tab .= "<td><input type='text' id='kdAfdling_".$no."' value='".$afd."' class='myinputtextnumber' style='width:45px' maxlength=2 onkeypress='return tanpa_kutip(event)' disabled /></td>";
            $tab .= "<td><input type='text' id='kdBlokBr_".$no."' class='myinputtextnumber' style='width:45px' maxlength=4 onkeypress='return tanpa_kutip(event)' /></td>";
            $tab .= '<td><input type=text id=hathnIniBr_'.$no."  class=myinputtextnumber onkeypress='return angka_doang(event)' value='0' style='width:55px' /></td>";
            $tab .= '<td><input type=text id=pokokThnINiBr_'.$no." class=myinputtextnumber onkeypress='return angka_doang(event)' value='0' style='width:75px'  onblur='cekThisBr(".$no.")' /></td>";
            $tab .= '<td><select id=statBlokBr_'.$no.'>'.$arrOptBlok[$afdIdBr].'</select></td>';
            $tab .= "<td><select id='topoGrafiBr_".$no."'>".$arrOptTopo[$afdIdBr]."</select></td><td><input type='text' id='thnTmnBr_".$no."' class=myinputtextnumber onkeypress='return angka_doang(event)'  style='width:75px' maxlength='4' /></td>";
            $tab .= '<td><input type=text id=lcThnBr_'.$no."  class=myinputtextnumber onkeypress='return angka_doang(event)' value='0' style='width:85px' /></td>";
            $tab .= '<td><input type=text id=haNonBr_'.$no."  class=myinputtextnumber onkeypress='return angka_doang(event)' value='0' style='width:85px' /></td>";
            $tab .= '<td><input type=text id=pkkProdukBr_'.$no."  class=myinputtextnumber onkeypress='return angka_doang(event)' value='0' onblur='cekThisBr(".$no.")'  /></td>";
            $tab .= "<td align=center><input type=checkbox  id='statPlasmaBr_".$no."' title='clik jika plasma' ".$statDtPlsma.' /></td>';
            $tab .= "<td align=center style='cursor:pointer;'><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"addDetail(".$no.")\" src='images/save.png'/></td>";
            $tab .= '</tr>';
            $tab .= '</tbody></table><input type=hidden id=jmlhRow value='.$no." /> <input type=hidden id=thnAngrnOld value=''  /><input type=hidden id=oldBlok value=''   /><input type=hidden id=topoGrafOld value=''  />";
            $limit = 20;
            $page = 0;
            if (isset($_POST['page'])) {
                $page = $_POST['page'];
                if ($page < 0) {
                    $page = 0;
                }
            }

            $offset = $page * $limit;
            $ql2 = 'select count(*) as jmlhrow from '.$dbname.".bgt_blok where  sumber='BARU' and kodeblok like '%".$afdIdBr."%' and tahunbudget='".$thnAngBr."'  order by tahunbudget desc";
            $query2 = mysql_query($ql2) || exit(mysql_error($conns));
            while ($jsl = mysql_fetch_object($query2)) {
                $jlhbrs = $jsl->jmlhrow;
            }
            $sLoad = 'select * from '.$dbname.".bgt_blok where sumber='BARU' and kodeblok like '%".$afdIdBr."%' and tahunbudget='".$thnAngBr."' order by tahunbudget desc limit ".$offset.','.$limit.'';
            $qLoad = mysql_query($sLoad) || exit(mysql_error($conns));
            while ($rLoad = mysql_fetch_assoc($qLoad)) {
                $kbn = substr($rLoad['kodeblok'], 0, 4);
                $afd = substr($rLoad['kodeblok'], 4, 2);
                $blk = substr($rLoad['kodeblok'], 6, 4);
                ++$no2;
                $tab2 .= '<tr class=rowcontent id=rewBr_'.$no2.'>';
                $tab2 .= '<td>'.$no2.'</td>';
                $tab2 .= '<td>'.$rLoad['tahunbudget'].'</td>';
                $tab2 .= '<td id=kdKbn_'.$no2.'>'.$kbn.'</td>';
                $tab2 .= '<td>'.$afd.'</td>';
                $tab2 .= '<td>'.$blk.'</td>';
                $tab2 .= '<td align=right>'.$rLoad['hathnini'].'</td>';
                $tab2 .= '<td align=right>'.$rLoad['pokokthnini'].'</td>';
                $tab2 .= '<td>'.$arrStatusBlok[$rLoad['statusblok']].'</td>';
                $tab2 .= '<td>'.$optTopografi[$rLoad['topografi']]."</td><td id='thnTmnBr_".$no2."' align='center'>".$rLoad['thntnm'].'</td>';
                $tab2 .= '<td align=right>'.$rLoad['hanonproduktif'].'</td>';
                if (1 !== $rLoad['closed']) {
                    $tab2 .= "<td align=center><img onclick=datakeExcel2(event,'".$rLoad['tahunbudget']."','".$afd."','".$rLoad['sumber']."') src=images/excel.jpg class=resicon title='MS.Excel'>&nbsp;\r\n                    <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rLoad['tahunbudget']."','".$rLoad['kodeblok']."','".$rLoad['sumber']."','".$rLoad['topografi']."');\"></td>";
                } else {
                    $tab2 .= '<td>&nbsp;</td>';
                }

                $tab2 .= '</tr>';
            }
            $tab2 .= "\r\n\t\t<tr class=rowheader><td colspan=11 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
            echo $tab;

            break;
        }

        exit('Error:Tahun Budget Sudah Tutup');
    case 'getPreviewBr':
        $sCek = 'select distinct * from '.$dbname.".bgt_blok where tahunbudget='".$thnAngBr."' and kodeblok like '%".$afdId."%' and sumber='BARU'";
        $qCek = mysql_query($sCek) || exit(mysql_error($conns));
        $rCek = mysql_num_rows($qCek);
        if ($jmlh < 1) {
            $sCek2 = 'select tahunbudget, kodeblok as kodeorg, hathnlalu as luasareaproduktif, hathnini, pokokthnlalu as jumlahpokok, pokokthnini , statusblok, topografi, thntnm as tahuntanam, hanonproduktif from '.$dbname.".bgt_blok \r\n            where kodeblok like '%".$afdIdBr."%' and tahunbudget='".$thnAngBr."' and sumber='LAMA'";
        } else {
            if (1 < $jmlh) {
                $sCek2 = 'select tahunbudget, kodeblok as kodeorg, hathnlalu as luasareaproduktif, hathnini, pokokthnlalu as jumlahpokok, pokokthnini , statusblok, topografi, thntnm as tahuntanam, hanonproduktif from '.$dbname.".bgt_blok \r\n            where kodeblok like '%".$afdIdBr."%' and tahunbudget='".$thnAngBr."' and sumber='BARU'";
            }
        }

        $qCek2 = mysql_query($sCek2) || exit(mysql_error($conns));
        $rRicek = mysql_num_rows($qCek2);
        if (1 < $rRicek) {
            $tab = "<table cellpadding=1 cellspacing=1 border=0 class=sortable>\r\n            <thead>\r\n            <tr class=rowheader>\r\n            <td>No</td>\r\n            <td>".$_SESSION['lang']['tahun']."</td>\r\n            <td>".$_SESSION['lang']['kebun']."</td>\r\n            <td>".$_SESSION['lang']['afdeling']."</td>\r\n            <td>".$_SESSION['lang']['blok']."</td>\r\n            <td>".$_SESSION['lang']['hathnini']."</td>\r\n            <td>".$_SESSION['lang']['pokokthnini']."</td>\r\n            <td>".$_SESSION['lang']['statusblok']."</td>\r\n            <td>".$_SESSION['lang']['topografi']."</td>\r\n            <td>".$_SESSION['lang']['thntnm']."</td>\r\n            <td>".$_SESSION['lang']['hanonproduktif']."</td>\r\n            <td>".$_SESSION['lang']['pkkproduktif']."</td>\r\n            <td>Action</td>\r\n            </tr>\r\n            </thead><tbody>";
            $tot = count($arrStatusBlok);
            $b = 1;
            while ($res = mysql_fetch_assoc($qCek2)) {
                $kbn = substr($res['kodeorg'], 0, 4);
                $afd = substr($res['kodeorg'], 4, 2);
                $blk = substr($res['kodeorg'], 6, 4);
                ++$no;
                $tab .= '<tr class=rowcontent id=rewBr_'.$no.'>';
                $tab .= '<td>'.$no.'</td>';
                $tab .= '<td>'.$thnAngBr.'</td>';
                $tab .= '<td id=kdKbn_'.$no.'>'.$kbn.'</td>';
                $tab .= "<td><input type='text' id='kdAfdling_".$no."' value='".$afd."' class='myinputtextnumber' style='width:45px' maxlength=2 onkeypress='return tanpa_kutip(event)' disabled /></td>";
                $tab .= "<td><input type='text' id='kdBlokBr_".$no."' value='".$blk."' class='myinputtextnumber' style='width:45px' maxlength=4 onkeypress='return tanpa_kutip(event)' /></td>";
                $tab .= '<td><input type=text id=hathnIniBr_'.$no."  class=myinputtextnumber onkeypress='return angka_doang(event)' value=".$res['hathnini']." style='width:55px' /></td>";
                $tab .= '<td><input type=text id=pokokThnINiBr_'.$no." class=myinputtextnumber onkeypress='return angka_doang(event)' value=".$res['pokokthnini']." style='width:75px' /></td>";
                $tab .= '<td><select id=statBlokBr_'.$no.'>'.$arrOptBlok[$res['tahunbudget']][$res['kodeorg']].'</select></td>';
                $tab .= "<td><select id='topoGrafiBr_".$no."'>".$arrOptTopo[$res['tahunbudget']][$res['kodeorg']]."</select></td><td id='thnTmnBr_".$no."' align='center'>".$res['tahuntanam'].'</td>';
                $tab .= '<td><input type=text id=haNonBr_'.$no."  class=myinputtextnumber onkeypress='return angka_doang(event)' value=".$res['hanonproduktif']." style='width:85px' /></td>";
                $tab .= "<td align=center><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"addDetail(".$no.")\" src='images/save.png'/></td>";
                $tab .= '</tr>';
            }
            $tab .= "<tr><td colspan='12' align=center><button class=mybutton  onclick=saveAllBr(1)  style=cursor:pointer;>".$_SESSION['lang']['save'].' '.$_SESSION['lang']['all'].'</button></td></tr>';
            $tab .= '</tbody></table><input type=hidden id=jmlhRow value='.$no.' />';
            echo $tab;

            break;
        }

        exit('Error:No data fund');
    case 'insertAllBr':
        if (strlen($kdBlokBr) < 10) {
            exit('Error:Block code required');
        }

        if ('' === $thnTmnBr) {
            exit('Error:Panting year required');
        }

        if (strlen($thnTmnBr) < 4) {
            exit('Error:Panting year incorrect');
        }

        if ('' === $haThnIniBr || '' === $pokokThnIniBr) {
            $sDel = 'delete from '.$dbname.".bgt_blok where tahunbudget='".$thnAngrnOld."' and kodeblok='".$oldBlok."' and topografi='".$topoGrafOld."' and sumber='BARU'";
            if (mysql_query($sDel)) {
                exit('Error:Ha this year and numbers of trees are required');
            }

            echo 'DB Error : '.$sDel."\n".mysql_error($conn);
        }

        $sCek = 'select * from '.$dbname.".bgt_blok where tahunbudget='".$thnAngrnOld."' and kodeblok='".$oldBlok."' and topografi='".$topoGrafOld."' and sumber='BARU' and  closed=1";
        $qCek = mysql_query($sCek) || exit(mysql_error($conns));
        $rCek = mysql_num_rows($qCek);
        if (1 !== $rCek) {
            $sCek2 = 'select * from '.$dbname.".bgt_blok where tahunbudget='".$thnAngrnOld."' and kodeblok='".$oldBlok."' and topografi='".$topoGrafOld."' and sumber='BARU'";
            $qCek2 = mysql_query($sCek2) || exit(mysql_error($conns));
            $rCek2 = mysql_num_rows($qCek2);
            if (0 < $rCek2) {
                $sDel = 'delete from '.$dbname.".bgt_blok where tahunbudget='".$thnAngrnOld."' and kodeblok='".$oldBlok."' and topografi='".$topoGrafOld."' and sumber='BARU'";
                if (mysql_query($sDel)) {
                    if ('TM' === $statBlokBr && $pokokThnIniBr < $pkkProdukBr) {
                        exit('Error:Number of productive trees should not be more than the amount of this year');
                    }

                    $tmbhn = '';
                    $tmbhn2 = '';
                    if ('P' === $_POST['statPlasmaBr']) {
                        $tmbhn = ',`intiplasma`';
                        $tmbhn2 = ",'".$_POST['statPlasmaBr']."'";
                    }

                    $sInsert = 'insert into '.$dbname.'.bgt_blok (tahunbudget, kodeblok,  hathnini,  pokokthnini, statusblok, topografi, thntnm, hanonproduktif, sumber, updateby,pokokproduksi,lcthnini'.$tmbhn.") \r\n                              value ('".$thnAngBr."','".$kdBlokBr."','".$haThnIniBr."','".$pokokThnIniBr."','".$statBlokBr."','".$topoGrafiBr."','".$thnTmnBr."','".$haNonBr."','BARU','".$_SESSION['standard']['userid']."','".$pkkProdukBr."','".$lcThnBr."'".$tmbhn2.')';
                    if (mysql_query($sInsert)) {
                        echo '';
                    } else {
                        echo 'DB Error : '.$sInsert."\n".mysql_error($conn);
                    }
                } else {
                    echo 'DB Error : '.$sDel."\n".mysql_error($conn);
                }
            } else {
                if ($pokokThnIniBr < $pkkProdukBr) {
                    exit('Error:Number of productive trees should not be more than the amount of this year');
                }

                $sInsert = 'insert into '.$dbname.".bgt_blok (tahunbudget, kodeblok,  hathnini,  pokokthnini, statusblok, topografi, thntnm, hanonproduktif, sumber, updateby,pokokproduksi) \r\n                    value ('".$thnAngBr."','".$kdBlokBr."','".$haThnIniBr."','".$pokokThnIniBr."','".$statBlokBr."','".$topoGrafiBr."','".$thnTmnBr."','".$haNonBr."','BARU','".$_SESSION['standard']['userid']."','".$pkkProdukBr."')";
                if (mysql_query($sInsert)) {
                    echo '';
                } else {
                    echo 'DB Error : '.$sInsert."\n".mysql_error($conn);
                }
            }
        }

        break;
    case 'loadData':
        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.".bgt_blok where  sumber='BARU' and kodeblok like '%".$_SESSION['empl']['lokasitugas']."%' order by tahunbudget desc";
        $query2 = mysql_query($ql2) || exit(mysql_error($conns));
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $sLoad = 'select * from '.$dbname.".bgt_blok where sumber='BARU' and kodeblok like '%".$_SESSION['empl']['lokasitugas']."%'   order by tahunbudget desc limit ".$offset.','.$limit.'';
        $qLoad = mysql_query($sLoad) || exit(mysql_error($conns));
        while ($rLoad = mysql_fetch_assoc($qLoad)) {
            $kbn = substr($rLoad['kodeblok'], 0, 4);
            $afd = substr($rLoad['kodeblok'], 4, 2);
            $blk = substr($rLoad['kodeblok'], 6, 4);
            ++$no2;
            $tab .= '<tr class=rowcontent id=rewBr_'.$no2.'>';
            $tab .= '<td>'.$no2.'</td>';
            $tab .= '<td>'.$rLoad['tahunbudget'].'</td>';
            $tab .= '<td id=kdKbn_'.$no2.'>'.$kbn.'</td>';
            $tab .= '<td>'.$afd.'</td>';
            $tab .= '<td>'.$blk.'</td>';
            $tab .= '<td align=right>'.$rLoad['hathnini'].'</td>';
            $tab .= '<td align=right>'.$rLoad['pokokthnini'].'</td>';
            $tab .= '<td>'.$arrStatusBlok[$rLoad['statusblok']].'</td>';
            $tab .= '<td>'.$optTopografi[$rLoad['topografi']]."</td><td id='thnTmnBr_".$no2."' align='center'>".$rLoad['thntnm'].'</td>';
            $tab .= '<td align=right>'.$rLoad['lcthnini'].'</td>';
            $tab .= '<td align=right>'.$rLoad['hanonproduktif'].'</td>';
            $tab .= '<td align=right>'.$rLoad['pokokproduksi'].'</td>';
            if (1 !== $rLoad['closed']) {
                $tab .= "<td align=center><img onclick=datakeExcel2(event,'".$rLoad['tahunbudget']."','".$_SESSION['empl']['lokasitugas'].$afd."','".$rLoad['sumber']."') src=images/excel.jpg class=resicon title='MS.Excel'>&nbsp;\r\n                    <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rLoad['tahunbudget']."','".$rLoad['kodeblok']."','".$rLoad['sumber']."','".$rLoad['topografi']."');\"></td>";
            } else {
                $tab .= "<td align=center><img onclick=datakeExcel2(event,'".$rLoad['tahunbudget']."','".$_SESSION['empl']['lokasitugas'].$afd."','".$rLoad['sumber']."') src=images/excel.jpg class=resicon title='MS.Excel'></td>";
            }

            $tab .= '</tr>';
        }
        $tab .= "\r\n\t\t<tr class=rowheader><td colspan=11 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
        echo $tab;

        break;
    case 'getData':
        $sCek2 = 'select * from '.$dbname.".bgt_blok where tahunbudget='".$thnAngBr."' and kodeblok='".$kdBlokBr."' and topografi='".$topoGrafiBr."' and sumber='".$sumber."'";
        $qCek2 = mysql_query($sCek2) || exit(mysql_error($conns));
        $tab = "<table cellpadding=1 cellspacing=1 border=0 class=sortable>\r\n            <thead>\r\n            <tr class=rowheader>\r\n            <td>No</td>\r\n            <td>".$_SESSION['lang']['tahun']."</td>\r\n            <td>".$_SESSION['lang']['kebun']."</td>\r\n            <td>".$_SESSION['lang']['afdeling']."</td>\r\n            <td>".$_SESSION['lang']['blok']."</td>\r\n            <td>".$_SESSION['lang']['hathnini']."</td>\r\n            <td>".$_SESSION['lang']['pokokthnini']."</td>\r\n            <td>".$_SESSION['lang']['statusblok']."</td>\r\n            <td>".$_SESSION['lang']['topografi']."</td>\r\n            <td>".$_SESSION['lang']['thntnm']."</td>\r\n            <td>".$_SESSION['lang']['lcthnini']."</td>\r\n            <td>".$_SESSION['lang']['hanonproduktif']."</td>\r\n            <td>".$_SESSION['lang']['pkkproduktif']."</td>\r\n            <td>Intiplasma</td>\r\n            <td>Action</td>\r\n            </tr>\r\n            </thead><tbody>";
        while ($res = mysql_fetch_assoc($qCek2)) {
            $kbn = substr($res['kodeblok'], 0, 4);
            $afd = substr($res['kodeblok'], 4, 2);
            $blk = substr($res['kodeblok'], 6, 4);
            $topograf = $res['topografi'];
            $blok = $res['kodeblok'];
            ++$no;
            $statDtPlsma = '';
            if ('P' === $res['intiplasma']) {
                $statDtPlsma = 'checked';
            }

            $tab .= '<tr class=rowcontent id=rewBr_'.$no.'>';
            $tab .= '<td>'.$no.'</td>';
            $tab .= '<td>'.$thnAngBr.'</td>';
            $tab .= '<td id=kdKbn_'.$no.'>'.$kbn.'</td>';
            $tab .= "<td><input type='text' id='kdAfdling_".$no."' value='".$afd."' class='myinputtextnumber' style='width:45px' maxlength=2 onkeypress='return tanpa_kutip(event)' disabled /></td>";
            $tab .= "<td><input type='text' id='kdBlokBr_".$no."' value='".$blk."' class='myinputtextnumber' style='width:45px' maxlength=4 onkeypress='return tanpa_kutip(event)' /></td>";
            $tab .= '<td><input type=text id=hathnIniBr_'.$no."  class=myinputtextnumber onkeypress='return angka_doang(event)' value=".$res['hathnini']." style='width:55px' /></td>";
            $tab .= '<td><input type=text id=pokokThnINiBr_'.$no." class=myinputtextnumber onkeypress='return angka_doang(event)' value=".$res['pokokthnini']." style='width:75px' /></td>";
            $tab .= '<td><select id=statBlokBr_'.$no.'>'.$arrOptBlok[$res['kodeblok']].'</select></td>';
            $tab .= "<td><select id='topoGrafiBr_".$no."'>".$arrOptTopo[$res['kodeblok']]."</select></td><td><input type='text' id='thnTmnBr_".$no."'  class='myinputtextnumber' onkeypress='return angka_doang(event)' value=".$res['thntnm']." style='width:75px' maxlength='4' /></td>";
            $tab .= '<td><input type=text id=lcThnBr_'.$no."  class=myinputtextnumber onkeypress='return angka_doang(event)' value=".$res['lcthnini']." style='width:85px' /></td>";
            $tab .= '<td><input type=text id=haNonBr_'.$no."  class=myinputtextnumber onkeypress='return angka_doang(event)' value=".$res['hanonproduktif']." style='width:85px' /></td>";
            $tab .= '<td><input type=text id=pkkProdukBr_'.$no."  class=myinputtextnumber onkeypress='return angka_doang(event)' value=".$res['pokokproduksi']." style='width:85px' /></td>";
            $tab .= "<td align=center><input type=checkbox  id='statPlasmaBr_".$no."' title='clik jika plasma' ".$statDtPlsma.' /></td>';
            $tab .= "<td align=center style='cursor:pointer;'><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"addDetail(".$no.")\" src='images/save.png'/></td>";
            $tab .= '</tr>';
        }
        $tab .= '</tbody></table><input type=hidden id=jmlhRow value='.$no.' /><input type=hidden id=thnAngrnOld value='.$thnAngBr.' /><input type=hidden id=oldBlok value='.$blok.' /><input type=hidden id=topoGrafOld value='.$topograf.' />';
        echo $tab;

        break;
    case 'prosesClose':
        $thn = date('Y');
        if ('' === $thnAngBr) {
            exit('Error:Budget year required');
        }

        if (strlen($thnAngBr) < 4) {
            exit('Error:Budget year incorrect');
        }

        if (substr($thn, 0, 1) !== substr($thnAngBr, 0, 1)) {
            exit('Error:Budget year incorrect');
        }

        $sRcek = 'select * from '.$dbname.".bgt_blok where tahunbudget='".$thnAngBr."' and kodeblok like '%".$_SESSION['empl']['lokasitugas']."%' and closed=1";
        $qRcek = mysql_query($sRcek) || exit(mysql_error($conns));
        $rRcek = mysql_num_rows($qRcek);
        if (1 <= $rRcek) {
            exit('Error:Budget has been closed');
        }

        $sUpdatae = 'update '.$dbname.".bgt_blok set closed=1 where tahunbudget='".$thnAngBr."' and kodeblok like '%".$_SESSION['empl']['lokasitugas']."%'";
        if (mysql_query($sUpdatae)) {
            echo '1';
        } else {
            echo 'DB Error : '.$sUpdatae."\n".mysql_error($conn);
        }

        break;
    case 'loadDataLama':
        $sJum = 'select * from '.$dbname.".bgt_blok where sumber='LAMA'  group by kodeblok";
        $qJum = mysql_query($sJum) || exit(mysql_error($conns));
        $rJum = mysql_num_rows($qJum);
        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $sLoad2 = 'select distinct substring(kodeblok,1,6) as kodeblok,tahunbudget,closed,sumber  from '.$dbname.".bgt_blok where sumber='LAMA' and kodeblok like '%".$_SESSION['empl']['lokasitugas']."%' group by tahunbudget,kodeblok order by tahunbudget desc";
        $qLoad2 = mysql_query($sLoad2) || exit(mysql_error($conns));
        $rLoad2 = mysql_num_rows($qLoad2);
        $sLoad = 'select distinct substring(kodeblok,1,6) as kodeblok,tahunbudget,closed,sumber  from '.$dbname.".bgt_blok where sumber='LAMA' and kodeblok like '%".$_SESSION['empl']['lokasitugas']."%' group by tahunbudget,kodeblok order by tahunbudget desc  limit ".$offset.','.$limit.'';
        $qLoad = mysql_query($sLoad) || exit(mysql_error($conns));
        while ($rLoad = mysql_fetch_assoc($qLoad)) {
            $sJum = 'select count(kodeblok) as jmlh from '.$dbname.".bgt_blok where sumber='LAMA' and  kodeblok like '%".$rLoad['kodeblok']."%'";
            $qJum = mysql_query($sJum) || exit(mysql_error($conns));
            $rJum = mysql_fetch_assoc($qJum);
            $kbn = substr($rLoad['kodeblok'], 0, 4);
            ++$no;
            (1 === $rLoad['closed'] ? ($stat = 'Close') : ($stat = 'Open'));
            $tab .= '<tr class=rowcontent id=rewBr_'.$no.'>';
            $tab .= '<td>'.$no.'</td>';
            $tab .= '<td>'.$rLoad['tahunbudget'].'</td>';
            $tab .= '<td id=kdKbn_'.$no.'>'.$kbn.'</td>';
            $tab .= '<td>'.$rLoad['kodeblok'].'</td>';
            $tab .= '<td align=right>'.$stat.'</td>';
            if (1 !== $rLoad['closed']) {
                $tab .= "<td align=center><img onclick=datakeExcel(event,'".$rLoad['tahunbudget']."','".$rLoad['kodeblok']."','".$rLoad['sumber']."') src=images/excel.jpg class=resicon title='MS.Excel'>&nbsp;\r\n                    <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editData('".$rJum['jmlh']."','".$rLoad['tahunbudget']."','".$rLoad['kodeblok']."','".$rLoad['sumber']."');\"></td>";
            } else {
                $tab .= "<td align=center><img onclick=datakeExcel(event,'".$rLoad['tahunbudget']."','".$rLoad['kodeblok']."','".$rLoad['sumber']."') src=images/excel.jpg class=resicon title='MS.Excel'></td>";
            }

            $tab .= '</tr>';
        }
        $tab .= "\r\n\t\t<tr class=rowheader><td colspan=11 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$rLoad2."<br />\r\n\t\t<button class=mybutton onclick=cariLoad(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariLoad(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
        echo $tab;

        break;
    case 'printExcel':
        $tab = "<table cellpadding=1 cellspacing=1 border=1 class=sortable>\r\n            <thead>\r\n            <tr class=rowheader>\r\n            <td align=center bgcolor=#DEDEDE>No</td>\r\n            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['blok']."</td>\r\n            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['hathnlalu']."</td>\r\n            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['hathnini']."</td>\r\n            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['pokokthnlalu']."</td>\r\n            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['pokokthnini']."</td>\r\n            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['statusblok']."</td>\r\n            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['topografi']."</td>\r\n            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['thntnm']."</td>\r\n            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['hanonproduktif']."</td>\r\n            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['pkkproduktif']."</td>\r\n            </tr>\r\n            </thead><tbody>";
        $sCek2 = 'select * from '.$dbname.".bgt_blok where tahunbudget='".$_GET['thnAngrn']."' and sumber='".$_GET['sumber']."' and kodeblok like '%".$_GET['afdId']."%'";
        $qCek2 = mysql_query($sCek2) || exit(mysql_error($conns));
        $rCek = mysql_num_rows($qCek2);
        while ($res = mysql_fetch_assoc($qCek2)) {
            ++$no;
            $tab .= '<tr class=rowcontent id=rew_'.$no.'>';
            $tab .= '<td>'.$no.'</td>';
            $tab .= '<td>'.$res['tahunbudget'].'</td><td id=kdBlok_'.$no.'>'.$res['kodeblok'].'</td><td  align=right>'.number_format($res['hathnlalu'], 2).'</td>';
            $tab .= '<td  align=right>'.number_format($res['hathnini'], 2).'</td>';
            $tab .= '<td  align=right>'.number_format($res['pokokthnlalu'], 2).'</td>';
            $tab .= '<td  align=right>'.number_format($res['pokokthnini'], 2).'</td>';
            $tab .= '<td align=center>'.$res['statusblok'].'</td>';
            $tab .= '<td>'.$res['topografi'].'-'.$optTopografi[$res['topografi']]."</td><td id='thnTmn_".$no."' align='center'>".$res['thntnm'].'</td>';
            $tab .= '<td align=right>'.number_format($res['hanonproduktif'], 2).'</td>';
            $tab .= '<td align=right>'.number_format($res['pokokproduksi'], 2).'</td>';
            $tab .= '</tr>';
        }
        $tab .= '</tbody></table>';
        $nop_ = 'list_data_'.$_GET['afdId'];
        if (0 < strlen($tab)) {
            if ($handle = opendir('tempExcel')) {
                while (false !== ($file = readdir($handle))) {
                    if ('.' !== $file && '..' !== $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $tab)) {
                echo "<script language=javascript1.2>\r\n        parent.window.alert('Can't convert to excel format');\r\n        </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls';\r\n        </script>";
            closedir($handle);
        }

        break;
    case 'printExcel2':
        $tab .= "<table cellpadding=1 cellspacing=1 border=1 class=sortable>\r\n            <thead>\r\n            <tr class=rowheader>\r\n            <td align=center bgcolor=#DEDEDE>No</td>\r\n            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['tahun']."</td>\r\n            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['kebun']."</td>\r\n            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['afdeling']."</td>\r\n            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['blok']."</td>\r\n            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['hathnini']."</td>\r\n            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['pokokthnini']."</td>\r\n            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['statusblok']."</td>\r\n            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['topografi']."</td>\r\n            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['thntnm']."</td>\r\n            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['hanonproduktif']."</td>\r\n            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['pkkproduktif']."</td>\r\n            </tr>\r\n            </thead><tbody id=containDetail>";
        $sLoad = 'select * from '.$dbname.".bgt_blok where sumber='BARU' and tahunbudget='".$_GET['thnAngrn']."' and kodeblok like '%".$_GET['afdId']."%' order by tahunbudget desc ";
        $qLoad = mysql_query($sLoad) || exit(mysql_error($conns));
        while ($rLoad = mysql_fetch_assoc($qLoad)) {
            $kbn = substr($rLoad['kodeblok'], 0, 4);
            $afd = substr($rLoad['kodeblok'], 4, 2);
            $blk = substr($rLoad['kodeblok'], 6, 4);
            ++$no;
            $tab .= '<tr class=rowcontent id=rewBr_'.$no.'>';
            $tab .= '<td>'.$no.'</td>';
            $tab .= '<td>'.$rLoad['tahunbudget'].'</td>';
            $tab .= '<td>'.$kbn.'</td>';
            $tab .= "<td>'".$afd.'</td>';
            $tab .= '<td>'.$blk.'</td>';
            $tab .= '<td align=right>'.$rLoad['hathnini'].'</td>';
            $tab .= '<td align=right>'.$rLoad['pokokthnini'].'</td>';
            $tab .= '<td>'.$rLoad['statusblok'].'</td>';
            $tab .= '<td>'.$optTopografi[$rLoad['topografi']]."</td><td id='thnTmnBr_".$no."' align='center'>".$rLoad['thntnm'].'</td>';
            $tab .= '<td align=right>'.$rLoad['hanonproduktif'].'</td>';
            $tab .= '<td align=right>'.$rLoad['pokokproduksi'].'</td>';
        }
        $tab .= '</tbody></table>';
        $nop_ = 'list_data_blok_baru'.$_GET['afdId'];
        if (0 < strlen($tab)) {
            if ($handle = opendir('tempExcel')) {
                while (false !== ($file = readdir($handle))) {
                    if ('.' !== $file && '..' !== $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $tab)) {
                echo "<script language=javascript1.2>\r\n        parent.window.alert('Can't convert to excel format');\r\n        </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls';\r\n        </script>";
            closedir($handle);
        }

        break;
    case 'getThnBudgt':
        $optThn = "<option value=''>".$_SESSION['lang']['budgetyear'].'</option>';
        $sThn = 'select distinct tahunbudget from '.$dbname.".bgt_blok where kodeblok like '%".$_SESSION['empl']['lokasitugas']."%'";
        $qThn = mysql_query($sThn) || exit(mysql_error($conns));
        while ($rThn = mysql_fetch_assoc($qThn)) {
            $optThn .= '<option value='.$rThn['tahunbudget'].'>'.$rThn['tahunbudget'].'</option>';
        }
        echo $optThn;

        break;
    default:
        break;
}

?>