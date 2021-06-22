<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zFunction.php');

$karyawanid=$_POST['karyawanid'];

$str="select a.namaorganisasi from ".$dbname.".datakaryawan b left join ".$dbname.".organisasi a 
          on b.kodeorganisasi=a.kodeorganisasi where b.karyawanid=".$karyawanid;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $namapt=$bar->namaorganisasi;
}


$str="select *,
      case jeniskelamin when 'L' then 'Laki-Laki'
          else  'Wanita'
          end as jk
          from ".$dbname.".datakaryawan where karyawanid=".$karyawanid ." limit 1";
$res=mysql_query($str);
$defaulsrc='images/user.png';
echo"<div style='width:100%;height:100%;overflow:scroll;'>
     <fieldset><legend>".$_SESSION['lang']['datapribadi']."</legend>
     <table class=standard cellspacing=1 width=100% bgcolor=#A3D988 cellpadding=2>";
while($bar=mysql_fetch_object($res))
{
        //get pendidikan
         $pendidikan='';
         $str1="select kelompok from ".$dbname.".sdm_5pendidikan where levelpendidikan=".$bar->levelpendidikan;
         $res1=mysql_query($str1);
         while($bar1=mysql_fetch_object($res1))
           {$pendidikan=$bar1->kelompok;}
        //Tipe karyawan
        $tipekaryawan='';
        $str2="select * from ".$dbname.".sdm_5tipekaryawan where id=".$bar->tipekaryawan;	  
        $res2=mysql_query($str2);
        while($bar2=mysql_fetch_object($res2))
        {$tipekaryawan=$bar2->tipe;}

        //jabatan
        $jabatan='';
        $str3="select * from ".$dbname.".sdm_5jabatan where kodejabatan=".$bar->kodejabatan." and namajabatan not like '%available' order by kodejabatan";
        $res3=mysql_query($str3);
        //echo $str3;
        while($bar3=mysql_fetch_object($res3))
        {$jabatan=$bar->kodejabatan;}	
        $jabatanku=$bar->kodejabatan;
		
		$grossup=$bar->isPPHGrossUp;
		if ($grossup==0){
			$grossup=$_SESSION['lang']['tidak'];
		}
		else if ($grossup==1){
			$grossup=$_SESSION['lang']['yes'];
		}
		
		$alokasi=$bar->alokasi;
		if ($alokasi==1){
			$alokasis = $_SESSION['lang']['ho'];
		}
		else if ($alokasi==1){
			$alokasis = $_SESSION['lang']['unit'];
		}
		$jenisbiaya=$_SESSION['lang'][$bar->jenisbiaya];
		
		//Golongan
		$slgol="select kodegolongan,namagolongan from sdm_5golongan";
		$resgol=$eksi->sSQL($slgol);
		foreach($resgol as $bargol){
			$namgol[$bargol['kodegolongan']]=$bargol['namagolongan'];
		}
		
        /*echo"<tr>
                 <td colspan=4 align=center>
                           <img src='".($bar->photo==''?$defaulsrc:$bar->photo)."' style='height:120px;'>
                         </td>
                 </tr>
             <tr class=rowcontent>
                <td align=right width=80px>".$_SESSION['lang']['uniqueid']."</td><td align=left bgcolor=#EDEDED><b>".$bar->karyawanid."</b></td>
                        <td align=right width=80px>".$_SESSION['lang']['nik']."</td><td align=left bgcolor=#EDEDED><b>".$bar->nik."</b></td>
                 </tr>
                 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['nama']."</td><td align=left bgcolor=#EDEDED><b>".$bar->namakaryawan."</b></td>
                        <td align=right>".$_SESSION['lang']['tempatlahir']."</td><td align=left bgcolor=#EDEDED><b>".$bar->tempatlahir."</b></td>
                 </tr>
                 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['tanggallahir']."</td><td align=left bgcolor=#EDEDED><b>".tanggalnormal_hrd($bar->tanggallahir)."</b></td>
                        <td align=right>".$_SESSION['lang']['warganegara']."</td><td align=left bgcolor=#EDEDED><b>".$bar->warganegara."</b></td>
                 </tr>		
                 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['jeniskelamin']."</td><td align=left bgcolor=#EDEDED><b>".$bar->jk."</b></td>
                        <td align=right>".$_SESSION['lang']['statusperkawinan']."</td align=left><td bgcolor=#EDEDED><b>".$bar->statusperkawinan."</b></td>
                 </tr>	
                 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['tanggalmenikah']."</td><td align=left bgcolor=#EDEDED><b>".tanggalnormal_hrd($bar->tanggalmenikah)."</b></td>
                        <td align=right>".$_SESSION['lang']['agama']."</td><td align=left bgcolor=#EDEDED><b>".$bar->agama."</b></td>
                 </tr>		 		  
                 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['golongandarah']."</td><td align=left bgcolor=#EDEDED><b>".$bar->golongandarah."</b></td>
                        <td align=right>".$_SESSION['lang']['pendidikan']."</td><td align=left bgcolor=#EDEDED><b>".$pendidikan."</b></td>
                 </tr>	
                 <tr class=rowcontent>
                <td align=right valign=top>".$_SESSION['lang']['alamataktif']."</td><td align=left bgcolor=#EDEDED valign=top>".$bar->alamataktif."</td>
                        <td align=right valign=top>".$_SESSION['lang']['kota']."</td><td align=left bgcolor=#EDEDED valign=top><b>".$bar->kota."</b></td>
                 </tr>
                 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['province']."</td><td align=left bgcolor=#EDEDED><b>".$bar->provinsi."</b></td>
                        <td align=right>".$_SESSION['lang']['kodepos']."</td><td align=left bgcolor=#EDEDED><b>".$bar->kodepos."</b></td>
                 </tr>
                 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['telp']."</td><td align=left bgcolor=#EDEDED><b>".$bar->noteleponrumah."</b></td>
                        <td align=right>".$_SESSION['lang']['nohp']."</td><td align=left bgcolor=#EDEDED><b>".$bar->nohp."</b></td>
                 </tr>	
                 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['norekeningbank']."</td><td align=left bgcolor=#EDEDED><b>".$bar->norekeningbank."</b></td>
                        <td align=right>".$_SESSION['lang']['namabank']."</td><td align=left bgcolor=#EDEDED><b>".$bar->namabank."</b></td>
                 </tr>	
                 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['sistemgaji']."</td><td align=left bgcolor=#EDEDED><b>".$bar->sistemgaji."</b></td>
                        <td align=right>".$_SESSION['lang']['passport']."</td><td align=left bgcolor=#EDEDED><b>".$bar->no_keluarga."</b></td>
                 </tr>	
                 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['noktp']."</td><td align=left bgcolor=#EDEDED><b>".$bar->noktp."</b></td>
                        <td align=right>".$_SESSION['lang']['notelepondarurat']."</td><td align=left bgcolor=#EDEDED><b>".$bar->notelepondarurat."</b></td>
                 </tr>
                 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['tanggalmasuk']."</td><td align=left bgcolor=#EDEDED><b>".tanggalnormal_hrd($bar->tanggalmasuk)."</b></td>
                        <td align=right>".$_SESSION['lang']['tanggalkeluar']."</td><td align=left bgcolor=#EDEDED><b>".tanggalnormal_hrd($bar->tanggalkeluar)."</b></td>
                 </tr>	
                 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['tipekaryawan']."</td><td align=left bgcolor=#EDEDED><b>".$tipekaryawan."</b></td>
                        <td align=right>".$_SESSION['lang']['jumlahanak']."</td><td align=left bgcolor=#EDEDED><b>".$bar->jumlahanak."</b></td>
                 </tr>	
                 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['tanggungan']."</td><td align=left bgcolor=#EDEDED><b>".$bar->jumlahtanggungan."</b></td>
                        <td align=right>".$_SESSION['lang']['statuspajak']."</td><td align=left bgcolor=#EDEDED><b>".$bar->statuspajak."</b></td>
                 </tr>		 
                 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['npwp']."</td><td align=left bgcolor=#EDEDED><b>".$bar->npwp."</b></td>
                        <td align=right>".$_SESSION['lang']['lokasipenerimaan']."</td><td align=left bgcolor=#EDEDED><b>".$bar->lokasipenerimaan."</b></td>
                 </tr>	
                 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['kodeorganisasi']."</td><td align=left bgcolor=#EDEDED><b>".$bar->kodeorganisasi."</b></td>
                        <td align=right>".$_SESSION['lang']['bagian']."</td><td align=left bgcolor=#EDEDED><b>".$bar->bagian."</b></td>
                 </tr>			 		 		 	 			 	 		 	 
                 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['functionname']."</td><td align=left bgcolor=#EDEDED><b>".$jabatan."</b></td>
                        <td align=right>".$_SESSION['lang']['kodegolongan']."</td><td align=left bgcolor=#EDEDED><b>".$bar->kodegolongan."</b></td>
                 </tr>
                 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['lokasitugas']."</td><td align=left bgcolor=#EDEDED><b>".$bar->lokasitugas."</b></td>
                        <td align=right>".$_SESSION['lang']['email']."</td><td align=left bgcolor=#EDEDED><b>".$bar->email."</b></td>
                 </tr>
                 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['subbagian']."</td><td align=left bgcolor=#EDEDED><b>".$bar->subbagian."</b></td>
                        <td align=right>".$_SESSION['lang']['bpjskes']."</td><td align=left bgcolor=#EDEDED><b>".$bar->jms."</b></td>
                 </tr>                 
				 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['bpjstkjp']."</td><td align=left bgcolor=#EDEDED><b>".$bar->jms2."</b></td>
                        <td align=right>".$_SESSION['lang']['bpjstk']."</td><td align=left bgcolor=#EDEDED><b>".$bar->jms3."</b></td>
                 </tr>
			";*/
			//Samakan tampilan dengan PDF (tombol lambang pdf) ==Jo 05-01-2016==
			echo"<tr>
                 <td colspan=4 align=center>
                           <img src='".($bar->photo==''?$defaulsrc:$bar->photo)."' style='height:120px;'>
                         </td>
                 </tr>
				 <tr class=rowcontent>
					<td align=right>".$_SESSION['lang']['nama']."</td><td align=left bgcolor=#EDEDED><b>".$bar->namakaryawan."</b></td>
				   <td align=right>".$_SESSION['lang']['email']."</td><td align=left bgcolor=#EDEDED><b>".$bar->email."</b></td>
                 </tr>
				<tr class=rowcontent>
					<td align=right width=80px>".$_SESSION['lang']['nik']."</td><td align=left bgcolor=#EDEDED><b>".$bar->nik."</b></td>
					<td align=right width=80px>".$_SESSION['lang']['noktp']."</td><td align=left bgcolor=#EDEDED><b>".$bar->noktp."</b></td>
					 
                 </tr>
				<tr class=rowcontent>
					<td align=right width=80px>".$_SESSION['lang']['tempatlahir']."</td><td align=left bgcolor=#EDEDED><b>".$bar->tempatlahir."</b></td>
					<td align=right width=80px>".$_SESSION['lang']['passport']."</td><td align=left bgcolor=#EDEDED><b>".$bar->no_keluarga."</b></td>
					 
                 </tr>
				<tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['tanggallahir']."</td><td align=left bgcolor=#EDEDED><b>".tanggalnormal_hrd($bar->tanggallahir)."</b></td>
				<td align=right>".$_SESSION['lang']['status']."</td><td align=left bgcolor=#EDEDED><b>".$bar->statusperkawinan."</b></td>
                 </tr>	
                 				
				<tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['jeniskelamin']."</td><td align=left bgcolor=#EDEDED><b>".$bar->jk."</b></td>
				<td align=right>".$_SESSION['lang']['tanggalmenikah']."</td><td align=left bgcolor=#EDEDED><b>".tanggalnormal_hrd($bar->tanggalmenikah)."</b></td>
                 </tr>	
                  				
				<tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['golongandarah']."</td><td align=left bgcolor=#EDEDED><b>".$bar->golongandarah."</b></td>
				 <td align=right>".$_SESSION['lang']['jumlahtanggungan']."</td><td align=left bgcolor=#EDEDED><b>".$bar->jumlahtanggungan."</b></td>
                 </tr>	
				 
				<tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['agama']."</td><td align=left bgcolor=#EDEDED><b>".$bar->agama."</b></td>
				 <td align=right>".$_SESSION['lang']['warganegara']."</td><td align=left bgcolor=#EDEDED><b>".$bar->warganegara."</b></td>
                 </tr>	
				 
				<tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['alamat']."</td><td align=left bgcolor=#EDEDED><b>".$bar->alamataktif.", ".$bar->kota.", ".$bar->provinsi.", ".$bar->kodepos."</b></td>
				 <td align=right>".$_SESSION['lang']['keluargadarurat']."</td><td align=left bgcolor=#EDEDED><b>".$bar->namadarurat."</b></td>
                 </tr>	
				 
 				<tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['telp']."</td><td align=left bgcolor=#EDEDED><b>".$bar->noteleponrumah."</b></td>
				 <td align=right>".$_SESSION['lang']['hubungan']."</td><td align=left bgcolor=#EDEDED><b>".$bar->hubungandarurat."</b></td>
                 </tr>	
 				 
 				<tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['nohp']."</td><td align=left bgcolor=#EDEDED><b>".$bar->nohp."</b></td>
				 <td align=right>".$_SESSION['lang']['notelepondarurat']."</td><td align=left bgcolor=#EDEDED><b>".$bar->notelepondarurat."</b></td>
                 </tr>	
 				 
 				<tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['pendidikan']."</td><td align=left bgcolor=#EDEDED><b>".$pendidikan."</b></td>
				 <td align=right>".$_SESSION['lang']['npwp']."</td><td align=left bgcolor=#EDEDED><b>".$bar->npwp."</b></td>
                 </tr>	
				 
				 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['lokasipenerimaan']."</td><td align=left bgcolor=#EDEDED><b>".$bar->lokasipenerimaan."</b></td>
				 <td align=right>".$_SESSION['lang']['no']." ".$_SESSION['lang']['bpjsktk']."</td><td align=left bgcolor=#EDEDED><b>".$bar->jms3."</b></td>
                 </tr>	
				 
				 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['nm_perusahaan']."</td><td align=left bgcolor=#EDEDED><b>".$namapt."</b></td>
				 <td align=right>".$_SESSION['lang']['no']." ".$_SESSION['lang']['bpjskes']."</td><td align=left bgcolor=#EDEDED><b>".$bar->jms."</b></td>
                 </tr>	
				 
				 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['lokasitugas']."</td><td align=left bgcolor=#EDEDED><b>".$bar->lokasitugas."</b></td>
				 <td align=right>".$_SESSION['lang']['no']." ".$_SESSION['lang']['bpjsjp']."</td><td align=left bgcolor=#EDEDED><b>".$bar->jms2."</b></td>
                 </tr>	
				 
				 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['tglmulaikerja']."</td><td align=left bgcolor=#EDEDED><b>".tanggalnormal_hrd($bar->tanggalmasuk)."</b></td>
				 <td align=right>".$_SESSION['lang']['statuspajak']."</td><td align=left bgcolor=#EDEDED><b>".$bar->statuspajak."</b></td>
                 </tr>	
				 
  				 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['departemen']."/".$_SESSION['lang']['bagian']."</td><td align=left bgcolor=#EDEDED><b>".$bar->bagian."</b></td>
				 <td align=right>".$_SESSION['lang']['jumlahtanggungan']."</td><td align=left bgcolor=#EDEDED><b>".$bar->jumlahtanggungan."</b></td>
                 </tr>	
 				 
  				 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['subbagian']."</td><td align=left bgcolor=#EDEDED><b>".$bar->subbagian."</b></td>
				 <td align=right>".$_SESSION['lang']['namabank']."</td><td align=left bgcolor=#EDEDED><b>".$bar->namabank."</b></td>
                 </tr>	
 				 
  				 <tr class=rowcontent>
                <td align=right>".$_SESSION['lang']['functionname']."</td><td align=left bgcolor=#EDEDED><b>".$bar->subbagian."</b></td>
				 <td align=right>".$_SESSION['lang']['norekeningbank']."</td><td align=left bgcolor=#EDEDED><b>".$bar->norekeningbank."</b></td>
                 </tr>	
				 
  				 <tr class=rowcontent>
               
				 <td align=right>".$_SESSION['lang']['levelname']."</td><td align=left bgcolor=#EDEDED><b>".$namgol[$bar->kodegolongan]."</b></td>
				 <td align=right>".$_SESSION['lang']['sistemgaji']."</td><td align=left bgcolor=#EDEDED><b>".$bar->sistemgaji."</b></td>
                 </tr>	
				 
  				 <tr class=rowcontent>
                
				 <td align=right>".$_SESSION['lang']['tipekaryawan']."</td><td align=left bgcolor=#EDEDED><b>".$tipekaryawan."</b></td>
				 <td align=right>".$_SESSION['lang']['grossup']."</td><td align=left bgcolor=#EDEDED><b>".$grossup."</b></td>
                 </tr>	
				 
  				 <tr class=rowcontent>
                
				 <td align=right>".$_SESSION['lang']['alokasibiaya']."</td><td align=left bgcolor=#EDEDED><b>".$alokasis."</b></td>
				 <td align=right>".$_SESSION['lang']['tglberhentikerja']."</td><td align=left bgcolor=#EDEDED><b>".tanggalnormal_hrd($bar->tanggalkeluar)."</b></td>
                 </tr>	
                 
  				 <tr class=rowcontent>
                
				<td align=right>".$_SESSION['lang']['jenisbiaya']."</td><td align=left bgcolor=#EDEDED><b>".$jenisbiaya."</b></td>
                 </tr>	
                 
			";

}
echo"</table>
     </fieldset>
         <fieldset><legend>".$_SESSION['lang']['pengalamankerja']."</legend>
         <table class=standard cellspacing=1 width=100% bgcolor=#A3D988 cellpadding=2>
                        <tr class=rowheader>
                          <td>No.</td>
                          <td>".$_SESSION['lang']['orgname']."</td>
                          <td>".$_SESSION['lang']['bidangusaha']."</td>
                          <td>".$_SESSION['lang']['bulanmasuk']."</td>
                          <td>".$_SESSION['lang']['bulankeluar']."</td>
                          <td>".$_SESSION['lang']['jabatanterakhir']."</td>
                          <td>".$_SESSION['lang']['bagian']."</td>
                          <td>".$_SESSION['lang']['masakerja']."</td>
                          <td>".$_SESSION['lang']['alamat']."</td>	
                        </tr>	 
         ";
         $str="select *,right(bulanmasuk,4) as masup,left(bulanmasuk,2) as busup from ".$dbname.".sdm_karyawancv where karyawanid=".$karyawanid." order by masup,busup";
         $res=mysql_query($str);
         $no=0;
         $mskerja=0;
         while($bar=mysql_fetch_object($res))
         {
         $no+=1;	
                  $msk=mktime(0,0,0,substr(str_replace("-","",$bar->bulanmasuk),0,2),1,substr($bar->bulanmasuk,3,4));	
                  $klr=mktime(0,0,0,substr(str_replace("-","",$bar->bulankeluar),0,2),1,substr($bar->bulankeluar,3,4));	
                  $dateDiff = $klr - $msk;
              $mskerja = floor($dateDiff/(60*60*24))/365; 

         echo"	  <tr class=rowcontent>
                          <td>".$no."</td>
                          <td>".$bar->namaperusahaan."</td>
                          <td>".$bar->bidangusaha."</td>
                          <td>".$bar->bulanmasuk."</td>
                          <td>".$bar->bulankeluar."</td>
                          <td>".$bar->jabatan."</td>
                          <td>".$bar->bagian."</td>
                          <td>".number_format($mskerja,2,',','.')." Yrs.</td>
                          <td>".$bar->alamatperusahaan."</td>	
                        </tr>";	 	
         }	 
echo"</table>
     </fieldset>
         <fieldset><legend>".$_SESSION['lang']['pendidikan']."</legend>
         <table class=standard cellspacing=1 width=100% bgcolor=#A3D988 cellpadding=2>
         <tr class=rowheader>
          <td>No.</td>
          <td>".$_SESSION['lang']['edulevel']."</td>			  
          <td>".$_SESSION['lang']['namasekolah']."</td>
          <td>".$_SESSION['lang']['kota']."</td>			  
          <td>".$_SESSION['lang']['jurusan']."</td>			  
          <td>".$_SESSION['lang']['tahunlulus']."</td>
          <td>".$_SESSION['lang']['gelar']."</td>
          <td>".$_SESSION['lang']['nilai']."</td>
          <td>".$_SESSION['lang']['keterangan']."</td>	
         </tr>
         ";
         $str="select a.*,b.kelompok from ".$dbname.".sdm_karyawanpendidikan a,".$dbname.".sdm_5pendidikan b
                        where a.karyawanid=".$karyawanid." 
                        and a.levelpendidikan=b.levelpendidikan
                        order by a.levelpendidikan desc";
         $res=mysql_query($str);
         $no=0;
         while($bar=mysql_fetch_object($res))
         {
         $no+=1;	
         echo"	  <tr class=rowcontent>
                          <td>".$no."</td>
                          <td>".$bar->kelompok."</td>			  
                          <td>".$bar->namasekolah."</td>
                          <td>".$bar->kota."</td>			  
                          <td>".$bar->spesialisasi."</td>			  
                          <td>".$bar->tahunlulus."</td>
                          <td>".$bar->gelar."</td>
                          <td>".$bar->nilai."</td>
                          <td>".$bar->keterangan."</td>
                        </tr>";	 	
         }	 
echo"</table>
         </fieldset>
         <fieldset><legend>EXTERNAL ".$_SESSION['lang']['kursus']."</legend>
         <table class=standard cellspacing=1 width=100% bgcolor=#A3D988 cellpadding=2>
         <tr class=rowheader>
          <td>No.</td>
          <td>".$_SESSION['lang']['jeniskursus']."</td>			  
          <td>".$_SESSION['lang']['legend']."</td>
          <td>".$_SESSION['lang']['penyelenggara']."</td>			  
          <td>".$_SESSION['lang']['bulanmasuk']."</td>			  
          <td>".$_SESSION['lang']['bulankeluar']."</td>
          <td>".$_SESSION['lang']['sertifikat']."</td>
         </tr>
     ";
         $str="select *,case sertifikat when 0 then 'N' else 'Y' end as bersertifikat 
               from ".$dbname.".sdm_karyawantraining
                        where karyawanid=".$karyawanid." 
                        order by bulanmulai desc";	
         $res=mysql_query($str);
         $no=0;
         while($bar=mysql_fetch_object($res))
         {
         $no+=1;	
         echo"	  <tr class=rowcontent>
                          <td class=firsttd>".$no."</td>
                          <td>".$bar->jenistraining."</td>			  
                          <td>".$bar->judultraining."</td>
                          <td>".$bar->penyelenggara."</td>			  
                          <td>".$bar->bulanmulai."</td>			  
                          <td>".$bar->bulanselesai."</td>
                          <td>".$bar->bersertifikat."</td>
                        </tr>";	 	
         }	 
echo"</table>
     </fieldset>
         <fieldset><legend>".$_SESSION['lang']['keluarga']."</legend>
         <table class=standard cellspacing=1 width=100% bgcolor=#A3D988 cellpadding=2>
         <tr class=rowheader>
          <td>No.</td>
          <td>".$_SESSION['lang']['nama']."</td>			  
          <td>".$_SESSION['lang']['jeniskelamin']."</td>
          <td>".$_SESSION['lang']['hubungan']."</td>			  
          <td>".$_SESSION['lang']['tanggallahir']."</td>			  
          <td>".$_SESSION['lang']['statusperkawinan']."</td>
                  <td>".$_SESSION['lang']['umur']."</td>
          <td>".$_SESSION['lang']['edulevel']."</td>
          <td>".$_SESSION['lang']['pekerjaan']."</td>
          <td>".$_SESSION['lang']['telp']."</td>
          <td>".$_SESSION['lang']['email']."</td>
          <td>".$_SESSION['lang']['tanggungan']."</td>
         </tr>
         ";
                 $str="select a.*,case a.tanggungan when 0 then 'N' else 'Y' end as tanggungan1, 
                       b.kelompok,COALESCE(ROUND(DATEDIFF('".date('Y-m-d')."',a.tanggallahir)/365.25,1),0) as umur
                           from ".$dbname.".sdm_karyawankeluarga a,".$dbname.".sdm_5pendidikan b
                                where a.karyawanid=".$karyawanid." 
                                and a.levelpendidikan=b.levelpendidikan
                                order by hubungankeluarga";	
                 $res=mysql_query($str);
                 $no=0;
                 while($bar=mysql_fetch_object($res))
                 {
					$val=trim($bar->hubungankeluarga);
                    if($_SESSION['language']=='EN'){
                                    switch($bar->hubungankeluarga){
                                      case'Pasangan':
                                          $val='Couple';
                                          break;
                                      case'Anak':
                                          $val='Child';
                                          break;
                                      case'Ibu':
                                          $val='Mother';
                                          break;
                                      case'Bapak':
                                          $val='Father';
                                          break;
                                      case'Adik':
                                          $val='Younger brother/sister';
                                          break;        
                                      case'Kakak':
                                          $val='Older brother/sister';
                                          break;      
                                      case'Ibu Mertua':
                                          $val='Monther-in-law';
                                          break;   
                                      case'Bapak Mertua':
                                          $val='Father-in-law';
                                          break;   
                                      case'Sepupu':
                                          $val='Cousin';
                                          break;  
                                      case'Ponakan':
                                          $val='Nephew';
                                          break;                                
                                      default:
                                          $val='Foster child';
                                          break;                         
                                 }
                }  
				    $gal=trim($bar->status);
                   if($_SESSION['language']=='EN' && $bar->status=='Kawin')
                       $gal='Married';
                   if($_SESSION['language']=='EN' && ($bar->status=='Bujang' or $bar->status=='Lajang'))
                          $gal='Single';            
                   
                 $no+=1;	
                 echo"	  <tr class=rowcontent>
                                  <td>".$no."</td>
                                  <td>".$bar->nama."</td>			  
                                  <td>".$bar->jeniskelamin."</td>
                                  <td>".$val."</td>			  
                                  <td>".$bar->tempatlahir.",".tanggalnormal_hrd($bar->tanggallahir)."</td>			  
                                  <td>".$gal."</td>
                                  <td>".$bar->umur." Yrs</td>
                                  <td>".$bar->kelompok."</td>
                                  <td>".$bar->pekerjaan."</td>
                                  <td>".$bar->telp."</td>
                                  <td>".$bar->email."</td>
                                  <td>".$bar->tanggungan1."</td>
                                </tr>";	 	
                 }	 
echo"</table>
     </fieldset>
         <fieldset><legend>".$_SESSION['lang']['alamat']."</legend>
         <table class=standard cellspacing=1 width=100% bgcolor=#A3D988 cellpadding=2>
         <tr class=rowheader>
          <td>No.</td>
          <td>".$_SESSION['lang']['alamat']."</td>			  
          <td>".$_SESSION['lang']['kota']."</td>
          <td>".$_SESSION['lang']['province']."</td>			  
          <td>".$_SESSION['lang']['kodepos']."</td>			  
          <td>".$_SESSION['lang']['emplasmen']."</td>
          <td>".$_SESSION['lang']['status']."</td>
         </tr>
         ";
                 $str="select *,case aktif when 1 then 'Yes' when 0 then 'No' end as status from ".$dbname.".sdm_karyawanalamat where karyawanid=".$karyawanid." order by nomor desc";
                 $res=mysql_query($str);
                 $no=0;
                 while($bar=mysql_fetch_object($res))
                 {
                 $no+=1;	
                 echo"	  <tr class=rowcontent>
                                  <td class=firsttd>".$no."</td>
                                  <td>".$bar->alamat."</td>			  
                                  <td>".$bar->kota."</td>
                                  <td>".$bar->provinsi."</td>			  
                                  <td>".$bar->kodepos."</td>			  
                                  <td>".$bar->emplasemen."</td>
                                  <td>".$bar->status."</td>
                                </tr>";	 	
                 }	 
                 
        if($_SESSION['language']=='EN'){
                $gg='History of reprimands';
        }else{
              $gg='Riwayat Teguran dan SP';
        }
        
echo"</table>
         </fieldset>
         <fieldset><legend>".$gg."</legend>
         <table class=standard cellspacing=1 width=100% bgcolor=#A3D988 cellpadding=2>
         <tr class=rowheader>
          <td>No.</td>
          <td>".$_SESSION['lang']['jenissp']."</td>			  
          <td>".$_SESSION['lang']['tanggalsurat']."</td>
          <td>".$_SESSION['lang']['masaberlaku']." (".$_SESSION['lang']['bulan'].")</td>			  
          <td>".$_SESSION['lang']['pelanggaran']."</td>			  
          <td>".$_SESSION['lang']['penandatangan']."</td>
          <td>".$_SESSION['lang']['functionname']."</td>
         </tr>
         ";
                 $str="select * from ".$dbname.".sdm_suratperingatan where karyawanid=".$karyawanid." order by tanggal desc";
                 $res=mysql_query($str);
                 $no=0;
                 while($bar=mysql_fetch_object($res))
                 {
                 $no+=1;	
                 echo"	  <tr class=rowcontent>
                                  <td class=firsttd>".$bar->nomor."</td>
                                  <td>".$bar->jenissp."</td>			  
                                  <td>".tanggalnormal_hrd($bar->tanggal)."</td>
                                  <td align=right>".$bar->masaberlaku."</td>			  
                                  <td>".$bar->pelanggaran."</td>			  
                                  <td>".$bar->penandatangan."</td>
                                  <td>".$bar->jabatan."</td>
                                </tr>";	 	
                 }	 

                 $str="select * from ".$dbname.".sdm_5jabatan";
                 $res=mysql_query($str);
                 $no=0;
                 while($bar=mysql_fetch_object($res))
                 {
                        $kamusjabatan[$bar->kodejabatan]=$bar->namajabatan;

                 }
        if($_SESSION['language']=='EN'){
                $gg='Promotion history';
        }else{
              $gg='Riwayat Mutasi/Promosi/Demosi';
        }                 
echo"</table>
         </fieldset>
         <fieldset><legend>".$gg."</legend>
         <table class=standard cellspacing=1 width=100% bgcolor=#A3D988 cellpadding=2>
         <tr class=rowheader>
          <td>No.</td>
          <td>".$_SESSION['lang']['tipetransaksi']."</td>
          <td>".$_SESSION['lang']['tanggalsurat']."</td>
          <td>".$_SESSION['lang']['tanggalberlaku']."</td>			  
          <td>".$_SESSION['lang']['dari']."</td>			  
          <td>".$_SESSION['lang']['ke']."</td>			  
          <td>".$_SESSION['lang']['penandatangan']."</td>
         </tr>
         ";
                 $str="select * from ".$dbname.".sdm_riwayatjabatan where karyawanid=".$karyawanid." order by tanggalsk desc";
                 $res=mysql_query($str);
                 $no=0;
                 while($bar=mysql_fetch_object($res))
                 {
                 $no+=1;	
                 echo"	  <tr class=rowcontent>
                                  <td class=firsttd>".$bar->nomorsk."</td>
                                  <td>".$bar->tipesk."</td>			  
                                  <td>".tanggalnormal_hrd($bar->tanggalsk)."</td>
                                  <td>".tanggalnormal_hrd($bar->mulaiberlaku)."</td>";
                                  if($bar->tipesk=='Mutasi'){
                                      echo"<td>".$bar->darikodeorg."</td>			  
                                      <td>".$bar->kekodeorg."</td>";			                                        
                                  }else
                                  if($bar->tipesk=='Promosi'){
                                      echo"<td>".$kamusjabatan[$bar->darikodejabatan]." (".$bar->darikodegolongan.")</td>			  
                                      <td>".$kamusjabatan[$bar->kekodejabatan]." (".$bar->kekodegolongan.") </td>";			                                        
                                  }else
                                  if($bar->tipesk=='Demosi'){
                                      echo"<td>".$kamusjabatan[$bar->darikodejabatan]." (".$bar->darikodegolongan.")</td>			  
                                      <td>".$kamusjabatan[$bar->kekodejabatan]." (".$bar->kekodegolongan.") </td>";			                                        
                                  }

                                  echo"<td>".$bar->namadireksi."</td>
                                </tr>";	 	
                 }	 
echo"</table>
         </fieldset>";
$sJabat="select * from ".$dbname.".sdm_5matriktraining where 1";
$qJabat=mysql_query($sJabat) or die(mysql_error());
while($rJabat=mysql_fetch_assoc($qJabat))
{
    $kamusKategori[$rJabat['matrixid']]=$rJabat['kategori'];
    $kamusTopik[$rJabat['matrixid']]=$rJabat['topik'];
}

        if($_SESSION['language']=='EN'){
                $gg='Training provided By '.$namapt;
        }else{             
                $gg='Training di '.$namapt;
        }
        
echo"<fieldset><legend>".$gg."</legend>
         <table class=standard cellspacing=1 width=100% bgcolor=#A3D988 cellpadding=2>
         <tr class=rowheader>
          <td>".$_SESSION['lang']['kategori']."</td>
          <td>".$_SESSION['lang']['topik']."</td>
          <td>".$_SESSION['lang']['tanggalmulai']."</td>			  
          <td>".$_SESSION['lang']['tanggalsampai']."</td>			  
          <td>".$_SESSION['lang']['catatan']."</td>			  
         </tr>
         ";
    $str="select * from ".$dbname.".sdm_matriktraining
        where karyawanid = '".$karyawanid."'
        ";
                 $res=mysql_query($str);
                 $no=0;
                 while($bar=mysql_fetch_object($res))
                 {
                 $no+=1;	
                 echo"	  <tr class=rowcontent>
                                  <td class=firsttd>".$kamusKategori[$bar->matrikxid]."</td>
                                  <td>".$kamusTopik[$bar->matrikxid]."</td>			  
                                  <td>".tanggalnormal_hrd($bar->tanggaltraining)."</td>
                                  <td>".tanggalnormal_hrd($bar->sampaitanggal)."</td>
                                  <td>".$bar->catatan."</td>
                                </tr>";	 	
                 }	 
echo"</table>
         </fieldset>";

        /*if($_SESSION['language']=='EN'){
                $gg='Standard Training';
        }else{           
                $gg='Standard training yang harus diikuti';
        }*/
        $gg=$_SESSION['lang']['standarpelatihan'];
echo"<fieldset><legend>".$gg."</legend>
         <table class=standard cellspacing=1 width=100% bgcolor=#A3D988 cellpadding=2>
         <tr class=rowheader>
          <td>".$_SESSION['lang']['jabatan']."</td>
          <td>".$_SESSION['lang']['kategori']."</td>
          <td>".$_SESSION['lang']['topik']."</td>			  
         </tr>
         ";
    $str="select * from ".$dbname.".sdm_5matriktraining
        where kodejabatan = '".$jabatanku."'
        ";
                 $res=mysql_query($str);
                 $no=0;
                 while($bar=mysql_fetch_object($res))
                 {
                 $no+=1;	
                 echo"	  <tr class=rowcontent>
                                  <td class=firsttd>".$kamusjabatan[$bar->kodejabatan]."</td>
                                  <td>".$bar->kategori."</td>			  
                                  <td>".$bar->topik."</td>
                                </tr>";	 	
                 }	 
echo"</table>
         </fieldset>";
$sJabat="select * from ".$dbname.".log_5supplier where 1";
$qJabat=mysql_query($sJabat) or die(mysql_error());
while($rJabat=mysql_fetch_assoc($qJabat))
{
    $kamusSup[$rJabat['supplierid']]=$rJabat['namasupplier'];
}

        if($_SESSION['language']=='EN'){
                $gg='Additional Training';
        }else{           
                $gg='Additional training yang sudah diikuti';
        }
        
echo"<fieldset><legend>".$gg."</legend>
         <table class=standard cellspacing=1 width=100% bgcolor=#A3D988 cellpadding=2>
         <tr class=rowheader>
          <td>".$_SESSION['lang']['namatraining']."</td>
          <td>".$_SESSION['lang']['penyelenggara']."</td>
          <td>".$_SESSION['lang']['tanggalmulai']."</td>			  
          <td>".$_SESSION['lang']['tanggalsampai']."</td>			  
         </tr>
         ";
    $str="select * from ".$dbname.".sdm_5training
        where karyawanid = '".$karyawanid."' and sthrd = '1'
        ";
                 $res=mysql_query($str);
                 $no=0;
                 while($bar=mysql_fetch_object($res))
                 {
                 $no+=1;	
                 echo"	  <tr class=rowcontent>
                                  <td class=firsttd>".$bar->namatraining."</td>
                                  <td>".$bar->penyelenggara."</td>			  
                                  <td>".tanggalnormal_hrd($bar->tglmulai)."</td>
                                  <td>".tanggalnormal_hrd($bar->tglselesai)."</td>
                                </tr>";	 	
                 }	 
echo"</table>
         </fieldset>";

         echo"</div>";
?>
