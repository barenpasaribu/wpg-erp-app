<?php 
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$kode_jns=$_POST['jns_id'];
$lokasi=$_SESSION['empl']['lokasitugas'];
$user_entry=$_SESSION['standard']['userid'];
$kode_vhc=$_POST['kode_vhc'];
$tgl_kerja=tanggalsystem($_POST['tglKerja']);
$kmhmAwal=$_POST['kmhmAwal'];
$kmhmAkhir=$_POST['kmhmAkhir'];
$satuan=$_POST['satuan'];
$jnsBbm=$_POST['jnsBbm'];
$jumlahBbm=$_POST['jumlah'];
$notransaksi_head=$_POST['no_trans'];
$proses=$_POST['proses'];
$kdVhc=$_POST['kdVhc'];
$statKary=0;
        $sOrg="select kodeorganisasi from ".$dbname.".organisasi where  kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";//tipe in ('KEBUN','KANWIL','TRAKSI')";

        $qOrg=mysql_query($sOrg) or die(mysql_error());
        while($rOrg=mysql_fetch_assoc($qOrg))
        {
                $kodeOrg.="'".$rOrg['kodeorganisasi']."',";
        }
$pnjgn=strlen($kodeOrg)-1;
switch($proses)
{
        case'load_data_header':
        echo"
        <table cellspacing='1' border='0' class=sortable>
        <thead>
        <tr class=\"rowheader\">
        <td>No.</td>
        <td>".$_SESSION['lang']['notransaksi']."</td>
        <td>".$_SESSION['lang']['jenisvch']."</td>
        <td>".$_SESSION['lang']['kodevhc']."</td>
        <td>".$_SESSION['lang']['tanggal']."</td>
        <td>".$_SESSION['lang']['vhc_kmhm_awal']."</td>
        <td>".$_SESSION['lang']['vhc_kmhm_akhir']."</td>
        <td>".$_SESSION['lang']['satuan']."</td>
        <td>".$_SESSION['lang']['vhc_jenis_bbm']."</td>
        <td>".$_SESSION['lang']['vhc_jumlah_bbm']."</td>
        <td align=center><input type=checkbox id=chkAll onclick=selectAll()></td>
        </tr></thead><tbody id=contentIsi>";


        //exit("Error".substr($inKodeorg,0,$pnjgn));
        $limit=20;
        $page=0;
        if(isset($_POST['page']))
        {
        $page=$_POST['page'];
        if($page<0)
        $page=0;
        }
        $offset=$page*$limit;

        $ql2="select count(*) as jmlhrow from ".$dbname.".vhc_runht where kodeorg='".$lokasi."' order by notransaksi,posting desc";// echo $ql2;
        $query2=mysql_query($ql2) or die(mysql_error());
        while($jsl=mysql_fetch_object($query2)){
        $jlhbrs= $jsl->jmlhrow;
        }
        $sql="select a.kmhmawal as kmhmawal,(a.kmhmawal+sum(jumlah)) as   
              kmhmakhir,a.satuan,a.notransaksi,b.jenisvhc,b.jenisbbm,b.kodevhc,b.tanggal, 
              b.jlhbbm,b.posting from ".$dbname.".vhc_rundt a
              left join ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' 
                  group by a.notransaksi order by tanggal desc,posting asc limit ".$offset.",".$limit."";
        //exit("Error".$sql);
        $query=mysql_query($sql) or die(mysql_error());
        while($res=mysql_fetch_assoc($query))
        {
                $sbrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$res['jenisbbm']."'";
                $qbrg=mysql_query($sbrg) or die(mysql_error());
                $rbrg=mysql_fetch_assoc($qbrg);
                $rbrg['namabarang'];
                $no+=1;
                echo"
                <tr class=rowcontent>
                <td>".$no."</td>
                <td align=center id=notransaksi_".$no.">".$res['notransaksi']."</td>
                <td align=center>".$res['jenisvhc']."</td>
                <td align=center  id=kdvhc_".$no.">".$res['kodevhc']."</td>
                <td align=center id=tgl_data_".$no.">".tanggalnormal($res['tanggal'])."</td>
                <td align=right>".number_format($res['kmhmawal'],0)."</td>
                <td align=right>".number_format($res['kmhmakhir'],0)."</td>
                <td align=center>".$res['satuan']."</td>
                <td align=center>".$rbrg['namabarang']."</td>
                <td align=right>".$res['jlhbbm']."</td>
                ";
                $sCek="select kodejabatan from ".$dbname.".datakaryawan where karyawanid='".$_SESSION['standard']['userid']."'";
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_fetch_assoc($qCek);
				// cek alias kodejabatan di tabel sdm_5jabatan
                $sCek="select alias from ".$dbname.".sdm_5jabatan where kodejabatan='".$rCek['kodejabatan']."'";
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_fetch_assoc($qCek);
//                if($rCek['kodejabatan']==98)
                if($rCek['alias']=='KTU' || $rCek['alias']=='Asisten')
                {
                echo"
                <td>
                <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_runht','".$res['notransaksi'].",". $res['kodevhc']."','','vhc_slave_pekerjaanPrint',event);\">";
                if($res['posting']<1  and $_SESSION['empl']['lokasitugas']==substr($res['notransaksi'],0,4))
                {
                        //echo"&nbsp;<a href=# onClick=postingData('".$res['notransaksi']."')>".$_SESSION['lang']['belumposting']."</a>";
                        echo"<input type=checkbox id=checkDt_".$no." title=".$_SESSION['lang']['belumposting']."  onclick=postData(".$no.")>";
                }
                else
                {
                        echo "&nbsp;".$_SESSION['lang']['posting'];
                }
                echo"</td>";}
                else
                {
                        if($res['posting']<1  and $_SESSION['empl']['lokasitugas']==substr($res['notransaksi'],0,4))
                        {
                        echo"<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_runht','".$res['notransaksi'].",". $res['kodevhc']."','','vhc_slave_pekerjaanPrint',event);\"> [".$_SESSION['lang']['belumposting']."] </td>";
                        }
                        else
                        {
                                echo"<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_runht','".$res['notransaksi'].",". $res['kodevhc']."','','vhc_slave_pekerjaanPrint',event);\">  [".$_SESSION['lang']['posting']."] </td>";
                        }
                }
                echo"</tr>";

        }
        echo"<tr class=rowheader><td colspan=11 align=center><div style=display:none id=tmblPosting><button class=mybutton onclick=postingData()>".$_SESSION['lang']['posting']."</button></div></td></tr>";
        echo" <tr><td colspan=11 align=center><div id=btnNextSmua style=display:block>
                                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
                                <br />
                                <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                                <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                                </div>
                                </td>
                                </tr></tbody>
</table>";
        break;
        case 'cari_transaksi':
        //echo"warning :masuk";
        if($_POST['txtSearch']!='')
                        {
                                $where.="and a.notransaksi LIKE  '%".$_POST['txtSearch']."%'";
                                $whr.="and notransaksi LIKE  '%".$_POST['txtSearch']."%'";
                        }

                        if($_POST['txtTglCr']!='')
                        {
                            $bln=explode("-",$_POST['txtTglCr']);
                            $cek=$bln[2]."-".$bln[1];
                            if($_POST['txtTgl']!='')
                            {
                                if($cek!=$_POST['txtTgl'])
                                {
                                    exit("Error:Tanggal tidak sama dengan periode");
                                }
                            }
                            $where.="and tanggal='".$bln[2]."-".$bln[1]."-".$bln[0]."'";
                            $whr.="and tanggal='".$bln[2]."-".$bln[1]."-".$bln[0]."'";
                        }
                        if($_POST['txtTgl']!='')
                        {
                                $where.="and tanggal LIKE  '".$_POST['txtTgl']."%' ";
                                $whr.="and tanggal LIKE  '".$_POST['txtTgl']."%' ";
                        }

                        if($_POST['statId']!='')
                        {
                                $where.="and posting='".$_POST['statId']."'";
                                $whr.="and posting='".$_POST['statId']."'";
                        }
                        else
                        {
                                $where.="and posting='0'";
                                $whr.="and posting='0'";
                        }
                        if($_POST['kdVhc']!='')
                        {
                            $where.="and b.kodevhc='".$_POST['kdVhc']."'";
                            $whr.="and kodevhc='".$_POST['kdVhc']."'";
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
                $ql2="select count(notransaksi) as jmlhrow from ".$dbname.".vhc_runht 
                      where  kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' ".$whr."
                      order by notransaksi desc"; //echo $ql2;

                        $strx="select a.kmhmawal as kmhmawal,(a.kmhmawal+sum(jumlah)) as
                               kmhmakhir,a.satuan,a.notransaksi,b.jenisvhc,b.jenisbbm,b.kodevhc,b.tanggal,b.jlhbbm,b.posting from ".$dbname.".vhc_rundt a
                      left join ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%'   
                                  ".$where."
                          group by a.notransaksi order by notransaksi  limit ".$offset.",".$limit."";
         echo"
        <div style='overflow:auto; height:550px;'>
        <table cellspacing='1' border='0' class=\"sortable\">
        <thead>
        <tr class=\"rowheader\">
        <td>No.</td>
        <td>".$_SESSION['lang']['notransaksi']."</td>
        <td>".$_SESSION['lang']['jenisvch']."</td>
        <td>".$_SESSION['lang']['kodevhc']."</td>
        <td>".$_SESSION['lang']['tanggal']."</td>
        <td>".$_SESSION['lang']['vhc_kmhm_awal']."</td>
        <td>".$_SESSION['lang']['vhc_kmhm_akhir']."</td>
        <td>".$_SESSION['lang']['satuan']."</td>
        <td>".$_SESSION['lang']['vhc_jenis_bbm']."</td>
        <td>".$_SESSION['lang']['vhc_jumlah_bbm']."</td>
        <td><input type=checkbox id=chkAll onclick=selectAll()></td>
        </tr></thead><tbody  id=contentIsi>";	
                $query2=mysql_query($ql2) or die(mysql_error());
                while($jsl=mysql_fetch_object($query2)){
                $jlhbrs= $jsl->jmlhrow;
                }
                        if($qres=mysql_query($strx))
                        {
                                $numrows=mysql_num_rows($qres);
                                if($numrows<1)
                                {
                                        echo"<tr class=rowcontent><td colspan=11>Not Found</td></tr>";
                                }
                                else
                                {
        while($res=mysql_fetch_assoc($qres))
        {
                $sbrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$res['jenisbbm']."'";
                $qbrg=mysql_query($sbrg) or die(mysql_error());
                $rbrg=mysql_fetch_assoc($qbrg);
                $rbrg['namabarang'];
                $no+=1;
                echo"
                <tr class=rowcontent>
                <td>".$no."</td>
                <td align=center  id=notransaksi_".$no.">".$res['notransaksi']."</td>
                <td align=center>".$res['jenisvhc']."</td>
                <td align=center  id=kdvhc_".$no.">".$res['kodevhc']."</td>
                <td align=center id=tgl_data_".$no.">".tanggalnormal($res['tanggal'])."</td>
                <td align=right>".number_format($res['kmhmawal'],0)."</td>
                <td align=right>".number_format($res['kmhmakhir'],0)."</td>
                <td align=center>".$res['satuan']."</td>
                <td align=center>".$rbrg['namabarang']."</td>
                <td align=right>".$res['jlhbbm']."</td>
                ";
                $sCek="select kodejabatan from ".$dbname.".datakaryawan where karyawanid='".$_SESSION['standard']['userid']."'";
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_fetch_assoc($qCek);
				// cek alias kodejabatan di tabel sdm_5jabatan
                $sCek="select alias from ".$dbname.".sdm_5jabatan where kodejabatan='".$rCek['kodejabatan']."'";
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_fetch_assoc($qCek);
//                if($rCek['kodejabatan']==98)
                if($rCek['alias']=='KTU' || $rCek['alias']=='Asisten')
                {
                echo"
                <td>
                <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_runht','".$res['notransaksi'].",". $res['kodevhc']."','','vhc_slave_pekerjaanPrint',event);\">";
                if($res['posting']<1  and $_SESSION['empl']['lokasitugas']==substr($res['notransaksi'],0,4))
                {
                        echo"<input type=checkbox id=checkDt_".$no." title=".$_SESSION['lang']['belumposting']." onclick=postData(".$no.")>";
                }
                else
                {
                        echo "&nbsp;".$_SESSION['lang']['posting'];
                }
                echo"</td>";}
                else
                {
                        if($res['posting']<1  and $_SESSION['empl']['lokasitugas']==substr($res['notransaksi'],0,4))
                        {
                        echo"<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_runht','".$res['notransaksi'].",". $res['kodevhc']."','','vhc_slave_pekerjaanPrint',event);\"> [".$_SESSION['lang']['belumposting']."] </td>";
                        }
                        else
                        {
                                echo"<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_runht','".$res['notransaksi'].",". $res['kodevhc']."','','vhc_slave_pekerjaanPrint',event);\">  [".$_SESSION['lang']['posting']."] </td>";
                        }
                }

        }
        echo"<tr class=rowheader><td colspan=11 align=center><div style=display:none id=tmblPosting><button class=mybutton onclick=postingData()>".$_SESSION['lang']['posting']."</button></div></td></tr>";
        echo" <tr><td colspan=11 align=center><div id=btnNextSmua style=display:block>
                                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
                                <br />
                                <button class=mybutton onclick=cariData(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                                <button class=mybutton onclick=cariData(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                                </div>
                                </td>
                                </tr>";
        echo" </tbody></table></div>";
                                }
                         }	
                        else
                        {
                                echo "Gagal,".(mysql_error($conn));
                        }	
                break;

        case'postData':
        //echo "warning:masuk";
                $scek="select kodeorg,updateby from ".$dbname.".vhc_runht where notransaksi='".$notransaksi_head."'";//echo "warning".$scek;
                $qcek=mysql_query($scek) or die(mysql_error());
                $rcek=mysql_fetch_assoc($qcek);
                $sCek="select kodejabatan from ".$dbname.".datakaryawan where karyawanid='".$_SESSION['standard']['userid']."'";
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_fetch_assoc($qCek);
				// cek alias kodejabatan di tabel sdm_5jabatan
                $sCek="select alias from ".$dbname.".sdm_5jabatan where kodejabatan='".$rCek['kodejabatan']."'";
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_fetch_assoc($qCek);
//                if($rCek['kodejabatan']!=98)
                if($rCek['alias'] !='KTU' && $rCek['alias'] !='Asisten')
                {
                        echo"warning: You are not authorized";
                        exit();
                }		
                $sudPost="update ".$dbname.".vhc_runht set posting='1',postingby='".$user_entry."' where notransaksi='".$notransaksi_head."'";
                if(mysql_query($sudPost))
                echo"";
                else
                echo "DB Error : ".mysql_error($conn);
        break;

        case'postingDa':
        foreach($_POST['notransaksi'] as $barisNtrns =>$dtrnotrans)
        {
                $tglData=tanggalsystem($_POST['tglData'][$barisNtrns]);
                $kdvhc=$_POST['kdVhc'][$barisNtrns];
                $tgl=substr($tglData,0,4);
                $tglm=substr($tglData,4,2);
                $tgld=substr($tglData,6,2);
                $period=$tgl."-".$tglm."-".$tgld;
                //cek kendaraan untuk //cek operator jika kendaraan bukan sewa
                $sStatKend="select kepemilikan from ".$dbname.".vhc_5master where kodevhc='".$kdvhc."'";
                $qStatKend=mysql_query($sStatKend) or die(mysql_error());
                $rStatKend=mysql_fetch_assoc($qStatKend);
                        if($rStatKend['kepemilikan']!=0)
                        {	
                                $sOpt="select idkaryawan from ".$dbname.".vhc_runhk 
                                      where notransaksi='".$dtrnotrans."'";
                                $qOpt=mysql_query($sOpt) or die(mysql_error());
                                        while($rCopt=mysql_fetch_assoc($qOpt))
                                        {
                                            $statKary=0;
                                        $sPost="select a.satuan from ".$dbname.".vhc_rundt a 
                                        left join ".$dbname.".vhc_runhk b on a.notransaksi=b.notransaksi 
                                        left join ".$dbname.".vhc_runht c on c.notransaksi=b.notransaksi 
                                        where b.idkaryawan='".$rCopt['idkaryawan']."' and c.tanggal like '".$period."%' group by a.satuan";
                                                // echo"warning".$sPost."__".$tglData;exit();
                                                $qPost=mysql_query($sPost) or die(mysql_error());
                                                $rPost=mysql_num_rows($qPost);
                                                if($rPost>1)
                                                {
                                                        $statKary+=1;
                                                }
                                        }
                                        if($statKary!=0)
                                        {
                                                echo"warning: Fail, there are ".$statKary." person, with different UOM";
                                                exit();
                                        }
                                        else
                                        {
                                                $sudPost="update ".$dbname.".vhc_runht set posting='1',postingby='".$_SESSION['standard']['userid']."' where notransaksi='".$dtrnotrans."'";
                                                //echo"warning".$sudPost;exit();
                                                if(mysql_query($sudPost))
                                                echo"";
                                                else
                                                echo "DB Error : ".mysql_error($conn);
                                        }

                                //}
                        }
                        else
                        {
                                $sudPost="update ".$dbname.".vhc_runht set posting='1',postingby='".$_SESSION['standard']['userid']."' where notransaksi='".$dtrnotrans."'";
                                //echo"warning".$sudPost;exit();
                                if(mysql_query($sudPost))
                                echo"";
                                else
                                echo "DB Error : ".mysql_error($conn);
                        }
         }
        break;
        case'postSat':

        $tgl=substr($_POST['tglData'],0,4);
        $tglm=substr($_POST['tanggal'],5,2);
        $period=$tgl."-".$tglm;
                $sOpt="select idkaryawan from ".$dbname.".vhc_runhk where notransaksi='".$_POST['notransaksi']."'";
                $qOpt=mysql_query($sOpt) or die(mysql_error());
                        while($rCopt=mysql_fetch_assoc($qOpt))
                        {
                                $sPost="select a.satuan from ".$dbname.".vhc_rundt a 
                                left join ".$dbname.".vhc_runhk b on a.notransaksi=b.notransaksi 
                                left join ".$dbname.".vhc_runht c on c.notransaksi=b.notransaksi 
                                where b.idkaryawan='".$rCopt['idkaryawan']."' and c.tanggal like '%".$period."%' group by a.satuan";
                                //echo"warning".$sPost;exit();
                                $qPost=mysql_query($sPost) or die(mysql_error());
                                $rPost=mysql_num_rows($qPost);
                                if($rPost>1)
                                {
                                        $statKary+=1;
                                }
                        }
                        if($statKary!=0)
                        {
                                echo"warning: Fail, there are ".$statKary." person, with different UOM";
                                exit();
                        }
                        else
                        {
                                $sudPost="update ".$dbname.".vhc_runht set posting='1',postingby='".$_SESSION['standard']['userid']."' where notransaksi='".$_POST['notransaksi']."'";
                                if(mysql_query($sudPost))
                                echo"";
                                else
                                echo "DB Error : ".mysql_error($conn);
                        }
        break;
        case'postingByTrip':
        //echo "warning:masuk";
        $sNotrans="select a.*,b.idkaryawan,b.posisi,c.alokasibiaya,c.jenispekerjaan,c.jumlahrit,c.beratmuatan from 
        ".$dbname.".vhc_runht a inner join ".$dbname.".vhc_runhk b on a.notransaksi=b.notransaksi 
        inner join ".$dbname.".vhc_rundt c on c.notransaksi=b.notransaksi
        where a.notransaksi='".$notransaksi_head."'"; //echo"warning:".$sNotrans;
        $qNotrans=mysql_query($sNotrans) or die(mysql_error());
        while($rNotrans=mysql_fetch_assoc($qNotrans))
        {
                $rNotrans['alokasibiaya']=substr($rNotrans['alokasibiaya'],0,4);
                $sPremi="select keycode from ".$dbname.".setup_mappremi where kodeorg='".$rNotrans['alokasibiaya']."'";//	echo"warning:".$sPremi;
                $qPremi=mysql_query($sPremi) or die(mysql_error());
                $rPremi=mysql_fetch_assoc($qPremi);
                if($rNotrans['premi']=='1')
                {	
                        if($rPremi['keycode']=='TRANS02')
                        {
                                $sKbn="select keycode,jumlahtrip,nomor,rate from ".$dbname.".kebun_5ratetransport where keycode='".$rPremi['keycode']."' 
                                and tipeangkutan='".$rNotrans['jenispekerjaan']."' and jobposition='".$rNotrans['posisi']."'";
                                $qKbn=mysql_query($sKbn) or die(mysql_error());
                                $rKbn=mysql_fetch_assoc($qKbn);
                                if($rNotrans['jumlahrit']>=$rKbn['jumlahtrip'])
                                {
                                        $set=" premi='".$rKbn['rate']."'";
                                        //echo "warning:masuk a".$set;
                                }
                                else if($rNotrans['jumlahrit']<$rKbn['jumlahtrip'])
                                {
                                        $set=" premi='0'";
                                }
                                $sIsi="update ".$dbname.".vhc_runhk set ".$set." where notransaksi='".$rNotrans['notransaksi']."' 
                                and idkaryawan='".$rNotrans['idkaryawan']."' and posisi='".$rNotrans['posisi']."'";
                                //echo "warning:".$sIsi."____";
                                if(mysql_query($sIsi))
                                {
                                        $sHead="update ".$dbname.".vhc_runht set posting='1',postingby='".$user_entry."' where notransaksi='".$notransaksi_head."'";	
                                        if(mysql_query($sHead))
                                        echo"";
                                        else
                                        echo "DB Error : ".mysql_error($conn);
                                }
                                else
                                {
                                        echo "DB Error : ".mysql_error($conn);		
                                }
                        }
                        elseif($rPremi['keycode']=='TRANS01')
                        {

                                $sKbn="select keycode,jaraksampai,jarakdari,nomor,rate from ".$dbname.".kebun_5ratetransport where keycode='".$rPremi['keycode']."' 
                                and tipeangkutan='".$rNotrans['jenispekerjaan']."' and jobposition='".$rNotrans['posisi']."'";
                                $qKbn=mysql_query($sKbn) or die(mysql_error());
                                $rKbn=mysql_fetch_assoc($qKbn);
                                if($rNotrans['jumlah']>=$rKbn['jaraksampai'])
                                {
                                        $setBasis=" premi='".$rKbn['rate']."'";
                                }
                                else if($rNotrans['jumlah']<$rKbn['jarakdari'])
                                {
                                        $setBasis=" premi='0'";
                                }
                                else if(($rNotrans['jumlah']>$rKbn['jarakdari'])&&($rNotrans['jumlah']<$rKbn['jaraksampai']))
                                {
                                        $setBasis=" premi='".$rKbn['rate']."'";
                                }

                                $sIsi="update ".$dbname.".vhc_runhk set ".$setBasis." where notransaksi='".$rNotrans['notransaksi']."' 
                                and idkaryawan='".$rNotrans['idkaryawan']."' and posisi='".$rNotrans['posisi']."'";//;echo "warning:".$sIsi."____2";exit();

                                if(mysql_query($sIsi))
                                {
                                        $data=array();

                                        $sHead="update ".$dbname.".vhc_runht set posting='1',postingby='".$user_entry."' where notransaksi='".$notransaksi_head."'";	
                                        if(mysql_query($sHead))
                                        echo"";
                                        else
                                        echo "DB Error : ".mysql_error($conn);
                                }
                                else
                                {
                                        echo "DB Error : ".mysql_error($conn);		
                                }
                        }
                }
        }
        break;
        default:
        break;	
}


?>