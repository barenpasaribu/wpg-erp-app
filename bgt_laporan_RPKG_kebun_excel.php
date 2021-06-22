<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$kodeorg = $_GET['kodeorg'];
$thnbudget = $_GET['thnbudget'];
$jenis = $_GET['jenis'];
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

	$str = 'select sum(lcthnini) as luas,thntnm from ' . $dbname . '.bgt_blok where ' . "\r\n" . '      kodeblok like \'' . $kodeOrg . '%\' and lcthnini!=\'\'';
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
$stream .= '<fieldset><legend>' . $_SESSION['lang']['produksi'] . '</legend>' . "\r\n" . '     <table class=sortable cellspacing=1 border=1>' . "\r\n" . '     <thead>' . "\r\n" . '         <tr class=rowheader>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['luas'] . ' TM(Ha)</td>' . "\r\n" . '            <td align=center>' . $_SESSION['lang']['luas'] . ' TBM(Ha)</td>    ' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['totalkg'] . '(Kg)</td>' . "\r\n" . '           <td align=center>Ton/Ha</td>    ' . "\r\n" . '         </tr>' . "\r\n" . '     </thead>' . "\r\n" . '     <tbody>' . "\r\n" . '         <tr class=rowcontent>' . "\r\n" . '           <td align=right>' . number_format($luasxtm, 2, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($luasxtbm, 2, '.', ',') . '</td>   ' . "\r\n" . '           <td align=right>' . number_format($produksi, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . @number_format($produksi / 1000 / $luasxtm, 2, '.', ',') . '</td>    ' . "\r\n" . '         </tr>     ' . "\r\n" . '     </tbody>' . "\r\n" . '     <tfoot></tfoot>' . "\r\n" . '     </table>' . "\r\n" . '     </fieldset>';
$stream .= ' Unit:' . $kodeorg . ' Tahun Budget:' . $thnbudget . "\r\n" . '     <table class=sortable cellspacing=1 border=1 style=\'width:1600px;\'>' . "\r\n" . '     <thead>' . "\r\n" . '         <tr class=rowheader>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['nourut'] . '</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['noakun'] . '</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['namaakun'] . '</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['luas'] . '</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['jumlahrp'] . '</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['rpperkg'] . '</td>      ' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['rpperha'] . '</td>  ' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['jan'] . '<br>(Rp)</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['peb'] . '<br>(Rp)</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['mar'] . '<br>(Rp)</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['apr'] . '<br>(Rp)</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['mei'] . '<br>(Rp)</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['jun'] . '<br>(Rp)</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['jul'] . '<br>(Rp)</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['agt'] . '<br>(Rp)</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['sep'] . '<br>(Rp)</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['okt'] . '<br>(Rp)</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['nov'] . '<br>(Rp)</td>' . "\r\n" . '           <td align=center>' . $_SESSION['lang']['dec'] . '<br>(Rp)</td>' . "\r\n" . '         </tr>' . "\r\n" . '         </thead>' . "\r\n" . '         <tbody>';
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
		$tt01tbm += $bar->rp01;
		$tt02tbm += $bar->rp02;
		$tt03tbm += $bar->rp03;
		$tt04tbm += $bar->rp04;
		$tt05tbm += $bar->rp05;
		$tt06tbm += $bar->rp06;
		$tt07tbm += $bar->rp07;
		$tt08tbm += $bar->rp08;
		$tt09tbm += $bar->rp09;
		$tt10tbm += $bar->rp10;
		$tt11tbm += $bar->rp11;
		$tt12tbm += $bar->rp12;
		$ttrptbm += $bar->rupiah;
		$stream .= '<tr class=rowcontent>' . "\r\n" . '           <td>' . $nobibitan . '</td>' . "\r\n" . '           <td>' . $bar->noakun . '</td>' . "\r\n" . '           <td>' . $bar->namaakun . '</td>' . "\r\n" . '           <td align=right>' . number_format($luasbibitan, 2, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rupiah, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format(0, 2, '.', ',') . '</td>     ' . "\r\n" . '           <td align=right>' . number_format($rpperha, 0, '.', ',') . '</td>    ' . "\r\n" . '           <td align=right>' . number_format($bar->rp01, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp02, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp03, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp04, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp05, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp06, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp07, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp08, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp09, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp10, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp11, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp12, 0, '.', ',') . '</td>' . "\r\n" . '         </tr>';
		$gttperha += 0;
		$gttperkg += 0;
		$gtt01 += $tt01tbm;
		$gtt02 += $tt02tbm;
		$gtt03 += $tt03tbm;
		$gtt04 += $tt04tbm;
		$gtt05 += $tt05tbm;
		$gtt06 += $tt06tbm;
		$gtt07 += $tt07tbm;
		$gtt08 += $tt08tbm;
		$gtt09 += $tt09tbm;
		$gtt10 += $tt10tbm;
		$gtt11 += $tt11tbm;
		$gtt12 += $tt12tbm;
		$gtt += $bar->rupiah;
		$nobibitan += 1;
	}
}

$stream .= '<tr class=rowheader>' . "\r\n" . '           <td colspan=4 align=right>Total Biaya BBT</td>' . "\r\n" . '           <td align=right>' . number_format($ttrptbm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . @number_format(0, 0, '.', ',') . '</td>     ' . "\r\n" . '           <td align=right>' . number_format($rpperhabbt, 0, '.', ',') . '</td>   ' . "\r\n" . '           <td align=right>' . number_format($tt01tbm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt02tbm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt03tbm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt04tbm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt05tbm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt06tbm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt07tbm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt08tbm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt09tbm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt10tbm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt11tbm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt12tbm, 0, '.', ',') . '</td>' . "\r\n" . '         </tr>';
$notbm = 1;

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
		$tt01tbm += $bar->rp01;
		$tt02tbm += $bar->rp02;
		$tt03tbm += $bar->rp03;
		$tt04tbm += $bar->rp04;
		$tt05tbm += $bar->rp05;
		$tt06tbm += $bar->rp06;
		$tt07tbm += $bar->rp07;
		$tt08tbm += $bar->rp08;
		$tt09tbm += $bar->rp09;
		$tt10tbm += $bar->rp10;
		$tt11tbm += $bar->rp11;
		$tt12tbm += $bar->rp12;
		$ttrptbm += $bar->rupiah;
		$no += 1;
		$stream .= '<tr class=rowcontent>' . "\r\n" . '           <td>' . $notbm . '</td>' . "\r\n" . '           <td>' . $bar->noakun . '</td>' . "\r\n" . '           <td>' . $bar->namaakun . '</td>' . "\r\n" . '           <td align=right>' . number_format($luasxtbm2, 2, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rupiah, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($rpperkg, 2, '.', ',') . '</td>     ' . "\r\n" . '           <td align=right>' . number_format($rpperha, 0, '.', ',') . '</td>    ' . "\r\n" . '           <td align=right>' . number_format($bar->rp01, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp02, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp03, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp04, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp05, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp06, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp07, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp08, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp09, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp10, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp11, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp12, 0, '.', ',') . '</td>' . "\r\n" . '         </tr>';
		$gttperha += $rpperha;
		$gttperkg += 0;
		$gtt01 += $tt01tbm;
		$gtt02 += $tt02tbm;
		$gtt03 += $tt03tbm;
		$gtt04 += $tt04tbm;
		$gtt05 += $tt05tbm;
		$gtt06 += $tt06tbm;
		$gtt07 += $tt07tbm;
		$gtt08 += $tt08tbm;
		$gtt09 += $tt09tbm;
		$gtt10 += $tt10tbm;
		$gtt11 += $tt11tbm;
		$gtt12 += $tt12tbm;
		$gtt += $bar->rupiah;
		$notbm += 1;
	}
}

$stream .= '<tr class=rowheader>' . "\r\n" . '           <td colspan=4 align=right>Total Biaya TBM</td>' . "\r\n" . '           <td align=right>' . number_format($ttrptbm, 2, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . @number_format(0, 0, '.', ',') . '</td>     ' . "\r\n" . '           <td align=right>' . @number_format($rpperhatbm, 0, '.', ',') . '</td>    ' . "\r\n" . '           <td align=right>' . number_format($tt01tbm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt02tbm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt03tbm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt04tbm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt05tbm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt06tbm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt07tbm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt08tbm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt09tbm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt10tbm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt11tbm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt12tbm, 0, '.', ',') . '</td>' . "\r\n" . '         </tr>';
$notm = 1;

while ($bar = mysql_fetch_object($res1)) {
	@$rpperkg = 0;
	@$rpperha = 0;

	if (substr($bar->noakun, 0, 1) == '6') {
		@$rpperkg = $bar->rupiah / $produksi;
		@$rpperha = $bar->rupiah / $luasxtm;
		@$rpperhatm += $bar->rupiah / $luasxtm;
		@$rpperkgtm += $bar->rupiah / $produksi;
		$tt01tm += $bar->rp01;
		$tt02tm += $bar->rp02;
		$tt03tm += $bar->rp03;
		$tt04tm += $bar->rp04;
		$tt05tm += $bar->rp05;
		$tt06tm += $bar->rp06;
		$tt07tm += $bar->rp07;
		$tt08tm += $bar->rp08;
		$tt09tm += $bar->rp09;
		$tt10tm += $bar->rp10;
		$tt11tm += $bar->rp11;
		$tt12tm += $bar->rp12;
		$ttrptm += $bar->rupiah;
		$stream .= '<tr class=rowcontent>' . "\r\n" . '           <td>' . $notm . '</td>' . "\r\n" . '           <td>' . $bar->noakun . '</td>' . "\r\n" . '           <td>' . $bar->namaakun . '</td>' . "\r\n" . '           <td align=right>' . number_format($luasxtm, 2, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rupiah, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($rpperkg, 2, '.', ',') . '</td>     ' . "\r\n" . '           <td align=right>' . number_format($rpperha, 0, '.', ',') . '</td>    ' . "\r\n" . '           <td align=right>' . number_format($bar->rp01, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp02, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp03, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp04, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp05, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp06, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp07, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp08, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp09, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp10, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp11, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp12, 0, '.', ',') . '</td>' . "\r\n" . '         </tr>';
		$gttperha += $rpperha;
		$gttperkg += $rpperkg;
		$gtt01 += $tt01tm;
		$gtt02 += $tt02tm;
		$gtt03 += $tt03tm;
		$gtt04 += $tt04tm;
		$gtt05 += $tt05tm;
		$gtt06 += $tt06tm;
		$gtt07 += $tt07tm;
		$gtt08 += $tt08tm;
		$gtt09 += $tt09tm;
		$gtt10 += $tt10tm;
		$gtt11 += $tt11tm;
		$gtt12 += $tt12tm;
		$gtt += $bar->rupiah;
		$notm += 1;
	}
}

$stream .= '<tr class=rowheader>' . "\r\n" . '           <td colspan=4 align=right>Total Biaya TM</td>' . "\r\n" . '           <td align=right>' . number_format($ttrptm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . @number_format($rpperkgtm, 2, '.', ',') . '</td>     ' . "\r\n" . '           <td align=right>' . @number_format($rpperhatm, 0, '.', ',') . '</td>    ' . "\r\n" . '           <td align=right>' . number_format($tt01tm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt02tm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt03tm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt04tm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt05tm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt06tm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt07tm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt08tm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt09tm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt10tm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt11tm, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt12tm, 0, '.', ',') . '</td>' . "\r\n" . '         </tr>';
$noumum = 1;

while ($bar = mysql_fetch_object($res2)) {
	@$rpperkg = 0;
	@$rpperha = 0;

	if ('6' < substr($bar->noakun, 0, 1)) {
		@$rpperkg = $bar->rupiah / $produksi;
		@$rpperha = $bar->rupiah / ($luasxtbm + $luasxtm);
		@$rpperhaum += $bar->rupiah / ($luasxtbm + $luasxtm);
		$rpperkgum += $rpperkg;
		$tt01um += $bar->rp01;
		$tt02um += $bar->rp02;
		$tt03um += $bar->rp03;
		$tt04um += $bar->rp04;
		$tt05um += $bar->rp05;
		$tt06um += $bar->rp06;
		$tt07um += $bar->rp07;
		$tt08um += $bar->rp08;
		$tt09um += $bar->rp09;
		$tt10um += $bar->rp10;
		$tt11um += $bar->rp11;
		$tt12um += $bar->rp12;
		$ttrpum += $bar->rupiah;
		$stream .= '<tr class=rowcontent>' . "\r\n" . '           <td>' . $noumum . '</td>' . "\r\n" . '           <td>' . $bar->noakun . '</td>' . "\r\n" . '           <td>' . $bar->namaakun . '</td>' . "\r\n" . '           <td align=right>' . number_format($luasxtbm + $luasxtm, 2, '.', ',') . '</td> ' . "\r\n" . '           <td align=right>' . number_format($bar->rupiah, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($rpperkg, 2, '.', ',') . '</td>     ' . "\r\n" . '           <td align=right>' . number_format($rpperha, 0, '.', ',') . '</td>    ' . "\r\n" . '           <td align=right>' . number_format($bar->rp01, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp02, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp03, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp04, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp05, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp06, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp07, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp08, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp09, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp10, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp11, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->rp12, 0, '.', ',') . '</td>' . "\r\n" . '         </tr>';
		$gttperha += $rpperha;
		$gttperkg += $rpperkg;
		$gtt01 += $tt01um;
		$gtt02 += $tt02um;
		$gtt03 += $tt03um;
		$gtt04 += $tt04um;
		$gtt05 += $tt05um;
		$gtt06 += $tt06um;
		$gtt07 += $tt07um;
		$gtt08 += $tt08um;
		$gtt09 += $tt09um;
		$gtt10 += $tt10um;
		$gtt11 += $tt11um;
		$gtt12 += $tt12um;
		$gtt += $bar->rupiah;
		$noumum += 1;
	}
}

$stream .= '<tr class=rowheader>' . "\r\n" . '           <td colspan=4 align=right>Total Biaya UMUM</td>' . "\r\n" . '           <td align=right>' . number_format($ttrpum, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . @number_format($rpperkgum, 2, '.', ',') . '</td>     ' . "\r\n" . '           <td align=right>' . @number_format($rpperhaum, 0, '.', ',') . '</td>    ' . "\r\n" . '           <td align=right>' . number_format($tt01um, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt02um, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt03um, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt04um, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt05um, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt06um, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt07um, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt08um, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt09um, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt10um, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt11um, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt12um, 0, '.', ',') . '</td>' . "\r\n" . '         </tr>';
$nokapital = 1;

while ($bar = mysql_fetch_object($resx1)) {
	@$rpperkg = 0;
	@$rpperha = 0;
	@$rpperkg = $bar->harga / $produksi;
	@$rpperha = $bar->harga / ($luasxtbm + $luasxtm);
	@$rpperhakap += $bar->harga / ($luasxtbm + $luasxtm);
	$rpperkgkap += $rpperkg;
	$tt01kap += $bar->k01;
	$tt02kap += $bar->k02;
	$tt03kap += $bar->k03;
	$tt04kap += $bar->k04;
	$tt05kap += $bar->k05;
	$tt06kap += $bar->k06;
	$tt07kap += $bar->k07;
	$tt08kap += $bar->k08;
	$tt09kap += $bar->k09;
	$tt10kap += $bar->k10;
	$tt11kap += $bar->k11;
	$tt12kap += $bar->k12;
	$ttrpkap += $bar->harga;
	$stream .= '<tr class=rowcontent>' . "\r\n" . '           <td>' . $nokapital . '</td>' . "\r\n" . '           <td>' . $bar->noakun . '</td>' . "\r\n" . '           <td>' . $bar->namatipe . '</td>' . "\r\n" . '           <td align=right>' . number_format($luasxtbm + $luasxtm, 2, '.', ',') . '</td>     ' . "\r\n" . '           <td align=right>' . number_format($bar->harga, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($rpperkg, 2, '.', ',') . '</td>     ' . "\r\n" . '           <td align=right>' . number_format($rpperha, 0, '.', ',') . '</td>    ' . "\r\n" . '           <td align=right>' . number_format($bar->k01, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->k02, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->k03, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->k04, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->k05, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->k06, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->k07, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->k08, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->k09, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->k10, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->k11, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($bar->k12, 0, '.', ',') . '</td>' . "\r\n" . '         </tr>';
	$gttperha += $rpperha;
	$gttperkg += $rpperkg;
	$gtt01 += $tt01kap;
	$gtt02 += $tt02kap;
	$gtt03 += $tt03kap;
	$gtt04 += $tt04kap;
	$gtt05 += $tt05kap;
	$gtt06 += $tt06kap;
	$gtt07 += $tt07kap;
	$gtt08 += $tt08kap;
	$gtt09 += $tt09kap;
	$gtt10 += $tt10kap;
	$gtt11 += $tt11kap;
	$gtt12 += $tt12kap;
	$gtt += $bar->harga;
	$nokapital += 1;
}

$stream .= '<tr class=rowheader>' . "\r\n" . '           <td colspan=4 align=right>Total KAPITAL</td>' . "\r\n" . '           <td align=right>' . number_format($ttrpkap, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . @number_format($rpperkgkap, 2, '.', ',') . '</td>     ' . "\r\n" . '           <td align=right>' . @number_format($rpperhakap, 0, '.', ',') . '</td>    ' . "\r\n" . '           <td align=right>' . number_format($tt01kap, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt02kap, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt03kap, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt04kap, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt05kap, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt06kap, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt07kap, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt08kap, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt09kap, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt10kap, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt11kap, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($tt12kap, 0, '.', ',') . '</td>' . "\r\n" . '         </tr>';
$gttperkg = $gtt / $produksi;
$gttperha = $gtt / ($luasxtbm + $luasxtm);
$stream .= '<tr class=rowheader>' . "\r\n" . '           <td colspan=4 align=right>GRAND TOTAL</td>' . "\r\n" . '           <td align=right>' . number_format($gtt, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . @number_format($gttperkg, 2, '.', ',') . '</td>     ' . "\r\n" . '           <td align=right>' . @number_format($gttperha, 0, '.', ',') . '</td>    ' . "\r\n" . '           <td align=right>' . number_format($gtt01, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($gtt02, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($gtt03, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($gtt04, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($gtt05, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($gtt06, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($gtt07, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($gtt08, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($gtt09, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($gtt10, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($gtt11, 0, '.', ',') . '</td>' . "\r\n" . '           <td align=right>' . number_format($gtt12, 0, '.', ',') . '</td>' . "\r\n" . '         </tr>';
$stream .= "\t" . ' ' . "\r\n\t\t" . ' </tbody>' . "\r\n\t\t" . ' <tfoot>' . "\r\n\t\t" . ' </tfoot>' . "\r\n\t\t" . ' </table></fieldset>';
$stream .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
$qwe = date('YmdHms');
$nop_ = 'Budget_' . $kodeorg . '_' . $jenis . '_' . $thnbudget . '__' . $qwe;

if (0 < strlen($stream)) {
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $stream);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '        window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '        </script>';
}

?>
