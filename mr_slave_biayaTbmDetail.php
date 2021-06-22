<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['pt']==''?$pt=$_GET['pt']:$pt=$_POST['pt'];
$_POST['unit']==''?$unit=$_GET['unit']:$unit=$_POST['unit'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];

if($pt=='')
{
    exit("Error:Field Tidak Boleh Kosong");
}

// dapatkan tahun bulan
$qwe=explode('-',$periode); $tahun=$qwe[0]; $bulan=$qwe[1];

// default: pt - unit
$kodeorg=" like '".$unit."%' ";

// kalo pt doang, dapatkan unit-unitnya
$unitunit="('')";
if($unit==''){
    $unitunit="(";
    $str="select kodeorganisasi from ".$dbname.".organisasi 
        where induk='".$pt."' and tipe='KEBUN'";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=  mysql_fetch_assoc($query))
    {
        $unitunit.="'".$res['kodeorganisasi']."',";
    }    
    $unitunit=substr($unitunit,0,-1);
    $unitunit=$unitunit.")";
    $kodeorg=" in ".$unitunit;
}

// selain preview excel pdf
if($proses=='getkebun'){ 
    $optkebun="<option value=''>".$_SESSION['lang']['all']."</option>";
    $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
        where induk='".$pt."' and tipe='KEBUN'";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=  mysql_fetch_assoc($query))
    {
        $optkebun.="<option value=".$res['kodeorganisasi'].">".$res['namaorganisasi']."</option>";
    }
    if($pt=='')$optkebun="<option value=''></option>";
    echo $optkebun;
// preview excel pdf    
}else{ 
    // luas blok per tahuntanam
    $str="SELECT tahuntanam, sum(luasareaproduktif) as luas FROM ".$dbname.".setup_blok
        WHERE statusblok in('TBM','TB') and substr(kodeorg,1,4) ".$kodeorg." group by tahuntanam order by tahuntanam";
    $tt[0]=0;
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $tt[$res['tahuntanam']]=$res['tahuntanam'];
        $luas[$res['tahuntanam']]+=$res['luas'];
        $luastotal+=$res['luas'];
    }       

    // data per tahuntanam per akun 126
    $str="SELECT tahuntanam, substr(noakun,1,5) as noakun, sum(debet) as debet, sum(kredit) as kredit FROM ".$dbname.".keu_jurnalsum_blok_vw
        WHERE ((substr(kodeorg,1,4) ".$kodeorg.") or (substr(kodeblok,1,4) ".$kodeorg.")) and periode <= '".$periode."' and noakun like '126%' group by tahuntanam, substr(noakun,1,5)";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
//        $akunhead[$res['noakun']]=$res['noakun'];
        $ttoke=false;
        if(!empty($tt))foreach($tt as $tata){
            if($res['tahuntanam']==$tata)$ttoke=true;
        }
        
        if($ttoke){
            $qwe=$res['noakun'].$res['tahuntanam'];
        }else{
            $qwe=$res['noakun'].'0';                
        }
        
//        $qwe=$res['noakun'].$res['tahuntanam'];
        $arr[$qwe]['noakun']=$res['noakun'];
        $arr[$qwe]['rp']+=($res['debet']-$res['kredit']);
    }
    $str="SELECT tahuntanam, noakun, namaakun, sum(debet) as debet, sum(kredit) as kredit FROM ".$dbname.".keu_jurnalsum_blok_vw
        WHERE substr(kodeorg,1,4) ".$kodeorg." and periode <= '".$periode."' and length(noakun)=7 and noakun like '126%' group by tahuntanam, noakun";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $ttoke=false;
        if(!empty($tt))foreach($tt as $tata){
            if($res['tahuntanam']==$tata)$ttoke=true;
        }
        
        if($ttoke){
            $qwe=$res['noakun'].$res['tahuntanam'];
        }else{
            $qwe=$res['noakun'].'0';                
        }

        $akunlist[$res['noakun']]=$res['noakun'];
        $namaakun[$res['noakun']]=$res['namaakun'];
//        $qwe=$res['noakun'].$res['tahuntanam'];
        $arr[$qwe]['noakun']=$res['noakun'];
        $arr[$qwe]['rp']+=($res['debet']-$res['kredit']);
    }
    
    // noakun tanam baru
    $str="SELECT noakun, namaakun FROM ".$dbname.".keu_5akun
        WHERE length( noakun ) = 5 and noakun between '12601' and '12605'";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $akuntb[$res['noakun']]=$res['noakun'];
        $namaakun[$res['noakun']]=$res['namaakun'];
    }

    // noakun tbm
    $str="SELECT noakun, namaakun FROM ".$dbname.".keu_5akun
        WHERE length( noakun ) = 5 and noakun between '12606' and '12616'";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $akuntbm[$res['noakun']]=$res['noakun'];
        $namaakun[$res['noakun']]=$res['namaakun'];
    }

    // noakun 17
    $str="SELECT noakun, namaakun FROM ".$dbname.".keu_5akun
        WHERE length( noakun ) = 5 and noakun between '12617' and '12617'";
//    echo $str;
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $akun17[$res['noakun']]=$res['noakun'];
        $namaakun[$res['noakun']]=$res['namaakun'];
    }
    
//    echo "<pre>";
//    print_r($akun17);
//    echo "</pre>";
    
    if($proses=='excel')
    {
        $bg=" bgcolor=#DEDEDE";
        $brdr=1;
        $tab.='PT '.$pt;
        if($unit!='')$tab.=', UNIT '.$unit;
        $tab.='<br>LAPORAN SUMMARY PEMBUKAAN LAHAN DAN TANAMAN BELUM MENGHASILKAN<br>PER TAHUN TANAM<br>S/D '.$bulan.'-'.$tahun;
    }
    else
    { 
        $bg="";
        $brdr=0;
    }
    // header ==================================================================
    $tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable >
        <thead class=rowheader>
        <tr>
        <td align=right colspan=3 ".$bg.">Tahun Tanam</td>";
        if(!empty($tt))foreach($tt as $tata){
            if($tata==0)$tab.="<td align=center colspan=2 ".$bg.">Undefined</td>";
            else $tab.="<td align=center colspan=2 ".$bg.">".$tata."</td>";
        }
        $tab.="<td align=center colspan=2 ".$bg.">Total</td>
        </tr>
        <tr>
        <td align=center colspan=2 width=50px ".$bg.">Nomor Akun</td>
        <td align=right colspan=1 ".$bg.">Luas (Ha)</td>";
        if(!empty($tt))foreach($tt as $tata){
            $tab.="<td align=right colspan=2 ".$bg.">".number_format($luas[$tata])."</td>";
        }
        $tab.="<td align=right colspan=2 ".$bg.">".number_format($luastotal)."</td>
        </tr>
        <tr>
        <td align=right colspan=1 ".$bg.">126</td>
        <td align=right colspan=1 ".$bg.">XXXX</td>
        <td align=left colspan=1 ".$bg.">Nama Akun</td>";
        if(!empty($tt))foreach($tt as $tata){
            $tab.="<td align=center ".$bg.">Total Rp.</td>
            <td align=center ".$bg.">per Ha</td>";
        }
        $tab.="<td align=center ".$bg.">Total Rp.</td>
        <td align=center ".$bg.">per Ha</td>
        </tr>
        </thead>
        <tbody>
    ";    
    
    $totin=array();
    // TB ======================================================================   
    $totat=array();    
    if(!empty($akuntb))foreach($akuntb as $akun){
        $tab.="<tr class=rowtitle>
        <td align=right colspan=2 ".$bg.">".$akun."XX</td>
        <td align=left colspan=1 ".$bg.">".$namaakun[$akun]."</td>";
        $total=0;
        if(!empty($tt))foreach($tt as $tata){
            $qwe=$akun.$tata;
            @$perha=$arr[$qwe]['rp']/$luas[$tata];
            $tab.="<td align=right ".$bg.">".number_format($arr[$qwe]['rp'])."</td>
            <td align=right ".$bg.">".number_format($perha)."</td>";
            $totat[$tata]+=$arr[$qwe]['rp'];
            $total+=$arr[$qwe]['rp'];
            $totin[$tata]+=$arr[$qwe]['rp'];
        }
        @$totalperha=$total/$luastotal;
        $tab.="<td align=right ".$bg.">".number_format($total)."</td>
        <td align=right ".$bg.">".number_format($totalperha)."</td>
        </tr>";
        // detail TB ===========================================================
        if(!empty($akunlist))foreach($akunlist as $akun2){
            $akun22=substr($akun2,0,5);
            if($akun==$akun22){
            $tab.="<tr class=rowcontent>
            <td align=right colspan=2>&nbsp;".$akun2."</td>
            <td align=left colspan=1>".$namaakun[$akun2]."</td>";
            $totalin=0;
            if(!empty($tt))foreach($tt as $tata){
                $qwe=$akun2.$tata;
                @$perha=$arr[$qwe]['rp']/$luas[$tata];
                $tab.="<td align=right>".number_format($arr[$qwe]['rp'])."</td>
                <td align=right>".number_format($perha)."</td>";
                $totalin+=$arr[$qwe]['rp'];
            }
            @$totalperha=$totalin/$luastotal;
            $tab.="<td align=right>".number_format($totalin)."</td>
            <td align=right>".number_format($totalperha)."</td>
            </tr>";
            }
        }
    }    
    // total TB ================================================================
    $tab.="<tr class=rowtitle>
    <td align=left colspan=3 ".$bg.">Sub Total TB</td>";
    $total=0;
    if(!empty($tt))foreach($tt as $tata){
        @$perha=$totat[$tata]/$luas[$tata];
        $tab.="<td align=right ".$bg.">".number_format($totat[$tata])."</td>
        <td align=right ".$bg.">".number_format($perha)."</td>";
        $total+=$totat[$tata];
    }
    @$totalperha=$total/$luastotal;
    $tab.="<td align=right ".$bg.">".number_format($total)."</td>
    <td align=right ".$bg.">".number_format($totalperha)."</td>
    </tr>";
    
    // TBM =====================================================================   
    $totat=array();    
    if(!empty($akuntbm))foreach($akuntbm as $akun){
        $tab.="<tr class=rowtitle>
        <td align=right colspan=2>".$akun."XX</td>
        <td align=left colspan=1>".$namaakun[$akun]."</td>";
        $total=0;
        if(!empty($tt))foreach($tt as $tata){
            $qwe=$akun.$tata;
            @$perha=$arr[$qwe]['rp']/$luas[$tata];
            $tab.="<td align=right>".number_format($arr[$qwe]['rp'])."</td>
            <td align=right>".number_format($perha)."</td>";
            $totat[$tata]+=$arr[$qwe]['rp'];
            $total+=$arr[$qwe]['rp'];
            $totin[$tata]+=$arr[$qwe]['rp'];
        }
        @$totalperha=$total/$luastotal;
        $tab.="<td align=right>".number_format($total)."</td>
        <td align=right>".number_format($totalperha)."</td>
        </tr>";
        // detail TBM ==========================================================
        if(!empty($akunlist))foreach($akunlist as $akun2){
            $akun22=substr($akun2,0,5);
            if($akun==$akun22){
            $tab.="<tr class=rowcontent>
            <td align=right colspan=2>&nbsp;".$akun2."</td>
            <td align=left colspan=1>".$namaakun[$akun2]."</td>";
            $totalin=0;
            if(!empty($tt))foreach($tt as $tata){
                $qwe=$akun2.$tata;
                @$perha=$arr[$qwe]['rp']/$luas[$tata];
                $tab.="<td align=right>".number_format($arr[$qwe]['rp'])."</td>
                <td align=right>".number_format($perha)."</td>";
                $totalin+=$arr[$qwe]['rp'];
            }
            @$totalperha=$totalin/$luastotal;
            $tab.="<td align=right>".number_format($totalin)."</td>
            <td align=right>".number_format($totalperha)."</td>
            </tr>";
            }
        }        
    }    
    // total TBM ===============================================================
    $tab.="<tr class=rowtitle>
    <td align=left colspan=3 ".$bg.">Sub Total TBM</td>";
    $total=0;
    if(!empty($tt))foreach($tt as $tata){
        @$perha=$totat[$tata]/$luas[$tata];
        $tab.="<td align=right ".$bg.">".number_format($totat[$tata])."</td>
        <td align=right ".$bg.">".number_format($perha)."</td>";
        $total+=$totat[$tata];
    }
    @$totalperha=$total/$luastotal;
    $tab.="<td align=right ".$bg.">".number_format($total)."</td>
    <td align=right ".$bg.">".number_format($totalperha)."</td>
    </tr>";    
    
    // 17 ======================================================================   
    $totat=array();    
    if(!empty($akun17))foreach($akun17 as $akun){
        $tab.="<tr class=rowtitle>
        <td align=right colspan=2>".$akun."XX</td>
        <td align=left colspan=1>".$namaakun[$akun]."</td>";
        $total=0;
        if(!empty($tt))foreach($tt as $tata){
            $qwe=$akun.$tata;
            @$perha=$arr[$qwe]['rp']/$luas[$tata];
            $tab.="<td align=right>".number_format($arr[$qwe]['rp'])."</td>
            <td align=right>".number_format($perha)."</td>";
            $totat[$tata]+=$arr[$qwe]['rp'];
            $total+=$arr[$qwe]['rp'];
            $totin[$tata]+=$arr[$qwe]['rp'];
        }
        @$totalperha=$total/$luastotal;
        $tab.="<td align=right>".number_format($total)."</td>
        <td align=right>".number_format($totalperha)."</td>
        </tr>";
        // detail TBM ==========================================================
        if(!empty($akunlist))foreach($akunlist as $akun2){
            $akun22=substr($akun2,0,5);
            if($akun==$akun22){
            $tab.="<tr class=rowcontent>
            <td align=right colspan=2>&nbsp;".$akun2."</td>
            <td align=left colspan=1>".$namaakun[$akun2]."</td>";
            $totalin=0;
            if(!empty($tt))foreach($tt as $tata){
                $qwe=$akun2.$tata;
                @$perha=$arr[$qwe]['rp']/$luas[$tata];
                $tab.="<td align=right>".number_format($arr[$qwe]['rp'])."</td>
                <td align=right>".number_format($perha)."</td>";
                $totalin+=$arr[$qwe]['rp'];
            }
            @$totalperha=$totalin/$luastotal;
            $tab.="<td align=right>".number_format($totalin)."</td>
            <td align=right>".number_format($totalperha)."</td>
            </tr>";
            }
        }        
    }    
    // total 17 ================================================================
    $tab.="<tr class=rowtitle>
    <td align=left colspan=3 ".$bg.">Sub Total</td>";
    $total=0;
    if(!empty($tt))foreach($tt as $tata){
        @$perha=$totat[$tata]/$luas[$tata];
        $tab.="<td align=right ".$bg.">".number_format($totat[$tata])."</td>
        <td align=right ".$bg.">".number_format($perha)."</td>";
        $total+=$totat[$tata];
    }
    @$totalperha=$total/$luastotal;
    $tab.="<td align=right ".$bg.">".number_format($total)."</td>
    <td align=right ".$bg.">".number_format($totalperha)."</td>
    </tr>";    

    // grand total =============================================================
    $tab.="<tr class=rowtitle>
    <td align=left colspan=3 ".$bg.">Grand Total</td>";
    $total=0;
    if(!empty($tt))foreach($tt as $tata){
        @$perha=$totin[$tata]/$luas[$tata];
        $tab.="<td align=right ".$bg.">".number_format($totin[$tata])."</td>
        <td align=right ".$bg.">".number_format($perha)."</td>";
        $total+=$totin[$tata];
    }
    @$totalperha=$total/$luastotal;
    $tab.="<td align=right ".$bg.">".number_format($total)."</td>
    <td align=right ".$bg.">".number_format($totalperha)."</td>
    </tr>";    
    
    $tab.="</tbody></table>";    
    switch($proses)
    {
        case'preview': 
        echo $tab;
        break;    
        case'excel':
        $tab.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
        $dte=date("YmdHis");
        $nop_="mr_biayaTbmDetail_".$pt.$unit.$periode; 
        if(strlen($tab)>0)
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
            if(!fwrite($handle,$tab))
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
        }
        break;
        case'pdf':
        $cols=247.5;
        $wkiri=5;
        $wlain=4.5;

        class PDF extends FPDF {
        function Header() {
            global $periode;
            global $pt;
            global $unit;
            global $optNm;
            global $optBulan;
            global $tahun;
            global $bulan;
            global $dbname;
            global $luas;
            global $wkiri, $wlain;
            global $luasbudg, $luasreal,$tt;
                $width = $this->w - $this->lMargin - $this->rMargin;

            $height = 20;
            $this->SetFillColor(220,220,220);
            $this->SetFont('Arial','B',12);

            $kepala='PT '.$pt;
            if($unit!='')$kepala.=', UNIT '.$unit;
            $this->Cell($width,$height,$kepala,NULL,0,'L',1);
            $this->Ln();
            $this->Cell($width,$height,'LAPORAN SUMMARY PEMBUKAAN DAN TANAMAN BELUM MENGHASILKAN',NULL,0,'L',1);
            $this->Ln();
            $this->Cell($width,$height,'PER TAHUN TANAM',NULL,0,'L',1);
            $this->Ln();
            $this->Cell($width,$height,'S/D '.$bulan.'-'.$tahun,NULL,0,'L',1);
            $this->Ln();
            $this->Ln();

            $height = 15;
            $this->SetFont('Arial','B',7);
            $this->Cell(($wkiri+$wlain+$wlain)/100*$width,$height,'Tahun Tanam',1,0,'C',1);	
            if(!empty($tt))foreach($tt as $tata){
                if($tata==0)$this->Cell(($wlain+$wlain)/100*$width,$height,'Undefined',1,0,'C',1);
                else $this->Cell(($wlain+$wlain)/100*$width,$height,$tata,1,0,'C',1);	
            }
            $this->Cell(($wlain+$wlain)/100*$width,$height,'Total',1,0,'C',1);	
            $this->Ln();
            
//        <thead class=rowheader>
//        <tr>
//        <td align=right colspan=3 ".$bg.">Tahun Tanam</td>";
//        if(!empty($tt))foreach($tt as $tata){
//            if($tata==0)$tab.="<td align=center colspan=2 ".$bg.">Undefined</td>";
//            else $tab.="<td align=center colspan=2 ".$bg.">".$tata."</td>";
//        }
//        $tab.="<td align=center colspan=2 ".$bg.">Total</td>
//        </tr>
//        <tr>
//        <td align=center colspan=2 ".$bg.">Nomor Akun</td>
//        <td align=right colspan=1 ".$bg.">Luas (Ha)</td>";
//        if(!empty($tt))foreach($tt as $tata){
//            $tab.="<td align=right colspan=2 ".$bg.">".numberformat($luas[$tata])."</td>";
//        }
//        $tab.="<td align=right colspan=2 ".$bg.">".numberformat($luastotal)."</td>
//        </tr>
//        <tr>
//        <td align=right colspan=1 ".$bg.">126</td>
//        <td align=right colspan=1 ".$bg.">XX</td>
//        <td align=left colspan=1 ".$bg.">Nama Akun</td>";
//        if(!empty($tt))foreach($tt as $tata){
//            $tab.="<td align=center ".$bg.">Total Rp.</td>
//            <td align=center ".$bg.">per Ha</td>";
//        }
//        $tab.="<td align=center ".$bg.">Total Rp.</td>
//        <td align=center ".$bg.">per Ha</td>
//        </tr>
//        </thead>
            
        }
        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
        }
    }
        //================================

        $pdf=new PDF('L','pt','A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 15;
        $pdf->AddPage();
        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','',7);

//        $no=1;
//    // pdf array content =========================================================================
//        if(!empty($dzArr))foreach($dzArr as $keg){
//            $pdf->Cell(3/100*$width,$height,$no,1,0,'R',1);	
//            $pdf->Cell($wkiri/100*$width,$height,$keg['namaakun'],1,0,'L',1);	
//            $pdf->Cell($wlain/100*$width,$height,$kamussatuan[$keg['noakun']],1,0,'L',1);	
//            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[110],0),1,0,'R',1);	
//            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[111]/1000,0),1,0,'R',1);	
//            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[112],0),1,0,'R',1);	
//            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[120],0),1,0,'R',1);	
//            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[121]/1000,0),1,0,'R',1);	
//            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[122],0),1,0,'R',1);	
//            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[130],0),1,0,'R',1);	
//            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[131]/1000,0),1,0,'R',1);	
//            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[132],0),1,0,'R',1);	
//            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[210],0),1,0,'R',1);	
//            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[211]/1000,0),1,0,'R',1);	
//            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[212],0),1,0,'R',1);	
//            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[220],0),1,0,'R',1);	
//            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[221]/1000,0),1,0,'R',1);	
//            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[222],0),1,0,'R',1);	
//            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[311],2),1,0,'R',1);	
//            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[312],2),1,0,'R',1);	
//            $no+=1;
//            $pdf->Ln();
//        }else echo 'Data Empty.';
//        $pdf->Cell((3/100*$width)+($wkiri/100*$width)+($wlain/100*$width),$height,'Total',1,0,'C',1);	
//        $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($total[111]/1000,0),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($total[112],0),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($total[121]/1000,0),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($total[122],0),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($total[131]/1000,0),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($total[132],0),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($total[211]/1000,0),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($total[212],0),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($total[221]/1000,0),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($total[222],0),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($total[311],2),1,0,'R',1);	
//        $pdf->Cell($wlain/100*$width,$height,numberformat($total[312],2),1,0,'R',1);

        $pdf->Output();	 
        break;
        default:
        break;        
        
    }    
    
}
	
?>
