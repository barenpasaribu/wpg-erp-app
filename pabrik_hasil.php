<?php
    require_once 'master_validation.php';
    include_once 'lib/eagrolib.php';
    include_once 'lib/zLib.php';
    include_once 'lib/rTable.php';
    echo open_body();
    include 'master_mainMenu.php';
    echo "  <script language=javascript src=js/zMaster.js></script>
            <script language=javascript src=js/zSearch.js></script>
            <script language=javascript1.2 src='js/pabrik_hasil.js'></script>
            <script languange=javascript1.2 src='js/formTable.js'></script>
            <link rel=stylesheet type=text/css href='style/zTable.css'>
        ";
    $ctl = [];
    // $ctl[] = "  <div align='center'>
    //                 <img class=delliconBig src=images/".$_SESSION['theme']."/addbig.png title='".$_SESSION['lang']['new']."' onclick=\"showAdd()\">
    //                 <br>
    //                 <span align='center'>".$_SESSION['lang']['new'].'</span>
    //             </div>';
    // $ctl[] = "  <div align='center'>
    //                 <img class=delliconBig src=images/".$_SESSION['theme']."/list.png title='".$_SESSION['lang']['list']."' onclick=\"defaultList()\"><br>
    //                 <span align='center'>".$_SESSION['lang']['list'].'</span>
    //             </div>';
    $ctl[] = '  <fieldset>
                    <legend>
                        <b>'.$_SESSION['lang']['find'].'</b>
                    </legend>'.makeElement('sNoTrans', 'label', $_SESSION['lang']['notransaksi']).makeElement('sNoTrans', 'text', '').makeElement('sFind', 'btn', $_SESSION['lang']['find'], ['onclick' => 'searchTrans()']).'
                </fieldset>';
    $header = [$_SESSION['lang']['nomor'], $_SESSION['lang']['tanggal'], $_SESSION['lang']['pabrik'], $_SESSION['lang']['kodetangki'], $_SESSION['lang']['kwantitas'], $_SESSION['lang']['kernelquantity'], $_SESSION['lang']['suhu']];
    $cols = 'notransaksi,tanggal,kodeorg,kodetangki,kuantitas,kernelquantity,suhu,posting';
    $query = selectQuery($dbname, 'pabrik_masukkeluartangki', $cols, "kodeorg='".$_SESSION['empl']['lokasitugas']."' order by tanggal desc", '', false, 10, 1);
    $data = fetchData($query);
    $totalRow = getTotalRow($dbname, 'pabrik_masukkeluartangki');
    foreach ($data as $key => $row) {
        $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
        if (1 === $row['posting']) {
            $data[$key]['switched'] = true;
        }

        unset($data[$key]['posting']);
    }
    $x = 'SELECT kodejabatan FROM '.$dbname.".sdm_5jabatan WHERE alias LIKE '%ka.%' or alias like '%kepala%' or alias like '%Mill'";
    $y = mysql_query($x);
    // print_r($_SESSION['empl']['kodejabatan']);
    // die();
    while ($z = mysql_fetch_assoc($y)) {
        $pos = $z['kodejabatan'];
        if ($pos === $_SESSION['empl']['kodejabatan']) {
            $flag = 1;
        }
    }
    $tHeader = new rTable('headTable', 'headTableBody', $header, $data);
    $tHeader->addAction('showEdit', 'Edit', 'images/'.$_SESSION['theme'].'/edit.png');
    $tHeader->addAction('deleteData', 'Delete', 'images/'.$_SESSION['theme'].'/delete.png');
    $tHeader->addAction('postingData', 'Posting', 'images/'.$_SESSION['theme'].'/posting.png');
    $tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
    $tHeader->addAction('lihatPDF', 'PDF', 'images/'.$_SESSION['theme'].'/pdf.jpg');

    if (1 !== $flag) {
        $tHeader->_actions[2]->_name = 'postingData';
        // $tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme'].'/posted.png');
        // $tHeader->_actions[0]->_name = '';
        // $tHeader->_actions[0]->_title = '';
        // $tHeader->_actions[0]->_img = '';
        // $tHeader->_actions[0]->_altImg = '';
        // $tHeader->_actions[0]->_switchImg = '';
    }
    $tHeader->pageSetting(1, $totalRow, 10);
    OPEN_BOX('', '<b>Sounding Produksi</b>');
    echo "<div><table align='center'><tr>";
    foreach ($ctl as $el) {
        echo "<td v-align='middle' style='min-width:100px'>".$el.'</td>';
    }
    echo '</tr></table></div>';

    echo "  <script>window.onload = function() {
                showAdd();
            };</script>";
    echo "<div id='workField'>";
    echo '</div>';
    CLOSE_BOX();
    echo close_body();

?>