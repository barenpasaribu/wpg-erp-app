<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');


$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];

$optNmorg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$_POST['kdPt']==''?$kdPt=$_GET['kdPt']:$kdPt=$_POST['kdPt'];
$_POST['kdUnit']==''?$kdUnit=$_GET['kdUnit']:$kdUnit=$_POST['kdUnit'];
$_POST['periodeDt']==''?$periodeDt=$_GET['periodeDt']:$periodeDt=$_POST['periodeDt'];
$thnbgt=explode("-",$periodeDt);


if($periodeDt=='')
{
    exit("Error:Periode Tidak Boleh Kosong");
}
$nmpt=$unit=$_SESSION['lang']['all'];

if($kdUnit!='')
{
    $whreblok="kodeorg like '".$kdUnit."%'";
    $whre="kodeblok like '".$kdUnit."%'";
    $whrakt="and blok like '".$kdUnit."%'";
    $unit=$optNmorg[$kdUnit];
}
elseif($kdPt!='')
{
    $nmpt=$optNmorg[$kdPt];
    $whreblok=" substr(kodeorg,1,4) in (";
    $whre=" substr(kodeblok,1,4) in (";
    $whrakt=" and substr(blok,1,4) in (";
   
    $sKod="select distinct kodeorganisasi from ".$dbname.".organisasi where induk ='".$kdPt."' and tipe='KEBUN'";
    $qKod=mysql_query($sKod) or die(mysql_error($conn));
    $rTot=mysql_num_rows($qKod);
    while($rKod=mysql_fetch_assoc($qKod))
    {
        $nord+=1;
        $whreblok.="'".$rKod['kodeorganisasi']."'";
        $whre.="'".$rKod['kodeorganisasi']."'";
        $whrakt.="'".$rKod['kodeorganisasi']."'";
        if($nord<$rTot){
            $whreblok.=",";
            $whre.=",";
            $whrakt.=",";
        }
    }
    $whreblok.=")";
    $whre.=")";
    $whrakt.=")";
}
elseif($kdPt=='')
{
   $whreblok=" substr(kodeorg,1,4) in (";
    $whre=" substr(kodeblok,1,4) in (";
    $whrakt=" and substr(blok,1,4) in (";
   
    $sKod="select distinct kodeorganisasi from ".$dbname.".organisasi  where tipe='KEBUN'";
    $qKod=mysql_query($sKod) or die(mysql_error($conn));
    $rTot=mysql_num_rows($qKod);
    while($rKod=mysql_fetch_assoc($qKod))
    {
        $nord+=1;
        $whreblok.="'".$rKod['kodeorganisasi']."'";
        $whre.="'".$rKod['kodeorganisasi']."'";
        $whrakt.="'".$rKod['kodeorganisasi']."'";
        if($nord<$rTot){
            $whreblok.=",";
            $whre.=",";
            $whrakt.=",";
        }
    }
    $whreblok.=")";
    $whre.=")";
    $whrakt.=")";
}

if($proses!='getData')
{
        
        ////merah luas
        $sLuasMrh="select sum(luasareaproduktif) as luas,substr(kodeorg,1,6) as afd,tahuntanam 
                   from ".$dbname.".setup_blok where ".$whreblok." and statusblok='TM' group by substr(kodeorg,1,6),tahuntanam order by substr(kodeorg,1,6)";
        // exit("Error".$sLuasMrh);
        $qLuasMrh=mysql_query($sLuasMrh) or die(mysql_error($conn));
        while($rLuasMrh=mysql_fetch_assoc($qLuasMrh))
        {
            $sPt="select distinct induk from ".$dbname.".organisasi where kodeorganisasi='".substr($rLuasMrh['afd'],0,4)."'";
           // exit("error:".$sPt);
            $qPt=mysql_query($sPt) or die(mysql_error($conn));
            $rPt=mysql_fetch_assoc($qPt);
            $dtPt[$rLuasMrh['tahuntanam'].$rLuasMrh['afd']]=$rPt['induk'];
            $dtLuas[$rLuasMrh['tahuntanam'].$rLuasMrh['afd']]=$rLuasMrh['luas'];
            $dtAfdeling[$rLuasMrh['afd']]=$rLuasMrh['afd'];
            $dtThnTnm[$rLuasMrh['tahuntanam'].$rLuasMrh['afd']]=$rLuasMrh['tahuntanam'];
            $amThnTnm[$rLuasMrh['tahuntanam']]=$rLuasMrh['tahuntanam'];
            $afdDt[$rLuasMrh['tahuntanam'].$rLuasMrh['afd']]=$rLuasMrh['afd'];
            
        }
        ////biru telur asin (budget)Luas Ambil dari bgt_blok ambil sum(hathnini+nonproduktif)
        $sLuasBgt="select sum(hathnini) as luasbgt,thntnm,substr(kodeblok,1,6) as afd from ".$dbname.".bgt_blok where 
                   ".$whre." and tahunbudget='".$thnbgt[0]."'  and statusblok='TM'  group by substr(kodeblok,1,6),thntnm order by substr(kodeblok,1,6)";
        // exit("Error".$sLuasBgt);
        $qLuasBgt=mysql_query($sLuasBgt) or die(mysql_error($conn));
        while($rLuasBgt=mysql_fetch_assoc($qLuasBgt))
        {
             $sPt="select distinct induk from ".$dbname.".organisasi where kodeorganisasi='".substr($rLuasBgt['afd'],0,4)."'";
           // exit("error:".$sPt);
            $qPt=mysql_query($sPt) or die(mysql_error($conn));
            $rPt=mysql_fetch_assoc($qPt);
            if($rLuasBgt['luasbgt']!=0)
            {
                $dtLuasBgt[$rLuasBgt['thntnm'].$rLuasBgt['afd']]=$rLuasBgt['luasbgt'];
                $dtAfdeling[$rLuasBgt['afd']]=$rLuasBgt['afd'];
                $dtThnTnm[$rLuasBgt['thntnm'].$rLuasBgt['afd']]=$rLuasBgt['thntnm'];
                $amThnTnm[$rLuasBgt['thntnm']]=$rLuasBgt['thntnm'];
                $afdDt[$rLuasBgt['thntnm'].$rLuasBgt['afd']]=$rLuasBgt['afd'];
               $dtPt[$rLuasBgt['thntnm'].$rLuasBgt['afd']]=$rPt['induk'];
            }
        }

        ////berat budget bln ini
        $sBrtBgt="select distinct sum(kg".$thnbgt[1].") as kgblnini,substr(kodeblok,1,6) as afd,thntnm,sum(kgsetahun) as kgsetahun from ".$dbname.".bgt_produksi_kbn_kg_vw 
                  where ".$whre." and tahunbudget='".$thnbgt[0]."'  group by substr(kodeblok,1,6),thntnm";
        //exit("Error".$sBrtBgt);
        $qBrtBgt=mysql_query($sBrtBgt) or die(mysql_error($conn));
        while($rBrtBgt=mysql_fetch_assoc($qBrtBgt))
        {
            if($rBrtBgt['kgblnini']!=0)
            {
                $brtBgtBlnini[$rBrtBgt['thntnm'].$rBrtBgt['afd']]=$rBrtBgt['kgblnini'];
            }
            $bgtThnan[$rBrtBgt['thntnm'].$rBrtBgt['afd']]=$rBrtBgt['kgsetahun'];
        }
        $addstr="(";
        for($W=1;$W<=intval($thnbgt[1]);$W++)
        {
            if($W<10)$jack="kg0".$W;
            else $jack="kg".$W;
            if($W<intval($thnbgt[1]))$addstr.=$jack."+";
            else $addstr.=$jack;
        }
        $addstr.=")";

        ////berat budget smp bln ini
        $sBrtBgt2="select distinct sum".$addstr." as kgsmpblnini,substr(kodeblok,1,6) as afd,thntnm from ".$dbname.".bgt_produksi_kbn_kg_vw 
                  where ".$whre." and tahunbudget='".$thnbgt[0]."' ".$whrbgt." group by substr(kodeblok,1,6),thntnm";
        //exit("Error".$sBrtBgt2);
        $qBrtBgt2=mysql_query($sBrtBgt2) or die(mysql_error($conn));
        while($rBrtBgt2=mysql_fetch_assoc($qBrtBgt2))
        {
            $brtBgtSmpBlnini[$rBrtBgt2['thntnm'].$rBrtBgt2['afd']]=$rBrtBgt2['kgsmpblnini'];
        }

        ////berat aktual bln ini
        $sAktual="select sum(kgwbtanpabrondolan) as brtaktual,substr(blok,1,6) as afd,tahuntanam from ".$dbname.".kebun_spb_vs_rencana_blok_vw where
                  periode='".$periodeDt."' ".$whrakt." group by substr(blok,1,6),tahuntanam";
       // exit("Error".$sAktual);
        $qAktual=mysql_query($sAktual) or die(mysql_error($conn));
        while($rAktual=mysql_fetch_assoc($qAktual))
        {
            $brtAktual[$rAktual['tahuntanam'].$rAktual['afd']]=$rAktual['brtaktual'];
        }
        
        ////berat aktual smp dgn bln ini
        $sAktualBln="select sum(kgwbtanpabrondolan) as brtaktual,substr(blok,1,6) as afd,tahuntanam from ".$dbname.".kebun_spb_vs_rencana_blok_vw where
                     periode between '".$thnbgt[0]."-01'  and  '".$periodeDt."' ".$whrakt."  group by substr(blok,1,6),tahuntanam";
        $qAktualBln=mysql_query($sAktualBln) or die(mysql_error($conn));
        while($rAktualBln=mysql_fetch_assoc($qAktualBln))
        {
            if($rAktualBln['tahuntanam']!='')
            {
                $brtAktualSmp[$rAktualBln['tahuntanam'].$rAktualBln['afd']]=$rAktualBln['brtaktual'];
            }
        }
$brd=0;
$agdDt='';
$lrt=0;
if(($proses=='excel')||($proses=='pdf'))
{
    $brd=1;
    $bgcoloraja="bgcolor=#DEDEDE ";
    $tab.="<table border=0>";
    $tab.="<tr><td colspan=13>".$_SESSION['lang']['pt']." :[ ".$nmpt." ] ".$_SESSION['lang']['unit']." : [ ".$unit." ]</td></tr>";
    $tab.="<tr><td colspan=13 align=center>LAPORAN PRODUKSI s.d. ".$thnbgt[1]."-".$thnbgt[0]." </td></tr>";
    $tab.="<tr><td colspan=13>".$_SESSION['lang']['satuan']." : Kg TBS  </td></tr>";
    $tab.="<tr><td colspan=13>".$_SESSION['lang']['periode']." : ".$thnbgt[1]."-".$thnbgt[0]."  </td></tr>";
    $tab.="</table>";

////tampilannya nich
//
 
$tab.="<div style='width:1180px;display:fixed;'>";
$tab.="<table cellpadding=1 cellspacing=1 border=".$brd." class=sortable width=100%>";
$tab.="<thead><tr class=rowheader>";
$tab.="<td rowspan=4 align=center ".$bgcoloraja."  style='width:40px;'>".$_SESSION['lang']['tahuntanam']."</td>";
$tab.="<td rowspan=4 align=center ".$bgcoloraja."  style='width:40px;'>".$_SESSION['lang']['pt']."</td>";
$tab.="<td rowspan=4 align=center ".$bgcoloraja."  style='width:40px;'>".$_SESSION['lang']['afdeling']."</td>";
$tab.="<td colspan=2  rowspan=2  align=center ".$bgcoloraja."  style='width:100px;'>LUAS TM (Ha)</td>";
$tab.="<td colspan=5   align=center ".$bgcoloraja."  style='width:270px;'>TOTAL PRODUKSI (KG)</td>";
$tab.="<td colspan=3   align=center ".$bgcoloraja."  style='width:150px;'>KG/HA</td></tr>";
$tab.="<tr class=rowheader>";
$tab.="<td colspan=2  align=center ".$bgcoloraja."  style='width:50px;'>BULAN INI</td>";
$tab.="<td colspan=2  align=center ".$bgcoloraja." style='width:50px;'>S.D. BULAN INI</td>";
$tab.="<td rowspan=3  align=center ".$bgcoloraja." style='width:70px;'>ANGGARAN TAHUNAN</td>";
$tab.="<td align=center ".$bgcoloraja."  style='width:40px;'>BI</td>";
$tab.="<td align=center ".$bgcoloraja." style='width:40px;'>SBI</td>";
$tab.="<td rowspan=3  align=center ".$bgcoloraja."  style='width:70px;'>ANGGARAN TAHUNAN KG/HA</td></tr>";
$tab.="<tr class=rowheader>";
$tab.="<td align=center rowspan=2 ".$bgcoloraja."  style='width:50px;'>Realisasi</td>";
$tab.="<td align=center rowspan=2 ".$bgcoloraja."  style='width:50px;'>Anggaran</td>";
$tab.="<td align=center rowspan=2 ".$bgcoloraja."  style='width:50px;'>Realisasi</td>";
$tab.="<td align=center rowspan=2 ".$bgcoloraja."  style='width:50px;'>Anggaran</td>";
$tab.="<td align=center rowspan=2 ".$bgcoloraja."  style='width:50px;'>Realisasi</td>";
$tab.="<td align=center rowspan=2 ".$bgcoloraja."  style='width:50px;'>Anggaran</td>";
$tab.="<td align=center rowspan=2 ".$bgcoloraja."  style='width:40px;'>KG/HA</td>";
$tab.="<td align=center rowspan=2 ".$bgcoloraja."  style='width:40px;'>KG/HA</td></tr></thead><tbody>";
$tab.="</tbody></table></div>";
$tab.="<div style='width:1180px;height:420px;overflow:scroll;'>
    <table cellpadding=1 cellspacing=1 border=".$brd." class=sortable width=100%>";
$tab.="<thead><tr>  </tr>  </thead><tbody>";
foreach($amThnTnm as $lstAfdeling){
    foreach($dtAfdeling as $rthntnm){
        if($dtThnTnm[$lstAfdeling.$rthntnm]!=''){
                    @$blnIni[$lstAfdeling.$rthntnm]=$brtAktual[$lstAfdeling.$rthntnm]/$dtLuas[$lstAfdeling.$rthntnm];
                    @$blnSmpIni[$lstAfdeling.$rthntnm]=$brtAktualSmp[$lstAfdeling.$rthntnm]/$dtLuas[$lstAfdeling.$rthntnm];
                    @$thnIni[$lstAfdeling.$rthntnm]=$bgtThnan[$lstAfdeling.$rthntnm]/$dtLuasBgt[$lstAfdeling.$rthntnm];
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td  align=center style='width:55px;'>".$lstAfdeling."</td>";
                    $tab.="<td  align=center style='width:85px;'>".$dtPt[$lstAfdeling.$rthntnm]."</td>";
                    $tab.="<td  style='width:63px;' align=center>".$afdDt[$lstAfdeling.$rthntnm]."</td>";
                    $tab.="<td style='width:69px;' align=right>".number_format($dtLuas[$lstAfdeling.$rthntnm],2)."</td>";//luas per tahun tanam per afdeling realisasi
                    $tab.="<td style='width:69px;' align=right>".number_format($dtLuasBgt[$lstAfdeling.$rthntnm],2)."</td>";//luas per tahun tanam per afdeling budget
                    $tab.="<td style='width:35px;' align=right>".number_format($brtAktual[$lstAfdeling.$rthntnm],2)."</td>";//luas per tahun tanam per afdeling realisasi
                    $tab.="<td style='width:35px;' align=right>".number_format($brtBgtBlnini[$lstAfdeling.$rthntnm],2)."</td>";
                    $tab.="<td style='width:35px;' align=right>".number_format($brtAktualSmp[$lstAfdeling.$rthntnm],2)."</td>";
                    $tab.="<td style='width:35px;' align=right>".number_format($brtBgtSmpBlnini[$lstAfdeling.$rthntnm],2)."</td>";
                    $tab.="<td style='width:70px;' align=right>".number_format($bgtThnan[$lstAfdeling.$rthntnm],2)."</td>";
                    $tab.="<td style='width:40px;' align=right>".number_format($blnIni[$lstAfdeling.$rthntnm],2)."</td>";
                    $tab.="<td style='width:40px;' align=right>".number_format($blnSmpIni[$lstAfdeling.$rthntnm],2)."</td>";
                    $tab.="<td style='width:70px;' align=right>".number_format($thnIni[$lstAfdeling.$rthntnm],2)."</td>";
                    $tab.="</tr>";
                    $subTotLuas[$lstAfdeling]+=$dtLuas[$lstAfdeling.$rthntnm];
                    $subTotLuasBgt[$lstAfdeling]+=$dtLuasBgt[$lstAfdeling.$rthntnm];
                    $subTotBrtAkt[$lstAfdeling]+=$brtAktual[$lstAfdeling.$rthntnm];
                    $subTotBrtBgt[$lstAfdeling]+=$brtBgtBlnini[$lstAfdeling.$rthntnm];
                    $subTotBrtSmp[$lstAfdeling]+=$brtAktualSmp[$lstAfdeling.$rthntnm];
                    $subTotBrtBgtSmp[$lstAfdeling]+=$brtBgtSmpBlnini[$lstAfdeling.$rthntnm];
                    $subTotThnan[$lstAfdeling]+=$bgtThnan[$lstAfdeling.$rthntnm];
                    $subTotBi[$lstAfdeling]+=$blnIni[$lstAfdeling.$rthntnm];
                    $subTotSbi[$lstAfdeling]+=$blnSmpIni[$lstAfdeling.$rthntnm];
                    
           
        }
    }
    if($lstAfdeling!=$agdDt)
    {
        $agdDt=$lstAfdeling;
        @$subTotThnAngr[$lstAfdeling]=$subTotThnan[$lstAfdeling]/$subTotLuasBgt[$lstAfdeling];
        $tab.="<tr class=rowcontent>";
        $tab.="<td colspan=3 ><b>Sub ".$_SESSION['lang']['total']."</b></td>";
        $tab.="<td align=right  ><b>".number_format($subTotLuas[$lstAfdeling],2)."</b></td>";//luas per tahun tanam per afdeling realisasi
        $tab.="<td align=right  ><b>".number_format($subTotLuasBgt[$lstAfdeling],2)."</b></td>";//luas per tahun tanam per afdeling budget
        $tab.="<td align=right  ><b>".number_format($subTotBrtAkt[$lstAfdeling],2)."</b></td>";//luas per tahun tanam per afdeling realisasi
        $tab.="<td align=right  ><b>".number_format($subTotBrtBgt[$lstAfdeling],2)."</b></td>";
        $tab.="<td align=right  ><b>".number_format($subTotBrtSmp[$lstAfdeling],2)."</b></td>";
        $tab.="<td align=right  ><b>".number_format($subTotBrtBgtSmp[$lstAfdeling],2)."</b></td>";
        $tab.="<td align=right  ><b>".number_format($subTotThnan[$lstAfdeling],2)."</b></td>";
        $tab.="<td align=right  ><b>".number_format($subTotBi[$lstAfdeling],2)."</b></td>";
        $tab.="<td align=right  ><b>".number_format($subTotSbi[$lstAfdeling],2)."</b></td>";
        $tab.="<td align=right  ><b>".number_format($subTotThnAngr[$lstAfdeling],2)."</b></td>";
        $tab.="</tr>";
        $grLuas+=$subTotLuas[$lstAfdeling];
        $grLuasBgt+=$subTotLuasBgt[$lstAfdeling];
        $grTotBrtAkt+=$subTotBrtAkt[$lstAfdeling];
        $grTotBrtBgt+=$subTotBrtBgt[$lstAfdeling];
        $grTotBrtSmp+=$subTotBrtSmp[$lstAfdeling];
        $grTotBrtBgtSmp+=$subTotBrtBgtSmp[$lstAfdeling];
        $grTotThnan+=$subTotThnan[$lstAfdeling];
        $grTotTotBi+=$subTotBi[$lstAfdeling];
        $grTotTotSbi+=$subTotSbi[$lstAfdeling];
        $grTotTotThnAngr+=$subTotThnAngr[$lstAfdeling];
    }

}
$tab.="<tr class=rowcontent>";
$tab.="<td colspan=3><b>Grand ".$_SESSION['lang']['total']."</b></td>";
$tab.="<td align=right><b>".number_format($grLuas,2)."</b></td>";//luas per tahun tanam per afdeling realisasi
$tab.="<td align=right><b>".number_format($grLuasBgt,2)."</b></td>";//luas per tahun tanam per afdeling budget
$tab.="<td align=right><b>".number_format($grTotBrtAkt,2)."</b></td>";//luas per tahun tanam per afdeling realisasi
$tab.="<td align=right><b>".number_format($grTotBrtBgt,2)."</b></td>";
$tab.="<td align=right><b>".number_format($grTotBrtSmp,2)."</b></td>";
$tab.="<td align=right><b>".number_format($grTotBrtBgtSmp,2)."</b></td>";
$tab.="<td align=right><b>".number_format($grTotThnan,2)."</b></td>";
$tab.="<td align=right><b>".number_format($grTotTotBi,2)."</b></td>";
$tab.="<td align=right><b>".number_format($grTotTotSbi,2)."</b></td>";
$tab.="<td align=right><b>".number_format($grTotTotThnAngr,2)."</b></td>";
$tab.="</tr>";

$tab.="</tbody></table>";
}
}
switch($proses)
{
    
	case'getData':
        //exit("Error:masuk donks");	
            if($kdPt!='')
            {
                $sOrg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
                       where induk='".$kdPt."' and tipe='KEBUN' order by namaorganisasi asc";
            }
            else
            {
                 $sOrg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
                       where tipe='KEBUN' order by namaorganisasi asc";
            }
         //   exit("Error".$sOrg);
	$optorg="<option value=''>".$_SESSION['lang']['all']."</option>";
	$qOrg=mysql_query($sOrg) or die(mysql_error());
	while($rOrg=mysql_fetch_assoc($qOrg))
	{
		$optorg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
	}
	echo $optorg;
	break;
	case'preview':
            foreach($amThnTnm as $lstAfdeling){
    foreach($dtAfdeling as $rthntnm){
        if($dtThnTnm[$lstAfdeling.$rthntnm]!=''){
                    @$blnIni[$lstAfdeling.$rthntnm]=$brtAktual[$lstAfdeling.$rthntnm]/$dtLuas[$lstAfdeling.$rthntnm];
                    @$blnSmpIni[$lstAfdeling.$rthntnm]=$brtAktualSmp[$lstAfdeling.$rthntnm]/$dtLuas[$lstAfdeling.$rthntnm];
                    @$thnIni[$lstAfdeling.$rthntnm]=$bgtThnan[$lstAfdeling.$rthntnm]/$dtLuasBgt[$lstAfdeling.$rthntnm];
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td  align=center style='width:50px;'>".$lstAfdeling."</td>";
                    $tab.="<td  align=center style='width:65px;'>".$dtPt[$lstAfdeling.$rthntnm]."</td>";
                    $tab.="<td  style='width:50px;' align=center>".$afdDt[$lstAfdeling.$rthntnm]."</td>";
                    $tab.="<td style='width:57px;' align=right>".number_format($dtLuas[$lstAfdeling.$rthntnm],2)."</td>";//luas per tahun tanam per afdeling realisasi
                    $tab.="<td style='width:55px;' align=right>".number_format($dtLuasBgt[$lstAfdeling.$rthntnm],2)."</td>";//luas per tahun tanam per afdeling budget
                    $tab.="<td style='width:57px;' align=right>".number_format($brtAktual[$lstAfdeling.$rthntnm],2)."</td>";//luas per tahun tanam per afdeling realisasi
                    $tab.="<td style='width:55px;' align=right>".number_format($brtBgtBlnini[$lstAfdeling.$rthntnm],2)."</td>";
                    $tab.="<td style='width:57px;' align=right>".number_format($brtAktualSmp[$lstAfdeling.$rthntnm],2)."</td>";
                    $tab.="<td style='width:55px;' align=right>".number_format($brtBgtSmpBlnini[$lstAfdeling.$rthntnm],2)."</td>";
                    $tab.="<td style='width:70px;' align=right>".number_format($bgtThnan[$lstAfdeling.$rthntnm],2)."</td>";
                    $tab.="<td style='width:45px;' align=right>".number_format($blnIni[$lstAfdeling.$rthntnm],2)."</td>";
                    $tab.="<td style='width:45px;' align=right>".number_format($blnSmpIni[$lstAfdeling.$rthntnm],2)."</td>";
                    $tab.="<td style='width:60px;' align=right>".number_format($thnIni[$lstAfdeling.$rthntnm],2)."</td>";
                    $tab.="</tr>";
                    $subTotLuas[$lstAfdeling]+=$dtLuas[$lstAfdeling.$rthntnm];
                    $subTotLuasBgt[$lstAfdeling]+=$dtLuasBgt[$lstAfdeling.$rthntnm];
                    $subTotBrtAkt[$lstAfdeling]+=$brtAktual[$lstAfdeling.$rthntnm];
                    $subTotBrtBgt[$lstAfdeling]+=$brtBgtBlnini[$lstAfdeling.$rthntnm];
                    $subTotBrtSmp[$lstAfdeling]+=$brtAktualSmp[$lstAfdeling.$rthntnm];
                    $subTotBrtBgtSmp[$lstAfdeling]+=$brtBgtSmpBlnini[$lstAfdeling.$rthntnm];
                    $subTotThnan[$lstAfdeling]+=$bgtThnan[$lstAfdeling.$rthntnm];
                    $subTotBi[$lstAfdeling]+=$blnIni[$lstAfdeling.$rthntnm];
                    $subTotSbi[$lstAfdeling]+=$blnSmpIni[$lstAfdeling.$rthntnm];
                    
           
        }
    }
    if($lstAfdeling!=$agdDt)
    {
        $agdDt=$lstAfdeling;
        @$subTotThnAngr[$lstAfdeling]=$subTotThnan[$lstAfdeling]/$subTotLuasBgt[$lstAfdeling];
        $tab.="<tr class=rowcontent>";
        $tab.="<td colspan=3 ><b>Sub ".$_SESSION['lang']['total']."</b></td>";
        $tab.="<td align=right  ><b>".number_format($subTotLuas[$lstAfdeling],2)."</b></td>";//luas per tahun tanam per afdeling realisasi
        $tab.="<td align=right ><b>".number_format($subTotLuasBgt[$lstAfdeling],2)."</b></td>";//luas per tahun tanam per afdeling budget
        $tab.="<td align=right  ><b>".number_format($subTotBrtAkt[$lstAfdeling],2)."</b></td>";//luas per tahun tanam per afdeling realisasi
        $tab.="<td align=right  ><b>".number_format($subTotBrtBgt[$lstAfdeling],2)."</b></td>";
        $tab.="<td align=right  ><b>".number_format($subTotBrtSmp[$lstAfdeling],2)."</b></td>";
        $tab.="<td align=right  ><b>".number_format($subTotBrtBgtSmp[$lstAfdeling],2)."</b></td>";
        $tab.="<td align=right  ><b>".number_format($subTotThnan[$lstAfdeling],2)."</b></td>";
        $tab.="<td align=right  ><b>".number_format($subTotBi[$lstAfdeling],2)."</b></td>";
        $tab.="<td align=right  ><b>".number_format($subTotSbi[$lstAfdeling],2)."</b></td>";
        $tab.="<td align=right  ><b>".number_format($subTotThnAngr[$lstAfdeling],2)."</b></td>";
        $tab.="</tr>";
        $grLuas+=$subTotLuas[$lstAfdeling];
        $grLuasBgt+=$subTotLuasBgt[$lstAfdeling];
        $grTotBrtAkt+=$subTotBrtAkt[$lstAfdeling];
        $grTotBrtBgt+=$subTotBrtBgt[$lstAfdeling];
        $grTotBrtSmp+=$subTotBrtSmp[$lstAfdeling];
        $grTotBrtBgtSmp+=$subTotBrtBgtSmp[$lstAfdeling];
        $grTotThnan+=$subTotThnan[$lstAfdeling];
        $grTotTotBi+=$subTotBi[$lstAfdeling];
        $grTotTotSbi+=$subTotSbi[$lstAfdeling];
        $grTotTotThnAngr+=$subTotThnAngr[$lstAfdeling];
    }

}
$tab.="<tr class=rowcontent>";
$tab.="<td colspan=3><b>Grand ".$_SESSION['lang']['total']."</b></td>";
$tab.="<td align=right><b>".number_format($grLuas,2)."</b></td>";//luas per tahun tanam per afdeling realisasi
$tab.="<td align=right><b>".number_format($grLuasBgt,2)."</b></td>";//luas per tahun tanam per afdeling budget
$tab.="<td align=right><b>".number_format($grTotBrtAkt,2)."</b></td>";//luas per tahun tanam per afdeling realisasi
$tab.="<td align=right><b>".number_format($grTotBrtBgt,2)."</b></td>";
$tab.="<td align=right><b>".number_format($grTotBrtSmp,2)."</b></td>";
$tab.="<td align=right><b>".number_format($grTotBrtBgtSmp,2)."</b></td>";
$tab.="<td align=right><b>".number_format($grTotThnan,2)."</b></td>";
$tab.="<td align=right><b>".number_format($grTotTotBi,2)."</b></td>";
$tab.="<td align=right><b>".number_format($grTotTotSbi,2)."</b></td>";
$tab.="<td align=right><b>".number_format($grTotTotThnAngr,2)."</b></td>";
$tab.="</tr>";
        echo $tab;
	break;
        case'excel':
            $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
            $dte=date("Hms");
            $nop_="produksiTbs__".$kdUnit."__".$dte;
            $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
            gzwrite($gztralala, $tab);
            gzclose($gztralala);
            echo "<script language=javascript1.2>
            window.location='tempExcel/".$nop_.".xls.gz';
            </script>";	
	break;
	case'pdf':
	
           class PDF extends FPDF {
           function Header() {
            global $periodeDt;
            global $kdUnit;
            global $unit;  
            global $dbname;
            global $thnbgt;
            global $kdPt;

   
                $this->SetFont('Arial','B',8);
                $this->Cell($width,$height,strtoupper("LAPORAN PRODUKSI s.d. ".$thnbgt[1]."-".$thnbgt[0]." "),0,1,'L');
                $this->Cell($width,$height,$_SESSION['lang']['bulan'].' : '.substr(tanggalnormal($periodeDt),1,7),0,1,'R');
                $tinggiAkr=$this->GetY();
                $ksamping=$this->GetX();
                $this->SetY($tinggiAkr+20);
                $this->SetX($ksamping);
                $this->Cell($width,$height,$_SESSION['lang']['unit'].' : '.$unit,0,1,'L');
                $this->Cell(790,$height,' ',0,1,'R');
                
                $height = 12;
                $this->SetFillColor(220,220,220);
                $this->SetFont('Arial','B',7);
                
                $tinggiAkr=$this->GetY();
                $ksamping=$this->GetX();
                $this->SetY($tinggiAkr+20);
                $this->SetX($ksamping);
               
                $this->Cell(51,$height,$_SESSION['lang']['tahuntanam'],TLR,0,'C',1);
                $this->Cell(48,$height,$_SESSION['lang']['pt'],TLR,0,'C',1);
                $this->Cell(48,$height,$_SESSION['lang']['afdeling'],TLR,0,'C',1);
                $this->Cell(96,$height,"LUAS TM (Ha)",TLR,0,'C',1);
                $this->Cell(242,$height,"TOTAL PRODUKSI (KG)",TLR,0,'C',1);
                $this->Cell(146,$height,"KG/HA ",TLR,1,'C',1);
                
                $this->Cell(51,$height,"",LR,0,'C',1);
                $this->Cell(48,$height,"",LR,0,'C',1);
                $this->Cell(48,$height,"",LR,0,'C',1);
                $this->Cell(96,$height," ",LR,0,'C',1);
                $this->Cell(96,$height,"BULAN INI",TLR,0,'C',1);
                $this->Cell(96,$height,"S.D. BULAN INI",TLR,0,'C',1);
                $this->Cell(50,$height,"ANGGARAN",TLR,0,'C',1);
                $this->Cell(48,$height,"BI",TLR,0,'C',1);
                $this->Cell(48,$height,"SBI",TLR,0,'C',1);
                $this->Cell(50,$height,"ANGGARAN",TLR,1,'C',1);
                
                $this->Cell(51,$height,"",BLR,0,'C',1);
                $this->Cell(48,$height,"",BLR,0,'C',1);
                $this->Cell(48,$height,"",BLR,0,'C',1);
                
                $this->Cell(48,$height,"Realisasi",TBLR,0,'C',1);
                $this->Cell(48,$height,"Anggaran",TBLR,0,'C',1);
                $this->Cell(48,$height,"Realisasi",TBLR,0,'C',1);
                $this->Cell(48,$height,"Anggaran",TBLR,0,'C',1);
                $this->Cell(48,$height,"Realisasi",TBLR,0,'C',1);
                $this->Cell(48,$height,"Anggaran",TBLR,0,'C',1);
                $this->Cell(50,$height,"TAHUNAN",BLR,0,'C',1);
                $this->Cell(48,$height,"KG/HA",TBLR,0,'C',1);
                $this->Cell(48,$height,"KG/HA",TBLR,0,'C',1);
                $this->Cell(50,$height,"THN KG/HA",BLR,1,'C',1);
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
            $height = 12;
            $tnggi=$jmlHari*$height;
            $pdf->AddPage();
            $pdf->SetFillColor(255,255,255);
            $pdf->SetFont('Arial','B',7);
            $i=0;
            foreach($amThnTnm  as $lstAfdeling){
                foreach($dtAfdeling as $rthntnm){
                    if($dtThnTnm[$lstAfdeling.$rthntnm]!=''){
                                $pdf->Cell(51,$height,$lstAfdeling,TBLR,0,'C',1);
                                $pdf->Cell(48,$height,$dtPt[$lstAfdeling.$rthntnm],TBLR,0,'C',1);
                                $pdf->Cell(48,$height,$afdDt[$lstAfdeling.$rthntnm],TBLR,0,'C',1);
                                $pdf->Cell(48,$height,number_format($dtLuas[$lstAfdeling.$rthntnm],2),TBLR,0,'R',1);
                                $pdf->Cell(48,$height,number_format($dtLuasBgt[$lstAfdeling.$rthntnm],2),TBLR,0,'R',1);
                                $pdf->Cell(48,$height,number_format($brtAktual[$lstAfdeling.$rthntnm],2),TBLR,0,'R',1);
                                $pdf->Cell(48,$height,number_format($brtBgtBlnini[$lstAfdeling.$rthntnm],2),TBLR,0,'R',1);
                                $pdf->Cell(48,$height,number_format($brtAktualSmp[$lstAfdeling.$rthntnm],2),TBLR,0,'R',1);
                                $pdf->Cell(48,$height,number_format($brtBgtSmpBlnini[$lstAfdeling.$rthntnm],2),TBLR,0,'R',1);
                                $pdf->Cell(50,$height,number_format($bgtThnan[$lstAfdeling.$rthntnm],2),TBLR,0,'R',1);
                                $pdf->Cell(48,$height,number_format($blnIni[$lstAfdeling.$rthntnm],2),TBLR,0,'R',1);
                                $pdf->Cell(48,$height,number_format($blnSmpIni[$lstAfdeling.$rthntnm],2),TBLR,0,'R',1);
                                $pdf->Cell(50,$height,number_format($thnIni[$lstAfdeling.$rthntnm],2),TBLR,1,'R',1);
                    }
                }
                if($lstAfdeling!=$agdDt)
                {
                    $agdDt=$lstAfdeling;
                    $pdf->Cell(147,$height,"Sub ".$_SESSION['lang']['total'],TBLR,0,'C',1);
                    $pdf->Cell(48,$height,number_format($subTotLuas[$lstAfdeling],2),TBLR,0,'R',1);
                    $pdf->Cell(48,$height,number_format($subTotLuasBgt[$lstAfdeling],2),TBLR,0,'R',1);
                    $pdf->Cell(48,$height,number_format($subTotBrtAkt[$lstAfdeling],2),TBLR,0,'R',1);
                    $pdf->Cell(48,$height,number_format($subTotBrtBgt[$lstAfdeling],2),TBLR,0,'R',1);
                    $pdf->Cell(48,$height,number_format($subTotBrtSmp[$lstAfdeling],2),TBLR,0,'R',1);
                    $pdf->Cell(48,$height,number_format($subTotBrtBgtSmp[$lstAfdeling],2),TBLR,0,'R',1);
                    $pdf->Cell(50,$height,number_format($subTotThnan[$lstAfdeling],2),TBLR,0,'R',1);
                    $pdf->Cell(48,$height,number_format($subTotBi[$lstAfdeling],2),TBLR,0,'R',1);
                    $pdf->Cell(48,$height,number_format($subTotSbi[$lstAfdeling],2),TBLR,0,'R',1);
                    $pdf->Cell(50,$height,number_format($subTotThnAngr[$lstAfdeling],2),TBLR,1,'R',1);
                }

            }
            $pdf->Cell(147,$height,"Grand ".$_SESSION['lang']['total'],TBLR,0,'C',1);
            $pdf->Cell(48,$height,number_format($grLuas,2),TBLR,0,'R',1);
            $pdf->Cell(48,$height,number_format($grLuasBgt,2),TBLR,0,'R',1);
            $pdf->Cell(48,$height,number_format($grTotBrtAkt,2),TBLR,0,'R',1);
            $pdf->Cell(48,$height,number_format($grTotBrtBgt,2),TBLR,0,'R',1);
            $pdf->Cell(48,$height,number_format($grTotBrtSmp,2),TBLR,0,'R',1);
            $pdf->Cell(48,$height,number_format($grTotBrtBgtSmp,2),TBLR,0,'R',1);
            $pdf->Cell(50,$height,number_format($grTotThnan,2),TBLR,0,'R',1);
            $pdf->Cell(48,$height,number_format($grTotTotBi,2),TBLR,0,'R',1);
            $pdf->Cell(48,$height,number_format($grTotTotSbi,2),TBLR,0,'R',1);
            $pdf->Cell(50,$height,number_format($grTotTotThnAngr,2),TBLR,1,'R',1);
            $pdf->Output();
	break;
	
	
	default:
	break;
}
?>