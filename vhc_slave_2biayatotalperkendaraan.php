<?php
require_once('master_validation.php');
require_once('config/connection.php');
//require_once('lib/nangkoelib.php');
require_once('lib/eagrolib.php');

$unit=$_POST['unit'];
$tglAwal=tanggalsystem($_POST['tglAwal']);
$tglAkhir=tanggalsystem($_POST['tglAkhir']);

if($unit==''){
    echo"warning: Working unit required";exit();
}
if($tglAwal==''||$tglAkhir==''){
	echo "Warning: date required"; exit;
}
#=========================================================
#4.5 ambilnoakun biaya kendaraan
  $akunkdari='';
  $akunksampai='';
  $strh="select distinct noakundebet,sampaidebet  from ".$dbname.".keu_5parameterjurnal where  jurnalid='LPVHC'";
  $resh=mysql_query($strh);
  echo mysql_error($conn);
  while($barh=mysql_fetch_object($resh))
  {
      $akunkdari=$barh->noakundebet;
      $akunksampai=$barh->sampaidebet;
  }
  if($akunkdari=='' or $akunksampai=='')
  {
      exit("Error: Journal parameter for LPVHC(vehicle cost) not exist");
  }
  

	$str="select sum(debet) as jumlah, kodevhc from ".$dbname.".keu_jurnaldt_vw where
		  kodevhc in (select kodevhc from ".$dbname.".vhc_5master where kodetraksi like '%".substr($unit,0,4)."%')
		  and tanggal>='".$tglAwal."' and tanggal<='".$tglAkhir."' and nojurnal like '%".substr($unit,0,4)."%'
		  and (noakun between '".$akunkdari."' and '".$akunksampai."') 
                  and (noreferensi not like '%ALK_KERJA_AB%' or noreferensi is NULL)
                  group by kodevhc";
//exit("Error:".$str);
//=================================================
	 
	$res=mysql_query($str);
	$no=0;
	if(mysql_num_rows($res)<1)
	{
		echo"<tr class=rowcontent><td colspan=4>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
	}
	else
	{
#ambil jumlah jam per kendaraan
            
   $str1="select sum(jumlah) as jumlah,kodevhc from ".$dbname.".vhc_rundt a left join 
       ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi
      where tanggal>='".$tglAwal."' and tanggal<='".$tglAkhir."' and kodevhc in (select kodevhc from ".$dbname.".vhc_5master
      where kodetraksi like '".$unit."%')
      group by kodevhc";
   $res1=mysql_query($str1); 
   $jumlahjam=Array();
   while($bar1=mysql_fetch_object($res1))
   {
       $jumlahjam[str_replace(" ","",$bar1->kodevhc)]=$bar1->jumlah;
   }

    #loop per kendaraan        
            while($bar=mysql_fetch_object($res))
		{
                        $no+=1; $total=0;
			if($jumlahjam[str_replace(" ","",$bar->kodevhc)]>0)
                            $rpunit=$bar->jumlah/$jumlahjam[str_replace(" ","",$bar->kodevhc)];
                        else
                            $rpunit=0;
                        
                       if(isset($jumlahjam[str_replace(" ","",$bar->kodevhc)])){
                            $color='#dedede';
                            $title='Normal';
                            $tmblDetail="<img onclick=\"viewDetail(event,'".str_replace(" ","",$bar->kodevhc)."','".$tglAwal."','".$tglAkhir."','".substr($unit,0,4)."','".$periode."','".$akunkdari."','".$akunksampai."');\" title=\"Detail Alokasi\" class=\"resicon\" src=\"images/zoom.png\">";
							//$tmblDetail="<img onclick=\"detailAlokasi(event,'".str_replace(" ","",$bar->kodevhc)."','".$rpunit."');\" title=\"Detail Alokasi\" class=\"resicon\" src=\"images/zoom.png\">";
                       }
                       else{
                            $color='red';
                            $title='No activity record';
                            $tmblDetail="";
                       }
                       $ondiKlik=" style='cursor:pointer;' title='Click' onclick=\"viewDetail(event,'".str_replace(" ","",$bar->kodevhc)."','".$tglAwal."','".$tglAkhir."','".substr($unit,0,4)."','".$periode."','".$akunkdari."','".$akunksampai."');\"";
                        echo"<tr class=rowcontent  class=rowcontent >
				  <td align=right ".$ondiKlik." >".$no."</td>
				  <td ".$ondiKlik.">".str_replace(" ","",$bar->kodevhc)."</td>
				
				  <td ".$ondiKlik." align=right>".number_format($bar->jumlah)."</td>
                                  <td ".$ondiKlik." align=right bgcolor=".$color." title='".$title."'>".number_format($jumlahjam[str_replace(" ","",$bar->kodevhc)],2)."</td> 
                                  <td ".$ondiKlik." align=right>".number_format($rpunit)."</td> 
                                  <td align=center>".$tmblDetail."</td>
				</tr>";
		}

	}
?>