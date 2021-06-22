<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
echo "\r\n" . '<script language=javascript1.2 src=\'js/log_2posisiBarang.js\'></script>' . "\r\n" . '<script language="javascript" src="js/zMaster.js"></script>' . "\r\n" . '<!--<link rel=stylesheet type=text/css href="style/zTable.css">-->' . "\r\n" . '<script language=javascript src=\'js/iReport.js\'></script>' . "\r\n" . '<script language=javascript src=js/zTools.js></script>' . "\r\n\r\n";
$arr = '##nopo##unit';
OPEN_BOX('', '<b>Laporan Posisi Barang</b>');

$optPO=makeOption2(getQuery("po"),
    array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['all']),
    array("valuefield"=>'nopo',"captionfield"=> 'namasupplier' ),
    function ($option,$value,$caption){
        $ret = array("newvalue"=>"","newcaption"=>"");
        if($option=='init'){
            $ret["newvalue"]=$value;
            $ret["newcaption"]=$caption;
        }
        if($option=='noninit'){
            $ret["newvalue"]=$value;
            $ret["newcaption"]= $value ." - ".$caption;
        }
        return $ret;
    }
);
$optunit= makeOption2(getQuery("lokasitugas"),
    array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
    array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);
echo '<table>' . "\r\n" . '     <tr>' . "\r\n\t\r\n\t" . ' <td><fieldset><legend>' . $_SESSION['lang']['find'] . '</legend>';
echo ' ' . $_SESSION['lang']['nopo'] . '&nbsp;:&nbsp;'.
//'<input type=text id=nopo size=25 maxlength=30 disabled class=myinputtext>' . "\r\n\t\t\t" . '<img src=images/zoom.png title=\'' . $_SESSION['lang']['find'] . '\' id=tmblCariNoPo class=resicon onclick=cariNoPo(\'' . $_SESSION['lang']['find'] . '\',event)>' .
    '<select id="nopo" name="nopo" style="width:150px" onchange="resetOption(\'unit\')">  '.
    $optPO.'</select>'.createDialogBox("containerPO","nmPO","Cari NO PP","searchPO","findPO").
'</td><tr>'.

    '     <tr>' . "\r\n\t\r\n\t" . ' <td><fieldset><legend>' . $_SESSION['lang']['unit'] . '</legend>'.

//'<input type=text id=nopo size=25 maxlength=30 disabled class=myinputtext>' . "\r\n\t\t\t" . '<img src=images/zoom.png title=\'' . $_SESSION['lang']['find'] . '\' id=tmblCariNoPo class=resicon onclick=cariNoPo(\'' . $_SESSION['lang']['find'] . '\',event)>' .
    '<select id=unit style=width:150px; onchange="resetOption(\'nopo\')">'.$optunit.'</select>'.
    '</td><tr>'.

    '<tr><td><button onclick=iPreview(\'log_slave_2posisiBarang\',\'' . $arr . '\',\'printContainer\') class=mybutton name=preview id=preview>' . $_SESSION['lang']['preview'] . '</button>' . "\r\n\t\t" . '<button onclick=iExcel(event,\'log_slave_2posisiBarang.php\',\'' . $arr . '\') class=mybutton name=preview id=preview>' . $_SESSION['lang']['excel'] . '</button>' . "\r\n\t\t" . '<button onclick=batal() class=mybutton name=btnBatal id=btnBatal>' . $_SESSION['lang']['cancel'] . '</button>'.
'</fieldset></td>  </tr>  </table> ';
CLOSE_BOX();
OPEN_BOX();
echo "\r\n" . '<fieldset style=\'clear:both\'><legend><b>' . $_SESSION['lang']['printArea'] . '</b></legend>' . "\r\n" . '<div id=\'printContainer\'  >' . "\r\n" . '</div></fieldset>';
CLOSE_BOX();
echo close_body();

?>
