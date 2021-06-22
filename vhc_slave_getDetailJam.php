<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
?>
<link rel=stylesheet type=text/css href=style/generic.css>
<?      
$param=$_GET;
  $str="select b.tanggal,a.notransaksi,a.alokasibiaya,a.keterangan,a.jumlah 
        from ".$dbname.".vhc_rundt a 
        left join ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi  
        where kodevhc='".$param['kodevhc']."' 
        and tanggal = '".$param['tanggal']."'";
$res=mysql_query($str);
echo "Detail Activity :".$param['kodevhc']." ".$_SESSION['lang']['tanggal']." ".$param['tanggal']."
      <table class=sortable cellspacing=1 border=0><thead>
      <tr class=rowheader><td>".$_SESSION['lang']['nomor']."</td>
          <td>".$_SESSION['lang']['tanggal']."</td>
          <td>".$_SESSION['lang']['notransaksi']."</td>
          <td>".$_SESSION['lang']['alokasibiaya']."</td>
          <td>".$_SESSION['lang']['keterangan']."</td>
          <td>".$_SESSION['lang']['jumlah']."(HM/KM)</td>  
      </tr>
      </thead>
      <tbody>";
$no=0;
$ttl=0;
while($bar=mysql_fetch_object($res))
{
   $no+=1;
    echo"<tr class=rowcontent>
          <td>".$no."</td>
          <td>".tanggalnormal($bar->tanggal)."</td>   
          <td>".$bar->notransaksi."</td>
          <td>".$bar->alokasibiaya."</td>
          <td>".$bar->keterangan."</td>    
          <td align=right>".$bar->jumlah."</td>
      </tr>";  
    $ttl+=$bar->jumlah;
}
    echo"<tr class=rowcontent>
          <td colspan=5 align=center>Total</td> 
          <td align=right>".$ttl."</td>
      </tr>"; 
echo"</tbody><tfoot></tfoot></table>";
?>