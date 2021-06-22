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

// dapatkan tahun bulan
$qwe=explode('-',$periode); $tahun=$qwe[0]; $bulan=$qwe[1];

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
//    $listpt['']='';
//    $namapt['']='undefined';
    // list pt
    $str="SELECT namaorganisasi, kodeorganisasi FROM ".$dbname.".organisasi
        WHERE tipe='PT' order by namaorganisasi desc";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $listpt[$res['kodeorganisasi']]=$res['kodeorganisasi'];
        $namapt[$res['kodeorganisasi']]=$res['namaorganisasi'];
    }    
    
    // luas aktual
    $str="SELECT a.kodeorg, (luasareaproduktif) as luas, b.induk FROM ".$dbname.".setup_blok a
        LEFT JOIN ".$dbname.".organisasi b ON substr(a.kodeorg,1,4)=b.kodeorganisasi
        WHERE length(b.kodeorganisasi)=4 and a.statusblok='TM' 
        ";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $luaspt[$res['induk']]+=$res['luas'];
    }        
    
    // produksi aktual bulan ini
    $str="SELECT a.blok, (kgwbtanpabrondolan) as kg, b.induk FROM ".$dbname.".kebun_spb_vs_rencana_blok_vw a
        LEFT JOIN ".$dbname.".organisasi b ON substr(a.blok,1,4)=b.kodeorganisasi
        WHERE b.tipe='KEBUN' and a.periode = '".$periode."' and length(b.kodeorganisasi)=4";    
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $prodpt[$res['induk']]+=$res['kg'];
    }        

    // produksi aktual sd bulan ini
    $str="SELECT a.blok, (kgwbtanpabrondolan) as kg, b.induk FROM ".$dbname.".kebun_spb_vs_rencana_blok_vw a
        LEFT JOIN ".$dbname.".organisasi b ON substr(a.blok,1,4)=b.kodeorganisasi
        WHERE b.tipe='KEBUN' and a.periode between '".$tahun."-01' and '".$periode."' and length(b.kodeorganisasi)=4";    
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $prsdpt[$res['induk']]+=$res['kg'];
    }          
    
    // noakun 61
    $str="SELECT noakun, namaakun FROM ".$dbname.".keu_5akun
        WHERE length(noakun)=5 and noakun between '61101' and '61102' order by noakun";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $akun61[$res['noakun']]=$res['noakun'];
        $namaakun[$res['noakun']]=$res['namaakun'];
    }

    // noakun 62
    $str="SELECT noakun, namaakun FROM ".$dbname.".keu_5akun
        WHERE length(noakun)=5 and noakun between '62101' and '62102' order by noakun";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $akun62[$res['noakun']]=$res['noakun'];
        $namaakun[$res['noakun']]=$res['namaakun'];
    }
    
    // noakun 62
    $str="SELECT noakun, namaakun FROM ".$dbname.".keu_5akun
        WHERE length(noakun)=5 and noakun between '62104' and '62111' order by noakun";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $akun62[$res['noakun']]=$res['noakun'];
        $namaakun[$res['noakun']]=$res['namaakun'];
    }
    
    // noakun 623
    $str="SELECT noakun, namaakun FROM ".$dbname.".keu_5akun
        WHERE length(noakun)=5 and noakun between '62103' and '62103' order by noakun";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $akun623[$res['noakun']]=$res['noakun'];
        $namaakun[$res['noakun']]=$res['namaakun'];
    }    
    
    // data aktual bulan ini (length=5)
    $str="SELECT a.kodeblok, substr(a.noakun,1,5) as noakun, a.debet, a.kredit, b.induk FROM ".$dbname.".keu_jurnalsum_blok_vw a
        LEFT JOIN ".$dbname.".organisasi b ON substr(a.kodeblok,1,4)=b.kodeorganisasi
        WHERE b.tipe='KEBUN' and a.periode = '".$periode."' and a.noakun between '6110000' and '7199999'";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $bipt[$res['noakun']][$res['induk']]+=($res['debet']-$res['kredit']);
        if(substr($res['noakun'],0,3)=='711')$bipt7[711][$res['induk']]+=($res['debet']-$res['kredit']);
        if(substr($res['noakun'],0,3)=='712')$bipt7[712][$res['induk']]+=($res['debet']-$res['kredit']);
        if(substr($res['noakun'],0,3)=='713')$bipt7[713][$res['induk']]+=($res['debet']-$res['kredit']);
        if(substr($res['noakun'],0,3)=='714')$bipt7[714][$res['induk']]+=($res['debet']-$res['kredit']);
        if(substr($res['noakun'],0,5)=='71501')$bipt7[71501][$res['induk']]+=($res['debet']-$res['kredit']);
        if(substr($res['noakun'],0,3)=='716')$bipt7[716][$res['induk']]+=($res['debet']-$res['kredit']);
        if(substr($res['noakun'],0,5)=='71502')$bipt9[71502][$res['induk']]+=($res['debet']-$res['kredit']);
        if(substr($res['noakun'],0,5)=='71999')$bipt9[71999][$res['induk']]+=($res['debet']-$res['kredit']);
    }
     
    // data aktual sd bulan ini (length=5)
    $str="SELECT a.kodeblok, substr(a.noakun,1,5) as noakun, a.debet, a.kredit, b.induk FROM ".$dbname.".keu_jurnalsum_blok_vw a
        LEFT JOIN ".$dbname.".organisasi b ON substr(a.kodeblok,1,4)=b.kodeorganisasi
        WHERE b.tipe='KEBUN' and a.periode between '".$tahun."-01' and '".$periode."' and a.noakun between '6110000' and '7199999'";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $sbipt[$res['noakun']][$res['induk']]+=($res['debet']-$res['kredit']);
        if(substr($res['noakun'],0,3)=='711')$sbipt7[711][$res['induk']]+=($res['debet']-$res['kredit']);
        if(substr($res['noakun'],0,3)=='712')$sbipt7[712][$res['induk']]+=($res['debet']-$res['kredit']);
        if(substr($res['noakun'],0,3)=='713')$sbipt7[713][$res['induk']]+=($res['debet']-$res['kredit']);
        if(substr($res['noakun'],0,3)=='714')$sbipt7[714][$res['induk']]+=($res['debet']-$res['kredit']);
        if(substr($res['noakun'],0,5)=='71501')$sbipt7[71501][$res['induk']]+=($res['debet']-$res['kredit']);
        if(substr($res['noakun'],0,3)=='716')$sbipt7[716][$res['induk']]+=($res['debet']-$res['kredit']);
        if(substr($res['noakun'],0,5)=='71502')$sbipt9[71502][$res['induk']]+=($res['debet']-$res['kredit']);
        if(substr($res['noakun'],0,5)=='71999')$sbipt9[71999][$res['induk']]+=($res['debet']-$res['kredit']);
    }
    
    $akun7[711]=711;
    $akun7[712]=712;
    $akun7[713]=713;
    $akun7[714]=714;
    $akun7[71501]=71501;
    $akun7[716]=716;
    
    $akun9[71502]=71502;
    $akun9[7199999]=7199999;

    $namaakun[711]='Karyawan';
    $namaakun[712]='Operasional Karyawan';
    $namaakun[713]='Operasional Mess dan Kantor';
    $namaakun[714]='Pemeliharaan';
    $namaakun[71501]='Pembelian Barang Inventaris';
    $namaakun[716]='Biaya Lainnya';
    
    $namaakun[71502]='Penyusutan';
    $namaakun[7199999]='Biaya Overhead Alokasi';

    if($proses=='excel')
    {
        $bg=" bgcolor=#DEDEDE";
        $brdr=1;
//        if($pt=='')$tab.='PT (seluruhnya)'; else $tab.='PT '.$pt;
//        if($unit!='')$tab.=', UNIT '.$unit;
//        if($afdeling!='')$tab.=', Afdeling '.$afdeling;
        $tab.='BIAYA PRODUKSI TANDAN BUAH SEGAR PER PT<br>01-'.$tahun.'S/D '.$bulan.'-'.$tahun;
    }
    else
    { 
        echo 'BIAYA PRODUKSI TANDAN BUAH SEGAR PER PT<br>01-'.$tahun.' S/D '.$bulan.'-'.$tahun;
        $bg="";
        $brdr=0;
    } 
    
    // header ==================================================================
    $tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable style='width:100%'>
        <thead class=rowheader>
        <tr>
            <td align=center colspan=2 ".$bg.">Keterangan</td>";
            if(!empty($listpt))foreach($listpt as $list){
                $tab.="<td align=center colspan=6 ".$bg.">".$namapt[$list]."</td>";
            }
        $tab.="</tr>";
        $tab.="<tr>
            <td align=center colspan=2 ".$bg.">Luas TM (ha)</td>";
            if(!empty($listpt))foreach($listpt as $list){
                $tab.="<td align=center colspan=6 ".$bg.">".number_format($luaspt[$list],2)."</td>";
            }
        $tab.="</tr>";
        $tab.="<tr>
            <td align=center ".$bg.">Produksi BI (ton)</td>
            <td align=center ".$bg.">Produksi SBI (ton)</td>";
            if(!empty($listpt))foreach($listpt as $list){
                $tab.="<td align=center colspan=3 ".$bg.">".number_format($prodpt[$list]/1000,2)."</td>";
                $tab.="<td align=center colspan=3 ".$bg.">".number_format($prsdpt[$list]/1000,2)."</td>";
            }
        $tab.="</tr>";
        $tab.="<tr>
            <td align=center colspan=2 ".$bg."></td>";
            if(!empty($listpt))foreach($listpt as $list){
                $tab.="<td align=center ".$bg.">BI Rp. (000)</td>";
                $tab.="<td align=center ".$bg.">SBI Rp. (000)</td>";
                $tab.="<td align=center ".$bg.">BI Rp/Ha</td>";
                $tab.="<td align=center ".$bg.">BI Rp/Kg</td>";
                $tab.="<td align=center ".$bg.">SBI Rp/Ha</td>";
                $tab.="<td align=center ".$bg.">SBI Rp/Kg</td>";
            }
        $tab.="</tr>";
        $tab.="</thead><tbody>
    ";    
            
    // total akun 61
    if(!empty($akun61))foreach($akun61 as $akun){
        if(!empty($listpt))foreach($listpt as $list){
            $bipt61[$list]+=$bipt[$akun][$list];
            $sbipt61[$list]+=$sbipt[$akun][$list];
            $bipt6t[$list]+=$bipt[$akun][$list];
            $sbipt6t[$list]+=$sbipt[$akun][$list];
            $bipt7t[$list]+=$bipt[$akun][$list];
            $sbipt7t[$list]+=$sbipt[$akun][$list];
            $bipt8t[$list]+=$bipt[$akun][$list];
            $sbipt8t[$list]+=$sbipt[$akun][$list];
        }
    }
        
    // total akun 62
    if(!empty($akun62))foreach($akun62 as $akun){
        if(!empty($listpt))foreach($listpt as $list){
            $bipt62[$list]+=$bipt[$akun][$list];
            $sbipt62[$list]+=$sbipt[$akun][$list];
            $bipt6t[$list]+=$bipt[$akun][$list];
            $sbipt6t[$list]+=$sbipt[$akun][$list];
            $bipt7t[$list]+=$bipt[$akun][$list];
            $sbipt7t[$list]+=$sbipt[$akun][$list];
            $bipt8t[$list]+=$bipt[$akun][$list];
            $sbipt8t[$list]+=$sbipt[$akun][$list];
        }
    }
        
    // total akun 623
    if(!empty($akun623))foreach($akun623 as $akun){
        if(!empty($listpt))foreach($listpt as $list){
            $bipt623[$list]+=$bipt[$akun][$list];
            $sbipt623[$list]+=$sbipt[$akun][$list];
            $bipt6t[$list]+=$bipt[$akun][$list];
            $sbipt6t[$list]+=$sbipt[$akun][$list];
            $bipt7t[$list]+=$bipt[$akun][$list];
            $sbipt7t[$list]+=$sbipt[$akun][$list];
            $bipt8t[$list]+=$bipt[$akun][$list];
            $sbipt8t[$list]+=$sbipt[$akun][$list];
        }
    }
    
    // total akun 71
    if(!empty($akun7))foreach($akun7 as $akun){
        if(!empty($listpt))foreach($listpt as $list){
            $bipt71[$list]+=$bipt7[$akun][$list];
            $sbipt71[$list]+=$sbipt7[$akun][$list];
            $bipt7t[$list]+=$bipt7[$akun][$list];
            $sbipt7t[$list]+=$sbipt7[$akun][$list];
            $bipt8t[$list]+=$bipt7[$akun][$list];
            $sbipt8t[$list]+=$sbipt7[$akun][$list];
        }
    }

    // total akun 91
    if(!empty($akun9))foreach($akun9 as $akun){
        if(!empty($listpt))foreach($listpt as $list){
            $bipt91[$list]+=$bipt9[$akun][$list];
            $sbipt91[$list]+=$sbipt9[$akun][$list];
            $bipt8t[$list]+=$bipt[$akun][$list];
            $sbipt8t[$list]+=$sbipt[$akun][$list];
        }
    }

    // tampil total akun 61 62 623
    $tab.="<tr class=rowtitle>
    <td align=left colspan=2 ".$bg.">Biaya Langsung</td>";
    if(!empty($listpt))foreach($listpt as $list){
        @$bipt6tperluas=$bipt6t[$list]/$luaspt[$list];
        @$bipt6tperprod=$bipt6t[$list]/$prodpt[$list];
        @$sbipt6tperluas=$sbipt6t[$list]/$luaspt[$list];
        @$sbipt6tperprod=$sbipt6t[$list]/$prsdpt[$list];
        $tab.="<td align=right ".$bg.">".number_format($bipt6t[$list]/1000)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($sbipt6t[$list]/1000)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($bipt6tperluas)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($bipt6tperprod)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($sbipt6tperluas)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($sbipt6tperprod)."</td>";
    }
    $tab.="</tr>";         
    
    // tampil total akun 61
    $tab.="<tr class=rowtitle>
    <td align=right ".$bg.">611XXXX</td>
    <td align=left ".$bg.">Panen dan Pengumpulan</td>";
    if(!empty($listpt))foreach($listpt as $list){
        @$bipt61perluas=$bipt61[$list]/$luaspt[$list];
        @$bipt61perprod=$bipt61[$list]/$prodpt[$list];
        @$sbipt61perluas=$sbipt61[$list]/$luaspt[$list];
        @$sbipt61perprod=$sbipt61[$list]/$prsdpt[$list];
        $tab.="<td align=right ".$bg.">".number_format($bipt61[$list]/1000)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($sbipt61[$list]/1000)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($bipt61perluas)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($bipt61perprod)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($sbipt61perluas)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($sbipt61perprod)."</td>";
    }
    $tab.="</tr>";        

    // tampil akun 61
    if(!empty($akun61))foreach($akun61 as $akun){
        $tab.="<tr class=rowcontent>
        <td align=right>".$akun."XX</td>
        <td align=left>".$namaakun[$akun]."</td>";
        if(!empty($listpt))foreach($listpt as $list){
            @$biptperluas=$bipt[$akun][$list]/$luaspt[$list];
            @$biptperprod=$bipt[$akun][$list]/$prodpt[$list];
            @$sbiptperluas=$sbipt[$akun][$list]/$luaspt[$list];
            @$sbiptperprod=$sbipt[$akun][$list]/$prsdpt[$list];
            $tab.="<td align=right>".number_format($bipt[$akun][$list]/1000)."</td>";
            $tab.="<td align=right>".number_format($sbipt[$akun][$list]/1000)."</td>";
            $tab.="<td align=right>".number_format($biptperluas)."</td>";
            $tab.="<td align=right>".number_format($biptperprod)."</td>";
            $tab.="<td align=right>".number_format($sbiptperluas)."</td>";
            $tab.="<td align=right>".number_format($sbiptperprod)."</td>";
        }
        $tab.="</tr>";        
    }
    
    // tampil total akun 62
    $tab.="<tr class=rowtitle>
    <td align=right ".$bg.">612XXXX</td>
    <td align=left ".$bg.">Pemeliharaan TM</td>";
    if(!empty($listpt))foreach($listpt as $list){
        @$bipt62perluas=$bipt62[$list]/$luaspt[$list];
        @$bipt62perprod=$bipt62[$list]/$prodpt[$list];
        @$sbipt62perluas=$sbipt62[$list]/$luaspt[$list];
        @$sbipt62perprod=$sbipt62[$list]/$prsdpt[$list];
        $tab.="<td align=right ".$bg.">".number_format($bipt62[$list]/1000)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($sbipt62[$list]/1000)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($bipt62perluas)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($bipt62perprod)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($sbipt62perluas)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($sbipt62perprod)."</td>";
    }
    $tab.="</tr>";        
    
    // tampil akun 62
    if(!empty($akun62))foreach($akun62 as $akun){
        $tab.="<tr class=rowcontent>
        <td align=right>".$akun."XX</td>
        <td align=left>".$namaakun[$akun]."</td>";
        if(!empty($listpt))foreach($listpt as $list){
            @$biptperluas=$bipt[$akun][$list]/$luaspt[$list];
            @$biptperprod=$bipt[$akun][$list]/$prodpt[$list];
            @$sbiptperluas=$sbipt[$akun][$list]/$luaspt[$list];
            @$sbiptperprod=$sbipt[$akun][$list]/$prsdpt[$list];
            $tab.="<td align=right>".number_format($bipt[$akun][$list]/1000)."</td>";
            $tab.="<td align=right>".number_format($sbipt[$akun][$list]/1000)."</td>";
            $tab.="<td align=right>".number_format($biptperluas)."</td>";
            $tab.="<td align=right>".number_format($biptperprod)."</td>";
            $tab.="<td align=right>".number_format($sbiptperluas)."</td>";
            $tab.="<td align=right>".number_format($sbiptperprod)."</td>";
        }
        $tab.="</tr>";        
    }
    
    // tampil total 623
    $tab.="<tr class=rowtitle>
    <td align=right ".$bg.">61203XX</td>
    <td align=left ".$bg.">Pemupukan TM</td>";
    if(!empty($listpt))foreach($listpt as $list){
        @$bipt623perluas=$bipt62[$list]/$luaspt[$list];
        @$bipt623perprod=$bipt62[$list]/$prodpt[$list];
        @$sbipt623perluas=$sbipt62[$list]/$luaspt[$list];
        @$sbipt623perprod=$sbipt62[$list]/$prsdpt[$list];
        $tab.="<td align=right ".$bg.">".number_format($bipt623[$list]/1000)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($sbipt623[$list]/1000)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($bipt623perluas)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($bipt623perprod)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($sbipt623perluas)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($sbipt623perprod)."</td>";
    }
    $tab.="</tr>";     
    
    // tampil akun 623
    if(!empty($akun623))foreach($akun623 as $akun){
        $tab.="<tr class=rowcontent>
        <td align=right>".$akun."XX</td>
        <td align=left>".$namaakun[$akun]."</td>";
        if(!empty($listpt))foreach($listpt as $list){
            @$biptperluas=$bipt[$akun][$list]/$luaspt[$list];
            @$biptperprod=$bipt[$akun][$list]/$prodpt[$list];
            @$sbiptperluas=$sbipt[$akun][$list]/$luaspt[$list];
            @$sbiptperprod=$sbipt[$akun][$list]/$prsdpt[$list];
            $tab.="<td align=right>".number_format($bipt[$akun][$list]/1000)."</td>";
            $tab.="<td align=right>".number_format($sbipt[$akun][$list]/1000)."</td>";
            $tab.="<td align=right>".number_format($biptperluas)."</td>";
            $tab.="<td align=right>".number_format($biptperprod)."</td>";
            $tab.="<td align=right>".number_format($sbiptperluas)."</td>";
            $tab.="<td align=right>".number_format($sbiptperprod)."</td>";
        }
        $tab.="</tr>";        
    }
    
    // tampil total 7
    $tab.="<tr class=rowtitle>
    <td align=right ".$bg.">7XXXXXX</td>
    <td align=left ".$bg.">Biaya Umum</td>";
    if(!empty($listpt))foreach($listpt as $list){
        @$bipt71perluas=$bipt71[$list]/$luaspt[$list];
        @$bipt71perprod=$bipt71[$list]/$prodpt[$list];
        @$sbipt71perluas=$sbipt71[$list]/$luaspt[$list];
        @$sbipt71perprod=$sbipt71[$list]/$prsdpt[$list];
        $tab.="<td align=right ".$bg.">".number_format($bipt71[$list]/1000)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($sbipt71[$list]/1000)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($bipt71perluas)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($bipt71perprod)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($sbipt71perluas)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($sbipt71perprod)."</td>";
    }
    $tab.="</tr>";     
    
    // tampil akun 7
    if(!empty($akun7))foreach($akun7 as $akun){
        $tab.="<tr class=rowcontent>";
        if($akun=='71501')$tab.="<td align=right>".$akun."XX</td>"; else $tab.="<td align=right>".$akun."XXXX</td>";
        $tab.="<td align=left>".$namaakun[$akun]."</td>";
        if(!empty($listpt))foreach($listpt as $list){
            @$biptperluas=$bipt7[$akun][$list]/$luaspt[$list];
            @$biptperprod=$bipt7[$akun][$list]/$prodpt[$list];
            @$sbiptperluas=$sbipt7[$akun][$list]/$luaspt[$list];
            @$sbiptperprod=$sbipt7[$akun][$list]/$prsdpt[$list];
            $tab.="<td align=right>".number_format($bipt7[$akun][$list]/1000)."</td>";
            $tab.="<td align=right>".number_format($sbipt7[$akun][$list]/1000)."</td>";
            $tab.="<td align=right>".number_format($biptperluas)."</td>";
            $tab.="<td align=right>".number_format($biptperprod)."</td>";
            $tab.="<td align=right>".number_format($sbiptperluas)."</td>";
            $tab.="<td align=right>".number_format($sbiptperprod)."</td>";
        }
        $tab.="</tr>";        
    }
    
    // tampil total akun 61 62 623 7
    $tab.="<tr class=rowtitle>
    <td align=left colspan=2 ".$bg.">Total Sebelum Depresiasi dan Alokasi</td>";
    if(!empty($listpt))foreach($listpt as $list){
        @$bipt7tperluas=$bipt7t[$list]/$luaspt[$list];
        @$bipt7tperprod=$bipt7t[$list]/$prodpt[$list];
        @$sbipt7tperluas=$sbipt7t[$list]/$luaspt[$list];
        @$sbipt7tperprod=$sbipt7t[$list]/$prsdpt[$list];
        $tab.="<td align=right ".$bg.">".number_format($bipt7t[$list]/1000)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($sbipt7t[$list]/1000)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($bipt7tperluas)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($bipt7tperprod)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($sbipt7tperluas)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($sbipt7tperprod)."</td>";
    }
    $tab.="</tr>";         
    
//    // tampil total 9
//    $tab.="<tr class=rowtitle>
//    <td align=left colspan=2 ".$bg.">Depresiasi dan Alokasi</td>";
//    if(!empty($listpt))foreach($listpt as $list){
//        $tab.="<td align=right>".number_format($bipt91[$list]/1000)."</td>";
//        $tab.="<td align=right>".number_format($sbipt91[$list]/1000)."</td>";
//        $tab.="<td align=right>".number_format($bipt91[$list]/$luaspt[$list])."</td>";
//        $tab.="<td align=right>".number_format($bipt91[$list]/$prodpt[$list])."</td>";
//        $tab.="<td align=right>".number_format($sbipt91[$list]/$luaspt[$list])."</td>";
//        $tab.="<td align=right>".number_format($sbipt91[$list]/$prodpt[$list])."</td>";
//    }
//    $tab.="</tr>";     
    
    // tampil akun 9
    if(!empty($akun9))foreach($akun9 as $akun){
        $tab.="<tr class=rowcontent>";
        if($akun=='71502')$tab.="<td align=right>".$akun."XX</td>"; else $tab.="<td align=right>".$akun."</td>";
        $tab.="<td align=left>".$namaakun[$akun]."</td>";
        if(!empty($listpt))foreach($listpt as $list){
            @$biptperluas=$bipt9[$akun][$list]/$luaspt[$list];
            @$biptperprod=$bipt9[$akun][$list]/$prodpt[$list];
            @$sbiptperluas=$sbipt9[$akun][$list]/$luaspt[$list];
            @$sbiptperprod=$sbipt9[$akun][$list]/$prsdpt[$list];
            $tab.="<td align=right>".number_format($bipt9[$akun][$list]/1000)."</td>";
            $tab.="<td align=right>".number_format($sbipt9[$akun][$list]/1000)."</td>";
            $tab.="<td align=right>".number_format($biptperluas)."</td>";
            $tab.="<td align=right>".number_format($biptperprod)."</td>";
            $tab.="<td align=right>".number_format($sbiptperluas)."</td>";
            $tab.="<td align=right>".number_format($sbiptperprod)."</td>";
        }
        $tab.="</tr>";        
    }
    
    // tampil total akun 61 62 623 7 8
    $tab.="<tr class=rowtitle>
    <td align=left colspan=2 ".$bg.">Biaya Produksi TBS</td>";
    if(!empty($listpt))foreach($listpt as $list){
        @$bipt8tperluas=$bipt8t[$list]/$luaspt[$list];
        @$bipt8tperprod=$bipt8t[$list]/$prodpt[$list];
        @$sbipt8tperluas=$sbipt8t[$list]/$luaspt[$list];
        @$sbipt8tperprod=$sbipt8t[$list]/$prsdpt[$list];
        $tab.="<td align=right ".$bg.">".number_format($bipt8t[$list]/1000)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($sbipt8t[$list]/1000)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($bipt8tperluas)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($bipt8tperprod)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($sbipt8tperluas)."</td>";
        $tab.="<td align=right ".$bg.">".number_format($sbipt8tperprod)."</td>";
    }
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
        $nop_="mr_biayaProduksiTbsPT_".$periode; 
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
