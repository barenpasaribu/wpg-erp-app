<?php
require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
$proses = $_GET['proses'];
$param = $_POST;
switch ($proses) {
    case 'showDetail':
		$karyid= $param['karyawanid'];
		$tglkeluar= $param['tanggalkeluar'];
		
        $where = 'karyawanid='.$karyid;
        $cols = '*';

        $query = 'SELECT * FROM sdm_pesangonht WHERE karyawanid="'.$karyid.'"';
		//echo $query;
        $data = fetchData($query);
        $dataH = $data[0];
        $queryD = selectQuery($dbname, 'sdm_pesangondt', $cols, $where, 'no asc');
        $dataD = fetchData($queryD);
        $content = '<fieldset><legend><b>Data Detail</b></legend>';
        $content .= "<div id='detailCont'><ol>";
        $subTotal = 0;
        $content .= "<li><div style='font-weight:bold'>Uang Penggantian Hak</div>";
        $content .= "<table id='tableGanti'><tbody id=tBodyGanti>";
        foreach ($dataD as $row) {
            if ('uang ganti' == $row['tipe']) {
                $rp = explode('.', $row['rp']);
                $decLen = 0;
                if (2 < count($rp) && 0 < $rp[1]) {
                    if (2 < strlen($rp[1])) {
                        $decLen = 2;
                    } else {
                        $decLen = strlen($rp[1]);
                    }
                }

                $content .= "<tr id='ganti_".$row['no']."' no='".$row['no']."'>";
                $content .= '<td>'.makeElement('ganti_narasi_'.$row['no'], 'text', $row['narasi'], ['onkeyup' => "changeBg(getById('ganti_".$row['no']."'),'#F4FF74')", 'style' => 'width:276px']).'</td>';
                $content .= '<td>'.makeElement('ganti_pengali_'.$row['no'], 'textnum', $row['pengali'], ['style' => 'width:200px', 'placeholder' => 'Pengali', 'onkeyup' => "changeBg(getById('ganti_".$row['no']."'),'#F4FF74');calcGanti('".$row['no']."')"]).'</td><td>X</td>';
                $content .= '<td>'.makeElement('ganti_rp_'.$row['no'], 'textnum', $row['rp'], ['style' => 'width:200px', 'placeholder' => 'Rp', 'onkeyup' => "changeBg(getById('ganti_".$row['no']."'),'#F4FF74');calcGanti('".$row['no']."')"]).'</td><td>=</td>';
                $content .= '<td>'.makeElement('ganti_total_'.$row['no'], 'textnum', number_format($row['total']), ['dototal' => 'total-plus', 'style' => 'width:200px', 'disabled' => 'disabled']).'</td>';
                $content .= "<td style='text-align:center'><img src='images/".$_SESSION['theme']."/save.png' class=zImgBtn onclick='saveGanti(".$row['no'].")'></td>\n\t\t\t\t\t<td><img src='images/".$_SESSION['theme']."/delete.png' class=zImgBtn onclick='deleteGanti(".$row['no'].")'></td>";
                $content .= '</tr>';
                $subTotal += $row['total'];
            }
        }
		
		// FA20200829 - Untuk tambahan atau penggantian hak (cuti,dll)
		$txtcuti= '';
		$sisacuti= '0';
		$gjharian= '0';
		$uangcuti= '0';

		$sql = "select * from sdm_pesangondt where narasi like '%cuti%' and karyawanid=".$karyid; // jika belum ada penggantian hak cuti
		$qstr = mysql_query($sql);
		if (mysql_num_rows($qstr)==0){
			$sql = "select tanggalkeluar, lokasitugas from datakaryawan where karyawanid= ".$karyid;
			$qstr = mysql_query($sql);
			$rstr = mysql_fetch_assoc($qstr);
			$tglkeluar= $rstr['tanggalkeluar'];
				$lokasikerja = substr($rstr['lokasitugas'], 3,1);
			$sql = "select karyawanid, sum(sisa) as jumsisa from sdm_cutiht where karyawanid=".$karyid." and dari<='".$tglkeluar."'";
			$qstr = mysql_query($sql);
			$rstr = mysql_fetch_assoc($qstr);
			if ($rstr['jumsisa']>0){
				$sisacuti= strval($rstr['jumsisa']);				
				$txtcuti= 'Penggantian Hak Cuti';
				
				// Ambil rate harian gaji karyawan dibagi 21
				$sql1 = "select karyawanid, sum(jumlah) as gjharian from sdm_5gajipokok where tahun= year(curdate()) and karyawanid= ".$karyid ." AND idkomponen < 2";
				switch($lokasikerja){
					case 'H':
					 $harikerja = 21;
					 break;
					case 'E':
					 $harikerja= 30;
					break;
					case 'M':
					 $harikerja=25;
					break;
					default:
					 $harikerja=25;
					break;
				}
/* 				h =21
				e 30
				m 25 */
				$qstr1 = mysql_query($sql1);
				$rstr1 = mysql_fetch_assoc($qstr1);
				$gjharian= strval($rstr1['gjharian']);
				$gjharian = $rstr1['gjharian']/$harikerja;
				//$uangcuti= strval($rstr1['gjharian'] * $rstr['jumsisa']);
			}
		}
        
		$content .= "</tbody><tbody><tr id='ganti_add'>";
        $content .= '<td>'.makeElement('ganti_narasi_add', 'text', $txtcuti, ['style' => 'width:276px']).'</td>';
        $content .= '<td>'.makeElement('ganti_pengali_add', 'textnum', $sisacuti, ['style' => 'width:200px', 'placeholder' => 'Pengali', 'onkeyup' => "calcGanti('add')"]).'</td><td>X</td>';
        $content .= '<td>'.makeElement('ganti_rp_add', 'textnum', floor($gjharian), ['style' => 'width:200px', 'placeholder' => 'Rp', 'onkeyup' => "calcGanti('add')"]).'</td><td>=</td>';
        $content .= '<td>'.makeElement('ganti_total_add', 'textnum', floor($uangcuti), ['dototal' => 'total-plus', 'style' => 'width:200px', 'disabled' => 'disabled']).'</td>';
        $content .= '<td colspan=2>'.makeElement('ganti_addBtn', 'btn', $_SESSION['lang']['tambah'], ['onclick' => 'addGanti()']).'</td>';
        $content .= '</tr>';
        $content .= '</tbody></table>';
        $content .= '</li>';
        $qPes = 'SELECT pesangon,penghargaan,pengganti,perusahaan,kesalahanbiasa,kesalahanberat,uangpisah FROM sdm_5pesangon WHERE masakerja="'.$dataH['masakerja'].'"';
		//echo $qPes;
        $fPes = fetchData($qPes);
		$resPes = $fPes[0];
		if($dataH['pesangon']==0 || $dataH['pesangon']==''){
		$dataH['pesangon'] = $resPes['pesangon'];
		}
		if($dataH['penghargaan']==0){
		$dataH['penghargaan'] = $resPes['penghargaan'];
		}
		if($dataH['pengganti']==0){
		$dataH['pengganti'] = $resPes['pengganti'];
		}
		if($dataH['perusahaan']==0){
		$dataH['perusahaan'] = $resPes['perusahaan'];
		}
		if($dataH['kesalahanbiasa']==0){
		$dataH['kesalahanbiasa'] = $resPes['kesalahanbiasa'];
		}
		if($dataH['kesalahanberat']==0){
		$dataH['kesalahanberat'] = $resPes['kesalahanberat'];
		}
		if($dataH['uangpisah']==0){
		$dataH['uangpisah'] = $resPes['uangpisah'];
		}
        $content .= "<li><div><div style='font-weight:bold;width:621px;float:left'>Uang Pesangon</div>";
        $content .= '<div>'.makeElement('detailPesangon', 'textnum', number_format($dataH['pesangon']), ['dototal' => 'total-plus', 'style' => 'width:200px', 'onkeyup' => "calcTotal()"]).'</div></div>';
        $content .= "<li><div><div style='font-weight:bold;width:621px;float:left'>Uang Penghargaan</div>";
        $content .= '<div>'.makeElement('penghargaan', 'textnum', number_format($dataH['penghargaan']), ['dototal' => 'total-plus', 'style' => 'width:200px', 'onkeyup' => "calcTotal()"]).'</div></div>';
        $content .= '</li>';
        $content .= "<li><div><div style='font-weight:bold;width:621px;float:left'>Uang Mengundurkan diri</div>";
        $content .= '<div>'.makeElement('pengganti', 'textnum', number_format($dataH['pengganti']), ['dototal' => 'total-plus', 'style' => 'width:200px', 'onkeyup' => "calcTotal()"]).'</div></div>';
        $content .= '</li>';
        $content .= "<li><div><div style='font-weight:bold;width:621px;float:left'>Uang Diberhentikan Perusahaan</div>";
        $content .= '<div>'.makeElement('perusahaan', 'textnum', number_format($dataH['perusahaan']), ['dototal' => 'total-plus', 'style' => 'width:200px', 'onkeyup' => "calcTotal()"]).'</div></div>';
        $content .= '</li>';
        $content .= "<li><div><div style='font-weight:bold;width:621px;float:left'>Uang Kesalahan Biasa</div>";
        $content .= '<div>'.makeElement('kesalahanbiasa', 'textnum', number_format($dataH['kesalahanbiasa']), ['dototal' => 'total-plus', 'style' => 'width:200px', 'onkeyup' => "calcTotal()"]).'</div></div>';
        $content .= '</li>';
        $content .= "<li><div><div style='font-weight:bold;width:621px;float:left'>Uang Kesalahan Berat	</div>";
        $content .= '<div>'.makeElement('kesalahanberat', 'textnum', number_format($dataH['kesalahanbesar']), ['dototal' => 'total-plus', 'style' => 'width:200px', 'onkeyup' => "calcTotal()"]).'</div></div>';
        $content .= '</li>';
        $content .= "<li><div><div style='font-weight:bold;width:621px;float:left'>Uang Pisah</div>";
        $content .= '<div>'.makeElement('uangpisah', 'textnum', number_format($dataH['uangpisah']), ['dototal' => 'total-plus', 'style' => 'width:200px', 'onkeyup' => "calcTotal()"]).'</div></div>';
        $content .= '</li>';

        $subTotal += $dataH['pesangon'] + $dataH['penghargaan'] + $dataH['pengganti'] + $dataH['perusahaan'] + $dataH['kesalahanbiasa'] + $dataH['kesalahanbesar'] + $dataH['uangpisah'];
        $pph = $dataH['pph'];

		
		/*
        if (50000000 < $subTotal) {
            $sisa = $subTotal - 50000000;
            if (50000000 < $sisa) {
                $pph += (50000000 * 5) / 100;
                $sisa -= 50000000;
                if (400000000 < $sisa) {
                    $pph += (400000000 * 15) / 100;
                    $sisa -= 400000000;
                    $pph += ($sisa * 25) / 100;
                } else {
                    $pph += ($sisa * 15) / 100;
                }
            } else {
                $pph += ($sisa * 5) / 100;
            }
        }
        */
		
		/*
		if ($subTotal <= 50000000) {
            $pph = 0;
        } elseif ($subTotal > 50000000 && $subTotal <= 100000000){
            $pph = $subTotal * 5 / 100;
        } elseif ($subTotal > 100000000 && $subTotal <= 500000000){
            $pph = $subTotal * 15 / 100;
        } elseif ($subTotal > 500000000){
            $pph = $subTotal * 25 / 100;
        }
		*/
		
		/*
		// FA 20200727 - ambil dari tabel master rate pesangon
		$pph= 0;
		$level= 0;
        $qPes = 'select * from sdm_5pesangon_tarif where batas_bawah<='.$subTotal.' and batas_atas>'.$subTotal;
        $resPes = fetchData($qPes);
        if (!empty($resPes)) {
            $pes = $resPes[0];
			$level = $pes['level'];
        }
		for ($a=1;$a<=$level;$a++){
			$qPes = 'select * from sdm_5pesangon_tarif where level='.$a;
			$resPes = fetchData($qPes);
			if (!empty($resPes)) {
				$pes = $resPes[0];
				if ($a == $level){
					$pph= $resPes['batas_atas'] * $resPes['tarif_persen']; // jika blm level maksimal, kalikan batas atas level dgn tarif
				} else {
					$pph= ($subTotal - ($resPes['batas_bawah']-1)) * $resPes['tarif_persen']; // jika sdh level maksimal, kalikan sisa sub total dgn tarif
				}
			}
		}
		*/

		$content .= "<div><div style='font-weight:bold;width:621px;float:left;text-align:right'>Total&nbsp;&nbsp;</div>";
        $content .= '<div>'.makeElement('subTotal', 'textnum', number_format($subTotal), ['style' => 'width:200px', 'disabled' => true]).'</div></div>';
        $content .= "<div><div style='font-weight:bold;width:621px;float:left;text-align:right'>Pph&nbsp;&nbsp;</div>";
        $content .= '<div>'.makeElement('pph', 'textnum', number_format($dataH['pph']), ['style' => 'width:200px', 'disabled' => true]).'</div></div>';
        $content .= '</li>';
        $totalPot = 0;
        $content .= "<li><div style='font-weight:bold'>Potongan</div>";
        $content .= "<table id='tablePotongan'><tbody id=tBodyPotongan>";
        foreach ($dataD as $row) {
            if ('potongan' == $row['tipe']) {
                $content .= "<tr id='potongan_".$row['no']."'>";
                $content .= '<td>'.makeElement('potongan_narasi_'.$row['no'], 'text', $row['narasi'], ['onkeyup' => "changeBg(getById('potongan_".$row['no']."'),'#F4FF74')", 'style' => 'width:612px']).'</td>';
                $content .= '<td>'.makeElement('potongan_total_'.$row['no'], 'textnum', number_format($row['total']), ['dototal' => 'total-min', 'style' => 'width:200px', 'onkeyup' => "changeBg(getById('potongan_".$row['no']."'),'#F4FF74');calcPotongan('".$row['no']."')"]).'</td>';
                $content .= "<td style='text-align:center'><img src='images/".$_SESSION['theme']."/save.png' class=zImgBtn onclick='savePotongan(".$row['no'].")'></td>\n\t\t\t\t\t<td><img src='images/".$_SESSION['theme']."/delete.png' class=zImgBtn onclick='deletePotongan(".$row['no'].")'></td>";
                $content .= '</tr>';
                $totalPot += $row['total'];
            }
        }
        $content .= "</tbody><tbody><tr id='potongan_add'>";
        $content .= '<td>'.makeElement('potongan_narasi_add', 'text', '', ['style' => 'width:612px']).'</td>';
        $content .= '<td>'.makeElement('potongan_total_add', 'textnum', '0', ['dototal' => 'total-min', 'style' => 'width:200px', 'onkeyup' => "calcPotongan('add')"]).'</td>';
        $content .= '<td colspan=2>'.makeElement('potongan_addBtn', 'btn', $_SESSION['lang']['tambah'], ['onclick' => 'addPotongan()']).'</td>';
        $content .= '</tr>';
        $content .= '</tbody></table>';
        $total = $subTotal - $pph - $totalPot;
        $content .= "<div><div style='font-weight:bold;width:621px;float:left;text-align:right'>Diterima&nbsp;&nbsp;</div>";
        $content .= '<div>'.makeElement('detailDiterima', 'textnum', number_format($total), ['style' => 'width:200px', 'disabled' => true]).'</div></div>';
        $content .= '</li>';
        $content .= '';
        $content .= '</ol></div></fieldset>';
        echo $content;

        break;
    default:
        break;
}

?>