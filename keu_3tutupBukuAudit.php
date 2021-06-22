<?php



include_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
echo "<!-- Includes -->\r\n<script language=javascript1.2 src='js/zTools.js'></script>\r\n<script language=javascript1.2 src='js/keu_3tutupbulanAudit.js'></script>\r\n<link rel=stylesheet type=text/css href='style/zTable.css'>\r\n";
$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
$bulantahun = ($_SESSION['org']['period']['tahun'] - 1).'-12';
$bulantahun1 = $_SESSION['org']['period']['tahun'].'-12';
$optPeriod = [$bulantahun1 => $bulantahun1, $bulantahun => $bulantahun];
$els = [];
$els[] = [makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', '', ['style' => 'width:300px'], $optOrg)];
$els[] = [makeElement('periode', 'label', $_SESSION['lang']['periode']), makeElement('periode', 'select', '', ['style' => 'width:300px'], $optPeriod)];
$els['btn'] = [makeElement('btnList', 'button', $_SESSION['lang']['tutupbuku'], ['onclick' => 'tutupBuku()'])];
include 'master_mainMenu.php';
OPEN_BOX();
echo genElTitle('Recognition Ending Audit(base on the last revision):', $els);
if ('EN' === $_SESSION['language']) {
    echo "<fieldset style='width:500px';><legend>".$_SESSION['lang']['info']."</legend>\r\n              After doing this process, you will be in the period of January next year, if you've been in a period greater than January \r\n              of the following year, then you must conduct the process of closing the books until your transaction at this time period \r\n              (after doing the process), through Finance menu-> process-> Close Book month (No need to process the 'Monthly Process') .\r\n              </feldset>";
} else {
    echo "<fieldset style='width:500px';><legend>".$_SESSION['lang']['info']."</legend>\r\n              Setelah melakukan proses ini, anda akan berada pada periode bulan Januari tahun selanjutnya, \r\n              jika anda sudah berada pada periode lebih besar dari bulan Januari tahun berikut, makan anda wajib melakukan proses Tutup buku hingga periode\r\n              transaksi anda saat ini(setelah melakukan proses ini), melalui menu Keuangan->Proses->Tutup Buku Bulanan(Tidak perlu proses akhir bulan) .\r\n              </feldset>";
}

CLOSE_BOX();
close_body();

?>