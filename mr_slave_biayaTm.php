<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['pt']==''?$pt=$_GET['pt']:$pt=$_POST['pt'];
$_POST['unit']==''?$unit=$_GET['unit']:$unit=$_POST['unit'];
$_POST['afdeling']==''?$afdeling=$_GET['afdeling']:$afdeling=$_POST['afdeling'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];

//$proses=$_POST['proses'];
//$pt=$_POST['pt'];
//$unit=$_POST['unit'];
//$afdeling=$_POST['afdeling'];
//$periode=$_POST['periode'];

//echo "proses:".$proses." pt:".$pt." unit:".$unit." afdeling:".$afdeling." periode:".$periode;

// dapatkan tahun bulan
$qwe=explode('-',$periode); $tahun=$qwe[0]; $bulan=$qwe[1];

$unitunit="('')";

// default: pt
$kodeorg=" like '".$pt."%'";

// kalo pt doang, dapatkan unit-unitnya
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
}else if($afdeling=='')$kodeorg=" like '".$unit."%'"; // semua afdeling
    else if($afdeling!='')$kodeorg=" like '".$afdeling."%'"; // afdeling tertentu
    
if($pt=='')$kodeorg=" like '".$pt."%'";    

// selain preview excel pdf
if($proses=='getkebun'||$proses=='getafdeling'){ 
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
    }
    if($proses=='getafdeling'){
        $optkebun="<option value=''>".$_SESSION['lang']['all']."</option>";
        $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
            where induk='".$unit."' and tipe='AFDELING'";
        $query=mysql_query($str) or die(mysql_error($conn));
        while($res=  mysql_fetch_assoc($query))
        {
            $optkebun.="<option value=".$res['kodeorganisasi'].">".$res['namaorganisasi']."</option>";
        }
        if($unit=='')$optkebun="<option value=''></option>";
        echo $optkebun;
    }
// preview excel pdf    
}else{ 
    if($afdeling!='')$kode_org=" kodeorg ".$kodeorg; else $kode_org=" substr(kodeorg,1,4) ".$kodeorg;
    if($afdeling!='')$kode_blk=" kodeblok ".$kodeorg; else $kode_blk=" substr(kodeblok,1,4) ".$kodeorg;
    // $kode_blk2 khusus untuk data bulan ini dan sd bulan ini
    // kalo pilihan afdeling kosong, pake kodeorg, kalo ada afdeling pake kodeblok
    if($afdeling!='')$kode_blk2=" kodeblok ".$kodeorg; else $kode_blk2=" substr(kodeorg,1,4) ".$kodeorg;
    if($afdeling!='')$blk=" blok ".$kodeorg; else $blk=" substr(blok,1,4) ".$kodeorg;    
    
    // noakun pemeliharaan tm
    $str="SELECT noakun, namaakun FROM ".$dbname.".keu_5akun
        WHERE length( noakun ) = 5 and noakun between '62101' and '62111' order by noakun";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $akuntb[$res['noakun']]=$res['noakun'];
        $namaakun[$res['noakun']]=$res['namaakun'];
    }
    
    // noakun pemeliharaan tm detail
    $str="SELECT noakun, namaakun FROM ".$dbname.".keu_5akun
        WHERE length( noakun ) = 7 and substr(noakun,1,5) between '62101' and '62111' order by noakun";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $akunlist[$res['noakun']]=$res['noakun'];
        $namaakun[$res['noakun']]=$res['namaakun'];
    }
    
    // luas aktual
    $str="SELECT kodeorg, intiplasma, (luasareaproduktif) as luas FROM ".$dbname.".setup_blok
        WHERE statusblok = 'TM' and ".$kode_org."";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $luasinmaaktu+=$res['luas'];
        if($res['intiplasma']=='I')$luasintiaktu+=$res['luas'];
        if($res['intiplasma']=='P')$luasplasaktu+=$res['luas'];
    }    
    
    // produksi aktual bulan ini
    $str="SELECT blok, intiplasma, (kgwbtanpabrondolan) as luas FROM ".$dbname.".kebun_spb_vs_rencana_blok_vw
        WHERE ".$blk." and periode = '".$periode."'";    
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $prodinmaaktu+=$res['luas'];
        if($res['intiplasma']=='I')$prodintiaktu+=$res['luas'];
        if($res['intiplasma']=='P')$prodplasaktu+=$res['luas'];
    }    
    
    // produksi aktual sd bulan ini
    $str="SELECT blok, intiplasma, (kgwbtanpabrondolan) as luas FROM ".$dbname.".kebun_spb_vs_rencana_blok_vw
        WHERE ".$blk." and periode between '".$tahun."-01' and '".$periode."'";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $prsdinmaaktu+=$res['luas'];
        if($res['intiplasma']=='I')$prsdintiaktu+=$res['luas'];
        if($res['intiplasma']=='P')$prsdplasaktu+=$res['luas'];
    }    

    // luas budget
    $str="SELECT kodeblok, intiplasma, (hathnini+hanonproduktif) as luas FROM ".$dbname.".bgt_blok
        WHERE statusblok = 'TM' and ".$kode_blk."";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $luasinmaangg+=$res['luas'];
        if($res['intiplasma']=='I')$luasintiangg+=$res['luas'];
        if($res['intiplasma']=='P')$luasplasangg+=$res['luas'];
    }    

    //produksi budget bulan ini
    $str="SELECT kodeblok, intiplasma, (kg".$bulan.") as luas FROM ".$dbname.".bgt_produksi_kbn_kg_vw
        WHERE ".$kode_blk." and tahunbudget = '".$tahun."'";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $prodinmaangg+=$res['luas'];
        if($res['intiplasma']=='I')$prodintiangg+=$res['luas'];
        if($res['intiplasma']=='P')$prodplasangg+=$res['luas'];
    }    
    
    $addstr="(";
    for($W=1;$W<=intval($bulan);$W++)
    {
        if($W<10)$jack="kg0".$W;
        else $jack="kg".$W;
        if($W<intval($bulan))$addstr.=$jack."+";
        else $addstr.=$jack;
    }
    $addstr.=")";
    
    //produksi budget sd bulan ini
    $str="SELECT kodeblok, intiplasma, ".$addstr." as luas FROM ".$dbname.".bgt_produksi_kbn_kg_vw
        WHERE ".$kode_blk." and tahunbudget = '".$tahun."'"; 
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $prsdinmaangg+=$res['luas'];
        if($res['intiplasma']=='I')$prsdintiangg+=$res['luas'];
        if($res['intiplasma']=='P')$prsdplasangg+=$res['luas'];
    }    
    
    $addstr="(";
    for($W=1;$W<=12;$W++)
    {
        if($W<10)$jack="kg0".$W;
        else $jack="kg".$W;
        if($W<12)$addstr.=$jack."+";
        else $addstr.=$jack;
    }
    $addstr.=")";
    
    //produksi budget setahun
    $str="SELECT kodeblok, intiplasma, ".$addstr." as luas FROM ".$dbname.".bgt_produksi_kbn_kg_vw
        WHERE ".$kode_blk." and tahunbudget = '".$tahun."'";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $prthinmaangg+=$res['luas'];
        if($res['intiplasma']=='I')$prthintiangg+=$res['luas'];
        if($res['intiplasma']=='P')$prthplasangg+=$res['luas'];
    }        

    // data aktual bulan ini (total)
    $str="SELECT kodeblok, intiplasma, substr(noakun,1,5) as noakun, debet, kredit FROM ".$dbname.".keu_jurnalsum_blok_vw
        WHERE ".$kode_blk2." and periode = '".$periode."' and noakun like '621%'";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $datainmaaktu[$res['noakun']]+=($res['debet']-$res['kredit']);
        if($res['intiplasma']=='I')$dataintiaktu[$res['noakun']]+=($res['debet']-$res['kredit']); else
        if($res['intiplasma']=='P')$dataplasaktu[$res['noakun']]+=($res['debet']-$res['kredit']); else
        $dataundeaktu[$res['noakun']]+=($res['debet']-$res['kredit']);
    }
    
    // data aktual bulan ini (detail)
    $str="SELECT kodeblok, intiplasma, noakun, debet, kredit FROM ".$dbname.".keu_jurnalsum_blok_vw
        WHERE ".$kode_blk2." and periode = '".$periode."' and noakun like '621%' and length(noakun)=7";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $datainmaaktu[$res['noakun']]+=($res['debet']-$res['kredit']);
        if($res['intiplasma']=='I')$dataintiaktu[$res['noakun']]+=($res['debet']-$res['kredit']); else
        if($res['intiplasma']=='P')$dataplasaktu[$res['noakun']]+=($res['debet']-$res['kredit']); else
        $dataundeaktu[$res['noakun']]+=($res['debet']-$res['kredit']);
    }
    
    // data aktual sd bulan ini (total)
    $str="SELECT kodeblok, intiplasma, substr(noakun,1,5) as noakun, debet, kredit FROM ".$dbname.".keu_jurnalsum_blok_vw
        WHERE ".$kode_blk2." and periode between '".$tahun."-01' and '".$periode."' and noakun like '621%'";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $dasdinmaaktu[$res['noakun']]+=($res['debet']-$res['kredit']);
        if($res['intiplasma']=='I')$dasdintiaktu[$res['noakun']]+=($res['debet']-$res['kredit']); else
        if($res['intiplasma']=='P')$dasdplasaktu[$res['noakun']]+=($res['debet']-$res['kredit']); else
        $dasdundeaktu[$res['noakun']]+=($res['debet']-$res['kredit']);
    }

    // data aktual sd bulan ini (detail)
    $str="SELECT kodeblok, intiplasma, noakun, debet, kredit FROM ".$dbname.".keu_jurnalsum_blok_vw
        WHERE ".$kode_blk2." and periode between '".$tahun."-01' and '".$periode."' and noakun like '621%' and length(noakun)=7";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $dasdinmaaktu[$res['noakun']]+=($res['debet']-$res['kredit']);
        if($res['intiplasma']=='I')$dasdintiaktu[$res['noakun']]+=($res['debet']-$res['kredit']); else
        if($res['intiplasma']=='P')$dasdplasaktu[$res['noakun']]+=($res['debet']-$res['kredit']); else
        $dasdundeaktu[$res['noakun']]+=($res['debet']-$res['kredit']);
    }

    // data budget bulan ini (total) dan setahun
    $str="SELECT kodeorg, intiplasma, substr(noakun,1,5) as noakun, (rp".$bulan.") as rp, rupiah FROM ".$dbname.".bgt_budget_kebun_perblok_vw
        WHERE ".$kode_org." and tahunbudget = '".$tahun."' and noakun like '621%'";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $datainmaangg[$res['noakun']]+=$res['rp'];
        if($res['intiplasma']=='I')$dataintiangg[$res['noakun']]+=$res['rp'];
        if($res['intiplasma']=='P')$dataplasangg[$res['noakun']]+=$res['rp'];        
        $dathinmaangg[$res['noakun']]+=$res['rupiah'];
        if($res['intiplasma']=='I')$dathintiangg[$res['noakun']]+=$res['rupiah'];
        if($res['intiplasma']=='P')$dathplasangg[$res['noakun']]+=$res['rupiah'];        
    }

    // data budget bulan ini (detail) dan setahun
    $str="SELECT kodeorg, intiplasma, noakun, (rp".$bulan.") as rp, rupiah FROM ".$dbname.".bgt_budget_kebun_perblok_vw
        WHERE ".$kode_org." and tahunbudget = '".$tahun."' and noakun like '621%' and length(noakun)=7";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $datainmaangg[$res['noakun']]+=$res['rp'];
        if($res['intiplasma']=='I')$dataintiangg[$res['noakun']]+=$res['rp'];
        if($res['intiplasma']=='P')$dataplasangg[$res['noakun']]+=$res['rp'];        
        $dathinmaangg[$res['noakun']]+=$res['rupiah'];
        if($res['intiplasma']=='I')$dathintiangg[$res['noakun']]+=$res['rupiah'];
        if($res['intiplasma']=='P')$dathplasangg[$res['noakun']]+=$res['rupiah'];        
    }

    $addstr="(";
    for($W=1;$W<=intval($bulan);$W++)
    {
        if($W<10)$jack="rp0".$W;
        else $jack="rp".$W;
        if($W<intval($bulan))$addstr.=$jack."+";
        else $addstr.=$jack;
    }
    $addstr.=")";

    // data budget sd bulan ini (total)
    $str="SELECT kodeorg, intiplasma, substr(noakun,1,5) as noakun, ".$addstr." as rp, rupiah as rupiah FROM ".$dbname.".bgt_budget_kebun_perblok_vw
        WHERE ".$kode_org." and tahunbudget = '".$tahun."' and noakun like '621%'";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $dasdinmaangg[$res['noakun']]+=$res['rp'];
        if($res['intiplasma']=='I')$dasdintiangg[$res['noakun']]+=$res['rp'];
        if($res['intiplasma']=='P')$dasdplasangg[$res['noakun']]+=$res['rp'];        
    }    
    
    // data budget sd bulan ini (detail)
    $str="SELECT kodeorg, intiplasma, noakun, ".$addstr." as rp, rupiah as rupiah FROM ".$dbname.".bgt_budget_kebun_perblok_vw
        WHERE ".$kode_org." and tahunbudget = '".$tahun."' and noakun like '621%' and length(noakun)=7";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $dasdinmaangg[$res['noakun']]+=$res['rp'];
        if($res['intiplasma']=='I')$dasdintiangg[$res['noakun']]+=$res['rp'];
        if($res['intiplasma']=='P')$dasdplasangg[$res['noakun']]+=$res['rp'];        
    }    
    
//    echo "<pre>";
//    print_r($dataintiaktu);
//    print_r($dasdintiaktu);
//    echo "</pre>";
    
    if($proses=='excel')
    {
        $bg=" bgcolor=#DEDEDE";
        $brdr=1;
        if($pt=='')$tab.='PT (seluruhnya)'; else $tab.='PT '.$pt;
        if($unit!='')$tab.=', UNIT '.$unit;
        if($afdeling!='')$tab.=', Afdeling '.$afdeling;
        $tab.='<br>LAPORAN BIAYA PEMELIHARAAN TANAMAN MENGHASILKAN<br>01-'.$tahun.'S/D '.$bulan.'-'.$tahun;
    }
    else
    { 
        echo 'LAPORAN BIAYA PEMELIHARAAN TANAMAN MENGHASILKAN<br>01-'.$tahun.' S/D '.$bulan.'-'.$tahun;
        $bg="";
        $brdr=0;
    } 
    
    // hitung varian produksi
    $prodinmavari=$prodinmaaktu-$prodinmaangg;  
    $prodintivari=$prodintiaktu-$prodintiangg;  
    $prodplasvari=$prodplasaktu-$prodplasangg;  
    $prsdinmavari=$prsdinmaaktu-$prsdinmaangg;  
    $prsdintivari=$prsdintiaktu-$prsdintiangg;  
    $prsdplasvari=$prsdplasaktu-$prsdplasangg;  
    
    // hitung varian akun, hitung total
    if(!empty($akuntb))foreach($akuntb as $akun){
        $datainmavari[$akun]=$datainmaaktu[$akun]-$datainmaangg[$akun];
        $dataintivari[$akun]=$dataintiaktu[$akun]-$dataintiangg[$akun];
        $dataplasvari[$akun]=$dataplasaktu[$akun]-$dataplasangg[$akun];
        $dasdinmavari[$akun]=$dasdinmaaktu[$akun]-$dasdinmaangg[$akun];
        $dasdintivari[$akun]=$dasdintiaktu[$akun]-$dasdintiangg[$akun];
        $dasdplasvari[$akun]=$dasdplasaktu[$akun]-$dasdplasangg[$akun];
        
        // total inti
        $totaintiaktu+=$dataintiaktu[$akun];
        $totaintiangg+=$dataintiangg[$akun];
        $totaintivari+=$dataintivari[$akun];
        $tosdintiaktu+=$dasdintiaktu[$akun];
        $tosdintiangg+=$dasdintiangg[$akun];
        $tosdintivari+=$dasdintivari[$akun];
        $tothintiangg+=$dathintiangg[$akun];

        // total plasma
        $totaplasaktu+=$dataplasaktu[$akun];
        $totaplasangg+=$dataplasangg[$akun];
        $totaplasvari+=$dataplasvari[$akun];
        $tosdplasaktu+=$dasdplasaktu[$akun];
        $tosdplasangg+=$dasdplasangg[$akun];
        $tosdplasvari+=$dasdplasvari[$akun];
        $tothplasangg+=$dathplasangg[$akun];
        
        // total undef
        $totaundeaktu+=$dataundeaktu[$akun];
        $tosdundeaktu+=$dasdundeaktu[$akun];
        
        // total inti plasma
        $totainmaaktu+=$datainmaaktu[$akun];
        $totainmaangg+=$datainmaangg[$akun];
        $totainmavari+=$datainmavari[$akun];
        $tosdinmaaktu+=$dasdinmaaktu[$akun];
        $tosdinmaangg+=$dasdinmaangg[$akun];
        $tosdinmavari+=$dasdinmavari[$akun];
        $tothinmaangg+=$dathinmaangg[$akun];        
    }        
        
    // header ==================================================================
    $tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable style='width:100%'>
        <thead class=rowheader>
        <tr>
        <td align=center colspan=3 rowspan=3 ".$bg.">Keterangan</td>
        <td align=center colspan=19 ".$bg.">Inti</td>
        <td align=center colspan=19 ".$bg.">Plasma</td>
        <td align=center colspan=2 ".$bg.">Undefined Block</td>
        <td align=center colspan=19 ".$bg.">Inti dan Plasma</td>
        </tr>";
        $tab.="<tr><td align=center colspan=6 ".$bg.">Bulan Ini</td>
        <td align=center colspan=2 rowspan=2 ".$bg.">Varian</td>
        <td align=center colspan=6 ".$bg.">SD Bulan Ini</td>
        <td align=center colspan=2 rowspan=2 ".$bg.">Varian</td>
        <td align=center colspan=3 rowspan=2 ".$bg.">Budget Setahun</td>
        <td align=center colspan=6 ".$bg.">Bulan Ini</td>
        <td align=center colspan=2 rowspan=2 ".$bg.">Varian</td>
        <td align=center colspan=6 ".$bg.">SD Bulan Ini</td>
        <td align=center colspan=2 rowspan=2 ".$bg.">Varian</td>
        <td align=center colspan=3 rowspan=2 ".$bg.">Budget Setahun</td>
        <td align=center ".$bg.">Bulan Ini</td>
        <td align=center ".$bg.">SD Bulan Ini</td>
        <td align=center colspan=6 ".$bg.">Bulan Ini</td>
        <td align=center colspan=2 rowspan=2 ".$bg.">Varian</td>
        <td align=center colspan=6 ".$bg.">SD Bulan Ini</td>
        <td align=center colspan=2 rowspan=2 ".$bg.">Varian</td>
        <td align=center colspan=3 rowspan=2 ".$bg.">Budget Setahun</td>
        </tr>";
        $tab.="<tr><td align=center colspan=3 ".$bg.">Aktual</td>
        <td align=center colspan=3 ".$bg.">Budget</td>
        <td align=center colspan=3 ".$bg.">Aktual</td>
        <td align=center colspan=3 ".$bg.">Budget</td>
        <td align=center colspan=3 ".$bg.">Aktual</td>
        <td align=center colspan=3 ".$bg.">Budget</td>
        <td align=center colspan=3 ".$bg.">Aktual</td>
        <td align=center colspan=3 ".$bg.">Budget</td>
        <td align=center ".$bg.">Aktual</td>
        <td align=center ".$bg.">Aktual</td>
        <td align=center colspan=3 ".$bg.">Aktual</td>
        <td align=center colspan=3 ".$bg.">Budget</td>
        <td align=center colspan=3 ".$bg.">Aktual</td>
        <td align=center colspan=3 ".$bg.">Budget</td>
        </tr>";
        $tab.="<tr><td align=center colspan=3 ".$bg.">Luas (ha)</td>
        <td align=right colspan=3 ".$bg.">".number_format($luasintiaktu,2)."</td>
        <td align=right colspan=3 ".$bg.">".number_format($luasintiangg,2)."</td>
        <td align=right colspan=2 ".$bg."></td>
        <td align=right colspan=3 ".$bg.">".number_format($luasintiaktu,2)."</td>
        <td align=right colspan=3 ".$bg.">".number_format($luasintiangg,2)."</td>
        <td align=right colspan=2 ".$bg."></td>
        <td align=right colspan=3 ".$bg.">".number_format($luasintiangg,2)."</td>
        <td align=right colspan=3 ".$bg.">".number_format($luasplasaktu,2)."</td>
        <td align=right colspan=3 ".$bg.">".number_format($luasplasangg,2)."</td>
        <td align=right colspan=2 ".$bg."></td>
        <td align=right colspan=3 ".$bg.">".number_format($luasplasaktu,2)."</td>
        <td align=right colspan=3 ".$bg.">".number_format($luasplasangg,2)."</td>
        <td align=right colspan=2 ".$bg."></td>
        <td align=right colspan=3 ".$bg.">".number_format($luasplasangg,2)."</td>
        <td align=right ".$bg."></td>
        <td align=right ".$bg."></td>
        <td align=right colspan=3 ".$bg.">".number_format($luasinmaaktu,2)."</td>
        <td align=right colspan=3 ".$bg.">".number_format($luasinmaangg,2)."</td>
        <td align=right colspan=2 ".$bg."></td>
        <td align=right colspan=3 ".$bg.">".number_format($luasinmaaktu,2)."</td>
        <td align=right colspan=3 ".$bg.">".number_format($luasinmaangg,2)."</td>
        <td align=right colspan=2 ".$bg."></td>
        <td align=right colspan=3 ".$bg.">".number_format($luasinmaangg,2)."</td>
        </tr>";
        
        @$prodintiakturibu=$prodintiaktu/1000;
        @$prodintianggribu=$prodintiangg/1000;
        @$prodintivariribu=$prodintivari/1000;
        @$prsdintiakturibu=$prsdintiaktu/1000;
        @$prsdintianggribu=$prsdintiangg/1000;
        @$prsdintivariribu=$prsdintivari/1000;
        @$prthintianggribu=$prthintiangg/1000;

        @$prodplasakturibu=$prodplasaktu/1000;
        @$prodplasanggribu=$prodplasangg/1000;
        @$prodplasvariribu=$prodplasvari/1000;
        @$prsdplasakturibu=$prsdplasaktu/1000;
        @$prsdplasanggribu=$prsdplasangg/1000;
        @$prsdplasvariribu=$prsdplasvari/1000;
        @$prthplasanggribu=$prthplasangg/1000;
        
        @$prodinmaakturibu=$prodinmaaktu/1000;
        @$prodinmaanggribu=$prodinmaangg/1000;
        @$prodinmavariribu=$prodinmavari/1000;
        @$prsdinmaakturibu=$prsdinmaaktu/1000;
        @$prsdinmaanggribu=$prsdinmaangg/1000;
        @$prsdinmavariribu=$prsdinmavari/1000;
        @$prthinmaanggribu=$prthinmaangg/1000;
        $tab.="<tr><td align=center colspan=3 ".$bg.">Produksi (ton)</td>
        <td align=right colspan=3 ".$bg.">".number_format($prodintiakturibu,2)."</td>
        <td align=right colspan=3 ".$bg.">".number_format($prodintianggribu,2)."</td>
        <td align=right colspan=2 ".$bg.">".number_format($prodintivariribu,2)."</td>
        <td align=right colspan=3 ".$bg.">".number_format($prsdintiakturibu,2)."</td>
        <td align=right colspan=3 ".$bg.">".number_format($prsdintianggribu,2)."</td>
        <td align=right colspan=2 ".$bg.">".number_format($prsdintivariribu,2)."</td>
        <td align=right colspan=3 ".$bg.">".number_format($prthintianggribu,2)."</td>
        <td align=right colspan=3 ".$bg.">".number_format($prodplasakturibu,2)."</td>
        <td align=right colspan=3 ".$bg.">".number_format($prodplasanggribu,2)."</td>
        <td align=right colspan=2 ".$bg.">".number_format($prodplasvariribu,2)."</td>
        <td align=right colspan=3 ".$bg.">".number_format($prsdplasakturibu,2)."</td>
        <td align=right colspan=3 ".$bg.">".number_format($prsdplasanggribu,2)."</td>
        <td align=right colspan=2 ".$bg.">".number_format($prsdplasvariribu,2)."</td>
        <td align=right colspan=3 ".$bg.">".number_format($prthplasanggribu,2)."</td>
        <td align=right ".$bg."></td>
        <td align=right ".$bg."></td>
        <td align=right colspan=3 ".$bg.">".number_format($prodinmaaktu/1000,2)."</td>
        <td align=right colspan=3 ".$bg.">".number_format($prodinmaangg/1000,2)."</td>
        <td align=right colspan=2 ".$bg.">".number_format($prodinmavari/1000,2)."</td>
        <td align=right colspan=3 ".$bg.">".number_format($prsdinmaaktu/1000,2)."</td>
        <td align=right colspan=3 ".$bg.">".number_format($prsdinmaangg/1000,2)."</td>
        <td align=right colspan=2 ".$bg.">".number_format($prsdinmavari/1000,2)."</td>
        <td align=right colspan=3 ".$bg.">".number_format($prthinmaangg/1000,2)."</td>
        </tr>";
        $tab.="<tr><td align=center colspan=3 ".$bg."></td>
        <td align=center colspan=1 ".$bg.">Rp. (000)</td>
        <td align=center colspan=1 ".$bg.">Rp./kg</td>
        <td align=center colspan=1 ".$bg.">Rp./Ha</td>
        <td align=center colspan=1 ".$bg.">Rp. (000)</td>
        <td align=center colspan=1 ".$bg.">Rp./kg</td>
        <td align=center colspan=1 ".$bg.">Rp./Ha</td>
        <td align=center colspan=1 ".$bg.">Rp. (000)</td>
        <td align=center colspan=1 ".$bg.">Rp./kg</td>
        <td align=center colspan=1 ".$bg.">Rp. (000)</td>
        <td align=center colspan=1 ".$bg.">Rp./kg</td>
        <td align=center colspan=1 ".$bg.">Rp./Ha</td>
        <td align=center colspan=1 ".$bg.">Rp. (000)</td>
        <td align=center colspan=1 ".$bg.">Rp./kg</td>
        <td align=center colspan=1 ".$bg.">Rp./Ha</td>
        <td align=center colspan=1 ".$bg.">Rp. (000)</td>
        <td align=center colspan=1 ".$bg.">Rp./kg</td>
        <td align=center colspan=1 ".$bg.">Rp. (000)</td>
        <td align=center colspan=1 ".$bg.">Rp./kg</td>
        <td align=center colspan=1 ".$bg.">Rp./Ha</td>
            
        <td align=center colspan=1 ".$bg.">Rp. (000)</td>
        <td align=center colspan=1 ".$bg.">Rp./kg</td>
        <td align=center colspan=1 ".$bg.">Rp./Ha</td>
        <td align=center colspan=1 ".$bg.">Rp. (000)</td>
        <td align=center colspan=1 ".$bg.">Rp./kg</td>
        <td align=center colspan=1 ".$bg.">Rp./Ha</td>
        <td align=center colspan=1 ".$bg.">Rp. (000)</td>
        <td align=center colspan=1 ".$bg.">Rp./kg</td>
        <td align=center colspan=1 ".$bg.">Rp. (000)</td>
        <td align=center colspan=1 ".$bg.">Rp./kg</td>
        <td align=center colspan=1 ".$bg.">Rp./Ha</td>
        <td align=center colspan=1 ".$bg.">Rp. (000)</td>
        <td align=center colspan=1 ".$bg.">Rp./kg</td>
        <td align=center colspan=1 ".$bg.">Rp./Ha</td>
        <td align=center colspan=1 ".$bg.">Rp. (000)</td>
        <td align=center colspan=1 ".$bg.">Rp./kg</td>
        <td align=center colspan=1 ".$bg.">Rp. (000)</td>
        <td align=center colspan=1 ".$bg.">Rp./kg</td>
        <td align=center colspan=1 ".$bg.">Rp./Ha</td>
            
        <td align=center colspan=1 ".$bg.">Rp. (000)</td>
        <td align=center colspan=1 ".$bg.">Rp. (000)</td>
 
        <td align=center colspan=1 ".$bg.">Rp. (000)</td>
        <td align=center colspan=1 ".$bg.">Rp./kg</td>
        <td align=center colspan=1 ".$bg.">Rp./Ha</td>
        <td align=center colspan=1 ".$bg.">Rp. (000)</td>
        <td align=center colspan=1 ".$bg.">Rp./kg</td>
        <td align=center colspan=1 ".$bg.">Rp./Ha</td>
        <td align=center colspan=1 ".$bg.">Rp. (000)</td>
        <td align=center colspan=1 ".$bg.">Rp./kg</td>
        <td align=center colspan=1 ".$bg.">Rp. (000)</td>
        <td align=center colspan=1 ".$bg.">Rp./kg</td>
        <td align=center colspan=1 ".$bg.">Rp./Ha</td>
        <td align=center colspan=1 ".$bg.">Rp. (000)</td>
        <td align=center colspan=1 ".$bg.">Rp./kg</td>
        <td align=center colspan=1 ".$bg.">Rp./Ha</td>
        <td align=center colspan=1 ".$bg.">Rp. (000)</td>
        <td align=center colspan=1 ".$bg.">Rp./kg</td>
        <td align=center colspan=1 ".$bg.">Rp. (000)</td>
        <td align=center colspan=1 ".$bg.">Rp./kg</td>
        <td align=center colspan=1 ".$bg.">Rp./Ha</td>
        </tr>";
        $tab.="</thead><tbody>
    ";    
    
    $totin=array();
    // TB ======================================================================   
    $totat=array();    
    if(!empty($akuntb))foreach($akuntb as $akun){
        $tab.="<tr class=rowtitle>
        <td align=right colspan=2 ".$bg.">".$akun."XX</td>
        <td align=left colspan=1 ".$bg.">".$namaakun[$akun]."</td>";
        $total=0;
        // inti bulan ini per
        @$dataintiaktuperkg=$dataintiaktu[$akun]/$prodintiaktu;
        @$dataintiaktuperha=$dataintiaktu[$akun]/$luasintiaktu;
        @$dataintianggperkg=$dataintiangg[$akun]/$prodintiangg;
        @$dataintianggperha=$dataintiangg[$akun]/$luasintiangg;
        @$dataintivariperkg=$dataintivari[$akun]/$prodintivari;
        // inti sd bulan ini per
        @$dasdintiaktuperkg=$dasdintiaktu[$akun]/$prodintiaktu;
        @$dasdintiaktuperha=$dasdintiaktu[$akun]/$luasintiaktu;
        @$dasdintianggperkg=$dasdintiangg[$akun]/$prodintiangg;
        @$dasdintianggperha=$dasdintiangg[$akun]/$luasintiangg;
        @$dasdintivariperkg=$dasdintivari[$akun]/$prodintivari;
        // inti setahun per
        @$dathintianggperkg=$dathintiangg[$akun]/$prthintiangg;        
        @$dathintianggperha=$dathintiangg[$akun]/$luasintiangg;
        
        // plasma bulan ini per
        @$dataplasaktuperkg=$dataplasaktu[$akun]/$prodplasaktu;
        @$dataplasaktuperha=$dataplasaktu[$akun]/$luasplasaktu;
        @$dataplasanggperkg=$dataplasangg[$akun]/$prodplasangg;
        @$dataplasanggperha=$dataplasangg[$akun]/$luasplasangg;
        @$dataplasvariperkg=$dataplasvari[$akun]/$prodplasvari;
        // plasma sd bulan ini per
        @$dasdplasaktuperkg=$dasdplasaktu[$akun]/$prodplasaktu;
        @$dasdplasaktuperha=$dasdplasaktu[$akun]/$luasplasaktu;
        @$dasdplasanggperkg=$dasdplasangg[$akun]/$prodplasangg;
        @$dasdplasanggperha=$dasdplasangg[$akun]/$luasplasangg;
        @$dasdplasvariperkg=$dasdplasvari[$akun]/$prodplasvari;
        // plasma setahun per
        @$dathplasanggperkg=$dathplasangg[$akun]/$prthplasangg;        
        @$dathplasanggperha=$dathplasangg[$akun]/$luasplasangg;
        
        // inti plasma bulan ini per
        @$datainmaaktuperkg=$datainmaaktu[$akun]/$prodinmaaktu;
        @$datainmaaktuperha=$datainmaaktu[$akun]/$luasinmaaktu;
        @$datainmaanggperkg=$datainmaangg[$akun]/$prodinmaangg;
        @$datainmaanggperha=$datainmaangg[$akun]/$luasinmaangg;
        @$datainmavariperkg=$datainmavari[$akun]/$prodinmavari;
        // inti plasma sd bulan ini per
        @$dasdinmaaktuperkg=$dasdinmaaktu[$akun]/$prodinmaaktu;
        @$dasdinmaaktuperha=$dasdinmaaktu[$akun]/$luasinmaaktu;
        @$dasdinmaanggperkg=$dasdinmaangg[$akun]/$prodinmaangg;
        @$dasdinmaanggperha=$dasdinmaangg[$akun]/$luasinmaangg;
        @$dasdinmavariperkg=$dasdinmavari[$akun]/$prodinmavari;
        // inti plasma setahun per
        @$dathinmaanggperkg=$dathinmaangg[$akun]/$prthinmaangg;        
        @$dathinmaanggperha=$dathinmaangg[$akun]/$luasinmaangg;
        
        @$dataintiaktuperrb=$dataintiaktu[$akun]/1000;
        @$dataintianggperrb=$dataintiangg[$akun]/1000;
        @$dataintivariperrb=$dataintivari[$akun]/1000;
        @$dasdintiaktuperrb=$dasdintiaktu[$akun]/1000;
        @$dasdintianggperrb=$dasdintiangg[$akun]/1000;
        @$dasdintivariperrb=$dasdintivari[$akun]/1000;
        @$dathintianggperrb=$dathintiangg[$akun]/1000;

        @$dataplasaktuperrb=$dataplasaktu[$akun]/1000;
        @$dataplasanggperrb=$dataplasangg[$akun]/1000;
        @$dataplasvariperrb=$dataplasvari[$akun]/1000;
        @$dasdplasaktuperrb=$dasdplasaktu[$akun]/1000;
        @$dasdplasanggperrb=$dasdplasangg[$akun]/1000;
        @$dasdplasvariperrb=$dasdplasvari[$akun]/1000;
        @$dathplasanggperrb=$dathplasangg[$akun]/1000;

        @$dataundeaktuperrb=$dataundeaktu[$akun]/1000;
        @$dasdundeaktuperrb=$dasdundeaktu[$akun]/1000;

        @$datainmaaktuperrb=$datainmaaktu[$akun]/1000;
        @$datainmaanggperrb=$datainmaangg[$akun]/1000;
        @$datainmavariperrb=$datainmavari[$akun]/1000;
        @$dasdinmaaktuperrb=$dasdinmaaktu[$akun]/1000;
        @$dasdinmaanggperrb=$dasdinmaangg[$akun]/1000;
        @$dasdinmavariperrb=$dasdinmavari[$akun]/1000;
        @$dathinmaanggperrb=$dathinmaangg[$akun]/1000;

        $tab.="
        <td align=right ".$bg.">".number_format($dataintiaktuperrb)."</td>
        <td align=right ".$bg.">".number_format($dataintiaktuperkg)."</td>
        <td align=right ".$bg.">".number_format($dataintiaktuperha)."</td>
        <td align=right ".$bg.">".number_format($dataintianggperrb)."</td>
        <td align=right ".$bg.">".number_format($dataintianggperkg)."</td>
        <td align=right ".$bg.">".number_format($dataintianggperha)."</td>
        <td align=right ".$bg.">".number_format($dataintivariperrb)."</td>
        <td align=right ".$bg.">".number_format($dataintivariperkg)."</td>
            
        <td align=right ".$bg.">".number_format($dasdintiaktuperrb)."</td>
        <td align=right ".$bg.">".number_format($dasdintiaktuperkg)."</td>
        <td align=right ".$bg.">".number_format($dasdintiaktuperha)."</td>
        <td align=right ".$bg.">".number_format($dasdintianggperrb)."</td>
        <td align=right ".$bg.">".number_format($dasdintianggperkg)."</td>
        <td align=right ".$bg.">".number_format($dasdintianggperha)."</td>
        <td align=right ".$bg.">".number_format($dasdintivariperrb)."</td>
        <td align=right ".$bg.">".number_format($dasdintivariperkg)."</td>
            
        <td align=right ".$bg.">".number_format($dathintianggperrb)."</td>
        <td align=right ".$bg.">".number_format($dathintianggperkg)."</td>
        <td align=right ".$bg.">".number_format($dathintianggperha)."</td>
            ";
        $tab.="
        <td align=right ".$bg.">".number_format($dataplasaktuperrb)."</td>
        <td align=right ".$bg.">".number_format($dataplasaktuperkg)."</td>
        <td align=right ".$bg.">".number_format($dataplasaktuperha)."</td>
        <td align=right ".$bg.">".number_format($dataplasanggperrb)."</td>
        <td align=right ".$bg.">".number_format($dataplasanggperkg)."</td>
        <td align=right ".$bg.">".number_format($dataplasanggperha)."</td>
        <td align=right ".$bg.">".number_format($dataplasvariperrb)."</td>
        <td align=right ".$bg.">".number_format($dataplasvariperkg)."</td>
            
        <td align=right ".$bg.">".number_format($dasdplasaktuperrb)."</td>
        <td align=right ".$bg.">".number_format($dasdplasaktuperkg)."</td>
        <td align=right ".$bg.">".number_format($dasdplasaktuperha)."</td>
        <td align=right ".$bg.">".number_format($dasdplasanggperrb)."</td>
        <td align=right ".$bg.">".number_format($dasdplasanggperkg)."</td>
        <td align=right ".$bg.">".number_format($dasdplasanggperha)."</td>
        <td align=right ".$bg.">".number_format($dasdplasvariperrb)."</td>
        <td align=right ".$bg.">".number_format($dasdplasvariperkg)."</td>
            
        <td align=right ".$bg.">".number_format($dathplasanggperrb)."</td>
        <td align=right ".$bg.">".number_format($dathplasanggperkg)."</td>
        <td align=right ".$bg.">".number_format($dathplasanggperha)."</td>
            ";
        $tab.="
        <td align=right ".$bg.">".number_format($dataundeaktuperrb)."</td>
        <td align=right ".$bg.">".number_format($dasdundeaktuperrb)."</td>
            ";
        $tab.="
        <td align=right ".$bg.">".number_format($datainmaaktuperrb)."</td>
        <td align=right ".$bg.">".number_format($datainmaaktuperkg)."</td>
        <td align=right ".$bg.">".number_format($datainmaaktuperha)."</td>
        <td align=right ".$bg.">".number_format($datainmaanggperrb)."</td>
        <td align=right ".$bg.">".number_format($datainmaanggperkg)."</td>
        <td align=right ".$bg.">".number_format($datainmaanggperha)."</td>
        <td align=right ".$bg.">".number_format($datainmavariperrb)."</td>
        <td align=right ".$bg.">".number_format($datainmavariperkg)."</td>
            
        <td align=right ".$bg.">".number_format($dasdinmaaktuperrb)."</td>
        <td align=right ".$bg.">".number_format($dasdinmaaktuperkg)."</td>
        <td align=right ".$bg.">".number_format($dasdinmaaktuperha)."</td>
        <td align=right ".$bg.">".number_format($dasdinmaanggperrb)."</td>
        <td align=right ".$bg.">".number_format($dasdinmaanggperkg)."</td>
        <td align=right ".$bg.">".number_format($dasdinmaanggperha)."</td>
        <td align=right ".$bg.">".number_format($dasdinmavariperrb)."</td>
        <td align=right ".$bg.">".number_format($dasdinmavariperkg)."</td>
            
        <td align=right ".$bg.">".number_format($dathinmaanggperrb)."</td>
        <td align=right ".$bg.">".number_format($dathinmaanggperkg)."</td>
        <td align=right ".$bg.">".number_format($dathinmaanggperha)."</td>
            ";
        $tab.="</tr>";
        // detail TB ===========================================================
        if(!empty($akunlist))foreach($akunlist as $akun2){
            $akun22=substr($akun2,0,5);
            if($akun==$akun22){
                $tab.="<tr class=rowcontent>
                <td align=right colspan=2>".$akun2."</td>
                <td align=left colspan=1>".$namaakun[$akun2]."</td>";
                $total=0;
                // inti bulan ini per
                @$dataintiaktuperkg=$dataintiaktu[$akun2]/$prodintiaktu;
                @$dataintiaktuperha=$dataintiaktu[$akun2]/$luasintiaktu;
                @$dataintianggperkg=$dataintiangg[$akun2]/$prodintiangg;
                @$dataintianggperha=$dataintiangg[$akun2]/$luasintiangg;
                @$dataintivariperkg=$dataintivari[$akun2]/$prodintivari;
                // inti sd bulan ini per
                @$dasdintiaktuperkg=$dasdintiaktu[$akun2]/$prodintiaktu;
                @$dasdintiaktuperha=$dasdintiaktu[$akun2]/$luasintiaktu;
                @$dasdintianggperkg=$dasdintiangg[$akun2]/$prodintiangg;
                @$dasdintianggperha=$dasdintiangg[$akun2]/$luasintiangg;
                @$dasdintivariperkg=$dasdintivari[$akun2]/$prodintivari;
                // inti setahun per
                @$dathintianggperkg=$dathintiangg[$akun2]/$prthintiangg;        
                @$dathintianggperha=$dathintiangg[$akun2]/$luasintiangg;

                // plasma bulan ini per
                @$dataplasaktuperkg=$dataplasaktu[$akun2]/$prodplasaktu;
                @$dataplasaktuperha=$dataplasaktu[$akun2]/$luasplasaktu;
                @$dataplasanggperkg=$dataplasangg[$akun2]/$prodplasangg;
                @$dataplasanggperha=$dataplasangg[$akun2]/$luasplasangg;
                @$dataplasvariperkg=$dataplasvari[$akun2]/$prodplasvari;
                // plasma sd bulan ini per
                @$dasdplasaktuperkg=$dasdplasaktu[$akun2]/$prodplasaktu;
                @$dasdplasaktuperha=$dasdplasaktu[$akun2]/$luasplasaktu;
                @$dasdplasanggperkg=$dasdplasangg[$akun2]/$prodplasangg;
                @$dasdplasanggperha=$dasdplasangg[$akun2]/$luasplasangg;
                @$dasdplasvariperkg=$dasdplasvari[$akun2]/$prodplasvari;
                // plasma setahun per
                @$dathplasanggperkg=$dathplasangg[$akun2]/$prthplasangg;        
                @$dathplasanggperha=$dathplasangg[$akun2]/$luasplasangg;

                // inti plasma bulan ini per
                @$datainmaaktuperkg=$datainmaaktu[$akun2]/$prodinmaaktu;
                @$datainmaaktuperha=$datainmaaktu[$akun2]/$luasinmaaktu;
                @$datainmaanggperkg=$datainmaangg[$akun2]/$prodinmaangg;
                @$datainmaanggperha=$datainmaangg[$akun2]/$luasinmaangg;
                @$datainmavariperkg=$datainmavari[$akun2]/$prodinmavari;
                // inti plasma sd bulan ini per
                @$dasdinmaaktuperkg=$dasdinmaaktu[$akun2]/$prodinmaaktu;
                @$dasdinmaaktuperha=$dasdinmaaktu[$akun2]/$luasinmaaktu;
                @$dasdinmaanggperkg=$dasdinmaangg[$akun2]/$prodinmaangg;
                @$dasdinmaanggperha=$dasdinmaangg[$akun2]/$luasinmaangg;
                @$dasdinmavariperkg=$dasdinmavari[$akun2]/$prodinmavari;
                // inti plasma setahun per
                @$dathinmaanggperkg=$dathinmaangg[$akun2]/$prthinmaangg;        
                @$dathinmaanggperha=$dathinmaangg[$akun2]/$luasinmaangg;

                @$dataintiaktuperrb=$dataintiaktu[$akun2]/1000;
                @$dataintianggperrb=$dataintiangg[$akun2]/1000;
                @$dataintivariperrb=$dataintivari[$akun2]/1000;
                @$dasdintiaktuperrb=$dasdintiaktu[$akun2]/1000;
                @$dasdintianggperrb=$dasdintiangg[$akun2]/1000;
                @$dasdintivariperrb=$dasdintivari[$akun2]/1000;
                @$dathintianggperrb=$dathintiangg[$akun2]/1000;

                @$dataplasaktuperrb=$dataplasaktu[$akun2]/1000;
                @$dataplasanggperrb=$dataplasangg[$akun2]/1000;
                @$dataplasvariperrb=$dataplasvari[$akun2]/1000;
                @$dasdplasaktuperrb=$dasdplasaktu[$akun2]/1000;
                @$dasdplasanggperrb=$dasdplasangg[$akun2]/1000;
                @$dasdplasvariperrb=$dasdplasvari[$akun2]/1000;
                @$dathplasanggperrb=$dathplasangg[$akun2]/1000;
                
                @$dataundeaktuperrb=$dataundeaktu[$akun2]/1000;
                @$dasdundeaktuperrb=$dasdundeaktu[$akun2]/1000;

                @$datainmaaktuperrb=$datainmaaktu[$akun2]/1000;
                @$datainmaanggperrb=$datainmaangg[$akun2]/1000;
                @$datainmavariperrb=$datainmavari[$akun2]/1000;
                @$dasdinmaaktuperrb=$dasdinmaaktu[$akun2]/1000;
                @$dasdinmaanggperrb=$dasdinmaangg[$akun2]/1000;
                @$dasdinmavariperrb=$dasdinmavari[$akun2]/1000;
                @$dathinmaanggperrb=$dathinmaangg[$akun2]/1000;
        
                $tab.="
                <td align=right>".number_format($dataintiaktuperrb)."</td>
                <td align=right>".number_format($dataintiaktuperkg)."</td>
                <td align=right>".number_format($dataintiaktuperha)."</td>
                <td align=right>".number_format($dataintianggperrb)."</td>
                <td align=right>".number_format($dataintianggperkg)."</td>
                <td align=right>".number_format($dataintianggperha)."</td>
                <td align=right>".number_format($dataintivariperrb)."</td>
                <td align=right>".number_format($dataintivariperkg)."</td>

                <td align=right>".number_format($dasdintiaktuperrb)."</td>
                <td align=right>".number_format($dasdintiaktuperkg)."</td>
                <td align=right>".number_format($dasdintiaktuperha)."</td>
                <td align=right>".number_format($dasdintianggperrb)."</td>
                <td align=right>".number_format($dasdintianggperkg)."</td>
                <td align=right>".number_format($dasdintianggperha)."</td>
                <td align=right>".number_format($dasdintivariperrb)."</td>
                <td align=right>".number_format($dasdintivariperkg)."</td>

                <td align=right>".number_format($dathintianggperrb)."</td>
                <td align=right>".number_format($dathintianggperkg)."</td>
                <td align=right>".number_format($dathintianggperha)."</td>
                    ";
                $tab.="
                <td align=right>".number_format($dataplasaktuperrb)."</td>
                <td align=right>".number_format($dataplasaktuperkg)."</td>
                <td align=right>".number_format($dataplasaktuperha)."</td>
                <td align=right>".number_format($dataplasanggperrb)."</td>
                <td align=right>".number_format($dataplasanggperkg)."</td>
                <td align=right>".number_format($dataplasanggperha)."</td>
                <td align=right>".number_format($dataplasvariperrb)."</td>
                <td align=right>".number_format($dataplasvariperkg)."</td>

                <td align=right>".number_format($dasdplasaktuperrb)."</td>
                <td align=right>".number_format($dasdplasaktuperkg)."</td>
                <td align=right>".number_format($dasdplasaktuperha)."</td>
                <td align=right>".number_format($dasdplasanggperrb)."</td>
                <td align=right>".number_format($dasdplasanggperkg)."</td>
                <td align=right>".number_format($dasdplasanggperha)."</td>
                <td align=right>".number_format($dasdplasvariperrb)."</td>
                <td align=right>".number_format($dasdplasvariperkg)."</td>

                <td align=right>".number_format($dathplasanggperrb)."</td>
                <td align=right>".number_format($dathplasanggperkg)."</td>
                <td align=right>".number_format($dathplasanggperha)."</td>
                    ";
                $tab.="
                <td align=right>".number_format($dataundeaktuperrb)."</td>
                <td align=right>".number_format($dasdundeaktuperrb)."</td>
                    ";
                $tab.="
                <td align=right>".number_format($datainmaaktuperrb)."</td>
                <td align=right>".number_format($datainmaaktuperkg)."</td>
                <td align=right>".number_format($datainmaaktuperha)."</td>
                <td align=right>".number_format($datainmaanggperrb)."</td>
                <td align=right>".number_format($datainmaanggperkg)."</td>
                <td align=right>".number_format($datainmaanggperha)."</td>
                <td align=right>".number_format($datainmavariperrb)."</td>
                <td align=right>".number_format($datainmavariperkg)."</td>

                <td align=right>".number_format($dasdinmaaktuperrb)."</td>
                <td align=right>".number_format($dasdinmaaktuperkg)."</td>
                <td align=right>".number_format($dasdinmaaktuperha)."</td>
                <td align=right>".number_format($dasdinmaanggperrb)."</td>
                <td align=right>".number_format($dasdinmaanggperkg)."</td>
                <td align=right>".number_format($dasdinmaanggperha)."</td>
                <td align=right>".number_format($dasdinmavariperrb)."</td>
                <td align=right>".number_format($dasdinmavariperkg)."</td>

                <td align=right>".number_format($dathinmaanggperrb)."</td>
                <td align=right>".number_format($dathinmaanggperkg)."</td>
                <td align=right>".number_format($dathinmaanggperha)."</td>
                    ";
                $tab.="</tr>";
            }
        }
    }    
//    // total TB ================================================================
//    $tab.="<tr class=rowtitle>
//    <td align=left colspan=3 ".$bg.">Sub Total</td>";
//    $total=0;
//    if(!empty($tt))foreach($tt as $tata){
//        @$perha=$totat[$tata]/$luas[$tata];
//        $tab.="<td align=right ".$bg.">".number_format($totat[$tata])."</td>
//        <td align=right ".$bg.">".number_format($perha)."</td>";
//        $total+=$totat[$tata];
//    }
//    @$totalperha=$total/$luastotal;
//    $tab.="<td align=right ".$bg.">".number_format($total)."</td>
//    <td align=right ".$bg.">".number_format($totalperha)."</td>
//    </tr>";
    
    // grand total =============================================================
    $tab.="<tr class=rowtitle>
    <td align=left colspan=3 ".$bg.">Grand Total</td>";
    $total=0;
    // inti bulan ini per
    @$totaintiaktuperkg=$totaintiaktu/$prodintiaktu;
    @$totaintiaktuperha=$totaintiaktu/$luasintiaktu;
    @$totaintianggperkg=$totaintiangg/$prodintiangg;
    @$totaintianggperha=$totaintiangg/$luasintiangg;
    @$totaintivariperkg=$totaintivari/$prodintivari;
    // inti sd bulan ini per
    @$tosdintiaktuperkg=$tosdintiaktu/$prodintiaktu;
    @$tosdintiaktuperha=$tosdintiaktu/$luasintiaktu;
    @$tosdintianggperkg=$tosdintiangg/$prodintiangg;
    @$tosdintianggperha=$tosdintiangg/$luasintiangg;
    @$tosdintivariperkg=$tosdintivari/$prodintivari;
    // inti setahun per
    @$tothintianggperkg=$tothintiangg/$prthintiangg;        
    @$tothintianggperha=$tothintiangg/$luasintiangg;

    // plasma bulan ini per
    @$totaplasaktuperkg=$totaplasaktu/$prodplasaktu;
    @$totaplasaktuperha=$totaplasaktu/$luasplasaktu;
    @$totaplasanggperkg=$totaplasangg/$prodplasangg;
    @$totaplasanggperha=$totaplasangg/$luasplasangg;
    @$totaplasvariperkg=$totaplasvari/$prodplasvari;
    // plasma sd bulan ini per
    @$tosdplasaktuperkg=$tosdplasaktu/$prodplasaktu;
    @$tosdplasaktuperha=$tosdplasaktu/$luasplasaktu;
    @$tosdplasanggperkg=$tosdplasangg/$prodplasangg;
    @$tosdplasanggperha=$tosdplasangg/$luasplasangg;
    @$tosdplasvariperkg=$tosdplasvari/$prodplasvari;
    // plasma setahun per
    @$tothplasanggperkg=$tothplasangg/$prthplasangg;        
    @$tothplasanggperha=$tothplasangg/$luasplasangg;

    // inti plasma bulan ini per
    @$totainmaaktuperkg=$totainmaaktu/$prodinmaaktu;
    @$totainmaaktuperha=$totainmaaktu/$luasinmaaktu;
    @$totainmaanggperkg=$totainmaangg/$prodinmaangg;
    @$totainmaanggperha=$totainmaangg/$luasinmaangg;
    @$totainmavariperkg=$totainmavari/$prodinmavari;
    // inti plasma sd bulan ini per
    @$tosdinmaaktuperkg=$tosdinmaaktu/$prodinmaaktu;
    @$tosdinmaaktuperha=$tosdinmaaktu/$luasinmaaktu;
    @$tosdinmaanggperkg=$tosdinmaangg/$prodinmaangg;
    @$tosdinmaanggperha=$tosdinmaangg/$luasinmaangg;
    @$tosdinmavariperkg=$tosdinmavari/$prodinmavari;
    // inti plasma setahun per
    @$tothinmaanggperkg=$tothinmaangg/$prthinmaangg;        
    @$tothinmaanggperha=$tothinmaangg/$luasinmaangg;

    $tab.="
    <td align=right ".$bg.">".number_format($totaintiaktu/1000)."</td>
    <td align=right ".$bg.">".number_format($totaintiaktuperkg)."</td>
    <td align=right ".$bg.">".number_format($totaintiaktuperha)."</td>
    <td align=right ".$bg.">".number_format($totaintiangg/1000)."</td>
    <td align=right ".$bg.">".number_format($totaintianggperkg)."</td>
    <td align=right ".$bg.">".number_format($totaintianggperha)."</td>
    <td align=right ".$bg.">".number_format($totaintivari/1000)."</td>
    <td align=right ".$bg.">".number_format($totaintivariperkg)."</td>

    <td align=right ".$bg.">".number_format($tosdintiaktu/1000)."</td>
    <td align=right ".$bg.">".number_format($tosdintiaktuperkg)."</td>
    <td align=right ".$bg.">".number_format($tosdintiaktuperha)."</td>
    <td align=right ".$bg.">".number_format($tosdintiangg/1000)."</td>
    <td align=right ".$bg.">".number_format($tosdintianggperkg)."</td>
    <td align=right ".$bg.">".number_format($tosdintianggperha)."</td>
    <td align=right ".$bg.">".number_format($tosdintivari/1000)."</td>
    <td align=right ".$bg.">".number_format($tosdintivariperkg)."</td>

    <td align=right ".$bg.">".number_format($tothintiangg/1000)."</td>
    <td align=right ".$bg.">".number_format($tothintianggperkg)."</td>
    <td align=right ".$bg.">".number_format($tothintianggperha)."</td>
        ";
    $tab.="
    <td align=right ".$bg.">".number_format($totaplasaktu/1000)."</td>
    <td align=right ".$bg.">".number_format($totaplasaktuperkg)."</td>
    <td align=right ".$bg.">".number_format($totaplasaktuperha)."</td>
    <td align=right ".$bg.">".number_format($totaplasangg/1000)."</td>
    <td align=right ".$bg.">".number_format($totaplasanggperkg)."</td>
    <td align=right ".$bg.">".number_format($totaplasanggperha)."</td>
    <td align=right ".$bg.">".number_format($totaplasvari/1000)."</td>
    <td align=right ".$bg.">".number_format($totaplasvariperkg)."</td>

    <td align=right ".$bg.">".number_format($tosdplasaktu/1000)."</td>
    <td align=right ".$bg.">".number_format($tosdplasaktuperkg)."</td>
    <td align=right ".$bg.">".number_format($tosdplasaktuperha)."</td>
    <td align=right ".$bg.">".number_format($tosdplasangg/1000)."</td>
    <td align=right ".$bg.">".number_format($tosdplasanggperkg)."</td>
    <td align=right ".$bg.">".number_format($tosdplasanggperha)."</td>
    <td align=right ".$bg.">".number_format($tosdplasvari/1000)."</td>
    <td align=right ".$bg.">".number_format($tosdplasvariperkg)."</td>

    <td align=right ".$bg.">".number_format($tothplasangg/1000)."</td>
    <td align=right ".$bg.">".number_format($tothplasanggperkg)."</td>
    <td align=right ".$bg.">".number_format($tothplasanggperha)."</td>
        ";
    $tab.="
    <td align=right ".$bg.">".number_format($totaundeaktu/1000)."</td>
    <td align=right ".$bg.">".number_format($tosdundeaktu/1000)."</td>
        ";
    $tab.="
    <td align=right ".$bg.">".number_format($totainmaaktu/1000)."</td>
    <td align=right ".$bg.">".number_format($totainmaaktuperkg)."</td>
    <td align=right ".$bg.">".number_format($totainmaaktuperha)."</td>
    <td align=right ".$bg.">".number_format($totainmaangg/1000)."</td>
    <td align=right ".$bg.">".number_format($totainmaanggperkg)."</td>
    <td align=right ".$bg.">".number_format($totainmaanggperha)."</td>
    <td align=right ".$bg.">".number_format($totainmavari/1000)."</td>
    <td align=right ".$bg.">".number_format($totainmavariperkg)."</td>

    <td align=right ".$bg.">".number_format($tosdinmaaktu/1000)."</td>
    <td align=right ".$bg.">".number_format($tosdinmaaktuperkg)."</td>
    <td align=right ".$bg.">".number_format($tosdinmaaktuperha)."</td>
    <td align=right ".$bg.">".number_format($tosdinmaangg/1000)."</td>
    <td align=right ".$bg.">".number_format($tosdinmaanggperkg)."</td>
    <td align=right ".$bg.">".number_format($tosdinmaanggperha)."</td>
    <td align=right ".$bg.">".number_format($tosdinmavari/1000)."</td>
    <td align=right ".$bg.">".number_format($tosdinmavariperkg)."</td>

    <td align=right ".$bg.">".number_format($tothinmaangg/1000)."</td>
    <td align=right ".$bg.">".number_format($tothinmaanggperkg)."</td>
    <td align=right ".$bg.">".number_format($tothinmaanggperha)."</td>
        ";
    $tab.="</tr>";    
    $tab.="</tbody></table>";    
    switch($proses)
    {
        case'preview': 
        echo $tab;
        break;    
        case'excel':
        $tab.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
        $dte=date("YmdHis");
        $nop_="mr_biayaTbmDetail_".$pt.$unit.$afdeling.$periode; 
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
