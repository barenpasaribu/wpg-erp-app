<?php
include_once 'lib/devLibrary.php';
function makePage($cPage, $cTotalRow, $cRowPerPage){
    $makePage='';
    $totalpage = ceil($cTotalRow / $cRowPerPage);
    ($totalpage < 1 ? ($totalpage = 1) : null);
    $select = "<select style:'width:50px' id='pageNumber' onchange=\"newChoosePage(this,".$cRowPerPage.")\">";
    for ($i = 1; $i <= $totalpage; $i++) {
        $select.="<option value='$i'>$i</option>";
    }
    $select.="</select>";
    $makePage .= "<img id='first' src='images/".$_SESSION['theme']."/first.png'";
    if ($cPage > 1) {
        $makePage .= " style='cursor:pointer' onclick=\"newGoToPages(1,".$cRowPerPage.")\"";
    }

    $makePage .= '>&nbsp;';
    $makePage .= "<img id='prev' src='images/".$_SESSION['theme']."/prev.png'";
    if ($cPage > 1) {
        $makePage .= " style='cursor:pointer' onclick=\"newGoToPages(".($cPage - 1).",".$cRowPerPage.")\"";
    }
    $makePage .= '>&nbsp;';
    $makePage .= $select;
    $makePage .= "<img id='next'  src='images/".$_SESSION['theme']."/next.png'";
    if ($cPage < $totalpage) {
        $makePage .= " style='cursor:pointer' onclick=\"newGoToPages(".($cPage + 1).",".$cRowPerPage.")\"";
    }

    $makePage .= '>&nbsp;';
    $makePage .= "<img id='last'  src='images/".$_SESSION['theme']."/last.png'";
    if ($cPage < $totalpage) {
        $makePage .= " style='cursor:pointer' onclick=\"newGoToPages(".$totalpage.','.$cRowPerPage.")\"";
    }

    $makePage .= '>';
    return $makePage;
}

function prepareQuery($param2)
{
    $arrayFilters = [];
    $param2['kodeorg'] != '' ? $kodeorg2 = $param2['kodeorg'] : $kodeorg2 = $_SESSION['empl']['lokasitugas'];
    $arrayFilters[] = " k.kodeorg='" . $kodeorg2 . "' ";
    if (isset($param2['where'])) {
        $arrWhere = json_decode(str_replace('\\', '', $param2['where']), true);
        if (!empty($arrWhere)) {
            foreach ($arrWhere as $key => $r1) {
                if ('4' == $key) {
                    if ('' != $r1[1]) {
                        $arrayFilters[] = " k.notransaksi in (select notransaksi from keu_kasbankdt " .
                            " where kodesupplier in (select supplierid from log_5supplier " .
                            " where namasupplier like '%" . $r1[1] . "%'))";
                    }
                } else {
                    if ('' != $r1[1]) {$arrayFilters[] .= ' k.' . $r1[0] . " like '%" . $r1[1] . "%'";}
                }
            }
        }
    }

    $filter = generateFilter($arrayFilters);

    $sql = "select a.namaakun,o.namaorganisasi,k.*,
                (select sum(jumlah) from keu_kasbankdt k2 where k2.notransaksi=k.notransaksi) as balance,
                (case 
						when (select bagian from datakaryawan where karyawanid='".$_SESSION['standard']['userid']."') in (select kode from sdm_5departemen where alias = 'acc') 
						then 'allow'
						else 'notallow'
					 end) as postingprivilage
                from keu_kasbankht k
                inner join keu_5akun a on a.noakun=k.noakun
                inner join organisasi o on o.kodeorganisasi=k.kodeorg $filter 
                order by k.tanggal desc,k.posting";
				
	//echo "warning: ".$sql;
	//exit();
	
    return $sql;
}

function generateTable($sql,$page, $totalRow){
    $theTable = '';
    $theTable .= "<fieldset style='clear:left'  id='mainFieldset'>";
    $theTable .= "<legend><b>".$_SESSION['lang']['list']."</b></legend>";
    $theTable .= "<table  id='headTable'  class='sortable' cellspacing='1' style='width:100%' border='0'>";
    $theTable .= "<thead><tr class='rowheader'>";
    $header = [$_SESSION['lang']['notransaksi'], $_SESSION['lang']['unit'], $_SESSION['lang']['tanggal'], $_SESSION['lang']['noakun'], $_SESSION['lang']['tipe'], $_SESSION['lang']['jumlah'], 'Balance', $_SESSION['lang']['remark'], $_SESSION['lang']['nobayar']];
    foreach ($header as $key) {
        $theTable .= "<td align='center' style='width:10%'>$key</td>";
    }
    $theTable .= "<td colspan='5' align='center' style='width:10%'>Aksi</td></tr></thead>";
    $theTable .=" <tbody>";
    if ($sql!='') {
        $res = mysql_query($sql);

        while ($bar = mysql_fetch_assoc($res)) {
            $theTable .= "<tr class='rowcontent'>";
            $theTable .= "<td>&nbsp;" . $bar['notransaksi'] . "</td>";
            $theTable .= "<td>&nbsp;" . $bar['namaorganisasi'] . "</td>";
            $theTable .= "<td>&nbsp;" . $bar['tanggal'] . "</td>";
            $theTable .= "<td>&nbsp;" . $bar['namaakun'] . "</td>";
            $theTable .= "<td>&nbsp;" . $bar['tipetransaksi'] . "</td>";
            $theTable .= "<td align='right'>" . number_format($bar['jumlah']) . "&nbsp;</td>";
            $theTable .= "<td align='right'>" . number_format($bar['jumlah'] - $bar['balance']) . "&nbsp;</td>";
            $theTable .= "<td>&nbsp;" . $bar['keterangan'] . "</td>";
            $theTable .= "<td>&nbsp;</td>";
            $theTable .= "<td><img src='images/skyblue/edit.png' class='zImgBtn' onclick=\"showEdit('" . $bar['notransaksi'] . "');\" title='Edit'></td>";
            $theTable .= "<td><img src='images/skyblue/delete.png' class='zImgBtn' onclick=\"deleteData('" . $bar['notransaksi'] . "');\" title='Delete'></td>";
            if ($bar['approval'] == '') {
                $theTable .= "<td><a href='" . $bar['notransaksi'] . "' onclick=\"checkApproval(event,'" . $bar['notransaksi'] . "','approval');\">Approval</a>";
            } else {
                $theTable .= "<td>&nbsp;&nbsp;&nbsp;</td>";
            }
            if ($bar['posting'] != 0) {
                $theTable .= "<td><img src='images/skyblue/posting.png' class='zImgBtn' onclick=\"checkApproval(event,'" . $bar['notransaksi'] . "','posting');\" title='Posting'></td>";
            } else {
                $theTable .= "<td><img src='images/skyblue/posted.png' class='zImgBtn'  title='Posted></td>";
            }
            $theTable .= "<td><img src='images/skyblue/pdf.jpg' class='zImgBtn' onclick=\"detailPDF('" . $bar['notransaksi'] . "',event);\" title='Print Data Detail'></td>";
            $theTable .= "<td><img src='images/skyblue/zoom.png' class='zImgBtn' onclick=\"tampilDetail('" . $bar['notransaksi'] . "',event);\" title='Print Data Detail'></td>";
            $theTable .= "</tr>";
        }
    }

    $theTable .= "<tfoot id='tableFoot'><tr>";
    $theTable .= "<td colspan='15' style='text-align:center; vertical-align:middle;'>";
    $theTable .= makePage($page, $totalRow, SHOW_ROW_COUNT);
    $theTable .= '</td>';
    $theTable .= '</tr></tfoot>';

    $theTable .="</tbody></table></fieldset>";
    return $theTable;
}