<?
   
function open_body_hrd($title = 'ANTHESIS ERP')
{
    echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">\r\n<html>\r\n\t<head>\r\n\t\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\r\n\t\t<meta http-equiv='Cache-Control' CONTENT='no-cache'>\r\n\t\t<meta http-equiv='Pragma' CONTENT='no-cache'>\r\n\t\t<title>".$title.'</title>';
    echo "\r\n    <script language=JavaScript1.2 src=js/menueAgro2.js></script>\r\n    <script language=JavaScript1.2 src=js/calendar.js></script>\r\n    <script language=JavaScript1.2 src=js/drag.js></script>\r\n    <script language=JavaScript1.2 src=js/generic.js></script>\r\n    <script language=JavaScript1.2 src=js/eagrosort.js></script>\r\n    <!--link rel=stylesheet type=text/css href=style/menu.css-->\r\n\t<link rel=stylesheet type=text/css href=style/menueAgro2.css>\r\n\t<!--link rel=stylesheet type=text/css href=style/generic.css-->\r\n\t<link rel=stylesheet type=text/css href=style/genericGray.css>\r\n\t<link rel=stylesheet type=text/css href=style/calendarblue.css>\r\n\t<link rel=stylesheet type=text/css href=style/zTable.css>\r\n\r\n    </head>\r\n<body  style='margin:0px;margin-top:0px;background-color:#F8F8F8;' onload=verify()>\r\n\r\n<noscript>\r\n\t<span style='font-size:13px;font-family:arial;'>\r\n\t\t<span style='color:#dd3300'>Warning!</span>\r\n\t\t\t&nbsp&nbsp; QuickMenu may have been blocked by IE-SP2's active\r\n\t\t\tcontent option. This browser feature blocks JavaScript from running\r\n\t\t\tlocally on your computer.<br>\r\n\t\t\t<br>This warning will not display once the menu is on-line.\r\n\t\t\tTo enable the menu locally, click the yellow bar above, and select\r\n\t\t\t<span style='color:#0033dd;'>'Allow Blocked Content'\r\n\t\t</span>.\r\n\t<br><br>To permanently enable active content locally...\r\n\t\t<div style=padding:0px 0px 30px 10px;color:#0033dd;'>\r\n\t\t\t<br>1: Select 'Tools' --> 'Internet Options' from the IE menu.\r\n\t\t\t<br>2: Click the 'Advanced' tab.\r\n\t\t\t<br>3: Check the 2nd option under 'Security' in the tree\r\n\t\t\t(Allow active content to run in files on my computer.)\r\n\t\t</div>\r\n\t</span>\r\n</noscript>\r\n<div style='height:30px'>\r\n</div>\r\n\r\n";
}

function OPEN_BODY_BI_HRD($title = 'ANTHESIS ERP')
{
    echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">\r\n<html>\r\n\t<head>\r\n\t\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\r\n\t\t<meta http-equiv='Cache-Control' CONTENT='no-cache'>\r\n\t\t<meta http-equiv='Pragma' CONTENT='no-cache'>\r\n\t\t<title>".$title.'</title>';
    echo "\r\n    <script language=JavaScript1.2 src=js/menueAgro2.js></script>\r\n\t <script language=JavaScript1.2 src=js/calendar.js></script>\r\n    <script language=JavaScript1.2 src=js/drag.js></script>\r\n    <script language=JavaScript1.2 src=js/generic.js></script>\r\n    <script language=JavaScript1.2 src=js/eagrosort.js></script>\r\n\t<link rel=stylesheet type=text/css href=style/menueAgro2.css>\r\n\t<link rel=stylesheet type=text/css href=style/genericGray.css>\r\n\t<link rel=stylesheet type=text/css href=style/calendarblue.css>\r\n    <link rel=stylesheet type=text/css href=style/zTable.css>\r\n    </head>\r\n<body  style='margin-top:10px;margin-left:2px;margin-right:2px;background-color:#E8F4F4;' onload=verify()>\r\n<div id='progress' style='display:none;border:orange solid 1px;width:150px;position:fixed;right:20px;top:65px;color:#ff0000;font-family:Tahoma;font-size:13px;font-weight:bolder;text-align:center;background-color:#FFFFFF;z-index:10000;'>\r\nPlease wait.....! <br>\r\n<img src='images/progress.gif'>\r\n</div>\r\n<img id='smallOwl' src='images/logo.png' width='300px' style='position:absolute;top:20%;left:35%;z-index:-998'>\r\n<noscript>\r\n\t<span style='font-size:13px;font-family:arial;'>\r\n\t\t<span style='color:#dd3300'>Warning!</span>\r\n\t\t\t&nbsp&nbsp; QuickMenu may have been blocked by IE-SP2's active\r\n\t\t\tcontent option. This browser feature blocks JavaScript from running\r\n\t\t\tlocally on your computer.<br>\r\n\t\t\t<br>This warning will not display once the menu is on-line.\r\n\t\t\tTo enable the menu locally, click the yellow bar above, and select\r\n\t\t\t<span style='color:#0033dd;'>'Allow Blocked Content'\r\n\t\t</span>.\r\n\t<br><br>To permanently enable active content locally...\r\n\t\t<div style=padding:0px 0px 30px 10px;color:#0033dd;'>\r\n\t\t\t<br>1: Select 'Tools' --> 'Internet Options' from the IE menu.\r\n\t\t\t<br>2: Click the 'Advanced' tab.\r\n\t\t\t<br>3: Check the 2nd option under 'Security' in the tree\r\n\t\t\t(Allow active content to run in files on my computer.)\r\n\t\t</div>\r\n\t</span>\r\n</noscript>\r\n<img src='images/logo.png'  style='height:50px;position:absolute;right:0px;top:0px;;z-index:-998'>\r\n";
}

function close_body_hrd()
{
    echo '</body></html>';
}

function OPEN_BOX_HRD($style = '', $title = '', $id = '', $contentId = '')
{
    echo "<div  id='".$id."' style='width:100%;'><fieldset id=''><legend><span class=judul>&nbsp;".$title."</span></legend>\r\n\t\t<div  id='contentBox".$contentId."' style='overflow:auto;'>\r\n\t\t";
}

function OPEN_BOX2_HRD($style = '', $title = '', $id = '', $contentId = '')
{
    return "<div  id='".$id."' class=\"x-box-blue\" style='".$style."'><div class=\"x-box-tl\"><div class=\"x-box-tr\">\r\n\t\t<div class=\"x-box-tc\"></div></div></div><div class=\"x-box-ml\"><div class=\"x-box-mr\">\r\n\t\t<div class=\"x-box-mc\" id='contentBox".$contentId."' style='overflow:auto;'>\r\n\t\t".$title;
}

function CLOSE_BOX_HRD()
{
    echo "</div></fieldset>\r\n        </div>";
}

function CLOSE_BOX2_HRD()
{
    return "</div></div></div>\r\n        <div ><div ><div ></div></div></div>\r\n        </div>";
}

function drawTab_hrd($tabId, $arrHeader, $arrContent, $tabLength = '250', $contentLength = '1200')
{
    $tabLength = str_replace('px', '', $tabLength);
    $tabLength = str_replace(';', '', $tabLength);
    $contentLength = str_replace('px', '', $contentLength);
    $contentLength = str_replace(';', '', $contentLength);
    $stream = "\r\n<table border=0 cellspacing=0>\r\n<tr class=tab>";
    for ($x = 0; $x < count($arrHeader); ++$x) {
        if (0 === $x) {
            $stream .= '<td id=tab'.$tabId.$x.' onclick=tabAction(this,'.$x.",'".$tabId."',".(count($arrHeader) - 1)."); onmouseover=chgBackgroundImg(this,'./images/tab3.png','#d0d0d0');  onmouseout=chgBackgroundImg(this,'./images/tab1.png','#333333');  style=\"background-image:url('./images/tab2.png');color:#FFFFFF;font-weight:bolder;border-right:#dedede solid 1px;width:".$tabLength.'px;height:20px">'.$arrHeader[$x].'</td>';
        } else {
            $stream .= '<td id=tab'.$tabId.$x." style='border-right:#dedede solid 1px; height:20px; width:".$tabLength."px;' onclick=tabAction(this,".$x.",'".$tabId."',".(count($arrHeader) - 1)."); onmouseover=chgBackgroundImg(this,'./images/tab3.png','#d0d0d0');  onmouseout=chgBackgroundImg(this,'./images/tab1.png','#333333'); >".$arrHeader[$x].'</td>';
        }
    }
    $stream .= '</tr></table>';
    for ($x = 0; $x < count($arrContent); ++$x) {
        if (0 === $x) {
            $stream .= "<fieldset style='display:\"\";border-color:#2368B0; border-style:solid;border-width:2px; border-top:#1E5896 solid 8px; background-color:#E0ECFF;margin-left:0px;width:100%;' id=content".$tabId.$x.'>'.$arrContent[$x].'</fieldset>';
        } else {
            $stream .= "<fieldset style='display:none;border-color:#2368B0; border-style:solid;border-width:2px; border-top:#1E5896 solid 8px; background-color:#E0ECFF;margin-left:0px;width:100%;' id=content".$tabId.$x.'>'.$arrContent[$x].'</fieldset>';
        }
    }
    echo $stream;
}

function OPEN_THEME_HRD($caption = '', $width = '', $text_align = 'left')
{
    if (isset($_SESSION['theme'])) {
        $theme = $_SESSION['theme'];
    } else {
        $theme = 'skyblue';
    }

    if ('black' === $theme) {
        $capcolor = '#FFFFFF';
    } else {
        $capcolor = '#333333';
    }

    if (isset($width)) {
        $lebar = ' width='.$width.'px';
    } else {
        $lebar = '';
    }

    $text = "<table class='boundary' ".$lebar." cellspacing='0' border='0' padding='0' style='font-family:Tahoma;font-size:14px;'>\r\n\t<tr class='trheader' style='height:32px;'>\r\n\r\n\t<td class='headleft' style='width:7px;height:32px;background: url(images/".$theme."/a1xy.gif);background-repeat:no-repeat;'></td>\r\n\t<td class='headtop' align='".$text_align."' style='color:".$capcolor.';height:32px;background: url(images/'.$theme."/a2x.gif);'><b>".$caption."</b></td>\r\n\t<td class='headright' style='width:13px;height:32px;background: url(images/".$theme."/a3x.gif);background-repeat:no-repeat;'></td>\r\n\t</tr>\r\n\r\n\t<tr>\r\n\t<td class='bodyleft' style='background: url(images/".$theme."/a4xx.gif);'></td>\r\n\t<td class='content' style='padding:0px 0px 0px 0px;background-color:#FFFFFF;'>";

    return $text;
}

function CLOSE_THEME_HRD()
{
    if (isset($_SESSION['theme'])) {
        $theme = $_SESSION['theme'];
    } else {
        $theme = 'skyblue';
    }

    $text = "</td>\r\n\t<td class='bodyright' style='background: url(images/".$theme."/a5x.gif);background-repeat:repeat-y;'></td>\r\n\t</tr>\r\n\r\n\t<tr class='trbottom' style='height:7px;'>\r\n\t<td class='bottomleft' style='background: url(images/".$theme."/a6xcvb.gif);background-repeat:no-repeat;'></td>\r\n\t<td class='bottom' style='background: url(images/".$theme."/a7xcv.gif);background-repeat:repeat-x;'></td>\r\n\t<td class='bottomright' style='background: url(images/".$theme."/a8zzc.gif);background-repeat:no-repeat;'></td></tr>\r\n\t</table>";

    return $text;
}
//====================================================end script
function tanggalnormal_hrd($_q)
{
 $_q=str_replace("-","",$_q);
 $_retval=substr($_q,6,2)."-".substr($_q,4,2)."-".substr($_q,0,4);
 return($_retval);
}
function tanggalnormald_hrd($_q)
{
//20090804 08:00:00
 $_q=str_replace("-","",$_q);
 $_retval=substr($_q,6,2)."-".substr($_q,4,2)."-".substr($_q,0,4)." ".substr($_q,9,5);
 return($_retval);
}
function tanggalsystem_hrd($_q)//from format dd-mm-YYYY
{
	if ($_q!='' && $_q!='00-00-0000'){
		$_retval=date('Y',strtotime($_q)).date('m',strtotime($_q)).date('d',strtotime($_q));
	}
	else {
		$_retval=substr($_q,6,4).substr($_q,3,2).substr($_q,0,2);
	}
 //$_retval=date('Ymd',strtotime($_q)); 
 return($_retval);//return 8 chardate format eg.20090804
}

function tanggalsystemd_hrd($_q)//from format dd-mm-YYYY
{//0408
 $_retval=substr($_q,6,4)."-".substr($_q,3,2)."-".substr($_q,0,2).substr($_q,10,7).":00";
 return($_retval);//return 8 chardate format eg.20090804
}

function hari_hrd($tgl,$lang='ID')//$tgl==2009-04-13
{
//return name of days in Indonesia	
	$bln=substr($tgl,5,2);
	$thn=substr($tgl,0,4);
	$tgl=substr($tgl,8,2);
	$ha=date("w", mktime(0, 0, 0, $bln,$tgl,$thn));
	$x=array ("Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu");
	$y=array ("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
	if($lang=='ID')
	   return($x[$ha]);
	else
	   return($y[$ha]);   
}

function getUserEmail_hrd($karyawanid)
{
	//find user email address on datakaryawan table
	global $dbname;
        global $conn;
	$strAv="select email from ".$dbname.".datakaryawan
	        where  karyawanid in(".$karyawanid.")";		
        $resAv=mysql_query($strAv);
        
	$retMail='';
        $no=0;
        while($barAv=mysql_fetch_object($resAv))
	{
            $email=$barAv->email;
            if(strpos($email,'@')>1)
		{
		   if($no==0)
                       $retMail=$email;
                   else
                       $retMail.=",".$email;#comma separated
                       
		}	
         $no+=1;   
        }

        return $retMail;
}

function getNamaKaryawan_hrd($karyawanid)
{
	global $dbname;
        global $conn;
	$strAv="select namakaryawan from ".$dbname.".datakaryawan
	        where  karyawanid in(".$karyawanid.")";		
        $resAv=mysql_query($strAv);
	$retnama='';
        $no=0;
        while($barAv=mysql_fetch_object($resAv))
	{
		   if($no==0)
                       $retnama=$barAv->namakaryawan;
                   else
                       $retnama.=",".$barAv->namakaryawan;#comma separated
         $no+=1;   
        }

        return $retnama;    
}

function getFieldName_hrd($TABLENAME,$output)
{
//get Fieldname on the table mentioned
//this is general purposed
//return value available on array or string like <option value...>
	global $dbname;
	global $conn;
	$option='';
	$arrReturn=Array();
	$strUx="select * from ".$dbname.".".$TABLENAME." limit 1";
	$resUx=mysql_query($strUx);
	while($PxUx=mysql_fetch_field($resUx))
	{
		array_push($arrReturn, $PxUx->name);
		$option.="<option value='".$PxUx->name."'>".$PxUx->name."</option>"; 
	}
	if($output=='array')
	  return $arrReturn;
	else
	  return $option; 
}

function printTableController_hrd($TABLENAME)
{
//seacrh controller for table query
//javascript function stated in tablebrowser.js	
$field=getFieldName($TABLENAME,'option');
echo"<br>".$_SESSION['lang']['find']." <input type=text class=myinputtext id=txtcari onkeypress=\"return tanpa_kutip(event);\" size=15 maxlength=20 onblur=checkThis(this) value=All> ".$_SESSION['lang']['oncolumn'].":<select id=field>".$field."</select>
    ".$_SESSION['lang']['order']." <select id=order1>".$field."</select>,<select id=order2>".$field."</select>
	 ";
echo"<button class=mybutton onclick=\"browseTable('".$TABLENAME."');\">".$_SESSION['lang']['find']."</button>";
	
}

function printSearchOnTable_hrd($TABLENAME,$fieldname,$texttofind,$order='',$curpage=0,$MAX_ROW=1000)
{
 	//$MAX_ROW plese change this if required
	global $dbname;
	global $conn;
	$offset   =$curpage*$MAX_ROW;//
	$disp_page=$curpage+1;
	$field=getFieldName($TABLENAME,'array'); 
	if($texttofind!='')
	{
		$where=" where ".$fieldname." like '%".$texttofind."%'"; 
	}	
	else
	{
		$where='';
	}
	$strXu="select * from ".$dbname.".".$TABLENAME." ".$where."  order by ".$order." limit ".$offset.",".$MAX_ROW;
	$resXu=mysql_query($strXu);
	//get num rows of the query
	//and create page navigator
	$strXur="select * from ".$dbname.".".$TABLENAME." ".$where;
	$resXur=mysql_query($strXur);
	$numrows=mysql_num_rows($resXur);
	if($numrows>$MAX_ROW)
	{
		if(($numrows%$MAX_ROW)!=0)
		    $page=(floor($numrows/$MAX_ROW))+1;
		else
		    $page=$numrows/$MAX_ROW;	
	}	
	else
	{
		$page=1;
	}
	echo $_SESSION['lang']['page']." ".$disp_page." ".$_SESSION['lang']['of']." ".$page." (Max.".$MAX_ROW."/".$_SESSION['lang']['page'].")";
	echo" [ ".$_SESSION['lang']['gotopage'].":<select id=page>";
	for($y=0;$y<$page;$y++)
	{
		echo"<option value=".$y.">".($y+1)."</option>";
	}
	echo "</select> <button onclick=\"navigatepage('".$TABLENAME."');\" class=mybutton>Go</button> ]";
		
	echo"<table class=sortable cellspacing=1 border=0>
	     <thead><tr class=rowheader>";
	for($x=0;$x<count($field);$x++)
	{
		echo"<td>".$field[$x]."</td>";
	}	 
	echo"</tr></thead><tbody>";
	$num=0;
	while($barXu=mysql_fetch_array($resXu))
	{
		echo"<tr class=rowcontent>";
		for($x=0;$x<count($field);$x++)
		{
			echo"<td>".$barXu[$x]."</td>";
		}	
		echo"</tr>";		
	}
	echo"</tbody><tfoot></tfoot></table>";
}
#send mail from win 32
function sendMail_hrd($subject,$content,$from='',$to,$cc='',$bcc='',$replyTo='')//for win
{
	//FOR WINDOW SERVER ONLY
	//$subject,$content and $to is obligatory
	$headers  ='MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    if($from!='')
    $headers .= "From: ".$from. "\r\n";
    if($cc!='')
    $headers .= "Cc: ".$cc. "\r\n";
    if($bcc!='')
	$headers .= 'Bcc: '.$bcc. "\r\n";
	if($replyTo!='')
	$headers .= 'Reply-To: '.$replyTo. "\r\n";
    if(mail($to,$subject,$content,$headers))
	    return true;
	else
	  {
	    return false;
	  }
}
//send mail on ubuntu linux
function  kirimEmail_hrd($to,$subject,$body,$mailType='text/html')//multiple recipient separated by comma
{
    global $conn;
    global $dbname;
    #default
    $port=25;
    $ssl='NO';
    $str="select * from ".$dbname.".setup_remotetimbangan where lokasi='MAILSYS'";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res)){
        $host=trim($bar->ip);
        $username=trim($bar->username);
        $password=trim($bar->password);
        $port=trim($bar->port);
        $ssl=strtoupper(trim($bar->dbname));
    }
    if($ssl=='YES' or $ssl=='TRUE')
    {
        $host="ssl://".$host;
    }
    #mailType posible value 'text/html' or 'text/text'
    
     require_once "Mail.php";   
     $from = "";
//     $host = "192.168.1.205";///"116.90.167.32";
//     $username = "";
//     $password = "ubuntu";
//
//     $headers = array ('From' => $from,
//       'To' => $to,
//       'Subject' => $subject,
//       'Content-Type'=> $mailType);
//     $mail = Mail::factory('smtp',
//       array ('host' => $host,
//         'auth' => true,
//         'port' => 25,
//         'username' => $username,
//         'password' => $password));


     $headers = array ('From' => $from,
       'To' => $to,
       'Subject' => $subject,
       'Content-Type'=> $mailType);
     $mail = Mail::factory('smtp',
       array ('host' => $host,
         'auth' => true,
         'port' => $port,
         'username' => $username,
         'password' => $password));     
     
     if($mailType=='text/html')
     {
         $body.="
                 ";
     }    
	 $toto=explode(",",$to);
	 foreach($toto as $key =>$val){
           $kirim = $mail->send($val, $headers, $body);
       }
     if (PEAR::isError($kirim)) {
       return $kirim->getMessage();
      } else {
       return true;
      }
} 


function readCountry_hrd($file)
{
$comment = "#";

$fp = fopen($file, "r");
$lin=-1;
while (!feof($fp)) {
$line = fgets($fp, 4096); // Read a line.
    if(!ereg("^#",$line) AND $line!='')
	{
    $lin+=1;
	$pieces = explode("=", $line);
    $name = trim($pieces[0]);
    $code = trim($pieces[1]);
	$curr = trim($pieces[2]);
	$cont = trim($pieces[3]);
    $country[$lin][0] = $name;
	$country[$lin][1] = $code;
	$country[$lin][2] = $curr;
	$country[$lin][3] = $cont;
	}
  }

fclose($fp);
return $country;
}

function readTextFile_hrd($file)
{
$handle = fopen($file, "r");
$contents = fread($handle, filesize($file));
fclose($handle);
return $contents;
}

function readOrgType_hrd($file)
{
$comment = "#";

$fp = fopen($file, "r");
$lin=0;
while (!feof($fp)) {
$line = fgets($fp, 4096); // Read a line.
    if(!ereg("^#",$line) AND $line!='')
	{
    $lin+=1;
	$pieces = explode("=", $line);
    $code = trim($pieces[0]);
    $name = trim($pieces[1]);
    $orgtype[$lin][0] = $code;
	$orgtype[$lin][1] = $name;
	}
  }

fclose($fp);
return $country;
}

function numToMonth_hrd($int,$lang='E',$format='short')
{
	$arr=Array();
	$arr[1]['E']['short']='Jan';
	$arr[1]['I']['short']='Jan';
	$arr[1]['E']['long']='January';
	$arr[1]['I']['long']='Januari';
	$arr[2]['E']['short']='Feb';
	$arr[2]['I']['short']='Peb';
	$arr[2]['E']['long']='February';
	$arr[2]['I']['long']='Feberuari';	
	$arr[3]['E']['short']='Mar';
	$arr[3]['I']['short']='Mar';
	$arr[3]['E']['long']='Maret';
	$arr[3]['I']['long']='Maret';	
	$arr[4]['E']['short']='Apr';
	$arr[4]['I']['short']='Apr';
	$arr[4]['E']['long']='April';
	$arr[4]['I']['long']='April';			
	$arr[5]['E']['short']='May';
	$arr[5]['I']['short']='Mei';
	$arr[5]['E']['long']='May';
	$arr[5]['I']['long']='Mei';	
	$arr[6]['E']['short']='Jun';
	$arr[6]['I']['short']='Jun';
	$arr[6]['E']['long']='Juni';
	$arr[6]['I']['long']='Juni';
	$arr[7]['E']['short']='Jul';
	$arr[7]['I']['short']='Jul';
	$arr[7]['E']['long']='July';
	$arr[7]['I']['long']='Juli';	
	$arr[8]['E']['short']='Aug';
	$arr[8]['I']['short']='Agu';
	$arr[8]['E']['long']='August';
	$arr[8]['I']['long']='Agustus';
	$arr[9]['E']['short']='Sep';
	$arr[9]['I']['short']='Sep';
	$arr[9]['E']['long']='September';
	$arr[9]['I']['long']='September';	
	$arr[10]['E']['short']='Oct';
	$arr[10]['I']['short']='Okt';
	$arr[10]['E']['long']='October';
	$arr[10]['I']['long']='Oktober';
	$arr[11]['E']['short']='Nov';
	$arr[11]['I']['short']='Nop';
	$arr[11]['E']['long']='November';
	$arr[11]['I']['long']='Nopember';	
	$arr[12]['E']['short']='Dec';
	$arr[12]['I']['short']='Des';
	$arr[12]['E']['long']='December';
	$arr[12]['I']['long']='Desember';
		
//find and return		
	$int=intval($int);
    return $arr[$int][$lang][$format];
}

//fungsi untuk memeriksa apakah periode transaksi normal

function isTransactionPeriod_hrd()
{
	$stat=true;
	if($_SESSION['org']['period']['start']=='')
	  $stat=false;
	if($_SESSION['org']['period']['end']=='')
	  $stat=false;
	if($_SESSION['org']['period']['bulan']=='')
	  $stat=false;
	if($_SESSION['org']['period']['tahun']=='')
	 $stat=false; 
return $stat;
}

function readCSV_hrd($file,$separator=',',$comment='#')
{
#read CSV file with optional separator and commented line
#return an array
$fp = fopen($file, "r");
while (!feof($fp)) {
$line = fgets($fp, 4096); // Read a line.
    if(!ereg("^".$comment,$line) AND $line!='')
	{
	 $baris[] = explode($separator, $line);
	}
  }
return $baris;
} 

function tanggalsystemstrip_hrd($_q)//from format dd-mm-YYYY
{
	if ($_q!='' && $_q!='00-00-0000'){
		$_retval=date('Y',strtotime($_q))."-".date('m',strtotime($_q))."-".date('d',strtotime($_q));
	}
	else {
		$_retval=substr($_q,6,4)."-".substr($_q,3,2)."-".substr($_q,0,2);
	}
 //$_retval=date('Ymd',strtotime($_q)); 
 return($_retval);//return 8 chardate format eg.20090804
}
?>