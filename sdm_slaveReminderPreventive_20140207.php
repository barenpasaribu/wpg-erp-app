<?
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

#default Message:
$mess="<html>
               <head>
               </head>
               <body>
               Remind you for the folowing task:<br>";

# TRAKSI
#1. Ambil barang per masing 
$str="select a.kodebarang,b.namabarang,a.jumlah,a.id,b.satuan from ".$dbname.".schedulerdt a left join 
          ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang
            left join ".$dbname.".schedulerht c on a.id=c.id";
$res=mysql_query($str);
$detail=Array();
while($bar=mysql_fetch_object($res))
{
    $detail[$bar->id]['namabarang'][]=$bar->namabarang;
    $detail[$bar->id]['jumlah'][]=$bar->jumlah;
    $detail[$bar->id]['kodebarang'][]=$bar->kodebarang;
    $detail[$bar->id]['satuan'][]=$bar->satuan;
}
#ambil value terakhir
$str="SELECT max(tanggal) as tanggal, id, nilai FROM ".$dbname.".scheduler_aksi group by id";
$res=mysql_query($str);
$lastReminder=Array();
while($bar=mysql_fetch_object($res))
{
    $lastReminder[$bar->id]=$bar->nilai;
}

#2. Ambil HM Traksi Terakhir
$kmAhir=Array();
$str="select * from ".$dbname.".vhc_kmhmakhir_vw order by kodevhc";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $kmAhir[$bar->kodevhc]=$bar->kmhmakhir;
}

#3. Ambil HM MESIN PKS;
$str="select sum(hmmesin) as hm, mesin as kodevhc from ".$dbname.".pabrik_hmmesin_vw group by mesin order by mesin";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $kmAhir[$bar->kodevhc]=$bar->hm;
}

$str="select * from ".$dbname.".schedulerht  order by batasreminder asc";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    if($bar->batasreminder==0 or $bar->batasreminder=='')
    {
        #reminder yang sekali tanpa perulangan
        if(date('Y-m-d')==$bar->setiaptanggal and $bar->sekali==2)
        {
                $subject="Preventive Maintenance ".$bar->kodemesin;
                $mess.="<table>
                                <thead>
                                </thead>
                                <tbody>
                                     <tr><td>Task name</td><td>:".$bar->namatugas."</td></tr>
                                     <tr><td>Object</td><td>:".$bar->kodemesin."</td></tr>
                                     <tr><td>Note</td><td>:".$bar->ketrangan."</td></tr>    
                                     <tr><td>Warning On</td><td>:".tanggalnormal_hrd($bar->setiaptanggal)."</td></tr>    
                                </tbody>
                                 </table>";
                if(count($detail[$bar->id]['namabarang'])>0){
                        $mess.="Detail:<br><table border=1><tr><td>Kodebarang</td><td>Nama Barang</td><td>Jumlah</td></tr>";
                           foreach($detail[$bar->id]['namabarang'] as $detil =>$val)
                            {
                                 $mess.="<tr><td>".$detail[$bar->id]['kodebarang'][$detil]."</td><td>".$detail[$bar->id]['namabarang'][$detil]."</td><td>".$detail[$bar->id]['jumlah'][$detil]." ".$detail[$bar->id]['satuan'][$detil]."</td></tr>";                             
                            }
                        $mess.="</table>";   
                }
                $mess.="<br><br>Regards,<br>OWL-Plantation System</body></html>";
                $to=$bar->email;
                if($to!=''){ 
                        $kirim=kirimEmail_hrd($to,$subject,$mess);#this has return but disobeying;     
               }
                #update table
                $stru="update ".$dbname.".schedulerht set lastreminder='".date('Y-m-d')."' where id=".$bar->id;
                mysql_query($stru);
                $stri="insert into ".$dbname.".scheduler_aksi(id, tanggal, kodeorg, keterangan, pic, selesai, updateby, nilai)
                            values(".$bar->id.",
                                       '".date('Y-m-d')."',
                                       '".$bar->kodeorg."',
                                       '".$bar->ketrangan."',
                                       '".$bar->email."',0,0,'')";
                mysql_query($stri); 
        }
        #reminder yang  perulangan
        else if(date('m-d')==substr($bar->setiaptanggal,5,5) and $bar->sekali==1)
        {
                $subject="Preventive Maintenance ".$bar->kodemesin;
                $mess.="<table>
                                <thead>
                                </thead>
                                <tbody>
                                     <tr><td>Task name</td><td>:".$bar->namatugas."</td></tr>
                                     <tr><td>Object</td><td>:".$bar->kodemesin."</td></tr>
                                     <tr><td>Note</td><td>:".$bar->ketrangan."</td></tr>    
                                     <tr><td>Warning On</td><td>:".tanggalnormal_hrd($bar->setiaptanggal)."</td></tr>    
                                </tbody>
                                 </table>";
                if(count($detail[$bar->id]['namabarang'])>0){
                        $mess.="Detail:<br><table border=1><tr><td>Kodebarang</td><td>Nama Barang</td><td>Jumlah</td></tr>";
                           foreach($detail[$bar->id]['namabarang'] as $detil =>$val)
                            {
                                 $mess.="<tr><td>".$detail[$bar->id]['kodebarang'][$detil]."</td><td>".$detail[$bar->id]['namabarang'][$detil]."</td><td>".$detail[$bar->id]['jumlah'][$detil]." ".$detail[$bar->id]['satuan'][$detil]."</td></tr>";                             
                            }
                        $mess.="</table>";   
                }
                $mess.="<br><br>Regards,<br>OWL-Plantation System</body></html>";
                $to=$bar->email;
                if($to!=''){ 
                        $kirim=kirimEmail_hrd($to,$subject,$mess);#this has return but disobeying;     
               }
                #update table
                $stru="update ".$dbname.".schedulerht set lastreminder='".date('Y-m-d')."' where id=".$bar->id;
                mysql_query($stru);
                $stri="insert into ".$dbname.".scheduler_aksi(id, tanggal, kodeorg, keterangan, pic, selesai, updateby, nilai)
                            values(".$bar->id.",
                                       '".date('Y-m-d')."',
                                       '".$bar->kodeorg."',
                                       '".$bar->ketrangan."',
                                       '".$bar->email."',0,0,'')";
                mysql_query($stri); 
        }        
    }
    else
    {
        if($bar->tastreminder!='0000-00-00' and $bar->sekali==2)
        {
            #diabaikan karena bukan perulangan dan sudah pernah direminder
        }
        else
        {
            $batasAtas=$bar->batasatas;
            $peringatan=$bar->batasreminder;
            @$saatIni=$kmAhir[$bar->kodemesin];
            if($saatIni=='')
                $saatIni=0;            
            @$peringatanTerakhir=  $lastReminder[$bar->id];            
            if($peringatanTerakhir=='')
                $peringatanTerakhir=0;
            
            #rumus
            @$z=$saatIni%$batasAtas;
             if($z=='')
                 $z=0;
            $akumulasi=$saatIni-$z;
            $sisa=$z;
                      
            if($sisa>=$peringatan and $peringatanTerakhir<$akumulasi)
            {
               $subject="Preventive Maintenance ".$bar->kodemesin;
                $mess.="<table>
                <thead>
                </thead>
                <tbody>
                     <tr><td>Task name</td><td>:".$bar->namatugas."</td></tr>
                     <tr><td>Object</td><td>:".$bar->kodemesin."</td></tr>
                     <tr><td>Note</td><td>:".$bar->ketrangan."</td></tr>    
                     <tr><td>Warning On</td><td>:".$saatIni." ".$bar->satuan."</td></tr>    
                </tbody>
                 </table>";
                if(count($detail[$bar->id]['namabarang'])>0){
                        $mess.="Detail:<br><table border=1><tr><td>Kodebarang</td><td>Nama Barang</td><td>Jumlah</td></tr>";
                           foreach($detail[$bar->id]['namabarang'] as $detil =>$val)
                            {
                                 $mess.="<tr><td>".$detail[$bar->id]['kodebarang'][$detil]."</td><td>".$detail[$bar->id]['namabarang'][$detil]."</td><td>".$detail[$bar->id]['jumlah'][$detil]." ".$detail[$bar->id]['satuan'][$detil]."</td></tr>";                             
                            }
                        $mess.="</table>";   
                }
                $mess.="<br><br>Regards,<br>OWL-Plantation System</body></html>";
                $to=$bar->email;
                if($to!=''){ 
                        $kirim=kirimEmail_hrd($to,$subject,$mess);#this has return but disobeying;     
               }
                #update table
                $stru="update ".$dbname.".schedulerht set lastreminder='".date('Y-m-d')."' where id=".$bar->id;
                mysql_query($stru);
                $stri="insert into ".$dbname.".scheduler_aksi(id, tanggal, kodeorg, keterangan, pic, selesai, updateby, nilai)
                            values(".$bar->id.",
                                       '".date('Y-m-d')."',
                                       '".$bar->kodeorg."',
                                       '".$bar->ketrangan."',
                                       '".$bar->email."',0,0,'".$saatIni."')";
                mysql_query($stri);       
            }          
        }
    }
    
}	

#reminder  stok minimum
$str="select kodebarang,namabarang,satuan,minstok from ".$dbname.".log_5masterbarang where minstok>0 order by kodebarang";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $barang[$bar->kodebarang]=$bar->kodebarang;
    $namabarang[$bar->kodebarang]=$bar->namabarang;
    $satuan[$bar->kodebarang]=$bar->satuan;
    $minstok[$bar->kodebarang]=$bar->minstok;
}

#ambil saldo per PT
$str="select sum(saldoqty) as saldo, a.kodebarang, kodeorg,b.minstok from ".$dbname.".log_5masterbarangdt a
          left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang where b.minstok>0
          group by kodeorg,a.kodebarang
          having (saldo < minstok or saldo=minstok)";
$res=mysql_query($str);
$mess1="<html>
               <head>
               </head>
               <body>
               <br>";
if(mysql_num_rows($res)>0)
{
    $subject=" Minimum stock reminder (On: ".date('d-m-Y H:i:s').")";
                    $mess1.="Dear All,<br>Berikut ini adalah Material yang sudah mencapai batas minimun. Segera lakukan pengadaan untuk barang:
                        <table border=1 cellspacing=0>
                        <thead>
                         <tr><td>No.</td>
                         <td>PT</td>
                         <td>Kodebarang</td>
                         <td>Nama Barang</td>
                         <td>Satuan</td>
                         <td>Saldo Saat Ini</td>
                         <td>Min.Saldo</td>
                         </tr>   
                        </thead>  
                    <tbody>";
              $no=0;      
              while($bar=mysql_fetch_object($res))
              {
                  
                 $no+=1;
                  $mess1.="<tr><td>".$no."</td><td>".$bar->kodeorg."</td><td>".$bar->kodebarang."</td>
                                  <td>".$namabarang[$bar->kodebarang]."</td><td>".$satuan[$bar->kodebarang]."</td>
                                   <td align=right>".number_format($bar->saldo,0)."</td><td align=right>".number_format($bar->minstok,0)."</td>
                                    </tr>";   
               } 
             $mess1.="</tbody><tfoot></tfoot></table><br>Regards, <br>OWL-Plantation System<br>The Best and Proven ERP For Palm Oil Plantation Solutions</body></html>";
             
     #ambil email dari reminder stok
             $str="select nilai from ".$dbname.".setup_parameterappl where kodeparameter='LOGRM'";
             $res1=mysql_query($str);
             while($bar=mysql_fetch_object($res1)){
                 $to1=$bar->nilai;
             }
          #kirim email   
        if($to1!=''){ 
               $kirim=kirimEmail_hrd($to1,$subject,$mess1);#this has return but disobeying;     
        }
}


##  email ulang tahun:============================================================
$str="select nilai from ".$dbname.".setup_parameterappl where kodeparameter='RCUTI'";
$res1=mysql_query($str);
while($bar=mysql_fetch_object($res1)){
    $to1=$bar->nilai;
}
             
$str1="select karyawanid,namakaryawan,lokasitugas,email from ".$dbname.".datakaryawan
	       where (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".date('Ymd').")  and tanggallahir like '%".date('m-d')."'
                          and tipekaryawan =0";
$res1=mysql_query($str1);
$mess2="<html>
               <head>
               </head>
               <body>
               <br>";
while($bar1=mysql_fetch_object($res1))
{
        $subject2="Selamat Ulang Tahun !";
        $mess2.="Dear ".$bar1->namakaryawan.",<br><br>
                        Waktu berjalan tiada henti<br> 
                        mengiringi rembulan dan mentari yang terbit nan tenggelam setiap hari<br>
                        mengiringi usiamu yang terus bertambah dari hari ke hari<br>
                        hingga saat ini..<br><br>

                        Selamat ulang tahun ".$bar1->namakaryawan."
                        Sungguh masa depan itu memang ada<br> 
                        karena kau telah berhasil melewati satu 1 tahun lagi masa usiamu.<br><br>

                        Semoga dengan bertambahnya usia menjadikan ".$bar1->namakaryawan." insan yang mulia,<br>
                        semakin bijak dan menjadi berkah bagi lingkungan kehidupan saudara dan 
                        semakin berprestasi dan berkarya.<br><br>

                        Kami segenap Direksi dan Karyawan mengucapkan SELAMAT ULANG TAHUN<br>
                        Panjang Umur dan Bahagia selalu dalam hidupmu.<br><br><br><br>
                        
                        </body></html>";
       if($bar1->email!==''){
           $to2.=$to1.",".$bar1->email;
       } 
       else{
           $to2=$to1;
       }
      if($to2!=''){
           $kirim=kirimEmail_hrd($to2,$subject2,$mess2);#this has return but disobeying;            
      }      
      $mess2="<html>
               <head>
               </head>
               <body>
               <br>";
}    


##  email  reminder probation:============================================================
//penentuan tanggal 2 minggu lagi
$t=mktime(0,0,0,date('m'),(date('d')-76),date('Y'));//105 dianggap 3 bulan kurang 2 minggu
$tanggalmasuk=date('Y-m-d',$t);

$str1="select karyawanid,namakaryawan,tanggalmasuk,lokasitugas,subbagian from ".$dbname.".datakaryawan
	       where  tanggalmasuk='".$tanggalmasuk."'  and tipekaryawan =0";
$res=mysql_query($str1);
$mess3="<html>
               <head>
               </head>
               <body>
               <br>";
if(mysql_num_rows($res)>0)
{
    $subject3=" Reminder Akhir Masa Percobaan Karyawan Baru (On: ".date('d-m-Y H:i:s').")";
                    $mess3.="Dear Hrd,<br>Berikut ini adalah karyawan yang akan berakhir masa percobannya:
                        <table border=1 cellspacing=0>
                        <thead>
                         <tr><td>No.</td>
                         <td>Nama Naryawan</td>
                         <td>TMK</td>
                         <td>Lokasi Tugas</td>
                         <td>Sub.Bagian</td>
                         </tr>   
                        </thead>  
                    <tbody>";
              $no=0;      
              while($bar=mysql_fetch_object($res))
              {
                  
                 $no+=1;
                  $mess3.="<tr><td>".$no."</td>
                                  <td>".$bar->namakaryawan."</td>
                                  <td>".tanggalnormal_hrd($bar->tanggalmasuk)."</td>
                                  <td>".$bar->lokasitugas."</td><td>".$bar->subbagian."</td>
                                    </tr>";   
               } 
             $mess3.="</tbody><tfoot></tfoot></table><br>
                               Silahkan diproses sesuai dengan tahapan yang berlaku.<br>
                             <br>Regards, <br>OWL-Plantation System<br>The Best and Proven ERP For Palm Oil Plantation Solutions</body></html>";
          #kirim email   
        if($to1!=''){ 
                $kirim=kirimEmail_hrd($to1,$subject3,$mess3);#this has return but disobeying;     
        }    
}    


##  email  reminder akhir kontrak karyawan:============================================================
             $str="select nilai from ".$dbname.".setup_parameterappl where kodeparameter='KONTRAK'";
             $res2=mysql_query($str);
             while($bar=mysql_fetch_object($res2)){
                 $to2=$bar->nilai;
             }
//penentuan tanggal 2 minggu lagi
$t=mktime(0,0,0,date('m'),(date('d')+14),date('Y'));
$tanggalkeluar=date('Y-m-d',$t);

$str1="select karyawanid,namakaryawan,tanggalmasuk,tanggalkeluar,lokasitugas,subbagian from ".$dbname.".datakaryawan
	       where  tanggalkeluar='".$tanggalkeluar."'  and tipekaryawan in(6,2)";
$res=mysql_query($str1);
$mess4="<html>
               <head>
               </head>
               <body>
               <br>";
if(mysql_num_rows($res)>0)
{
    $subject4=" Reminder Akhir Masa Kontrak Karyawan (On: ".date('d-m-Y H:i:s').")";
                    $mess4.="Dear Hrd,<br>Berikut ini adalah karyawan yang akan berakhir masa kontraknya:
                        <table border=1 cellspacing=0>
                        <thead>
                         <tr><td>No.</td>
                         <td>Nama Naryawan</td>
                         <td>TMK</td>
                         <td>Lokasi Tugas</td>
                         <td>Sub.Bagian</td>
                         <td>Akhir.Kontrak</td>
                         </tr>   
                        </thead>  
                    <tbody>";
              $no=0;      
              while($bar=mysql_fetch_object($res))
              {
                  
                 $no+=1;
                  $mess4.="<tr><td>".$no."</td>
                                  <td>".$bar->namakaryawan."</td>
                                  <td>".tanggalnormal_hrd($bar->tanggalmasuk)."</td>
                                  <td>".$bar->lokasitugas."</td>
                                  <td>".$bar->subbagian."</td>
                                  <td>".tanggalnormal_hrd($bar->tanggalkeluar)."</td>    
                                    </tr>";   
               } 
             $mess4.="</tbody><tfoot></tfoot></table><br>
                               Silahkan diproses sesuai dengan tahapan yang berlaku.<br>
                             <br>Regards, <br>OWL-Plantation System<br>The Best and Proven ERP For Palm Oil Plantation Solutions</body></html>";
          #kirim email   
        if($to2!=''){ 
                $kirim=kirimEmail_hrd($to2,$subject4,$mess4);#this has return but disobeying;     
        }    
}    


##  email  reminder janjang angin:============================================================
             $str="select nilai from ".$dbname.".setup_parameterappl where kodeparameter='RB01'";
             $res2=mysql_query($str);
             while($bar=mysql_fetch_object($res2)){
                 $to2=$bar->nilai;
             }
             
$tanggalkeluar=date('Y-m-d', strtotime( '-2 days' ));
$tanggalkeluar2=date('Y-m-d', strtotime( '-4 days' ));

        $query = "SELECT tanggal,left(blok,6) as afdeling, sum(jjgmasak) as jjgmasak
            FROM ".$dbname.".`kebun_taksasi`
            WHERE `tanggal` between '".$tanggalkeluar2."' and '".$tanggalkeluar."' GROUP BY tanggal,left(blok,6) ORDER BY left(blok,6)
            ";
        $qDetail=mysql_query($query) or die(mysql_error($conn));
        while($rDetail=mysql_fetch_assoc($qDetail))
        {
            
            $janjangtaksasi[$rDetail['tanggal']][$rDetail['afdeling']]=$rDetail['jjgmasak'];
            $afdeling[$rDetail['afdeling']]=$rDetail['afdeling'];
            $tanggal[$rDetail['tanggal']]=$rDetail['tanggal'];
        }
        
        $query = "SELECT tanggal,left(kodeorg,6) as afdeling, sum(hasilkerja) as janjangpanen
            FROM ".$dbname.".`kebun_prestasi_vw`
            WHERE `tanggal` between '".$tanggalkeluar2."' and '".$tanggalkeluar."' GROUP BY tanggal,left(kodeorg,6) ORDER BY left(kodeorg,6)
            ";
        
        $qDetail=mysql_query($query) or die(mysql_error($conn));
        while($rDetail=mysql_fetch_assoc($qDetail))
        {
            $janjangpanen[$rDetail['tanggal']][$rDetail['afdeling']]=$rDetail['janjangpanen'];
            $afdeling[$rDetail['afdeling']]=$rDetail['afdeling'];
            $tanggal[$rDetail['tanggal']]=$rDetail['tanggal'];
        }                          
        
        $tab='';
        if(!empty($afdeling))foreach($afdeling as $key=>$nilai){
            if(($janjangtaksasi[$nilai]*1.1<$janjangpanen[$nilai])or($janjangtaksasi[$nilai]*0.9>$janjangpanen[$nilai])){
                $tab.="<tr><td>".$nilai."</td>";
                if(!empty($tanggal))foreach($tanggal as $tgl){
                @$selisih=(abs($janjangtaksasi[$tgl][$nilai]-$janjangpanen[$tgl][$nilai]))/$janjangtaksasi[$tgl][$nilai]*100;
                    $tab.="<td align=right>".number_format($janjangtaksasi[$tgl][$nilai])."</td><td align=right>".number_format($janjangpanen[$tgl][$nilai])."</td>
                        <td align=right>".number_format($selisih,2)."</td>";
                }
                $tab.="</tr>";
            }
        }
        
        
            
        

$mess4="<html>
               <head>
               </head>
               <body>
               <br>";
if($tab!=''){

    $subject4=" Reminder Varian Taksasi dan Realisasi Panen (On: ".$tanggalkeluar.")";
                    $mess4.="Dear All,<br>Berikut ini adalah data realisasi panen pada ".$tanggalkeluar." dengan toleransi taksasi lebih dari 10%:
                        <table border=1 cellspacing=0>
                        <thead>
                         <tr><td rowspan=2>Afdeling</td>";
                         if(!empty($tanggal))foreach($tanggal as $tgl){
                             $mess4.="<td colspan=3>".$tgl."</td>";
                         }
                         $mess4.="</tr><tr><td>Taksasi (JJG)</td>
                         <td>Panen (JJG)</td>
                         <td>Selisih (%)</td>
                         </tr>   
                        </thead>  
                    <tbody>";
             $mess4.=$tab;       
             $mess4.="</tbody><tfoot></tfoot></table><br>
                               Demikian informasi ini disampaikan, semoga berguna.<br>
                             <br>Regards, <br>OWL-Plantation System<br>The Best and Proven ERP For Palm Oil Plantation Solutions</body></html>";
          #kirim email   
        if($to2!=''){ 
                $kirim=kirimEmail_hrd($to2,$subject4,$mess4);#this has return but disobeying;     
        }    
//        echo $mess4;
}    

?>