<?
require_once('Mail.php');
require_once('Mail/mime.php');
require_once('../config/connection.php');
require_once('../lib/pearUserSMTP.php');
require_once('../lib/nangkoelib.php');

?>
<html>
<head>
<script language=javascript1.2 type=text/javascript>
function createXMLHttpRequest() {
   try { return new ActiveXObject("Msxml2.XMLHTTP"); } 
   catch (e) {}
   try { return new ActiveXObject("Microsoft.XMLHTTP"); } 
   catch (e) {}
   try { return new XMLHttpRequest(); } 
   catch(e) {}
   alert("XMLHttpRequest Tidak didukung oleh browser");
   return null;
 }

 var con = createXMLHttpRequest();

function post_response_text(tujuan,param,functiontoexecute)
{
con.open("POST", tujuan, true);
con.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
con.setRequestHeader("Content-length", param.length);
con.setRequestHeader("Connection", "close");

con.onreadystatechange = eval(functiontoexecute);
con.send(param);
}
function error_catch(x){
	switch (x) {
		case 203:
			alert('Dibutuhkan Authority');
			break;
		case 400:
			alert('Error Server');
			break;
		case 403:
			alert('Anda dilarang masuk');
			break;
		case 404:
			alert('File tidak ditemukan');
			break;
		case 405:
			alert('Method tidak diijinkan');
			break;
		case 407:
			alert('Proxy Error');
			break;
		case 408:
			alert('Permintaan terlalu lama');
			break;
		case 409:
			alert('Query Conflict');
			break;
		case 414:
			alert('ULI terlalu panjang');
			break;
		case 412:
			alert('Variable terlalu banyak');
			break;
		case 415:
			alert('Unsupported Media Type');
			break;
		case 500:
			alert('Server busy, try submit later');
			break;
		case 502:
			alert('Bad gateway');
			break;
		case 505:
			alert('Browser anda terlalu tua');
			break;
	}
}	
function time_()
{
 setInterval("jam()",500);
}
function jam()
{
 var x=new Date();
 var menit=x.getMinutes();
 var detik=x.getSeconds();
 var jam=x.getHours();
 var tg=x.getDate();

 if (jam == '10' && menit == '10' && detik == '10') {//wil execute on hour 06:06:06 everyday
 	window.location.reload();//reload to send mail 
	}

document.getElementById('waktu').innerHTML=jam+":"+menit+":"+detik;

  yengki=x.getDay();  
  if (jam == 3 && menit == 0 && detik == 0) {//jam 3 pagi
  	    if (yengki == 0)//jika hari minggu, 1=senin,2=selasa dst
			{      //untuk mengubah hari, cukup sesuaikan saja yengki
			 //periksaPosting();
			}
  }	

	
}

</script>
<title>Karyawan Kontract Scheduler</title>
</head>
<body onload=time_()>
Karyawan Kontrak Sheduler:<br><span id=waktu style='color:#4444FF;font-size:24px;'></span>
<br>Automatic reload on: 10:10:10	
<?
//mail config
 $host = "116.90.167.32";
 $username = "admin@minanga.co.id";
 $password = "pintubesarutara";
 $mail = Mail::factory('smtp',
   array ('host' => $host,
     'auth' => true,
	 'port' => 25,
     'username' => $username,
     'password' => $password));
	//================== 
$str="select * from ".$dbname.".remindertarget where UPPER(remindertype)='KONTRAK'
      and onoff=1";
$res=mysql_query($str);
$mailto='';
$x=0;
while($bar=mysql_fetch_object($res))
{
	$x+=1;
	if($x==1)
	 $mailto="<".$bar->email.">";
	else
	 $mailto.=",<".$bar->email.">";	 
}
$tujuh=mktime(0,0,0,date('m'),date('d')+30,date('Y'));
$tujuh=date('Y-m-d',$tujuh); //tujuh hari kedepan
$hariini=mktime(0,0,0,date('m'),date('d'),date('Y'));
$hariini=date('Y-m-d',$hariini);//hari ini

//respond 0 berarti sudah di respond oleh penerima reminder
$str="select a.*,b.name from ".$dbname.".user_emplcontract a,
      ".$dbname.".user_empl b where dateend>='".$hariini."' 
      and dateend<='".$tujuh."' and respond=0
	  and a.userid=b.userid";

$content="Dear All,

Berikut karyawan kontrak yang akan berakhir kontraknya satu bulan dari sekarang:
	";

$res=mysql_query($str);
$no=0;
while($bar=mysql_fetch_object($res))
{
	$no+=1;
	$content.=$no.". ".$bar->name.", Kontrak ke ".$bar->kontrakke.", berakhir tanggal:".tanggalnormal($bar->dateend).".
	";
}

$content.="

Mohon segera di F/U berkenaan denganakan berakhirnya kontrak dari karyawan-karyawan tersebut.


regards,
Minanga Online System
".date('Ymd H:i:s');   		     	   
	
//============================
	$reply  ='noreply';
	$from   ='Administrator <admin@minanga.co.id>';
	$subject="Employee Contrack Reminder";
	$cc='';
	$bcc='<r.ginting@minanga.co.id>';

    $headers = array(
					"From"=>$from,
					"To"=>$mailto,
					"Reply-To"=>$reply,
					"Cc"=>$cc, 
					"Bcc"=>$bcc,
					"Subject"=>$subject);
   if($no>0)
   {					
    $res=$mail->send($mailto, $headers, $content);
	if (PEAR::isError($res)) {
	  echo("<p>" . $res->getMessage() . "</p>");
	 } else {
	 	$stg="update ".$dbname.".user_emplcontract
		      set remindersent=1 where dateend>='".$hariini."' 
              and dateend<='".$tujuh."' and remindersent=0";
		mysql_query($stg);	  
	  echo("<p>Message successfully sent on: ".date('d-m-Y H:i:s')."</p>");
	 }
   }
   else
   {
   	echo "No data.. on: ".date('d-m-Y H:i:s');
   }
?>
</body>
</html>