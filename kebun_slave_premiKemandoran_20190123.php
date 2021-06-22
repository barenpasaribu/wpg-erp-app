<?php
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';

$_GET['aksi']!=''?$_POST['aksi']=$_GET['aksi']:$_POST['aksi']=$_POST['aksi'];
$_GET['tanggal']!=''?$_POST['tanggal']=$_GET['tanggal']:$_POST['tanggal']=$_POST['tanggal'];
$_GET['proses']!=''?$_POST['proses']=$_GET['proses']:$_POST['proses']=$_POST['proses'];

$_GET['aksi']!=''?$_POST['aksi']=$_GET['aksi']:$_POST['aksi']=$_POST['aksi'];
$_GET['tanggal']!=''?$_POST['tanggal']=$_GET['tanggal']:$_POST['tanggal']=$_POST['tanggal'];
$_GET['proses']!=''?$_POST['proses']=$_GET['proses']:$_POST['proses']=$_POST['proses'];

             //ambil kamus karyawan
          $str="select a.karyawanid,a.namakaryawan,a.subbagian,b.tipe,a.tipekaryawan,a.kodejabatan,d.namajabatan
                  from ".$dbname.".datakaryawan a left join ".$dbname.".sdm_5tipekaryawan b on a.tipekaryawan=b.id                  
                  left join ".$dbname.".sdm_5jabatan d on a.kodejabatan=d.kodejabatan    
                  where a.lokasitugas='".$kodeorg."' and tipekaryawan!=0 and b.tipe!='KHL'";
          $res=mysql_query($str);
          $kamusKar=Array();
          while($bar=mysql_fetch_object($res))
          {
              $kamusKar[$bar->karyawanid]['id']=$bar->karyawanid;
              $kamusKar[$bar->karyawanid]['nama']=$bar->namakaryawan;
              $kamusKar[$bar->karyawanid]['subbagian']=$bar->subbagian;
              $kamusKar[$bar->karyawanid]['tipekaryawan']=$bar->tipekaryawan;
              $kamusKar[$bar->karyawanid]['namatipe']=$bar->tipe;              
              $kamusKar[$bar->karyawanid]['jabatan']=$bar->namajabatan;              
          }
          
 
  switch($_POST['aksi']){
      case 'ambilMandor':
        $str="select karyawanid as nikmandor,namakaryawan,subbagian from ".$dbname.".datakaryawan
        where lokasitugas='".$_SESSION['empl']['lokasitugas']."' and (tanggalkeluar IS NULL 
		or tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].")
        and tipekaryawan!=0 order by namakaryawan";
          $res=mysql_query($str);
          $optkar="<option value=''></option>";
          while($bar=mysql_fetch_object($res))
          {
              $optkar.="<option value='".$bar->nikmandor."'>".$bar->namakaryawan." [".$bar->subbagian."]</option>";
          }
          echo $optkar;
          break;
          //===============================================
      case 'ambilMandorMK':
			$str="select karyawanid as nikmandor,namakaryawan,subbagian from ".$dbname.".datakaryawan
			where lokasitugas='".$_SESSION['empl']['lokasitugas']."' and (tanggalkeluar IS NULL or tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].")
			and tipekaryawan!=0 order by namakaryawan";
            $res=mysql_query($str);
           
          $optkar="<option value=''></option>";
          while($bar=mysql_fetch_object($res))
          {
              $optkar.="<option value='".$bar->nikmandor1."'>".$bar->namakaryawan." [".$bar->subbagian."]</option>";
          }
          echo $optkar;
          break;
          //===============================================
       case 'ambilKerani':           
			$str="select karyawanid as nikmandor,namakaryawan,subbagian from ".$dbname.".datakaryawan
			where lokasitugas='".$_SESSION['empl']['lokasitugas']."' and (tanggalkeluar IS NULL 
			or tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].")
			and tipekaryawan!=0 order by namakaryawan";          
			$res=mysql_query($str);
			while($bar=mysql_fetch_object($res))
			{
				$arrku['id'][$bar->keranimuat]=$bar->keranimuat;
				$arrku['nama'][$bar->keranimuat]=$bar->namakaryawan;
				$arrku['subbagian'][$bar->keranimuat]=$bar->subbagian;
			} 
          
			if(count($arrku)>0){
                foreach($arrku['id'] as $id => $val){
                    $optkar.="<option value='".$id."'>".$arrku['nama'][$id]." [".$arrku['subbagian'][$id]."]</option>";
                }
			}
			echo $optkar;
			break;
			//===============================================
       case 'ambilKeraniPanen':
       
			$str="select karyawanid as nikmandor,namakaryawan,subbagian from ".$dbname.".datakaryawan
			where lokasitugas='".$_SESSION['empl']['lokasitugas']."' and (tanggalkeluar IS NULL 
			or tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].")
			and tipekaryawan!=0 order by namakaryawan";
			$res=mysql_query($str);
			$optkar="<option value=''></option>";
						
			while($bar=mysql_fetch_object($res))
			{
				$optkar.="<option value='".$bar->nikmandor."'>".$bar->namakaryawan." [".$bar->subbagian."]</option>";
			}
			echo $optkar;
			break;
			//===============================================

			/* FA 20190109
			$str="select karyawanid as nikmandor,namakaryawan,subbagian from ".$dbname.".datakaryawan
			where lokasitugas='".$_SESSION['empl']['lokasitugas']."' and (tanggalkeluar IS NULL 
			or tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].")
			and tipekaryawan!=0 order by namakaryawan";
		
          $res=mysql_query($str);
          $optkar="<option value=''></option>";
          $arrku=Array();
          while($bar=mysql_fetch_object($res))
          {
              $arrku['id'][$bar->keranimuat]=$bar->keranimuat;
              $arrku['nama'][$bar->keranimuat]=$bar->namakaryawan;
              $arrku['subbagian'][$bar->keranimuat]=$bar->subbagian;
          }
           if(count($arrku)>0){
                foreach($arrku['id'] as $id => $val){
                    $optkar.="<option value='".$id."'>".$arrku['nama'][$id]." [".$arrku['subbagian'][$id]."]</option>";
                }
           }
          echo $optkar;
          break;
		  */
          
        case 'ambilList':    
             if($_POST['tipe']=='ALLLIST')
             {//hanya pada tab terakhir
             $str="select a.karyawanid,a.tanggal,b.namakaryawan,a.pembagi,a.premisumber,a.premikomputer,premiinput,a.posting,a.jabatan
                     from ".$dbname.".kebun_premikemandoran a
                     left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                     where a.tanggal like '".($_POST['tanggal'])."%'   
                     and a.kodeorg='".$_SESSION['empl']['lokasitugas']."'";
             // exit("error:".$str);
             $res=mysql_query($str);
             $stream="<table class=sortable cellspacing=1 border=0>
                            <thead>
                            <tr class=rowheader>
                             <td>".$_SESSION['lang']['nomor']."</td>
                             <td>".$_SESSION['lang']['tanggal']."</td>
                             <td>".$_SESSION['lang']['namakaryawan']."</td> 
                             <td>".$_SESSION['lang']['jabatan']."</td>     
                             <td>Devider(Pembagi)</td>  
                             <td>".$_SESSION['lang']['premi']." ".$_SESSION['lang']['sumber']."</td> 
                             <td>Computer Calculation</td>
                             <td>".$_SESSION['lang']['premi']."</td>
                             <td>".$_SESSION['lang']['status']."</td>
                             <td>".$_SESSION['lang']['action']."</td>
                             </tr> 
                            </thead>
                            <tbody>";
                $no=0;
                $ttkom=0;
                $ttpremi=0;
                while($bar=mysql_fetch_object($res))
                {
                    $no++;
                $stream.="<tr class=rowcontent>
                                <td>".$no."</td>
                                <td>".tanggalnormal($bar->tanggal)."</td>
                                <td>".$bar->namakaryawan."</td>
                                <td>".substr($bar->jabatan,0,7)."</td>    
                                <td>".$bar->pembagi."</td>
                                <td align=right>".number_format($bar->premisumber,0,'.',',')."</td>
                                <td align=right>".number_format($bar->premikomputer,0,'.',',')."</td> 
                                <td align=right>".number_format($bar->premiinput,0,'.',',')."</td>
                                <td>".($bar->posting=='1'?"Posted":"Open")."</td>
                                <td>".($bar->posting=='1'?"":"<img src='images/skyblue/posting.png' style='cursor:pointer;' title='Posting' onclick=\"postingPremi('".$bar->karyawanid."','".$_SESSION['empl']['lokasitugas']."','".$bar->tanggal."','".$bar->jabatan."')\"")."</td>    
                                </tr>";
                $ttkom+=$bar->premikomputer;
                $ttpremi+=$bar->premiinput;                
                }
             $stream.="</tbody><tfoot>
                       <tr class=rowheader>
                       <td colspan=6>".$_SESSION['lang']['total']."</td>
                       <td align=right>".number_format($ttkom,0,'.',',')."</td>
                       <td align=right>".number_format($ttpremi,0,'.',',')."</td>
                        <td colspan=2></td>
                       </tr>
                     </tfoot></table>";             
             }   
             else//jika hanya ambil list
             {    
             $str="select a.karyawanid,a.tanggal,b.namakaryawan,a.pembagi,a.premisumber,a.premikomputer,premiinput,a.posting
                     from ".$dbname.".kebun_premikemandoran a
                     left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                     where a.tanggal=".tanggalsystem($_POST['tanggal'])."
                     and a.jabatan='".$_POST['tipe']."'    
                     and a.kodeorg='".$_SESSION['empl']['lokasitugas']."'";
            $bd=0;
             $res=mysql_query($str);
             if($_POST['proses']=='excel'){
                 $bg=" bgcolor=#DEDEDE";
                 $bd=1;
                 $str="select a.karyawanid,a.tanggal,b.namakaryawan,a.pembagi,a.premisumber,a.premikomputer,premiinput,a.posting,a.jabatan
                     from ".$dbname.".kebun_premikemandoran a
                     left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                     where a.tanggal like '".($_POST['tanggal'])."%'   
                     and a.kodeorg='".$_SESSION['empl']['lokasitugas']."'";
                 $res=mysql_query($str);
             }
             $stream="<table class=sortable cellspacing=1 border=".$bd.">
                            <thead>
                            <tr class=rowheader>
                             <td ".$bg.">".$_SESSION['lang']['nomor']."</td>
                             <td ".$bg.">".$_SESSION['lang']['tanggal']."</td>
                             <td ".$bg.">".$_SESSION['lang']['namakaryawan']."</td>";
            if($_POST['proses']=='excel'){ $stream.="<td ".$bg.">".$_SESSION['lang']['subbagian']."</td><td ".$bg.">".$_SESSION['lang']['tipekaryawan']."</td>";}
                           $stream.="<td ".$bg.">Devider(Pembagi)</td>  
                             <td ".$bg.">".$_SESSION['lang']['premi']." ".$_SESSION['lang']['sumber']."</td> 
                             <td ".$bg.">Computer Calculation</td>
                             <td ".$bg.">".$_SESSION['lang']['premi']."</td>
                             <td ".$bg.">".$_SESSION['lang']['status']."</td>";
             if($_POST['proses']!='excel')
             {
                           $stream.="<td ".$bg.">".$_SESSION['lang']['action']."</td>";
             }       
                             $stream.=" </tr> 
                            </thead>
                            <tbody>";
                $no=0;
                while($bar=mysql_fetch_object($res))
                {
                    $no++;
                $stream.="<tr class=rowcontent>
                                <td>".$no."</td>";
                                if($_POST['proses']=='excel')
                                {
                                    $stream.="<td>".$bar->tanggal."</td>";
                                }
                                else
                                {
                                    $stream.="<td>".tanggalnormal($bar->tanggal)."</td>";
                                }
                                    
                               $stream.=" <td>".$bar->namakaryawan."</td>";
                               if($_POST['proses']=='excel')
                               {    
                                    $sAdd="select distinct subbagian,tipekaryawan from ".$dbname.".datakaryawan where karyawanid='".$bar->karyawanid."'";
                                    $qAdd=mysql_query($sAdd) or die(mysql_error($conn));
                                    $rAdd=mysql_fetch_object($qAdd);
                                    $optTipe=makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
                                    $stream.="<td>".$rAdd->subbagian."</td>
                                            <td>".$optTipe[$rAdd->tipekaryawan]."</td>";
                               }
                               $stream.="  <td>".$bar->pembagi."</td>
                                <td align=right>".number_format($bar->premisumber,0,'.',',')."</td>
                                <td align=right>".number_format($bar->premikomputer,0,'.',',')."</td> 
                                <td align=right>".number_format($bar->premiinput,0,'.',',')."</td>";
                                $stream.="<td>".($bar->posting=='1'?"Posted":"Open")."</td>";
               if($_POST['proses']!='excel')
               {
                     $stream.="<td>".($bar->posting=='1'?"":"<img src='images/skyblue/delete.png' style='cursor:pointer;' title='Delete' onclick=\"deletePremi('".$bar->karyawanid."','".$_SESSION['empl']['lokasitugas']."','".$bar->tanggal."','".$_POST['tipe']."')\"")."</td>    
                                    ";
               }
                $stream.="</tr>";
                }
             $stream.="</tbody><tfoot></tfoot></table>";
             }
             if($_POST['proses']=='excel'){
                 if($no==0)$stream.='Data Kosong.<br>';
                $tab=$stream."Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
                $dte=date("YmdHis");
                $nop_="premiKemandoran_".$_POST['tanggal']; 
                if(strlen($tab)>0)
                {
                    if ($handle = opendir('tempExcel')) {
                        while (false != ($file = readdir($handle))) {
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
             }else
             {
             echo $stream;
             }
             break;
			 
        case 'simpan':
                 //periksa apakah sudah ada premi MK atau kerani untuk tgl tsb ...karyawanid = '".$_POST['karyawanid']."' and... 
                 $str="select * from ".$dbname.".kebun_premikemandoran where 
                          kodeorg= '".$_SESSION['empl']['lokasitugas']."' and tanggal='".tanggalsystem($_POST['tanggal'])."'
                          and jabatan!='MANDORPANEN'";
                 if($_POST['jabatan']=='MANDORPANEN')
                 {
                    $res=mysql_query($str);
                    if(mysql_num_rows($res)>0)
                    {
						# FA 20190109 coba dilepas atas permintaan klien
                        /*
						if($_SESSION['language']=='EN'){
                            exit('Error: Foreman premium and/or clerk premium has been recorded on this date, it can not be done unless you remove it first');
                        }else{
                            exit('Error: Premi MK dan Kerani sudah diinput pada tanggal tsb, premi Mandor Panen tidak dapat diinput kecuali menghapus terlebih dahulu Premi MK dan Premi Kerani');
                        }
						*/
                     }
                 }
                 
                 $str="insert into ".$dbname.".kebun_premikemandoran (kodeorg, tanggal, karyawanid, jabatan, pembagi, premisumber, premikomputer, premiinput, updateby, postingby, posting)
                         values(
                         '".$_SESSION['empl']['lokasitugas']."',
                         '".tanggalsystem($_POST['tanggal'])."',
                         ".$_POST['karyawanid'].",
                         '".$_POST['jabatan']."',
                         ".$_POST['pembagi'].", 
                         ".$_POST['sumber'].",
                         ".$_POST['komputer'].",
                         ".$_POST['premi'].",
                         ".$_SESSION['standard']['userid'].",
                         0,
                         0
                         )";
                 if(mysql_query($str))
                 {               
                 }
                 else
                 {
                     exit(" Error:".addslashes(mysql_error($conn)));
                 }   
             break;

        case 'delete':          
                 //periksa apakah sudah ada premi MK atau kerani untuk tgl tsb
                 $str="select * from ".$dbname.".kebun_premikemandoran where 
                          kodeorg= '".$_SESSION['empl']['lokasitugas']."' and tanggal='".$_POST['tanggal']."'
                          and jabatan!='MANDORPANEN'";
                 if($_POST['jabatan']=='MANDORPANEN')
                 {
                    $res=mysql_query($str);
                    if(mysql_num_rows($res)>0)
                    {
                        exit('Error: Premi MK dan Kerani sudah diinput pada tanggal tsb, premi Mandor Panen tidak dapat dihapus, kecuali menghapus terlebih dahulu Premi MK dan Premi Kerani');
                    }
                 }
                //periksa posting
                 $str="select * from ".$dbname.".kebun_premikemandoran where 
                          kodeorg= '".$_SESSION['empl']['lokasitugas']."' and tanggal='".$_POST['tanggal']."'
                          and karyawanid=".$_POST['karyawanid']." and posting=1";
                    $res=mysql_query($str);
                    if(mysql_num_rows($res)>0)
                    {
                        exit('Error: Maaf, data tersebut sudah diposting, tidak dapat dihapus');
                    }
                  else{
                         $str="delete from ".$dbname.".kebun_premikemandoran where 
                          kodeorg= '".$_SESSION['empl']['lokasitugas']."' and tanggal='".$_POST['tanggal']."'
                          and karyawanid=".$_POST['karyawanid'];
                         //echo $str;
                         //exit("Error");
                         if(mysql_query($str))
                         {
                             
                         }
                         else
                         {
                             echo "Error ".addslashes(mysql_error($conn));
                         }
                  }
          break;

        case 'posting':          
                    $str="update  ".$dbname.".kebun_premikemandoran 
                    set posting=1,postingby=".$_SESSION['standard']['userid']."    
                    where kodeorg= '".$_POST['kodeorg']."' and tanggal='".$_POST['tanggal']."'
                    and karyawanid=".$_POST['karyawanid'];
                    if(mysql_query($str))
                    {

                    }
                    else
                    {
                        echo "Error ".addslashes(mysql_error($conn));
                    }
          break;

        case 'ambilPremiPanen':          // -a.upahpenalty ga dipake sebagai perhitungan premi
			$str="select sum(a.upahpremi+a.premibasis-a.rupiahpenalty) as premi,a.nik  from ".$dbname.".kebun_prestasi a
                   left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi
                   where a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and b.tanggal=".tanggalsystem($_POST['tanggal'])."
                   and b.nikmandor='".$_POST['nikmandor']."'
                   group by a.nik";
			$res=mysql_query($str);
			$jlhpemanen=  mysql_num_rows($res);
			$tPremiPanen=0;
			while($bar=mysql_fetch_object($res))
			{
              $tPremiPanen+=$bar->premi;
			}
          
          //ambil standar jumlah pembagi
          //ambil standar jumlah pembagi untuk mandor sebagai informasi
            $str="select nilai from ".$dbname.".setup_parameterappl where kodeparameter='STMD'";
            $res=mysql_query($str);            
            while($bar=mysql_fetch_object($res))
            {
                $jlh=$bar->nilai;
            }

                    echo $jlhpemanen."#".number_format($tPremiPanen,0,'.',',')."#".$jlh;
            break;
         
		case 'ambilPremiMandor':          
           $str="select sum(a.premiinput) as premi,count(*) as jlhmandor  from ".$dbname.".kebun_premikemandoran a                      
                   where a.kodeorg ='".$_SESSION['empl']['lokasitugas']."' 
                   and a.tanggal=".tanggalsystem($_POST['tanggal'])."
                   and karyawanid in(
                   select distinct b.nikmandor from ".$dbname.".kebun_aktifitas b where b.tanggal=".tanggalsystem($_POST['tanggal'])."
                   and b.nikmandor= '".$_POST['nikmandor']."')
                   and a.jabatan='MANDORPANEN'";
          $res=mysql_query($str);
          $jlhpemanen=  mysql_num_rows($res);
          $tPremiPanen=0;
          $jumlahmandor=0;
          while($bar=mysql_fetch_object($res))
          {
              $tPremiPanen=$bar->premi;
              $jumlahmandor=$bar->jlhmandor;
          }
          
           echo $jumlahmandor."#".number_format($tPremiPanen,0,'.',',');
          break;

        case 'ambilPremiKerani':          
           $str="select sum(a.premiinput) as premi,count(*) as jlhmandor  from ".$dbname.".kebun_premikemandoran a                      
                   where a.kodeorg ='".$_SESSION['empl']['lokasitugas']."' 
                   and a.tanggal=".tanggalsystem($_POST['tanggal'])."
                   and karyawanid in(
                   select distinct b.nikmandor from ".$dbname.".kebun_aktifitas b where b.tanggal=".tanggalsystem($_POST['tanggal'])."
                   and b.nikasisten= '".$_POST['nikkerani']."')
                   and a.jabatan='MANDORPANEN'";

          $res=mysql_query($str);
          $jlhpemanen=  mysql_num_rows($res);
          $tPremiPanen=0;
          $jumlahmandor=0;
          while($bar=mysql_fetch_object($res))
          {
              $tPremiPanen=$bar->premi;
              $jumlahmandor=$bar->jlhmandor;
          }
          
           echo $jumlahmandor."#".number_format($tPremiPanen,0,'.',',');
          break;

        case 'ambilPremiKeraniPanen':  
			
			// MIG sama dengan Premi Mandor Panen - FA 20190116	
/*
           $str="select sum(a.premiinput) as premi,count(*) as jlhmandor  from ".$dbname.".kebun_premikemandoran a                      
                   where a.kodeorg ='".$_SESSION['empl']['lokasitugas']."' 
                   and a.tanggal=".tanggalsystem($_POST['tanggal'])."
                   and karyawanid in(
                   select distinct b.nikmandor from ".$dbname.".kebun_aktifitas b where b.tanggal=".tanggalsystem($_POST['tanggal'])."
                   and b.nikmandor1= '".$_POST['nikmandor1']."')
                   and a.jabatan='MANDORPANEN'";
*/
           $str= "select a.tanggal,a.keranimuat, b.premisumber as premi, b.pembagi as jlhmandor  
					from ".$dbname.".kebun_aktifitas a left join ".$dbname.".kebun_premikemandoran b on a.nikmandor = b.karyawanid                       
					where a.kodeorg = '".$_SESSION['empl']['lokasitugas']."' 
					and left(a.notransaksi,8)='".tanggalsystem($_POST['tanggal'])."' 
					and a.keranimuat = '".$_POST['nikmandor1']."' and b.jabatan = 'MANDORPANEN'";
		   
			$res=mysql_query($str);
			$jlhpemanen=  mysql_num_rows($res);
			$tPremiPanen=0;
			$jumlahmandor=0;
			while($bar=mysql_fetch_object($res))
			{
				$tPremiPanen=$bar->premi;
				$jumlahmandor=$bar->jlhmandor;
			}
			$jlh = $jumlahmandor;
//			echo $jumlahmandor."#".number_format($tPremiPanen,0,'.',',');
			echo $jumlahmandor."#".number_format($tPremiPanen,0,'.',',')."#".$jlh;
			break;

} 
?>