<?php

session_start();

require_once('master_validation.php');

require_once('config/connection.php');

include_once('lib/nangkoelib.php');

include_once('lib/zLib.php');



$notransaksi_head=$_POST['notrans'];

$notransaksi=$_POST['noOptrans'];

$proses=$_POST['proses'];

$lokasi=$_SESSION['empl']['lokasitugas'];

$jnsPekerjaan=$_POST['jnsPekerjaan'];

$lokKerja=$_POST['locationKerja'];

$muatan=$_POST['muatan'];

$brtMuatan=$_POST['brtmuatan'];

$jmlhRit=$_POST['jmlhRit'];

$ket=$_POST['ket'];

$posisi=$_POST['posisi'];

$kdKry=$_POST['kdKry'];

$oldjnsPekerjaan=$_POST['oldjnsPekerjaan'];

$uphOprt=$_POST['uphOprt'];

$prmiOprt=$_POST['prmiOprt'];

$pnltyOprt=$_POST['pnltyOprt'];

$tglTrans=tanggalsystem($_POST['tglTrans']);

$thnKntrk=$_POST['thnKntrk'];

//$lksiTgs=substr($_SESSION['empl']['lokasitugas'],0,4);

$noKntrak=$_POST['noKntrak'];

$biaya=$_POST['biaya'];

$Blok=$_POST['Blok'];

$oldBlok=$_POST['oldBlok'];

$old_lokKerja=$_POST['old_lokKerja'];



$kmhmAwal=$_POST['kmhmAwal'];

$kmhmAkhir=$_POST['kmhmAkhir'];

$satuan=$_POST['satuan'];



if($notransaksi_head!='')

{

        $sKode="select kodeorg from ".$dbname.".vhc_runht where notransaksi='".$notransaksi_head."'";

        //$sKode="select kodeorg from ".$dbname.".vhc_runht";

        $qKode=mysql_query($sKode) or die(mysql_error());

        $rKode=mysql_fetch_assoc($qKode);

}

$optKdVhc=makeOption($dbname, 'vhc_runht', 'notransaksi,kodevhc');



switch($proses)

{

        case 'load_data_kerjaan':

        //echo "warning:masuk";	



        $sql="select * from ".$dbname.".vhc_rundt where substring(notransaksi,1,4)='".$rKode['kodeorg']."' and notransaksi='".$notransaksi_head."' order by notransaksi desc";// echo $sql;

        //$sql="select * from ".$dbname.".vhc_rundt where substring(notransaksi,1,4)='".$rKode['kodeorg']."' order by notransaksi desc";// echo $sql;

        $query=mysql_query($sql) or die(mysql_error());

        while($res=mysql_fetch_assoc($query))

        {

                $no+=1;

                echo"

                <tr class=rowcontent>

                <td>".$no."</td>

                <td>".$res['notransaksi']."</td>

                <td>".$res['jenispekerjaan']."</td>

                <td>".$res['alokasibiaya']."</td>

                <td>".number_format($res['jumlahrit'],2)."</td>

                <td>".number_format($res['beratmuatan'],2)."</td>

                <td>".number_format($res['kmhmawal'],2)."</td>

                <td>".number_format($res['kmhmakhir'],2)."</td>

                 <td>".$res['satuan']."</td>

                <td>".number_format($res['biaya'],2)."</td>

                <td><img src=images/application/application_edit.png class=resicon  title='Edit' 

                onclick=\"fillFieldKrj('".$res['jenispekerjaan']."','".$res['alokasibiaya']."','". $res['beratmuatan']."','". $res['jumlahrit']."','". $res['keterangan']."','". $res['biaya']."','". $res['kmhmawal']."','". $res['kmhmakhir']."','". $res['satuan']."');\">

                <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDataKrj('". $res['notransaksi']."','". $res['jenispekerjaan']."');\" >	

                </td>

                </tr>

                ";

        }

        break;



        case'insert_pekerjaan':

        if($notransaksi_head=='')

        {

                echo"warning: please confirm heade first";

                exit();

        }

        if($jnsPekerjaan=='')

        {

            echo"warning: Activity required";

            exit();



        }

        if($lokKerja=='')

        {

            echo"warning: Cost allocation (block) required";

            exit();



        }

        if($kmhmAwal>=$kmhmAkhir)

        {

                echo"warning:".$_SESSION['lang']['vhc_kmhm_awal']." must lower then ".$_SESSION['lang']['vhc_kmhm_akhir']."";

                exit();

        }

        $jumlah=$kmhmAkhir-$kmhmAwal;

        $sCekHt="select notransaksi from ".$dbname.".vhc_runht where notransaksi='".$notransaksi_head."'";



        $qCekHt=mysql_query($sCekHt) or die(mysql_error());

        $rCekHt=mysql_num_rows($qCekHt);

        if($rCekHt<1)

        {

                echo"warning: Header required";

                exit();

        }



        if($Blok!='')

        {

            /*

			if(strlen($Blok)<10)

            {

                exit("Error: Block required");

            }

			*/

                $lokKerja=$Blok;

        }



        if($biaya=='')

            $biaya=0;

        $sins="insert into ".$dbname.".vhc_rundt (`notransaksi`,`jenispekerjaan`,`alokasibiaya`,`beratmuatan`,`jumlahrit`,`keterangan`,`biaya`,`kmhmawal`,

                `kmhmakhir`,`jumlah`,`satuan`) 

                values ('".$notransaksi_head."','".$jnsPekerjaan."','".$lokKerja."','".$brtMuatan."','".$jmlhRit."','".$ket."'

                ,'".$biaya."','".$kmhmAwal."','".$kmhmAkhir."','".$jumlah."','".$satuan."')";



        if(mysql_query($sins))

        {

            $sKm="select distinct kmhmakhir from ".$dbname.".vhc_kmhmakhir_vw where kodevhc='".$optKdVhc[$notransaksi_head]."'";

            $qKm=mysql_query($sKm) or die(mysql_error($conn));

            $rKm=mysql_fetch_assoc($qKm);

            echo intval($rKm['kmhmakhir']);

        }

        else

        {	echo "DB Error : ".mysql_error($conn);	 }

                break;



        case'update_kerja':

        if(($brtMuatan=='')||($jmlhRit==''))

        {

                echo"warning:Please Complete The Form";

                exit();

        }

        if($Blok!='')

        {

                $lokKerja=$Blok;

                if($lokKerja!=$oldBlok)

                {

                        $where.=" and alokasibiaya='".$oldBlok."'";

                }

                else

                {

                        $where.=" and alokasibiaya='".$lokKerja."'";

                }

        }

        else

        {

                if($old_lokKerja!=$lokKerja)

                {

                        $where.=" and alokasibiaya='".$old_lokKerja."'";

                }

                else

                {

                        $where.=" and alokasibiaya='".$lokKerja."'";

                }

        }

        if($oldjnsPekerjaan!='')

        {

                if($jnsPekerjaan!=$oldjnsPekerjaan)

                {

                        $where.="  and jenispekerjaan='".$oldjnsPekerjaan."'";

                }

                else

                {

                        $where.="  and jenispekerjaan='".$jnsPekerjaan."'";

                }

        }

        if($kmhmAwal>=$kmhmAkhir)

        {

                echo"warning:".$_SESSION['lang']['vhc_kmhm_awal']." must lower then ".$_SESSION['lang']['vhc_kmhm_akhir']."";

                exit();

        }

         $jumlah=$kmhmAkhir-$kmhmAwal;

        $sup="update ".$dbname.".vhc_rundt set jenispekerjaan='".$jnsPekerjaan."',alokasibiaya='".$lokKerja."',beratmuatan='".$brtMuatan."'

        ,jumlahrit='".$jmlhRit."',keterangan='".$ket."',biaya='".$biaya."',kmhmawal='".$kmhmAwal."',kmhmakhir='".$kmhmAkhir."',jumlah='".$jumlah."'

        ,satuan='".$satuan."' where notransaksi='".$notransaksi_head."' ".$where."";

        //exit("Error:".$sup);

        if(mysql_query($sup))

        {



            $sKm="select distinct kmhmakhir from ".$dbname.".vhc_kmhmakhir_vw where kodevhc='".$optKdVhc[$notransaksi_head]."'";

            //exit("Error:".$sKm);

            $qKm=mysql_query($sKm) or die(mysql_error($conn));

            $rKm=mysql_fetch_assoc($qKm);

            echo intval($rKm['kmhmakhir']);

        }

        else

        {echo "DB Error : ".mysql_error($conn);	 }

        break;



        case'deleteKrj':

        $delKrj="delete from ".$dbname.".vhc_rundt where notransaksi='".$notransaksi_head."' and jenispekerjaan='".$jnsPekerjaan."'";

        if(mysql_query($delKrj))

        echo"";

        else

        echo "DB Error : ".mysql_error($conn);	 



        break;

        case'insert_operator':

        $sCekHt="select notransaksi from ".$dbname.".vhc_runht where notransaksi='".$notransaksi_head."'";

		//echo"warning:".$sCekHt;

        $qCekHt=mysql_query($sCekHt) or die(mysql_error());

        $rCekHt=mysql_num_rows($qCekHt);

        if($rCekHt<1)

        {

                echo"warning: Header required";

                exit();

        }



        $sPeriode="select periode from ".$dbname.".sdm_5periodegaji where kodeorg='".substr($rKode['kodeorg'],0,4)."' and periode='".substr($tglTrans,0,4)."-".substr($tglTrans,4,2)."'";# tanggalmulai<".$tglTrans." and tanggalsampai>=".$tglTrans;

        //echo $sPeriode;

        //exit("Error:");

        $qPeriode=mysql_query($sPeriode) or die(mysql_error($conn));

        $rPeriode=mysql_fetch_assoc($qPeriode);

        //echo"warning".$rPeriode['periode'];exit();

        /*if($rPeriode['periode']=='')

        {

        echo"warning: Transaction date out of range";

        exit();

        }*/

                $sKd="select lokasitugas,subbagian from ".$dbname.".datakaryawan where karyawanid='".$kdKry."'";

                $qKd=mysql_query($sKd) or die(mysql_error());

                $rKd=mysql_fetch_assoc($qKd);

                $lokasiTugas=$rKd['lokasitugas'];

                if(!is_null($rKd['subbagian'])||$rKd['subbagian']!=0||$rKd['subbagian']!='')

                {

                   $lokasiTugas=$rKd['subbagian'];

                }







        if($posisi==1)

        {

                $sCek="select count(posisi) as jmlh from ".$dbname.".vhc_runhk where notransaksi='".$notransaksi_head."' and posisi='1'";

                //echo "warning:".$sCek;

                $qCek=mysql_query($sCek) or die(mysql_error());

                $rCek=mysql_fetch_assoc($qCek);



                        if($rCek['jmlh']!=4)

                        {

                                $sqlIns="insert into ".$dbname.".vhc_runhk (`notransaksi`,`idkaryawan`,`posisi`,`tanggal`,`statuskaryawan`,`upah`,`premi`,`penalty`) values ('".$notransaksi_head."','".$kdKry."','".$posisi."','".$tglTrans."','".$rkry['tipe']."','".$uphOprt."','".$prmiOprt."','".$pnltyOprt."')";

                                //echo"warning:".$sqlIns;

                                if(mysql_query($sqlIns))

                                {									

                                    /*    //cek tanggal dan periode sudah ada di header atau blm

                                        $sInsAbsnC="select tanggal from ".$dbname.".sdm_absensiht where tanggal='".$tglTrans."' and periode='".$rPeriode['periode']."' and kodeorg='".$lokasiTugas."'";

                                        $qInsAbsnC=mysql_query($sInsAbsnC) or die(mysql_error($conn));

                                        $rInsAbsnC=mysql_num_rows($qInsAbsnC);

                                        if($rInsAbsnC>0)

                                        {



                                                $sCek="select karyawanid from ".$dbname.".sdm_absensidt where kodeorg='".$lokasiTugas."' and tanggal='".$tglTrans."' and karyawanid='".$kdKry."'";



                                                $qCek=mysql_query($sCek) or die(mysql_error($conn));

                                                $rCek=mysql_num_rows($qCek);

                                                if($rCek!=1)

                                                {

                                                $sUpdAbns="insert into ".$dbname.".sdm_absensidt (`kodeorg`, `tanggal`, `karyawanid`, `absensi`, `jam`, `jamPlg`) values ('".$lokasiTugas."','".$tglTrans."','".$kdKry."','H','07:00:00','15:00:00')";

                                                //echo"warning".$sUpdAbns;

                                                        if(!mysql_query($sUpdAbns))

                                                        {

                                                        echo "DB Error : ".mysql_error($conn);

                                                        }

                                                }

                                            
                                        }

                                        elseif($rInsAbsnC<1)

                                        {

                                                //echo"warning:Masuk aja B";

                                                $sInshead="insert into ".$dbname.".sdm_absensiht (`tanggal`, `kodeorg`, `periode`, `posting`) values('".$tglTrans."','".$lokasiTugas."','".$rPeriode['periode']."','0')";

                                                //echo"warning".$sInshead;

                                                if(mysql_query($sInshead))

                                                {

                                                $sUpdAbns="insert into ".$dbname.".sdm_absensidt (`kodeorg`, `tanggal`, `karyawanid`, `absensi`, `jam`, `jamPlg`) values ('".$lokasiTugas."','".$tglTrans."','".$kdKry."','H','07:00:00','15:00:00')";

                                                        if(!mysql_query($sUpdAbns))

                                                        {

                                                                echo "DB Error : ".mysql_error($conn);

                                                        }	

                                                }

                                                else

                                                {

                                                echo "DB Error : ".mysql_error($conn);

                                                }
                                            					

                                        }
                                */

                                }

                                else

                                {

                                        echo "DB Error : ".mysql_error($conn);	

                                }

                        }

                        else

                        {

                                echo"warning: Can`t complete transaction, Operator maximum limit exeed";

                                exit();

                        }

        }

        elseif($posisi==0)

        {

                $sCekSop="select count(posisi) as jmlh from ".$dbname.".vhc_runhk where notransaksi='".$notransaksi_head."' and posisi='0'";

                //echo "warning:".$sCekSop;

                $qCekSop=mysql_query($sCekSop) or die(mysql_error());

                $rCekSop=mysql_fetch_assoc($qCekSop);

                if($rCekSop['jmlh']==1)

                {

                        echo"warning: Operator can only one";

                        break;

                        exit();

                }

                elseif($rCekSop['jmlh']==0)

                {



                                $sqlIns="insert into ".$dbname.".vhc_runhk (`notransaksi`,`idkaryawan`,`posisi`,`tanggal`,`statuskaryawan`,`upah`,`premi`,`penalty`) values ('".$notransaksi_head."','".$kdKry."','".$posisi."','".$tglTrans."','".$rkry['tipe']."','".$uphOprt."','".$prmiOprt."','".$pnltyOprt."')";

                                        //echo"warning:".$sqlIns;

                                if(mysql_query($sqlIns))

                                {

                                    /*    //cek tanggal dan periode sudah ada di header atau blm

                                        $sInsAbsnC="select tanggal from ".$dbname.".sdm_absensiht where tanggal='".$tglTrans."' and kodeorg='".$lokasiTugas."'";

                                        //exit("Error:".$sInsAbsnC);

                                        $qInsAbsnC=mysql_query($sInsAbsnC) or die(mysql_error($conn));

                                        $rInsAbsnC=mysql_num_rows($qInsAbsnC);

                                        if($rInsAbsnC>0)

                                        {

                                        //echo"warning:Masuk aja A";

                                                $sCek="select karyawanid from ".$dbname.".sdm_absensidt where kodeorg='".$lokasiTugas."' and tanggal='".$tglTrans."' and karyawanid='".$kdKry."'";

                                                $qCek=mysql_query($sCek) or die(mysql_error($conn));

                                                $rCek=mysql_num_rows($qCek);

                                                if($rCek<1)

                                                {

                                                $sUpdAbns="insert into ".$dbname.".sdm_absensidt (`kodeorg`, `tanggal`, `karyawanid`, `absensi`, `jam`, `jamPlg`) values ('".$lokasiTugas."','".$tglTrans."','".$kdKry."','H','07:00:00','15:00:00')";

                                                //echo"warning".$sUpdAbns;

                                                        if(!mysql_query($sUpdAbns))

                                                        {

                                                        echo "DB Error : ".mysql_error($conn);

                                                        }

                                                }

                                        }

                                        elseif($rInsAbsnC<1)

                                        {

                                                //echo"warning:Masuk aja B";

                                                $sInshead="insert into ".$dbname.".sdm_absensiht (`tanggal`, `kodeorg`, `periode`, `posting`) values('".$tglTrans."','".$lokasiTugas."','".$rPeriode['periode']."','0')";

                                                //echo"warning".$sInshead;

                                                if(mysql_query($sInshead))

                                                {

                                                $sUpdAbns="insert into ".$dbname.".sdm_absensidt (`kodeorg`, `tanggal`, `karyawanid`, `absensi`, `jam`, `jamPlg`) values ('".$lokasiTugas."','".$tglTrans."','".$kdKry."','H','07:00:00','15:00:00')";

                                                        if(!mysql_query($sUpdAbns))

                                                        {

                                                                echo "DB Error : ".mysql_error($conn);

                                                        }	

                                                }

                                                else

                                                {

                                                echo "DB Error : ".mysql_error($conn);

                                                }					

                                        }

                                */
                                }

                                else

                                {

                                        echo "DB Error : ".mysql_error($conn);

                                }

                        }

        }

		

        elseif($posisi==2)

        {

                $sCekSop="select count(posisi) as jmlh from ".$dbname.".vhc_runhk where notransaksi='".$notransaksi_head."' and posisi='2'";

                //echo "warning:".$sCekSop;

                $qCekSop=mysql_query($sCekSop) or die(mysql_error());

                $rCekSop=mysql_fetch_assoc($qCekSop);

                if($rCekSop['jmlh']==1)

                {

                        echo"warning: Operator can only one";

                        break;

                        exit();

                }

                elseif($rCekSop['jmlh']==0)

                {



                                $sqlIns="insert into ".$dbname.".vhc_runhk (`notransaksi`,`idkaryawan`,`posisi`,`tanggal`,`statuskaryawan`,`upah`,`premi`,`penalty`) values ('".$notransaksi_head."','".$kdKry."','".$posisi."','".$tglTrans."','".$rkry['tipe']."','".$uphOprt."','".$prmiOprt."','".$pnltyOprt."')";

                                        //echo"warning:".$sqlIns;

                                if(mysql_query($sqlIns))

                                {

                                /*        //cek tanggal dan periode sudah ada di header atau blm

                                        $sInsAbsnC="select tanggal from ".$dbname.".sdm_absensiht where tanggal='".$tglTrans."' and kodeorg='".$lokasiTugas."'";

                                        //exit("Error:".$sInsAbsnC);

                                        $qInsAbsnC=mysql_query($sInsAbsnC) or die(mysql_error($conn));

                                        $rInsAbsnC=mysql_num_rows($qInsAbsnC);

                                        if($rInsAbsnC>0)

                                        {

                                        //echo"warning:Masuk aja A";

                                                $sCek="select karyawanid from ".$dbname.".sdm_absensidt where kodeorg='".$lokasiTugas."' and tanggal='".$tglTrans."' and karyawanid='".$kdKry."'";

                                                $qCek=mysql_query($sCek) or die(mysql_error($conn));

                                                $rCek=mysql_num_rows($qCek);

                                                if($rCek<1)

                                                {

                                                $sUpdAbns="insert into ".$dbname.".sdm_absensidt (`kodeorg`, `tanggal`, `karyawanid`, `absensi`, `jam`, `jamPlg`) values ('".$lokasiTugas."','".$tglTrans."','".$kdKry."','H','07:00:00','15:00:00')";

                                                //echo"warning".$sUpdAbns;

                                                        if(!mysql_query($sUpdAbns))

                                                        {

                                                        echo "DB Error : ".mysql_error($conn);

                                                        }

                                                }

                                        }

                                        elseif($rInsAbsnC<1)

                                        {

                                                //echo"warning:Masuk aja B";

                                                $sInshead="insert into ".$dbname.".sdm_absensiht (`tanggal`, `kodeorg`, `periode`, `posting`) values('".$tglTrans."','".$lokasiTugas."','".$rPeriode['periode']."','0')";

                                                //echo"warning".$sInshead;

                                                if(mysql_query($sInshead))

                                                {

                                                $sUpdAbns="insert into ".$dbname.".sdm_absensidt (`kodeorg`, `tanggal`, `karyawanid`, `absensi`, `jam`, `jamPlg`) values ('".$lokasiTugas."','".$tglTrans."','".$kdKry."','H','07:00:00','15:00:00')";

                                                        if(!mysql_query($sUpdAbns))

                                                        {

                                                                echo "DB Error : ".mysql_error($conn);

                                                        }	

                                                }

                                                else

                                                {

                                                echo "DB Error : ".mysql_error($conn);

                                                }					

                                        }

                                */
                                }

                                else

                                {

                                        echo "DB Error : ".mysql_error($conn);

                                }

                        }

        }		

        break;

        case 'update_operator':

        if($posisi==1)

        {

                $sCek="select count(posisi) as jmlh from ".$dbname.".vhc_runhk where notransaksi='".$notransaksi_head."' and posisi='1'";

                //echo "warning:".$sCek;

                $qCek=mysql_query($sCek) or die(mysql_error());

                $rCek=mysql_fetch_assoc($qCek);

        }

        elseif($posisi==0)

        {

                $sCekSop="select count(posisi) as jmlh from ".$dbname.".vhc_runhk where notransaksi='".$notransaksi_head."' and posisi='0'";

                //echo "warning:".$sCekSop;

                $qCekSop=mysql_query($sCekSop) or die(mysql_error());

                $rCekSop=mysql_fetch_assoc($qCekSop);

        }

        elseif($posisi==2)

        {

                $sCekSop2="select count(posisi) as jmlh from ".$dbname.".vhc_runhk where notransaksi='".$notransaksi_head."' and posisi='2'";

                //echo "warning:".$sCekSop;

                $qCekSop2=mysql_query($sCekSop2) or die(mysql_error());

                $rCekSop2=mysql_fetch_assoc($qCekSop2);

        }		

        if($rCek['jmlh']>4)

        {

                echo"warning: Can`t complete transaction, Operator maximum limit exeed";

                exit();

        }

        if($rCekSop['jmlh']>1)

        {

                echo"warning: Can`t complete transaction, Operator maximum limit exeed";

                exit();

        }

        if($rCekSop2['jmlh']>1)

        {

                echo"warning: Can`t complete transaction, Operator maximum limit exeed";

                exit();

        }		

        $skry="select a.`alokasi`,b.tipe from ".$dbname.".datakaryawan a inner join ".$dbname.".sdm_5tipekaryawan b on 

        a.tipekaryawan=b.id where karyawanid='".$kdKry."'"; 

        //echo "warning:".$skry;

        $qkry=mysql_query($skry) or die(mysql_error());

        $rkry=mysql_fetch_assoc($qkry);



        $sup_op="update ".$dbname.".vhc_runhk set posisi='".$posisi."',tanggal='".$tglTrans."',statuskaryawan='".$rkry['tipe']."',upah='".$uphOprt."',premi='".$prmiOprt."',penalty='".$pnltyOprt."' where notransaksi='".$notransaksi_head."' and idkaryawan='".$kdKry."'";

        if(mysql_query($sup_op))

        echo"";

        else

                echo "DB Error : ".mysql_error($conn);

        break;



// Ambil dari premi kegiatan di vhc_kegiatan

		case'getUmr':

        if($_POST['tahun']!='')

            $tahun=$_POST['tahun'];

        else {

            $tahun=date('Y');

        }



/*

		$sUmr="select sum(jumlah) as jumlah from ".$dbname.".sdm_5gajipokok 

            where karyawanid='".$kdKry."' and tahun=".$tahun."  and idkomponen in (1,31)";

        $qUmr=mysql_query($sUmr) or die(mysql_error());

        $rUmr=mysql_fetch_assoc($qUmr);

        $umr=$rUmr['jumlah']/25;

        echo intval($umr);

*/

        $qwe = date('D', strtotime($tnggl));

		$totPremi = 0;

		$regional = $_SESSION['empl']['regional'];



		$sqlx= "select jenispekerjaan as kodekegiatan, beratmuatan as jmlkegiatan from ".$dbname.".vhc_rundt where notransaksi='".$notransaksi_head."'";

        $queryx=mysql_query($sqlx) or die(mysql_error());

        while($resx=mysql_fetch_assoc($queryx))

        {

			$jml= $resx['jmlkegiatan'];

			$kdkeg= $resx['kodekegiatan'];

			$sql1= "select kodekegiatan,basis,hargasatuan,hargaslebihbasis,hargaminggu from ".$dbname."

					.vhc_kegiatan where kodekegiatan='".$kdkeg."' and regional= '".$regional."'";

			$query1=mysql_query($sql1) or die(mysql_error());

			while($res1=mysql_fetch_assoc($query1))

			{

//				echo "warning : ".$res1['kodekegiatan']."/".$res1['basis']."/"

//					.$res1['hargasatuan']."/".$res1['hargaslebihbasis']."/".$res1['hargaminggu']." // ".$jml;

//				exit();

				

				if ($qwe == 'Sun'){

					$totPremi = $totPremi + ($jml * $res1['hargaminggu']);

				} else {

					// jika hargasatuan > 0 maka jml langsung dikali hargasatuan

					if ($res1['hargsatuan'] > 0){

						$totPremi = $totPremi + ($jml * $res1['hargasatuan']);

					} else {

						if ($jml - $res1['basis'] > 0){

							$lbhbasis = ($jml - $res1['basis']) * $res1['hargaslebihbasis'];

//							echo "warning : ".$lbhbasis;

//							exit();



						} else {

							$lbhbasis = 0;

						}

						$totPremi = $totPremi + $lbhbasis;

					}

				}

			}

		}

        $umr=$totPremi;

        echo intval($umr);

        break;



        case'load_data_opt':

        $arrPos=array("Sopir","Kondektur", "Operator");

        $sql="select * from ".$dbname.".vhc_runhk where substring(notransaksi,1,4)='".$rKode['kodeorg']."' and notransaksi='".$notransaksi_head."' order by notransaksi desc"; //echo "warning:".$sql;

        $query=mysql_query($sql) or die(mysql_error());

        while($res=mysql_fetch_assoc($query))

        {

                $skry="select `namakaryawan` from ".$dbname.".datakaryawan where karyawanid='".$res['idkaryawan']."'";

                $qkry=mysql_query($skry) or die(mysql_error());

                $rkry=mysql_fetch_assoc($qkry);

                $no+=1;

                echo"

                <tr class=rowcontent>

                <td>".$no."</td>

                <td>".$res['notransaksi']."</td>

                <td>".$rkry['namakaryawan']."</td>

                <td>".$arrPos[$res['posisi']]."</td>

                <td>".number_format($res['upah'],2)."</td>

                <td>".number_format($res['premi'],2)."</td>

                <td>".number_format($res['penalty'],2)."</td>

                <td align=center>

                <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('". $res['notransaksi']."','". $res['idkaryawan']."');\" >	

                </td>

                </tr>

                ";

        }

        break;

		

        case'getKntrk':

        $optKntrk="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

        $sSpk="select notransaksi from ".$dbname.".log_spkht where kodeorg='".$lokasi."' and posting<>'0' and tanggal like '%".$thnKntrk."%'";

        //echo "warning:".$sSpk;

        $qSpk=mysql_query($sSpk) or die(mysql_error());

        $rSpk=mysql_num_rows($qSpk);

        if($rSpk>0)

        {

                while($rSpk=mysql_fetch_assoc($qSpk))

                {

                        $optKntrk.="<option value=".$rSpk['notransaksi']." ".($rSpk['notransaksi']==$noKntrak?'selected':'').">".$rSpk['notransaksi']."</option>";

                }



        }

        else

        {

                $optKntrk="<option value=''></option>";

                //echo $optKntrk;

        }

        echo $optKntrk;

        break;



        case'delete_opt':

            $sTanggal="select distinct tanggal from ".$dbname.".vhc_runht where notransaksi='".$notransaksi."'";

            $qTanggal=mysql_query($sTanggal) or die(mysql_error($conn));

            $rTanggal=mysql_fetch_assoc($qTanggal);

            $delAbsen="delete from ".$dbname.".sdm_absensidt where karyawanid='".$kdKry."' and tanggal='".$rTanggal['tanggal']."'";

            if(mysql_query($delAbsen))

            {

                $sdel="delete from ".$dbname.".vhc_runhk where notransaksi='".$notransaksi."' and idkaryawan='".$kdKry."'";

                //echo "warning:".$sdel;

                if(mysql_query($sdel))

                echo"";

                else

                echo "DB Error : ".mysql_error($conn);

            }

            else

            {

                 echo "DB Error : ".$delAbsen."___".mysql_error($conn);

            }

        break;

		

        case'getBlok':

        $optBlok="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

        $sBlok="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 

                where induk like '%".$lokKerja."%' and tipe='BLOK'

                and kodeorganisasi in (select distinct kodeorg from ".$dbname.".setup_blok where left(kodeorg,4)='".$lokKerja."' and luasareaproduktif>0)";

        $qBlok=mysql_query($sBlok) or die(mysql_error());

        while($rBlok=mysql_fetch_assoc($qBlok))

        {

                if($Blok!="")

                {

                        $optBlok.="<option value=".$rBlok['kodeorganisasi']." ".($rBlok['kodeorganisasi']==$Blok?"selected":"").">".$rBlok['namaorganisasi']."</option>";

                }

                else

                {

                        $optBlok.="<option value=".$rBlok['kodeorganisasi'].">".$rBlok['namaorganisasi']."</option>";

                }

        }

            #khusus Project:

              $str="select kode,nama from  ".$dbname.".project where kodeorg='".$lokKerja."' and posting=0";

              $res=mysql_query($str);

              while($bar=mysql_fetch_object($res))

              {

                  $optBlok.="<option value=".$bar->kode.">Project-".$bar->nama."</option>";

              }

        echo $optBlok;

        break;

        default:

        break;

}

?>