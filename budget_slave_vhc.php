<?php



session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
('' === $_POST['proses'] ? ($proses = $_GET['proses']) : ($proses = $_POST['proses']));
$kdTraksi = $_POST['kdTraksi'];
$kdVhc = $_POST['kdVhc'];
$thnBudget = $_POST['thnBudget'];
$kodeOrg = $_POST['kdOrg'];
$kdVhc = $_POST['kdVhc'];
$jmlhPerson = $_POST['jmlhPerson'];
$kdGol = $_POST['kdGol'];
$hkEfektif = $_POST['hkEfektif'];
$tipeBudget = $_POST['tipeBudget'];
$totBiaya = $_POST['totBiaya'];
$nmBrg = $_POST['nmBrg'];
$klmpkBrg = $_POST['klmpkBrg'];
$idData = $_POST['idData'];
$kdBudget = $_POST['kdBudget'];
$kdBrg = $_POST['kdBrg'];
$jmlhBrg = $_POST['jmlhBrg'];
$satuanBrg = $_POST['satuanBrg'];
$totHarga = $_POST['totHarga'];
$optNmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun');
$kdBudgetB = $_POST['kdBudgetB'];
$noAkun = $_POST['noAkun'];
$totBiayaB = $_POST['totBiayaB'];
$kdBudgetS = $_POST['kdBudgetS'];
$kdWorkshop = $_POST['kdWorkshop'];
$jmlhJam = $_POST['jmlhJam'];
$totHargaJam = $_POST['totHargaJam'];
$where2 = " kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' and tipebudget='TRK' and tahunbudget='".$thnBudget."'";
switch ($proses) {
    case 'getVhc':
        $optVhc = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sVhc = 'select distinct kodevhc from '.$dbname.".vhc_5master where kodetraksi='".$kdTraksi."'";
        $qVhc = mysql_query($sVhc);
        while ($rVhc = mysql_fetch_assoc($qVhc)) {
            if ('' !== $kdVhc) {
                $optVhc .= "<option value='".$rVhc['kodevhc']."' ".(($kdVhc === $rVhc['kodevhc'] ? 'selected' : '')).'>'.$rVhc['kodevhc'].'</option>';
            } else {
                $optVhc .= "<option value='".$rVhc['kodevhc']."'>".$rVhc['kodevhc'].'</option>';
            }
        }
        echo $optVhc;

        break;
    case 'cekSave':
        if ('' === $thnBudget || '' === $kodeOrg || '' === $kdVhc) {
            exit('Error: Budget year, Org code, Vhc code are obligatory');
        }

        if (strlen($thnBudget) < 4) {
            exit('Error:Budget year required');
        }

/*         $sCek = 'select distinct tutup from '.$dbname.".bgt_budget where tahunbudget='".$thnBudget."' and kodeorg='".$kodeOrg."'";
        $qCek = mysql_query($sCek);
        $rCek = mysql_fetch_assoc($qCek);
        if (0 !== $rCek['tutup']) {
            exit('Error:  Budget year '.$thnBudget.' has been closed,Can not add data');
        }
 */
        $sHk = 'select distinct * from '.$dbname.".bgt_hk where tahunbudget='".$thnBudget."'";
        $qHk = mysql_query($sHk);
        $rHk = mysql_fetch_assoc($qHk);
        $hkEfektip = (int) ($rHk['harisetahun']) - (int) ($rHk['hrminggu']) - (int) ($rHk['hrlibur']) + (int) ($rHk['hrliburminggu']);
        $optWs = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sWs = 'select distinct kodews from '.$dbname.".bgt_ws_jam where tahunbudget='".$thnBudget."' and kodetraksi='".$kodeOrg."'";
        $qWs = mysql_query($sWs) || exit(mysql_error($conns));
        while ($rWs = mysql_fetch_assoc($qWs)) {
            $optWs .= "<option value='".$rWs['kodews']."'>".$optNm[$rWs['kodews']].'</option>';
        }
        echo $hkEfektip.'###'.$optWs;

        break;
    case 'getUpah':
        if ('' === $kdGol) {
            exit('Error: Budget code required');
        }

        //$sUpah = 'select jumlah from '.$dbname.".bgt_upah where tahunbudget='".$thnBudget."' and kodeorg='".$kodeOrg."' and golongan='".$kdGol."' and closed=1";
		
        //$sUpah = 'select jumlah from '.$dbname.".bgt_upah where tahunbudget='".$thnBudget."' and kodeorg='".$kodeOrg."' and golongan='".$kdGol."' and closed=0";
		$query = "SELECT kodeorganisasi, namaorganisasi,induk FROM organisasi WHERE kodeorganisasi='".$kodeOrg."'";
		//echo $query; ambil nama organisasi dan kode;
		$chOrg = mysql_query($query);
		while($dtOrg= mysql_fetch_assoc($chOrg)){
			$kodeorganisasi = $dtOrg['kodeorganisasi'];
			$namaorganisasi = $dtOrg['namaorganisasi'];
			$indukorg = $dtOrg['induk'];
		}
		
		$query ="SELECT jumlah from bgt_upah WHERE kodeorg ='".$kodeOrg."' and tahunbudget=".$thnBudget." and golongan='".$kdGol."' and closed=0";
		$runChk = mysql_query($query);
		$row= mysql_num_rows($runChk);
		if($row >= 1){
			$sUpah = $query;
		}else{
			$sUpah = "SELECT jumlah from bgt_upah WHERE kodeorg ='".$indukorg."' and tahunbudget=".$thnBudget." and golongan='".$kdGol."' and closed=0";
		}
		$row;
        $qUpah = mysql_query($sUpah);
        $row = mysql_num_rows($qUpah);
        if ($row < 1) {
			$sUpah = "SELECT jumlah from bgt_upah WHERE kodeorg LIKE '".substr($kodeOrg, 0, 3) ."%' and tahunbudget=".$thnBudget." and golongan='".$kdGol."' and closed=0";
        }
		//echo $sUpah;
		$qUpah = mysql_query($sUpah);
        $row = mysql_num_rows($qUpah);
        if (0 !== $row) {
            $rUpah = mysql_fetch_assoc($qUpah);
            if ('0' === $rUpah['jumlah']) {
                exit('Error:Daily salary not exist');
            }			
            $totalUpah = (float) ($rUpah['jumlah']) * (float) $jmlhPerson * (float) $hkEfektif;
            echo $totalUpah;        
        }else{
			echo $sUpah;
		}
	break;
        //exit('Error: Data not closed, please re-check');
    case 'saveSdm':
        if ('' === $kdGol || '' === $jmlhPerson || 0 === $totBiaya) {
            exit('Error: Field can not be empty or 0');
        }

        $sCek = 'select * from '.$dbname.".bgt_budget where tahunbudget='".$thnBudget."' and kodeorg='".$kodeOrg."' and tipebudget='".$tipeBudget."' and kodebudget='".$kdGol."' and kodevhc='".$kdVhc."'";
        $qCek = mysql_query($sCek) || exit(mysql_error($conns));
        $rCek = mysql_num_rows($qCek);
        if ($rCek < 1) {
            $vol = (float) $jmlhPerson * (float) $hkEfektif;
            $sIns = 'insert into '.$dbname.".bgt_budget (tahunbudget, kodeorg, tipebudget, kodebudget,kodevhc,rupiah,jumlah, satuanj,updateby,volume, satuanv) values\r\n                  ('".$thnBudget."','".$kodeOrg."','".$tipeBudget."','".$kdGol."','".$kdVhc."','".$totBiaya."','".$jmlhPerson."','orang','".$_SESSION['standard']['userid']."','".$vol."','HK')";
            if (mysql_query($sIns)) {
                echo '';
            } else {
                echo 'DB Error : '.$sIns."\n".mysql_error($conn);
            }

            break;
        }

        exit('Error:Data already exist');
    case 'saveMat':
        if ('' === $kdBudget || '' === $kdBrg || 0 === $totHarga || '' === $jmlhBrg) {
            exit('Error: Field can not be empty or 0');
        }

        $sCek = 'select * from '.$dbname.".bgt_budget where tahunbudget='".$thnBudget."' and kodeorg='".$kodeOrg."' and tipebudget='".$tipeBudget."' and kodebudget='".$kdBudget."' and kodebarang='".$kdBrg."' and kodevhc='".$kdVhc."'";
        $qCek = mysql_query($sCek) || exit(mysql_error($conns));
        $rCek = mysql_num_rows($qCek);
        if ($rCek < 1) {
            $sRegion = 'select distinct regional from '.$dbname.".bgt_regional_assignment where kodeunit='".substr($kodeOrg, 0, 4)."'";
            $qRegion = mysql_query($sRegion) || exit(mysql_error($conns));
            $rRegion = mysql_fetch_assoc($qRegion);
            $sIns = 'insert into '.$dbname.".bgt_budget (tahunbudget, kodeorg, tipebudget, kodebudget,kodevhc,rupiah, kodebarang, regional, updateby,jumlah,satuanj) values\r\n                  ('".$thnBudget."','".$kodeOrg."','".$tipeBudget."','".$kdBudget."','".$kdVhc."','".$totHarga."','".$kdBrg."','".$rRegion['regional']."','".$_SESSION['standard']['userid']."','".$jmlhBrg."','".$satuanBrg."')";
            if (mysql_query($sIns)) {
                echo '';
            } else {
                echo 'DB Error : '.$sIns."\n".mysql_error($conn);
            }

            break;
        }

        exit('Error: Data already exist');
    case 'saveLain':
        if ('' === $kdBudgetB || 0 === $totBiayaB || '' === $noAkun) {
            exit('Error: Field can not be empty or 0');
        }

        $sCek = 'select * from '.$dbname.".bgt_budget where tahunbudget='".$thnBudget."' \r\n                and kodeorg='".$kodeOrg."' and tipebudget='".$tipeBudget."' \r\n                and kodebudget='".$kdBudgetB."' and noakun='".$noAkun."' and kodevhc='".$kdVhc."'";
        $qCek = mysql_query($sCek) || exit(mysql_error($conns));
        $rCek = mysql_num_rows($qCek);
        if ($rCek < 1) {
            $sIns = 'insert into '.$dbname.".bgt_budget (tahunbudget, kodeorg, tipebudget, kodebudget,kodevhc, noakun,rupiah,updateby) values\r\n                  ('".$thnBudget."','".$kodeOrg."','".$tipeBudget."','".$kdBudgetB."','".$kdVhc."','".$noAkun."','".$totBiayaB."','".$_SESSION['standard']['userid']."')";
            if (mysql_query($sIns)) {
                echo '';
            } else {
                echo 'DB Error : '.$sIns."\n".mysql_error($conn);
            }

            break;
        }

        exit('Error: Data already exist');
    case 'saveService':
        if ('' === $kdBudgetS || 0 === $totHargaJam || '' === $jmlhJam) {
            exit("Error:Field tidak boleh kosong atau nol \n Jika total harga nol, mohon input alokasi jam bengkel");
        }

        $sCek = 'select * from '.$dbname.".bgt_budget where tahunbudget='".$thnBudget."' and kodeorg='".$kodeOrg."' and tipebudget='".$tipeBudget."' and kodebudget='".$kdBudgetS."' and kodevhc='".$kdVhc."'";
        $qCek = mysql_query($sCek);
        $rCek = mysql_num_rows($qCek);
        if ($rCek < 1) {
            $sIns = 'insert into '.$dbname.".bgt_budget (tahunbudget, kodeorg, tipebudget, kodebudget,kodevhc, rupiah,jumlah,satuanj,updateby) values\r\n                  ('".$thnBudget."','".$kodeOrg."','".$tipeBudget."','".$kdBudgetS."','".$kdVhc."','".$totHargaJam."','".$jmlhJam."','JAM','".$_SESSION['standard']['userid']."')";
            if (mysql_query($sIns)) {
                echo '';
            } else {
                echo 'DB Error : '.$sIns."\n".mysql_error($conn);
            }

            break;
        }

        exit('Error: data already exist');
    case 'loadDataSdm':
        $sLoad = 'select kunci,tahunbudget, kodeorg, tipebudget, kodebudget,kodevhc,rupiah,jumlah, satuanj,volume, satuanv from '.$dbname.".bgt_budget where \r\n            tahunbudget='".$thnBudget."' and kodeorg='".$kodeOrg."' and tipebudget='".$tipeBudget."' and kodevhc='".$kdVhc."' and kodebudget like '%SDM%'";
        $qLoad = mysql_query($sLoad);
        while ($res = mysql_fetch_assoc($qLoad)) {
            ++$no;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$no.'</td>';
            $tab .= "<td align='center'>".$res['kunci'].'</td>';
            $tab .= "<td align='center'>".$res['tahunbudget'].'</td>';
            $tab .= "<td align='center'>".$res['kodeorg'].'</td>';
            $tab .= "<td align='center'>".$res['tipebudget'].'</td>';
            $tab .= "<td align='center'>".$res['kodebudget'].'</td>';
            $tab .= "<td align='center'>".$res['kodevhc'].'</td>';
            $tab .= "<td align='right'>".$res['volume'].'</td>';
            $tab .= "<td align='center'>".$res['satuanv'].'</td>';
            $tab .= "<td  align='right'>".$res['jumlah'].'</td>';
            $tab .= "<td  align='center'>".$res['satuanj'].'</td>';
            $tab .= "<td align='right'>".number_format($res['rupiah'], 2).'</td>';
            $tab .= "<td align=center style='cursor:pointer;'><img id='detail_add' title='delete data' class=zImgBtn onclick=\"deleteSdm(".$res['kunci'].",1)\" src='images/application/application_delete.png'/></td>";
            $tab .= '</tr>';
        }
        echo $tab;

        break;
    case 'getBarang':
        $tab = '<fieldset><legend>'.$_SESSION['lang']['result']."</legend>\r\n                        <div style=\"overflow:auto;height:295px;width:455px;\">\r\n                        <table cellpading=1 border=0 class=sortbale>\r\n                        <thead>\r\n                        <tr class=rowheader>\r\n                        <td>No.</td>\r\n                        <td>".$_SESSION['lang']['kodebarang']."</td>\r\n                        <td>".$_SESSION['lang']['namabarang']."</td>\r\n                        <td>".$_SESSION['lang']['satuan']."</td>\r\n                        </tr><tbody>\r\n                        ";
        if ('' === $nmBrg) {
            $nmBrg = $kdBarang;
        }
		$str = 'select * from '.$dbname.".bgt_masterbarang a JOIN log_5masterbarang b ON a.kodebarang = b.kodebarang 
		where (b.namabarang like '%".$nmBrg."%' or b.kodebarang like '%".$nmBrg."%')";
        $sLoad = 'select kodebarang,namabarang,satuan from '.$dbname.".log_5masterbarang where  kelompokbarang='".substr($klmpkBrg, 2, 3)."' and (kodebarang like '%".$nmBrg."%'\r\n            or namabarang like '%".$nmBrg."%')";
        $qLoad = mysql_query($str);
		
        while ($res = mysql_fetch_assoc($qLoad)) {
            ++$no;
            $tab .= "<tr class=rowcontent onclick=\"setData('".$res['kodebarang']."','".$res['namabarang']."','".$res['satuan']."')\">";
            $tab .= '<td>'.$no.'</td>';
            $tab .= '<td>'.$res['kodebarang'].'</td>';
            $tab .= '<td>'.$res['namabarang'].'</td>';
            $tab .= '<td>'.$res['satuan'].'</td>';
            $tab .= '</tr>';
        }
        echo $tab;

        break;
    case 'getHarga':
        if ('' === $jmlhBrg || '0' === $jmlhBrg) {
            exit('Material volume is empty');
        }

        $sRegion = 'select distinct regional from '.$dbname.".bgt_regional_assignment where kodeunit='".substr($kodeOrg, 0, 4)."' ";
        $qRegion = mysql_query($sRegion) || exit(mysql_error($conns));
        $rRegion = mysql_fetch_assoc($qRegion);
        $sHrg = 'select distinct hargasatuan from '.$dbname.".bgt_masterbarang where regional='".$rRegion['regional']."' and kodebarang='".$kdBrg."' and tahunbudget='".$thnBudget."' and closed=1";
        $qHrg = mysql_query($sHrg);
        $row = mysql_num_rows($qHrg);
        if (0 !== $row) {
            $rHrg = mysql_fetch_assoc($qHrg);
            if ('' !== $rHrg['hargasatuan'] || '0' !== $rHrg['hargasatuan']) {
                $hasil = (float) ($rHrg['hargasatuan']) * (float) $jmlhBrg;
                echo $hasil;
                break;
            }
            exit('Error: Please contact purchase dept');
        }

        //exit('Error:Please contact purchase dept');
    case 'getBiayaService':
        if ('' === $kdBudgetS || '' === $kdWorkshop || '' === $jmlhJam || '0' === $jmlhJam) {
            exit('Working hour is empty');
        }

        $sHrg = 'select distinct rpperjam from '.$dbname.".bgt_biaya_ws_per_jam where tahunbudget='".$thnBudget."' and kodews='".$kdWorkshop."'";
        $qHrg = mysql_query($sHrg);
        $rHrg = mysql_fetch_assoc($qHrg);
        if ('' !== $rHrg['rppertahun'] || '0' !== $rHrg['rppertahun']) {
            $hasil = (float) ($rHrg['rpperjam']) * (float) $jmlhJam;
            echo $hasil;
        }
    break;
        //exit('Error: You are not assigned on a regional, please contact IT');
    case 'delData':
        $sDel = 'delete from '.$dbname.".bgt_budget where kunci='".$idData."'";
        if (mysql_query($sDel)) {
            echo '';
        } else {
            echo 'DB Error : '.$sDel."\n".mysql_error($conn);
        }

        break;
    case 'loadDataMat':
        $sLoad = 'select kunci,tahunbudget, kodeorg, tipebudget, kodebudget,kodevhc,rupiah,jumlah, satuanj,kodebarang, satuanv from '.$dbname.".bgt_budget where \r\n            tahunbudget='".$thnBudget."' and kodeorg='".$kodeOrg."' and tipebudget='".$tipeBudget."' and substring(kodebudget,1,1)='M' and kodevhc='".$kdVhc."'";
        $qLoad = mysql_query($sLoad);
        while ($res = mysql_fetch_assoc($qLoad)) {
            ++$no;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$no.'</td>';
            $tab .= "<td align='center'>".$res['kunci'].'</td>';
            $tab .= "<td align='center'>".$res['tahunbudget'].'</td>';
            $tab .= "<td align='center'>".$res['kodeorg'].'</td>';
            $tab .= "<td align='center'>".$res['tipebudget'].'</td>';
            $tab .= "<td align='center'>".$res['kodebudget'].'</td>';
            $tab .= "<td align='center'>".$res['kodevhc'].'</td>';
            $tab .= "<td align='right'>".$res['kodebarang'].'</td>';
            $tab .= "<td align='center'>".$optNmBrg[$res['kodebarang']].'</td>';
            $tab .= "<td  align='right'>".$res['jumlah'].'</td>';
            $tab .= "<td  align='center'>".$res['satuanj'].'</td>';
            $tab .= "<td align='right'>".number_format($res['rupiah'], 2).'</td>';
            $tab .= "<td align=center  style='cursor:pointer;'><img id='detail_add' title='delete data' class=zImgBtn onclick=\"deleteSdm(".$res['kunci'].",2)\" src='images/application/application_delete.png'/></td>";
            $tab .= '</tr>';
        }
        echo $tab;

        break;
    case 'loadDtLain':
        $sLoad = 'select kunci,tahunbudget, kodeorg, tipebudget, kodebudget,kodevhc,rupiah,noakun, satuanj from '.$dbname.".bgt_budget where \r\n            tahunbudget='".$thnBudget."' and kodeorg='".$kodeOrg."' and tipebudget='".$tipeBudget."' and kodebudget like '%TRANSIT%' and kodevhc='".$kdVhc."'";
        $qLoad = mysql_query($sLoad);
        while ($res = mysql_fetch_assoc($qLoad)) {
            ++$no;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$no.'</td>';
            $tab .= "<td align='center'>".$res['kunci'].'</td>';
            $tab .= "<td align='center'>".$res['tahunbudget'].'</td>';
            $tab .= "<td align='center'>".$res['kodeorg'].'</td>';
            $tab .= "<td align='center'>".$res['tipebudget'].'</td>';
            $tab .= "<td align='center'>".$res['kodebudget'].'</td>';
            $tab .= "<td align='center'>".$res['kodevhc'].'</td>';
            $tab .= "<td align='right'>".$res['noakun'].'</td>';
            $tab .= "<td align='left'>".$optNmAkun[$res['noakun']].'</td>';
            $tab .= "<td align='right'>".number_format($res['rupiah'], 2).'</td>';
            $tab .= "<td align=center  style='cursor:pointer;'><img id='detail_add' title='delete data' class=zImgBtn onclick=\"deleteSdm(".$res['kunci'].",4)\" src='images/application/application_delete.png'/></td>";
            $tab .= '</tr>';
        }
        echo $tab;

        break;
    case 'loadDtService':
        $sLoad = 'select kunci,tahunbudget, kodeorg, tipebudget, kodebudget,kodevhc,rupiah,jumlah, satuanj from '.$dbname.".bgt_budget where \r\n            tahunbudget='".$thnBudget."' and kodeorg='".$kodeOrg."' and tipebudget='".$tipeBudget."' and kodebudget like '%SERVICE%' and kodevhc='".$kdVhc."'";
        $qLoad = mysql_query($sLoad);
        while ($res = mysql_fetch_assoc($qLoad)) {
            ++$no;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$no.'</td>';
            $tab .= "<td align='center'>".$res['kunci'].'</td>';
            $tab .= "<td align='center'>".$res['tahunbudget'].'</td>';
            $tab .= "<td align='center'>".$res['kodeorg'].'</td>';
            $tab .= "<td align='center'>".$res['tipebudget'].'</td>';
            $tab .= "<td align='center'>".$res['kodebudget'].'</td>';
            $tab .= "<td align='center'>".$res['kodevhc'].'</td>';
            $tab .= "<td align='center'>".$res['jumlah'].'</td>';
            $tab .= "<td align='center'>".$res['satuanj'].'</td>';
            $tab .= "<td align='right'>".number_format($res['rupiah'], 2).'</td>';
            $tab .= "<td align=center  style='cursor:pointer;'><img id='detail_add' title='delete data' class=zImgBtn onclick=\"deleteSdm(".$res['kunci'].",3)\" src='images/application/application_delete.png'/></td>";
            $tab .= '</tr>';
        }
        echo $tab;

        break;
    case 'setKdBrg':
        echo substr($klmpkBrg, 2, 3);

        break;
    case 'DataHeader':
        $sJm = 'select * from '.$dbname.'.bgt_biaya_ken_per_jam order by tahunbudget desc';
        $qJm = mysql_query($sJm);
        while ($rJm = mysql_fetch_assoc($qJm)) {
            $rJmthn[$rJm['tahunbudget']][$rJm['kodetraksi']][$rJm['kodevhc']] = $rJm['rpsetahun'];
            $rJmhm[$rJm['tahunbudget']][$rJm['kodetraksi']][$rJm['kodevhc']] = $rJm['rpperjam'];
        }
        $tab = '<table cellspacing=1 cellpadding=1 class=sortable border=0><thead>';
        $tab .= '<tr class=rowheader>';
        $tab .= '<td>No.</td>';
        $tab .= '<td>'.$_SESSION['lang']['budgetyear'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['tipe'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['kodeorg'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['kodevhc'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['rpperthn'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['rpperjam'].'</td>';
        $tab .= '<td>Action</td>';
        $tab .= '</tr></thead>';
        $tab .= '<tbody>';
        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        if ('' !== $thnBudget) {
            $addKon .= " and tahunbudget='".$thnBudget."'";
        }

        if ('' !== $kdVhc) {
            $addKon .= " and kodevhc='".$kdVhc."'";
        }

        $sql2 = 'select * from '.$dbname.".bgt_budget where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' and tipebudget='TRK' ".$addKon.' group by tahunbudget,kodeorg,tipebudget, kodevhc order by tahunbudget desc  ';
        $query2 = mysql_query($sql2);
        $jlhbrs = mysql_num_rows($query2);
        $sData = 'select kunci,tahunbudget, kodeorg, tipebudget, kodebudget,kodevhc,tutup from '.$dbname.".bgt_budget where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' and tipebudget='TRK' ".$addKon.'  group by tahunbudget,kodeorg,tipebudget, kodevhc order by tahunbudget desc  limit '.$offset.','.$limit.'';
        $qData = mysql_query($sData);
        while ($rData = mysql_fetch_assoc($qData)) {
            ++$no;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$no.'</td>';
            $tab .= '<td>'.$rData['tahunbudget'].'</td>';
            $tab .= '<td>'.$rData['tipebudget'].'</td>';
            $tab .= '<td>'.$rData['kodeorg'].'</td>';
            $tab .= '<td>'.$rData['kodevhc'].'</td>';
            $tab .= '<td align=right>'.number_format($rJmthn[$rData['tahunbudget']][$rData['kodeorg']][$rData['kodevhc']], 2).'</td>';
            $tab .= '<td align=right>'.number_format($rJmhm[$rData['tahunbudget']][$rData['kodeorg']][$rData['kodevhc']], 2).'</td>';
            if (0 === $rData['tutup']) {
                $tab .= "<td  align=center style='cursor:pointer;'><img id='detail_edit' title='Simpan' class=zImgBtn onclick=\"filFieldHead('".$rData['tahunbudget']."','".$rData['kodeorg']."','".$rData['kodevhc']."')\" src='images/application/application_edit.png'/></td>";
            } else {
                $tab .= '<td>'.$_SESSION['lang']['tutup'].'</td>';
            }

            $tab .= '</tr>';
        }
        $tab .= "\r\n\t\t<tr class=rowheader><td colspan=10 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
        $tab .= '</tbody></table>';
        echo $tab;

        break;
    case 'closeBudget':
        if ('' === $thnBudget) {
            exit('Error: Budget year required');
        }

        $sQl = 'select distinct tutup from '.$dbname.'.bgt_budget where '.$where2.' and tutup=1';
        $qQl = mysql_query($sQl);
        $row = mysql_num_rows($qQl);
        if (1 !== $row) {
            $sUpdate = 'update '.$dbname.'.bgt_budget set tutup=1 where '.$where2.'';
            if (mysql_query($sUpdate)) {
                echo '';
            } else {
                echo ' Gagal,_'.$sUpdate.'__'.mysql_error($conn);
            }

            break;
        }

        exit('Error: Budget has been closed');
    case 'getThnBudget':
        $optThnTtp = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sThn = 'select distinct tahunbudget from '.$dbname.".bgt_budget where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' and tipebudget='TRK' and tutup=0 order by tahunbudget desc";
        $qThn = mysql_query($sThn);
        while ($rThn = mysql_fetch_assoc($qThn)) {
            $optThnTtp .= "<option value='".$rThn['tahunbudget']."'>".$rThn['tahunbudget'].'</option>';
        }
        echo $optThnTtp;

        break;
    default:
        break;
}

?>