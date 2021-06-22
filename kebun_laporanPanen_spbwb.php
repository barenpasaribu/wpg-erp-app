<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$pt = $_POST['pt'];
$unit = $_POST['unit'];
$tgl1 = $_POST['tgl1'];
$tgl2 = $_POST['tgl2'];
$tanggal1 = explode('-', $tgl1);
$tanggal2 = explode('-', $tgl2);
$date1 = $tanggal1[2].'-'.$tanggal1[1].'-'.$tanggal1[0];
$tanggalterakhir = date(t, strtotime($date1));
$sdakar = 'select kodeorg,tahuntanam from '.$dbname.'.setup_blok';
$qdakar = mysql_query($sdakar) ;
while ($rdakar = mysql_fetch_assoc($qdakar)) {
    $belok[$rdakar['kodeorg']] = $rdakar['tahuntanam'];
}
if ('' === $unit) {
    $str = "select a.blok,a.tanggal,a.nospb,a.notiket,a.nokendaraan,a.jjg,a.kgwb,a.bjr,a.kgbjr\r\n        from ".$dbname.".kebun_spb_vw a\r\n        left join ".$dbname.".organisasi c\r\n        on substr(a.kodeorg,1,4)=c.kodeorganisasi\r\n        where c.induk = '".$pt."'  and a.tanggal between ".tanggalsystem($tgl1).' and '.tanggalsystem($tgl2)." \r\n        order by a.blok, a.tanggal";
} else {
    $str = "select a.blok,a.tanggal,a.nospb,a.notiket,a.nokendaraan,a.jjg,a.kgwb,a.bjr,a.kgbjr\r\n        from ".$dbname.".kebun_spb_vw a\r\n        where blok like '".$unit."%'  and a.tanggal between ".tanggalsystem($tgl1).' and '.tanggalsystem($tgl2)." \r\n        order by a.blok, a.tanggal";
}

echo "<thead> \r\n        <tr>\r\n            <td align=center>No.</td>\r\n            <td align=center>".$_SESSION['lang']['afdeling']."</td>\r\n            <td align=center>".$_SESSION['lang']['blok']."</td>\r\n            <td align=center>".$_SESSION['lang']['tahuntanam']."</td>\r\n            <td align=center>".$_SESSION['lang']['tanggal']."</td>\r\n            <td align=center>".$_SESSION['lang']['nospb']."</td>\r\n            <td align=center>".$_SESSION['lang']['noTiket']."</td>\r\n            <td align=center>".$_SESSION['lang']['kendaraan']."</td>\r\n            <td align=center>".$_SESSION['lang']['jjg']."</td>\r\n            <td align=center>".'KG '.$_SESSION['lang']['kebun']."</td>    \r\n            <td align=center>".$_SESSION['lang']['kgwb']."</td>\r\n            <td align=center>".$_SESSION['lang']['bjr'].' '.$_SESSION['lang']['aktual']."</td>\r\n            <td align=center>".$_SESSION['lang']['bjr']." Sensus</td>\r\n            <td align=center>%</td>\r\n        </tr></thead>\r\n\t<tbody>";
$res = mysql_query($str);
$no = 0;
if (mysql_num_rows($res) < 1) {
    $jukol = 12;
    echo '<tr class=rowcontent><td colspan='.$jukol.'>'.$_SESSION['lang']['tidakditemukan'].'</td></tr>';
} else {
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $aktual = $bar->kgwb / $bar->jjg;
        echo "<tr class='rowcontent'>\r\n            <td align=center>".$no."</td>\r\n            <td align=left>".substr($bar->blok, 0, 6)."</td>\r\n            <td align=center>".$bar->blok."</td>\r\n            <td align=center>".$belok[$bar->blok]."</td>\r\n            <td align=center>".$bar->tanggal."</td>\r\n            <td align=center>".$bar->nospb.'</td>';
        $notiket = $bar->notiket;
        if ('' !== $notiket) {
            echo '<td align=right>'.$notiket.'</td>';
        } else {
            echo "<td bgcolor=red title='Belum Masuk PKS' align=right>".$notiket.'</td>';
        }

        echo '<td align=center>'.$bar->nokendaraan."</td>\r\n            <td align=right>".$bar->jjg.'</td>';
        echo '<td align=right>'.number_format($bar->kgbjr, 2).'</td>';
        $kgwb = $bar->kgwb;
        if (0 !== $kgwb) {
            echo '<td align=right>'.number_format($kgwb, 2).'</td>';
            $beda = $kgwb - $bar->kgbjr;
            $persen = $beda / $bar->kgbjr * 100;
        } else {
            echo "<td bgcolor=red title='SPB Belum Diinput' align=right>".number_format($kgwb, 2).'</td>';
            $persen = 0;
        }

        echo '<td align=right>'.number_format($aktual, 2)."</td>\r\n            <td align=right>".$bar->bjr.'</td>';
        echo '<td align=right>'.number_format($persen, 2).'</td>';
        echo '</tr>';
        $totalbarjjg += $bar->jjg;
        $totalbarkgbjr += $bar->kgbjr;
        $totalbarkgwb += $bar->kgwb;
    }
    echo "<tr class='rowcontent'>\r\n            <td align=center></td>\r\n            <td align=left></td>\r\n            <td align=center></td>\r\n            <td align=center></td>\r\n            <td align=center></td>\r\n            <td align=center>Total</td><td align=right></td>";
    echo "<td align=center></td>\r\n            <td align=right>".number_format($totalbarjjg).'</td>';
    echo '<td align=right>'.number_format($totalbarkgbjr, 2).'</td>';
    echo '<td align=right>'.number_format($totalbarkgwb, 2).'</td>';
    $beda = $totalbarkgwb - $totalbarkgbjr;
    $persen = $beda / $totalbarkgbjr * 100;
    $aktual = $totalbarkgwb / $totalbarjjg;
    echo '<td align=right>'.number_format($aktual, 2)."</td>\r\n            <td align=right></td>";
    echo '<td align=right>'.number_format($persen, 2).'</td>';
    echo '</tr>';
}

echo "</tbody>\r\n        <tfoot>\r\n        </tfoot>";

?>