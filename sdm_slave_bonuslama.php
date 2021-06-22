<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/zPosting.php');

$param = $_POST;
$proses = $_GET['proses'];
function findFirstAndLastDay($anyDate)
{
    //$anyDate            =    '2009-08-25';    // date format should be yyyy-mm-dd
    list($yr,$mn,$dt)    =    split('-',$anyDate);    // separate year, month and date
    $timeStamp            =    mktime(0,0,0,$mn,1,$yr);    //Create time stamp of the first day from the give date.
    $firstDay            =     date('D',$timeStamp);    //get first day of the given month
    list($y,$m,$t)        =     split('-',date('Y-m-t',$timeStamp)); //Find the last date of the month and separating it
    $lastDayTimeStamp    =    mktime(0,0,0,$m,$t,$y);//create time stamp of the last date of the give month
    $lastDay            =    date('Y-m-d',$lastDayTimeStamp);// Find last day of the month
    $arrDay                =    array("$firstDay","$lastDay"); // return the result in an array format.
   
    return $arrDay;
}
//Usage
//$dayArray=array();
//$dayArray=findFirstAndLastDay('2009-02-25');
//print $dayArray[0];
//print $dayArray[1];
$tglTerakhir=array();
$tgl=explode("-",$param['tanggal']);
$tglAbis=$tgl[2]."-".$tgl[1]."-".$tgl[0];
$bagi=365;
if(date("Y-m-d",strtotime($tglAbis))%4==0)
{
	$bagi=366;	
}
$masakrjmin=1;
$sstr="select masakrjmin from ".$dbname.".sdm_ho_thr_setup where ifnull(masakrjmin,0)>0 LIMIT 1";
$res1=mysql_query($sstr);
while($bar1=mysql_fetch_object($res1))
{
	$masakrjmin=(($bar1->masakrjmin)/$bagi)*12;
}

$table="";
if($param['jenis']=='26')
{
	$table="".$dbname.".sdm_ho_bonus_setup";
}
if($param['jenis']=='28')
{
	$table="".$dbname.".sdm_ho_thr_setup";
}

switch($proses) {
    case 'list' :
		//kapan dipakainya parameter ini???
        $dis="";
        if($param['jenis']=='26')
        {
            $dis="'disabled'=>'disabled'";
        }
        # Get Period Range
        $str="select * from ".$dbname.".setup_periodeakuntansi where periode='".$param['periodegaji']."' and
                kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=1";
               // exit("Error".$str) ;
                $res=mysql_query($str);
                if(mysql_num_rows($res)>0)
                $aktif=true;
                else
                $aktif=false;
                if($aktif==true)
                {
                exit("Error: Accounting period has been closed");
                }
        $qPeriod = selectQuery($dbname,'sdm_5periodegaji','tanggalmulai,tanggalsampai,jenisgaji',
            "periode='".$param['periodegaji']."' and jenisgaji='".substr($param['jnsGaji'],0,1)."' and kodeorg='".
            $_SESSION['empl']['lokasitugas']."'");
        $resPeriod = fetchData($qPeriod);
        $tglH1 = '';$tglH2 = '';
        $tglB1 = '';$tglB2 = '';
        foreach($resPeriod as $row) {
            if($param['jnsGaji']=='Harian') {
                isset($row['tanggalmulai']) ? $tglH1 = $row['tanggalmulai'] : $tglH1 = '';
                isset($row['tanggalsampai']) ? $tglH2 = $row['tanggalsampai'] : $tglH2 = '';
            } else {
                isset($row['tanggalmulai']) ? $tglB1 = $row['tanggalmulai'] : $tglB1 = '';
                isset($row['tanggalsampai']) ? $tglB2 = $row['tanggalsampai'] : $tglB2 = '';
            }
        }
		if($tglB2!='')
		$dtTgl=$tglB2;
		else
		$dtTgl=$tglH2;
        $tglTerakhir=findFirstAndLastDay($dtTgl);
		//exit("Error".$tglTerakhir[1]);
        #======== Get Data Bulanan ============
        if($param['jnsGaji']=='Bulanan')
        {
			// $where1 = "tipekaryawan in (1,2,3,6) and alokasi=0 and lokasitugas='".
         //$where1 = "tipekaryawan in (0,1,2,3,6) and alokasi=0 and lokasitugas='".
       
       $cols1 = "karyawanid,namakaryawan,tipekaryawan,kodejabatan,subbagian,bagian,tanggalmasuk,".
            "COALESCE(ROUND(DATEDIFF('".$tglAbis."',tanggalmasuk)/".$bagi.",3),0) as masakerja";
        $where1 = "tipekaryawan in (0,1,2,3,6,7) 
			and karyawanid in (select x.karyawanid from ".$dbname.".sdm_ho_employee x
					      where x.operator='".$_SESSION['standard']['username']."')
				and sistemgaji='".$param['jnsGaji']."' and 
                (tanggalkeluar>'".$row['tanggalmulai']."' or tanggalkeluar='0000-00-00')";
        
        $query1 = selectQuery($dbname,'datakaryawan',$cols1,$where1);
        $resBln = fetchData($query1);
        }
        else if($param['jnsGaji']=='Harian')
        {
        #======== Get Data Harian ============
        //$where2 = "tipekaryawan in (1,2,3,6) and alokasi=0 and lokasitugas='".
        $cols2 = "karyawanid,namakaryawan,tipekaryawan,kodejabatan,subbagian,bagian,tanggalmasuk,".
            "COALESCE(ROUND(DATEDIFF('".$tglAbis."',tanggalmasuk)/".$bagi.",3),0) as masakerja";
        $where2 = "tipekaryawan in (1,2,3,6)  
			and karyawanid in (select x.karyawanid from ".$dbname.".sdm_ho_employee x
					      where x.operator='".$_SESSION['standard']['username']."')
				and sistemgaji='".$param['jnsGaji']."' and
            (tanggalkeluar>'".$row['tanggalmulai']."' or tanggalkeluar='0000-00-00')";
        $query2 = selectQuery($dbname,'datakaryawan',$cols2,$where2);
        $resHrn = fetchData($query2);
        }
        //exit("Error".$query1."__".$query2);
        #======== Get Gaji Pokok ============
        $whereGj = "karyawanid in ("; //kapan kepakai parameter ini ?
        $first=true;
        $tmpGj=array();
		if($param['jnsGaji']=='Bulanan')
        {
            foreach($resBln as $key=>$row) {
                $sGaji="select karyawanid,idkomponen,pengali,jumlah from ".$dbname.".sdm_gaji where karyawanid='".$row['karyawanid']."'
                    and idkomponen in (".$param['jenis'].")  
			and karyawanid in (select x.karyawanid from ".$dbname.".sdm_ho_employee x
					      where x.operator='".$_SESSION['standard']['username']."')
				and periodegaji='".$param['periodegaji']."'";
                $qGaji=mysql_query($sGaji) or die(mysql_error());
                $rGaji=mysql_fetch_assoc($qGaji);
                if($rGaji['jumlah']!=''&&$rGaji['jumlah']!=0)
                {
                    
                     $tmpGj[]=$rGaji;
                }
                else{
					
                    $sGaji2="select karyawanid,idkomponen,1 as pengali,sum(jumlah) as jumlah from ".$dbname.".sdm_5gajipokok where karyawanid='".$row['karyawanid']."'";                   
					if($param['jenis']=='28')
					{
						$sGaji2.=" and ((idkomponen in(select distinct component from ".$table."))
									or (idkomponen in (select id from ".$dbname.".sdm_ho_component where isGP=1))
									)";
					}
					if($param['jenis']=='26')
					{
						$sGaji2.=" and idkomponen in(select distinct component from ".$table.")";
					}
					$sGaji2.=" and tahun=".$param['tahun']." GROUP BY karyawanid";
					
					$qGaji2=mysql_query($sGaji2) or die(mysql_error());
                    $rGaji2=mysql_fetch_assoc($qGaji2);
                    $tmpGj[]=$rGaji2;
                }
            }
        }
        else if($param['jnsGaji']=='Harian')
        {
            foreach($resHrn as $row) {
                $sGaji="select karyawanid,idkomponen,pengali,jumlah from ".$dbname.".sdm_gaji where karyawanid='".$row['karyawanid']."'
                    and idkomponen in (".$param['jenis'].") ) 
			and karyawanid in (select x.karyawanid from ".$dbname.".sdm_ho_employee x
					      where x.operator='".$_SESSION['standard']['username']."')
				and periodegaji='".$param['periodegaji']."'";
                $qGaji=mysql_query($sGaji) or die(mysql_error());
                $rGaji=mysql_fetch_assoc($qGaji);
                if($rGaji['jumlah']!=''||$rGaji['jumlah']!=0)
                { 
                    $tmpGj[]=$rGaji;
                }
                else{
                    
                    $sGaji2="select karyawanid,idkomponen,1 as pengali,sum(jumlah) as jumlah from ".$dbname.".sdm_5gajipokok where karyawanid='".$row['karyawanid']."'";                   
					if($param['jenis']=='28')
					{
						$sGaji2.=" and ((idkomponen in(select distinct component from ".$table."))
									or (idkomponen in (select id from ".$dbname.".sdm_ho_component where isGP=1))
									)";
					}
					if($param['jenis']=='26')
					{
						$sGaji2.=" and idkomponen in(select distinct component from ".$table.")";
					}
					$sGaji2.=" and tahun=".$param['tahun']." GROUP BY karyawanid";
                    $qGaji2=mysql_query($sGaji2) or die(mysql_error());
                    $rGaji2=mysql_fetch_assoc($qGaji2);
                    $tmpGj[]=$rGaji2;
                }
            }
        }
		
        $resGj = array();
		foreach($tmpGj as $row) {             
           // $resGj[$row['karyawanid']][]=$row['karyawanid'];
            $resGj[$row['karyawanid']][1]['jumlah'] = $row['jumlah'];
            $resGj[$row['karyawanid']][$row['idkomponen']]['pengali'] = $row['pengali'];
      
        }
        
        # Options
        $optKary = array();
        if($param['jnsGaji']=='Bulanan')
        {
            foreach($resBln as $row) {
				$optKary[$row['karyawanid']]['nama'] = $row['namakaryawan'];
                $optKary[$row['karyawanid']]['tipe'] = $row['tipekaryawan'];              
                if($row['subbagian']==''||is_null($row['subbagian'])||$row['subbagian']=='0')
                {
                    $optKary[$row['karyawanid']]['kodeorganisasi']=$_SESSION['empl']['lokasitugas'];
                }
                else
                { 
					$optKary[$row['karyawanid']]['kodeorganisasi']=$row['subbagian'];
                }
                $optKary[$row['karyawanid']]['kodejabatan'] = $row['kodejabatan'];
                $optKary[$row['karyawanid']]['bagian'] = $row['bagian'];
                $optKary[$row['karyawanid']]['tmk'] = $row['tanggalmasuk'];
                $optKary[$row['karyawanid']]['masakerja'] = $row['masakerja'];
            }
        }
        else if($param['jnsGaji']=='Harian')
        {
            foreach($resHrn as $row) {
                 $optKary[$row['karyawanid']]['nama'] = $row['namakaryawan'];
                $optKary[$row['karyawanid']]['tipe'] = $row['tipekaryawan'];
              
                if($row['subbagian']==''||is_null($row['subbagian'])||$row['subbagian']=='0')
                {
                    $optKary[$row['karyawanid']]['kodeorganisasi']=$_SESSION['empl']['lokasitugas'];
                }
                else
                { 
                $optKary[$row['karyawanid']]['kodeorganisasi']=$row['subbagian'];
                }
                $optKary[$row['karyawanid']]['kodejabatan'] = $row['kodejabatan'];
                $optKary[$row['karyawanid']]['bagian'] = $row['bagian'];
                $optKary[$row['karyawanid']]['tmk'] = $row['tanggalmasuk'];
                $optKary[$row['karyawanid']]['masakerja'] = $row['masakerja'];
            }
        }
        $optTipe = makeOption($dbname,'sdm_5tipekaryawan','id,tipe');
        $optNmorg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
        $optBagian = makeOption($dbname,'sdm_5departemen','kode,nama');
        $optJabatan = makeOption($dbname,'sdm_5jabatan','kodejabatan,namajabatan');
        # Rearrange Data
        $data = array();
        $i=0;
        $dataExist = array();
		
        foreach($resGj as $kary=>$row) {
		  
            $data[$i] = array(
                'id'=>$kary,
                'tipe'=>$optKary[$kary]['tipe'],
                'lokasitugas'=>$optKary[$kary]['kodeorganisasi'],
                'bagian'=>$optKary[$kary]['bagian'],
                'kodejabatan'=>$optKary[$kary]['kodejabatan'],
                'tmk'=>$optKary[$kary]['tmk'],
                'masakerja'=>$optKary[$kary]['masakerja'],
                'gajipokok'=>$row[1]['jumlah']
            );
			
            // exit("Error".$optKary[$kary]['masakerja']."__".$kary);
            $msKerja=$optKary[$kary]['masakerja']*12;
            if(isset($row[$param['jenis']])) {
                $dataExist[$i] = 1;
                if($row[$param['jenis']]['pengali']!=0)
                {
                 $data[$i]['gajipokok']=$row[1]['jumlah']/$row[$param['jenis']]['pengali'];
                }
                $data[$i]['pengali'] = $row[$param['jenis']]['pengali'];
            } else {
                        $dataExist[$i] = 0;
                        //exit("Error".$param['jenis']);
							
						/* SAS 20160927 sementara ditutup sampai dapat rule pasti
                          if($msKerja<3)
                          {
                              $row['1']['pengali']=0;
                          }
                          else if($msKerja<12&&$msKerja>=3)
                            {
                                $data[$i]['gajipokok']=$row[1]['jumlah'];
                                #pemberlakuan peraturan perusahaan
                                #kurang dari 3 bulan tidak dapat
                                #kelebihan 1-15 hari tidak tihitung
                                #kelebihan diatas 15 hari dihitung 1 bulan
                                $z=floor($msKerja);
                                $z=$msKerja-$z;                  
                                 if($z>0.5)//15 hari
                                    $row['1']['pengali']= ceil($msKerja)/12;
                                 else
                                     $row['1']['pengali']=floor($msKerja)/12;
                            }
                            else if($msKerja>=12)
                            {
                                $row['1']['pengali']=1;
                            }
						*/
							if($msKerja<$masakrjmin)
							  {
								  $row['1']['pengali']=0;
							  }
							else 
								{
									$row['1']['pengali']=1;
								}
				
				
				
                $data[$i]['pengali'] = $row['1']['pengali'];
            }

            $data[$i]['jumlah'] = $data[$i]['gajipokok']*$data[$i]['pengali'];
            $i++;
           
        }
        
        # Data Show
        $dataShow = $data;
        foreach($dataShow as $key=>$row) {
            $dataShow[$key]['id'] = $optKary[$row['id']]['nama'];
            $dataShow[$key]['tipe'] = $optTipe[$row['tipe']];
            $dataShow[$key]['lokasitugas'] = $optNmorg[$row['lokasitugas']];
            $dataShow[$key]['bagian'] = $optBagian[$row['bagian']];
            $dataShow[$key]['kodejabatan'] = $optJabatan[$row['kodejabatan']];
            $dataShow[$key]['tmk'] = tanggalnormal_hrd($row['tmk']);
        }
       
        #======== Make Table ==========
        $tmpColTab = 'namakaryawan,tipekaryawan,lokasitugas,bagian,kodejabatan,tmk,masakerja,thr,pengali,jumlah,action';
        $colTab = explode(',',$tmpColTab);
        $table = "";
        $table .= "<div style=overflow:auto;height:800px;><table class='sortable'>";
        # Header
        $table .= "<thead><tr class='rowheader'>";
        foreach($colTab as $row) {
            $table .= "<td align='center'>".$_SESSION['lang'][$row]."</td>";
        }
        $table .= "</tr></thead>";
        # Content
		$listdt=0;
		$table .= "<tbody>";
        foreach($dataShow as $key=>$row) {
        
            if($row['id']!='')
            {
				$listdt+=1;
				if($dataExist[$key]==1) {
					$table .= "<tr id='tr_".$listdt."' class='rowcontent' style='background:green'>";
				} else {
					$table .= "<tr id='tr_".$listdt."' class='rowcontent'>";
				}
				foreach($row as $head=>$cont) {
			
					$table .= "<td id='".$head."_".$listdt."' value='".$data[$key][$head]."'>";
					if($head=='pengali') {
						if($param['jenis']!='26')
						{
						$table .= makeElement($head.'_'.$listdt.'_text','textnum',$cont,
							//array('style'=>'width:70px;text-align:center','onkeyup'=>"updJumlah(".$listdt.")",'disabled'=>'disabled')); SAS 20160927 dibuka sementara
							array('style'=>'width:70px;text-align:center','onkeyup'=>"updJumlah(".$listdt.")"));
						}
						else
						{
						   $table .= makeElement($head.'_'.$listdt.'_text','textnum',$cont,
							array('style'=>'width:70px;text-align:center','onkeyup'=>"updJumlah(".$listdt.")")); 
						}
					} elseif($head=='jumlah') {
						$table .= makeElement($head.'_'.$listdt.'_text','textnum',$cont,
							array('style'=>'width:70px'));
					} else {
						$table .= $cont;
					   // exit("Error".$cont);
					}
					$table .= "</td>";
			  
				}
				# Action
				$table .= "<td>";
				$table .= makeElement('save_'.$listdt,'btn',$_SESSION['lang']['save'],
					array('onclick'=>"saveItDt(".$listdt.")"));
				$table .= "</td>";
				$table .= "</tr>";
			}
        }
		if($listdt>0){
			$table.="<tr><td colspan=12 align=center><button class=mybutton onclick=saveAll(".$listdt.",1)>".$_SESSION['lang']['save']." ".$_SESSION['lang']['all']."</button></td></tr>";
        }
		$table .= "</tbody>";
        $table .= "</table></div>";
        
        echo $table;
        break;
    case 'post':
        $data = array(
            'kodeorg'=>$_SESSION['empl']['lokasitugas'],
            'periodegaji'=>$param['periodegaji'],
            'karyawanid'=>$param['id'],
            'idkomponen'=>$param['jenis'],
            'jumlah'=>$param['jumlah'],
            'pengali'=>$param['pengali']
        );
        $dataUpd = array(
            'jumlah'=>$param['jumlah'],
            'pengali'=>$param['pengali']
        );
        $sCek="select distinct tutupbuku from ".$dbname.".setup_periodeakuntansi where kodeorg='".$data['kodeorg']."' and periode='".$data['periodegaji']."'";
        $qCek=mysql_query(($sCek)) or die(mysql_error());
        $rCek=mysql_fetch_assoc($qCek);
        if($rCek['tutupbuku']!=0)
        {
            echo"warning: Data can not be saved, accunting period has closed";
            exit();
        }
        # Insert
        $query1 = insertQuery($dbname,'sdm_gaji',$data);
        if(!mysql_query($query1)) {
            $tmpErr = mysql_error();
            $where = "kodeorg='".$data['kodeorg'].
                "' and periodegaji='".$data['periodegaji'].
                "' and karyawanid='".$data['karyawanid'].
                "' and idkomponen=".$data['idkomponen'];
            $query2 = updateQuery($dbname,'sdm_gaji',$dataUpd,$where);
            if(!mysql_query($query2)) {
                echo "Insert DB Error : ".$tmpErr."\n";
                echo "Update DB Error : ".mysql_error()."\n";
            }
        }
        
        break;
        case 'excel' :
             $str="select * from ".$dbname.".setup_periodeakuntansi where periode='".$param['periodegaji']."' and
                kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=1";
               // exit("Error".$str) ;
                $res=mysql_query($str);
                if(mysql_num_rows($res)>0)
                $aktif=true;
                else
                $aktif=false;
                if($aktif==true)
                {
                exit("Error: Accounting period has been closed");
                }
            $param = $_GET;
            $tgl=explode("-",$param['tanggal']);
           $tglAbis=$tgl[2]."-".$tgl[1]."-".$tgl[0];

            $periode=$param['periodegaji'];
        # Get Period Range
        $qPeriod = selectQuery($dbname,'sdm_5periodegaji','tanggalmulai,tanggalsampai,jenisgaji',
            "periode='".$param['periodegaji']."' and jenisgaji='".substr($param['jnsGaji'],0,1)."' and kodeorg='".
            $_SESSION['empl']['lokasitugas']."'");
        $resPeriod = fetchData($qPeriod);
        $tglH1 = '';$tglH2 = '';
        $tglB1 = '';$tglB2 = '';
        foreach($resPeriod as $row) {
            if($param['jnsGaji']=='Harian') {
                isset($row['tanggalmulai']) ? $tglH1 = $row['tanggalmulai'] : $tglH1 = '';
                isset($row['tanggalsampai']) ? $tglH2 = $row['tanggalsampai'] : $tglH2 = '';
            } else {
                isset($row['tanggalmulai']) ? $tglB1 = $row['tanggalmulai'] : $tglB1 = '';
                isset($row['tanggalsampai']) ? $tglB2 = $row['tanggalsampai'] : $tglB2 = '';
            }
        }
        if($tglB2!='')
		$dtTgl=$tglB2;
		else
		$dtTgl=$tglH2;
        $tglTerakhir=findFirstAndLastDay($dtTgl);
        #======== Get Data Bulanan ============
        if($param['jnsGaji']=='Bulanan')
        {
        //$where1 = "tipekaryawan in (1,2,3,6) and alokasi=0 and lokasitugas='".
        $cols1 = "karyawanid,namakaryawan,tipekaryawan,kodejabatan,subbagian,bagian,tanggalmasuk,".
            "COALESCE(ROUND(DATEDIFF('".$tglAbis."',tanggalmasuk)/".$bagi.",3),0) as masakerja";
        $where1 = "tipekaryawan in (0,1,2,3,6,7)  
			and karyawanid in (select x.karyawanid from ".$dbname.".sdm_ho_employee x
					      where x.operator='".$_SESSION['standard']['username']."')
				and sistemgaji='".$param['jnsGaji']."' and 
                (tanggalkeluar>'".$row['tanggalmulai']."' or tanggalkeluar='0000-00-00')";
        $query1 = selectQuery($dbname,'datakaryawan',$cols1,$where1);
        $resBln = fetchData($query1);

        }
        else if($param['jnsGaji']=='Harian')
        {
        #======== Get Data Harian ============
        //$where2 = "tipekaryawan in (1,2,3,6) and alokasi=0 and lokasitugas='".
        $cols2 = "karyawanid,namakaryawan,tipekaryawan,kodejabatan,subbagian,bagian,tanggalmasuk,".
            "COALESCE(ROUND(DATEDIFF('".$tglAbis."',tanggalmasuk)/".$bagi.",3),0) as masakerja";
        $where2 = "tipekaryawan in (1,2,3,6)  
			and karyawanid in (select x.karyawanid from ".$dbname.".sdm_ho_employee x
					      where x.operator='".$_SESSION['standard']['username']."')
				and sistemgaji='".$param['jnsGaji']."' and
            (tanggalkeluar>'".$row['tanggalmulai']."' or tanggalkeluar='0000-00-00')";
        $query2 = selectQuery($dbname,'datakaryawan',$cols2,$where2);
        $resHrn = fetchData($query2);
        }
       // exit("Error".$query1."__".$query2);
        #======== Get Gaji Pokok ============
        $whereGj = "karyawanid in (";//kapan kepakai parameter ini ?
        $first=true;
        $tmpGj=array();
        if($param['jnsGaji']=='Bulanan')
        {
            foreach($resBln as $key=>$row) {

                $sGaji="select karyawanid,idkomponen,pengali,jumlah from ".$dbname.".sdm_gaji where karyawanid='".$row['karyawanid']."'
                    and idkomponen in (".$param['jenis'].") and kodeorg='".$_SESSION['empl']['lokasitugas']."' and periodegaji='".$param['periodegaji']."'";
                $qGaji=mysql_query($sGaji) or die(mysql_error());
                $rGaji=mysql_fetch_assoc($qGaji);
                if($rGaji['jumlah']!=''&&$rGaji['jumlah']!=0)
                {
                    
                     $tmpGj[]=$rGaji;
                }
                else{
                   
                    $sGaji2="select karyawanid,idkomponen,1 as pengali,sum(jumlah) as jumlah from ".$dbname.".sdm_5gajipokok where karyawanid='".$row['karyawanid']."'";                   
					if($param['jenis']=='28')
					{
						$sGaji2.=" and ((idkomponen in(select distinct component from ".$table."))
									or (idkomponen in (select id from ".$dbname.".sdm_ho_component where isGP=1))
									)";
					}
					if($param['jenis']=='26')
					{
						$sGaji2.=" and idkomponen in(select distinct component from ".$table.")";
					}
					$sGaji2.=" and tahun=".$param['tahun']." GROUP BY karyawanid";
                    $qGaji2=mysql_query($sGaji2) or die(mysql_error());
                    $rGaji2=mysql_fetch_assoc($qGaji2);
                    $tmpGj[]=$rGaji2;
                }
            }
        }
        else if($param['jnsGaji']=='Harian')
        {
            foreach($resHrn as $row) {
                $sGaji="select karyawanid,idkomponen,pengali,jumlah from ".$dbname.".sdm_gaji where karyawanid='".$row['karyawanid']."'
                    and idkomponen in (".$param['jenis'].") and kodeorg='".$_SESSION['empl']['lokasitugas']."' and periodegaji='".$param['periodegaji']."'";
                $qGaji=mysql_query($sGaji) or die(mysql_error());
                $rGaji=mysql_fetch_assoc($qGaji);
                if($rGaji['jumlah']!='')
                {

                    $tmpGj[]=$rGaji;
                }
                else{
                    
                    $sGaji2="select karyawanid,idkomponen,1 as pengali,sum(jumlah) as jumlah from ".$dbname.".sdm_5gajipokok where karyawanid='".$row['karyawanid']."'";                   
					if($param['jenis']=='28')
					{
						$sGaji2.=" and ((idkomponen in(select distinct component from ".$table."))
									or (idkomponen in (select id from ".$dbname.".sdm_ho_component where isGP=1))
									)";
					}
					if($param['jenis']=='26')
					{
						$sGaji2.=" and idkomponen in(select distinct component from ".$table.")";
					}
					$sGaji2.=" and tahun=".$param['tahun']." GROUP BY karyawanid";
                    $qGaji2=mysql_query($sGaji2) or die(mysql_error());
                    $rGaji2=mysql_fetch_assoc($qGaji2);
                    $tmpGj[]=$rGaji2;
                }
            }
        }

        $resGj = array();
        
        foreach($tmpGj as $row) {
           // $resGj[$row['karyawanid']][]=$row['karyawanid'];
            $resGj[$row['karyawanid']][1]['jumlah'] = $row['jumlah'];
            $resGj[$row['karyawanid']][$row['idkomponen']]['pengali'] = $row['pengali'];
           
        }
        
        # Options
        $optKary = array();
        if($param['jnsGaji']=='Bulanan')
        {
            foreach($resBln as $row) {

                $optKary[$row['karyawanid']]['nama'] = $row['namakaryawan'];
                $optKary[$row['karyawanid']]['tipe'] = $row['tipekaryawan'];
              
                if($row['subbagian']==''||is_null($row['subbagian'])||$row['subbagian']=='0')
                {
                    $optKary[$row['karyawanid']]['kodeorganisasi']=$_SESSION['empl']['lokasitugas'];
                }
                else
                { 
                $optKary[$row['karyawanid']]['kodeorganisasi']=$row['subbagian'];
                }
                $optKary[$row['karyawanid']]['kodejabatan'] = $row['kodejabatan'];
                $optKary[$row['karyawanid']]['bagian'] = $row['bagian'];
                $optKary[$row['karyawanid']]['tmk'] = $row['tanggalmasuk'];
                $optKary[$row['karyawanid']]['masakerja'] = $row['masakerja'];
            }
        }
         else if($param['jnsGaji']=='Harian')
        {
            foreach($resHrn as $row) {
                $optKary[$row['karyawanid']]['nama'] = $row['namakaryawan'];
                $optKary[$row['karyawanid']]['tipe'] = $row['tipekaryawan'];
                $optKary[$row['karyawanid']]['masakerja'] = $row['masakerja'];
                 if($row['subbagian']==''||is_null($row['subbagian'])||$row['subbagian']=='0')
                {
                    $optKary[$row['karyawanid']]['kodeorganisasi']=$_SESSION['empl']['lokasitugas'];
                }
                else
                { 
                $optKary[$row['karyawanid']]['kodeorganisasi']=$row['subbagian'];
                }
               $optKary[$row['karyawanid']]['kodejabatan'] = $row['kodejabatan'];
               $optKary[$row['karyawanid']]['bagian'] = $row['bagian'];
                $optKary[$row['karyawanid']]['tmk'] = $row['tanggalmasuk'];
                $optKary[$row['karyawanid']]['masakerja'] = $row['masakerja'];
            }
        }
        $optTipe = makeOption($dbname,'sdm_5tipekaryawan','id,tipe');
        $optNmorg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
        $optBagian = makeOption($dbname,'sdm_5departemen','kode,nama');
        $optJabatan = makeOption($dbname,'sdm_5jabatan','kodejabatan,namajabatan');
        # Rearrange Data
        $data = array();
        $i=0;
        $dataExist = array();
       
        foreach($resGj as $kary=>$row) {
        //exit("Error".$kary);
           // echo"warning".$kary;exit();
            $data[$i] = array(
                'id'=>$kary,
                'tipe'=>$optKary[$kary]['tipe'],
                'lokasitugas'=>$optKary[$kary]['kodeorganisasi'],
                'kodejabatan'=>$optKary[$kary]['kodejabatan'],
                'bagian'=>$optKary[$kary]['bagian'],
                'tmk'=>$optKary[$kary]['tmk'],
                'masakerja'=>$optKary[$kary]['masakerja'],
                'gajipokok'=>$row[1]['jumlah']
            );
            // exit("Error".$optKary[$kary]['masakerja']."__".$kary);
            $msKerja=$optKary[$kary]['masakerja']*12;
            if(isset($row[$param['jenis']])) {
                $dataExist[$i] = 1;
                
                if($row[$param['jenis']]['pengali']!=0)
                {
                 $data[$i]['gajipokok']=$row[1]['jumlah']/$row[$param['jenis']]['pengali'];
                }
                  
                $data[$i]['pengali'] = $row[$param['jenis']]['pengali'];
            } else {
                $dataExist[$i] = 0;
				/* SAS 20160927 Sementara ditutup sampai dapat rule nya
                  if($msKerja<3)
                        {
                            $row['1']['pengali']=0;
                        }
                 else  if($msKerja<12&&$msKerja>=3)
                    {
                        $data[$i]['gajipokok']=$row[1]['jumlah'];
                                #pemberlakuan peraturan perusahaan
                                #kurang dari 3 bulan tidak dapat
                                #kelebihan 1-15 hari tidak tihitung
                                #kelebihan diatas 15 hari dihitung 1 bulan
                                $z=floor($msKerja);
                                $z=$msKerja-$z;                  
                                 if($z>0.5)//15 hari
                                    $row['1']['pengali']= ceil($msKerja)/12;
                                 else
                                     $row['1']['pengali']=floor($msKerja)/12;
                            }
                            else if($msKerja>=12)
                            {
                                $row['1']['pengali']=1;
                            }
				*/
				
				
				  if($msKerja<$masakrjmin)
                        {
                            $row['1']['pengali']=0;
                        }
					else  
						{
							$row['1']['pengali']=1;
						}
                $data[$i]['pengali'] = $row['1']['pengali'];
            }
            $data[$i]['jumlah'] = $data[$i]['gajipokok']*$data[$i]['pengali'];
            $i++;
           
        }
        
        # Data Show
        $dataShow = $data;
        foreach($dataShow as $key=>$row) {
//        echo"<pre>";
//        print_r($dataShow);
//        echo"</pre>";
            $dataShow[$key]['id'] = $optKary[$row['id']]['nama'];
            $dataShow[$key]['tipe'] = $optTipe[$row['tipe']];
            $dataShow[$key]['lokasitugas'] = $optNmorg[$row['lokasitugas']];
            $dataShow[$key]['kodejabatan'] = $optJabatan[$row['kodejabatan']];
            $dataShow[$key]['bagian'] = $optBagian[$row['bagian']];
            $dataShow[$key]['tmk'] = $row['tmk'];
        }
       
        #======== Make Table ==========
        $tmpColTab = 'namakaryawan,tipekaryawan,lokasitugas,kodejabatan,bagian,tmk,masakerja,gajipokok,pengali,jumlah';
        $colTab = explode(',',$tmpColTab);
        $table = "<table cellspacing=1 cellpading=0 border=0><tr><td colspan=7 align=center>
            ".$_SESSION['lang']['rkpThr'].",".$_SESSSION['lang']['periode']." :".$periode."<br /> ".$_SESSION['lang']['unit']." :".$_SESSION['empl']['lokasitugas']."</td></tr></table>";
        $table .= "<table class='sortable' border=1>";
        # Header
        $table .= "<thead><tr class='rowheader'>";
        foreach($colTab as $row) {
            $table .= "<td  bgcolor=#DEDEDE align=center>".$_SESSION['lang'][$row]."</td>";
        }
        $table .= "</tr></thead>";
        # Content
       
        $table .= "<tbody>";
        foreach($dataShow as $key=>$row) {
            if($row['id']!='')
            {
                $table .= "<tr id='tr_".$key."' class='rowcontent'>";

                foreach($row as $head=>$cont) {
                    $table .= "<td id='".$head."_".$key."' value='".$data[$key][$head]."'>";
              
                    $table .= $cont;
                    $table .= "</td>";
                }
            }
        }
        $table .= "</tbody>";
        $table .= "</table>";
        $table.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $nop_="RekapThr_bonus_".$periode."__".$_SESSION['empl']['lokasitugas'];
                    if(strlen($table)>0)
                    {
                    if ($handle = opendir('tempExcel')) {
                    while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != "..") {
                    @unlink('tempExcel/'.$file);
                    }
                    }	
                    closedir($handle);
                    }
                    $handle=fopen("tempExcel/".$nop_.".xls",'w');
                    if(!fwrite($handle,$table))
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
        //echo $table;
        break;
    default:
}
?>