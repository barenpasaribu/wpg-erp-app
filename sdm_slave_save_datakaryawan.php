<?php







require_once 'master_validation.php';

require_once 'config/connection.php';

require_once 'lib/eagrolib.php';

$nik = $_POST['nik'];

$namakaryawan = $_POST['namakaryawan'];

$tempatlahir = $_POST['tempatlahir'];

$tanggallahir = tanggalsystem($_POST['tanggallahir']);

$noktp = $_POST['noktp'];

$nopassport = $_POST['nopassport'];

$npwp = $_POST['npwp'];

$kodepos = $_POST['kodepos'];

$alamataktif = $_POST['alamataktif'];

$kota = $_POST['kota'];

$noteleponrumah = $_POST['noteleponrumah'];

$nohp = $_POST['nohp'];

$norekeningbank = $_POST['norekeningbank'];

$namabank = $_POST['namabank'];

$alokasi = $_POST['alokasi'];

$jms = $_POST['jms'];

$bpjskes = $_POST['bpjskes'];

$tanggalmasuk = tanggalsystem($_POST['tanggalmasuk']);

if ('-' == $_POST['tanggalkeluar'] || '' == $_POST['tanggalkeluar'] || null == $_POST['tanggalkeluar']) {

//    $_POST['tanggalkeluar'] = '00-00-0000';
    $_POST['tanggalkeluar'] = null;
}



$tanggalkeluar = tanggalsystem($_POST['tanggalkeluar']);

$jumlahanak = $_POST['jumlahanak'];

if ('' == $jumlahanak) {

    $jumlahanak = 0;

}



$jumlahtanggungan = $_POST['jumlahtanggungan'];

if ('' == $jumlahtanggungan) {

    $jumlahtanggungan = 0;

}



if ('' == $_POST['tanggalmenikah'] || '-' == $_POST['tanggalmenikah'] || null == $_POST['tanggalmenikah']) {

//    $_POST['tanggalmenikah'] = '0000-00-00';
    $_POST['tanggalmenikah'] = null;
}


$tanggalmenikah = tanggalsystem($_POST['tanggalmenikah']);

$notelepondarurat = $_POST['notelepondarurat'];

$natura = $_POST['natura'];

$email = $_POST['email'];

$jeniskelamin = $_POST['jeniskelamin'];

$agama = $_POST['agama'];

$bagian = $_POST['bagian'];

$kodejabatan = $_POST['kodejabatan'];

$kodegolongan = $_POST['kodegolongan'];

$lokasitugas = $_POST['lokasitugas'];

$kodeorganisasi = $_POST['kodeorganisasi'];

$tipekaryawan = $_POST['tipekaryawan'];

$warganegara = $_POST['warganegara'];

$lokasipenerimaan = $_POST['lokasipenerimaan'];

$statuspajak = $_POST['statuspajak'];

$provinsi = $_POST['provinsi'];

$sistemgaji = $_POST['sistemgaji'];

$golongandarah = $_POST['golongandarah'];

$statusperkawinan = $_POST['statusperkawinan'];

$levelpendidikan = $_POST['levelpendidikan'];

$method = $_POST['method'];

$karyawanid = $_POST['karyawanid'];

$subbagian = $_POST['subbagian'];

$catu = $_POST['catu'];

$kecamatan = $_POST['kecamatan'];

$desa = $_POST['desa'];

$pangkat = $_POST['pangkat'];

if ('0' == $subbagian) {

    $subbagian = '';

}



switch ($method) {

    case 'delete':

        $strx = 'delete from '.$dbname.'.datakaryawan where karyawanid='.$karyawanid;



        break;
    case 'CekBlacklist':

         $iCekBlack = 'select * from '.$dbname.".datakaryawan where namakaryawan='".$namakaryawan."' and tempatlahir='".$tempatlahir."' and tanggallahir='".$tanggallahir."' and isduplicate=0";

        $nCekBlack = mysql_query($iCekBlack);

        $ada = true;
        $karyawanid='';

        while ($dCekBlack = mysql_fetch_assoc($nCekBlack)) {

            if (true == $ada) {

                $karyawanid= $dCekBlack['karyawanid'];

            }

        }
        

        if($karyawanid==''){
            echo 'lanjut';
            exit();
        }else{
        $strx = 'select a.* from '.$dbname.".sdm_exitinterview a,datakaryawan b where a.karyawanid=b.karyawanid and b.namakaryawan='".$namakaryawan."' and b.tempatlahir='".$tempatlahir."' and b.tanggallahir='".$tanggallahir."' and b.isduplicate=0 ";
       
      
        $res0 = mysql_query($strx);
        $alasan = '';
        $jlhbrs = mysql_num_rows($res0);
         while ($bar0 = mysql_fetch_object($res0)) {
            $alasan = $bar0->alasan;
           
        }
            if($jlhbrs==0){
                echo 'lanjut';
            }else{
                echo 'Atas nama : '.$namakaryawan.' dengan tempat dan tanggal lahir : '.$tempatlahir.','.$tanggallahir.' keluar dari perusahaan dengan alasan : '.$alasan.' ,';
            }

         }   
         



        break;

    case 'update':

	/*

        if (5 == $tipekaryawan && substr($kodegolongan, 0, 1) < 1) {

            exit('error: Silakan cek kodegolongan');

        }

	*/

        if ('' == $tanggalmenikah && '' == $tanggalkeluar) {

            $strx = 'update '.$dbname.".datakaryawan set\r\n\t\t\t       `nik`\t\t\t='".$nik."',\r\n                                                        `namakaryawan`\t='".$namakaryawan."',\r\n                                                        `tempatlahir`\t='".$tempatlahir."',\r\n                                                        `tanggallahir`\t=".$tanggallahir.",\r\n\t\t\t       `warganegara`            ='".$warganegara."',\r\n                                                       `jeniskelamin`\t='".$jeniskelamin."',\r\n\t\t\t       `statusperkawinan`       ='".$statusperkawinan."',\r\n\r\n\t\t\t       `agama`\t\t\t='".$agama."',\r\n\t\t\t\t   `golongandarah`\t='".$golongandarah."',\r\n\t\t\t       `levelpendidikan`        =".$levelpendidikan.",\r\n\t\t\t\t   `alamataktif`\t='".$alamataktif."',\r\n\t\t\t       `provinsi`\t\t='".$provinsi."',\r\n\t\t\t\t   `kota`\t\t='".$kota."',\r\n\t\t\t\t   `kodepos`\t\t='".$kodepos."',\r\n\t\t\t       `noteleponrumah`         ='".$noteleponrumah."',\r\n\t\t\t\t   `nohp`\t\t='".$nohp."',\r\n\t\t\t       `norekeningbank`         ='".$norekeningbank."',\r\n\t\t\t\t   `namabank`\t\t='".$namabank."',\r\n\t\t\t       `sistemgaji`\t\t='".$sistemgaji."',\r\n\t\t\t\t   `nopaspor`\t\t='".$nopaspor."',\r\n\t\t\t       `noktp`\t\t\t='".$noktp."',\r\n\t\t\t\t   `notelepondarurat`   ='".$notelepondarurat."',\r\n\t\t\t       `tanggalmasuk`           =".$tanggalmasuk.",\r\n\r\n\t\t\t       `tipekaryawan`           =".$tipekaryawan.",\r\n\t\t\t\t   `jumlahanak`\t\t=".$jumlahanak.",\r\n\t\t\t       `jumlahtanggungan`       =".$jumlahtanggungan.",\r\n\t\t\t\t   `statuspajak`\t='".$statuspajak."',\r\n\t\t\t       `npwp`\t\t\t='".$npwp."',\r\n\t\t\t\t   `lokasipenerimaan`   ='".$lokasipenerimaan."',\r\n\t\t\t\t   `kodeorganisasi`\t='".$kodeorganisasi."',\r\n\t\t\t       `bagian`\t\t\t='".$bagian."',\r\n\t\t\t\t   `kodejabatan`\t=".$kodejabatan.",\r\n\t\t\t\t   `kodegolongan`\t='".$kodegolongan."',\r\n\t\t\t       `lokasitugas`            ='".$lokasitugas."',\r\n\t\t\t\t   `email`\t\t='".$email."',\r\n\t\t\t\t   `alokasi`\t\t=".$alokasi.",\r\n\t\t\t\t   `subbagian`\t\t='".$subbagian."',\r\n                   `jms`            ='".$jms."' ,\r\n                   `idmedical`            ='".$bpjskes."' ,\r\n                   `kodecatu`       ='".$catu."',\r\n                   `statpremi`      ='".$_POST['statPremi']."',\r\n\t\t\t\t   `kecamatan`      ='".$_POST['kecamatan']."',\r\n\t\t\t\t   

            `desa`           ='".$_POST['desa']."',\r\n\t\t\t\t   

            `isduplicate`           ='".$_POST['isduplicate']."',\r\n\t\t\t\t   

            `pangkat`        ='".$_POST['pangkat']."'\r\n\t\t\t\t   

            where karyawanid=".$karyawanid;

        } else {

            if ('' == $tanggalmenikah) {

                $strx = 'update '.$dbname.".datakaryawan set\r\n\t\t\t       `nik`\t\t\t='".$nik."',\r\n                                                        `namakaryawan`\t='".$namakaryawan."',\r\n                                                        `tempatlahir`\t='".$tempatlahir."',\r\n                                                        `tanggallahir`\t=".$tanggallahir.",\r\n\t\t\t       `warganegara`            ='".$warganegara."',\r\n                                                       `jeniskelamin`\t='".$jeniskelamin."',\r\n\t\t\t       `statusperkawinan`       ='".$statusperkawinan."',\r\n\r\n\t\t\t       `agama`\t\t\t='".$agama."',\r\n\t\t\t\t   `golongandarah`\t='".$golongandarah."',\r\n\t\t\t       `levelpendidikan`        =".$levelpendidikan.",\r\n\t\t\t\t   `alamataktif`\t='".$alamataktif."',\r\n\t\t\t       `provinsi`\t\t='".$provinsi."',\r\n\t\t\t\t   `kota`\t\t='".$kota."',\r\n\t\t\t\t   `kodepos`\t\t='".$kodepos."',\r\n\t\t\t       `noteleponrumah`         ='".$noteleponrumah."',\r\n\t\t\t\t   `nohp`\t\t='".$nohp."',\r\n\t\t\t       `norekeningbank`         ='".$norekeningbank."',\r\n\t\t\t\t   `namabank`\t\t='".$namabank."',\r\n\t\t\t       `sistemgaji`\t\t='".$sistemgaji."',\r\n\t\t\t\t   `nopaspor`\t\t='".$nopaspor."',\r\n\t\t\t       `noktp`\t\t\t='".$noktp."',\r\n\t\t\t\t   `notelepondarurat`   ='".$notelepondarurat."',\r\n\t\t\t       `tanggalmasuk`           =".$tanggalmasuk.",\r\n                   `tanggalkeluar`\t=".$tanggalkeluar.",\r\n\t\t\t       `tipekaryawan`           =".$tipekaryawan.",\r\n\t\t\t\t   `jumlahanak`\t\t=".$jumlahanak.",\r\n\t\t\t       `jumlahtanggungan`       =".$jumlahtanggungan.",\r\n\t\t\t\t   `statuspajak`\t='".$statuspajak."',\r\n\t\t\t       `npwp`\t\t\t='".$npwp."',\r\n\t\t\t\t   `lokasipenerimaan`   ='".$lokasipenerimaan."',\r\n\t\t\t\t   `kodeorganisasi`\t='".$kodeorganisasi."',\r\n\t\t\t       `bagian`\t\t\t='".$bagian."',\r\n\t\t\t\t   `kodejabatan`\t=".$kodejabatan.",\r\n\t\t\t\t   `kodegolongan`\t='".$kodegolongan."',\r\n\t\t\t       `lokasitugas`            ='".$lokasitugas."',\r\n\t\t\t\t   `email`\t\t='".$email."',\r\n\t\t\t\t   `alokasi`\t\t=".$alokasi.",\r\n\t\t\t\t   `subbagian`\t\t='".$subbagian."',\r\n                   `jms`            ='".$jms."' ,\r\n                   `idmedical`            ='".$bpjskes."' ,\r\n                   `kodecatu`       ='".$catu."',\r\n                   `statpremi`      ='".$_POST['statPremi']."',\r\n\t\t\t\t   `kecamatan`      ='".$_POST['kecamatan']."',\r\n\t\t\t\t   

                `desa`           ='".$_POST['desa']."',\r\n\t\t\t\t   

                `isduplicate`           ='".$_POST['isduplicate']."',\r\n\t\t\t\t   

                `pangkat`        ='".$_POST['pangkat']."'\r\n\t\t\t\t   

                where karyawanid=".$karyawanid;

            } else {

                if ('' == $tanggalkeluar) {

                    $strx = 'update '.$dbname.".datakaryawan set\r\n\t\t\t       `nik`\t\t\t='".$nik."',\r\n                                                        `namakaryawan`\t='".$namakaryawan."',\r\n                                                        `tempatlahir`\t='".$tempatlahir."',\r\n                                                        `tanggallahir`\t=".$tanggallahir.",\r\n\t\t\t       `warganegara`            ='".$warganegara."',\r\n                                                       `jeniskelamin`\t='".$jeniskelamin."',\r\n\t\t\t       `statusperkawinan`       ='".$statusperkawinan."',\r\n                    `tanggalmenikah`\t=".$tanggalmenikah.",\r\n\t\t\t       `agama`\t\t\t='".$agama."',\r\n\t\t\t\t   `golongandarah`\t='".$golongandarah."',\r\n\t\t\t       `levelpendidikan`        =".$levelpendidikan.",\r\n\t\t\t\t   `alamataktif`\t='".$alamataktif."',\r\n\t\t\t       `provinsi`\t\t='".$provinsi."',\r\n\t\t\t\t   `kota`\t\t='".$kota."',\r\n\t\t\t\t   `kodepos`\t\t='".$kodepos."',\r\n\t\t\t       `noteleponrumah`         ='".$noteleponrumah."',\r\n\t\t\t\t   `nohp`\t\t='".$nohp."',\r\n\t\t\t       `norekeningbank`         ='".$norekeningbank."',\r\n\t\t\t\t   `namabank`\t\t='".$namabank."',\r\n\t\t\t       `sistemgaji`\t\t='".$sistemgaji."',\r\n\t\t\t\t   `nopaspor`\t\t='".$nopaspor."',\r\n\t\t\t       `noktp`\t\t\t='".$noktp."',\r\n\t\t\t\t   `notelepondarurat`   ='".$notelepondarurat."',\r\n\t\t\t       `tanggalmasuk`           =".$tanggalmasuk.",\r\n                   \r\n\t\t\t       `tipekaryawan`           =".$tipekaryawan.",\r\n\t\t\t\t   `jumlahanak`\t\t=".$jumlahanak.",\r\n\t\t\t       `jumlahtanggungan`       =".$jumlahtanggungan.",\r\n\t\t\t\t   `statuspajak`\t='".$statuspajak."',\r\n\t\t\t       `npwp`\t\t\t='".$npwp."',\r\n\t\t\t\t   `lokasipenerimaan`   ='".$lokasipenerimaan."',\r\n\t\t\t\t   `kodeorganisasi`\t='".$kodeorganisasi."',\r\n\t\t\t       `bagian`\t\t\t='".$bagian."',\r\n\t\t\t\t   `kodejabatan`\t=".$kodejabatan.",\r\n\t\t\t\t   `kodegolongan`\t='".$kodegolongan."',\r\n\t\t\t       `lokasitugas`            ='".$lokasitugas."',\r\n\t\t\t\t   `email`\t\t='".$email."',\r\n\t\t\t\t   `alokasi`\t\t=".$alokasi.",\r\n\t\t\t\t   `subbagian`\t\t='".$subbagian."',\r\n                   `jms`            ='".$jms."' ,\r\n                   `idmedical`            ='".$bpjskes."' ,\r\n                   `kodecatu`       ='".$catu."',\r\n                   `statpremi`      ='".$_POST['statPremi']."',\r\n\t\t\t\t   `kecamatan`      ='".$_POST['kecamatan']."',\r\n\t\t\t\t   

                    `desa`           ='".$_POST['desa']."',\r\n\t\t\t\t   

                    `isduplicate`           ='".$_POST['isduplicate']."',\r\n\t\t\t\t   

                    `pangkat`        ='".$_POST['pangkat']."'\r\n\t\t\t\t   

                    where karyawanid=".$karyawanid;

                } else {

                    $strx = 'update '.$dbname.".datakaryawan set\r\n\t\t\t       `nik`\t\t\t='".$nik."',\r\n                                                        `namakaryawan`\t='".$namakaryawan."',\r\n                                                        `tempatlahir`\t='".$tempatlahir."',\r\n                                                        `tanggallahir`\t=".$tanggallahir.",\r\n\t\t\t       `warganegara`            ='".$warganegara."',\r\n                                                       `jeniskelamin`\t='".$jeniskelamin."',\r\n\t\t\t       `statusperkawinan`       ='".$statusperkawinan."',\r\n\t\t\t\t   `tanggalmenikah`\t=".$tanggalmenikah.",\r\n\t\t\t       `agama`\t\t\t='".$agama."',\r\n\t\t\t\t   `golongandarah`\t='".$golongandarah."',\r\n\t\t\t       `levelpendidikan`        =".$levelpendidikan.",\r\n\t\t\t\t   `alamataktif`\t='".$alamataktif."',\r\n\t\t\t       `provinsi`\t\t='".$provinsi."',\r\n\t\t\t\t   `kota`\t\t='".$kota."',\r\n\t\t\t\t   `kodepos`\t\t='".$kodepos."',\r\n\t\t\t       `noteleponrumah`         ='".$noteleponrumah."',\r\n\t\t\t\t   `nohp`\t\t='".$nohp."',\r\n\t\t\t       `norekeningbank`         ='".$norekeningbank."',\r\n\t\t\t\t   `namabank`\t\t='".$namabank."',\r\n\t\t\t       `sistemgaji`\t\t='".$sistemgaji."',\r\n\t\t\t\t   `nopaspor`\t\t='".$nopaspor."',\r\n\t\t\t       `noktp`\t\t\t='".$noktp."',\r\n\t\t\t\t   `notelepondarurat`   ='".$notelepondarurat."',\r\n\t\t\t       `tanggalmasuk`           =".$tanggalmasuk.",\r\n\t\t\t\t   `tanggalkeluar`\t=".$tanggalkeluar.",\r\n\t\t\t       `tipekaryawan`           =".$tipekaryawan.",\r\n\t\t\t\t   `jumlahanak`\t\t=".$jumlahanak.",\r\n\t\t\t       `jumlahtanggungan`       =".$jumlahtanggungan.",\r\n\t\t\t\t   `statuspajak`\t='".$statuspajak."',\r\n\t\t\t       `npwp`\t\t\t='".$npwp."',\r\n\t\t\t\t   `lokasipenerimaan`   ='".$lokasipenerimaan."',\r\n\t\t\t\t   `kodeorganisasi`\t='".$kodeorganisasi."',\r\n\t\t\t       `bagian`\t\t\t='".$bagian."',\r\n\t\t\t\t   `kodejabatan`\t=".$kodejabatan.",\r\n\t\t\t\t   `kodegolongan`\t='".$kodegolongan."',\r\n\t\t\t       `lokasitugas`            ='".$lokasitugas."',\r\n\t\t\t\t   `email`\t\t='".$email."',\r\n\t\t\t\t   `alokasi`\t\t=".$alokasi.",\r\n\t\t\t\t   `subbagian`\t\t='".$subbagian."',\r\n                   `jms`            ='".$jms."' ,\r\n                   `idmedical`            ='".$bpjskes."' ,\r\n                   `kodecatu`       ='".$catu."',\r\n                   `statpremi`      ='".$_POST['statPremi']."',\r\n\t\t\t\t   `kecamatan`      ='".$_POST['kecamatan']."',\r\n\t\t\t\t   

                    `desa`           ='".$_POST['desa']."',\r\n\t\t\t\t   

                    `isduplicate`           ='".$_POST['isduplicate']."',\r\n\t\t\t\t   

                    `pangkat`        ='".$_POST['pangkat']."'\r\n\t\t\t\t   

                    where karyawanid=".$karyawanid;

                }

            }

        }



        break;

    case 'insert':

        /*

		if (5 == $tipekaryawan && substr($kodegolongan, 0, 1) < 1) {

            exit('error: Silakan cek kodegolongan');

        }

		*/

		

        $iCek = 'select nik from '.$dbname.".datakaryawan where nik='".$nik."'";

        $nCek = mysql_query($iCek);

        $ada = true;

        while ($dCek = mysql_fetch_assoc($nCek)) {

            if (true == $ada) {

                echo 'warning : NIK for '.$nik.' already exist';

                exit();

            }

        }
       
        if ('' == $tanggalmenikah && '' == $tanggalkeluar) {

            $strx = 'insert into '.$dbname.".datakaryawan(\r\n\t\t\t\t  `nik`,`namakaryawan`,\r\n\t\t\t\t  `tempatlahir`,`tanggallahir`,\r\n\t\t\t\t  `warganegara`,`jeniskelamin`,\r\n\t\t\t\t  `statusperkawinan`,\r\n\t\t\t\t  `agama`,`golongandarah`,\r\n\t\t\t\t  `levelpendidikan`,`alamataktif`,\r\n\t\t\t\t  `provinsi`,`kota`,`kodepos`,\r\n\t\t\t\t  `noteleponrumah`,`nohp`,\r\n\t\t\t\t  `norekeningbank`,`namabank`,\r\n\t\t\t\t  `sistemgaji`,`nopaspor`,\r\n\t\t\t\t  `noktp`,`notelepondarurat`,\r\n\t\t\t\t  `tanggalmasuk`,\r\n\t\t\t\t  `tipekaryawan`,`jumlahanak`,\r\n\t\t\t\t  `jumlahtanggungan`,`statuspajak`,\r\n\t\t\t\t  `npwp`,`lokasipenerimaan`,`kodeorganisasi`,\r\n\t\t\t\t  `bagian`,`kodejabatan`,`kodegolongan`,\r\n\t\t\t\t  `lokasitugas`,`email`,`alokasi`,`subbagian`,`jms`,\r\n\t\t\t\t  kodecatu,statpremi,kecamatan,desa,pangkat,idmedical)\r\n\t\t\t\t  values('".$nik."','".$namakaryawan."',\r\n\t\t\t\t  '".$tempatlahir."',".$tanggallahir.",\r\n\t\t\t\t  '".$warganegara."','".$jeniskelamin."',\r\n\t\t\t\t  '".$statusperkawinan."',\r\n\t\t\t\t  '".$agama."','".$golongandarah."',\r\n\t\t\t\t  ".$levelpendidikan.",'".$alamataktif."',\r\n\t\t\t\t  '".$provinsi."','".$kota."','".$kodepos."',\r\n\t\t\t\t  '".$noteleponrumah."','".$nohp."',\r\n\t\t\t\t  '".$norekeningbank."','".$namabank."',\r\n\t\t\t\t  '".$sistemgaji."','".$nopaspor."',\r\n\t\t\t\t  '".$noktp."','".$notelepondarurat."',\r\n\t\t\t\t  ".$tanggalmasuk.",\r\n\t\t\t\t  ".$tipekaryawan.','.$jumlahanak.",\r\n\t\t\t\t  ".$jumlahtanggungan.",'".$statuspajak."',\r\n\t\t\t\t  '".$npwp."','".$lokasipenerimaan."','".$kodeorganisasi."',\r\n\t\t\t\t  '".$bagian."',".$kodejabatan.",'".$kodegolongan."',\r\n\t\t\t\t  '".$lokasitugas."','".$email."',".$alokasi.",\r\n\t\t\t\t  '".$subbagian."','".$jms."','".$catu."','".$_POST['statPremi']."',\r\n\t\t\t\t  '".$kecamatan."','".$desa."','".$pangkat."','".$bpjskes."')";

        } else {

            if ('' == $tanggalmenikah) {

                $strx = 'insert into '.$dbname.".datakaryawan(\r\n\t\t\t  `nik`,`namakaryawan`,\r\n\t\t\t  `tempatlahir`,`tanggallahir`,\r\n\t\t\t  `warganegara`,`jeniskelamin`,\r\n\t\t\t  `statusperkawinan`,\r\n\t\t\t  `agama`,`golongandarah`,\r\n\t\t\t  `levelpendidikan`,`alamataktif`,\r\n\t\t\t  `provinsi`,`kota`,`kodepos`,\r\n\t\t\t  `noteleponrumah`,`nohp`,\r\n\t\t\t  `norekeningbank`,`namabank`,\r\n\t\t\t  `sistemgaji`,`nopaspor`,\r\n\t\t\t  `noktp`,`notelepondarurat`,\r\n\t\t\t  `tanggalmasuk`,`tanggalkeluar`,\r\n\t\t\t  `tipekaryawan`,`jumlahanak`,\r\n\t\t\t  `jumlahtanggungan`,`statuspajak`,\r\n\t\t\t  `npwp`,`lokasipenerimaan`,`kodeorganisasi`,\r\n\t\t\t  `bagian`,`kodejabatan`,`kodegolongan`,\r\n\t\t\t  `lokasitugas`,`email`,`alokasi`,`subbagian`,`jms`,\r\n\t\t\t  kodecatu,statpremi,kecamatan,desa,pangkat,idmedical)\r\n\t\t\tvalues('".$nik."','".$namakaryawan."',\r\n\t\t\t  '".$tempatlahir."',".$tanggallahir.",\r\n\t\t\t  '".$warganegara."','".$jeniskelamin."',\r\n\t\t\t  '".$statusperkawinan."',\r\n\t\t\t  '".$agama."','".$golongandarah."',\r\n\t\t\t  ".$levelpendidikan.",'".$alamataktif."',\r\n\t\t\t  '".$provinsi."','".$kota."','".$kodepos."',\r\n\t\t\t  '".$noteleponrumah."','".$nohp."',\r\n\t\t\t  '".$norekeningbank."','".$namabank."',\r\n\t\t\t  '".$sistemgaji."','".$nopaspor."',\r\n\t\t\t  '".$noktp."','".$notelepondarurat."',\r\n\t\t\t  ".$tanggalmasuk.','.$tanggalkeluar.",\r\n\t\t\t  ".$tipekaryawan.','.$jumlahanak.",\r\n\t\t\t  ".$jumlahtanggungan.",'".$statuspajak."',\r\n\t\t\t  '".$npwp."','".$lokasipenerimaan."','".$kodeorganisasi."',\r\n\t\t\t  '".$bagian."',".$kodejabatan.",'".$kodegolongan."',\r\n\t\t\t  '".$lokasitugas."','".$email."',".$alokasi.",\r\n\t\t\t  '".$subbagian."','".$jms."','".$catu."','".$_POST['statPremi']."',\r\n\t\t\t  '".$kecamatan."','".$desa."','".$pangkat."','".$bpjskes."')";

            } else {

                if ('' == $tanggalkeluar) {

                    $strx = 'insert into '.$dbname.".datakaryawan(\r\n\t\t\t  `nik`,`namakaryawan`,\r\n\t\t\t  `tempatlahir`,`tanggallahir`,\r\n\t\t\t  `warganegara`,`jeniskelamin`,\r\n\t\t\t  `statusperkawinan`,`tanggalmenikah`,\r\n\t\t\t  `agama`,`golongandarah`,\r\n\t\t\t  `levelpendidikan`,`alamataktif`,\r\n\t\t\t  `provinsi`,`kota`,`kodepos`,\r\n\t\t\t  `noteleponrumah`,`nohp`,\r\n\t\t\t  `norekeningbank`,`namabank`,\r\n\t\t\t  `sistemgaji`,`nopaspor`,\r\n\t\t\t  `noktp`,`notelepondarurat`,\r\n\t\t\t  `tanggalmasuk`,\r\n\t\t\t  `tipekaryawan`,`jumlahanak`,\r\n\t\t\t  `jumlahtanggungan`,`statuspajak`,\r\n\t\t\t  `npwp`,`lokasipenerimaan`,`kodeorganisasi`,\r\n\t\t\t  `bagian`,`kodejabatan`,`kodegolongan`,\r\n\t\t\t  `lokasitugas`,`email`,`alokasi`,`subbagian`,`jms`,\r\n\t\t\t  kodecatu,statpremi,kecamatan,desa,pangkat,idmedical)\r\n\t\t\tvalues('".$nik."','".$namakaryawan."',\r\n\t\t\t  '".$tempatlahir."',".$tanggallahir.",\r\n\t\t\t  '".$warganegara."','".$jeniskelamin."',\r\n\t\t\t  '".$statusperkawinan."',".$tanggalmenikah.",\r\n\t\t\t  '".$agama."','".$golongandarah."',\r\n\t\t\t  ".$levelpendidikan.",'".$alamataktif."',\r\n\t\t\t  '".$provinsi."','".$kota."','".$kodepos."',\r\n\t\t\t  '".$noteleponrumah."','".$nohp."',\r\n\t\t\t  '".$norekeningbank."','".$namabank."',\r\n\t\t\t  '".$sistemgaji."','".$nopaspor."',\r\n\t\t\t  '".$noktp."','".$notelepondarurat."',\r\n\t\t\t  ".$tanggalmasuk.",\r\n\t\t\t  ".$tipekaryawan.','.$jumlahanak.",\r\n\t\t\t  ".$jumlahtanggungan.",'".$statuspajak."',\r\n\t\t\t  '".$npwp."','".$lokasipenerimaan."','".$kodeorganisasi."',\r\n\t\t\t  '".$bagian."',".$kodejabatan.",'".$kodegolongan."',\r\n\t\t\t  '".$lokasitugas."','".$email."',".$alokasi.",\r\n\t\t\t  '".$subbagian."','".$jms."','".$catu."','".$_POST['statPremi']."',\r\n\t\t\t  '".$kecamatan."','".$desa."','".$pangkat."','".$bpjskes."')";

                } else {

                    $strx = 'insert into '.$dbname.".datakaryawan(\r\n\t\t\t  `nik`,`namakaryawan`,\r\n\t\t\t  `tempatlahir`,`tanggallahir`,\r\n\t\t\t  `warganegara`,`jeniskelamin`,\r\n\t\t\t  `statusperkawinan`,`tanggalmenikah`,\r\n\t\t\t  `agama`,`golongandarah`,\r\n\t\t\t  `levelpendidikan`,`alamataktif`,\r\n\t\t\t  `provinsi`,`kota`,`kodepos`,\r\n\t\t\t  `noteleponrumah`,`nohp`,\r\n\t\t\t  `norekeningbank`,`namabank`,\r\n\t\t\t  `sistemgaji`,`nopaspor`,\r\n\t\t\t  `noktp`,`notelepondarurat`,\r\n\t\t\t  `tanggalmasuk`,`tanggalkeluar`,\r\n\t\t\t  `tipekaryawan`,`jumlahanak`,\r\n\t\t\t  `jumlahtanggungan`,`statuspajak`,\r\n\t\t\t  `npwp`,`lokasipenerimaan`,`kodeorganisasi`,\r\n\t\t\t  `bagian`,`kodejabatan`,`kodegolongan`,\r\n\t\t\t  `lokasitugas`,`email`,`alokasi`,`subbagian`,`jms`,\r\n\t\t\t  kodecatu,statpremi,kecamatan,desa,pangkat,idmedical)\r\n\t\t\tvalues('".$nik."','".$namakaryawan."',\r\n\t\t\t  '".$tempatlahir."',".$tanggallahir.",\r\n\t\t\t  '".$warganegara."','".$jeniskelamin."',\r\n\t\t\t  '".$statusperkawinan."',".$tanggalmenikah.",\r\n\t\t\t  '".$agama."','".$golongandarah."',\r\n\t\t\t  ".$levelpendidikan.",'".$alamataktif."',\r\n\t\t\t  '".$provinsi."','".$kota."','".$kodepos."',\r\n\t\t\t  '".$noteleponrumah."','".$nohp."',\r\n\t\t\t  '".$norekeningbank."','".$namabank."',\r\n\t\t\t  '".$sistemgaji."','".$nopaspor."',\r\n\t\t\t  '".$noktp."','".$notelepondarurat."',\r\n\t\t\t  ".$tanggalmasuk.','.$tanggalkeluar.",\r\n\t\t\t  ".$tipekaryawan.','.$jumlahanak.",\r\n\t\t\t  ".$jumlahtanggungan.",'".$statuspajak."',\r\n\t\t\t  '".$npwp."','".$lokasipenerimaan."','".$kodeorganisasi."',\r\n\t\t\t  '".$bagian."',".$kodejabatan.",'".$kodegolongan."',\r\n\t\t\t  '".$lokasitugas."','".$email."',".$alokasi.",\r\n\t\t\t  '".$subbagian."','".$jms."','".$catu."','".$_POST['statPremi']."',\r\n\t\t\t  '".$kecamatan."','".$desa."','".$pangkat."','".$bpjskes."')";

                }

            }

        }



        break;

    default:

        $strx = 'select 1=1';



        break;

}
saveLog($strx);
if (mysql_query($strx)) {

    if ('delete' != $method || 'CekBlacklist' != $method) {

        $karid = '';

        $nama = '';

        $str = 'select karyawanid,namakaryawan from '.$dbname.".datakaryawan where\r\n\t\t\t      namakaryawan='".$namakaryawan."' and tanggallahir=".$tanggallahir;

        $res = mysql_query($str);

        while ($bar = mysql_fetch_object($res)) {

            $karid = $bar->karyawanid;

            $nama = $bar->namakaryawan;

        }
        if ('CekBlacklist' != $method) {
        echo "<?xml version='1.0' ?>\r\n\t\t\t     <karyawan>\r\n\t\t\t\t <karyawanid>".$karid."</karyawanid>\r\n\t\t\t\t <namakaryawan>".$nama."</namakaryawan>\r\n\t\t\t\t </karyawan>";
            }

    }

} else {

    echo ' Gagal: SIMPAN = '.$strx;

}



?>