<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'config/connection.php';
include_once 'lib/zLib.php';
('' != $_POST['method'] ? ($method = $_POST['method']) : ($method = $_GET['method']));
('' != $_POST['no_permintaan'] ? ($nomor = $_POST['no_permintaan']) : ($nomor = $_GET['no_permintaan']));
('' != $_POST['ckno_permintaan'] ? ($no_prmntan = $_POST['ckno_permintaan']) : ($no_prmntan = $_GET['ckno_permintaan']));
$tgl = tanggalsystem($_POST['tgl']);
$supplier_id = $_POST['id_supplier'];
$id_user = $_POST['user_id'];
$kd_brg = $_POST['kdbrg'];
$mtUang = $_POST['mtUang'];
$kurs = $_POST['kurs'];
$noUrut = $_POST['noUrut'];
$optBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$arrNmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optSat = makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
$optNmkry = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNmSup = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
$kdNopp = $_POST['kdNopp'];
$nilDiskon = $_POST['nilDiskon'];
$diskonPersen = $_POST['diskonPersen'];
$nilPPn = $_POST['nilPPn'];
$nilaiPermintaan = $_POST['nilaiPermintaan'];
$subTotal = $_POST['subTotal'];
$termPay = $_POST['termPay'];
$idFranco = $_POST['idFranco'];
$stockId = $_POST['stockId'];
$ketUraian = $_POST['ketUraian'];
$nmSupplier = $_POST['nmSupplier'];
switch ($method) {
    case 'getSupplierNm':
        $sSupplier = 'select namasupplier,supplierid from '.$dbname.".log_5supplier where namasupplier like '%".$nmSupplier."%' and kodekelompok='S001'";
        $qSupplier = mysql_query($sSupplier);
        echo '<fieldset><legend>'.$_SESSION['lang']['result']."</legend>\r\n                        <div style=\"overflow:auto;height:295px;width:455px;\">\r\n                        <table cellpading=1 border=0 class=sortbale>\r\n                        <thead>\r\n                        <tr class=rowheader>\r\n                        <td>No.</td>\r\n                        <td>".$_SESSION['lang']['kodesupplier']."</td>\r\n                        <td>".$_SESSION['lang']['namasupplier']."</td>\r\n                        </tr><tbody>\r\n                        ";
        while ($rSupplier = mysql_fetch_assoc($qSupplier)) {
            ++$no;
            echo "<tr class=rowcontent onclick=setData('".$rSupplier['supplierid']."')>\r\n                         <td>".$no."</td>\r\n                         <td>".$rSupplier['supplierid']."</td>\r\n                         <td>".$rSupplier['namasupplier']."</td>\r\n                    </tr>";
        }
        echo '</tbody></table></div>';

        break;
    case 'getNopp':
        echo '<fieldset><legend>'.$_SESSION['lang']['result']."</legend>\r\n                        <div style=\"overflow:auto;height:295px;width:455px;\">\r\n                        <table cellpading=1 border=0 cellspacing=1 class=sortbale>\r\n                        <thead>\r\n                        <tr class=rowheader>\r\n                        <td>No.</td>\r\n                        <td>".$_SESSION['lang']['nopp']."</td>\r\n                        \r\n                        </tr><tbody>\r\n                        ";
        $sSupplier = 'select distinct nopp from '.$dbname.".log_prapodt where nopp like '%".$kdNopp."%' and create_po='0'";
        $qSupplier = mysql_query($sSupplier);
        while ($rSupplier = mysql_fetch_assoc($qSupplier)) {
            ++$no;
            echo "<tr class=rowcontent onclick=setDataNopp('".$rSupplier['nopp']."')>\r\n                         <td>".$no."</td>\r\n                         <td>".$rSupplier['nopp']."</td>\r\n                         \r\n                    </tr>";
        }
        echo '</tbody></table></div>';

        break;
    case 'getNopp2':
        if (strlen($kdNopp) < 5) {
            exit('error: Min 4 character');
        }

        echo '<fieldset><legend>'.$_SESSION['lang']['result']."</legend>\r\n                        <div style=\"overflow:auto;height:295px;width:455px;\">\r\n                        <table cellpading=1 border=0 cellspacing=1 class=sortbale>\r\n                        <thead>\r\n                        <tr class=rowheader>\r\n                        <td>No.</td>\r\n                        <td>".$_SESSION['lang']['nopp']."</td>\r\n                        \r\n                        </tr><tbody>\r\n                        ";
        $sSupplier = 'select distinct nopp from '.$dbname.".log_perintaanhargaht where nopp like '%".$kdNopp."%'";
        $qSupplier = mysql_query($sSupplier);
        while ($rSupplier = mysql_fetch_assoc($qSupplier)) {
            ++$no;
            echo "<tr class=rowcontent onclick=setDataNopp('".$rSupplier['nopp']."')>\r\n                         <td>".$no."</td>\r\n                         <td>".$rSupplier['nopp']."</td>\r\n                         \r\n                    </tr>";
        }
        echo '</tbody></table></div>';

        break;
    case 'getBarangPP':
        if ('15' == $_SESSION['empl']['kodejabatan'] || '113' == $_SESSION['empl']['kodejabatan']) {
            $sql2 = 'select a.nopp, a.kodebarang, a.jumlah from  '.$dbname.".log_sudahpo_vsrealisasi_vw a  eft join ".$dbname.".log_permintaanhargadt b on (a.kodebarang = b.kodebarang and a.nopp = b.nopp) where b.kodebarang is null and b.nopp is null and (kodept='".$_POST['kdPt']."' and status!='3') and (selisih>0 or selisih is null)";
        } else {
            // $sql2 = 'select * from  '.$dbname.".log_sudahpo_vsrealisasi_vw  where (kodept='".$_POST['kdPt']."' and purchaser='".$_SESSION['standard']['userid']."' and status!='3') and (selisih>0 or selisih is null)";
            $sql2 = "select a.nopp, a.kodebarang, a.jumlah from ".$dbname.".log_sudahpo_vsrealisasi_vw a left join ".$dbname.".log_permintaanhargadt b on (a.kodebarang = b.kodebarang and a.nopp = b.nopp) where b.kodebarang is null and b.nopp is null and (a.kodept='".$_POST['kdPt']."' and a.purchaser='".$_SESSION['standard']['userid']."' and a.status!='3') and (a.selisih>0 or a.selisih is null)";
        }

        // var_dump($sql2);


        $qPp = mysql_query($sql2);
        while ($rPp = mysql_fetch_assoc($qPp)) {
            ++$no;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td style=width:20px>'.$no.'</td>';
            $tab .= "<td style='width:180px' id=nopplst_".$no.'>'.$rPp['nopp'].'</td>';
            $tab .= "<td style='width:88px' id=kodebrg_".$no.'>'.$rPp['kodebarang'].'</td>';
            $tab .= '<td style=width:380px>'.$optBarang[$rPp['kodebarang']].'</td>';
            $tab .= "<td style='width:62px' align=right id=jumlah_".$no.'>'.$rPp['jumlah'].'</td>';
            $tab .= '<td style=width:55px>'.$optSat[$rPp['kodebarang']].'</td>';
            $tab .= "<td  style='width:10px' align=center><input type=checkbox id=pilBrg_".$no.' /></td></tr>';
        }
        $tab .= '<tr><td colspan=5 align=center><button class=mybutton onclick=lanjutAdd() >'.$_SESSION['lang']['lanjut'].'</button></td></tr>';
        echo $tab;

        break;
    case 'loadSuppier':
        $sData = 'select nomor,supplierid,nourut from '.$dbname.".log_perintaanhargaht \r\n                 where nomor='".$_POST['notrans']."'\r\n                 order by nomor asc";
        $qData = mysql_query($sData);
        while ($rData = mysql_fetch_assoc($qData)) {
            ++$no;
            $sNmsup = 'select distinct namasupplier from '.$dbname.".log_5supplier where supplierid='".$rData['supplierid']."'";
            $qNmsup = mysql_query($sNmsup);
            $rNmsup = mysql_fetch_assoc($qNmsup);
            $tabl .= '<tr class=rowcontent>';
            $tabl .= '<td>'.$no.'</td>';
            $tabl .= '<td>'.$rData['nomor'].'</td>';
            $tabl .= '<td>'.$rNmsup['namasupplier'].'</td>';
            $tabl .= "<td>\r\n                    <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPer('".$rData['nomor']."','".$rData['nourut']."');\">\r\n                    <img src=images/application/application_view_detail.png class=resicon  title='".$_SESSION['lang']['keterangan']."' onclick=\"addKet('".$rData['nomor']."','".$rData['nourut']."','".$_SESSION['lang']['keterangan']."',event);\">\r\n                    </td>";
            $tabl .= '</tr>';
        }
        echo $tabl;

        break;
    case 'addData':
        $tgl = date('Ymd');
        if ('' == $_POST['notransaksi']) {
            foreach ($_POST['kdbrg'] as $row => $Act) {
                if (1 == $row) {
                    $kdbrg = $Act;
                    $jmlh = $_POST['jmlh'][$row];
                    $nopp = $_POST['lstnopp'][$row];
                    $optKdPT = makeOption($dbname, 'log_prapoht', 'nopp,kodeorg');
                    $kdOrgPt = $optKdPT[$nopp];
                }
            }
            $bln = substr($tgl, 4, 2);
            $thn = substr($tgl, 0, 4);
            $no = '/'.date('Y').'/DPH/'.$kdOrgPt;
            $ql = 'select `nomor` from '.$dbname.".`log_perintaanhargaht` where nomor like '%".$no."%' order by `nomor` desc limit 0,1";
            $qr = mysql_query($ql);
            $rp = mysql_fetch_object($qr);
            $awal = substr($rp->nomor, 0, 3);
            $awal = (int) $awal;
            $cekbln = substr($rp->nomor, 4, 2);
            $cekthn = substr($rp->nomor, 7, 4);
            if ($thn != $cekthn) {
                $awal = 1;
            } else {
                ++$awal;
            }

            $counter = addZero($awal, 3);
            $no_permintaan = $counter.'/'.$bln.'/'.$thn.'/DPH/'.$kdOrgPt;
        } else {
            $no_permintaan = $_POST['notransaksi'];
            $scek = 'select distinct * from '.$dbname.".log_perintaanhargaht \r\n                    where nomor='".$no_permintaan."' and supplierid='".$supplier_id."'";
            $qcek = mysql_query($scek);
            $rcek = mysql_num_rows($qcek);
            if (0 != $rcek) {
                exit('error: Data tersebut sudah ada');
            }
        }

        $ins = 'insert into '.$dbname.".log_perintaanhargaht \r\n                (nomor, tanggal, purchaser, supplierid,nourut) values \r\n                ('".$no_permintaan."','".$tgl."','".$_SESSION['standard']['userid']."','".$supplier_id."','".$_POST['norurut']."')";
        if (mysql_query($ins)) {
            foreach ($_POST['kdbrg'] as $row => $Act) {
                $kdbrg = $Act;
                $jmlh = $_POST['jmlh'][$row];
                $nopp = $_POST['lstnopp'][$row];
                $where = "nopp='".$nopp."' and kodebarang='".$kdbrg."'";
                $ketPP = makeOption($dbname, 'log_prapodt', 'kodebarang,keterangan', $where);
                $sqp = 'insert into '.$dbname.".log_permintaanhargadt (`nomor`,`kodebarang`,`jumlah`,nopp,nourut,keterangan) \r\n                        values('".$no_permintaan."','".$kdbrg."','".$jmlh."','".$nopp."','".$_POST['norurut']."','".$ketPP[$kdbrg]."')";
                if (!mysql_query($sqp)) {
                    echo $sqp;
                    echo 'Gagal,'.mysql_error($conn);
                    exit();
                }
            }
            ++$_POST['norurut'];
            echo $no_permintaan.'###'.$_POST['norurut'];
        } else {
            echo 'Gagal,'.$ins.'__'.mysql_error($conn);
        }

        break;
    case 'cari_pp':
        $limit = 25;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $sql = 'select * from '.$dbname.".log_perintaanhargaht where purchaser='".$_SESSION['standard']['userid']."' order by tanggal desc LIMIT ".$offset.','.$limit.'';
        $sql2 = 'select count(*) as jmlhrow from '.$dbname.".log_perintaanhargaht where purchaser='".$_SESSION['standard']['userid']."' order by tanggal desc";
        $query2 = mysql_query($sql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        if ($query = mysql_query($sql)) {
            while ($res2 = mysql_fetch_assoc($query)) {
                ++$no;
                $dtkr = 'select * from '.$dbname.".datakaryawan where karyawanid='".$res2['purchaser']."'";
                $qdtkr = mysql_query($dtkr);
                $rdtkr = mysql_fetch_object($qdtkr);
                $splr = 'select * from '.$dbname.".log_5supplier where supplierid='".$res2['supplierid']."'";
                $qsuplr = mysql_query($splr);
                $rsplr = mysql_fetch_object($qsuplr);
                if (0 != $res2['ppn']) {
                    $ppn = $res2['ppn'] / ($res2['subtotal'] - $res2['nilaidiskon']) * 100;
                }

                echo "<tr class=rowcontent>\r\n                                    <td>".$no."</td>\r\n                                    <td>".$res2['nomor']."</td>\r\n                                    <td align=center>".$res2['nourut']."</td>\r\n                                    <td>".tanggalnormal($res2['tanggal']).'</td>';
                echo '<td>'.$rsplr->namasupplier.'</td>';
                if ($res2['purchaser'] == $_SESSION['standard']['userid']) {
                    echo "\r\n                <td>\r\n                <img src=images/application/application_edit.png class=resicon  title='Edit Quotation Request' onclick=\"zPreview2('log_slave_save_permintaan_harga','".$res2['nomor']."','printContainer2');\">\r\n                <img src=images/plus.png class=resicon  title='Add more supplier ' onclick=\"addSupplierPlus('".$res2['nomor']."','".$res2['nourut']."');\">\r\n                <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPer1('".$res2['nomor']."','".$res2['nourut']."');\">\r\n                <img src=images/application/application_view_detail.png class=resicon  title='".$_SESSION['lang']['keterangan']."' onclick=\"addKet('".$res2['nomor']."','".$res2['nourut']."','".$_SESSION['lang']['keterangan']."',event);\">\r\n                <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_perintaanhargaht','".$res2['nomor'].','.$res2['nourut']."','','log_slave_print_permintaan_penawaran',event);\">\r\n                <img onclick=datakeExcel(event,'".$res2['nomor']."') src=images/excel.jpg class=resicon title='MS.Excel'>      \r\n                </td>";
                } else {
                    echo "<td>\r\n                                                        <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_perintaanhargaht','".$res2['nomor'].','.$res2['nourut']."','','log_slave_print_permintaan_penawaran',event);\">\r\n                                                        <img onclick=datakeExcel(event,'".$res2['nomor'].") src=images/excel.jpg class=resicon title='MS.Excel'>          \r\n                                                        </td>";
                }

                echo "\r\n            </tr>";
            }
            echo "\r\n                     <tr><td colspan=6 align=center>\r\n                    ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n                    <button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                    <button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                    </td>\r\n                    </tr><input type=hidden id=nopp_".$no.' name=nopp_'.$no." value='".$bar['nopp']."' />";
        } else {
            echo 'Gagal,'.mysql_error($conn);
        }

        break;
    case 'deleted':
        $strx = 'delete from '.$dbname.".log_perintaanhargaht where nomor='".$nomor."' and nourut='".$_POST['nourut']."'";
        if (!mysql_query($strx)) {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        } else {
            $strq = 'delete from '.$dbname.".log_permintaanhargadt where nomor='".$nomor."' and nourut='".$_POST['nourut']."'";
            if (!mysql_query($strq)) {
                echo ' Gagal,'.addslashes(mysql_error($conn));
            }
        }

        break;
    case 'cari_permintaan':
        $limit = 25;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        if ('' != $_POST['txtSearch']) {
            $where = " and nomor LIKE  '%".$_POST['txtSearch']."%'";
        }

        if ('' != $_POST['tglCari']) {
            $txt_tgl = tanggalsystem($_POST['tglCari']);
            $txt_tgl_t = substr($txt_tgl, 0, 4);
            $txt_tgl_b = substr($txt_tgl, 4, 2);
            $txt_tgl_tg = substr($txt_tgl, 6, 2);
            $txt_tgl = $txt_tgl_t.'-'.$txt_tgl_b.'-'.$txt_tgl_tg;
            $where .= " and tanggal LIKE '".$txt_tgl."'";
        }

        if ('' != $_POST['txtNopp']) {
            $where .= " and nopp='".$_POST['txtNopp']."'";
        }

        if ('' != $_POST['txtNmbrg']) {
            $sCek = 'select distinct nomor from '.$dbname.".log_permintaanhargadt where kodebarang in \r\n                      (select distinct kodebarang from ".$dbname.".log_5masterbarang where namabarang like '%".$_POST['txtNmbrg']."%')";
            $qCek = mysql_query($sCek);
            while ($rCek = mysql_fetch_assoc($qCek)) {
                ++$ard;
                if (1 == $ard) {
                    $dtr = "'".$rCek['nomor']."'";
                } else {
                    $dtr .= ",'".$rCek['nomor']."'";
                }
            }
            $where .= ' and nomor in ('.$dtr.')';
        }

        $strx = 'SELECT * FROM '.$dbname.".log_perintaanhargaht where purchaser='".$_SESSION['standard']['userid']."' ".$where.' ORDER BY tanggal DESC LIMIT '.$offset.','.$limit.'';
        $sql2 = 'select count(*) as jmlhrow from '.$dbname.".log_perintaanhargaht where purchaser='".$_SESSION['standard']['userid']."' ".$where.' order by tanggal desc';
        $query2 = mysql_query($sql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        if ($query = mysql_query($strx)) {
            while ($res2 = mysql_fetch_assoc($query)) {
                ++$no;
                $dtkr = 'select * from '.$dbname.".datakaryawan where karyawanid='".$res2['purchaser']."'";
                $qdtkr = mysql_query($dtkr);
                $rdtkr = mysql_fetch_object($qdtkr);
                $splr = 'select * from '.$dbname.".log_5supplier where supplierid='".$res2['supplierid']."'";
                $qsuplr = mysql_query($splr);
                $rsplr = mysql_fetch_object($qsuplr);
                if (0 != $res2['ppn']) {
                    $ppn = $res2['ppn'] / ($res2['subtotal'] - $res2['nilaidiskon']) * 100;
                }

                echo "<tr class=rowcontent>\r\n                                    <td>".$no."</td>\r\n                                    <td>".$res2['nomor']."</td>\r\n                                    <td>".$res2['nourut']."</td>\r\n                                    <td>".tanggalnormal($res2['tanggal']).'</td>';
                echo '<td>'.$rsplr->namasupplier.'</td>';
                if ($res2['purchaser'] == $_SESSION['standard']['userid']) {
                    echo "\r\n                <td>\r\n                <img src=images/application/application_edit.png class=resicon  title='Edit Quotation Request' onclick=\"zPreview2('log_slave_save_permintaan_harga','".$res2['nomor']."','printContainer2');\">\r\n                <img src=images/plus.png class=resicon  title='Tambah Supplier ' onclick=\"addSupplierPlus('".$res2['nomor']."','".$res2['nourut']."');\">\r\n                <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPer1('".$res2['nomor']."','".$res2['nourut']."');\">\r\n                <img src=images/application/application_view_detail.png class=resicon  title='".$_SESSION['lang']['keterangan']."' onclick=\"addKet('".$res2['nomor']."','".$res2['nourut']."','".$_SESSION['lang']['keterangan']."',event);\">\r\n                <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_perintaanhargaht','".$res2['nomor'].','.$res2['nourut']."','','log_slave_print_permintaan_penawaran',event);\">\r\n                <img onclick=datakeExcel(event,'".$res2['nomor']."') src=images/excel.jpg class=resicon title='MS.Excel'>      \r\n                </td>";
                } else {
                    echo "<td>\r\n                                                        <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_perintaanhargaht','".$res2['nomor'].','.$res2['nourut']."','','log_slave_print_permintaan_penawaran',event);\">\r\n                                                        <img onclick=datakeExcel(event,'".$res2['nomor'].") src=images/excel.jpg class=resicon title='MS.Excel'>          \r\n                                                        </td>";
                }

                echo "\r\n            </tr>";
            }
            echo "\r\n                     <tr><td colspan=6 align=center>\r\n                    ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n                    <button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                    <button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                    </td>\r\n                    </tr><input type=hidden id=nopp_".$no.' name=nopp_'.$no." value='".$bar['nopp']."' />";
        } else {
            echo 'Gagal,'.mysql_error($conn);
        }

        break;
    case 'get_nopp':
        $optNopp = '';
        $sql = 'SELECT a.nopp FROM '.$dbname.'.`log_prapodt` a left join '.$dbname.".`log_prapoht` b on a.nopp=b.nopp where b.close='2' \r\n                    and (a.create_po is null or create_po='') \r\n                    and a.kodebarang='".$kd_brg."'";
        $query = mysql_query($sql);
        while ($res = mysql_fetch_assoc($query)) {
            $optNopp .= '<option value='.$res['nopp'].'>'.$res['nopp'].'</option>';
        }
        echo $optNopp;

        break;
    case 'getSpek':
        $sSpek = 'select spesifikasi from '.$dbname.".log_5photobarang where kodebarang='".$kd_brg."'";
        $qSpek = mysql_query($sSpek);
        $rSpek = mysql_fetch_assoc($qSpek);
        echo $rSpek['spesifikasi'];

        break;
    case 'getKurs':
        $tgl = date('Ymd');
        $sGet = 'select distinct kurs from '.$dbname.".setup_matauangrate where kode='".$mtUang."' and daritanggal='".$tgl."'";
        $qGet = mysql_query($sGet);
        $rGet = mysql_fetch_assoc($qGet);
        if ('IDR' == $mtUang) {
            $rGet['kurs'] = 1;
        } else {
            if (0 != $rGet['kurs']) {
                $rGet['kurs'] = $rGet['kurs'];
            } else {
                $rGet['kurs'] = 1;
            }
        }

        echo $rGet['kurs'];

        break;
    case 'printExcel':
        $optTermPay = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $optStock = $optTermPay;
        $optKrm = $optTermPay;
        $arrOptTerm = [1 => 'Cash', 2 => 'Credit 2 weeks', 3 => 'Credit 1 month', 4 => 'Spesific Terms', 5 => 'Down Payment'];
        $arrStock = [1 => 'Ready Stock', 2 => 'Not Ready'];
        $sdtheder = 'select distinct * from '.$dbname.".log_perintaanhargaht where nomor='".$_GET['no_permintaan']."'";
        $qdtheder = mysql_query($sdtheder);
        while ($rdtheder = mysql_fetch_assoc($qdtheder)) {
            if ('IDR' == $rdtheder['matauang']) {
                $rdtheder['kurs'] = 1;
            }

            $dtNomor[] = $rdtheder['nourut'];
            $dtSupp[$rdtheder['nourut']] = $rdtheder['supplierid'];
            $dtFranco[$rdtheder['nourut']] = $rdtheder['id_franco'];
            $dtStock[$rdtheder['nourut']] = $rdtheder['stock'];
            $dtCattn[$rdtheder['nourut']] = $rdtheder['catatan'];
            $dtSisbyr[$rdtheder['nourut']] = $rdtheder['sisbayar'];
            $dtPpn[$rdtheder['nourut']] = $rdtheder['ppn'];
            $dtSbtotal[$rdtheder['nourut']] = $rdtheder['kurs'] * $rdtheder['subtotal'];
            $dtDisknPrsn[$rdtheder['nourut']] = $rdtheder['diskonpersen'];
            $dtNildis[$rdtheder['nourut']] = $rdtheder['kurs'] * $rdtheder['nilaidiskon'];
            $dtNilPer[$rdtheder['nourut']] = $rdtheder['kurs'] * $rdtheder['nilaipermintaan'];
            $dtMtuang[$rdtheder['nourut']] = $rdtheder['matauang'];
            $dtTglDr[$rdtheder['nourut']] = $rdtheder['tgldari'];
            $dtTglSmp[$rdtheder['nourut']] = $rdtheder['tglsmp'];
            $kurs[$rdtheder['nourut']] = $rdtheder['kurs'];
            $dtCttn[$rdtheder['nourut']] = $rdtheder['catatan'];
        }
        $sDetail = 'select distinct kodebarang,jumlah,nomor,harga,nopp,merk,nourut,keterangan from '.$dbname.".log_permintaanhargadt where nomor='".$_GET['no_permintaan']."' ";
        $qDetail = mysql_query($sDetail);
        while ($rDetail = mysql_fetch_assoc($qDetail)) {
            if ('' == $rDetail['harga']) {
                $rDetail['harga'] = 0;
            }

            $dtSub[$rDetail['nourut']][$rDetail['kodebarang']] = $rDetail['jumlah'] * (float) ($rDetail['harga']) * $kurs[$rDetail['nourut']];
            $dtHarga[$rDetail['nourut']][$rDetail['kodebarang']] = $kurs[$rDetail['nourut']] * $rDetail['harga'];
            $dtMerk[$rDetail['nourut']][$rDetail['kodebarang']] = $rDetail['merk'];
            $dtNopp[$rDetail['nourut']] = $rDetail['nopp'];
            $arrJmlh[$rDetail['kodebarang']] = $rDetail['jumlah'];
            $arrKet[$rDetail['kodebarang']] = $rDetail['keterangan'];
            $listBarang[$rDetail['kodebarang']] = $rDetail['kodebarang'];
        }
        $tab = "<table cellspacing=1 border=1 class=sortable >\r\n                <thead class=rowheader>\r\n                <tr>\r\n                <td bgcolor=#DEDEDE rowspan=2 align=center>No.</td>\r\n                <td bgcolor=#DEDEDE rowspan=2 align=center>".$_SESSION['lang']['kodebarang']."</td>\r\n                <td bgcolor=#DEDEDE rowspan=2 align=center>".$_SESSION['lang']['namabarang']."</td>\r\n                <td bgcolor=#DEDEDE rowspan=2 align=center>".$_SESSION['lang']['jumlah']."</td>\r\n                <td bgcolor=#DEDEDE rowspan=2 align=center>".$_SESSION['lang']['satuan']."</td>\r\n                <td bgcolor=#DEDEDE rowspan=2 align=center>".$_SESSION['lang']['keterangan']."</td>\r\n                <td bgcolor=#DEDEDE rowspan=2 align=center>".$_SESSION['lang']['nopp'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            $tab .= '<td bgcolor=#DEDEDE colspan=3 align=center>'.$optNmSup[$dtSupp[$ard]].'</td>';
        }
        $tab .= '</tr><tr>';
        foreach ($dtNomor as $brs) {
            $tab .= '<td   bgcolor=#DEDEDE align=center width=85px>'.$_SESSION['lang']['merk'].'</td><td  align=center width=85px bgcolor=#DEDEDE>'.$_SESSION['lang']['harga'].'</td><td align=center width=85px bgcolor=#DEDEDE>'.$_SESSION['lang']['subtotal'].'</td>';
        }
        $tab .= '<tr>';
        $tab .= "</thead>\r\n                <tbody>";
        $totRow = count($dtNomor);
        $totBrg = count($listBarang);
        foreach ($listBarang as $brsKdBrg) {
            ++$no;
            $tab .= "<tr class='rowcontent'>";
            $tab .= '<td>'.$no.'</td>';
            $tab .= "<td id='kd_brg_".$no."'>".$brsKdBrg.'</td>';
            $tab .= "<td title='".$arrNmBrg[$brsKdBrg]."'>".$arrNmBrg[$brsKdBrg].'</td>';
            $tab .= "<td align=right id='jumlah_".$no."'>".$arrJmlh[$brsKdBrg].'</td>';
            $tab .= '<td align=center>'.$optSat[$brsKdBrg].'</td>';
            $tab .= '<td align=center>'.$arrKet[$brsKdBrg].'</td>';
            $tab .= '<td align=left>'.$dtNopp[$ard].'</td>';
            $ard = 0;
            foreach ($dtNomor as $brs) {
                ++$ard;
                $tab .= '<td align=left>'.$dtMerk[$ard][$brsKdBrg].'</td>';
                $tab .= '<td align=right>'.number_format($dtHarga[$ard][$brsKdBrg], 2).'</td>';
                $tab .= '<td align=right>'.number_format($dtSub[$ard][$brsKdBrg], 2).'</td>';
            }
            $tab .= '</tr>';
        }
        $tab .= "<tr class='rowcontent'>";
        $tab .= '<td rowspan=4 colspan=5 valign=top align=left>&nbsp</td><td colspan=2>'.$_SESSION['lang']['subtotal'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            $tab .= '<td align=right colspan=3 id=total_harga_po_'.$ard.'>'.number_format($dtSbtotal[$ard], 2).'</td>';
        }
        $tab .= '</tr>';
        $tab .= "<tr class='rowcontent'><td colspan=2>".$_SESSION['lang']['diskon'].'</td>';
        foreach ($dtNomor as $brs) {
            ++$nor;
            $tab .= '<td align=right colspan=2>'.$dtDisknPrsn[$nor].'%</td>';
            $tab .= '<td align=right>'.number_format($dtNildis[$nor], 2).'</td>';
        }
        $tab .= '</tr>';
        $tab .= "<tr class='rowcontent'><td colspan=2>".$_SESSION['lang']['ppn'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            $persen[$ard] = $dtPpn[$ard] / ($dtSbtotal[$ard] - $dtNildis[$ard]) * 100;
            $tab .= '<td align=right colspan=2>'.$persen[$ard].'</td>';
            $tab .= '<td align=right >'.number_format($dtPPN[$ard], 2).'</td>';
        }
        $tab .= '</tr>';
        $tab .= "<tr class='rowcontent'><td colspan=2>".$_SESSION['lang']['grnd_total'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            $tab .= '<td align=right colspan=3 id=grand_total_'.$ard.'>'.number_format($dtNilPer[$ard], 2).'</td>';
        }
        $tab .= '</tr>';
        $tab .= "<tr class='rowcontent'><td rowspan=10 colspan=5 valign=top align=left>".$_SESSION['lang']['rekomendasi'].'</td>';
        $tab .= '<td colspan=2>'.$_SESSION['lang']['nopermintaan'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            $tab .= '<td colspan=3>'.$_POST['notransaksi'].'</td>';
        }
        $tab .= '</tr>';
        $tab .= "<tr class='rowcontent'><td colspan=2>".$_SESSION['lang']['matauang'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            $tab .= '<td colspan=3>'.$dtMtuang[$ard].'</td>';
        }
        $tab .= '</tr>';
        $tab .= "<tr class='rowcontent'><td colspan=2>".$_SESSION['lang']['kurs'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            $tab .= '<td colspan=3>'.$kurs[$ard].'</td>';
        }
        $tab .= '</tr>';
        $tab .= "<tr class='rowcontent'><td colspan=2>".$_SESSION['lang']['tgldari'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            $tab .= '<td colspan=3>'.$dtTglDr[$ard].'</td>';
        }
        $tab .= '</tr>';
        $tab .= "<tr class='rowcontent'><td colspan=2>".$_SESSION['lang']['tglsmp'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            $tab .= '<td colspan=3>'.$dtTglSmp[$ard].'</td>';
        }
        $tab .= '</tr>';
        $tab .= "<tr class='rowcontent'><td colspan=2>".$_SESSION['lang']['syaratPem'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            $tab .= '<td colspan=3>'.$arrOptTerm[$dtSisbyr[$ard]].'</td>';
        }
        $tab .= '</tr>';
        $tab .= "<tr class='rowcontent'><td colspan=2>".$_SESSION['lang']['stock'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            $tab .= '<td colspan=3>'.$arrStock[$dtStock[$ard]].'</td>';
        }
        $tab .= '</tr>';
        $tab .= "<tr class='rowcontent'><td colspan=2>".$_SESSION['lang']['almt_kirim'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            $tab .= '<td colspan=3>'.$arrFranco[$dtFranco[$ard]].'</td>';
        }
        $tab .= '</tr>';
        $tab .= "<tr class='rowcontent'><td colspan=2>".$_SESSION['lang']['keterangan'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            $tab .= '<td align=justify colspan=3>'.$dtCttn[$ard].'</td>';
        }
        $tab .= '</tr>';
        $tab .= '</tbody></table>';
        $tab .= 'Print Time : '.date('H:i:s, d/m/Y').'<br>By : '.$_SESSION['empl']['name'];
        $tglSkrg = date('Ymd');
        $nop_ = 'form_permintaan_harga';
        if (0 < strlen($tab)) {
            if ($handle = opendir('tempExcel')) {
                while (false != ($file = readdir($handle))) {
                    if ('.' != $file && '..' != $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $tab)) {
                echo "<script language=javascript1.2>\r\n\t\t\t\t\t\t\t\t\tparent.window.alert('Can't convert to excel format');\r\n\t\t\t\t\t\t\t\t\t</script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n\t\t\t\t\t\t\t\t\twindow.location='tempExcel/".$nop_.".xls';\r\n\t\t\t\t\t\t\t\t\t</script>";
            closedir($handle);
        }

        break;
    case 'getNotifikasi':
		#showerror();
        $Sorg = 'select distinct kodeorganisasi from '.$dbname.".organisasi where tipe='PT'";
        #echo $Sorg;
		$dafUnit = array();
		$qOrg = mysql_query($Sorg);
		#pre($qOrg);
        while ($rOrg = mysql_fetch_assoc($qOrg)) {
			#echo "A";
            $dafUnit[] = $rOrg['kodeorganisasi'];
        }
        echo '<table border=0>';
		#pre($dafUnit);
		$ared = 0;
        foreach ($dafUnit as $lstKdOrg) {
			#echo"a";
            ++$ared;
            if (1 == $ared) {
                echo '<tr>';
            }

            if ('15' == $_SESSION['empl']['kodejabatan'] || '113' == $_SESSION['empl']['kodejabatan']) {
                $sList = 'select count(*) as jmlhJob from  '.$dbname.".log_sudahpo_vsrealisasi_vw a left join ".$dbname.".log_permintaanhargadt b on (a.kodebarang = b.kodebarang and a.nopp = b.nopp) where b.kodebarang is null and b.nopp is null and (a.kodept='".$lstKdOrg."' and a.status!='3') and (a.selisih>0 or a.selisih is null)";
            } else {
                $sList = 'select count(*) as jmlhJob from  '.$dbname.".log_sudahpo_vsrealisasi_vw a left join ".$dbname.".log_permintaanhargadt b on (a.kodebarang = b.kodebarang and a.nopp = b.nopp) where  b.kodebarang is null and b.nopp is null and (a.kodept='".$lstKdOrg."' and a.purchaser='".$_SESSION['standard']['userid']."' and a.status!='3') and (a.selisih>0 or a.selisih is null)";

            }
			#echo $sList;
            $qList = mysql_query($sList);
            $rBaros = mysql_num_rows($qList);
            $rList = mysql_fetch_assoc($qList);
            if (0 != (int) ($rList['jmlhJob'])) {
                if ('' == $rList['jmlhJob']) {
                    $rList['jmlhJob'] = 0;
                }

                if (1 == $_POST['status']) {
                    echo '<td>'.$lstKdOrg.'</td><td>: '.$rList['jmlhJob'].'</td>';
                } else {
                    echo '<td>'.$lstKdOrg."</td><td>: <a href='#' onclick=\"getDtPP('".$lstKdOrg."')\">".$rList['jmlhJob'].'</a></td>';
                }
            }

            if (5 == $ared) {
                echo '</tr>';
                $ared = 0;
            }
        }
        echo '</table>';

        break;
    case 'cekBarang':
        foreach ($_POST['lstnopp'] as $row => $Rslt) {
            for ($a = 0; $a < $row; ++$a) {
                for ($b = 0; $b < $_POST['baris']; ++$b) {
                    if ($a != $b && $_POST['kdbrg'][$a] == $_POST['kdbrg'][$b]) {
                        ++$cek;
                        $cekBrg2 = $_POST['kdbrg'][$a];
                    }
                }
            }
            if (0 != $cek) {
                echo 'warning:Kodebarang : '.$cekBrg2.' Lebih Dari Satu';
                exit();
            }
        }

        break;
    case 'listBarangDetail':
        $tab .= '<tr class=rowcontent><td colspan=7>&nbsp;</td></tr>';
        $sPp = 'select distinct * from '.$dbname.". log_permintaanhargadt where nomor='".$_POST['notransaksi']."' and nourut='".$_POST['nourut']."'";
        $qPp = mysql_query($sPp) || exit(msyql_error($conn));
        while ($rPp = mysql_fetch_assoc($qPp)) {
            ++$no;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td style=width:20px>'.$no.'</td>';
            $tab .= "<td style='width:180px' id=nopplst_".$no.'>'.$rPp['nopp'].'</td>';
            $tab .= "<td style='width:88px' id=kodebrg_".$no.'>'.$rPp['kodebarang'].'</td>';
            $tab .= '<td style=width:380px>'.$optBarang[$rPp['kodebarang']].'</td>';
            $tab .= "<td style='width:62px' align=right id=jumlah_".$no.'>'.$rPp['jumlah'].'</td>';
            $tab .= '<td style=width:55px>'.$optSat[$rPp['kodebarang']].'</td>';
            $tab .= "<td  style='width:10px' align=center><input type=checkbox id=pilBrg_".$no.' checked /></td></tr>';
        }
        echo $tab;

        break;
    case 'preview2':
        $formPil = 1;
        $optTermPay = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $optStock = $optTermPay;
        $optKrm = $optTermPay;
        $arrOptTerm = [1 => 'Cash', 2 => 'Credit 2 weeks', 3 => 'Credit 1 month', 4 => 'Spesific Terms', 5 => 'Down Payment'];
        $arrStock = [1 => 'Ready Stock', 2 => 'Not Ready'];
        $sdtheder = 'select distinct * from '.$dbname.".log_perintaanhargaht where nomor='".$_POST['notransaksi']."'";
        $qdtheder = mysql_query($sdtheder);
        while ($rdtheder = mysql_fetch_assoc($qdtheder)) {
            $dtNomor[] = $rdtheder['nourut'];
            $dtSupp[$rdtheder['nourut']] = $rdtheder['supplierid'];
            $dtFranco[$rdtheder['nourut']] = $rdtheder['id_franco'];
            $dtStock[$rdtheder['nourut']] = $rdtheder['stock'];
            $dtCattn[$rdtheder['nourut']] = $rdtheder['catatan'];
            $dtSisbyr[$rdtheder['nourut']] = $rdtheder['sisbayar'];
            $dtPpn[$rdtheder['nourut']] = $rdtheder['ppn'];
            $dtSbtotal[$rdtheder['nourut']] = $rdtheder['subtotal'];
            $dtDisknPrsn[$rdtheder['nourut']] = $rdtheder['diskonpersen'];
            $dtNildis[$rdtheder['nourut']] = $rdtheder['nilaidiskon'];
            $dtNilPer[$rdtheder['nourut']] = $rdtheder['nilaipermintaan'];
            $dtMtuang[$rdtheder['nourut']] = $rdtheder['matauang'];
            $dtTglDr[$rdtheder['nourut']] = $rdtheder['tgldari'];
            $dtTglSmp[$rdtheder['nourut']] = $rdtheder['tglsmp'];
            $kurs[$rdtheder['nourut']] = $rdtheder['kurs'];
        }
        $sDetail = 'select distinct kodebarang,jumlah,nomor,harga,merk,nourut from '.$dbname.".log_permintaanhargadt where nomor='".$_POST['notransaksi']."' ";
        $qDetail = mysql_query($sDetail);
        while ($rDetail = mysql_fetch_assoc($qDetail)) {
            if ('' == $rDetail['harga']) {
                $rDetail['harga'] = 0;
            }

            $dtSub[$rDetail['nourut']][$rDetail['kodebarang']] = (float) ($rDetail['jumlah']) * (float) ($rDetail['harga']);
            $dtHarga[$rDetail['nourut']][$rDetail['kodebarang']] = $rDetail['harga'];
            $dtMerk[$rDetail['nourut']][$rDetail['kodebarang']] = $rDetail['merk'];
            $arrJmlh[$rDetail['kodebarang']] = $rDetail['jumlah'];
            $listBarang[$rDetail['kodebarang']] = $rDetail['kodebarang'];
        }
        $tab = "<table cellspacing=1 border=0 class=sortable >\r\n                <thead class=rowheader>\r\n                <tr>\r\n                <td rowspan=2 align=center>No.</td>\r\n                <td rowspan=2 align=center>".$_SESSION['lang']['kodebarang']."</td>\r\n                <td rowspan=2 align=center>".$_SESSION['lang']['namabarang']."</td>\r\n                <td rowspan=2 align=center>".$_SESSION['lang']['jumlah']."</td>\r\n                <td rowspan=2 align=center>".$_SESSION['lang']['satuan'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            $optSupplier = '';
            $sql = 'select namasupplier,supplierid from '.$dbname.'.log_5supplier order by namasupplier asc';
            $query = mysql_query($sql);
            while ($res = mysql_fetch_assoc($query)) {
                $optSupplier .= "<option value='".$res['supplierid']."' ".(($res['supplierid'] == $dtSupp[$ard] ? 'selected' : '')).'>'.$res['namasupplier'].'</option>';
            }
            $tab .= '<td colspan=3 align=center><select id=supplierId_'.$ard.'>'.$optSupplier.'</select></td>';
        }
        $tab .= '</tr><tr>';
        foreach ($dtNomor as $brs) {
            $tab .= '<td  align=center width=85px>'.$_SESSION['lang']['merk'].'</td><td  align=center width=85px>'.$_SESSION['lang']['harga'].'</td><td align=center width=85px>'.$_SESSION['lang']['subtotal'].'</td>';
        }
        $tab .= '<tr>';
        $tab .= "</thead>\r\n                <tbody>";
        $totRow = count($dtNomor);
        $totBrg = count($listBarang);
        foreach ($listBarang as $brsKdBrg) {
            ++$no;
            $tab .= "<tr class='rowcontent'>";
            $tab .= '<td>'.$no.'</td>';
            $tab .= "<td id='kd_brg_".$no."'>".$brsKdBrg.'</td>';
            $tab .= "<td title='".$arrNmBrg[$brsKdBrg]."'>".$arrNmBrg[$brsKdBrg].'</td>';
            $tab .= "<td align=right id='jumlah_".$no."'>".$arrJmlh[$brsKdBrg].'</td>';
            $tab .= '<td align=center>'.$optSat[$brsKdBrg].'</td>';
            $ard = 0;
            foreach ($dtNomor as $brs) {
                ++$ard;
                if ('1' != $formPil) {
                    $tab .= '<td align=left>'.$dtMerk[$ard][$brsKdBrg].'</td>';
                    $tab .= '<td align=right>'.number_format($dtHarga[$ard][$brsKdBrg], 2).'</td>';
                    $tab .= '<td align=right>'.number_format($dtSub[$ard][$brsKdBrg], 2).'</td>';
                } else {
                    $tab .= '<td align=right><input type=text id=merk_'.$no.'_'.$ard." value='".$dtMerk[$ard][$brsKdBrg]."' class='myinputtext' onkeypress='return tanpa_kutip(event)' maxlength=50 style='width:85px' /></td>";
                    $tab .= '<td align=right><input type=text id=price_'.$no.'_'.$ard." value='".$dtHarga[$ard][$brsKdBrg]."' class='myinputtextnumber' onkeypress='return angka_doang(event)' onfocus='normal_number(".$no.','.$ard.','.$totBrg.")' onkeyup='calculate(".$no.','.$ard.','.$totBrg.")' style='width:85px' /></td>";
                    $tab .= '<td align=right><input type=text id=total_'.$no.'_'.$ard." disabled value='".$dtSub[$ard][$brsKdBrg]."'  class='myinputtextnumber' onkeypress='return angka_doang(event)' style='width:85px'  /></td>";
                }
            }
            $tab .= '</tr>';
        }
        $tab .= "<tr class='rowcontent'>";
        $tab .= '<td rowspan=4 colspan=3 valign=top align=left>&nbsp</td><td colspan=2>'.$_SESSION['lang']['subtotal'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            $tab .= '<td align=right colspan=3 id=total_harga_po_'.$ard.'>'.$dtSbtotal[$ard].'</td>';
        }
        $tab .= '</tr>';
        $tab .= "<tr class='rowcontent'><td colspan=2>".$_SESSION['lang']['diskon'].'</td>';
        foreach ($dtNomor as $brs) {
            ++$nor;
            if ('1' != $formPil) {
                $tab .= '<td align=right colspan=2>'.number_format($dtDisknPrsn[$nor], 2).'%</td>';
                $tab .= '<td align=right>'.number_format($dtNildis[$nor], 2).'</td>';
            } else {
                $tab .= '<td align=right colspan=2><input type=text  id=diskon_'.$nor.' name=diskon_'.$nor.' class=myinputtextnumber onkeyup=calculate_diskon('.$nor.') maxlength=3 onkeypress=return angka_doang(event) onblur="getZero('.$nor.")\" value='".$dtDisknPrsn[$nor]."' style='width:85px'  /></td>";
                $tab .= '<td align=right><input type=text  id=angDiskon_'.$nor.' name=angDiskon_'.$nor.' class=myinputtextnumber  onkeyup=calculate_angDiskon('.$nor.') onkeypress=return angka_doang(event) onblur="getZero('.$nor.")\" value='".$dtNildis[$nor]."' style='width:85px' /></td>";
            }
        }
        $tab .= '</tr>';
        $tab .= "<tr class='rowcontent'><td colspan=2>".$_SESSION['lang']['ppn'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            if ('1' != $formPil) {
                $tab .= '<td align=right colspan=3>'.number_format($dtPPN[$ard], 2).'</td>';
            } else {
                $persen[$ard] = $dtPpn[$ard] / ($dtSbtotal[$ard] - $dtNildis[$ard]) * 100;
                $tab .= '<td align=right colspan=2><input type=text  id=ppN_'.$ard.' name=ppN_'.$ard.' class=myinputtextnumber  onkeyup=calculatePpn('.$ard.')  maxlength=2  onkeypress=return angka_doang(event) onblur="getZero('.$ard.")\"  value='".$persen[$ard]."' style='width:85px' /></td>";
                $tab .= '<td align=right><input type=text  id=ppn_'.$ard.' name=ppn_'.$ard." class=myinputtextnumber  disabled value='".$dtPpn[$ard]."' style='width:85px' /></td>";
            }
        }
        $tab .= '</tr>';
        $tab .= "<tr class='rowcontent'><td colspan=2>".$_SESSION['lang']['grnd_total'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            $tab .= '<td align=right colspan=3 id=grand_total_'.$ard.'>'.number_format($dtNilPer[$ard], 2).'</td>';
        }
        $tab .= '</tr>';
        $tab .= "<tr class='rowcontent'><td rowspan=10 colspan=3 valign=top align=left>".$_SESSION['lang']['rekomendasi'].'</td>';
        $tab .= '<td colspan=2>'.$_SESSION['lang']['nopermintaan'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            if ('1' != $formPil) {
                $tab .= '<td colspan=3>'.$_POST['notransaksi'].'</td>';
            } else {
                $tab .= '<td colspan=3><input type=text disabled id=no_prmntan_'.$ard." value='".$_POST['notransaksi']."' class=myinputtext style='width:150px' /></td>";
            }
        }
        $tab .= '</tr>';
        $tab .= "<tr class='rowcontent'><td colspan=2>".$_SESSION['lang']['matauang'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            $optMt = '';
            $optMt = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
            $sMt = 'select kode,kodeiso from '.$dbname.'.setup_matauang order by kode desc';
            $qMt = mysql_query($sMt);
            while ($rMt = mysql_fetch_assoc($qMt)) {
                if ('' != $dtMtuang[$ard]) {
                    $optMt .= '<option value='.$rMt['kode'].' '.(($dtMtuang[$ard] == $rMt['kode'] ? 'selected' : ' ')).'>'.$rMt['kodeiso'].'</option>';
                } else {
                    $optMt .= '<option value='.$rMt['kode'].'>'.$rMt['kodeiso'].'</option>';
                }
            }
            if ('1' != $formPil) {
                $tab .= '<td colspan=3>'.$dtMtuang[$ard].'</td>';
            } else {
                $tab .= '<td colspan=3><select id="mtUang_'.$ard.'" name="mtUang_'.$ard.'" style="width:150px;" >'.$optMt.'</select></td>';
            }
        }
        $tab .= '</tr>';
        $tab .= "<tr class='rowcontent'><td colspan=2>".$_SESSION['lang']['kurs'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            if ('1' != $formPil) {
                $tab .= '<td colspan=3>'.$kurs[$ard].'</td>';
            } else {
                $tab .= '<td colspan=3><input type="text" class="myinputtextnumber" id="Kurs_'.$ard.'" name="Kurs_'.$ard.'" style="width:150px;" onkeypress="return angka_doang(event)" value='.$kurs[$ard].'  /></td>';
            }
        }
        $tab .= '</tr>';
        $tab .= "<tr class='rowcontent'><td colspan=2>".$_SESSION['lang']['tgldari'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            if ('1' != $formPil) {
                $tab .= '<td colspan=3>'.$dtTglDr[$ard].'</td>';
            } else {
                $tab .= "<td colspan=3><input type=text class=myinputtext style='width:150px' id=tgl_dari_".$ard." onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 value='".tanggalnormal($dtTglDr[$ard])."' /></td>";
            }
        }
        $tab .= '</tr>';
        $tab .= "<tr class='rowcontent'><td colspan=2>".$_SESSION['lang']['tglsmp'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            if ('1' != $formPil) {
                $tab .= '<td colspan=3>'.$dtTglSmp[$ard].'</td>';
            } else {
                $tab .= "<td colspan=3><input type=text class=myinputtext style='width:150px' id=tgl_smp_".$ard." onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 value='".tanggalnormal($dtTglSmp[$ard])."' /></td>";
            }
        }
        $tab .= '</tr>';
        $tab .= "<tr class='rowcontent'><td colspan=2>".$_SESSION['lang']['syaratPem'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            if ('1' != $formPil) {
                $tab .= '<td colspan=3>'.$arrOptTerm[$dtSisbyr[$ard]].'</td>';
            } else {
                $optTermPay = '';
                $optTermPay = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
                foreach ($arrOptTerm as $brsOptTerm => $listTerm) {
                    if ('0' != $dtSisbyr[$ard]) {
                        $optTermPay .= "<option value='".$brsOptTerm."' ".(($brsOptTerm == $dtSisbyr[$ard] ? 'selected' : '')).'>'.$listTerm.'</option>';
                    } else {
                        $optTermPay .= "<option value='".$brsOptTerm."'>".$listTerm.'</option>';
                    }
                }
                $tab .= "<td colspan=3><select id='term_pay_".$ard."'  style='width:150px'>".$optTermPay.'</select></td>';
            }
        }
        $tab .= '</tr>';
        $tab .= "<tr class='rowcontent'><td colspan=2>".$_SESSION['lang']['stock'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            if ('1' != $formPil) {
                $tab .= '<td colspan=3>'.$arrStock[$dtStock[$ard]].'</td>';
            } else {
                $optStock = '';
                $optStock = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
                foreach ($arrStock as $brsStock => $listStock) {
                    if ('' != $dtStock[$ard]) {
                        $optStock .= "<option value='".$brsStock."' ".(($brsStock == $dtStock[$ard] ? 'selected' : '')).'>'.$listStock.'</option>';
                    } else {
                        $optStock .= "<option value='".$brsStock."'>".$listStock.'</option>';
                    }
                }
                $tab .= '<td colspan=3><select id=stockId_'.$ard." style='width:150px'>".$optStock.'</select></td>';
            }
        }
        $tab .= '</tr>';
        $tab .= "<tr class='rowcontent'><td colspan=2>".$_SESSION['lang']['almt_kirim'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            if ('1' != $formPil) {
                $tab .= '<td colspan=3>'.$arrFranco[$dtFranco[$ard]].'</td>';
            } else {
                $optKrm = '';
                $optKrm = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
                $sKrm = 'select id_franco,franco_name from '.$dbname.'.setup_franco where status=0 order by franco_name asc';
                $qKrm = mysql_query($sKrm);
                while ($rKrm = mysql_fetch_assoc($qKrm)) {
                    if ('0' != $dtFranco[$ard]) {
                        $optKrm .= '<option value='.$rKrm['id_franco'].' '.(($rKrm['id_franco'] == $dtFranco[$ard] ? 'selected' : '')).'>'.$rKrm['franco_name'].'</option>';
                    } else {
                        $optKrm .= '<option value='.$rKrm['id_franco'].'>'.$rKrm['franco_name'].'</option>';
                    }
                }
                $tab .= '<td colspan=3><select id=tmpt_krm_'.$ard." style='width:150px'>".$optKrm.'</select></td>';
            }
        }
        $tab .= '</tr>';
        $tab .= "<tr class='rowcontent'><td colspan=2>".$_SESSION['lang']['keterangan'].'</td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            if ('1' != $formPil) {
                $tab .= '<td align=justify colspan=3>'.$dtCttn[$ard].'</td>';
            } else {
                $tab .= "<td align=justify colspan=3><textarea id='ketUraian_".$ard."' name='ketUraian_".$ard."' onkeypress='return tanpa_kutip(event);' cols=18 rows=3>".$dtCttn[$ard].'</textarea></td>';
            }
        }
        $tab .= '</tr>';
        $tab .= '<tr class=rowcontent><td colspan=2></td>';
        $ard = 0;
        foreach ($dtNomor as $brs) {
            ++$ard;
            if ('0' != $formPil) {
                $tab .= '<td align=center colspan=3><button class=mybutton id=save_'.$ard.' onclick=simpanSemua2('.$ard.','.$totBrg.')>'.$_SESSION['lang']['save'].'</button></td>';
            }
        }
        $tab .= '</tr>';
        $tab .= '</tbody></table>';
        echo $tab;

        break;
    case 'updateTransaksi':
        $subTotal = str_replace(',', '', $subTotal);
        $nilaiPermintaan = str_replace(',', '', $nilaiPermintaan);
        $scek = 'select distinct supplierid from '.$dbname.".log_perintaanhargaht \r\n                       where nomor='".$no_prmntan."' and nourut='".$_POST['nourut']."'";
        $qcek = mysql_query($scek);
        $rcek = mysql_fetch_assoc($qcek);
        if ($_POST['supplierId'] == $rcek['$rcek']) {
            exit('error: Supplier Tersebut Sudah Terdaftar');
        }

        $sUpdate = 'update '.$dbname.".log_perintaanhargaht set id_franco='".(int) $idFranco."', stock='".(int) $stockId."', \r\n                          catatan='".$ketUraian."',sisbayar='".(int) $termPay."', ppn='".$nilPPn."', subtotal='".$subTotal."', \r\n                          diskonpersen='".$diskonPersen."', nilaidiskon='".$nilDiskon."', nilaipermintaan='".$nilaiPermintaan."', \r\n                          tgldari='".tanggalsystem($_POST['tglDari'])."', tglsmp='".tanggalsystem($_POST['tglSmp'])."', kurs='".$_POST['kurs']."',\r\n                          matauang='".$_POST['mtUang']."',supplierid='".$_POST['supplierId']."'\r\n                          where nomor='".$no_prmntan."' and nourut='".$_POST['nourut']."'";
        if (mysql_query($sUpdate)) {
            $totRow = count($_POST['kdbrg']);
            foreach ($_POST['kdbrg'] as $row => $Act) {
                $kdbrg = $Act;
                $merk = $_POST['merk'][$row];
                $hrg = $_POST['price'][$row];
                $jmlh = $_POST['jmlh'][$row];
                $sUpdate2 = 'update '.$dbname.".log_permintaanhargadt set `jumlah`='".$jmlh."',`harga`='".$hrg."',`merk`='".$merk."' \r\n                                                where nomor='".$no_prmntan."' and kodebarang='".$kdbrg."' and nourut='".$_POST['nourut']."'";
                if (mysql_query($sUpdate2)) {
                    ++$berhasil;
                } else {
                    echo ' Gagal,'.$sUpdate2."\n detail".addslashes(mysql_error($conn));
                }
            }
        } else {
            echo $sUpdate."\n";
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        if ($totRow == $berhasil) {
            exit('Done');
        }

        break;
    case 'getKetNopp':
        $optNmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
        $tab .= '<thead><td>'.$_SESSION['lang']['kodebarang'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['namabarang'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['keterangan'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['action'].'</td></tr></thead><tbody>';
        $sdet = 'select * from '.$dbname.".`log_permintaanhargadt` \r\n                       where nomor='".$_POST['notransaksi']."' and nourut='".$_POST['nourut']."'";
        $qdet = mysql_query($sdet);
        while ($rdet = mysql_fetch_assoc($qdet)) {
            ++$no;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td id=kdBrg_'.$no.'>'.$rdet['kodebarang'].'</td>';
            $tab .= '<td>'.$optNmBrg[$rdet['kodebarang']].'</td>';
            $tab .= '<td><textarea id=ketId_'.$no.'>'.$rdet['keterangan'].'</textarea></td>';
            $tab .= "<td><button class=mybutton  onclick=saveKetData('".$_POST['notransaksi']."','".$_POST['nourut']."','".$no."')>".$_SESSION['lang']['save'].'</button></td></tr>';
        }
        $tab .= '</tbody></table>';
        echo $tab;

        break;
    case 'updateKet':
        $supdate = 'update '.$dbname.".log_permintaanhargadt set keterangan='".$_POST['ket']."'\r\n                          where nomor='".$_POST['notransaksi']."' and kodebarang='".$_POST['kdBrng']."'";
        if (!mysql_query($supdate)) {
            exit('error: db error'.mysql_error($conn).'___'.$supdate);
        }

        break;
}

?>