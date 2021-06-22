<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include('lib/zMysql.php');
include('lib/zFunction.php');
include_once('lib/zLib.php');
//$arr="##tglHarga##kdBarang##satuan##idPasar##idMatauang##hrgPasar##proses";
$proses=$_POST['proses'];
$pabrik=$_POST['pabrik'];
$supplier=$_POST['supplier'];
$hargab=$_POST['hargab'];
$hargas=$_POST['hargas'];
$hargak=$_POST['hargak'];
$tanggal=tanggalsystem_hrd($_POST['tanggal']);

$caripabrik=$_POST['caripabrik'];
$carisupplier=$_POST['carisupplier'];
$caritanggal=tanggalsystem_hrd($_POST['caritanggal']);

if($proses=='insert')
if(($pabrik=='')or($supplier=='')or($tanggal=='')){
    echo "error: Please fill required fields.";
    exit();
}

$optsupplier=makeOption($dbname, 'log_5supplier', 'kodetimbangan,namasupplier');
$where="tanggal='".$tanggal."' and supplier='".$supplier."' and pabrik='".$pabrik."'";

        switch($proses)
        {

//            case'getSatuan':
//            $sSatuan="select distinct satuan from ".$dbname.".log_5masterbarang where kodebarang='".$kdBarang."'";
//            $qSatuan=mysql_query($sSatuan) or die(mysql_error($conn));
//            $rSatuan=mysql_fetch_assoc($qSatuan);
//            echo $rSatuan['satuan'];
//            break;
            case'insert':
                $sCek="select distinct * from ".$dbname.".pmn_hargatbsharian where ".$where."";
                $qCek=mysql_query($sCek) or die(mysql_error($conn));
                $rCek=mysql_num_rows($qCek);
                if($rCek<1)
                {
            $sIns="insert into ".$dbname.".pmn_hargatbsharian (pabrik, supplier, tanggal, hargab, hargas, hargak, karyawanid) 
                   values ('".$pabrik."','".$supplier."','".$tanggal."','".$hargab."','".$hargas."','".$hargak."','".$_SESSION['standard']['userid']."')";
                if(!mysql_query($sIns))
                { echo "Gagal,".addslashes(mysql_error($conn));}
                }
                else
                {
                    exit("Error: Already exist");
                }
            break;
            case'update':
            $sIns="update ".$dbname.".pmn_hargatbsharian set hargab='".$hargab."', hargas='".$hargas."', hargak='".$hargak."'
                   where ".$where."";
            if(!mysql_query($sIns))
                { echo "Gagal,".addslashes(mysql_error($conn));}
            break;
                case'delData':
                $sDel="delete from ".$dbname.".pmn_hargatbsharian where ".$where." ";
//                    echo "error:".$sDel;
                    if(!mysql_query($sDel))
                    {
                        echo " Gagal,".(mysql_error($conn));
                    }
                break;            
                case'loadData':

                $limit=20;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;

                $ql2="select count(*) as jmlhrow from ".$dbname.".pmn_hargatbsharian order by `tanggal` desc, pabrik";// echo $ql2;
                $query2=mysql_query($ql2) or die(mysql_error());
                while($jsl=mysql_fetch_object($query2)){
                $jlhbrs= $jsl->jmlhrow;
                }

                $str="select * from ".$dbname.".pmn_hargatbsharian order by `tanggal` desc, pabrik limit ".$offset.",".$limit."";
                //echo "warning:".$str;exit();
                if($res=mysql_query($str))
                {
                    $barisData=mysql_num_rows($res);
//                    if($barisData>0)
//                    {
                        while($bar=mysql_fetch_object($res))
                        {

                        $no+=1;


                        echo"<tr class=rowcontent id='tr_".$no."'>
                        <td>".$no."</td>

                        <td>".$bar->pabrik."</td>
                        <td>".$optsupplier[$bar->supplier]."</td>
                        <td>".tanggalnormal_hrd($bar->tanggal)."</td>
                        <td align=right>".number_format($bar->hargab,2)."</td>
                        <td align=right>".number_format($bar->hargas,2)."</td>
                        <td align=right>".number_format($bar->hargak,2)."</td>
                        <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->pabrik."','".tanggalnormal_hrd($bar->tanggal)."','".$bar->supplier."','".$bar->hargab."','".$bar->hargas."','".$bar->hargak."');\">
                            <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$bar->pabrik."','".tanggalnormal_hrd($bar->tanggal)."','".$bar->supplier."');\"></td>
                        </tr>";
                        }	 	
                        echo"
                        <tr><td colspan=11 align=center>
                        ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                        <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                        <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                        </td>
                        </tr>";  
//                    }
//                    else
//                    {
//                        echo"<tr class=rowcontent><td colspan=8>".$_SESSION['lang']['dataempty']."</td></tr>";
//                    }
                }	
                else
                {
                echo " Gagal,".(mysql_error($conn));
                }	
                echo"</tbody></table>";
                break;

                case'cariData':
                if($caripabrik!='')
                {
                    $wre.=" and pabrik='".$caripabrik."'";
                }
                if($caritanggal!='')
                {
                     $wre.=" and tanggal='".$caritanggal."'";
                }
                if($carisupplier!='')
                {
                    $wre.=" and supplier='".$carisupplier."'";
                }
                $limit=20;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;

                $ql2="select count(*) as jmlhrow from ".$dbname.".pmn_hargatbsharian where tanggal!='' ".$wre." order by `tanggal` desc, pabrik";// echo $ql2;
                $query2=mysql_query($ql2) or die(mysql_error());
                while($jsl=mysql_fetch_object($query2)){
                $jlhbrs= $jsl->jmlhrow;
                }


                $str="select * from ".$dbname.".pmn_hargatbsharian where tanggal!='' ".$wre." order by `tanggal` desc, pabrik  limit ".$offset.",".$limit."";
                //echo "warning:".$str;exit();
                if($res=mysql_query($str))
                {
                    $barisData=mysql_num_rows($res);
//                    if($barisData>0)
//                    {
                        while($bar=mysql_fetch_object($res))
                        {

                        $no+=1;


                        echo"<tr class=rowcontent id='tr_".$no."'>
                        <td>".$no."</td>

                        <td>".$bar->pabrik."</td>
                        <td>".$optsupplier[$bar->supplier]."</td>
                        <td>".tanggalnormal_hrd($bar->tanggal)."</td>
                        <td align=right>".number_format($bar->hargab,2)."</td>
                        <td align=right>".number_format($bar->hargas,2)."</td>
                        <td align=right>".number_format($bar->hargak,2)."</td>
                        <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->pabrik."','".tanggalnormal_hrd($bar->tanggal)."','".$bar->supplier."','".$bar->hargab."','".$bar->hargas."','".$bar->hargak."');\">
                            <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$bar->pabrik."','".tanggalnormal_hrd($bar->tanggal)."','".$bar->supplier."');\"></td>
                        </tr>";
                        }	 	
                        echo"
                        <tr><td colspan=11 align=center>
                        ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                        <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                        <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                        </td>
                        </tr>";  
//                    }
//                    else
//                    {
//                        echo"<tr class=rowcontent><td colspan=8>".$_SESSION['lang']['dataempty']."</td></tr>";
//                    }
                }	
                else
                {
                echo " Gagal,".(mysql_error($conn));
                }		
                echo"</tbody></table>";
                break;


                default:
                break;
        }

?>