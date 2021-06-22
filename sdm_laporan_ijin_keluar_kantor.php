<?php

    require_once 'master_validation.php';
    include 'lib/eagrolib.php';
    include_once 'lib/zLib.php';
    echo open_body();
    echo "<script language=javascript1.2 src='js/sdm_laporan_ijin_keluar_kantor.js'></script>\r\n<script>\r\n    tolak=\"";
    echo $_SESSION['lang']['ditolak'];
    echo "\";\r\n    </script>\r\n";
    include 'master_mainMenu.php';
    OPEN_BOX('', '<b>'.strtoupper($_SESSION['lang']['list'].' '.$_SESSION['lang']['izinkntor']).'</b>');
    $optKary = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
    $optJenis = $optKary;
    $sKary = 'select distinct a.karyawanid,b.namakaryawan,b.nik from '.$dbname.".sdm_ijin a \r\n\t\tleft join ".$dbname.'.datakaryawan b on a.karyawanid=b.karyawanid order by namakaryawan asc';
    $qKary = mysql_query($sKary);
    while ($rKary = mysql_fetch_assoc($qKary)) {
        $optKary .= "<option value='".$rKary['karyawanid']."'>".$rKary['namakaryawan'].' - '.$rKary['nik'].'</option>';
    }
    $arragama = getEnum($dbname, 'sdm_ijin', 'jenisijin');
    foreach ($arragama as $kei => $fal) {
        if ('ID' == $_SESSION['language']) {
            $optJenis .= "<option value='".$kei."'>".$fal.'</option>';
        } else {
            switch ($fal) {
                case 'TERLAMBAT':
                    $fal = 'Late for work';

                    break;
                case 'KELUAR':
                    $fal = 'Out of Office';

                    break;
                case 'PULANGAWAL':
                    $fal = 'Home early';

                    break;
                case 'IJINLAIN':
                    $fal = 'Other purposes';

                    break;
                case 'CUTI':
                    $fal = 'Leave';

                    break;
                case 'MELAHIRKAN':
                    $fal = 'Maternity';

                    break;
                default:
                    $fal = 'Wedding, Circumcision or Graduation';

                    break;
            }
            $optJenis .= "<option value='".$kei."'>".$fal.'</option>';
        }
    }
    echo "<br><img onclick=detailExcel(event,'sdm_slave_laporan_ijin_meninggalkan_kantor.php') src=images/excel.jpg class=resicon title='MS.Excel'> \r\n     &nbsp;".$_SESSION['lang']['namakaryawan'].': <select id=karyidCari style=width:150px onchange=getCariDt()>'.$optKary."</select>&nbsp;\r\n     ".$_SESSION['lang']['jeniscuti'].': <select id=jnsCuti style=width:150px onchange=getCariDt()>'.$optJenis."</select>&nbsp;\r\n         <button class=mybutton onclick=dtReset()>".$_SESSION['lang']['cancel']."</button>\r\n         
            <br><br>
            <div style='width:100%;height:600px;overflow:scroll;'>
                <table class=sortable cellspacing=1 border=0>
                    <thead>
                        <tr>
                            <td align=center>No.</td>
                            <td align=center>".$_SESSION['lang']['tanggal']."</td>
                            <td align=center>".$_SESSION['lang']['nama']."</td>
                            <td align=center>".$_SESSION['lang']['ganti']."</td>
                            <td align=center>".$_SESSION['lang']['keperluan']."</td>
                            <td align=center>".$_SESSION['lang']['jenisijin']."</td>
                            <td align=center>".$_SESSION['lang']['tanggal'].' '.$_SESSION['lang']['mulai']."</td>
                            <td align=center>".$_SESSION['lang']['tanggal'].' '.$_SESSION['lang']['selesai']."</td>
                            <td align=center>Jumlah HK</td>
                            <td align=center>Sisa Cuti</td>
                            <td align=center>".$_SESSION['lang']['atasan']."</td>
                            <td align=center>".' '.$_SESSION['lang']['atasan'].' '.$_SESSION['lang']['dari'].' '.$_SESSION['lang']['atasan']."</td>
                            <td align=center>".$_SESSION['lang']['hrd']."</td>
                            <td align=center>Action</td>
                        </tr>
                    </thead>
                    <tbody id=container><script>loadData()</script></tbody>
                </table>
            </div>";
    CLOSE_BOX();
    close_body();

?>