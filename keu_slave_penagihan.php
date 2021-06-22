<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
include_once 'lib/devLibrary.php';
$param = $_POST;

$optnmcust = makeOption($dbname, 'pmn_4customer', 'kodecustomer,namacustomer');
switch ($param['proses']) {
    case 'insert':
        if ('' == $param['noinvoice']) {
            exit('error:Noinvoice tidak boleh kosong');
        }

        if ('' == $param['tanggal']) {
            exit('error:Tanggal tidak boleh kosong');
        }

        if ('' == $param['dpp']) {
            exit('error:Nilai invoice tidak boleh kosong');
        }

        if ('' == $param['nilaippn']) {
            $param['nilaippn'] = 0;
        }

        if ('' == $param['uangmuka']) {
            $param['uangmuka'] = 0;
        }

        if ('' == $param['jatuhtempo']) {
            $param['jatuhtempo'] = '0000-00-00 00:00:00';
        }

        if ('' == $param['nilaipph']) {
            $param['nilaipph'] = 0;
        }

        $sdel = 'delete from '.$dbname.".keu_penagihanht where noinvoice='".$param['noinvoice']."'";
        if (mysql_query($sdel)) {
            $sinser = "insert into $dbname.keu_penagihanht ".
                "(noinvoice,kodeorg,tanggal,noorder,kodecustomer,nilaiinvoice,nilaippn,jatuhtempo,keterangan, ".
                "bayarke,debet,kredit,nofakturpajak,uangmuka,akunuangmuka,akunpph,potongsusutkgint,potongsusutjmlint,potongsusutkgext,potongsusutjmlext,potongmutuint,potongmutuext,akunppn,namattd,tipe,nilaipph,totaltonase,hargasatuan) values ".
                "('".$param['noinvoice']."','".
                $param['kodeorganisasi']."','".
                tanggalsystem($param['tanggal'])."','".
                $param['noorder']."','".
                $param['kodecustomer']."','".
                $param['dpp']."','".
                $param['nilaippn']."','".
                tanggalsystem($param['jatuhtempo'])."','".
                $param['keterangan']."','".
                $param['bayarke']."','".
                $param['debet']."','".
                $param['kredit']."','".
                $param['nofakturpajak']."','".
                $param['uangmuka']."','".
                $param['akunuangmuka']."','".
                $param['akunpph']."','".
                $param['potongsusutkgint']."','".
                $param['potongsusutjmlint']."','".
                $param['potongsusutkgext']."','".
                $param['potongsusutjmlext']."','".
                $param['potongmutuint']."','".
                $param['potongmutuext']."','".
                $param['noakunppn']."','".
                $param['ttd']."','".
                $param['tipe']."','".
                $param['nilaipph']."','".
                $param['tonase']."','".
                $param['hargasatuan'].
                "')";
                saveLog($sinser);
            if (!mysql_query($sinser)) {
                exit("error: code 1125\n ".mysql_error($conn).'___'.$sinser);
            }
            echo $sinser;
            break;
        }
        exit("error: code 1125\n ".mysql_error($conn).'___'.$sdel);
    case 'genNo':
          $lokasitugas = $_SESSION['empl']['induklokasitugas'];
          $tanggal = $_POST['tanggal'];
          if(!$tanggal=='' || !empty($tanggal)){
          	$data = explode("-" , $tanggal);
          	$monthh = $data[1];
          	$year = $data[2];
          }else{


			$year = date('Y');
        	$monthh=date('m');
        }
        if($monthh == '01'){
            $month = 'I';
        }else if($monthh == '02'){
            $month = 'II';
        }else if($monthh == '03'){
            $month = 'III';
        }else if($monthh == '04'){
            $month = 'IV';
        }else if($monthh == '05'){
            $month = 'V';
        }else if($monthh == '06'){
            $month = 'VI';
        }else if($monthh == '07'){
            $month = 'VII';
        }else if($monthh == '08'){
            $month = 'VIII';
        }else if($monthh == '09'){
            $month = 'IX';
        }else if($monthh == '10'){
            $month = 'X';
        }else if($monthh == '11'){
            $month = 'XI';
        }else{
            $month = 'XII';
        }
        $where1 = "/" . $lokasitugas;
        $where2 = "/" . $year;
        $where3 = "/" . $month;
        $wherefull= $where1.$where3.$where2;
        $query = "SELECT noinvoice FROM keu_penagihanht WHERE noinvoice like '%".$wherefull."'";
        $queryAct = mysql_query($query);
        $numberRunning = 0;
        $ada = mysql_num_rows($queryAct);
        if($ada = 0){
        $numberRunning = 0;
        }else{
             while($data = mysql_fetch_object($queryAct)){
                 $explodeNoinvoice = explode("/", $data->noinvoice);
                $numberRunning =$explodeNoinvoice[0];
;
           
        } 
        }
      

        $numberRunning += 1;
        $numberRunning = addZero($numberRunning, 3);
        $noinvoice = $numberRunning."/".$lokasitugas."/".$month."/".$year;
       
        echo $noinvoice;
        break;
         case 'simpanDt':
         $noinvoice = $param['noinvoice'];
         $nokontrak = $param['kontrkno'];
         $nodo = $param['nodo'];
         $notiket = $param['notiket'];
        $sIns = 'INSERT INTO '.$dbname.".`keu_penagihandt` (`noinvoice`, `nokontrak`, `nodo`, `notiket`) VALUES ('".$noinvoice."','".$nokontrak."','".$nodo."','".$notiket."')";
         if (mysql_query($sIns)) {
           
            $stat = 1;
            echo $stat;
         }else{
             $stat = 0;
                echo $stat;
                
         }
        break;
    case 'loadData':
        if (@$param['noinvoice'] != '') {
            $where = " and noinvoice like '%".$param['noinvoice']."%'";
        }
        if (@$param['tanggalCr'] != '') {
            $tgrl = explode('-', $param['tanggalCr']);
            $ert = $tgrl[2].'-'.$tgrl[1].'-'.$tgrl[0];
            $where = " and left(tanggal,10) = '".$ert."'";
        }

        $sdel = '';
        $limit = 10;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $sql = 'select count(*) jmlhrow from '.$dbname.".keu_penagihanht where kodeorg='".$_SESSION['empl']['lokasitugas']."' ".$where.' order by tanggal desc';
        $query = mysql_query($sql);
        while ($jsl = mysql_fetch_object($query)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $str = 'select * from '.$dbname.".keu_penagihanht where kodeorg='".$_SESSION['empl']['lokasitugas']."'  ".$where."  order by tanggal desc\r\n              limit ".$offset.','.$limit.' ';
		#echo $str;
        $qstr = mysql_query($str);
        while ($rstr = mysql_fetch_assoc($qstr)) {
            ++$nor;
            $tab .= '<tr '.$bgdr." class=rowcontent>\r\n                 <td id='noinvoice_".$nor."' align=center value='".$rstr['noinvoice']."'>".$rstr['noinvoice']."</td>\r\n                 <td id='kodeorg_".$nor."' align=center value='".$rstr['kodeorg']."'>".$rstr['kodeorg']."</td>\r\n                 <td id='tanggal_".$nor."' align=center value='".$rstr['tanggal']."'>".tanggalnormal(substr($rstr['tanggal'], 0, 10))."</td>\r\n                 <td id='noakun_".$nor."' align=center value='".$rstr['noorder']."'>".$rstr['noorder']."</td>\r\n                 <td align=center>".$rstr['jumlah']."</td>\r\n                 <td align=center>".$rstr['keterangan'].'</td>';
            if (0 == $rstr['posting']) {
                $tab .= "<td align=center><img src=images/application/application_edit.png class=resicon  title='Edit ".$rstr['noinvoice']."' onclick=\"fillField('".$rstr['noinvoice']."');\" ></td>";
                $tab .= "<td align=center><img src=images/application/application_delete.png class=resicon  title='Hapus ".$rstr['noinvoice']."' onclick=\"delData('".$rstr['noinvoice']."');\" ></td>";
                $tab .= "<td align=center><img src=images/pdf.jpg class=resicon  title='Detail ".$rstr['notransaksi']."' onclick=\"masterPDF('keu_penagihanht','".$rstr['noinvoice']."','','keu_slave_print_pengihan',event);\" ></td>";
                $tab .= "<td align=center><img src=images/excel.jpg class=resicon  title='Penagihan Excel' onclick=\"masterExcell('keu_penagihanht','".$rstr['noinvoice']."','','keu_slave_print_pengihanexcel',event);\" ></td>";
                $tab .= "<td align=center><img src=images/skyblue/posting.png class=resicon  title='Posting ".$rstr['noinvoice']."' onclick=\"postingData('".$rstr['noinvoice']."');\" ></td>";

            } else {
                $tab .= "<td align=center colspan=2><img src=images/pdf.jpg class=resicon  title='Detail ".$rstr['noinvoice']."'  onclick=\"masterPDF('keu_penagihanht','".$rstr['noinvoice']."','','keu_slave_print_pengihan',event);\" ></td>";
                $tab .= "<td align=center><img src=images/excel.jpg class=resicon  title='Penagihan Excel' onclick=\"masterExcell('keu_penagihanht','".$rstr['noinvoice']."','','keu_slave_print_pengihanexcel',event);\" ></td>";
                $tab .= "<td align=center colspan=2><img src=images/skyblue/posted.png class=resicon  title='Posted ".$rstr['noinvoice']."' ></td>";
            }

            $tab .= '</tr>';
        }
        $skeupenagih = 'select count(*) as rowd from '.$dbname.".keu_penagihanht where kodeorg='".$_SESSION['empl']['lokasitugas']."'";
        $qkeupenagih = mysql_query($skeupenagih);
        $rkeupenagih = mysql_num_rows($qkeupenagih);
        $totrows = ceil($rkeupenagih / $limit);
        if (0 == $totrows) {
            $totrows = 1;
        }

        for ($er = 1; $er <= $totrows; ++$er) {
            $isiRow .= "<option value='".$er."'>".$er.'</option>';
        }
        $footd .= "</tr>\r\n            <tr><td colspan=10 align=center>\r\n            \r\n            <button class=mybutton onclick=loadData(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n            <select id=\"pages\" name=\"pages\" style=\"width:50px\" onchange=\"getPage()\">".$isiRow."</select>\r\n            <button class=mybutton onclick=loadData(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n            </td>\r\n            </tr>";
        echo $tab.'####'.$footd;

        break;
    case 'getData':
        $sdata = 'select distinct * from '.$dbname.".keu_penagihanht where noinvoice='".$param['noinvoice']."'";
        $qdata = mysql_query($sdata);
        $rdata = mysql_fetch_assoc($qdata);
        echo $rdata['noinvoice'].'###'.$rdata['kodeorg'].'###'.tanggalnormal(substr($rdata['tanggal'], 0, 10)).'###'.$rdata['noorder'].'###'.$rdata['kodecustomer'].'###'.$rdata['nilaiinvoice'].'###'.$rdata['nilaippn'].'###'.tanggalnormal(substr($rdata['jatuhtempo'], 0, 10)).'###'.$rdata['keterangan'].'###'.$rdata['bayarke'].'###'.$rdata['debet'].'###'.$rdata['kredit'].'###'.$rdata['nofakturpajak'].'###'.$rdata['uangmuka'];

        break;
        case 'getDataPPh':
        $sdata = 'select  * from '.$dbname.".pmn_4customer where kodecustomer='".$param['cust']."'";
        $qdata = mysql_query($sdata);
        $rdata = mysql_fetch_assoc($qdata);
        $ada = mysql_num_rows($qdata);
        if($ada = 0){
          $nilaipph =  ($param['nilaiinvoice'] )*(0.05);
        }else{
            $nilaipph =  ($param['nilaiinvoice'] )*(0.025);
        }
        echo $nilaipph;

        break;
          case 'getDataDetail':
    

        // menentukan kode barang
       
        $lokasitugas = $_SESSION['empl']['induklokasitugas'];
           $tanggal = $_POST['tanggal'];
          if(!$tanggal=='' || !empty($tanggal)){
          	$data = explode("-" , $tanggal);
          	$monthh = $data[1];
          	$year = $data[2];
          }else{


			$year = date('Y');
        	$monthh=date('m');
        }
         if($monthh == '01'){
            $month = 'I';
        }else if($monthh == '02'){
            $month = 'II';
        }else if($monthh == '03'){
            $month = 'III';
        }else if($monthh == '04'){
            $month = 'IV';
        }else if($monthh == '05'){
            $month = 'V';
        }else if($monthh == '06'){
            $month = 'VI';
        }else if($monthh == '07'){
            $month = 'VII';
        }else if($monthh == '08'){
            $month = 'VIII';
        }else if($monthh == '09'){
            $month = 'IX';
        }else if($monthh == '10'){
            $month = 'X';
        }else if($monthh == '11'){
            $month = 'XI';
        }else{
            $month = 'XII';
        }
        $where1 = "/" . $lokasitugas;
        $where2 = "/" . $year;
        $where3 = "/" . $month;
        $wherefull= $where1.$where3.$where2;
        $query = "SELECT noinvoice FROM keu_penagihanht WHERE noinvoice like '%".$wherefull."'";
        $queryAct = mysql_query($query);
        $numberRunning = 0;
        $ada = mysql_num_rows($queryAct);
        if($ada = 0){
        $numberRunning = 0;
        }else{
             while($data = mysql_fetch_object($queryAct)){
                 $explodeNoinvoice = explode("/", $data->noinvoice);
                $numberRunning =$explodeNoinvoice[0];
;
           
        } 
        }
      

        $numberRunning += 1;
        $numberRunning = addZero($numberRunning, 3);
        $noinvoice = $numberRunning."/".$lokasitugas."/".$month."/".$year;
         $sdt = 'select distinct * from '.$dbname.".pmn_kontrakjual where nodo='".$param['sipb']."'";
        $qdt = mysql_query($sdt);
        $rdt = mysql_fetch_assoc($qdt);
         $sadt = 'select sum(beratbersih) as tonase from '.$dbname.".pabrik_timbangan where nosipb='".$param['sipb']."' and IsPosting=1 and notransaksi not in(select notiket from keu_penagihandt where nodo='".$param['sipb']."') order by notransaksi ASC";
        $qadt = mysql_query($sadt);
        $radt = mysql_fetch_assoc($qadt);
        
        $nilai =($rdt['hargasatuan']) * ($radt['tonase']);
        echo $nilai.','.$noinvoice.','.$radt['tonase'].','.$rdt['hargasatuan'];

        break;
        case 'getDataDetail1':
    

        // menentukan kode barang
       
        $lokasitugas = $_SESSION['empl']['induklokasitugas'];
           $tanggal = $_POST['tanggal'];
          if(!$tanggal=='' || !empty($tanggal)){
          	$data = explode("-" , $tanggal);
          	$monthh = $data[1];
          	$year = $data[2];
          }else{


			$year = date('Y');
        	$monthh=date('m');
        }
         if($monthh == '01'){
            $month = 'I';
        }else if($monthh == '02'){
            $month = 'II';
        }else if($monthh == '03'){
            $month = 'III';
        }else if($monthh == '04'){
            $month = 'IV';
        }else if($monthh == '05'){
            $month = 'V';
        }else if($monthh == '06'){
            $month = 'VI';
        }else if($monthh == '07'){
            $month = 'VII';
        }else if($monthh == '08'){
            $month = 'VIII';
        }else if($monthh == '09'){
            $month = 'IX';
        }else if($monthh == '10'){
            $month = 'X';
        }else if($monthh == '11'){
            $month = 'XI';
        }else{
            $month = 'XII';
        }
        $where1 = "/" . $lokasitugas;
        $where2 = "/" . $year;
        $where3 = "/" . $month;
        $wherefull= $where1.$where3.$where2;
        $query = "SELECT noinvoice FROM keu_penagihanht WHERE noinvoice like '%".$wherefull."'";
        $queryAct = mysql_query($query);
        $numberRunning = 0;
        $ada = mysql_num_rows($queryAct);
        if($ada = 0){
        $numberRunning = 0;
        }else{
             while($data = mysql_fetch_object($queryAct)){
                 $explodeNoinvoice = explode("/", $data->noinvoice);
                $numberRunning =$explodeNoinvoice[0];
;
           
        } 
        }
      

        $numberRunning += 1;
        $numberRunning = addZero($numberRunning, 3);
        $noinvoice = $numberRunning."/".$lokasitugas."/".$month."/".$year;
         $sdt = 'select distinct * from '.$dbname.".pmn_kontrakjual where nodo='".$param['sipb']."'";
        $qdt = mysql_query($sdt);
        $rdt = mysql_fetch_assoc($qdt);
        $sadt = 'select sum(beratbersih) as tonase from '.$dbname.".pabrik_timbangan where nosipb='".$param['sipb']."' and IsPosting=1 and notransaksi not in(select notiket from keu_penagihandt where nodo='".$param['sipb']."') order by notransaksi ASC";
        $qadt = mysql_query($sadt);
        $radt = mysql_fetch_assoc($qadt);
        
        $nilai =($rdt['hargasatuan']) * ($rdt['kuantitaskontrak']);
        echo $nilai.','.$noinvoice;

        break;
        case 'getAkun':
    
        if($_POST['tipe']=='1'){
            echo '1130101####2140101'; 
        }
        break;
        case 'getDataDetail2':
  
        // menentukan kode barang    
        $lokasitugas = $_SESSION['empl']['induklokasitugas'];
           $tanggal = $_POST['tanggal'];
          if(!$tanggal=='' || !empty($tanggal)){
          	$data = explode("-" , $tanggal);
          	$monthh = $data[1];
          	$year = $data[2];
          }else{


			$year = date('Y');
        	$monthh=date('m');
        }
         if($monthh == '01'){
            $month = 'I';
        }else if($monthh == '02'){
            $month = 'II';
        }else if($monthh == '03'){
            $month = 'III';
        }else if($monthh == '04'){
            $month = 'IV';
        }else if($monthh == '05'){
            $month = 'V';
        }else if($monthh == '06'){
            $month = 'VI';
        }else if($monthh == '07'){
            $month = 'VII';
        }else if($monthh == '08'){
            $month = 'VIII';
        }else if($monthh == '09'){
            $month = 'IX';
        }else if($monthh == '10'){
            $month = 'X';
        }else if($monthh == '11'){
            $month = 'XI';
        }else{
            $month = 'XII';
        }
        $where1 = "/" . $lokasitugas;
        $where2 = "/" . $year;
        $where3 = "/" . $month;
        $wherefull= $where1.$where3.$where2;
        $query = "SELECT noinvoice FROM keu_penagihanht WHERE noinvoice like '%".$wherefull."'";
        $queryAct = mysql_query($query);
        $numberRunning = 0;
        $ada = mysql_num_rows($queryAct);
        if($ada = 0){
        $numberRunning = 0;
        }else{
             while($data = mysql_fetch_object($queryAct)){
                 $explodeNoinvoice = explode("/", $data->noinvoice);
                $numberRunning =$explodeNoinvoice[0];
           
        } 
        }
      
        $numberRunning += 1;
        $numberRunning = addZero($numberRunning, 3);
        $noinvoice = $numberRunning."/".$lokasitugas."/".$month."/".$year;
         $sdt = 'select distinct * from '.$dbname.".pmn_kontrakjual where nodo='".$param['sipb']."'";
        $qdt = mysql_query($sdt);
        $rdt = mysql_fetch_assoc($qdt);
        $sadt = 'select sum(beratbersih) as tonase from '.$dbname.".pabrik_timbangan where nosipb='".$param['sipb']."' and IsPosting=1 and notransaksi not in(select notiket from keu_penagihandt where nodo='".$param['sipb']."') order by notransaksi ASC";
        $qadt = mysql_query($sadt);
        $radt = mysql_fetch_assoc($qadt);
        
/*        $sadt = 'select * from '.$dbname.".keu_penagihanht where noorder='".$param['sipb']."'";
        $qadt = mysql_query($sadt);
        $radt = mysql_fetch_assoc($qadt);
        $tipe = $radt['tipe'];
        saveLog($sadt);
        if($tipe=='2'){
             $sPeg = 'select * from '.$dbname.".keu_penagihanht where noorder='".$param['sipb']."'";
        $qPeg = mysql_query($sPeg);
        $rPeg = mysql_fetch_assoc($qPeg);
        $uangmuka = $rPeg['uangmuka'];
        $nilaiinvoice = $rPeg['nilaiinvoice'];
        }else{
*/             $sPegUangMuka = 'select * from '.$dbname.".keu_penagihanht where noorder='".$param['sipb']."' and tipe='1'";
        //     saveLog($sPegUangMuka);
        $qPegUangMuka = mysql_query($sPegUangMuka);
        $rPegUangMuka = mysql_fetch_assoc($qPegUangMuka);
        $uangmuka = $rPegUangMuka['nilaiinvoice'];
        //saveLog($uangmuka);
        $sPegNilai = 'select * from '.$dbname.".keu_penagihanht where noorder='".$param['sipb']."' and tipe='0'";
       // saveLog($sPegNilai);
        $qPegUangMuka = mysql_query($sPegNilai);
        $rPegUangMuka = mysql_fetch_assoc($qPegUangMuka);
        $nilaiinvoice = $rPegUangMuka['nilaiinvoice'];
//        }
//        saveLog($rdt['hargasatuan']);
//                saveLog($radt['tonase']);
//                saveLog($nilaiinvoice);
//        $nilai =((($rdt['hargasatuan']) * ($radt['tonase']))-($nilaiinvoice + $uangmuka));
        $nilai =(($rdt['hargasatuan']) * ($radt['tonase']));
        
//        echo $nilai.','.$noinvoice.','.$uangmuka;
        echo $nilai.','.$noinvoice.','.$radt['tonase'].','.$rdt['hargasatuan'].','.$uangmuka;

        break;
         case 'getHslSusut':
        $sdt = 'select distinct * from '.$dbname.".pmn_kontrakjual where nokontrak='".$param['nokontrak']."'";
        $qdt = mysql_query($sdt);
        $rdt = mysql_fetch_assoc($qdt);
        $nilai =round(($rdt['hargasatuan']) * ($param['kgpotsusut']));
 //       $nilai =round(($rdt['grand_total']) * ($param['kgpotsusut']));
        echo $nilai;

        break;
      
    case 'getFormNosipb':
        $optSupplierCr = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sSuplier = 'select distinct supplierid,namasupplier,substr(kodekelompok,1,1) as status from '.$dbname.'.log_5supplier order by namasupplier asc';
        $qSupplier = mysql_query($sSuplier) || exit(mysql_error($sSupplier));
        while ($rSupplier = mysql_fetch_assoc($qSupplier)) {
            $optSupplierCr .= "<option value='".$rSupplier['supplierid']."'>".$rSupplier['namasupplier'].' ['.$rSupplier['status'].']</option>';
        }
        $form = "<fieldset style=float: left;>\r\n               <legend>".$_SESSION['lang']['find'].' '.$_SESSION['lang']['nosipb']."</legend>\r\n               ".$_SESSION['lang']['nosipb'].'&nbsp;<input type=text class=myinputtext id=nosipbcr />&nbsp;&nbsp;&nbsp;<button class=mybutton onclick=findNosipb()>'.$_SESSION['lang']['find']."</button></fieldset>\r\n               <fieldset><legend>".$_SESSION['lang']['result'].'</legend><div id=container2 style=overflow:auto;width:100%;height:430px;></fieldset></div>';
        echo $form;

        break;
    case 'getnosibp':
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
        $tab .= '<thead>';
        $tab .= '<tr><td>'.$_SESSION['lang']['nosipb'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['kodecustomer'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['namacustomer'].'</td></tr></thead><tbody>';

//        $sdata = 'select distinct nodo AS SIPBNO,nokontrak, koderekanan from '.$dbname.".pmn_kontrakjual where nodo like '%".$param['txtfind']."%' and kodept='".$_SESSION['empl']['induklokasitugas']."'";
  
        $sdata = 'select distinct nodo AS SIPBNO,nokontrak, koderekanan from '.$dbname.".pmn_kontrakjual where nodo like '%".$param['txtfind']."%' and kodept='".$_SESSION['empl']['induklokasitugas']."'";

        $qdata = mysql_query($sdata);
        while ($rdata = mysql_fetch_assoc($qdata)) {
            $brt = "style=cursor:pointer; onclick=setData('".$rdata['SIPBNO']."','".$rdata['koderekanan']."','".$rdata['nokontrak']."')";
            $tab .= '<tr '.$brt.' class=rowcontent><td>'.$rdata['SIPBNO'].'</td>';
            $tab .= '<td>'.$rdata['koderekanan'].'</td>';
            $tab .= '<td>'.$optnmcust[$rdata['koderekanan']].'</td></tr>';
        }
        $tab .= '</tbody></table>';
        echo $tab;

        break;
    case 'delData':
        $sdel = 'delete from '.$dbname.".keu_penagihanht where noinvoice='".$param['noinvoice']."'";
        $sdela = 'delete from '.$dbname.".keu_penagihandt where noinvoice='".$param['noinvoice']."'";
        if (!mysql_query($sdel)) {
            exit('error: gak berhasil'.mysql_error($conn).'___'.$sdel);
        }
         if (!mysql_query($sdela)) {
            exit('error: gak berhasil'.mysql_error($conn).'___'.$sdel);
        }

        break;
     case 'preview':
        $nodo = $_POST['nodo'];
        $netto =0;
         $sdt = 'select distinct * from '.$dbname.".pmn_kontrakjual where nodo='".$nodo."' ";
        $qdt = mysql_query($sdt);
        $rdt = mysql_fetch_assoc($qdt);
        $tonase_ctr = $rdt['kuantitaskontrak'];
        $sCob = 'select * from '.$dbname.".pabrik_timbangan where  nosipb = '".$nodo."' and IsPosting=1 and notransaksi not in(select notiket from keu_penagihandt where nodo='".$nodo."') order by notransaksi ASC";
     //   saveLog($sCob);
        $res = mysql_query($sCob);
        $row = mysql_num_rows($res);
        if ($row > 0) {
            echo "<div style='overflow:scroll;width:100%'>\r\n\t <table class=sortable cellspacing=1 border=0 >\r\n\t<thead>\r\n\t<tr class=rowheader align=center>\r\n\t<td>No.</td>\r\n\t<td>"
            .$_SESSION['lang']['tanggal']."</td>\r\n\t<td>No Tiket</td><td>No DO</td><td>No Kontrak</td>\r\n\t<td>Netto</td>   </tr>\r\n\t</thead><tbody id=ListData>";
 
            while ($hsl = mysql_fetch_assoc($res)) {
                $no++;
                $netto += $hsl['beratbersih'];
              
                
                $tanggal = substr($hsl['tanggal'], 10, 9);
                echo '<tr class=rowcontent id=row_'.$no." >\r\n\t\t\t<td ><input type=checkbox   id=chk".$no."  class=myinputtext onchange=hitungTonase() checked=true></td>\r\n\t\t\t<td id=tglData_"
                .$no.'>'.$hsl['tanggal']."</td>\r\n\t\t\t<td id=notiket"
                .$no.'>'.$hsl['notransaksi']."</td>\r\n\t\t\t<td id=nodo"
                .$no.'>'.$hsl['nosipb']."</td>\r\n\t\t\t<td id=kntrkno".$no.'>'.$hsl['nokontrak']."</td>\r\n\t\t\t<td id=netto"
                .$no.'>'.$hsl['beratbersih']."</td>   </tr>\r\n\t\t\t";
            }
            $presentase = ($netto/$tonase_ctr)*100;
           echo" <tr  class=rowcontent><td colspan=30>Total Netto :".$netto." (".$presentase."% dari Kontrak ".$tonase_ctr.")</td></tr><tr  class=rowcontent> Total Data :".$row." <td colspan=30 id=MaxRow>".$row.'</td></tr>';
        } else {
            echo " <table class=sortable cellspacing=1 border=0>\r\n\t<thead>\r\n\t<tr class=rowheader align=center>\r\n\t<td>No.</td>\r\n\t<td>"
            .$_SESSION['lang']['tanggal']."</td>\r\n\t<td>No Tiket</td><td>No DO</td><td>No Kontrak</td>\r\n\t<td>Netto</td>   </tr>\r\n\t</thead><tbody><tr class=rowcontent align=center><td colspan=12>Not Found</td><td colspan=30 id=MaxRow></td></tr>";
        }

        echo '</tbody></table></div>';

        break;

    case 'postingData':
        $sdata = 'select distinct * from '.$dbname.".keu_penagihanht where noinvoice='".$param['noinvoice']."'";
        $qdata = mysql_query($sdata);
        $rdata = mysql_fetch_assoc($qdata);
        $roc = mysql_num_rows($qdata);
        $error0 = '';
        if (1 == $rdata['posting']) {
            $error0 .= $_SESSION['lang']['errisposted'];
        }

        if ('' !== $error0) {
            echo "Data Error :\n".$error0;
            exit();
        }

        $tgl = str_replace('-', '', $rdata['tanggal']);
        if ($tgl < $_SESSION['org']['period']['start']) {
            exit('Error:Date beyond active period');
        }

        $error1 = '';
        if (0 == $roc) {
            $error1 .= $_SESSION['lang']['errheadernotexist']."\n";
        }

        if ('' !== $error1) {
            echo "Data Error :\n".$error1;
            exit();
        }

        $yy = tanggalnormal(substr($rdata['tanggal'], 0, 10));
        $isyy = tanggalsystem($yy);
        $norjunal = $isyy.'/'.$_SESSION['empl']['lokasitugas'].'/PNJ/';
        $snojr = 'select max(substr(nojurnal,19,7)) as nourut from '.$dbname.".keu_jurnalht where nojurnal like '".$norjunal."%'";
        $qnojr = mysql_query($snojr);
        $rnojr = mysql_fetch_assoc($qnojr);
        $nourut = addZero((int) ($rnojr['nourut']) + 1, '3');


        //TIPE 1 UANG MUKA
        if($rdata['tipe']==1){

            $jmlall = ($rdata['nilaiinvoice'] + $rdata['nilaippn'] - $rdata['nilaipph']);
            $nojurnal = $norjunal.$nourut;
 
            $sinsert = "insert into $dbname.keu_jurnalht (nojurnal, kodejurnal, tanggal, tanggalentry, posting, totaldebet, totalkredit, amountkoreksi, noreferensi, autojurnal, matauang, kurs, revisi) values ('".$nojurnal."','PNJ','".$isyy."','".date('Y-m-d')."','1','".$jmlall."','-".$jmlall."','0','".$rdata['noinvoice']."','1','IDR','1','0')";

            if (mysql_query($sinsert)) {
                //DEBET
                $no=1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg,kodecustomer, noreferensi, nodok, revisi,nik,kodesupplier) values 
                    ('".$nojurnal."','".$isyy."','".$no."','". $rdata['debet']."','UANG MUKA DARI ".$optnmcust[$rdata['kodecustomer']]."','".$jmlall."', 'IDR','1','".$_SESSION['empl']['lokasitugas']."','".$rdata['kodecustomer']."','".$rdata['noinvoice']."',
                '".$rdata['noorder']."','0','','')";
                mysql_query($sins);

                //KREDIT
                //DPP
                $no=$no+1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg,kodecustomer, noreferensi, nodok, revisi,nik,kodesupplier) values 
                ('".$nojurnal."','".$isyy."','".$no."','".$rdata['kredit']."','UANG MUKA DARI ".$optnmcust[$rdata['kodecustomer']]."','-".
                $rdata['nilaiinvoice']."','IDR','1','".$_SESSION['empl']['lokasitugas']."','".$rdata['kodecustomer']."','".$rdata['noinvoice']."','".
                $rdata['noorder']."','0','','')";
                mysql_query($sins);

                //PPN
                if($rdata['nilaippn']>0){
                $no=$no+1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg,kodecustomer, noreferensi, nodok, revisi,nik,kodesupplier) values 
                ('".$nojurnal."','".$isyy."','".$no."','2120108','PPN UANG MUKA DARI ".$optnmcust[$rdata['kodecustomer']]."','-".$rdata['nilaippn']."','IDR','1','".$_SESSION['empl']['lokasitugas']."','".$rdata['kodecustomer']."','".$rdata['noinvoice']."','".
                $rdata['noorder']."','0','','')";
                mysql_query($sins);
                }
 
                $supd = 'update '.$dbname.".keu_penagihanht set posting=1 where noinvoice='".$rdata['noinvoice']."'";
                if (!mysql_query($supd)) {
                    exit('error: gak berhasil'.mysql_error($conn).'___'.$supd);
                }
  
            }
        
        }

        if($rdata['tipe']==0){

            $jmlall1 = ($rdata['nilaiinvoice'] + $rdata['potongsusutjmlint'] + $rdata['potongsusutjmlext'] + $rdata['potongmutuint'] + $rdata['potongmutuext']);
            $jmlall = ($rdata['nilaiinvoice'] + $rdata['nilaippn'] - $rdata['nilaipph']);
           
            $nojurnal = $norjunal.$nourut;
 
            $sinsert = "insert into $dbname.keu_jurnalht (nojurnal, kodejurnal, tanggal, tanggalentry, posting, totaldebet, totalkredit, amountkoreksi, noreferensi, autojurnal, matauang, kurs, revisi) values ('".$nojurnal."','PNJ','".$isyy."','".date('Y-m-d')."','1','".$jmlall."','-".$jmlall."','0','".$rdata['noinvoice']."','1','IDR','1','0')";

            if (mysql_query($sinsert)) {
                //DEBET
                $no=1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg,kodecustomer, noreferensi, nodok, revisi,nik,kodesupplier) values 
                    ('".$nojurnal."','".$isyy."','".$no."','". $rdata['debet']."','PIUTANG DARI ".$optnmcust[$rdata['kodecustomer']]."','".$jmlall."', 'IDR','1','".$_SESSION['empl']['lokasitugas']."','".$rdata['kodecustomer']."','".$rdata['noinvoice']."','".$rdata['noorder']."','0','','')";
                mysql_query($sins);

                //POTONGAN SUSUT INT
                if($rdata['potongsusutjmlint']>0){
                $no=$no+1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg,kodecustomer, noreferensi, nodok, revisi,nik,kodesupplier) values 
                ('".$nojurnal."','".$isyy."','".$no."','8110302','Pot. Susut Internal ".$optnmcust[$rdata['kodecustomer']]."','".$rdata['potongsusutjmlint']."','IDR','1','".$_SESSION['empl']['lokasitugas']."','".$rdata['kodecustomer']."','".$rdata['noinvoice']."','".
                $rdata['noorder']."','0','','')";
                mysql_query($sins);
                }

                //POTONGAN MUTU INT
                if($rdata['potongmutuint']>0){
                $no=$no+1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg,kodecustomer, noreferensi, nodok, revisi,nik,kodesupplier) values 
                ('".$nojurnal."','".$isyy."','".$no."','8110301','Pot. Mutu Internal ".$optnmcust[$rdata['kodecustomer']]."','".$rdata['potongmutuint']."','IDR','1','".$_SESSION['empl']['lokasitugas']."','".$rdata['kodecustomer']."','".$rdata['noinvoice']."','".
                $rdata['noorder']."','0','','')";
                mysql_query($sins);
                }

                //POTONGAN SUSUT EKS
                if($rdata['potongsusutjmlext']>0){
                $no=$no+1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg,kodecustomer, noreferensi, nodok, revisi,nik,kodesupplier) values 
                ('".$nojurnal."','".$isyy."','".$no."','1140105','Pot. Susut Eksternal ".$optnmcust[$rdata['kodecustomer']]."','".$rdata['potongsusutjmlext']."','IDR','1','".$_SESSION['empl']['lokasitugas']."','".$rdata['kodecustomer']."','".$rdata['noinvoice']."','".
                $rdata['noorder']."','0','','')";
                mysql_query($sins);
                }

                if($rdata['potongmutuext']>0){
                $no=$no+1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg,kodecustomer, noreferensi, nodok, revisi,nik,kodesupplier) values 
                ('".$nojurnal."','".$isyy."','".$no."','1140105','Pot. Mutu Eksternal ".$optnmcust[$rdata['kodecustomer']]."','".$rdata['potongmutuext']."','IDR','1','".$_SESSION['empl']['lokasitugas']."','".$rdata['kodecustomer']."','".$rdata['noinvoice']."','".
                $rdata['noorder']."','0','','')";
                mysql_query($sins);
                }

                //KREDIT
                $no=$no+1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg,kodecustomer, noreferensi, nodok, revisi,nik,kodesupplier) values 
                ('".$nojurnal."','".$isyy."','".$no."','".$rdata['kredit']."','PENJUALAN DARI ".$optnmcust[$rdata['kodecustomer']]."','-".$jmlall1."','IDR','1','".$_SESSION['empl']['lokasitugas']."','".$rdata['kodecustomer']."','".$rdata['noinvoice']."','".
                $rdata['noorder']."','0','','')";
                mysql_query($sins);

                //PPN
                if($rdata['nilaippn']>0){
                $no=$no+1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg,kodecustomer, noreferensi, nodok, revisi,nik,kodesupplier) values 
                ('".$nojurnal."','".$isyy."','".$no."','2120108','Ppn ".$optnmcust[$rdata['kodecustomer']]."','-".$rdata['nilaippn']."','IDR','1','".$_SESSION['empl']['lokasitugas']."','".$rdata['kodecustomer']."','".$rdata['noinvoice']."','".
                $rdata['noorder']."','0','','')";
                mysql_query($sins);
                }
 
                $supd = 'update '.$dbname.".keu_penagihanht set posting=1 where noinvoice='".$rdata['noinvoice']."'";
                if (!mysql_query($supd)) {
                    exit('error: gak berhasil'.mysql_error($conn).'___'.$supd);
                }
  
            }
        
        }


        if($rdata['tipe']==3){

            $jmlall = ($rdata['nilaiinvoice'] + $rdata['nilaippn'] - $rdata['nilaipph']);
            $nojurnal = $norjunal.$nourut;
 
            $sinsert = "insert into $dbname.keu_jurnalht (nojurnal, kodejurnal, tanggal, tanggalentry, posting, totaldebet, totalkredit, amountkoreksi, noreferensi, autojurnal, matauang, kurs, revisi) values ('".$nojurnal."','PNJ','".$isyy."','".date('Y-m-d')."','1','".$jmlall."','-".$jmlall."','0','".$rdata['noinvoice']."','1','IDR','1','0')";

            if (mysql_query($sinsert)) {
                //DEBET
                $no=1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg,kodecustomer, noreferensi, nodok, revisi,nik,kodesupplier) values 
                    ('".$nojurnal."','".$isyy."','".$no."','". $rdata['debet']."','PIUTANG DARI ".$optnmcust[$rdata['kodecustomer']]."','".$jmlall."', 'IDR','1','".$_SESSION['empl']['lokasitugas']."','".$rdata['kodecustomer']."','".$rdata['noinvoice']."',
                '".$rdata['noorder']."','0','','')";
                mysql_query($sins);

                //POTONGAN SUSUT INT
                if($rdata['potongsusutjmlint']>0){
                $no=$no+1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg,kodecustomer, noreferensi, nodok, revisi,nik,kodesupplier) values 
                ('".$nojurnal."','".$isyy."','".$no."','8110302','Pot. Susut Internal ".$optnmcust[$rdata['kodecustomer']]."','".$rdata['potongsusutjmlint']."','IDR','1','".$_SESSION['empl']['lokasitugas']."','".$rdata['kodecustomer']."','".$rdata['noinvoice']."','".
                $rdata['noorder']."','0','','')";
                mysql_query($sins);
                }

                //POTONGAN MUTU INT
                if($rdata['potongmutuint']>0){
                $no=$no+1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg,kodecustomer, noreferensi, nodok, revisi,nik,kodesupplier) values 
                ('".$nojurnal."','".$isyy."','".$no."','8110301','Pot. Mutu Internal ".$optnmcust[$rdata['kodecustomer']]."','".$rdata['potongmutuint']."','IDR','1','".$_SESSION['empl']['lokasitugas']."','".$rdata['kodecustomer']."','".$rdata['noinvoice']."','".
                $rdata['noorder']."','0','','')";
                mysql_query($sins);
                }

                //POTONGAN SUSUT EKS
                if($rdata['potongsusutjmlext']>0){
                $no=$no+1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg,kodecustomer, noreferensi, nodok, revisi,nik,kodesupplier) values 
                ('".$nojurnal."','".$isyy."','".$no."','1140105','Pot. Susut Eksternal ".$optnmcust[$rdata['kodecustomer']]."','".$rdata['potongsusutjmlext']."','IDR','1','".$_SESSION['empl']['lokasitugas']."','".$rdata['kodecustomer']."','".$rdata['noinvoice']."','".
                $rdata['noorder']."','0','','')";
                mysql_query($sins);
                }

                if($rdata['potongmutuext']>0){
                $no=$no+1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg,kodecustomer, noreferensi, nodok, revisi,nik,kodesupplier) values 
                ('".$nojurnal."','".$isyy."','".$no."','1140105','Pot. Mutu Eksternal ".$optnmcust[$rdata['kodecustomer']]."','".$rdata['potongmutuext']."','IDR','1','".$_SESSION['empl']['lokasitugas']."','".$rdata['kodecustomer']."','".$rdata['noinvoice']."','".
                $rdata['noorder']."','0','','')";
                mysql_query($sins);
                }

                //UANG MUKA
                if($rdata['uangmuka']>0){
                $no=$no+1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg,kodecustomer, noreferensi, nodok, revisi,nik,kodesupplier) values 
                ('".$nojurnal."','".$isyy."','".$no."','".$rdata['akunuangmuka']."','Uang Muka ".$optnmcust[$rdata['kodecustomer']]."','".$rdata['uangmuka']."','IDR','1','".$_SESSION['empl']['lokasitugas']."','".$rdata['kodecustomer']."','".$rdata['noinvoice']."','".
                $rdata['noorder']."','0','','')";
                mysql_query($sins);
                }

                //KREDIT
                //DPP
                $dpp=$rdata['nilaiinvoice']+$rdata['uangmuka']+$rdata['potongsusutjmlint']+$rdata['potongmutuint']+$rdata['potongsusutjmlext']+$rdata['potongmutuext'];
                $no=$no+1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg,kodecustomer, noreferensi, nodok, revisi,nik,kodesupplier) values 
                ('".$nojurnal."','".$isyy."','".$no."','".$rdata['kredit']."','PENJUALAN DARI ".$optnmcust[$rdata['kodecustomer']]."','-".$dpp."','IDR','1','".$_SESSION['empl']['lokasitugas']."','".$rdata['kodecustomer']."','".$rdata['noinvoice']."','".
                $rdata['noorder']."','0','','')";
                mysql_query($sins);

                //PPN
                if($rdata['nilaippn']>0){
                $no=$no+1;
                $sins = "insert into $dbname.keu_jurnaldt (nojurnal, tanggal, nourut, noakun, keterangan, jumlah, matauang, kurs, kodeorg,kodecustomer, noreferensi, nodok, revisi,nik,kodesupplier) values 
                ('".$nojurnal."','".$isyy."','".$no."','2120108','Ppn ".$optnmcust[$rdata['kodecustomer']]."','-".$rdata['nilaippn']."','IDR','1','".$_SESSION['empl']['lokasitugas']."','".$rdata['kodecustomer']."','".$rdata['noinvoice']."','".
                $rdata['noorder']."','0','','')";
                mysql_query($sins);
                }

 
                $supd = 'update '.$dbname.".keu_penagihanht set posting=1 where noinvoice='".$rdata['noinvoice']."'";
                if (!mysql_query($supd)) {
                    exit('error: gak berhasil'.mysql_error($conn).'___'.$supd);
                }
  
            }
        
        }

        break;
}

?>