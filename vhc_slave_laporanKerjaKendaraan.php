<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/fpdf.php');

$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['comId']==''?$comId=$_GET['comId']:$comId=$_POST['comId'];
$_POST['kdVhc']==''?$kdVhc=$_GET['kdVhc']:$kdVhc=$_POST['kdVhc'];
$_POST['jnsVhc']==''?$jnsVhc=$_GET['jnsVhc']:$jnsVhc=$_POST['jnsVhc'];
$_POST['alokasi']==''?$alokasi=$_GET['alokasi']:$alokasi=$_POST['alokasi'];
$_POST['tglAwal']==''?$tglAwal=tanggalsystem($_GET['tglAwal']):$tglAwal=tanggalsystem($_POST['tglAwal']);
$_POST['tglAkhir']==''?$tglAkhir=tanggalsystem($_GET['tglAkhir']):$tglAkhir=tanggalsystem($_POST['tglAkhir']);
$where2=' kelompokbarang=351';
$optBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang',$where2);	
$optOperator=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan','');
	switch($proses)
	{
		case'getJnsVhc':
                $optOrg=makeOption($dbname, 'vhc_5jenisvhc', 'jenisvhc,namajenisvhc');
		$optJnsvhc="<option value=''>".$_SESSION['lang']['all']."</option>";
		$sjnsVhc="select distinct jenisvhc from ".$dbname.".vhc_runht where kodeorg='".substr($comId,0,4)."' group by jenisvhc"; //echo "warning:".$sjnsVhc;
		$qjnsVhc=mysql_query($sjnsVhc) or die(mysql_error());
		while($rjnsVhc=mysql_fetch_assoc($qjnsVhc))
		{
//			$sJhvc="select from ".$dbname.".vhc_5jenisvhc where jenisvhc='".$rjnsVhc['jenisvhc']."'";
//			$qjhvc=mysql_query($sJhvc) or die(mysql_error());
//			$rjhvc=mysql_fetch_assoc($qjhvc);
		$optJnsvhc.="<option value='".$rjnsVhc['jenisvhc']."'>".$optOrg[$rjnsVhc['jenisvhc']]."</option>";
		}
		echo $optJnsvhc;
		break;
		
		case'getKdvhc':
		$optKvhc="<option value=''>".$_SESSION['lang']['all']."</option>";
		$skdVhc="select kodevhc from ".$dbname.".vhc_runht where jenisvhc='".$jnsVhc."' group by kodevhc"; //echo "warning:".$skdVhc;
		$qkdVhc=mysql_query($skdVhc) or die(mysql_error());
		while($rkdVhc=mysql_fetch_assoc($qkdVhc))
		{
		$optKvhc.="<option value='".$rkdVhc['kodevhc']."'>".$rkdVhc['kodevhc']."</option>";
		}
		echo $optKvhc;
		break;
		
		case'get_result':
            if($comId=='')
            {
                echo"warning:Unit Tidak Boleh Kosong";
                exit();
            }
            if($tglAkhir==''||$tglAwal='')
            {
                echo"warning:Tanggal Tidak Boleh Kosong";
                exit();
            }
               if($jnsVhc!='')
                {
                 $where.=" and jenisvhc='".$jnsVhc."'";   
                }
                if($kdVhc!='')
                {
                 $where.=" and kodevhc='".$kdVhc."'";
                }
                if($alokasi!='')
                {
                 $where.=" and alokasibiaya like'".$alokasi."%'";
                }
              /*  $sql="select distinct a.*,b.*,c.upah,c.premi from ".$dbname.".vhc_rundt b 
                    left join ".$dbname.".vhc_runhk c on a.notransaksi=b.notransaksi 
                    left join ".$dbname.".vhc_runht a on b.notransaksi=c.notransaksi where
                    a.kodeorg='".substr($comId, 0,4)."' and a.tanggal between  '".tanggalsystem($_POST['tglAwal'])."' and '".$tglAkhir."' ".$where." group by a.notransaksi order by a. tanggal asc";

               *                 //exit("Error".$sql);
               */
                $sql="select distinct a.notransaksi,a.jenispekerjaan,a.alokasibiaya,a.jumlah,c.upah,c.premi,b.kodevhc,b.jenisvhc,
                      b.tanggal,a.jumlahrit,a.beratmuatan,a.biaya,a.keterangan,a.satuan,b.jenisbbm,b.jlhbbm, idkaryawan
                      from ".$dbname.".vhc_rundt a left join ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi
                      left join ".$dbname.".vhc_runhk c on a.notransaksi=c.notransaksi where kodeorg='".substr($comId, 0,4)."' and 
                      b.tanggal between  '".tanggalsystem($_POST['tglAwal'])."' and '".$tglAkhir."' ".$where." group by a.notransaksi,a.alokasibiaya,a.jenispekerjaan
                      order by b.kodevhc,b.tanggal asc";
                if($alokasi!='')
                {
                $sql="select distinct a.notransaksi,a.jenispekerjaan,a.alokasibiaya,a.jumlah,c.upah,c.premi,b.kodevhc,b.jenisvhc,
                      b.tanggal,a.jumlahrit,a.beratmuatan,a.biaya,a.keterangan,a.satuan,b.jenisbbm,b.jlhbbm, idkaryawan
                      from ".$dbname.".vhc_rundt a left join ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi
                      left join ".$dbname.".vhc_runhk c on a.notransaksi=c.notransaksi where
                      b.tanggal between  '".tanggalsystem($_POST['tglAwal'])."' and '".$tglAkhir."' ".$where." group by a.notransaksi,a.alokasibiaya,a.jenispekerjaan
                      order by b.kodevhc,b.tanggal asc";
                }
//echo $sql;
		echo"
			<table cellspacing=1 border=0 class=sortable>
		<thead>
        	<tr class=rowheader>
            <td>No.</td>
			<td align=center>".$_SESSION['lang']['notransaksi']."</td>
			<td align=center>".$_SESSION['lang']['tanggal']." </td>
			<td align=center>".$_SESSION['lang']['jenisvch']."</td>
			<td align=center>".$_SESSION['lang']['kodevhc']."</td>
      <td align=center>Operator</td>
			<td align=center>HM/KM/KWH</td>
			<td align=center>".$_SESSION['lang']['vhc_jenis_bbm']."</td>
			<td align=center>".$_SESSION['lang']['vhc_jumlah_bbm']."</td>
      <td align=center>".$_SESSION['lang']['kodekegiatan']."</td>
			<td align=center>".$_SESSION['lang']['noakun']."</td>
			<td align=center>".$_SESSION['lang']['vhc_jenis_pekerjaan']."</td>
			<td align=center>".$_SESSION['lang']['alokasibiaya']."</td>
			<td align=center>".$_SESSION['lang']['vhc_berat_muatan']."</td>
			<td align=center>".$_SESSION['lang']['jumlahrit']."</td>
			<td align=center>".$_SESSION['lang']['biaya']."</td>			
			<td align=center>".$_SESSION['lang']['upahpremi']."</td>
			<td align=center>".$_SESSION['lang']['upahkerja']."</td>
            <td align=center>".$_SESSION['lang']['keterangan']."</td>    
            </tr>
        </thead>
        <tbody>";
		//echo "warning".$sql;exit();
                
		$arrPos=array("Sopir","Kondektur");
			$qRvhc=mysql_query($sql) or die(mysql_error());
                        $old='';
			while($res=mysql_fetch_assoc($qRvhc))
			{
							
				$sJns="select *  from ".$dbname.".vhc_kegiatan where kodekegiatan='".$res['jenispekerjaan']."'";
				$qJns=mysql_query($sJns) or die(mysql_error());
				$rJns=mysql_fetch_assoc($qJns);
			
			$no+=1;
                        if($res['notransaksi']==$old)
                        {
                            $res['biaya']=0;
                            $res['premi']=0;
                            $res['upah']=0;
                        }
                        $sBn="select sum(jumlah) as totalhm from ".$dbname.".vhc_rundt where notransaksi='".$res['notransaksi']."'";
                        $qBn=mysql_query($sBn) or die(mysql_error($conn));
                        $rBn=mysql_fetch_assoc($qBn);
                        @$jmlhBbm=$res['jlhbbm']*($res['jumlah']/$rBn['totalhm']);
			echo"
			<tr class=rowcontent>
			<td align=center>".$no."</td>
			
			<td>".$res['notransaksi']."</td>
			<td>".tanggalnormal($res['tanggal'])."</td>
			<td>".$res['jenisvhc']."</td>
			<td>".$res['kodevhc']."</td>
      <td>".$optOperator[$res['idkaryawan']]."</td>
			<td align=right>".number_format($res['jumlah'],2)."</td>
			<td>".$optBrg[$res['jenisbbm']]."</td>
			<td align=right>".number_format($jmlhBbm,2)."</td>
                        <td>".$rJns['kodekegiatan']."</td>
                        <td>".$rJns['noakun']."</td>
			<td>".$rJns['namakegiatan']."</td>
			<td>".$res['alokasibiaya']."</td>
			<td align=right>".number_format($res['beratmuatan'],2)."</td>
			<td align=right>".number_format($res['jumlahrit'],2)."</td>
			<td align=right>".number_format($res['biaya'],2)."</td>
			<td align=right>".number_format($res['premi'],2)."</td>
			<td align=right>".number_format($res['upah'],2)."</td>
                        <td>".$res['keterangan']."</td>    
			</tr>";
                        $old=$res['notransaksi'];
			}			
			echo"</tbody></table>";
		
		break;
		case'getResultKry':
		$sRvhc="select a.*,b.jenispekerjaan,b.jumlahrit,b.keterangan from ".$dbname.".vhc_runht 
		a inner join ".$dbname.".vhc_rundt b on a.notransaksi=b.notransaksi 
		inner join ".$dbname.".vhc_runhk c on b.notransaksi=c.notransaksi 
		where c.idkaryawan='".$kryId."' order by a.tanggal asc"; 
		//echo "warning:".$sRvhc;
		$qRvhc=mysql_query($sRvhc) or die(mysql_error());
		while($rRvhc=mysql_fetch_assoc($qRvhc))
		{
		$no+=1;
		echo"
		<tr class=rowcontent>
		<td>".$no."</td>
		<td align=center>".$rRvhc['notransaksi']."</td>
		<td align=center>".tanggalnormal($rRvhc['tanggal'])."</td>
		<td align=center>".$rRvhc['kmhmawal']."</td>
		<td align=center>".$rRvhc['kmhmakhir']."</td>
		<td align=center>".$rRvhc['jumlah']."</td>
		<td align=center>".$rRvhc['jenispekerjaan']."</td>
		<td align=center>".$rRvhc['keterangan']."</td>
		<td align=center>".$rRvhc['jumlahrit']."</td>
		<td align=center>".$rRvhc['jlhbbm']."</td>
		</tr>
		";
		}
		break;
		case'excel':
            if($comId=='')
            {
                echo"warning:Unit Tidak Boleh Kosong";
                exit();
            }
            if($tglAkhir==''||$tglAwal='')
            {
                echo"warning:Tanggal Tidak Boleh Kosong";
                exit();
            }
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".substr($comId,0,4)."'";
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}

			$stream.="
			<table>
			<tr><td colspan=15 align=center>".$_SESSION['lang']['laporanPekerjaan']."</td></tr>";
			if($comId!='')
			{
				$stream.="
			<tr><td colspan=6>".$_SESSION['lang']['unit'].":".$namapt."</td></tr>";
			}
			
			$stream.="
			<tr><td colspan=6>".$_SESSION['lang']['periode'].":".$_GET['tglAwal']."-".$_GET['tglAkhir']."</td></tr>";
			
			$stream.="
			<tr><td colspan=6>&nbsp;</td></tr>
			</table>
			<table border=1 bgcolor=#DEDEDE >
                            <tr>
                            <td align=center valign=middle>No.</td>
                            <td align=center>".$_SESSION['lang']['notransaksi']."</td>
                            <td align=center>".$_SESSION['lang']['tanggal']." </td>
                            <td align=center>".$_SESSION['lang']['jenisvch']."</td>
                            <td align=center>".$_SESSION['lang']['kodevhc']."</td>
                            <td align=center>HM/KM</td>
                            <td align=center>".$_SESSION['lang']['vhc_jenis_bbm']."</td>
                            <td align=center>".$_SESSION['lang']['vhc_jumlah_bbm']."</td>
                            <td align=center>".$_SESSION['lang']['kodekegiatan']."</td>
                            <td align=center>".$_SESSION['lang']['noakun']."</td>
                            <td align=center>".$_SESSION['lang']['vhc_jenis_pekerjaan']."</td>
                            <td align=center>".$_SESSION['lang']['alokasibiaya']."</td>
                            <td align=center>".$_SESSION['lang']['vhc_berat_muatan']."</td>
                            <td align=center>".$_SESSION['lang']['jumlahrit']."</td>
                            <td align=center>".$_SESSION['lang']['biaya']."</td>			
                            <td align=center>".$_SESSION['lang']['upahpremi']."</td>
                            <td align=center>".$_SESSION['lang']['upahkerja']."</td>
                            <td align=center>".$_SESSION['lang']['keterangan']."</td>   
                            </tr>
                            </table>						
                            ";

                            $stream.="<table border='1'>";
		if($jnsVhc!='')
                {
                 $where.=" and jenisvhc='".$jnsVhc."'";   
                }
                if($kdVhc!='')
                {
                 $where.=" and kodevhc='".$kdVhc."'";
                }
                if($alokasi!='')
                {
                 $where.=" and alokasibiaya like'".$alokasi."%'";
                }        
                        $sql="select distinct a.notransaksi,a.jenispekerjaan,a.alokasibiaya,a.jumlah,c.upah,c.premi,b.kodevhc,b.jenisvhc,
                      b.tanggal,a.jumlahrit,a.beratmuatan,a.biaya,a.keterangan,a.satuan,b.jenisbbm,b.jlhbbm
                      from ".$dbname.".vhc_rundt a left join ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi
                      left join ".$dbname.".vhc_runhk c on a.notransaksi=c.notransaksi where kodeorg='".substr($comId, 0,4)."' and 
                      b.tanggal between  '".tanggalsystem($_GET['tglAwal'])."' and '".$tglAkhir."' ".$where." group by a.notransaksi,a.alokasibiaya,a.jenispekerjaan
                      order by b.kodevhc,b.tanggal asc";
                        if($alokasi!='')
                {
                        $sql="select distinct a.notransaksi,a.jenispekerjaan,a.alokasibiaya,a.jumlah,c.upah,c.premi,b.kodevhc,b.jenisvhc,
                      b.tanggal,a.jumlahrit,a.beratmuatan,a.biaya,a.keterangan,a.satuan,b.jenisbbm,b.jlhbbm
                      from ".$dbname.".vhc_rundt a left join ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi
                      left join ".$dbname.".vhc_runhk c on a.notransaksi=c.notransaksi where 
                      b.tanggal between  '".tanggalsystem($_GET['tglAwal'])."' and '".$tglAkhir."' ".$where." group by a.notransaksi,a.alokasibiaya,a.jenispekerjaan
                      order by b.kodevhc,b.tanggal asc";
                        }
//                        $stream.=$sql;
                $resx=mysql_query($sql);
   
		$no=0;
		$arrPos=array("Sopir","Kondektur");
		while($res=mysql_fetch_assoc($resx))
		{
                    $sbrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$res['jenisbbm']."'";
                    $qbrg=mysql_query($sbrg) or die(mysql_error());
                    $rbrg=mysql_fetch_assoc($qbrg);


                    $sJns="select *  from ".$dbname.".vhc_kegiatan where kodekegiatan='".$res['jenispekerjaan']."'";
                    $qJns=mysql_query($sJns) or die(mysql_error());
                    $rJns=mysql_fetch_assoc($qJns);	

                    $no+=1;	
                    if($res['notransaksi']==$old)
                        {
                            $res['biaya']=0;
                            $res['premi']=0;
                            $res['upah']=0;
                        }
                         $sBn="select sum(jumlah) as totalhm from ".$dbname.".vhc_rundt where notransaksi='".$res['notransaksi']."'";
                        $qBn=mysql_query($sBn) or die(mysql_error($conn));
                        $rBn=mysql_fetch_assoc($qBn);
                        @$jmlhBbm=$res['jlhbbm']*($res['jumlah']/$rBn['totalhm']);
                    $stream.="
                    <tr class=rowcontent>
                    <td>".$no."</td>
			<td>".$res['notransaksi']."</td>
			<td>".$res['tanggal']."</td>
			<td>".$res['jenisvhc']."</td>
			<td>".$res['kodevhc']."</td>
			<td align=right>".number_format($res['jumlah'],2)."</td>
			<td>".$optBrg[$res['jenisbbm']]."</td>
			<td align=right>".number_format($jmlhBbm,2)."</td>
                        <td>".$rJns['kodekegiatan']."</td>
                        <td>".$rJns['noakun']."</td>
			<td>".$rJns['namakegiatan']."</td>
			<td>".$res['alokasibiaya']."</td>
			<td align=right>".number_format($res['beratmuatan'],2)."</td>
			<td align=right>".number_format($res['jumlahrit'],2)."</td>
			<td align=right>".number_format($res['biaya'],2)."</td>
			<td align=right>".number_format($res['premi'],2)."</td>
			<td align=right>".number_format($res['upah'],2)."</td>
                        <td>".$res['keterangan']."</td>    
                    </tr>";
                    $old=$res['notransaksi'];
		}

	//echo "warning:".$strx;
//=================================================
		
	$stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
$dte=date("Hms");
$nop_="ReportVehicleUsage__".$dte;
 $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
 gzwrite($gztralala, $stream);
 gzclose($gztralala);
 echo "<script language=javascript1.2>
	window.location='tempExcel/".$nop_.".xls.gz';
	</script>";
/*if(strlen($stream)>0)
{
if ($handle = opendir('tempExcel')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            @unlink('tempExcel/'.$file);
        }
    }	
   closedir($handle);
}
 $handle=fopen("tempExcel/".$nop_.".xls",'w');
 if(!fwrite($handle,$stream))
 {
  echo "<script language=javascript1.2>
        parent.window.alert('Can't convert to excel format');
        </script>";
   exit;
 }
 else
 {
  echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls';
        </script>";
 }
closedir($handle);
}*/
 
        break;
		default:
		break;
	}

?>