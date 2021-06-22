<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zFunction.php';
echo open_body();
include 'master_mainMenu.php';
echo '<script language="javascript">' . "\r\n" . '    function proseskang()' . "\r\n" . '{' . "\r\n\t" . 'tahunbudget=document.getElementById(\'thnbudget\');' . "\r\n" . '        tahunbudget=tahunbudget.options[tahunbudget.selectedIndex].value;' . "\r\n" . '        ' . "\r\n\t" . 'kodebudget=document.getElementById(\'kodebudget\');' . "\r\n" . '        kodebudget=kodebudget.options[kodebudget.selectedIndex].value;' . "\r\n" . '        ' . "\r\n\t" . 'kodeorg=document.getElementById(\'kodeorg\');' . "\r\n" . '        kodeorg=kodeorg.options[kodeorg.selectedIndex].value;' . "\r\n" . '        ' . "\r\n\t" . 'param=\'kodeorg=\'+kodeorg+\'&kodebudget=\'+kodebudget+\'&tahunbudget=\'+tahunbudget;' . "\r\n\t" . '//alert(param);' . "\r\n\t" . 'tujuan=\'bgt_slave_save_budget_nol.php\';' . "\r\n" . '        if(confirm(\'Are you sure..?\')){' . "\r\n" . '            post_response_text(tujuan, param, respog);' . "\r\n" . '        }' . "\r\n" . '            function respog(){' . "\r\n" . '                if (con.readyState == 4) {' . "\r\n" . '                    if (con.status == 200) {' . "\r\n" . '                        busy_off();' . "\r\n" . '                        if (!isSaveResponse(con.responseText)) {' . "\r\n" . '                                alert(\'ERROR TRANSACTION,\\n\' + con.responseText);' . "\r\n" . '                        }' . "\r\n" . '                        else {' . "\r\n" . '                          alert(\'Done\');' . "\t\r\n" . '                        }' . "\r\n" . '                    }' . "\r\n" . '                    else {' . "\r\n" . '                        busy_off();' . "\r\n" . '                        error_catch(con.status);' . "\r\n" . '                    }' . "\r\n" . '                }' . "\r\n" . '            }' . "\t\r\n\r\n" . '}' . "\r\n" . '</script>' . "\r\n";
$str = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where length(kodeorganisasi)=4 ' . "\r\n" . '      order by kodeorganisasi';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$opt_unit .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
}

$str = 'select tahunbudget from ' . $dbname . '.bgt_hk order by tahunbudget desc';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$opt_tahun .= '<option value=\'' . $bar->tahunbudget . '\'>' . $bar->tahunbudget . '</option>';
}

$str = 'select kodebudget,nama from ' . $dbname . '.bgt_kode order by nama';
$res = mysql_query($str);
$opt_kode = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$opt_kode .= '<option value=\'KAPITAL\'>[KAPITAL]-KAPITAL</option>';

while ($bar = mysql_fetch_object($res)) {
	$opt_kode .= '<option value=\'' . $bar->kodebudget . '\'>[' . $bar->kodebudget . ']-' . $bar->nama . '</option>';
}

OPEN_BOX('', '<b>BUDGET DISTRIBUTION:</b>');
echo '<fieldset style=\'width:500px;\'><legend>' . $_SESSION['lang']['form'] . '</legend>' . "\r\n" . '      <table>' . "\r\n" . '        <tr>' . "\r\n" . '          <td>' . $_SESSION['lang']['unit'] . '</td><td><select id=kodeorg>' . $opt_unit . '</select></td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '          <td>' . $_SESSION['lang']['tahunanggaran'] . '</td><td><select id=thnbudget>' . $opt_tahun . '</select></td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '          <td>' . $_SESSION['lang']['kodebudget'] . '</td><td><select id=kodebudget>' . $opt_kode . '</select></td>' . "\r\n" . '        </tr>' . "\r\n" . '      </table>' . "\r\n" . '      <button class=mybutton onclick=proseskang()>' . $_SESSION['lang']['proses'] . '</button>' . "\r\n" . '      <hr>';

if ($_SESSION['language'] == 'ID') {
	echo '      Note:Seluruh budget unit bersangkutan akan di sebar merata setiap bulan baik fisik maupun harga';
}
else {
	echo '      Note:The entire budget units concerned will be spread evenly each month both physical and price';
}

echo '     </fieldset>';
CLOSE_BOX();
echo close_body();

?>
