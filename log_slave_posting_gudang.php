<?php
//lastupdate 26062020
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';

if (isTransactionPeriod()) {
	$gudang = $_POST['gudang'];
	$notransaksi = $_POST['notransaksi'];
	$tipetransaksi = $_POST['tipe'];
	echo ' <div  style=\'width:690px;height:380px;overflow:scroll;\'> ' . "\r\n" . '        <table class=sortable cellspacing=1 border=0 width=100%>' . "\r\n" . '        <thead>';
	$num = 0;

	switch ($tipetransaksi) {
	case 1:
		$str = 'select a.kodebarang,a.satuan,a.jumlah,a.hargasatuan,b.tanggal,b.kodept,b.tipetransaksi,c.namasupplier,b.nopo,b.idsupplier,a.hargasatuan' . "\r\n" . 'from ' . $dbname . '.log_transaksidt a left join  ' . $dbname . '.log_transaksiht b ' . "\r\n" . 'on a.notransaksi=b.notransaksi' . "\r\n" . '        left join ' . $dbname . '.log_5supplier c' . "\r\n" . '        on b.idsupplier=c.supplierid    ' . "\r\n" . '        where a.notransaksi=\'' . $notransaksi . '\' and b.kodegudang=\'' . $gudang . '\' and statussaldo=0';
		echo '<tr class=rowheader>' . "\r\n" . '                <td>No</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['tipe'] . '</td>' . "\t\t\t" . '   ' . "\r\n" . '                    <td>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['ptpemilikbarang'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['supplier'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['nopo'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['hargasatuan'] . '</td>    ' . "\r\n" . '                    </tr>' . "\r\n" . '                    </thead>' . "\r\n" . '                    <tbody>';
		$res = mysql_query($str);
		$num = mysql_num_rows($res);
		$no = 0;

		while ($bar = mysql_fetch_object($res)) {
			$no += 1;
			$strc = 'select namabarang from ' . $dbname . '.log_5masterbarang where' . "\r\n" . '                        kodebarang=\'' . $bar->kodebarang . '\'';
			$resc = mysql_query($strc);
			$namabarang = '';

			while ($barc = mysql_fetch_object($resc)) {
				$namabarang = $barc->namabarang;
			}

			echo '<tr class=rowcontent id=row' . $no . '>' . "\r\n" . '                    <td>' . $no . '</td>' . "\r\n" . '                    <td id=tipe' . $no . ' title="1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi">' . $bar->tipetransaksi . '</td>' . "\r\n" . '                    <td id=tanggal' . $no . ' >' . tanggalnormal($bar->tanggal) . '</td>' . "\r\n" . '                    <td id=kodebarang' . $no . ' >' . $bar->kodebarang . '</td>' . "\r\n" . '                    <td>' . $namabarang . '</td>' . "\r\n" . '                    <td id=satuan' . $no . ' >' . $bar->satuan . '</td>' . "\r\n" . '                    <td  id=jumlah' . $no . ' align=right>' . $bar->jumlah . '</td>' . "\r\n" . '                    <td  id=harga' . $no . ' align=right>' . $bar->hargasatuan . '</td>' . "\r\n" . '                    <td id=kodept' . $no . ' >' . $bar->kodept . '</td>' . "\r\n" . '                    <td id=supplier' . $no . '>' . $bar->idsupplier . '</td>' . "\r\n" . '                    <td id=nopo' . $no . '>' . $bar->nopo . '</td>' . "\r\n" . '                    <td id=hargasatuan' . $no . '>' . $bar->hargasatuan . '</td>    ' . "\r\n" . '                    </tr>';
		}

		break;

	case 2:
		$str = 'select a.kodebarang,a.satuan,a.jumlah,b.tanggal,b.kodept,b.tipetransaksi,a.kodeblok,a.kodekegiatan,kodemesin,b.untukunit' . "\r\n" . '                from ' . $dbname . '.log_transaksidt a left join  ' . $dbname . '.log_transaksiht b ' . "\r\n" . '                    on a.notransaksi=b.notransaksi' . "\r\n" . '                    where a.notransaksi=\'' . $notransaksi . '\' and b.kodegudang=\'' . $gudang . '\' and statussaldo=0';
		echo '<tr class=rowheader>' . "\r\n" . '                <td>No</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['tipe'] . '</td>' . "\t\t\t" . '   ' . "\r\n" . '                    <td>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['ptpemilikbarang'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['untukunit'] . '</td>                                ' . "\r\n" . '                    <td>' . $_SESSION['lang']['kodekegiatan'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['kodevhc'] . '</td>                          ' . "\r\n" . '                    <td>' . $_SESSION['lang']['kodeblok'] . '</td>                     ' . "\r\n" . '                    </tr>' . "\r\n" . '                    </thead>' . "\r\n" . '                    <tbody>';
		$res = mysql_query($str);
		$num = mysql_num_rows($res);
		$no = 0;

		while ($bar = mysql_fetch_object($res)) {
			$no += 1;
			$strc = 'select namabarang from ' . $dbname . '.log_5masterbarang where' . "\r\n" . '                        kodebarang=\'' . $bar->kodebarang . '\'';
			$resc = mysql_query($strc);
			$namabarang = '';

			while ($barc = mysql_fetch_object($resc)) {
				$namabarang = $barc->namabarang;
			}

			echo '<tr class=rowcontent id=row' . $no . '>' . "\r\n" . '                    <td>' . $no . '</td>' . "\r\n" . '                    <td id=tipe' . $no . ' title="1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi">' . $bar->tipetransaksi . '</td>' . "\r\n" . '                    <td id=tanggal' . $no . ' >' . tanggalnormal($bar->tanggal) . '</td>' . "\r\n" . '                    <td id=kodebarang' . $no . ' >' . $bar->kodebarang . '</td>' . "\r\n" . '                    <td>' . $namabarang . '</td>' . "\r\n" . '                    <td id=satuan' . $no . ' >' . $bar->satuan . '</td>' . "\r\n" . '                    <td  id=jumlah' . $no . ' align=right>' . $bar->jumlah . '</td>' . "\r\n" . '                    <td id=kodept' . $no . ' >' . $bar->kodept . '</td>' . "\r\n" . '                    <td id=untukunit' . $no . '>' . $bar->untukunit . '</td>                                ' . "\r\n" . '                    <td id=kodekegiatan' . $no . '>' . $bar->kodekegiatan . '</td>      ' . "\r\n" . '                    <td id=kodemesin' . $no . '>' . $bar->kodemesin . '</td>                         ' . "\r\n" . '                    <td id=kodeblok' . $no . '>' . $bar->kodeblok . '</td>' . "\r\n" . '                    </tr>';
		}

		break;

	case 3:
		$str = 'select a.kodebarang,a.satuan,a.jumlah,b.gudangx,a.kodeblok,a.hargasatuan,' . "\r\n" . '                        b.tanggal,b.kodept,b.tipetransaksi' . "\r\n" . '                        from ' . $dbname . '.log_transaksidt a left join  ' . $dbname . '.log_transaksiht b ' . "\r\n" . '                            on a.notransaksi=b.notransaksi' . "\r\n" . '                            where a.notransaksi=\'' . $notransaksi . '\' and b.kodegudang=\'' . $gudang . '\' and statussaldo=0';
		echo '<tr class=rowheader>' . "\r\n" . '                        <td>No</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['tipe'] . '</td>' . "\t\t\t" . '   ' . "\r\n" . '                            <td>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['ptpemilikbarang'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['sumber'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['kodeblok'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['hargasatuan'] . '</td>    ' . "\r\n" . '                            </tr>' . "\r\n" . '                            </thead>' . "\r\n" . '                            <tbody>';
		$res = mysql_query($str);
		$num = mysql_num_rows($res);
		$no = 0;

		while ($bar = mysql_fetch_object($res)) {
			$no += 1;
			$strc = 'select namabarang from ' . $dbname . '.log_5masterbarang where' . "\r\n" . '                                kodebarang=\'' . $bar->kodebarang . '\'';
			$resc = mysql_query($strc);
			$namabarang = '';

			while ($barc = mysql_fetch_object($resc)) {
				$namabarang = $barc->namabarang;
			}

			echo '<tr class=rowcontent id=row' . $no . '>' . "\r\n" . '                            <td>' . $no . '</td>' . "\r\n" . '                            <td id=tipe' . $no . ' title="1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi">' . $bar->tipetransaksi . '</td>' . "\r\n" . '                            <td id=tanggal' . $no . ' >' . tanggalnormal($bar->tanggal) . '</td>' . "\r\n" . '                            <td id=kodebarang' . $no . ' >' . $bar->kodebarang . '</td>' . "\r\n" . '                            <td>' . $namabarang . '</td>' . "\r\n" . '                            <td id=satuan' . $no . ' >' . $bar->satuan . '</td>' . "\r\n" . '                            <td  id=jumlah' . $no . ' align=right>' . $bar->jumlah . '</td>' . "\r\n" . '                            <td id=kodept' . $no . ' >' . $bar->kodept . '</td>' . "\r\n" . '                            <td id=gudangx' . $no . ' >' . $bar->gudangx . '</td>' . "\r\n" . '                            <td id=kodeblok' . $no . '>' . $bar->kodeblok . '</td>' . "\r\n" . '                            <td id=hargasatuan' . $no . '>' . $bar->hargasatuan . '</td>    ' . "\r\n" . '                            </tr>';
		}

		break;

	case 5:
		$str = 'select a.kodebarang,a.satuan,a.jumlah,b.untukpt,a.kodeblok,b.untukunit,a.kodekegiatan,a.kodemesin,' . "\r\n" . '                        b.tanggal,b.kodept,b.tipetransaksi' . "\r\n" . '                        from ' . $dbname . '.log_transaksidt a left join  ' . $dbname . '.log_transaksiht b ' . "\r\n" . '                            on a.notransaksi=b.notransaksi' . "\r\n" . '                            where a.notransaksi=\'' . $notransaksi . '\' and b.kodegudang=\'' . $gudang . '\' and statussaldo=0';
		echo '<tr class=rowheader>' . "\r\n" . '                        <td>No</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['tipe'] . '</td>' . "\t\t\t" . '   ' . "\r\n" . '                            <td>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['ptpemilikbarang'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['pt'] . '</td>    ' . "\r\n" . '                            <td>' . $_SESSION['lang']['untukunit'] . '</td>                                ' . "\r\n" . '                            <td>' . $_SESSION['lang']['kodeblok'] . '</td><td>Nama Blok</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['kodekegiatan'] . '</td> ' . "\r\n" . '                            <td>' . $_SESSION['lang']['kodevhc'] . '</td>     ' . "\r\n" . '                            </tr>' . "\r\n" . '                            </thead>' . "\r\n" . '                            <tbody>';
		$res = mysql_query($str);
		$num = mysql_num_rows($res);
		$no = 0;

		while ($bar = mysql_fetch_object($res)) {
			$no += 1;
			$strc = 'select namabarang from ' . $dbname . '.log_5masterbarang where' . "\r\n" . '                                kodebarang=\'' . $bar->kodebarang . '\'';
			$resc = mysql_query($strc);
			$namabarang = '';

			while ($barc = mysql_fetch_object($resc)) {
				$namabarang = $barc->namabarang;
			}

			$str1 = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $bar->kodeblok . '\'';
			$res1 = mysql_query($str1);
			$has = mysql_fetch_assoc($res1);
				$namablok = $has['namaorganisasi'];

			echo '<tr class=rowcontent id=row' . $no . '>' . "\r\n" . '                            <td>' . $no . '</td>' . "\r\n" . '                            <td id=tipe' . $no . ' title="1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi">' . $bar->tipetransaksi . '</td>' . "\r\n" . '                            <td id=tanggal' . $no . ' >' . tanggalnormal($bar->tanggal) . '</td>' . "\r\n" . '                            <td id=kodebarang' . $no . ' >' . $bar->kodebarang . '</td>' . "\r\n" . '                            <td>' . $namabarang . '</td>' . "\r\n" . '                            <td id=satuan' . $no . ' >' . $bar->satuan . '</td>' . "\r\n" . '                            <td  id=jumlah' . $no . ' align=right>' . $bar->jumlah . '</td>' . "\r\n" . '                            <td id=kodept' . $no . ' >' . $bar->kodept . '</td>' . "\r\n" . '                            <td id=untukpt' . $no . ' >' . $bar->untukpt . '</td>' . "\r\n" . '                            <td id=untukunit' . $no . '>' . $bar->untukunit . '</td>                                ' . "\r\n" . '                            <td id=kodeblok' . $no . '>' . $bar->kodeblok . '</td><td>'.$namablok.'</td>' . "\r\n" . '                            <td id=kodekegiatan' . $no . '>' . $bar->kodekegiatan . '</td>                                ' . "\r\n" . '                            <td id=kodemesin' . $no . '>' . $bar->kodemesin . '</td>    ' . "\r\n" . '                            </tr>';
		}

		break;

	case 6:
		$str = 'select a.kodebarang,a.satuan,a.jumlah,a.hargasatuan,b.tanggal,b.kodept,b.tipetransaksi,c.namasupplier,b.nopo,b.idsupplier,a.hargasatuan' . "\r\n" . '        from ' . $dbname . '.log_transaksidt a left join  ' . $dbname . '.log_transaksiht b ' . "\r\n" . '        on a.notransaksi=b.notransaksi' . "\r\n" . '        left join ' . $dbname . '.log_5supplier c' . "\r\n" . '        on b.idsupplier=c.supplierid    ' . "\r\n" . '        where a.notransaksi=\'' . $notransaksi . '\' and b.kodegudang=\'' . $gudang . '\' and statussaldo=0';
		echo '<tr class=rowheader>' . "\r\n" . '                <td>No</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['tipe'] . '</td>' . "\t\t\t" . '   ' . "\r\n" . '                    <td>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['ptpemilikbarang'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['supplier'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['nopo'] . '</td>' . "\r\n" . '                    <td>' . $_SESSION['lang']['hargasatuan'] . '</td>    ' . "\r\n" . '                    </tr>' . "\r\n" . '                    </thead>' . "\r\n" . '                    <tbody>';
		$res = mysql_query($str);
		$num = mysql_num_rows($res);
		$no = 0;

		while ($bar = mysql_fetch_object($res)) {
			$no += 1;
			$strc = 'select namabarang from ' . $dbname . '.log_5masterbarang where' . "\r\n" . '                        kodebarang=\'' . $bar->kodebarang . '\'';
			$resc = mysql_query($strc);
			$namabarang = '';

			while ($barc = mysql_fetch_object($resc)) {
				$namabarang = $barc->namabarang;
			}

			echo '<tr class=rowcontent id=row' . $no . '>' . "\r\n" . '                    <td>' . $no . '</td>' . "\r\n" . '                    <td id=tipe' . $no . ' title="1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi">' . $bar->tipetransaksi . '</td>' . "\r\n" . '                    <td id=tanggal' . $no . ' >' . tanggalnormal($bar->tanggal) . '</td>' . "\r\n" . '                    <td id=kodebarang' . $no . ' >' . $bar->kodebarang . '</td>' . "\r\n" . '                    <td>' . $namabarang . '</td>' . "\r\n" . '                    <td id=satuan' . $no . ' >' . $bar->satuan . '</td>' . "\r\n" . '                    <td  id=jumlah' . $no . ' align=right>' . $bar->jumlah . '</td>' . "\r\n" . '                    <td  id=harga' . $no . ' align=right>' . $bar->hargasatuan . '</td>' . "\r\n" . '                    <td id=kodept' . $no . ' >' . $bar->kodept . '</td>' . "\r\n" . '                    <td id=supplier' . $no . '>' . $bar->idsupplier . '</td>' . "\r\n" . '                    <td id=nopo' . $no . '>' . $bar->nopo . '</td>' . "\r\n" . '                    <td id=hargasatuan' . $no . '>' . $bar->hargasatuan . '</td>                         ' . "\r\n" . '                    </tr>';
		}

		break;

	case 7:
		$str = 'select a.kodebarang,a.satuan,a.jumlah,b.gudangx,a.kodeblok,' . "\r\n" . '                        b.tanggal,b.kodept,b.tipetransaksi' . "\r\n" . '                        from ' . $dbname . '.log_transaksidt a left join  ' . $dbname . '.log_transaksiht b ' . "\r\n" . '                            on a.notransaksi=b.notransaksi' . "\r\n" . '                            where a.notransaksi=\'' . $notransaksi . '\' and b.kodegudang=\'' . $gudang . '\' and statussaldo=0';
		echo '<tr class=rowheader>' . "\r\n" . '                        <td>No</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['tipe'] . '</td>' . "\t\t\t" . '   ' . "\r\n" . '                            <td>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['ptpemilikbarang'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['tujuan'] . '</td>' . "\r\n" . '                            <td>' . $_SESSION['lang']['kodeblok'] . '</td>' . "\r\n" . '                            </tr>' . "\r\n" . '                            </thead>' . "\r\n" . '                            <tbody>';
		$res = mysql_query($str);
		$num = mysql_num_rows($res);
		$no = 0;

		while ($bar = mysql_fetch_object($res)) {
			$no += 1;
			$strc = 'select namabarang from ' . $dbname . '.log_5masterbarang where' . "\r\n" . '                                kodebarang=\'' . $bar->kodebarang . '\'';
			$resc = mysql_query($strc);
			$namabarang = '';

			while ($barc = mysql_fetch_object($resc)) {
				$namabarang = $barc->namabarang;
			}

			echo '<tr class=rowcontent id=row' . $no . '>' . "\r\n" . '                            <td>' . $no . '</td>' . "\r\n" . '                            <td id=tipe' . $no . ' title="1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi">' . $bar->tipetransaksi . '</td>' . "\r\n" . '                            <td id=tanggal' . $no . ' >' . tanggalnormal($bar->tanggal) . '</td>' . "\r\n" . '                            <td id=kodebarang' . $no . ' >' . $bar->kodebarang . '</td>' . "\r\n" . '                            <td>' . $namabarang . '</td>' . "\r\n" . '                            <td id=satuan' . $no . ' >' . $bar->satuan . '</td>' . "\r\n" . '                            <td  id=jumlah' . $no . ' align=right>' . $bar->jumlah . '</td>' . "\r\n" . '                            <td id=kodept' . $no . ' >' . $bar->kodept . '</td>' . "\r\n" . '                            <td id=gudangx' . $no . ' >' . $bar->gudangx . '</td>' . "\r\n" . '                            <td id=kodeblok' . $no . '>' . $bar->kodeblok . '</td>' . "\r\n" . '                            </tr>';
		}

		break;

	default:
	}

	echo '</tbody><tfoot></tfoot></table>' . "\r\n" . '   <center>' . "\r\n" . '     <button onclick="prosesPosting(' . $no . ',\'' . $tipetransaksi . '\',\'' . $notransaksi . '\'); this.disabled=true;" class=mybutton>' . $_SESSION['lang']['posting'] . '</button>' . "\r\n\t" . ' <button onclick=closeDialog() class=mybutton>' . $_SESSION['lang']['cancel'] . '</button>' . "\r\n" . '   </center>' . "\r\n" . '   </div>';
}
else {
	echo ' Error: Transaction Period missing';
}

?>
