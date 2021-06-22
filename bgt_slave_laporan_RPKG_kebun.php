<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$kodeorg = $_POST['kodeorg'];
$thnbudget = $_POST['thnbudget'];
$jenis = $_POST['jenis'];
$luas = 0;
$produksi = 0;
$str = 'select sum(kgsetahun) as produksi from ' . $dbname . '.bgt_produksi_kbn_kg_vw ' . "\r\n" . '      where tahunbudget=' . $thnbudget . ' and kodeunit=\'' . $kodeorg . '\'';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$produksi = $bar->produksi;
}

$luastm = 0;
$luastbm = 0;
$luasxtm = 0;
$luasxtbm = 0;
$luasbibitan = 0;
$luaslc = 0;
$str = 'select hathnini as luas,statusblok from ' . $dbname . '.bgt_blok ' . "\r\n" . '          where tahunbudget=' . $thnbudget . ' and kodeblok like \'' . $kodeorg . '%\'';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	if ($bar->statusblok == 'TM') {
		$luasxtm += $bar->luas;
	}
	else {
		$luasxtbm += $bar->luas;
	}
}

if ($jenis == 'LANGSUNG') {
	$str = 'select sum(hathnini) as luas,thntnm from ' . $dbname . '.bgt_blok where ' . "\r\n" . '      kodeblok like \'' . $kodeorg . '%\' and statusblok in (\'TBM\',\'TB\')';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$luastbm = $bar->luas;
	}

	$str = 'select sum(pokokthnini) as luas,thntnm from ' . $dbname . '.bgt_blok where ' . "\r\n" . '      kodeblok like \'' . $kodeorg . '%\' and statusblok =\'BBT\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$luaslpkk = $bar->luas;
	}

	$str = 'select sum(hathnini) as luas,thntnm from ' . $dbname . '.bgt_blok where ' . "\r\n" . '      kodeblok like \'' . $kodeorg . '%\' and statusblok=\'TM\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$luastm = $bar->luas;
	}

	$str = 'select sum(lcthnini) as luas,thntnm from ' . $dbname . '.bgt_blok where ' . "\r\n" . '      kodeblok like \'' . $kodeorg . '%\' and lcthnini!=\'\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$luaslc = $bar->luas;
	}
}
else if ($jenis != 'LANGSUNG') {
	$str = 'select sum(hathnini)+sum(hanonproduktif) as luas ,thntnm from ' . $dbname . '.bgt_blok where ' . "\r\n" . '        kodeblok like \'' . $kodeorg . '%\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$luastm = $bar->luas;
		$luastbm = $bar->luas;
	}

	$str = 'select sum(pokokthnini) as luas,thntnm from ' . $dbname . '.bgt_blok where ' . "\r\n" . '        kodeblok like \'' . $kodeorg . '%\' and statusblok =\'BBT\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$luaslpkk = $bar->luas;
	}

	$str = 'select sum(lcthnini) as luas,thntnm from ' . $dbname . '.bgt_blok where ' . "\r\n" . '        kodeblok like \'' . $kodeorg . '%\' and lcthnini!=\'\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$luaslc = $bar->luas;
	}
}

$adq = 'a.noakun, sum(a.rupiah) as rupiah,sum(a.rp01) as rp01,' . "\r\n" . '      sum(a.rp02) as rp02,sum(a.rp03) as rp03,' . "\r\n" . '      sum(a.rp04) as rp04,sum(a.rp05) as rp05,' . "\r\n" . '      sum(a.rp06) as rp06,sum(a.rp07) as rp07,' . "\r\n" . '      sum(a.rp08) as rp08,sum(a.rp09) as rp09,' . "\r\n" . '      sum(a.rp10) as rp10,sum(a.rp11) as rp11,' . "\r\n" . '      sum(a.rp12) as rp12';

if ($jenis == 'UMUM') {
	$str = 'select ' . $adq . ',b.namaakun as namaakun from ' . $dbname . '.bgt_budget_detail a left join' . "\r\n" . '      ' . $dbname . '.keu_5akun b on a.noakun=b.noakun' . "\r\n" . '      where a.kodebudget=\'UMUM\' and tahunbudget=' . $thnbudget . ' and a.kodeorg like \'' . $kodeorg . '%\'' . "\r\n" . '      and tipebudget=\'ESTATE\'    ' . "\r\n" . '      group by a.noakun';
}
else if ($jenis == 'LANGSUNG') {
	$str = 'select ' . $adq . ',b.namaakun as namaakun from ' . $dbname . '.bgt_budget_detail a left join' . "\r\n" . '      ' . $dbname . '.keu_5akun b on a.noakun=b.noakun' . "\r\n" . '      where a.kodebudget<>\'UMUM\' and tahunbudget=' . $thnbudget . ' and a.kodeorg like \'' . $kodeorg . '%\'' . "\r\n" . '      and tipebudget=\'ESTATE\'    ' . "\r\n" . '      group by a.noakun';
}
else {
	$str = 'select ' . $adq . ',b.namaakun as namaakun from ' . $dbname . '.bgt_budget_detail a left join' . "\r\n" . '      ' . $dbname . '.keu_5akun b on a.noakun=b.noakun' . "\r\n" . '      where  tahunbudget=' . $thnbudget . ' and a.kodeorg like \'' . $kodeorg . '%\'' . "\r\n" . '      and tipebudget=\'ESTATE\'    ' . "\r\n" . '      group by a.noakun';
}

$strx = 'select * from ' . $dbname . '.bgt_kapital_vw where tahunbudget=\'' . $thnbudget . '\' and' . "\r\n" . '       kodeunit like \'' . $kodeorg . '%\' order by namatipe';
$resx1 = mysql_query($strx);
echo '<fieldset><legend>' . $_SESSION['lang']['produksi'] . '</legend>' . "\r\n" . '     <table class=sortable cellspacing=1 border=0>' . "\r\n" . '     <thead>' . "\r\n" . '         <tr class=rowheader>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['luas'] . ' TM(Ha)</td>' . "\r\n" . '            <td align=center>' . $_SESSION['lang']['luas'] . ' TBM(Ha)</td>    ' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['totalkg'] . '(Kg)</td>' . "\r\n" . '           <td align=center>Ton/Ha</td>    ' . "\r\n" . '         </tr>' . "\r\n" . '     </thead>' . "\r\n" . '     <tbody>' . "\r\n" . '         <tr class=rowcontent>' . "\r\n" . '           <td align=right>' . number_format($luasxtm, 2, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($luasxtbm, 2, '.', ',') . '</td>   ' . "\r\n" . '           <td align=right>' . number_format($produksi, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . @number_format($produksi / 1000 / $luasxtm, 2, '.', ',') . '</td>    ' . "\r\n" . '         </tr>     ' . "\r\n" . '     </tbody>' . "\r\n" . '     <tfoot></tfoot>' . "\r\n" . '     </table>' . "\r\n" . '     </fieldset>';
echo '<fieldset><legend>' . $_SESSION['lang']['list'] . ': ' . $jenis . "\r\n" . '    Result:' . "\r\n" . '    <span id="printPanel">' . "\r\n" . '    <img onclick="fisikKeExcelRPKG(event,\'bgt_laporan_RPKG_kebun_excel.php\')" src="images/excel.jpg" class="resicon" title="MS.Excel"> ' . "\r\n" . '     <img onclick="fisikKePDFRPKG(event,\'bgt_laporan_RPKG_kebun_pdf.php\')" title="PDF" class="resicon" src="images/pdf.jpg">' . "\r\n" . '    </span>' . "\r\n" . '    </legend>' . "\r\n" . '     Unit:' . $kodeorg . ' Tahun Budget:' . $thnbudget . "\r\n" . '     <table class=sortable cellspacing=1 border=0 style=\'width:1600px;\'>' . "\r\n" . '     <thead>' . "\r\n" . '         <tr class=rowheader>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['nourut'] . '</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['noakun'] . '</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['namaakun'] . '</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['luas'] . '</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['jumlahrp'] . '</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['rpperkg'] . '</td>      ' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['rpperha'] . '</td>  ' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['jan'] . '<br>(Rp)</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['peb'] . '<br>(Rp)</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['mar'] . '<br>(Rp)</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['apr'] . '<br>(Rp)</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['mei'] . '<br>(Rp)</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['jun'] . '<br>(Rp)</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['jul'] . '<br>(Rp)</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['agt'] . '<br>(Rp)</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['sep'] . '<br>(Rp)</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['okt'] . '<br>(Rp)</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['nov'] . '<br>(Rp)</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['dec'] . '<br>(Rp)</td>' . "\r\n" . '         </tr>' . "\r\n" . '         </thead>' . "\r\n" . '         <tbody>';
$res = mysql_query($str);
$res1 = mysql_query($str);
$res2 = mysql_query($str);
$res3 = mysql_query($str);
$nobibitan = 1;
$rpperha = 0;
$ttrp = 0;

while ($bar = mysql_fetch_object($res3)) {
	@$rpperkg = 0;
	@$rpperha = 0;

	if (substr($bar->noakun, 0, 3) == '128') {
		@$rpperha = $bar->rupiah / $luasbibitan;
		@$rpperhabbt += $bar->rupiah / $luasbibitan;
		@$rpperkgtbm = 0;
		$tt01tbm[bbt] += 1;
		$tt02tbm[bbt] += 2;
		$tt03tbm[bbt] += 3;
		$tt04tbm[bbt] += 4;
		$tt05tbm[bbt] += 5;
		$tt06tbm[bbt] += 6;
		$tt07tbm[bbt] += 7;
		$tt08tbm[bbt] += 0;
		$tt09tbm[bbt] += 0;
		$tt10tbm[bbt] += 10;
		$tt11tbm[bbt] += 11;
		$tt12tbm[bbt] += 12;
		$ttrptbm[bbt] += total;
		echo '<tr class=rowcontent>' . "\r\n" . '           <td>' . $nobibitan . '</td>' . "\r\n" . '           <td>' . $bar->noakun . '</td>' . "\r\n" . '           <td>' . $bar->namaakun . '</td>' . "\r\n" . '           <td align=right>' . number_format($luasbibitan, 2, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rupiah, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format(0, 2, '.', ',') . '</td>     ' . "\r\n" . '           <td align=right>' . number_format($rpperha, 0, '.', ',') . '</td>    ' . "\r\n" . '           <td align=right>' . number_format($bar->rp01, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp02, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp03, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp04, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp05, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp06, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp07, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp08, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp09, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp10, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp11, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp12, 0, '.', ',') . '</td>' . "\r\n" . '         </tr>';
		$gttperha += 0;
		$gttperkg += 0;
		$gtt01 += $tt01tbm[bbt][1];
		$gtt02 += $tt02tbm[bbt][2];
		$gtt03 += $tt03tbm[bbt][3];
		$gtt04 += $tt04tbm[bbt][4];
		$gtt05 += $tt05tbm[bbt][5];
		$gtt06 += $tt06tbm[bbt][6];
		$gtt07 += $tt07tbm[bbt][7];
		$gtt08 += $tt08tbm[bbt][0];
		$gtt09 += $tt09tbm[bbt][0];
		$gtt10 += $tt10tbm[bbt][10];
		$gtt11 += $tt11tbm[bbt][11];
		$gtt12 += $tt12tbm[bbt][12];
		$gtt += $bar->rupiah;
		$nobibitan += 1;
	}
}

echo '<tr class=rowheader>' . "\r\n" . '           <td colspan=4 align=right>Total Biaya BBT</td>' . "\r\n" . '           <td align=right>' . number_format($ttrptbm[bbt][total], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . @number_format(0, 0, '.', ',') . '</td>     ' . "\r\n" . '           <td align=right>' . number_format($rpperhabbt, 0, '.', ',') . '</td>   ' . "\r\n" . '           <td align=right>' . number_format($tt01tbm[bbt][1], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt02tbm[bbt][2], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt03tbm[bbt][3], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt04tbm[bbt][4], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt05tbm[bbt][5], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt06tbm[bbt][6], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt07tbm[bbt][7], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt08tbm[bbt][0], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt09tbm[bbt][0], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt10tbm[bbt][10], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt11tbm[bbt][11], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt12tbm[bbt][12], 0, '.', ',') . '</td>' . "\r\n" . '         </tr>';

while ($bar = mysql_fetch_object($res)) {
	@$rpperkg = 0;
	@$rpperha = 0;

	if (substr($bar->noakun, 0, 3) == '126') {
		if (substr($bar->noakun, 0, 5) == '12601') {
			$luasxtbm2 = $luaslc;
			@$rpperha = $bar->rupiah / $luasxtbm2;
			@$rpperhatbm += $bar->rupiah / $luasxtbm2;
		}
		else if ('12602' <= substr($bar->noakun, 0, 5)) {
			$luasxtbm2 = $luasxtbm;
			@$rpperha = $bar->rupiah / $luasxtbm2;
			@$rpperhatbm += $bar->rupiah / $luasxtbm2;
		}

		@$rpperkgtbm = 0;
		$tt01tbm[tbm] += 1;
		$tt02tbm[tbm] += 2;
		$tt03tbm[tbm] += 3;
		$tt04tbm[tbm] += 4;
		$tt05tbm[tbm] += 5;
		$tt06tbm[tbm] += 6;
		$tt07tbm[tbm] += 7;
		$tt08tbm[tbm] += 0;
		$tt09tbm[tbm] += 0;
		$tt10tbm[tbm] += 10;
		$tt11tbm[tbm] += 11;
		$tt12tbm[tbm] += 12;
		$ttrptbm[tbm] += total;
		echo '<tr class=rowcontent>' . "\r\n" . '           <td>' . $notbm . '</td>' . "\r\n" . '           <td>' . $bar->noakun . '</td>' . "\r\n" . '           <td>' . $bar->namaakun . '</td>' . "\r\n" . '           <td align=right>' . number_format($luasxtbm2, 2, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rupiah, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($rpperkg, 2, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($rpperha, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp01, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp02, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp03, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp04, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp05, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp06, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp07, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp08, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp09, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp10, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp11, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp12, 0, '.', ',') . '</td>' . "\r\n" . '         </tr>';
		$gtt01 += $tt01tbm[tbm][1];
		$gtt02 += $tt02tbm[tbm][2];
		$gtt03 += $tt03tbm[tbm][3];
		$gtt04 += $tt04tbm[tbm][4];
		$gtt05 += $tt05tbm[tbm][5];
		$gtt06 += $tt06tbm[tbm][6];
		$gtt07 += $tt07tbm[tbm][7];
		$gtt08 += $tt08tbm[tbm][0];
		$gtt09 += $tt09tbm[tbm][0];
		$gtt10 += $tt10tbm[tbm][10];
		$gtt11 += $tt11tbm[tbm][11];
		$gtt12 += $tt12tbm[tbm][12];
		$gtt += $bar->rupiah;
		$notbm += 1;
	}
}

echo '<tr class=rowheader>' . "\r\n" . '           <td colspan=4 align=right>Total Biaya TBM</td>' . "\r\n" . '           <td align=right>' . number_format($ttrptbm[tbm][total], 2, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . @number_format(0, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . @number_format($rpperhatbm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt01tbm[tbm][1], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt02tbm[tbm][2], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt03tbm[tbm][3], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt04tbm[tbm][4], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt05tbm[tbm][5], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt06tbm[tbm][6], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt07tbm[tbm][7], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt08tbm[tbm][0], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt09tbm[tbm][0], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt10tbm[tbm][10], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt11tbm[tbm][11], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt12tbm[tbm][12], 0, '.', ',') . '</td>' . "\r\n" . '         </tr>';
$notm = 1;

while ($bar = mysql_fetch_object($res1)) {
	@$rpperkg = 0;
	@$rpperha = 0;

	if (substr($bar->noakun, 0, 1) == '6') {
		@$rpperkg = $bar->rupiah / $produksi;
		@$rpperha = $bar->rupiah / $luasxtm;
		@$rpperhatm += $bar->rupiah / $luasxtm;
		@$rpperkgtm += $bar->rupiah / $produksi;
		$tt01tbm[tm] += 1;
		$tt02tbm[tm] += 2;
		$tt03tbm[tm] += 3;
		$tt04tbm[tm] += 4;
		$tt05tbm[tm] += 5;
		$tt06tbm[tm] += 6;
		$tt07tbm[tm] += 7;
		$tt08tbm[tm] += 0;
		$tt09tbm[tm] += 0;
		$tt10tbm[tm] += 10;
		$tt11tbm[tm] += 11;
		$tt12tbm[tm] += 12;
		$ttrptbm[tm] += total;
		echo '<tr class=rowcontent>' . "\r\n" . '           <td>' . $notm . '</td>' . "\r\n" . '           <td>' . $bar->noakun . '</td>' . "\r\n" . '           <td>' . $bar->namaakun . '</td>' . "\r\n" . '           <td align=right>' . number_format($luasxtm, 2, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rupiah, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($rpperkg, 2, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($rpperha, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp01, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp02, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp03, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp04, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp05, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp06, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp07, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp08, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp09, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp10, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp11, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp12, 0, '.', ',') . '</td>' . "\r\n" . '         </tr>';
		$gtt01 += $tt01tbm[tm][1];
		$gtt02 += $tt02tbm[tm][2];
		$gtt03 += $tt03tbm[tm][3];
		$gtt04 += $tt04tbm[tm][4];
		$gtt05 += $tt05tbm[tm][5];
		$gtt06 += $tt06tbm[tm][6];
		$gtt07 += $tt07tbm[tm][7];
		$gtt08 += $tt08tbm[tm][0];
		$gtt09 += $tt09tbm[tm][0];
		$gtt10 += $tt10tbm[tm][10];
		$gtt11 += $tt11tbm[tm][11];
		$gtt12 += $tt12tbm[tm][12];
		$gtt += $bar->rupiah;
		$notm += 1;
	}
}

echo '<tr class=rowheader>' . "\r\n" . '           <td colspan=4 align=right>Total Biaya TM</td>' . "\r\n" . '           <td align=right>' . number_format($ttrptbm[tm][total], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . @number_format($rpperkgtm, 2, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . @number_format($rpperhatm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt01tbm[tm][1], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt02tbm[tm][2], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt03tbm[tm][3], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt04tbm[tm][4], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt05tbm[tm][5], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt06tbm[tm][6], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt07tbm[tm][7], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt08tbm[tm][0], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt09tbm[tm][0], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt10tbm[tm][10], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt11tbm[tm][11], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt12tbm[tm][12], 0, '.', ',') . '</td>' . "\r\n" . '         </tr>';
$noumum = 1;

while ($bar = mysql_fetch_object($res2)) {
	@$rpperkg = 0;
	@$rpperha = 0;

	if ('6' < substr($bar->noakun, 0, 1)) {
		@$rpperkg = $bar->rupiah / $produksi;
		@$rpperha = $bar->rupiah / ($luasxtbm + $luasxtm);
		@$rpperhaum += $bar->rupiah / ($luasxtbm + $luasxtm);
		$rpperkgum += $rpperkg;
		$tt01tbm[um] += 1;
		$tt02tbm[um] += 2;
		$tt03tbm[um] += 3;
		$tt04tbm[um] += 4;
		$tt05tbm[um] += 5;
		$tt06tbm[um] += 6;
		$tt07tbm[um] += 7;
		$tt08tbm[um] += 0;
		$tt09tbm[um] += 0;
		$tt10tbm[um] += 10;
		$tt11tbm[um] += 11;
		$tt12tbm[um] += 12;
		$ttrptbm[um] += total;
		echo '<tr class=rowcontent>' . "\r\n" . '           <td>' . $noumum . '</td>' . "\r\n" . '           <td>' . $bar->noakun . '</td>' . "\r\n" . '           <td>' . $bar->namaakun . '</td>' . "\r\n" . '           <td align=right>' . number_format($luasxtbm + $luasxtm, 2, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rupiah, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($rpperkg, 2, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($rpperha, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp01, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp02, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp03, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp04, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp05, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp06, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp07, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp08, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp09, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp10, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp11, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp12, 0, '.', ',') . '</td>' . "\r\n" . '         </tr>';
		$gtt01 += $tt01tbm[um][1];
		$gtt02 += $tt02tbm[um][2];
		$gtt03 += $tt03tbm[um][3];
		$gtt04 += $tt04tbm[um][4];
		$gtt05 += $tt05tbm[um][5];
		$gtt06 += $tt06tbm[um][6];
		$gtt07 += $tt07tbm[um][7];
		$gtt08 += $tt08tbm[um][0];
		$gtt09 += $tt09tbm[um][0];
		$gtt10 += $tt10tbm[um][10];
		$gtt11 += $tt11tbm[um][11];
		$gtt12 += $tt12tbm[um][12];
		$gtt += $bar->rupiah;
		$noumum += 1;
	}
}

echo '<tr class=rowheader>' . "\r\n" . '           <td colspan=4 align=right>Total Biaya UMUM</td>' . "\r\n" . '           <td align=right>' . number_format($ttrptbm[um][total], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . @number_format($rpperkgum, 2, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . @number_format($rpperhaum, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt01tbm[um][1], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt02tbm[um][2], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt03tbm[um][3], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt04tbm[um][4], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt05tbm[um][5], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt06tbm[um][6], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt07tbm[um][7], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt08tbm[um][0], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt09tbm[um][0], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt10tbm[um][10], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt11tbm[um][11], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt12tbm[um][12], 0, '.', ',') . '</td>' . "\r\n" . '         </tr>';
$nokapital = 1;

while ($bar = mysql_fetch_object($resx1)) {
	@$rpperkg = 0;
	@$rpperha = 0;
	@$rpperkg = $bar->harga / $produksi;
	@$rpperha = $bar->harga / ($luasxtbm + $luasxtm);
	@$rpperhakap += $bar->harga / ($luasxtbm + $luasxtm);
	$rpperkgkap += $rpperkg;
	$tt01tbm[kp] += 1;
	$tt02tbm[kp] += 2;
	$tt03tbm[kp] += 3;
	$tt04tbm[kp] += 4;
	$tt05tbm[kp] += 5;
	$tt06tbm[kp] += 6;
	$tt07tbm[kp] += 7;
	$tt08tbm[kp] += 0;
	$tt09tbm[kp] += 0;
	$tt10tbm[kp] += 10;
	$tt11tbm[kp] += 11;
	$tt12tbm[kp] += 12;
	$ttrptbm[kp] += total;
	echo '<tr class=rowcontent>' . "\r\n" . '           <td>' . $nokapital . '</td>' . "\r\n" . '           <td>' . $bar->noakun . '</td>' . "\r\n" . '           <td>' . $bar->namatipe . '</td>' . "\r\n" . '           <td align=right>' . number_format($luasxtbm + $luasxtm, 2, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->harga, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($rpperkg, 2, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($rpperha, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->k01, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->k02, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->k03, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->k04, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->k05, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->k06, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->k07, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->k08, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->k09, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->k10, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->k11, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->k12, 0, '.', ',') . '</td>' . "\r\n" . '         </tr>';
	$gttperha += $rpperha;
	$gttperkg += $rpperkg;
	$gtt01 += $tt01tbm[kp][1];
	$gtt02 += $tt02tbm[kp][2];
	$gtt03 += $tt03tbm[kp][3];
	$gtt04 += $tt04tbm[kp][4];
	$gtt05 += $tt05tbm[kp][5];
	$gtt06 += $tt06tbm[kp][6];
	$gtt07 += $tt07tbm[kp][7];
	$gtt08 += $tt08tbm[kp][0];
	$gtt09 += $tt09tbm[kp][0];
	$gtt10 += $tt10tbm[kp][10];
	$gtt11 += $tt11tbm[kp][11];
	$gtt12 += $tt12tbm[kp][12];
	$gtt += $bar->harga;
	$nokapital += 1;
}

echo '<tr class=rowheader>' . "\r\n" . '           <td colspan=4 align=right>Total KAPITAL</td>' . "\r\n" . '           <td align=right>' . number_format($ttrptbm[kp][total], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . @number_format($rpperkgkap, 2, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . @number_format($rpperhakap, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt01tbm[kp][1], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt02tbm[kp][2], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt03tbm[kp][3], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt04tbm[kp][4], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt05tbm[kp][5], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt06tbm[kp][6], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt07tbm[kp][7], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt08tbm[kp][0], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt09tbm[kp][0], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt10tbm[kp][10], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt11tbm[kp][11], 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt12tbm[kp][12], 0, '.', ',') . '</td>' . "\r\n" . '         </tr>';
$gttperkg = $gtt / $produksi;
$gttperha = $gtt / ($luasxtbm + $luasxtm);
echo '<tr class=rowheader>' . "\r\n" . '           <td colspan=4 align=right>GRAND TOTAL</td>' . "\r\n" . '           <td align=right>' . number_format($gtt, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . @number_format($gttperkg, 2, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . @number_format($gttperha, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($gtt01, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($gtt02, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($gtt03, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($gtt04, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($gtt05, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($gtt06, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($gtt07, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($gtt08, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($gtt09, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($gtt10, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($gtt11, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($gtt12, 0, '.', ',') . '</td>' . "\r\n" . '         </tr>';
echo "\t" . ' ' . "\r\n\t\t" . ' </tbody>' . "\r\n\t\t" . ' <tfoot>' . "\r\n\t\t" . ' </tfoot>' . "\r\n\t\t" . ' </table></fieldset>';

?>
