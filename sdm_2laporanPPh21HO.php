<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/devLibrary.php';
echo open_body();
echo "<script language=javascript1.2 src=js/sdm_payrollHO.js></script>\r\n
<script language=javascript1.2 >
function generatepdf(periode,kodeorg){	
	if( periode == ''){
		alert('Pilih periode');
		return false;
	}
	if( kodeorg == ''){
		alert('Pilih kodeorg');
		return false;
	}
	location.href='generate_csv_pajak.php?periodegaji='+periode+'&kodeorg='+kodeorg;
}
</script>
<link rel=stylesheet type=text/css href=style/payroll.css>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>PPh21 REPORT</b>');
echo '<div id=EList>';
echo OPEN_THEME('PPh21 Report Form:');
$optp = "<option value='".date('Y-m')."'>".date('m-Y').'</option>';
for ($x = -1; $x <= 24; ++$x) {
    $d = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));
    $optp .= "<option value='".date('Y-m', $d)."'>".date('m-Y', $d).'</option>';
}

$optUnit= makeOption2(getQuery("lokasitugas"),
	array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['pilihdata']),
	array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);



$hfrm[0] = 'PPh21 Bulanan';
$hfrm[1] = 'PPh21 Tahunan';
$hfrm[2] = 'Option';
$frm[0] = "PPh21 Bulanan: Periode\r\n         <select id=bulanan>".$optp."</select> <select id=kodeorg1 style='width:150px;\' >" . $optUnit . "</select> <button onclick=showPPh21Monthly() class=mybutton>Show</button>\r\n\t\t <img src=images/excel.jpg height=17px style='cursor:pointer;' onclick=convertPPh21Excel('bulan') title='Convert to Ms.Excel'> <a href=\"javascript:void generatepdf(document.getElementById('bulanan').value,document.getElementById('kodeorg1').value);\">[Generate CSV] </a>\r\n\t\t <div style='display:none;'>\r\n\t\t <iframe id=ifrm></iframe>\r\n\t\t </div>\t         \r\n\t\t <table class=sortable border=0 cellspacing=1 width=100%>\r\n\t\t <thead>\r\n\t\t   <tr class=rowheader>\r\n\t\t    <td class=firsttd>No.</td>\r\n\t\t\t<td>No.Karyawan</td>\r\n\t\t\t<td>Nama.Karyawan</td>\r\n\t\t\t<td>Status</td>\r\n\t\t\t<td>N.P.W.P</td>\r\n\t\t\t<td>Periode</td>\r\n\t\t\t<td>Sumber</td>\r\n\t\t\t<td>PPh21</td>\r\n\t\t   </tr>\r\n\t\t </thead><tbody id=tbody>\r\n\t\t";
$frm[0] .= "</tbody>\r\n          <tfoot>\r\n\t\t  <tr><td colspan=8>Jika Status pajak tidak sesuai atau kosong maka akan dikenakan status K/3.\r\n\t\t  </tr>\r\n\t\t  </tfoot>\r\n\t\t  </table>";
$frm[1] = "PPh21 Tahunan:\r\n        Tahun<select id=tahun>\r\n\t\t      <option value='".(date('Y') - 0)."'>".(date('Y') - 0)."</option>\r\n\t\t      <option value='".(date('Y') + 1)."'>".(date('Y') + 1)."</option>\r\n\t\t\t  <option value='".(date('Y') - 1)."'>".(date('Y') - 1)."</option>\r\n\t\t\t  <option value='".(date('Y') - 2)."'>".(date('Y') - 2)."</option>\r\n\t\t\t  <option value='".(date('Y') - 3)."'>".(date('Y') - 3)."</option>\r\n\t\t\t  <option value='".(date('Y') - 4)."'>".(date('Y') - 4)."</option>\r\n\t\t\t  <option value='".(date('Y') - 5)."'>".(date('Y') - 5)."</option>\r\n\t\t     </select> <select id=kodeorg2 style='width:150px;\' >" . $optUnit . "</select> <button onclick=showPPh21Yearly() class=mybutton>Show</button>\r\n\t\t <img src=images/excel.jpg height=17px style='cursor:pointer;' onclick=convertPPh21Excel('tahun') title='Convert to Ms.Excel'> \r\n\t\t <div style='display:none;'>\r\n\t\t <iframe id=ifrm1></iframe>\r\n\t\t </div>\t         \r\n\t\t <table class=sortable border=0 cellspacing=1 width=100%>\r\n\t\t <thead>\r\n\t\t   <tr class=rowheader>\r\n\t\t    <td class=firsttd>No.</td>\r\n\t\t\t<td>No.Karyawan</td>\r\n\t\t\t<td>Nama.Karyawan</td>\r\n\t\t\t<td>Status</td>\r\n\t\t\t<td>N.P.W.P</td>\r\n\t\t\t<td>Tahun</td>\r\n\t\t\t<td>Sumber</td>\r\n\t\t\t<td>PPh21</td>\r\n\t\t   </tr>\r\n\t\t </thead><tbody id=tbodyYear>\r\n\t\t";
$frm[1] .= "</tbody>\r\n          <tfoot>\r\n\t\t  <tr><td colspan=8>Jika Status pajak tidak sesuai atau kosong maka akan dikenakan status K/3.\r\n\t\t  </tr>\t\t  \r\n\t\t  </tfoot>\r\n\t\t  </table>";
$frm[2] = "\r\n         <fieldset><legend><b>Sertakan Jamsostek tanggungan perusahaan ?</b></legend>\r\n         <input type=checkbox id=jmsperusahaan value=jmsperusahaan checked>\r\n\t\t (Ya/Tidak) Jamsostek tanggungan perusahaan<br>\r\n\t\t </fieldset>\r\n         <fieldset><legend><b>Jenis pendapatan yang disertakan</b></legend>\r\n\t\t Berlaku pada PPh21 tahunan(Tidak berlaku pada PPh21 Bulanan)<br>\r\n         <input type=checkbox id=regular value=regular checked>Gaji Regular<br>\r\n\t\t <input type=checkbox id=thr value=thr checked>Tunjangan Hari Raya (THR)<br>\r\n\t\t <input type=checkbox id=jaspro value=jaspro checked>Jasa produksi (Bonoes)<br>\r\n\t\t </fieldset> \r\n        ";
drawTab('FRM', $hfrm, $frm, 150, 800);
echo '</div>';
echo CLOSE_THEME();
CLOSE_BOX();
echo close_body();

?>