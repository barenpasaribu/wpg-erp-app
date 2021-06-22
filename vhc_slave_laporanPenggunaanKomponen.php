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
$_POST['period']==''?$period=$_GET['period']:$period=$_POST['period'];
$_POST['jenisVhc']==''?$jenisVhc=$_GET['jenisVhc']:$jenisVhc=$_POST['jenisVhc'];
$_POST['tglAwal']==''?$tglAwal=tanggalsystem($_GET['tglAwal']):$tglAwal=tanggalsystem($_POST['tglAwal']);
$_POST['tglAkhir']==''?$tglAkhir=tanggalsystem($_GET['tglAkhir']):$tglAkhir=tanggalsystem($_POST['tglAkhir']);



switch($proses)
{
        case'getKdvhc':
        $optOrg=makeOption($dbname, 'vhc_5jenisvhc', 'jenisvhc,namajenisvhc');
        $optKvhc="<option value=''>".$_SESSION['lang']['all']."</option>";
        if(kodetraksi!='')
        {
            $where=" kodetraksi='".$comId."'";
        }
        if($jenisVhc!='')
        {
            $where.=" and jenisvhc='".$jenisVhc."'";
        }
        $skdVhc="select distinct kodevhc,jenisvhc from ".$dbname.".vhc_5master where ".$where." group by kodevhc,jenisvhc order by jenisvhc";// echo "warning:".$skdVhc;
        $qkdVhc=mysql_query($skdVhc) or die(mysql_error());
        while($rkdVhc=mysql_fetch_assoc($qkdVhc))
        {
                $optKvhc.="<option value='".$rkdVhc['kodevhc']."'>".$rkdVhc['kodevhc']." [".$optOrg[$rkdVhc['jenisvhc']]."]</option>";
        }
        echo $optKvhc;
        break;
        case'get_result':
            if($comId=='')
            {
                echo"warning:Working unit required";
                exit();
            }
            if($tglAkhir==''||$tglAwal='')
            {
                echo"warning: Date required";
                exit();
            }
        echo"<div style=\"overflow:width:550px;\">
                        <table cellspacing=1 border=0>
                <thead>
                <tr class=rowheader>
            <td>No.</td>
                        <td align=center>".$_SESSION['lang']['notransaksi']."</td>
                        <td align=center>".$_SESSION['lang']['tanggal']."</td>
                        <td align=center>".$_SESSION['lang']['kodevhc']."</td>
                        <td align=center>".$_SESSION['lang']['downtime']."</td>
                        <td align=center>".$_SESSION['lang']['kodebarang']."</td>
                        <td align=center>".$_SESSION['lang']['namabarang']."</td>
                        <td align=center>".$_SESSION['lang']['satuan']."</td>
                        <td align=center>".$_SESSION['lang']['jumlah']."</td>
                        <td align=center>".$_SESSION['lang']['keterangan']."</td>
                        <td align=center>".$_SESSION['lang']['status']."</td>
            </tr>
        </thead>
        <tbody>";

        if($jenisVhc!='')
        {
         $where=" and a.kodevhc in (select distinct kodevhc from ".$dbname.".vhc_5master where jenisvhc='".$jenisVhc."' and kodetraksi='".$comId."')";   
         if($kdVhc!='')
        {
            $where=" and a.kodevhc='".$kdVhc."'";
        }
        }else{
         $where=" and a.kodevhc in (select distinct kodevhc from ".$dbname.".vhc_5master where kodetraksi='".$comId."')";   
        }
        $sql="select a.tanggal,a.kodevhc,a.downtime,a.posting,a.notransaksi,b.kodebarang,b.jumlah,b.satuan,b.keterangan, c.jenisvhc, c.detailvhc from ".$dbname.".vhc_penggantianht a left join ".$dbname.".vhc_penggantiandt b on a.notransaksi=b.notransaksi left join ".$dbname.".vhc_5master c on a.kodevhc=c.kodevhc
            where a.kodeorg='".substr($comId, 0,4)."' and a.tanggal between  '".tanggalsystem($_POST['tglAwal'])."' and '".$tglAkhir."' ".$where."";
//echo $sql;
        //exit("Error".$sql."__".$tglAwal);posting
        $qRvhc=mysql_query($sql) or die(mysql_error());
        $row=mysql_num_rows($qRvhc);
        if($row>1)
        {
                while($rRvhc=mysql_fetch_assoc($qRvhc))
                {
                        $no+=1;
                        echo"
                                <tr class=rowcontent>
                                        <td>".$no."</td>
                                        <td>".$rRvhc['notransaksi']."</td>
                                        <td>".tanggalnormal($rRvhc['tanggal'])."</td>
                                        <td>".$rRvhc['kodevhc']." | ".$rRvhc['jenisvhc']." | ".$rRvhc['detailvhc']."</td>
                                        <td align=right>".$rRvhc['downtime']."</td>
                                        <td>";
                        
                        $sbrg="select namabarang from ".$dbname.".log_transaksi_vw a inner join  ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang where kodeblok='".$rRvhc['kodevhc']."' and date(a.waktutransaksi)='".$rRvhc['tanggal']."'";
                        
                        $qbrg=mysql_query($sbrg) or die(mysql_error());
                        while($rbrg=mysql_fetch_assoc($qbrg))
                        {
                                        
                         echo      "<table><tr>".$rbrg['namabarang']."</td>
                                    </tr></table>";
                        }
                        echo"<td align=center>";
                        $rRvhc['posting']=='1'?$imgt="<img src='images/buttongreen.png'  title='Sudah Posting' />":$imgt="<img src='images/hot.png' title='Belum Posting' />";
                        echo $imgt;
                        echo"</td>";
                        echo"
                                </tr>
                        ";
                }
        }
        else
        {
                echo"<tr class=rowcontent align=center><td colspan=8>Not Found</td></tr>";
        }
        echo"</tbody></table></div>";
        break;
        case'get_result_cari':
        $sBrg="select namabarang,kodebarang from ".$dbname.".log_5masterbarang where kodebarang=".$kdBrg."";
        $qBrg=mysql_query($sBrg) or die(mysql_error());
        $rBrg=mysql_fetch_assoc($qBrg);

        $sRvhc="select a.tanggal,a.kodevhc,b.* from ".$dbname.".vhc_penggantianht a inner join ".$dbname.".vhc_penggantiandt b on a.notransaksi=b.notransaksi where 
        b.kodebarang='".$kdBrg."' order by a.tanggal asc "; //echo "warning:".$sRvhc;
        //echo $sRvhc;
        $qRvhc=mysql_query($sRvhc) or die(mysql_error());
        while($rRvhc=mysql_fetch_assoc($qRvhc))
        {
                $no+=1;
                echo"
                        <tr class=rowcontent>
                                <td>".$no."</td>
                                <td align=center>".$rRvhc['notransaksi']."</td>
                                <td align=center>".$rRvhc['kodevhc']."</td>
                                <td align=center>".tanggalnormal($rRvhc['tanggal'])."</td>
                                <td align=center >".$rRvhc['kodebarang']."</td>
                                <td align=center>".$rRvhc['satuan']."</td>
                                <td align=center>".$rRvhc['jumlah']."</td>
                                <td align=center>".$rRvhc['keterangan']."</td>
                        </tr><input type=hidden id=kd_br name=kd_brg value=".$rRvhc['kodebarang']." />
                        ";
        }
        break;
        case'getExcel':
            if($comId=='')
            {
                echo"warning: Working unit required";
                exit();
            }
            if($tglAkhir==''||$tglAwal='')
            {
                echo"warning: Date required";
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
            <tr><td colspan=8 align=center>".$_SESSION['lang']['laporanPenggunaanKomponen']."</td></tr>";
            if($comId!='')
            {
                $stream.="<tr><td colspan=3>".$_SESSION['lang']['unit'].":".$namapt."</td></tr>";
            }
            if($kdVhc!='')
            {
                $stream.="<tr><td colspan=3>".$_SESSION['lang']['kodevhc'].":".$kdVhc."</td></tr>";
            }

                $stream.="<tr><td colspan=3>".$_SESSION['lang']['periode'].":".$_GET['tglAwal']."-".$_GET['tglAkhir']."</td></tr>";

            $stream.="<tr><td colspan=3>&nbsp;</td></tr>
            </table>
            <table border=1>
            <tr>
            <td bgcolor=#DEDEDE align=center>No.</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['notransaksi']."</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodevhc']."</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['downtime']."</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodebarang']."</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namabarang']."</td>
            ";
            $stream.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['satuan']."</td>	
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlah']."</td>	
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['keterangan']."</td>	
            </tr>";

        if($jenisVhc!='')
        {
         $where=" and kodevhc in (select distinct kodevhc from ".$dbname.".vhc_5master where jenisvhc='".$jenisVhc."' and kodetraksi='".$comId."')";   
         if($kdVhc!='')
        {
            $where=" and kodevhc='".$kdVhc."'";
        }
        }else{
         $where=" and kodevhc in (select distinct kodevhc from ".$dbname.".vhc_5master where kodetraksi='".$comId."')";   
        }
        $sql="select a.tanggal,a.kodevhc,a.downtime,a.posting,a.notransaksi,b.kodebarang,b.jumlah,b.satuan,b.keterangan from ".$dbname.".vhc_penggantianht a left join ".$dbname.".vhc_penggantiandt b on a.notransaksi=b.notransaksi 
            where a.kodeorg='".substr($comId, 0,4)."' and a.tanggal between  '".tanggalsystem($_POST['tglAwal'])."' and '".$tglAkhir."' ".$where."";
        $sql="select a.tanggal,a.kodevhc,a.downtime,b.* from ".$dbname.".vhc_penggantianht a left join ".$dbname.".vhc_penggantiandt b on a.notransaksi=b.notransaksi 
            where a.kodeorg='".substr($comId, 0,4)."' and a.tanggal between  '".tanggalsystem($_GET['tglAwal'])."' and '".$tglAkhir."' ".$where."";
            $qRvhc=mysql_query($sql) or die(mysql_error());
            $row=mysql_num_rows($qRvhc);
            if($row>1)
            {
                $no=0;
                while($rRvhc=mysql_fetch_assoc($qRvhc))
                {
                        $no+=1;
                        $sbrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$rRvhc['kodebarang']."'";
                        $qbrg=mysql_query($sbrg) or die(mysql_error());
                        $rbrg=mysql_fetch_assoc($qbrg);	
                        $stream.="	<tr class=rowcontent>
                                        <td>".$no."</td>
                                        <td>".$rRvhc['notransaksi']."</td>
                                        <td>".tanggalnormal($rRvhc['tanggal'])."</td>
                                        <td>".$rRvhc['kodevhc']."</td>
                                        <td align=right>".$rRvhc['downtime']."</td>
                                        <td>".$rRvhc['kodebarang']."</td>
                                        <td>".$rbrg['namabarang']."</td>
                                        <td>".$rRvhc['satuan']."</td>
                                        <td>".$rRvhc['jumlah']."</td>
                                        <td>".$rRvhc['keterangan']."</td>
                                </tr>";
                }
            }
            else
            {
                $stream.="<tr class=rowcontent><td colspan=9>Not Found</td></tr>";
            }

            $stream.="</table>Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
            $dte=date("YmdHis");
            $nop_="ReportComponentUsage_".$dte;
            $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
                         gzwrite($gztralala, $stream);
                         gzclose($gztralala);
                         echo "<script language=javascript1.2>
                            window.location='tempExcel/".$nop_.".xls.gz';
                            </script>";

        break;
         case 'pdf':
             if($comId=='')
            {
                echo"warning: Working unit required";
                exit();
            }
            if($tglAkhir==''||$tglAwal='')
            {
                echo"warning: Date required";
                exit();
            }
        class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
                                global $comId;
                                global $kdVhc;
                                global $jenisVhc;
                                global $period;
                                global $tglAkhir;
                                global $tglAwal;

                # Alamat & No Telp
                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                $orgData = fetchData($query);

                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 12;
                $path='images/logo.jpg';
                $this->Image($path,$this->lMargin,$this->tMargin,70);	
                $this->SetFont('Arial','B',9);
                $this->SetFillColor(255,255,255);	
                $this->SetX(100);   
                $this->Cell($width-100,$height,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
                $this->SetX(100); 		
                $this->Cell($width-100,$height,$orgData[0]['alamat'],0,1,'L');	
                $this->SetX(100); 			
                $this->Cell($width-100,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
                $this->Line($this->lMargin,$this->tMargin+($height*4),
                    $this->lMargin+$width,$this->tMargin+($height*4));
                $this->Ln();
                $this->Ln();
                $this->SetFont('Arial','',8);
                                if($comId!='')
                                {
                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['kodeorg'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(45/100*$width,$height,$comId,'',0,'L');
                                }
                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['user'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(15/100*$width,$height, $_SESSION['standard']['username'],0,0,'L');
                $this->Ln();
                                if($comId!='')
                                {

                                $query2 = selectQuery($dbname,'organisasi','namaorganisasi',
                                "kodeorganisasi='".$comId."'");
                                $orgData2 = fetchData($query2);
                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['unit'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(45/100*$width,$height,$orgData2[0]['namaorganisasi'],'',0,'L');
                                }
                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['tanggal'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(15/100*$width,$height,date('d-m-Y H:i:s'),'',1,'L');		

                                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['periode'].":".$_GET['tglAwal']."-".$_GET['tglAkhir'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(45/100*$width,$height,$period,'',0,'L');


                $this->Ln();
                $this->SetFont('Arial','U',12);
                $this->Cell($width,$height,$_SESSION['lang']['laporanPenggunaanKomponen'],0,1,'C');	
                $this->Ln();	
                 $this->SetFont('Arial','',8);
                $this->SetFillColor(220,220,220);
                                $this->Cell(3/100*$width,$height,'No',1,0,'C',1);
                                $this->Cell(15/100*$width,$height,$_SESSION['lang']['notransaksi'],1,0,'C',1);
                                $this->Cell(10/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);
                                $this->Cell(13/100*$width,$height,$_SESSION['lang']['kodevhc'],1,0,'C',1);
                                $this->Cell(25/100*$width,$height,$_SESSION['lang']['namabarang'],1,0,'C',1);
                                $this->Cell(6/100*$width,$height,$_SESSION['lang']['satuan'],1,0,'C',1);
                                $this->Cell(10/100*$width,$height,$_SESSION['lang']['jumlah'],1,0,'C',1);
                                $this->Cell(18/100*$width,$height,$_SESSION['lang']['keterangan'],1,1,'C',1);

            }

            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
        }
        $pdf=new PDF('P','pt','A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
                $pdf->AddPage();

                $pdf->SetFillColor(255,255,255);
                $pdf->SetFont('Arial','',7);	
        if($jenisVhc!='')
        {
         $where=" and kodevhc in (select distinct kodevhc from ".$dbname.".vhc_5master where jenisvhc='".$jenisVhc."' and kodetraksi='".$comId."')";   
         if($kdVhc!='')
        {
            $where=" and kodevhc='".$kdVhc."'";
        }
        }else{
         $where=" and kodevhc in (select distinct kodevhc from ".$dbname.".vhc_5master where kodetraksi='".$comId."')";   
        }
        $sql="select a.tanggal,a.kodevhc,a.downtime,a.posting,a.notransaksi,b.kodebarang,b.jumlah,b.satuan,b.keterangan from ".$dbname.".vhc_penggantianht a left join ".$dbname.".vhc_penggantiandt b on a.notransaksi=b.notransaksi 
            where a.kodeorg='".substr($comId, 0,4)."' and a.tanggal between  '".tanggalsystem($_POST['tglAwal'])."' and '".$tglAkhir."' ".$where."";
        $sql="select a.tanggal,a.kodevhc,b.* from ".$dbname.".vhc_penggantianht a left join ".$dbname.".vhc_penggantiandt b on a.notransaksi=b.notransaksi 
            where a.kodeorg='".substr($comId, 0,4)."' and a.tanggal between  '".tanggalsystem($_GET['tglAwal'])."' and '".$tglAkhir."' ".$where."";
        //exit("Error".$sql);
        $qRvhc=mysql_query($sql) or die(mysql_error());
        $row=mysql_num_rows($qRvhc);
        if($row>1)
        {
                $no=0;
                while($rRvhc=mysql_fetch_assoc($qRvhc))
                {
                        $sbrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$rRvhc['kodebarang']."'";
                        $qbrg=mysql_query($sbrg) or die(mysql_error());
                        $rbrg=mysql_fetch_assoc($qbrg);	
                        $no+=1;
                        $pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
                        $pdf->Cell(15/100*$width,$height,$rRvhc['notransaksi'],1,0,'L',1);
                        $pdf->Cell(10/100*$width,$height,tanggalnormal($rRvhc['tanggal']),1,0,'C',1);
                        $pdf->Cell(13/100*$width,$height,$rRvhc['kodevhc'],1,0,'L',1);
                        $pdf->Cell(25/100*$width,$height,$rbrg['namabarang'],1,0,'L',1);
                        $pdf->Cell(6/100*$width,$height,$rRvhc['satuan'],1,0,'C',1);
                        $pdf->Cell(10/100*$width,$height,$rRvhc['jumlah'],1,0,'R',1);
                        $pdf->Cell(18/100*$width,$height,$rRvhc['keterangan'],1,1,'L',1);

                }
        }
        else
        {
                $pdf->Cell(83/100*$width,$height,'Not Found',1,1,'C',1);
        }
        $pdf->Output();
        break;
        default:
        break;
}


?>

