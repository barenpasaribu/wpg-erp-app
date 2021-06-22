<?php

    require_once 'master_validation.php';
    include 'lib/eagrolib.php';
    include_once 'lib/zLib.php';
    echo open_body();
    include 'master_mainMenu.php';
    OPEN_BOX();
    echo "<script language=javascript src='js/zTools.js'></script>\r\n<script language=javascript src='js/sdm_5pesangon.js'></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div id=form style='margin-bottom:30px;clear:both'>";
    $els = [];
    $els[] = [makeElement('rowNum', 'hidden').makeElement('masakerja', 'label', $_SESSION['lang']['masakerja']), makeElement('masakerja', 'textnum', '', ['style' => 'width:100px', 'maxlength' => '10'])];
    $els[] = [makeElement('pesangon', 'label', 'Rp Pesangon'), makeElement('pesangon', 'textnum', '', ['style' => 'width:100px'])];
    $els[] = [makeElement('penghargaan', 'label', 'Rp Penghargaan'), makeElement('penghargaan', 'textnum', '', ['style' => 'width:100px'])];
    $els[] = [makeElement('pengganti', 'label', 'Rp Mengundurkan diri'), makeElement('pengganti', 'textnum', '', ['style' => 'width:100px'])];
    $els[] = [makeElement('perusahaan', 'label', 'Rp Diberhentikan Perusahaan'), makeElement('perusahaan', 'textnum', '', ['style' => 'width:100px'])];
    $els[] = [makeElement('kesalahanbiasa', 'label', 'Rp Kesalahan Biasa'), makeElement('kesalahanbiasa', 'textnum', '', ['style' => 'width:100px'])];
    $els[] = [makeElement('kesalahanberat', 'label', 'Rp Kesalahan Berat'), makeElement('kesalahanberat', 'textnum', '', ['style' => 'width:100px'])];
    $els[] = [makeElement('uangpisah', 'label', 'Rp Uang Pisah'), makeElement('uangpisah', 'textnum', '', ['style' => 'width:100px'])];
    $fieldStr = '##kodeorg##kodekelompok##keterangan##nokounter';
    $fieldArr = explode('##', substr($fieldStr, 2, strlen($fieldStr) - 2));
    $els['btn'] = [makeElement('addBtn', 'btn', $_SESSION['lang']['tambah'], ['onclick' => 'add()']).makeElement('editBtn', 'btn', $_SESSION['lang']['edit'], ['onclick' => 'edit()', 'style' => 'display:none']).makeElement('cancelBtn', 'btn', $_SESSION['lang']['cancel'], ['onclick' => 'cancel()'])];
    echo genElTitle('Setup Pesangon', $els);
    echo '</div>';
    $cols = 'masakerja,pesangon,penghargaan,pengganti,perusahaan,kesalahanbiasa,kesalahanberat,uangpisah';
    $query = selectQuery($dbname, 'sdm_5pesangon', $cols);
    $data = fetchData($query);
    echo "<fieldset style='clear:both'>\r\n\t<legend><b>Table</b></legend>\r\n\t<table class='sortable'><tr class=rowheader>\r\n\t\t<td colspan=2>Aksi</td>\r\n\t\t<td>Masa Kerja</td>\r\n\t\t<td>Rp Pesangon</td>\r\n\t\t<td>Rp Penghargaan</td>\r\n\t\t<td>Rp Mengundurkan diri</td>\r\n\t\t<td>Rp Diberhentikan Perusahaan</td>\r\n\t\t<td>Rp Kesalahan Biasa</td>\r\n\t\t<td>Rp Kesalahan Berat</td>\r\n\t\t<td>Rp Uang Pisah</td>\r\n\t</tr><tbody id=tBody>";
    foreach ($data as $key => $row) {
        echo '<tr class=rowcontent>';
        echo "<td><img src='images/".$_SESSION['theme']."/edit.png' onclick=editMode(".$key.') class=zImgBtn></td>';
        echo "<td><img src='images/".$_SESSION['theme']."/delete.png' onclick=deleteData(".$key.') class=zImgBtn></td>';
        foreach ($row as $attr => $val) {
            if ('masakerja' != $attr) {
                $tmpVal = number_format($val, 2);
            } else {
                $tmpVal = $val;
            }

            echo "<td id='".$attr.'_'.$key."' value='".$val."'>".$tmpVal.'</td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table></fieldset>';
    CLOSE_BOX();
    echo close_body();

?>