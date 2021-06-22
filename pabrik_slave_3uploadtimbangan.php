<?php

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';

if ('' != isset($_POST['proses'])) {

    $proses = $_POST['proses'];

} else {

    if ('' != isset($_GET['proses'])) {

        $proses = $_GET['proses'];

    }

}



$periode = $_POST['periode'];
$idRemote = $_POST['idRemote'];
$dbnm = $_POST['dbnm'];
$prt = $_POST['prt'];
$pswrd = $_POST['pswrd'];
$ipAdd = $_POST['ipAdd'];
$period = explode('-', $_POST['period']);
$tglPeriod = $period[2].'-'.$period[1].'-'.$period[0];
$usrName = $_POST['usrName'];
$lksiServer = $_POST['lksiServer'];
$idTimbangan = $_POST['idTimbangan'];
$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$tglData = tanggalsystemd($_POST['tglData']);
$custData = $_POST['custData'];
$kbn = $_POST['kbn'];
$pabrik = $_POST['pabrik'];
$kdBrg = $_POST['kdBrg'];
$spbno = $_POST['spbno'];
$sibno = $_POST['sibno'];
$thnTnm = $_POST['thnTnm'];
$thnTnm2 = $_POST['thnTnm2'];
$thnTnm3 = $_POST['thnTnm3'];
$jmlhjjg = $_POST['jmlhjjg'];
$jmlhjjg2 = $_POST['jmlhjjg2'];
$jmlhjjg3 = $_POST['jmlhjjg3'];
$brndln = $_POST['brndln'];
$nodo = $_POST['nodo'];
$kdVhc = $_POST['kdVhc'];
$spir = $_POST['spir'];
$trp = $_POST['trp_'];

$jmMasuk = $_POST['jmMasuk'];
$jmKeluar = $_POST['jmKeluar'];
$brtBrsih = $_POST['brtBrsih'];
$brtnormal = $_POST['brtNorm'];
$brtMsk = $_POST['brtMsk'];
$brtOut = $_POST['brtOut'];
$usrNm = $_POST['usrNm'];
$kntrkNo = $_POST['kntrkNo'];
$tipe = $_POST['tipe'];
$nosp = $_POST['nosp'];
$bjrakt = $_POST['bjrAkt'];
$kgpotsortasi = $_POST['hslSortasi'];
$prsnsortasi = $_POST['persenSortasi'];
$IsSambung = $_POST['IsSambung'];
$potwajib = $_POST['potwajib'];
$potsampah = $_POST['potsampah'];
$potbasah = $_POST['potbasah'];
$potpanjang = $_POST['potpanjang'];
$potmengkal = $_POST['potmengkal'];
$potlain = $_POST['potlain'];



switch ($proses) {

    case 'preview':

        if ($lksiServer == '') {

            echo 'warning:Lokasi Harus Di Isi';

            exit();

        }  



        $arr = '##dbnm##prt##pswrd##ipAdd##period##usrName##lksiServer';

//        $corn = @mysql_connect($ipAdd.':'.$prt, $usrName, $pswrd) || exit('Error/Gagal :Unable to Connect to database : '.$ipAdd);

        error_reporting(E_ALL ^ (E_DEPRECATED | E_WARNING));

        $corn = mysql_connect($ipAdd.':'.$prt, $usrName, $pswrd);

        $sCob = 'select * from '.$dbnm.".mstrxtbs where PRODUCTCODE in ('40000001','40000002','40000003','40000004','40000005','40000006','40000007','40000008','40000009','40000016') 

				and GI in ('0','') and date(DATEIN) = '".$tglPeriod."' and OUTIN='0' order by TICKETNO2 ASC";

        $res = mysql_query($sCob, $corn);

        $row = mysql_num_rows($res);

        $no = 0;

     



        if ($row > 0) {

             echo "<button class=mybutton onclick=uploadData('".$row."','".$arr."') id=btnUpload>".$_SESSION['lang']['startUpload']

            ."</button>\r\n\t<div style='overflow:scroll;width:100%'>\r\n\t <table class=sortable cellspacing=1 border=0 >\r\n\t<thead>\r\n\t<tr class=rowheader align=center>\r\n\t<td>No.</td>\r\n\t<td>"

            .$_SESSION['lang']['tanggal']."</td>\r\n\t<td>".$_SESSION['lang']['notransaksi']."</td>\r\n\t<td>"

            .$_SESSION['lang']['kodecustomer']."</td>\r\n\t<td>".$_SESSION['lang']['NoKontrak']."</td>\r\n\t<td>"

            .$_SESSION['lang']['kebun']."</td>\r\n\t<td>".$_SESSION['lang']['pabrik']."</td>\r\n\t<td>".$_SESSION['lang']['kodebarang']."</td>\r\n\t<td>"

            .$_SESSION['lang']['nospb']."</td>\r\n     <td>".$_SESSION['lang']['beratMasuk']."</td>\r\n    <td>"

            .$_SESSION['lang']['beratKeluar']."</td>\r\n\t<td>".$_SESSION['lang']['beratnormal']."</td>\r\n    <td>% Potongan</td>\r\n\t<td>"

            .$_SESSION['lang']['potongankg']."</td><td>Pot TBS Basah</td><td>Pot Sampah</td><td>Pot Wajib</td><td>Pot Tk Panjang</td><td>Pot Mengkal</td><td>Pot Lainya</td>\r\n    <td>".$_SESSION['lang']['beratBersih']."</td>\r\n    <td>"

            .$_SESSION['lang']['bjraktual']."</td>\r\n    <td>".$_SESSION['lang']['jammasuk']."</td>\r\n\t<td>"

            .$_SESSION['lang']['jamkeluar']."</td>\r\n    <td>".$_SESSION['lang']['kodenopol']."</td>\r\n\t<td>"

            .$_SESSION['lang']['sopir']."</td>\r\n\t<td>".$_SESSION['lang']['nosipb']."</td>\r\n\t<td>".$_SESSION['lang']['tahuntanam']." 1</td>\r\n\t<td>"

            .$_SESSION['lang']['tahuntanam']." 2</td>\r\n\t<td>".$_SESSION['lang']['tahuntanam']." 3</td>\r\n\t<td>"

            .$_SESSION['lang']['jmlhTandan']." 1</td>\r\n\t<td>".$_SESSION['lang']['jmlhTandan']." 2</td>\r\n\t<td>"

            .$_SESSION['lang']['jmlhTandan']." 3</td>\r\n\t<td>".$_SESSION['lang']['brondolan']."</td><td>Transporter</td><td>Rekanan</td><td>IsSambung</td>\r\n\t<td>"

            .$_SESSION['lang']['nodo']."</td><td>NO SP</td>\r\n\t<td>".$_SESSION['lang']['username']."</td>\r\n\t<td>Tipe</td>   </tr>\r\n\t</thead><tbody id=ListData><tr  class=rowcontent><td colspan=30>Total Data :".$row.'</td></tr>';

 

			while ($hsl = mysql_fetch_assoc($res)) {

                $no++;

                if($hsl['tipe']=='0'){

                    $tipe = 'Penerimaan internal';

                }else if($hsl['tipe']=='1'){

                    $tipe = 'Penerimaan External';

                }else if($hsl['tipe']=='2'){

                    $tipe = 'Penjuala Produk';

                }else if($hsl['tipe']=='3'){

                    $tipe = 'Penerimaan Lain-lain';

                }else if($hsl['tipe']=='4'){

                    $tipe = 'Pengiriman Lain-lain';

                }

                 $sSup = "SELECT namasupplier,supplierid FROM ".$dbname.".log_5supplier WHERE supplierid ='".$hsl['TRPCODE']."' ";

            $qSup = mysql_query($sSup,$conn);

            while ($rSup = mysql_fetch_assoc($qSup)) {

                $namasupplier = $rSup['namasupplier'];

            }

               

             if($hsl['tkpanjangsat']=="kg"){

                    $potbasah = $hsl['tkpanjang'];

                }else if ($hsl['tkpanjanngsat']=="%"){

                    $potbasah = (($hsl['tkpanjang'])/100)*($hsl['NETTO']);

                }else{

                    $potbasah =0;

                }

                if($hsl['lwtmatangsat']=="kg"){

                    $potsampah = $hsl['lwtmatang'];

                }else if($hsl['lwtmatangsat']=="%"){

                    $potsampah = (($hsl['lwtmatang'])/100)*($hsl['NETTO']);

                }else{

                    $potsampah = 0;

                }

                if($hsl['buahbusuk']=="0"){

                     $potwajib = $hsl['buahbusuk'];  

                }else{

                  $potwajib = (($hsl['buahbusuk'])/100)*($hsl['NETTO']);  

                }

                  if($hsl['buahsakit']=="0"){

                     $potpanjang = $hsl['buahsakit'];  

                }else{

                  $potpanjang = (($hsl['buahsakit'])/100)*($hsl['NETTO']);  

                }

                  if($hsl['mengkal']=="0"){

                     $potmengkal = $hsl['mengkal'];  

                }else{

                  $potmengkal = (($hsl['mengkal'])/100)*($hsl['NETTO']);  

                }

                 if($hsl['mutu']=="0"){

                     $potlain = $hsl['mutu'];  

                }else{

                  $potlain = (($hsl['mutu'])/100)*($hsl['NETTO']);  

                }

         
                
                $jmMasuk = substr($hsl['DATEIN'], 10, 9);

                $jmKeluar = substr($hsl['DATEOUT'], 10, 9);

                echo '<tr class=rowcontent id=row_'.$no." >\r\n\t\t\t<td >".$no."</td>\r\n\t\t\t<td id=tglData_"

				.$no.'>'.tanggalnormald($hsl['DATEIN'])."</td>\r\n\t\t\t<td id=isiData_"

				.$no.'>'.$hsl['jns'].$hsl['tgl'].'-'.$hsl['TICKETNO']."</td>\r\n\t\t\t<td id=custData_"

				.$no.'>'.$hsl['TRPCODE']."</td>\r\n\t\t\t<td id=kntrkNo_".$no.'>'.$hsl['CTRNO']."</td>\r\n\t\t\t<td id=kbn_"

				.$no.'>'.$hsl['UNITCODE']."</td>\r\n\t\t\t<td id=pabrik_".$no.'>'.$hsl['MILLCODE']."</td>\r\n\t\t\t<td id=kdBrg_"

				.$no.'>'.$hsl['PRODUCTCODE']."</td>\r\n\t\t\t<td id=spbno_".$no.'>'

				.$hsl['SPBNO']."</td>\r\n            <td align=right id=brtMsk_".$no.'>'

				.$hsl['WEI1ST']."</td>\r\n            <td align=right id=brtOut_".$no.'>'

				.$hsl['WEI2ND']."</td>\r\n            <td align=right id=brtBrsih_".$no.'>'

				.$hsl['NETTO']."</td>\r\n            <td align=right id=persenSortasi_".$no.'>'

				.$hsl['buahbusuk']."</td>\r\n\t\t\t<td align=right id=hslSortasi_".$no.'>'

				.$hsl['KGPOTSORTASI']."</td> <td align=right id=potbasah".$no.'>'

                .$potbasah."</td><td align=right id=potsampah".$no.'>'

                .$potsampah."</td><td align=right id=potwajib".$no.'>'

                .$potwajib."</td><td align=right id=potpanjang".$no.'>'

                .$potpanjang."</td><td align=right id=potmengkal".$no.'>'

                .$potmengkal."</td><td align=right id=potlain".$no.'>'

                .$potlain."</td>\r\n            <td align=right id=brtNorm_"

				.$no.'>'.round($hsl['NETTO'] - $hsl['KGPOTSORTASI'], 2)."</td>\r\n            <td align=right id=bjrAkt_"

				.$no.'>'.round(($hsl['NETTO'] - $hsl['KGPOTSORTASI']) / $hsl['JMLHJJG'], 2)."</td>\r\n            <td id=jmMasuk_"

				.$no.'>'.$jmMasuk."</td>\r\n\t\t\t<td id=jmKeluar_".$no.'>'.$jmKeluar."</td>\r\n\t\t\t<td id=kdVhc_"

				.$no.'>'.$hsl['VEHNOCODE']."</td>\r\n\t\t\t<td id=spir_".$no.'>'.$hsl['DRIVER']."</td>\r\n\t\t\t<td id=sibno_"

				.$no.'>'.$hsl['SIPBNO']."</td>\r\n\t\t\t<td id=thnTnm_".$no.'>'.$hsl['TAHUNTANAM']."</td>\r\n\t\t\t<td id=thnTnm2_"

				.$no.'>'.$hsl['TAHUNTANAM2']."</td>\r\n\t\t\t<td id=thnTnm3_".$no.'>'.$hsl['TAHUNTANAM3']."</td>\r\n\t\t\t<td id=jmlhjjg_"

				.$no.'>'.$hsl['JMLHJJG']."</td>\r\n\t\t\t<td id=jmlhjjg2_".$no.'>'.$hsl['JMLHJJG2']."</td>\r\n\t\t\t<td id=jmlhjjg3_"

				.$no.'>'.$hsl['JMLHJJG3']."</td>\r\n\t\t\t<td id=brndln_".$no.'>'.$hsl['BRONDOLAN']."</td></td>\r\n\t\t\t<td id=trp_".$no.'>'.$hsl['TRPCODE']."</td></td>\r\n\t\t\t<td>".$namasupplier."</td><td id=IsSambung".$no.'>'.$hsl['IsSambung']."</td>\r\n\t\t\t<td id=nodo_"

				.$no.'>'.$hsl['NODOTRP']."</td><td id=nosp_"

                .$no.'>'.$hsl['SPNO']."</td>\r\n\t\t\t<td id=usrNm_".$no.'>'.$hsl['USERID']."</td><td id=tipeTrx".$no.'>'.$hsl['tipe'].'('.$tipe.')'."</td><td id=tipe".$no.'>'.$hsl['tipe']."</td>\r\n            </tr>\r\n\t\t\t";

            }

        } else {

            echo " <table class=sortable cellspacing=1 border=0>\r\n\t<thead>\r\n\t<tr class=rowheader>\r\n\t<td>No.</td>\r\n\t<td>"

			.$_SESSION['lang']['tanggal']."</td>\r\n\t<td>".$_SESSION['lang']['notransaksi']."</td>\r\n\t<td>"

			.$_SESSION['lang']['nospb']."</td>\r\n\t<td>".$_SESSION['lang']['nosipb']."</td>\r\n\t<td>"

			.$_SESSION['lang']['nodo']."</td>\r\n\t<td>".$_SESSION['lang']['kebun']."</td>\r\n\t<td>"

			.$_SESSION['lang']['kodenopol']."</td>\r\n\t<td>".$_SESSION['lang']['sopir']."</td>\r\n\t<td>"

			.$_SESSION['lang']['jammasuk']."</td>\r\n\t<td>".$_SESSION['lang']['jamkeluar']."</td>\r\n\t<td>"

			.$_SESSION['lang']['beratnormal']."</td><td>"

            .'Aksi'."</td>\r\n\t</tr>\r\n\t</thead><tbody><tr class=rowcontent align=center><td colspan=12>Not Found/Not Conected</td></tr>";

        }



        echo '</tbody></table></div>';



        break;

         case 'getDataTiketDo':

         $nodo = $_POST['nodo'];

         $tanggal = tanggalsystem($_POST['tgl']);

         $lokasi = substr($_SESSION['empl']['lokasitugas'], 0, 3);

        $sCob = 'select a.*, b.hargasatuan, left(a.notransaksi,1) as kode, c.namasupplier as namasupplier, (select namasupplier from log_5supplier where supplierid=a.kodecustomer) as  namacus, d.namacustomer as namacustomer from '.$dbname.".pabrik_timbangan a LEFT JOIN  pmn_kontrakjual b on a.nosipb=b.nodo left join log_5supplier c on b.transporter=c.supplierid

            left join pmn_4customer d ON b.koderekanan=d.kodecustomer 

            where date(a.tanggal)='".$tanggal."' and a.millcode like '%".$lokasi."%'   order by tanggal,IsPosting DESC";

       

        $res = mysql_query($sCob);

        $row = mysql_num_rows($res);

        if ($row > 0) {

            echo "<div style='overflow:scroll;width:100%'>

                    <table class=sortable cellspacing=1 border=0 >

                        <thead>

                            <tr class=rowheader align=center>

                                <td>No.</td>

                                <td>".$_SESSION['lang']['tanggal']."</td>

                                <td>No SIPB</td>

                                <td>No SPB</td>

                                <td>".$_SESSION['lang']['notransaksi']."</td>

                                <td>Nama Buyer</td>

                                <td>Transporter</td>

                                <td>Berat 1</td>

                                <td>Berat 2</td>

                                <td>NETTO</td>

                                <td>Harga Satuan</td>

                            </tr>

                        </thead>

                    <tbody id=ListData>

                    <tr class=rowcontent><td colspan=30>Total Data :".$row.'</td></tr>';

 

            while ($hsl = mysql_fetch_assoc($res)) {

                $no++;

                if($hsl['kode']=='M'){

                    $hsl['namacustomer']=$hsl['namacus'];

                

                    $qry = "select harga_akhir as harga from log_supplier_harga_history where tanggal_akhir=DATE('".$hsl['tanggal']."') 

                            AND kode_supplier='".$hsl['kodecustomer']."'";



                    $hasl = mysql_query($qry);

                    $hasil= mysql_fetch_assoc($hasl);

                    $hsl['hargasatuan']=$hasil['harga'];



                } 

         

               if($hsl['IsPosting']=='0'){



                echo "<tr class=rowcontent id=row_".$no.">

                        <td>".$no."</td>

                        <td id=tgl".$no.">".$hsl['tanggal']."</td>

                        <td id=sipb".$no.">".$hsl['nosipb']."</td>

                        <td id=spb".$no.">".$hsl['nospb']."</td>

                        <td id=notiket".$no.">".$hsl['notransaksi']."</td>

                        <td id=namacustomer_".$no.">".$hsl['namacustomer']."</td>

                        <td id=namasupplier".$no.">".$hsl['namasupplier']."</td>

                        <td id=berat1".$no." align='right'>".number_format($hsl['beratmasuk'])."</td>

                        <td id=berat2".$no." align='right'>".number_format($hsl['beratkeluar'])."</td>

                        <td id=netto".$no." align='right'>".number_format($hsl['beratnormal'])."</td>";

                        

                        if(number_format($hsl['hargasatuan'])<=0){

                            echo "<td id=hargasatuan".$no." align='CENTER' colspan='3'><strong style=color:red;>HARGA BLM DI SET</strong> </td>";

                        } else {

//<td id=hargasatuan".$no." align='right'>".number_format($hsl['hargasatuan'])."</td>

//<td id=totalharga".$no." align='right'>".number_format($hsl['hargasatuan']*$hsl['beratbersih'])."</td>

                                  

                            echo "  <td><button onclick=\"postingData('".$hsl['notransaksi']."','".$hsl['nosipb']."')\" class=\"mybutton\" name=\"posting\" id=\"posting\">Posting</button></td></tr>";

                        }

               }else{

                echo "<tr class=rowcontent id=row_".$no.">

                        <td>".$no."</td>

                        <td id=tgl".$no.">".$hsl['tanggal']."</td>

                        <td id=sipb".$no.">".$hsl['nosipb']."</td>

                        <td id=spb".$no.">".$hsl['nospb']."</td>

                        <td id=notiket".$no.">".$hsl['notransaksi']."</td>

                        <td id=namacustomer_".$no.">".$hsl['namacustomer']."</td>

                        <td id=namasupplier".$no.">".$hsl['namasupplier']."</td>

                        <td id=berat1".$no." align='right'>".number_format($hsl['beratmasuk'])."</td>

                        <td id=berat2".$no." align='right'>".number_format($hsl['beratkeluar'])."</td>

                        <td id=netto".$no." align='right'>".number_format($hsl['beratnormal'])."</td>

                        <td align='center'>POSTED</td></tr>";

//                      <td id=hargasatuan".$no." align='right'>".number_format($hsl['hargasatuan'])."</td>

//                        <td id=totalharga".$no." align='right'>".number_format($hsl['hargasatuan']*$hsl['beratbersih'])."</td>

         

                }

            }

        }

        break;

        case 'getSup':

       

          $kodekl= $_POST['klsup'];

            $sSup = "SELECT namasupplier,supplierid FROM ".$dbname.".log_5supplier WHERE kodekelompok ='".$kodekl."' ";

            $qSup = mysql_query($sSup);

            while ($rSup = mysql_fetch_assoc($qSup)) {

                $optSup .= '<option value='.$rSup['supplierid'].'>'.$rSup['namasupplier'].'</option>';

            }

       

        echo $optSup;

        break;

        

        case 'postingData':

        $notiket = $_POST['notiket'];
        $nosipb=$_POST['nosipb'];
        $sCek = 'select *, left(notransaksi,1) as kode from '.$dbname.".pabrik_timbangan a LEFT JOIN pmn_kontrakjual b on a.nosipb=b.nodo where notransaksi='".$notiket."' and nosipb='".$_POST['nosipb']."'";
        $qCek = mysql_query($sCek);
        $rCek = mysql_num_rows($qCek);

        if ($rCek > 0) {

            $hsl = mysql_fetch_assoc($qCek);

            if($hsl['kode']=='M'){

                    $qry = "select ((harga_akhir)+(fee)) as harga from log_supplier_harga_history where tanggal_akhir=DATE('".$hsl['tanggal']."') 

                            AND kode_supplier='".$hsl['kodecustomer']."'";

                    $hasl = mysql_query($qry);

                    $hasil= mysql_fetch_assoc($hasl);

                    $hsl['hargasatuan']=$hasil['harga'];

                    $kodesupplier=$hsl['kodecustomer'];


                $group = 'TIMM';

                $str = 'select noakundebet, noakunkredit from ' . $dbname . '.keu_5parameterjurnal where jurnalid=\'' . $group . '\' limit 1';

                $res = mysql_query($str);
                $bar = mysql_fetch_object($res);

                if (mysql_num_rows($res) < 1 || $bar->noakundebet=='' || $bar->noakunkredit=='') {
                    exit('Error: No.Akun pada parameterjurnal belum ada untuk TMMM');
                }
                else {
               
                    $akundebet = $bar->noakundebet;
                    $akunkredit = $bar->noakunkredit;

                }

            }else{

               $kodecustomer=$hsl['kodecustomer'];

               if($hsl['kodebarang']=='40000001'){

                    $group = 'TIMCPO';
                    $str = 'select noakundebet, noakunkredit from ' . $dbname . '.keu_5parameterjurnal where jurnalid=\'' . $group . '\' limit 1';
                    $res = mysql_query($str);

                }


                if($hsl['kodebarang']=='40000002'){

                    $group = 'TIMPK.';
                    $str = 'select noakundebet, noakunkredit from ' . $dbname . '.keu_5parameterjurnal where jurnalid=\'' . $group . '\' limit 1';
                    $res = mysql_query($str);

                }

                

                if($hsl['kodebarang']=='40000004'){

                    $group = 'TIMCGG';
                    $str = 'select noakundebet, noakunkredit from ' . $dbname . '.keu_5parameterjurnal where jurnalid=\'' . $group . '\' limit 1';
                    $res = mysql_query($str);

                }


                if($hsl['kodebarang']=='40000005'){

                    $group = 'TIMFIB';
                    $str = 'select noakundebet, noakunkredit from ' . $dbname . '.keu_5parameterjurnal where jurnalid=\'' . $group . '\' limit 1';
                    $res = mysql_query($str);

                }



                if($hsl['kodebarang']=='40000006'){

                    $group = 'TIMABU';
                    $str = 'select noakundebet, noakunkredit from ' . $dbname . '.keu_5parameterjurnal where jurnalid=\'' . $group . '\' limit 1';
                    $res = mysql_query($str);

                }

                $bar = mysql_fetch_object($res);
                if (mysql_num_rows($res) < 1 || $bar->noakundebet=='' || $bar->noakunkredit=='') {

                    exit('Error: No.Akun pada parameterjurnal belum ada');

                }

                else {

                    $akundebet = $bar->noakundebet;
                    $akunkredit = $bar->noakunkredit;

                }   

            }



            $kodeJurnal = $group;
            $tgl=date_create($hsl['tanggal']);
            $tgmulaid = date_format($tgl,'Ymd');
            $pengguna = substr($_SESSION['empl']['lokasitugas'], 0, 3);


           $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter','kodeorg=\''.$pengguna.'\' and kodekelompok=\''.$kodeJurnal.'\'');



            $tmpKonter = fetchData($queryJ);
            $konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
            $nojurnal = str_replace('-', '', $tgmulaid) . '/' . $_SESSION['empl']['lokasitugas']. '/' . $kodeJurnal . '/' . $konter;


            $qryht="insert into keu_jurnalht values('".$nojurnal."','".$kodeJurnal."','".$tgmulaid."','".date('Ymd')."','1','0','0','0','".$hsl['notransaksi']."','1','IDR','0','1')";
            $head=mysql_query($qryht);


            $x=0;

            if($akundebet!=''){

                $x++;

                $qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$akundebet."',' Timbangan ".$hsl['notransaksi']." ".$_POST['namasuplier']."','".ROUND($hsl['hargasatuan']*$hsl['beratnormal'],0)."','IDR','1','".$_SESSION['empl']['lokasitugas']."', '','','','','".$hsl['koderekanan']."','".$kodesupplier."','".$hsl['notransaksi']."','','','".$hsl['nosipb']."','','')";

                $dt=mysql_query($qrydt);

                

            }

            if($akunkredit!=''){

                $x++;

                $qrydt="insert into keu_jurnaldt values('".$nojurnal."','".$tgmulaid."','".$x."','".$akunkredit."',' Timbangan ".$hsl['notransaksi']." ".$_POST['namasuplier']."','".ROUND($hsl['hargasatuan']*$hsl['beratnormal']*-1,0)."','IDR','1','".$_SESSION['empl']['lokasitugas']."', '','','','','".$hsl['koderekanan']."','".$kodesupplier."','".$hsl['notransaksi']."','','','".$hsl['nosipb']."','','')";

                $dt=mysql_query($qrydt);

                

            }   


            $upcounter="UPDATE keu_5kelompokjurnal SET nokounter='".$konter."' WHERE kodeorg='".$pengguna."' and kodekelompok='".$kodeJurnal."'";
            $upcount=mysql_query($upcounter);


            $sUp = 'update '.$dbname.".pabrik_timbangan set IsPosting='1' where notransaksi='".$notiket."' and nosipb='".$nosipb."' "; 

            if (mysql_query($sUp)) {   

               echo 'Berhasil Posting';

            } else {

               echo ' ERROR Gagal Posting';

            }

        } else {

          
           echo 'Error ! data tidak ada';

        }



        break;

        case 'simpanPB':

         $idRemote = $_POST['idRem'];

         $notiket = $_POST['tiket'];

         $nosipb = $_POST['nosipb'];

         $tujuan = $_POST['tujuan'];

         $trp = $_POST['trp'];

          $IdP = explode("-" , $notiket);

                $ticket = $IdP[1];

                $jns = substr($IdP[0], 0,1);

                $tgl = substr($IdP[0], 1,8);

        $sql = 'select * from '.$dbname.".setup_remotetimbangan where id='".$idRemote."'";

        $query = mysql_query($sql);

        $res = mysql_fetch_assoc($query);

       



      



        $sCek = 'select notransaksi from '.$dbname.".pabrik_timbangan where notransaksi='".$notiket."' and IsPosting=0";

        $qCek = mysql_query($sCek);

        $rCek = mysql_num_rows($qCek);

         error_reporting(E_ALL ^ E_DEPRECATED);

        $corn = mysql_connect($res['ip'].':'.$res['port'], $res['username'], $res['password']);

        $dbnm = $res['dbname'];

        $sCekTimb = 'select * from '.$dbnm.".mstrxtbs where jns='".$jns."' and tgl='".$tgl."' and TICKETNO='".$ticket."' ";



        $qCekTimb = mysql_query($sCekTimb,$corn);

        $rCekTimb = mysql_num_rows($qCekTimb);

        

        if ($rCek = 1 && $rCekTimb = 1) {

                

                 error_reporting(E_ALL ^ E_DEPRECATED);

        $corn = mysql_connect($res['ip'].':'.$res['port'], $res['username'], $res['password']);

        $sCekCtr = 'select CTRNO from '.$dbnm.".mssipb where SIPBNO='".$nosipb."' ";

                $qCekCtr = mysql_query($sCekCtr,$corn);

                $resCekCtr = mysql_fetch_assoc($qCekCtr);

                $CTRNO = $resCekCtr['CTRNO'];

                 $sUp = 'update '.$dbname.".pabrik_timbangan set nosipb ='".$nosipb."', nokontrak ='".$CTRNO."',trpcode='".$trp."' where notransaksi='".$notiket."' ";

            if( mysql_query($sUp,$conn)){
                   error_reporting(E_ALL ^ E_DEPRECATED);

        $corn = mysql_connect($res['ip'].':'.$res['port'], $res['username'], $res['password']);

        $dbnm = $res['dbname'];

                $sUpCtr = 'update '.$dbnm.".mstrxtbs set SIPBNO ='".$nosipb."', CTRNO ='".$CTRNO."',TRPCODE='".$trp."' where jns='".$jns."' and tgl='".$tgl."' and TICKETNO='".$ticket."' ";

               

              
              if (mysql_query($sUpCtr)) {  

              

                    

               echo 'Berhasil Di ubah';

            } else {

                

                echo ' ERROR Pindah Buyer';

            }
        }else{
            echo ' ERROR Pindah Buyer server anthesis';
        }

        } else {

            //$corn = @mysql_connect($ipAdd.':'.$prt, $usrName, $pswrd) || exit('Error/Gagal :Unable to Connect to database : '.$ipAdd);

             

            echo 'Error ! data tidak ada';

        }



        break;

    case 'uploadData':

        $sCek = 'select notransaksi from '.$dbname.".pabrik_timbangan where notransaksi='".$idTimbangan."' and SIPBNO='".$sibno."' and MILLCODE='".$pabrik."'";

        $qCek = mysql_query($sCek);

        $rCek = mysql_num_rows($qCek);

       

        if ($rCek <= 0) {

            if ($kbn == 'NULL' || $kbn == '') {

                $inTex = 0;

            } else {

                $sCek = 'select induk from '.$dbname.".organisasi where kodeorganisasi='".$kbn."'";

                $qCek = mysql_query($sCek);

                $rCek = mysql_fetch_assoc($qCek);

                if ('SSP' != $rCek['induk'] || 'MJR' != $rCek['induk'] || 'HSS' != $rCek['induk'] || 'BNM' != $rCek['induk']) {

                    $inTex = 2;

                } else {

                    if (preg_match('/e$/Di', $kbn)) {

                        $inTex = 1;

                    }

                }

            }



            $DtTime = date('Y-m-d H:i:s');

            $sIns = 'INSERT INTO '.$dbname.".`pabrik_timbangan` (`notransaksi`, `tanggal`, `kodeorg`, `kodecustomer`, `jumlahtandan1`, `kodebarang`, `jammasuk`, `beratmasuk`, `jamkeluar`, `beratkeluar`, `nokendaraan`, `supir`, `nospb`, `nokontrak`, `nodo`, `nosipb`, `thntm1`, `thntm2`, `thntm3`, `jumlahtandan2`, `jumlahtandan3`, `brondolan`, `username`, `millcode`, `beratbersih`,`intex`,`timbangonoff`,`kgpotsortasi`,`bjr`,`beratnormal`,`persenBrondolan`,`trpcode`,`IsSambung`,`tipe`,`nosp`,`potbasah`,`potwajib`,`potpanjang`,`potmengkal`,`potsampah`,`potlain`) VALUES ('".$idTimbangan."','".$tglData."','".$kbn."','".$custData."','".$jmlhjjg."','".$kdBrg."','".$jmMasuk."','".$brtMsk."','".$jmKeluar."','".$brtOut."','".$kdVhc."','".$spir."','".$spbno."','".$kntrkNo."','".$nodo."','".$sibno."','".$thnTnm."','".$thnTnm2."','".$thnTnm3."','".$jmlhjjg2."','".$jmlhjjg3."','".$brndln."','".$usrNm."','".$pabrik."','".$brtBrsih."','".$inTex."','0','".$kgpotsortasi."','".$bjrakt."','".$brtnormal."','".$prsnsortasi."','".$trp."','".$IsSambung."','".$tipe."','".$nosp."','".$potbasah."','".$potwajib."','".$potpanjang."','".$potmengkal."','".$potsampah."','".$potlain."')";

            if (mysql_query($sIns)) {

                //@$corn = mysql_connect($ipAdd.':'.$prt, $usrName, $pswrd) || exit('Error/Gagal :Unable to Connect to database : '.$ipAdd);

                $DtTime = date('Y-m-d H:i:s');

                $IdP = explode("-" , $idTimbangan);

                $ticket = $IdP[1];

                $jns = substr($IdP[0], 0,1);

                $tgl = substr($IdP[0], 1,8);

                 error_reporting(E_ALL ^ E_DEPRECATED);

                $corn = mysql_connect($ipAdd.':'.$prt, $usrName, $pswrd);

                $sUp = 'update '.$dbnm.".mstrxtbs set GI='".$DtTime."' where TICKETNO='".$ticket."' and jns='".$jns."' and tgl='".$tgl."'  ";



                if(mysql_query($sUp, $corn)){

                   $stat = 1;

                echo $stat; 

            }else{

                exit('Error/Gagal :Unable to Connect to database : '.$ipAdd.$prt.$usrName.$pswrd);

            }

               

                

            } else {

                  $IdP = explode("-" , $idTimbangan);

                $ticket = $IdP[1];

                $jns = substr($IdP[0], 0,1);

                $tgl = substr($IdP[0], 1,8);

                error_reporting(E_ALL ^ E_DEPRECATED);

                $corn = mysql_connect($ipAdd.':'.$prt, $usrName, $pswrd);

                $sUp = 'update '.$dbnm.".mstrxtbs set GI='".$DtTime."' where TICKETNO='".$ticket."' and jns='".$jns."' and tgl='".$tgl."' and IsSambung='".$IsSambung."' ";

             

                mysql_query($sUp, $corn);

                $stat = 0;

                echo $stat;

            }

        } else {

           // $DtTime = date('Y-m-d H:i:s');

             error_reporting(E_ALL ^ E_DEPRECATED);

            // $corn = @mysql_connect($ipAdd.':'.$prt, $usrName, $pswrd) || exit('Error/Gagal :Unable to Connect to database : '.$ipAdd);

            //   $IdP = explode("-" , $idTimbangan);

            //     $ticket = $IdP[1];

            //     $jns = substr($IdP[0], 0,1);

            //     $tgl = substr($IdP[0], 1,8);

            //     error_reporting(E_ALL ^ E_DEPRECATED);

            // $corn = mysql_connect($ipAdd.':'.$prt, $usrName, $pswrd);

            // $sUp = 'update '.$dbnm.".mstrxtbs set GI='".$DtTime."' where TICKETNO='".$ticket."' and jns='".$jns."' and tgl='".$tgl."' and IsSambung='".$IsSambung."'";

           

            // mysql_query($sUp, $corn);

            echo $sCek;



            $stat = 3;

            echo $stat;

             exit();

        }



        break;

    case 'getDataLokasi':

        $sql = 'select * from '.$dbname.".setup_remotetimbangan where id='".$idRemote."'";

        $query = mysql_query($sql);

        $res = mysql_fetch_assoc($query);

        echo $res['ip'].'###'.$res['port'].'###'.$res['dbname'].'###'.$res['username'].'###'.$res['password'];



        break;

         case 'getDataLokasiTiket':

        $sql = 'select * from '.$dbname.".setup_remotetimbangan where id='".$idRemote."'";

        $query = mysql_query($sql);

        $res = mysql_fetch_assoc($query);

        error_reporting(E_ALL ^ E_DEPRECATED);

        $corn = mysql_connect($res['ip'].':'.$res['port'], $res['username'], $res['password']);

        $dbnm = $res['dbname'];

        $sqlTiket = 'select * from '.$dbnm.".mstrxtbs where tipe=2 and OUTIN=0";

        $queryTiket = mysql_query($sqlTiket,$corn);

        $optTiket = '<option value=0></option>';

        while ($resTiket = mysql_fetch_assoc($queryTiket)) {

            $optTiket .= "<option value='".$resTiket['jns'].$resTiket['tgl']."-".$resTiket['TICKETNO']."'>".$resTiket['jns'].$resTiket['tgl'].'-'.$resTiket['TICKETNO']."</option>";

        }



        echo '***'.$res['ip'].'***'.$res['port'].'***'.$res['dbname'].'***'.$res['username'].'***'.$res['password'].'***'.$optTiket;



        break;



         case 'getDataTiketDetail':

        $ipadd1 = $_POST['ipadd'];

        $prt1 = $_POST['prt'];

        $username1= $_POST['usrName'];

        $pswrd1 = $_POST['pswrd'];

        $dbnm1 = $_POST['dbnm'];

        $tiketno = $_POST['notiket'];

        $sCob = 'select * from '.$dbname.".pabrik_timbangan where notransaksi='".$tiketno."' and IsPosting=0 ";

        $res = mysql_query($sCob);

        $row = mysql_num_rows($res);



        if($row >= 1){





          $IdP = explode("-" , $tiketno);

            $ticket = $IdP[1];

            $jns = substr($IdP[0], 0,1);

            $tgl = substr($IdP[0], 1,8);

            error_reporting(E_ALL ^ E_DEPRECATED);

        $corn1 = mysql_connect($ipadd1.':'.$prt1, $username1, $pswrd1);

       // Check connection

        // Check connection



        $sqlTiket = "select a.*,b.BUYERCODE from ".$dbnm1.".mstrxtbs as a, ".$dbnm1.".mscontract as b where a.CTRNO=b.CTRNO and a.TICKETNO='".$ticket."' and a.jns='".$jns."' and a.tgl='".$tgl."' and OUTIN=0 limit 1 ";

        $queryTiket = mysql_query($sqlTiket, $corn1);

        $resTiket = mysql_fetch_assoc($queryTiket);

        $trpcode=$resTiket['TRPCODE'];

        $buyercode=$resTiket['BUYERCODE'];

        

        $sqlDo = "select a.* from ".$dbnm1.".mssipb as a, ".$dbnm1.".mscontract as b where a.CTRNO=b.CTRNO and a.TRPCODE='".$trpcode."' and b.BUYERCODE !='". $buyercode."' order by a.SIPBNO asc ";

        $queryDo = mysql_query($sqlDo, $corn1);

        $resDo = mysql_fetch_assoc($queryDo);

        $optDo = "<option value='".$resTiket['SIPBNO']."'>".$resTiket['SIPBNO']."</option>";

          while ($resDo = mysql_fetch_assoc($queryDo)) {

            $optDo .= "<option value='".$resDo['SIPBNO']."'>".$resDo['SIPBNO']."</option>";

        }



        $row1 = mysql_num_rows($queryTiket);

        if(mysql_query($sqlTiket, $corn1)){

             echo '###'.$resTiket['BUYERCODE'].'###'.$resTiket['SIPBNO'].'###'.$resTiket['TRPCODE'].'###'.$resTiket['tujuan'].'###'.$resTiket['WEI1ST'].'###'.$resTiket['WEI2ND'].'###'.$resTiket['NETTO'].'###'.$optDo;

        }else{

            echo "Data Error : Tidak ada data atas atas No Tiket".$jns.$tgl.'-'.$ticket.'-';

           

        }

    }else{

      

        echo "Data Error : Tiket sudah di posting Atau Tiket Belum di Download !";

    }

       



        break;

         case 'getDataDoDetail':

        $ipadd1 = $_POST['ipadd'];

        $prt1 = $_POST['prt'];

        $username1= $_POST['usrName'];

        $pswrd1 = $_POST['pswrd'];

        $dbnm1 = $_POST['dbnm'];

        $nosipb = $_POST['nosipb'];

        $trp = $_POST['trp'];

        $vendorbuyer = $_POST['vendorbuyer'];



          

           

        @$corn1 = mysql_connect($ipadd1.':'.$prt1, $username1, $pswrd1);

       // Check connection

        // Check connection



        $sqlTiket = "select a.*,a.TRPCODE as TRPCODE,c.TRPNAME as TRPNAME,d.BUYERCODE as BUYERCODE from ".$dbnm1.".mssipb as a, ".$dbnm1.".msvendorbuyer as b, ".$dbnm1.".msvendortrp as c, ".$dbnm1.".mscontract as d where a.CTRNO=d.CTRNO and d.BUYERCODE=b.BUYERCODE and a.TRPCODE=c.TRPCODE  and a.SIPBNO='".$nosipb."' ";

        $queryTiket = mysql_query($sqlTiket, $corn1);

        $resTiket = mysql_fetch_assoc($queryTiket);



        $TRPCODE = $resTiket['TRPCODE'];

        $SIPBNO = $resTiket['SIPBNO'];

        if ($TRPCODE==$trp){



           echo '###'.$resTiket['SIPBNO'].'###'.$resTiket['BUYERCODE'].'###'.$resTiket['TRPCODE'];

       



        }else{

            echo "Data Error : DO yang di pilih harus dengan Transporter yang sama";

             echo '###'.$nosipb.'###'.$vendorbuyer.'###'.$trp.'###'.$TRPCODE;

             echo $sqlTiket;

        }



       



        break;

    default:

        break;

}



?>