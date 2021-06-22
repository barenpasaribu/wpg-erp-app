<?php



if (isset($_SESSION['theme'])) {
    $theme = $_SESSION['theme'];
} else {
    $theme = 'skyblue';
}

function OPEN_BODY($title = 'ANTHESIS ERP')
{
    echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">\r\n<html>\r\n\t<head>\r\n\t\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\r\n\t\t<meta http-equiv='Cache-Control' CONTENT='no-cache'>\r\n\t\t<meta http-equiv='Pragma' CONTENT='no-cache'>\r\n\t\t<title>".$title.'</title>';
    echo "\r\n    <script language=JavaScript1.2 src=js/menueAgro2.js></script>\r\n    <script language=JavaScript1.2 src=js/calendar.js></script>\r\n    <script language=JavaScript1.2 src=js/drag.js></script>\r\n    <script language=JavaScript1.2 src=js/generic.js></script>\r\n    <script language=JavaScript1.2 src=js/eagrosort.js></script>\r\n    <!--link rel=stylesheet type=text/css href=style/menu.css-->\r\n\t<link rel=stylesheet type=text/css href=style/menueAgro2.css>\r\n\t<!--link rel=stylesheet type=text/css href=style/generic.css-->\r\n\t<link rel=stylesheet type=text/css href=style/genericGray.css>\r\n\t<link rel=stylesheet type=text/css href=style/calendarblue.css>\r\n\t<link rel=stylesheet type=text/css href=style/zTable.css>\r\n\r\n    </head>\r\n<body  style='margin:0px;margin-top:0px;background-color:#F8F8F8;' onload=verify()>\r\n\r\n<noscript>\r\n\t<span style='font-size:13px;font-family:arial;'>\r\n\t\t<span style='color:#dd3300'>Warning!</span>\r\n\t\t\t&nbsp&nbsp; QuickMenu may have been blocked by IE-SP2's active\r\n\t\t\tcontent option. This browser feature blocks JavaScript from running\r\n\t\t\tlocally on your computer.<br>\r\n\t\t\t<br>This warning will not display once the menu is on-line.\r\n\t\t\tTo enable the menu locally, click the yellow bar above, and select\r\n\t\t\t<span style='color:#0033dd;'>'Allow Blocked Content'\r\n\t\t</span>.\r\n\t<br><br>To permanently enable active content locally...\r\n\t\t<div style=padding:0px 0px 30px 10px;color:#0033dd;'>\r\n\t\t\t<br>1: Select 'Tools' --> 'Internet Options' from the IE menu.\r\n\t\t\t<br>2: Click the 'Advanced' tab.\r\n\t\t\t<br>3: Check the 2nd option under 'Security' in the tree\r\n\t\t\t(Allow active content to run in files on my computer.)\r\n\t\t</div>\r\n\t</span>\r\n</noscript>\r\n<div style='height:30px'>\r\n</div>\r\n\r\n";
}

function OPEN_BODY_BI($title = 'ANTHESIS ERP')
{
    echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">\r\n<html>\r\n\t<head>\r\n\t\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\r\n\t\t<meta http-equiv='Cache-Control' CONTENT='no-cache'>\r\n\t\t<meta http-equiv='Pragma' CONTENT='no-cache'>\r\n\t\t<title>".$title.'</title>';
    echo "\r\n    <script language=JavaScript1.2 src=js/menueAgro2.js></script>\r\n\t <script language=JavaScript1.2 src=js/calendar.js></script>\r\n    <script language=JavaScript1.2 src=js/drag.js></script>\r\n    <script language=JavaScript1.2 src=js/generic.js></script>\r\n    <script language=JavaScript1.2 src=js/eagrosort.js></script>\r\n\t<link rel=stylesheet type=text/css href=style/menueAgro2.css>\r\n\t<link rel=stylesheet type=text/css href=style/genericGray.css>\r\n\t<link rel=stylesheet type=text/css href=style/calendarblue.css>\r\n    <link rel=stylesheet type=text/css href=style/zTable.css>\r\n    </head>\r\n<body  style='margin-top:10px;margin-left:2px;margin-right:2px;background-color:#E8F4F4;' onload=verify()>\r\n<div id='progress' style='display:none;border:orange solid 1px;width:150px;position:fixed;right:20px;top:65px;color:#ff0000;font-family:Tahoma;font-size:13px;font-weight:bolder;text-align:center;background-color:#FFFFFF;z-index:10000;'>\r\nPlease wait.....! <br>\r\n<img src='images/progress.gif'>\r\n</div>\r\n<img id='smallOwl' src='images/logo.png' width='300px' style='position:absolute;top:20%;left:35%;z-index:-998'>\r\n<noscript>\r\n\t<span style='font-size:13px;font-family:arial;'>\r\n\t\t<span style='color:#dd3300'>Warning!</span>\r\n\t\t\t&nbsp&nbsp; QuickMenu may have been blocked by IE-SP2's active\r\n\t\t\tcontent option. This browser feature blocks JavaScript from running\r\n\t\t\tlocally on your computer.<br>\r\n\t\t\t<br>This warning will not display once the menu is on-line.\r\n\t\t\tTo enable the menu locally, click the yellow bar above, and select\r\n\t\t\t<span style='color:#0033dd;'>'Allow Blocked Content'\r\n\t\t</span>.\r\n\t<br><br>To permanently enable active content locally...\r\n\t\t<div style=padding:0px 0px 30px 10px;color:#0033dd;'>\r\n\t\t\t<br>1: Select 'Tools' --> 'Internet Options' from the IE menu.\r\n\t\t\t<br>2: Click the 'Advanced' tab.\r\n\t\t\t<br>3: Check the 2nd option under 'Security' in the tree\r\n\t\t\t(Allow active content to run in files on my computer.)\r\n\t\t</div>\r\n\t</span>\r\n</noscript>\r\n<img src='images/logo.png'  style='height:50px;position:absolute;right:0px;top:0px;;z-index:-998'>\r\n";
}

function CLOSE_BODY()
{
    echo '</body></html>';
}

function OPEN_BOX($style = '', $title = '', $id = '', $contentId = '')
{
    echo "<div  id='".$id."' style='width:100%;'><fieldset id=''><legend><span class=judul>&nbsp;".$title."</span></legend>\r\n\t\t<div  id='contentBox".$contentId."' style='overflow:auto;'>\r\n\t\t";
}

function OPEN_BOX2($style = '', $title = '', $id = '', $contentId = '')
{
    return "<div  id='".$id."' class=\"x-box-blue\" style='".$style."'><div class=\"x-box-tl\"><div class=\"x-box-tr\">\r\n\t\t<div class=\"x-box-tc\"></div></div></div><div class=\"x-box-ml\"><div class=\"x-box-mr\">\r\n\t\t<div class=\"x-box-mc\" id='contentBox".$contentId."' style='overflow:auto;'>\r\n\t\t".$title;
}

function CLOSE_BOX()
{
    echo "</div></fieldset>\r\n        </div>";
}

function CLOSE_BOX2()
{
    return "</div></div></div>\r\n        <div ><div ><div ></div></div></div>\r\n        </div>";
}

function drawTab($tabId, $arrHeader, $arrContent, $tabLength = '250', $contentLength = '1200')
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

function OPEN_THEME($caption = '', $width = '', $text_align = 'left')
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

function CLOSE_THEME()
{
    if (isset($_SESSION['theme'])) {
        $theme = $_SESSION['theme'];
    } else {
        $theme = 'skyblue';
    }

    $text = "</td>\r\n\t<td class='bodyright' style='background: url(images/".$theme."/a5x.gif);background-repeat:repeat-y;'></td>\r\n\t</tr>\r\n\r\n\t<tr class='trbottom' style='height:7px;'>\r\n\t<td class='bottomleft' style='background: url(images/".$theme."/a6xcvb.gif);background-repeat:no-repeat;'></td>\r\n\t<td class='bottom' style='background: url(images/".$theme."/a7xcv.gif);background-repeat:repeat-x;'></td>\r\n\t<td class='bottomright' style='background: url(images/".$theme."/a8zzc.gif);background-repeat:no-repeat;'></td></tr>\r\n\t</table>";

    return $text;
}

function tanggalnormal($_q)
{
    $_q = str_replace('-', '', $_q);

    return substr($_q, 6, 2).'-'.substr($_q, 4, 2).'-'.substr($_q, 0, 4);
}

function tanggalnormald($_q)
{
    $_q = str_replace('-', '', $_q);

    return substr($_q, 6, 2).'-'.substr($_q, 4, 2).'-'.substr($_q, 0, 4).' '.substr($_q, 9, 5);
}

function tanggalsystemw($_q)
{
    if ('00-00-0000' === $_q) {
        $_retval = '';
    } else {
        $_retval = substr($_q, 6, 4).substr($_q, 3, 2).substr($_q, 0, 2);
    }

    return $_retval;
}

function tanggalsystem($_q)
{
    if ('00-00-0000' === $_q || '--' === $_q) {
        $_retval = '';
    } else {
        $_retval = substr($_q, 6, 4).substr($_q, 3, 2).substr($_q, 0, 2);
    }

    return $_retval;
}

function tanggalsystemd($_q)
{
    return substr($_q, 6, 4).'-'.substr($_q, 3, 2).'-'.substr($_q, 0, 2).substr($_q, 10, 7).':00';
}

function tanggaldgnbar($_q)
{
    return substr($_q, 6, 4).'-'.substr($_q, 3, 2).'-'.substr($_q, 0, 2);
}

function hari($tgl, $lang = 'ID')
{
    $bln = substr($tgl, 5, 2);
    $thn = substr($tgl, 0, 4);
    $tgl = substr($tgl, 8, 2);
    $ha = date('w', mktime(0, 0, 0, $bln, $tgl, $thn));
    $x = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $y = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    if ('ID' === $lang) {
        return $x[$ha];
    }

    return $y[$ha];
}

function getUserEmail($karyawanid)
{
    global $dbname;
    global $conn;
    $strAv = 'select email from '.$dbname.'.datakaryawan where  karyawanid in('.$karyawanid.')';
    $resAv = mysql_query($strAv);
    $retMail = '';
    $no = 0;
    while ($barAv = mysql_fetch_object($resAv)) {
        $email = $barAv->email;
        if (1 < strpos($email, '@')) {
            if (0 === $no) {
                $retMail = $email;
            } else {
                $retMail .= ','.$email;
            }
        }

        ++$no;
    }

    return $retMail;
}

function getNamaKaryawan($karyawanid)
{
    global $dbname;
    global $conn;
    $strAv = 'select namakaryawan from '.$dbname.".datakaryawan\r\n\t        where  karyawanid in(".$karyawanid.')';
    $resAv = mysql_query($strAv);
    $retnama = '';
    $no = 0;
    while ($barAv = mysql_fetch_object($resAv)) {
        if (0 === $no) {
            $retnama = $barAv->namakaryawan;
        } else {
            $retnama .= ','.$barAv->namakaryawan;
        }

        ++$no;
    }

    return $retnama;
}

function getFieldName($TABLENAME, $output)
{
    global $dbname;
    global $conn;
    $option = '';
    $arrReturn = [];
    $strUx = 'select * from '.$dbname.'.'.$TABLENAME.' limit 1';
    $resUx = mysql_query($strUx);
    while ($PxUx = mysql_fetch_field($resUx)) {
        array_push($arrReturn, $PxUx->name);
        $option .= "<option value='".$PxUx->name."'>".$PxUx->name.'</option>';
    }
    if ('array' === $output) {
        return $arrReturn;
    }

    return $option;
}

function printTableController($TABLENAME)
{
    $field = getfieldname($TABLENAME, 'option');
    echo '<br>'.$_SESSION['lang']['find'].' <input type=text class=myinputtext id=txtcari onkeypress="return tanpa_kutip(event);" size=15 maxlength=20 onblur=checkThis(this) value=All> '.$_SESSION['lang']['oncolumn'].':<select id=field>'.$field."</select>\r\n    ".$_SESSION['lang']['order'].' <select id=order1>'.$field.'</select>,<select id=order2>'.$field."</select>\r\n\t ";
    echo "<button class=mybutton onclick=\"browseTable('".$TABLENAME."');\">".$_SESSION['lang']['find'].'</button>';
}

function printSearchOnTable($TABLENAME, $fieldname, $texttofind, $order = '', $curpage = 0, $MAX_ROW = 1000)
{
    global $dbname;
    global $conn;
    $offset = $curpage * $MAX_ROW;
    $disp_page = $curpage + 1;
    $field = getfieldname($TABLENAME, 'array');
    if ('' !== $texttofind) {
        $where = ' where '.$fieldname." like '%".$texttofind."%'";
    } else {
        $where = '';
    }

    $strXu = 'select * from '.$dbname.'.'.$TABLENAME.' '.$where.'  order by '.$order.' limit '.$offset.','.$MAX_ROW;
    $resXu = mysql_query($strXu);
    $strXur = 'select * from '.$dbname.'.'.$TABLENAME.' '.$where;
    $resXur = mysql_query($strXur);
    $numrows = mysql_num_rows($resXur);
    if ($MAX_ROW < $numrows) {
        if (0 !== $numrows % $MAX_ROW) {
            $page = floor($numrows / $MAX_ROW) + 1;
        } else {
            $page = $numrows / $MAX_ROW;
        }
    } else {
        $page = 1;
    }

    echo $_SESSION['lang']['page'].' '.$disp_page.' '.$_SESSION['lang']['of'].' '.$page.' (Max.'.$MAX_ROW.'/'.$_SESSION['lang']['page'].')';
    echo ' [ '.$_SESSION['lang']['gotopage'].':<select id=page>';
    for ($y = 0; $y < $page; ++$y) {
        echo '<option value='.$y.'>'.($y + 1).'</option>';
    }
    echo "</select> <button onclick=\"navigatepage('".$TABLENAME."');\" class=mybutton>Go</button> ]";
    echo "<table class=sortable cellspacing=1 border=0>\r\n\t     <thead><tr class=rowheader>";
    for ($x = 0; $x < count($field); ++$x) {
        echo '<td>'.$field[$x].'</td>';
    }
    echo '</tr></thead><tbody>';
    $num = 0;
    while ($barXu = mysql_fetch_array($resXu)) {
        echo '<tr class=rowcontent>';
        for ($x = 0; $x < count($field); ++$x) {
            echo '<td>'.$barXu[$x].'</td>';
        }
        echo '</tr>';
    }
    echo '</tbody><tfoot></tfoot></table>';
}

function sendMail($subject, $content, $from, $to, $cc = '', $bcc = '', $replyTo = '')
{
    $headers = 'MIME-Version: 1.0'."\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
    if ('' !== $from) {
        $headers .= 'From: '.$from."\r\n";
    }

    if ('' !== $cc) {
        $headers .= 'Cc: '.$cc."\r\n";
    }

    if ('' !== $bcc) {
        $headers .= 'Bcc: '.$bcc."\r\n";
    }

    if ('' !== $replyTo) {
        $headers .= 'Reply-To: '.$replyTo."\r\n";
    }

    if (mail($to, $subject, $content, $headers)) {
        return true;
    }

    return false;
}

function kirimEmailWindows($to, $subject, $body, $mailType = 'text/html')
{
    include 'PHPMailer/PHPMailerAutoload.php';
    $body = wordwrap($body, 70, "\r\n");
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'eagroplantationsystem@gmail.com';
    $mail->Password = 'eagro!007';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('eagroplantationsystem@gmail.com', 'eagroplantationsystem');
    $mail->addReplyTo('eagroplantationsystem@gmail.com', 'eagroplantationsystem');
    $toto = explode(',', $to);
    foreach ($toto as $key => $val) {
        $mail->addAddress($val);
    }
    $mail->addCC('');
    $mail->addBCC('');
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    if (!$mail->send()) {
        echo 'Message could not be sent.';
        $mail->SMTPDebug = 2;
    } else {
        echo 'Message has been sent';
    }
}

function readCountry($file)
{
    $comment = '#';
    $fp = fopen($file, 'r');
    $lin = -1;
    while (!feof($fp)) {
        $line = fgets($fp, 4096);
        if (!preg_match('/^#/D', $line) && '' !== $line) {
            ++$lin;
            $pieces = explode('=', $line);
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

function readTextFile($file)
{
    $handle = fopen($file, 'r');
    $contents = fread($handle, filesize($file));
    fclose($handle);

    return $contents;
}

function readOrgType($file)
{
    $comment = '#';
    $fp = fopen($file, 'r');
    $lin = 0;
    while (!feof($fp)) {
        $line = fgets($fp, 4096);
        if (!preg_match('/^#/D', $line) && '' !== $line) {
            ++$lin;
            $pieces = explode('=', $line);
            $code = trim($pieces[0]);
            $name = trim($pieces[1]);
            $orgtype[$lin][0] = $code;
            $orgtype[$lin][1] = $name;
        }
    }
    fclose($fp);

    return $country;
}

function numToMonth($int, $lang = 'E', $format = 'short')
{
    $arr = [];
    $arr[1]['E']['short'] = 'Jan';
    $arr[1]['I']['short'] = 'Jan';
    $arr[1]['E']['long'] = 'January';
    $arr[1]['I']['long'] = 'Januari';
    $arr[2]['E']['short'] = 'Feb';
    $arr[2]['I']['short'] = 'Peb';
    $arr[2]['E']['long'] = 'February';
    $arr[2]['I']['long'] = 'Februari';
    $arr[3]['E']['short'] = 'Mar';
    $arr[3]['I']['short'] = 'Mar';
    $arr[3]['E']['long'] = 'Maret';
    $arr[3]['I']['long'] = 'Maret';
    $arr[4]['E']['short'] = 'Apr';
    $arr[4]['I']['short'] = 'Apr';
    $arr[4]['E']['long'] = 'April';
    $arr[4]['I']['long'] = 'April';
    $arr[5]['E']['short'] = 'May';
    $arr[5]['I']['short'] = 'Mei';
    $arr[5]['E']['long'] = 'May';
    $arr[5]['I']['long'] = 'Mei';
    $arr[6]['E']['short'] = 'Jun';
    $arr[6]['I']['short'] = 'Jun';
    $arr[6]['E']['long'] = 'Juni';
    $arr[6]['I']['long'] = 'Juni';
    $arr[7]['E']['short'] = 'Jul';
    $arr[7]['I']['short'] = 'Jul';
    $arr[7]['E']['long'] = 'July';
    $arr[7]['I']['long'] = 'Juli';
    $arr[8]['E']['short'] = 'Aug';
    $arr[8]['I']['short'] = 'Agu';
    $arr[8]['E']['long'] = 'August';
    $arr[8]['I']['long'] = 'Agustus';
    $arr[9]['E']['short'] = 'Sep';
    $arr[9]['I']['short'] = 'Sep';
    $arr[9]['E']['long'] = 'September';
    $arr[9]['I']['long'] = 'September';
    $arr[10]['E']['short'] = 'Oct';
    $arr[10]['I']['short'] = 'Okt';
    $arr[10]['E']['long'] = 'October';
    $arr[10]['I']['long'] = 'Oktober';
    $arr[11]['E']['short'] = 'Nov';
    $arr[11]['I']['short'] = 'Nop';
    $arr[11]['E']['long'] = 'November';
    $arr[11]['I']['long'] = 'Nopember';
    $arr[12]['E']['short'] = 'Dec';
    $arr[12]['I']['short'] = 'Des';
    $arr[12]['E']['long'] = 'December';
    $arr[12]['I']['long'] = 'Desember';
    $int = (int) $int;

    return $arr[$int][$lang][$format];
}

function isTransactionPeriod()
{
    $stat = true;
    if ('' === $_SESSION['org']['period']['start']) {
        $stat = false;
    }

    if ('' === $_SESSION['org']['period']['end']) {
        $stat = false;
    }

    if ('' === $_SESSION['org']['period']['bulan']) {
        $stat = false;
    }

    if ('' === $_SESSION['org']['period']['tahun']) {
        $stat = false;
    }

    return $stat;
}

function readCSV($file, $separator = ',', $comment = '#')
{
    $fp = fopen($file, 'r');
    while (!feof($fp)) {
        $line = fgets($fp, 4096);
        if (!ereg('^'.$comment, $line) && '' !== $line) {
            $baris[] = explode($separator, $line);
        }
    }

    return $baris;
}

function nambahHari($tgld, $jmlhhari, $stat)
{
    $tgl = explode('-', $tgld);
    $tglck = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
    if (0 === $stat) {
        $hslTgl = strtotime('-'.$jmlhhari.' day', strtotime($tglck));
    } else {
        $hslTgl = strtotime('+'.$jmlhhari.' day', strtotime($tglck));
    }

    return date('Y-m-d', $hslTgl);
}

function hitungHrMinggu($bln1, $tgl1, $thn1, $date2, $hrLbr)
{
    global $dbname;
    global $conn;
    $i = 0;
    $sum = 0;
    if ('' === $hrLbr) {
        $hrLbr = 0;
    }

    do {
        $tanggal = date('d-m-Y', mktime(0, 0, 0, $bln1, $tgl1 + $i, $thn1));
        if (0 === date('w', mktime(0, 0, 0, $bln1, $tgl1 + $i, $thn1))) {
            ++$sum;
        }

        if (1 === $hrLbr) {
            $sLbr = 'select distinct * from '.$dbname.".sdm_5harilibur where\r\n                  tanggal='".tanggalsystem($tanggal)."' and regional='".$_SESSION['empl']['regional']."'";
            $qLbr = mysql_query($sLbr) || exit(mysql_error($conns));
            if (1 === mysql_num_rows($qLbr)) {
                ++$sum;
            }
        }

        ++$i;
    } while ($tanggal !== $date2);

    return $sum;
}

function arrHrLbr($bln1, $tgl1, $thn1, $date2, $hrLbr)
{
    global $dbname;
    global $conn;
    $i = 0;
    $sum = 0;
    if ('' === $hrLbr) {
        $hrLbr = 0;
    }

    do {
        $tanggal = date('d-m-Y', mktime(0, 0, 0, $bln1, $tgl1 + $i, $thn1));
        if (0 === date('w', mktime(0, 0, 0, $bln1, $tgl1 + $i, $thn1))) {
            $tglarr = date('Y-m-d', mktime(0, 0, 0, $bln1, $tgl1 + $i, $thn1));
            $arrTgl[$tglarr] = $tglarr;
        }

        if (1 === $hrLbr) {
            $sLbr = 'select distinct * from '.$dbname.".sdm_5harilibur where\r\n                  tanggal='".tanggalsystem($tanggal)."' and regional='".$_SESSION['empl']['regional']."'";
            $qLbr = mysql_query($sLbr) || exit(mysql_error($conns));
            if (1 === mysql_num_rows($qLbr)) {
                $rdt = mysql_fetch_assoc($qLbr);
                $arrTgl[$rdt['tanggal']] = $rdt['tanggal'];
            }
        }

        ++$i;
    } while ($tanggal !== $date2);

    return $arrTgl;
}

function rangeTanggal($date1, $date2)
{
    $day = 60 * 60 * 24;
    $date1 = strtotime($date1);
    $date2 = strtotime($date2);
    $days_diff = round(($date2 - $date1) / $day);
    $dates_array = [];
    $dates_array[] = date('Y-m-d', $date1);
    for ($x = 1; $x < $days_diff; ++$x) {
        $dates_array[] = date('Y-m-d', $date1 + $day * $x);
    }
    $dates_array[] = date('Y-m-d', $date2);
    if ($date1 === $date2) {
        $dates_array = [];
        $dates_array[] = date('Y-m-d', $date1);
    }

    return $dates_array;
}

?>