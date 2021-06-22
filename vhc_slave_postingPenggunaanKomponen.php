<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses=$_POST['proses'];
$notransaksi=$_POST['notrans'];
$lokasi=$_SESSION['empl']['lokasitugas'];
$user_online=$_SESSION['standard']['userid'];
$kdVhc=$_POST['kdVhc'];
$sOrg="select kodeorganisasi from ".$dbname.".organisasi where  tipe in ('KEBUN','KANWIL','TRAKSI')";

        $qOrg=mysql_query($sOrg) or die(mysql_error());
        while($rOrg=mysql_fetch_assoc($qOrg))
        {
                $kodeOrg.="'".$rOrg['kodeorganisasi']."',";
        }
        $svhc2="select kodeorg from ".$dbname.".vhc_5master group by kodeorg"; //echo $svhc;
        $qvhc2=mysql_query($svhc2) or die(mysql_error());
        while($rvhc2=mysql_fetch_assoc($qvhc2))
        {
                $kodeOrg.="'".$rvhc2['kodeorg']."',";
        }

        $pnjgn=strlen($kodeOrg)-1;

switch($proses)
{	
        case'load_data':
        echo"<table cellspacing='1' border='0' class='sortable'>
        <thead>
        <tr class='rowheader'>
        <td>".$_SESSION['lang']['notransaksi']."</td>
        <td>".$_SESSION['lang']['tanggal']."</td>
        <td>".$_SESSION['lang']['kodevhc']."</td>
        <td>".$_SESSION['lang']['jenisvch']."</td>
        <td>Action</td>
        </tr>
        </thead>
        <tbody>";
				$alias= '';
                $limit=20;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;

                //$sql2="select count(*) as jmlhrow from ".$dbname.".vhc_penggantianht where kodeorg in (".substr($kodeOrg,0,$pnjgn).") order by `notransaksi` desc";
                $sql2="select count(*) as jmlhrow from ".$dbname.".vhc_penggantianht where 
                       kodevhc in (select distinct kodevhc from ".$dbname.".vhc_5master where kodetraksi like '%".$_SESSION['empl']['lokasitugas']."%')
                       and posting=0 order by tanggal desc";
                       //where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%'  order by `notransaksi` desc";
                $query2=mysql_query($sql2) or die(mysql_error());
                while($jsl=mysql_fetch_object($query2)){
                $jlhbrs= $jsl->jmlhrow;
                }
                $slvhc="select * from ".$dbname.".vhc_penggantianht where
                        kodevhc in (select distinct kodevhc from ".$dbname.".vhc_5master where kodetraksi like '%".$_SESSION['empl']['lokasitugas']."%')
                        and posting=0 order by tanggal desc";
                        // where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%'  order by `notransaksi` desc limit ".$offset.",".$limit."";
                $qlvhc=mysql_query($slvhc) or die(mysql_error());
                while($rlvhc=mysql_fetch_assoc($qlvhc))
                {
                $pvhc="select kodevhc,jenisvhc from ".$dbname.".vhc_5master where kodevhc='".$rlvhc['kodevhc']."'order by kodevhc";
                $qpvhc=mysql_query($pvhc) or die(mysql_error());
                $rpvhc=mysql_fetch_assoc($qpvhc);
                echo"
                <tr class=rowcontent>
                <td>". $rlvhc['notransaksi']."</td>
                <td>". tanggalnormal($rlvhc['tanggal'])."</td>
                <td>". $rlvhc['kodevhc']."</td>
                <td>". $rpvhc['jenisvhc']."</td>";
                $sCek="select kodejabatan from ".$dbname.".datakaryawan where karyawanid='".$_SESSION['standard']['userid']."'";
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_fetch_assoc($qCek);
				// alias jabatan
				$sql1= "select alias from sdm_5jabatan where kodejabatan=".$rCek['kodejabatan'];
                $qsql1=mysql_query($sql1) or die(mysql_error());
                $rsql1=mysql_fetch_assoc($qsql1);
				$alias= $rsql1['alias'];

                if($alias=='KTU' || $alias=='Asisten')
				//if($rCek['kodejabatan']==98)
                {
                echo"<td>
                <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_penggantianht','".$rlvhc['notransaksi'].",".$rlvhc['kodevhc']."','','vhc_slave_penggunaanKomponen',event);\">&nbsp;";
                        if($rlvhc['posting']<1)
                        {
                                echo
                                "<a href=# onClick=\"posting_data('".$rlvhc['notransaksi']."','".$rlvhc['kodevhc']."')\" >".$_SESSION['lang']['belumposting']."</a>
                                </td>";
                        }
                        else
                        { echo $_SESSION['lang']['posting'];}
                }
                else{ 
                if($rlvhc['posting']<1)
                $post=$_SESSION['lang']['belumposting'];
                else
                $post=$_SESSION['lang']['posting'];
                echo"<td>		<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_penggantianht','".$rlvhc['notransaksi'].",".$rlvhc['kodevhc']."','','vhc_slave_penggunaanKomponen',event);\">&nbsp;".$post."
                    </td>";
                
                }
                echo"
                </tr>";
                }
                echo"
        <tr><td colspan=5 align=center>
        ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
        <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
        <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
        </td>
        </tr></tbody></table>";
                break;
                case 'cari_transaksi':
                //echo "warning:masuk";
                echo"<div style='overflow:auto;height:450px;'>
                <table cellspacing='1' border='0' class='sortable'>
        <thead>
        <tr class='rowheader'>
        <td>".$_SESSION['lang']['notransaksi']."</td>
        <td>".$_SESSION['lang']['tanggal']."</td>
        <td>".$_SESSION['lang']['kodevhc']."</td>
        <td>".$_SESSION['lang']['jenisvch']."</td>
        <td>Action</td>
        </tr>
        </thead>
        <tbody>";

                if(isset($_POST['txtSearch']))
                {
                        $txt_search=$_POST['txtSearch'];
                        $txt_tgl=tanggalsystem($_POST['txtTgl']);
                        $txt_tgl_a=substr($txt_tgl,0,4);
                        $txt_tgl_b=substr($txt_tgl,4,2);
                        $txt_tgl_c=substr($txt_tgl,6,2);
                        $txt_tgl=$txt_tgl_a."-".$txt_tgl_b."-".$txt_tgl_c;
                }
                else
                {
                        $txt_search='';
                        $txt_tgl='';			
                }
                        if($txt_search!='')
                        {
                                $where="and notransaksi LIKE  '%".$txt_search."%' ";
                        }
                        elseif($txt_tgl!='')
                        {
                                $where="and tanggal LIKE '%".$txt_tgl."%' ";
                        }
                        elseif(($txt_tgl!='')&&($txt_search!=''))
                        {
                                $where="and notransaksi LIKE '%".$txt_search."%' and tanggal LIKE '%".$txt_tgl."%' ";
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

                $sql2="select count(*) as jmlhrow from ".$dbname.".vhc_penggantianht 
                       where kodevhc in (select distinct kodevhc from ".$dbname.".vhc_5master where kodetraksi like '%".$_SESSION['empl']['lokasitugas']."%')
                       ".$where." order by tanggal desc";
                 //echo "warning:".$sql2; exit();
                      //kodeorg like '%".$_SESSION['empl']['lokasitugas']."%'";
                $query2=mysql_query($sql2) or die(mysql_error());
                while($jsl=mysql_fetch_object($query2)){
                $jlhbrs= $jsl->jmlhrow;
                }

                $strx="select * from ".$dbname.".vhc_penggantianht 
                       where kodevhc in (select distinct kodevhc from ".$dbname.".vhc_5master where kodetraksi like '%".$_SESSION['empl']['lokasitugas']."%')
                       ".$where."  order by tanggal desc limit ".$offset.",".$limit." "; 
                       //kodeorg like '%".$_SESSION['empl']['lokasitugas']."%'  ".$where." limit ".$offset.",".$limit."";
                  //echo "warning:".$strx; exit();
                        if($res=mysql_query($strx))
                        {
                                $numrows=mysql_num_rows($res);
                                if($numrows<1)
                                {
                                        echo"<tr class=rowcontent><td colspan=5>Not Found</td></tr>";
                                }
                                else
                                {
                                        while($rlvhc=mysql_fetch_assoc($res))
                                        {
                                                $pvhc="select kodevhc,jenisvhc from ".$dbname.".vhc_5master order by kodevhc";
                                                $qpvhc=mysql_query($pvhc) or die(mysql_error());
                                                $rpvhc=mysql_fetch_assoc($qpvhc);
                                                echo"
                                                <tr class=rowcontent>
                                                <td>". $rlvhc['notransaksi']."</td>
                                                <td>". tanggalnormal($rlvhc['tanggal'])."</td>
                                                <td>". $rlvhc['kodevhc']."</td>
                                                <td>". $rpvhc['jenisvhc']."</td>";
                                                $sCek="select kodejabatan from ".$dbname.".datakaryawan where karyawanid='".$_SESSION['standard']['userid']."'";
                                                $qCek=mysql_query($sCek) or die(mysql_error());
                                                $rCek=mysql_fetch_assoc($qCek);
                                                if($alias=='KTU' || $alias=='Asisten')
												//if($rCek['kodejabatan']==98)
                                                {
                                                echo"<td>
                                                <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_penggantianht','".$rlvhc['notransaksi']."','".$rlvhc['notransaksi']."','vhc_slave_penggunaanKomponen',event);\">&nbsp;";
                                                if($rlvhc['posting']<1)
                                                {
                                                echo
                                                "<a href=# onClick=\"posting_data('".$rlvhc['notransaksi']."','".$rlvhc['kodevhc']."')\" >".$_SESSION['lang']['belumposting']."</a>
                                                </td>";
                                                }
                                                else
                                                { echo $_SESSION['lang']['posting'];}
                                                }
                                                else{ 
                                                if($rlvhc['posting']<1)
                                                $post=$_SESSION['lang']['belumposting'];
                                                else
                                                $post=$_SESSION['lang']['posting'];

                                                echo"<td>		<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_penggantianht','".$rlvhc['notransaksi'].",".$rlvhc['kodevhc']."','','vhc_slave_penggunaanKomponen',event);\">&nbsp;".$post."
                                                </td>";}
                                                echo"
                                                </tr>";
                                                }
                                                echo"
                                                <tr><td colspan=5 align=center>
                                                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                                                <button class=mybutton onclick=cariData(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                                                <button class=mybutton onclick=cariData(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                                                </td>
                                                </tr>";
                                                echo"</tbody></table></div>";
                                }
                         }	
                        else
                        {
                                echo "Gagal,".(mysql_error($conn));
                        }	
                break;
                case 'postingData':
                $scek="select kodeorg,updateby from ".$dbname.".vhc_penggantianht where notransaksi='".$notransaksi."' and posting='0'";
                $qcek=mysql_query($scek) or die(mysql_error());
                $rcek=mysql_fetch_assoc($qcek);
                $sCek="select kodejabatan from ".$dbname.".datakaryawan where karyawanid='".$_SESSION['standard']['userid']."'";
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_fetch_assoc($qCek);
				// alias jabatan
				$alias='';
				$sql1= "select alias from sdm_5jabatan where kodejabatan=".$rCek['kodejabatan'];
                $qsql1=mysql_query($sql1) or die(mysql_error());
                $rsql1=mysql_fetch_assoc($qsql1);
				$alias= $rsql1['alias'];
				
                if(!($alias=='KTU' || $alias=='Asisten'))
//                if($rCek['kodejabatan']!=98)
                {
                /*if(($rcek['updateby']==$user_online)||($rcek['updateby']==''))
                {*/
                        echo"Warning: Anda Tidak memiliki autorisasi (".$alias.") atau No Transaksi ini sudah di posting!!";
                        exit();
                }		
                $sudPost="update ".$dbname.".vhc_penggantianht set posting='1',postingby='".$user_online."' where notransaksi='".$notransaksi."' and kodevhc='".$kdVhc."'";
                if(mysql_query($sudPost))
                echo"";
                else
                echo "DB Error : ".mysql_error($conn);

                break;
        default:
        break;
}
?>