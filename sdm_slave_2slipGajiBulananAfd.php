<?php

    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/eagrolib.php';
    require_once 'lib/zLib.php';
    require_once 'lib/fpdf.php';
    require_once 'lib/terbilang.php';
    $proses = $_GET['proses'];
    ('' == $_POST['periode'] ? ($periode = $_GET['periode']) : ($periode = $_POST['periode']));
    ('' == $_POST['period'] ? ($period = $_GET['period']) : ($period = $_POST['period']));
    ('' == $_POST['perod'] ? ($perod = $_GET['perod']) : ($perod = $_POST['perod']));
    ('' == $_POST['idKry'] ? ($idKry = $_GET['idKry']) : ($idKry = $_POST['idKry']));
    $arrBln = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
    ('' == $_POST['idAfd'] ? ($idAfd = $_GET['idAfd']) : ($idAfd = $_POST['idAfd']));
    ('' == $_POST['tPkary2'] ? ($tPkary = $_GET['tPkary2']) : ($tPkary = $_POST['tPkary2']));
    $rNmTipe = makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
    $lksiTgs = substr($idAfd, 0, 4);
    $kdBag2 = $_POST['kdBag2'];
    if ('HOLDING' == $_SESSION['empl']['tipelokasitugas'] || 'KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
        if ('' != $idAfd) {
            $add = "b.lokasitugas='".$idAfd."'";
            if ('' != $kdBag2) {
                $add .= " and b.bagian='".$kdBag2."'";
            }
        } else {
            exit('Error: Working unit required');
        }
    } else {
        if (strlen($idAfd) < 6) {
            $add = "b.lokasitugas='".$idAfd."' and (b.subbagian is null or b.subbagian='')";
        } else {
            $add = "b.subbagian='".$idAfd."'";
        }

        if ('' != $kdBag2) {
            $add .= " and b.bagian='".$kdBag2."'";
        }
    }

    if ('' != $tPkary) {
        $dtTipe = " and b.tipekaryawan='".$tPkary."'";
    }

    switch ($proses) {
        case 'preview':
            $sOrg = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$idAfd."'";
            $qOrg = mysql_query($sOrg);
            $rOrg = mysql_fetch_assoc($qOrg);
            if ('SSP' == $_SESSION['org']['kodeorganisasi']) {
                $path = 'images/SSP_logo.jpg';
            } else {
                if ('MJR' == $_SESSION['org']['kodeorganisasi']) {
                    $path = 'images/MI_logo.jpg';
                } else {
                    if ('HSS' == $_SESSION['org']['kodeorganisasi']) {
                        $path = 'images/HS_logo.jpg';
                    } else {
                        if ('BNM' == $_SESSION['org']['kodeorganisasi']) {
                            $path = 'images/BM_logo.jpg';
                        }
                    }
                }
            }

            $bln = explode('-', $perod);
            $idBln = (int) ($bln[1]);
            $sSlip = "select distinct a.*,b.tipekaryawan,b.statuspajak,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama from\r\n               ".$dbname.'.sdm_gaji_vw a  left join '.$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n               left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan\r\n               left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode\r\n               where b.sistemgaji='Bulanan' and a.periodegaji='".$perod."' and ".$add.' '.$dtTipe.' order by b.namakaryawan asc';
            $qSlip = mysql_query($sSlip);
            $rCek = mysql_num_rows($qSlip);
            if (0 < $rCek) {
                while ($rSlip = mysql_fetch_assoc($qSlip)) {
                    if ('' != $rSlip['karyawanid']) {
                        $arrKary[$rSlip['karyawanid']] = $rSlip['karyawanid'];
                        $arrKomp[$rSlip['karyawanid']] = $rSlip['idkomponen'];
                        $arrTglMsk[$rSlip['karyawanid']] = $rSlip['tanggalmasuk'];
                        $arrNik[$rSlip['karyawanid']] = $rSlip['nik'];
                        $arrNmKary[$rSlip['karyawanid']] = $rSlip['namakaryawan'];
                        $arrBag[$rSlip['karyawanid']] = $rSlip['bagian'];
                        $arrJbtn[$rSlip['karyawanid']] = $rSlip['namajabatan'];
                        $arrDept[$rSlip['karyawanid']] = $rSlip['nama'];
                        $arrJmlh[$rSlip['karyawanid'].$rSlip['idkomponen']] = $rSlip['jumlah'];
                    }
                }
                $sKomp = 'select id,name from '.$dbname.".sdm_ho_component where plus='1' and id not in ('13','14', '55', '6', '7', '56', '29', '30', '32', '33', '74', '57') ORDER BY urut ASC";
                $qKomp = mysql_query($sKomp);
                while ($rKomp = mysql_fetch_assoc($qKomp)) {
                    $arrIdKompPls[] = $rKomp['id'];
                    $arrNmKomPls[$rKomp['id']] = $rKomp['name'];
                }
                $sKomp = 'select id,name from '.$dbname.".sdm_ho_component where plus='0' and id not in ('66') ORDER BY urut ASC";
                $qKomp = mysql_query($sKomp);
                while ($rKomp = mysql_fetch_assoc($qKomp)) {
                    $arrIdKompMin[] = $rKomp['id'];
                    $arrNmKomMin[$rKomp['id']] = $rKomp['name'];
                }
                $jmlhKary = count($arrKary);
                foreach ($arrKary as $dtKary) {
                    echo "  <table cellspacing=1 border=0 width=500>
                                <tr style='border-bottom:#000 solid 2px; border-top:#000 solid 2px;'>
                                    <td valign=top>
                                        <table border=0 width=110%>
                                            <tr>
                                                <td width=49% valign=top><table border=0>
                                            <tr>
                                                <td colspan=3>".$_SESSION['lang']['slipGaji'].': '.$arrBln[$idBln].'-'.$bln[0]."</td>
                                            </tr>
                                            <tr>
                                                <td>".$_SESSION['lang']['nik'].'/'.$_SESSION['lang']['tmk'].'</td>
                                                <td>:</td><td>'.$arrNik[$dtKary].'/'.tanggalnormal($arrTglMsk[$dtKary])."</td>
                                            </tr>
                                            <tr>
                                                <td>".$_SESSION['lang']['nama'].'</td>
                                                <td>:</td><td>'.$arrNmKary[$dtKary]."</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width=51% valign=top>
                                        <table border=0>
                                            <tr>
                                                <td colspan=3>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td>".$_SESSION['lang']['unit'].'/'.$_SESSION['lang']['bagian'].'</td>
                                                <td>:</td>
                                                <td>'.$rOrg['namaorganisasi'].'/'.$arrBag[$dtKary]."</td>
                                            </tr>
                                            <tr>
                                                <td>".$_SESSION['lang']['jabatan'].'</td>
                                                <td>:</td>
                                                <td>'.$arrJbtn[$dtKary]."</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width=100%>
                                <thead>
                                    <tr>
                                        <td align=center>Pendapatan</td><td align=center>Potongan</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td valign=top>
                                            <table width=100%>";
                    $arrPlus = [];
                    $s = 0;
                    foreach ($arrIdKompPls as $idKompPls) {
                        if ($arrJmlh[$dtKary.$idKompPls] > 0 ) {
                            echo '                  <tr>
                                                    <td>'.$arrNmKomPls[$idKompPls].'</td>
                                                    <td>:Rp.</td>
                                                    <td align=right> '.number_format($arrJmlh[$dtKary.$idKompPls], 2).'</td>
                                                </tr>';
                        }
                        
                        $arrPlus[$s] = $arrJmlh[$dtKary.$idKompPls];
                        ++$s;
                    }
                    echo "                  </table>
                                        </td>
                                        <td valign=top>
                                            <table width=100%>";
                    $arrMin = [];
                    $q = 0;
                    foreach ($arrIdKompMin as $idKompMin) {
                        if ($arrJmlh[$dtKary.$idKompMin] > 0 ) {
                            echo '                  <tr>
                                                    <td>'.$arrNmKomMin[$idKompMin].'</td>
                                                    <td>:Rp.</td>
                                                    <td align=right> '.number_format($arrJmlh[$dtKary.$idKompMin], 2).'</td>
                                                </tr>';
                        }
                        
                        $arrMin[$q] = $arrJmlh[$dtKary.$idKompMin];
                        ++$q;
                    }
                    $gajiBersih = array_sum($arrPlus) - array_sum($arrMin);
                    echo "                  </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td valign='top'>
                                            <table width=100%>
                                                <tbody>
                                                    <tr>
                                                        <td>Total Pendapatan</td>
                                                        <td>:Rp.</td>
                                                        <td align='right'> ".number_format(array_sum($arrPlus), 2)."</td>
                                                    </tr>  
                                                    <tr>
                                                        <td>Take Home Pay</td>
                                                        <td>:Rp.</td>
                                                        <td align='right'> ".number_format(array_sum($arrPlus) - array_sum($arrMin), 2)."</td>
                                                    </tr>  
                                                    
                                                </tbody>
                                            </table>
                                        </td>
                                        
                                        <td valign='top'>
                                            <table width=100%>
                                                <tbody>
                                                     
                                                    <tr>
                                                        <td>Total Potongan</td>
                                                        <td>:Rp.</td>
                                                        <td align='right'> ".number_format(array_sum($arrMin), 2)."</td>
                                                    </tr>  
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                    <td>Terbilang :</td>
                                    <td colspan=3> ".terbilang($gajiBersih, 2)."</td>
                                    </tr>
                                    <tr>
                                        <td valign='top'>
                                            <table width=100%>
                                                <tbody>
                                                    <tr>
                                                        <td>Pekanbaru, .... ".$arrBln[$idBln+1]." ".$bln[0]."</td>
                                                    </tr>
                                                    <tr>
                                                        <td> </td>
                                                    </tr>
                                                    <tr>
                                                        <td> </td>
                                                    </tr>
                                                    <tr>
                                                        <td> </td>
                                                    </tr>
                                                    <tr>
                                                        <td> </td>
                                                    </tr>
                                                    <tr>
                                                        <td> </td>
                                                    </tr>
                                                    <tr>
                                                        <td> </td>
                                                    </tr>
                                                    <tr>
                                                        <td> </td>
                                                    </tr>
                                                    <tr>
                                                        <td> </td>
                                                    </tr>
                                                    <tr>
                                                        <td> </td>
                                                    </tr>
                                                    <tr>
                                                        <td> </td>
                                                    </tr>
                                                    <tr>
                                                        <td> </td>
                                                    </tr>
                                                    <tr>
                                                        <td> </td>
                                                    </tr>
                                                    <tr>
                                                        <td> </td>
                                                    </tr>
                                                    <tr>
                                                        <td> </td>
                                                    </tr>
                                                    <tr>
                                                        <td>".$arrNmKary[$dtKary]."</td>
                                                    </tr> 
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                </table>
                ";
                }
            } else {
                echo 'Not Found';
            }

            break;
        case 'pdf':
            $perod = $_GET['perod'];
            $idAfd = $_GET['idAfd'];
            $idKry = $_GET['idKry'];
            $kdBag2 = $_GET['kdBag2'];

            class PDF extends FPDF
            {
                public $col = 0;
                public $dbname;

                public function SetCol($col)
                {
                    $this->col = $col;
                    $x = 10 + $col * 100;
                    $this->SetLeftMargin($x);
                    $this->SetX($x);
                }

                public function AcceptPageBreak()
                {
                    if ($this->col < 1) {
                        $this->SetCol($this->col + 1);
                        $this->SetY(10);

                        return false;
                    }

                    $this->SetCol(0);

                    return true;
                }

                public function Header()
                {
                    $this->lMargin = 5;
                }

                public function Footer()
                {
                    $this->SetY(-15);
                    $this->SetFont('Arial', 'I', 5);
                    $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
                }
            }

            $pdf = new PDF('P', 'mm', 'A4');
            $pdf->AddPage();
            $pdf->SetFont('Arial', '', 5);
            $bln = explode('-', $perod);
            $idBln = (int) ($bln[1]);

            $sSlip = "  select distinct 
                            a.*,b.tipekaryawan, b.statuspajak, b.tanggalmasuk, b.nik, b.namakaryawan, b.bagian, c.namajabatan, d.nama 
                        from
                            ".$dbname.'.sdm_gaji_vw a  
                        left join 
                            '.$dbname.".datakaryawan b on a.karyawanid = b.karyawanid
                        left join 
                            ".$dbname.".sdm_5jabatan c on b.kodejabatan = c.kodejabatan
                        left join 
                            ".$dbname.".sdm_5departemen d on b.bagian = d.kode
                        where 
                            b.sistemgaji = 'Bulanan' 
                        and 
                            a.periodegaji='".$perod."' 
                        and
                            idkomponen not in ('13','14', '55', '6', '7', '56', '29', '30', '32', '33', '74', '57', '66')
                        and 
                            ".$add.'  '.$dtTipe.' 
                        order 
                            by b.namakaryawan asc';
            $qSlip = mysql_query($sSlip);
            $rCek = mysql_num_rows($qSlip);
            if (0 < $rCek) {
                while ($rSlip = mysql_fetch_assoc($qSlip)) {
                    $datakaryawan[$rSlip['karyawanid']]['karyawanid'] = $rSlip['karyawanid'];
                    $datakaryawan[$rSlip['karyawanid']]['nik'] = $rSlip['nik'];
                    $datakaryawan[$rSlip['karyawanid']]['tanggalmasuk'] = $rSlip['tanggalmasuk'];
                    $datakaryawan[$rSlip['karyawanid']]['namakaryawan'] = $rSlip['namakaryawan'];
                    $datakaryawan[$rSlip['karyawanid']]['nama'] = $rSlip['nama'];
                    $datakaryawan[$rSlip['karyawanid']]['namajabatan'] = $rSlip['namajabatan'];
                    $datakaryawan[$rSlip['karyawanid']]['idkomponen'][$rSlip['idkomponen']]['idkomponen'] = $rSlip['idkomponen'];
                    $datakaryawan[$rSlip['karyawanid']]['idkomponen'][$rSlip['idkomponen']]['jumlah'] = $rSlip['jumlah'];
                    $datakaryawan[$rSlip['karyawanid']]['bagian'] = $rSlip['bagian'];
                }       

                $arrValPlus = [];
                $arrValMinus = [];

                $str3 = '   select 
                                jumlah, idkomponen, a.karyawanid, c.plus, c.name
                            from 
                                '.$dbname.".sdm_gaji_vw a
                            left join 
                                ".$dbname.".sdm_ho_component c on a.idkomponen=c.id
                            where 
                                a.sistemgaji='Bulanan' 
                            AND
                                idkomponen not in ('13','14', '55', '6', '7', '56', '29', '30', '32', '33', '74', '57', '66')
                            AND
                                c.plus = 1
                            and 
                                a.periodegaji='".$perod."' 
                            ORDER BY
                                a.karyawanid, c.urut ASC";
                $res3 = mysql_query($str3, $conn);
                
                $dataGajiKaryawanPlus = [];
                $i = 0;
                $karyawanidloop = 0;
                while ($bar3 = mysql_fetch_assoc($res3)) {
                    if ($bar3['karyawanid'] != $karyawanidloop) {
                        $karyawanidloop = $bar3['karyawanid'];
                        $i = 0;
                    }
                    $dataGajiKaryawanPlus[$bar3['karyawanid']][$i]['judul'] = $bar3['name'];
                    $dataGajiKaryawanPlus[$bar3['karyawanid']][$i]['jumlah'] = $bar3['jumlah'];
                    $i++;
                }

                $str3 = '   select 
                                jumlah, idkomponen, a.karyawanid, c.plus, c.name
                            from 
                                '.$dbname.".sdm_gaji_vw a
                            left join 
                                ".$dbname.".sdm_ho_component c on a.idkomponen=c.id
                            where 
                                a.sistemgaji='Bulanan' 
                            AND
                                idkomponen not in ('13','14', '55', '6', '7', '56', '29', '30', '32', '33', '74', '57', '66')
                            AND
                                c.plus = 0
                            and 
                                a.periodegaji='".$perod."' 
                            ORDER BY
                                a.karyawanid, c.urut ASC";

                $res3 = mysql_query($str3, $conn);
                $dataGajiKaryawanMinus = [];
                $i = 0;
                $karyawanidloop = 0;
                while ($bar3 = mysql_fetch_assoc($res3)) {
                    if ($bar3['karyawanid'] != $karyawanidloop) {
                        $karyawanidloop = $bar3['karyawanid'];
                        $i = 0;
                    }
                    $dataGajiKaryawanMinus[$bar3['karyawanid']][$i]['judul'] = $bar3['name'];
                    $dataGajiKaryawanMinus[$bar3['karyawanid']][$i]['jumlah'] = $bar3['jumlah'];
                    $i++;
                }

                foreach ($datakaryawan as $dtKary) {
                    // $pdf->Image('images/logo_ssp.jpg', $pdf->GetX(), $pdf->GetY() - 7, 10);
                    // $pdf->SetX($pdf->getX() + 10);
                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->Cell(75, 6, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');

                    $pdf->SetFont('Arial', '', 6);
                    $pdf->Cell(71, 4, $_SESSION['lang']['slipGaji'].': '.$arrBln[$idBln].'-'.$bln[0], 'T', 0, 'L');
                    $pdf->Cell(25, 4, 'Printed on: '.date('d-m-Y: H:i:s'), 'T', 1, 'R');
                    
                    $pdf->Cell(15, 4, $_SESSION['lang']['nik'].'/'.$_SESSION['lang']['tmk'], 0, 0, 'L');
                    $pdf->Cell(35, 4, '  : '.$dtKary['nik'].'/'.tanggalnormal($dtKary['tanggalmasuk']), 0, 0, 'L');

                    $pdf->Cell(15, 4, $_SESSION['lang']['unit'].'/'.$_SESSION['lang']['bagian'], 0, 0, 'L');
                    $pdf->Cell(35, 4, ': '.$idAfd.' / '.$dtKary['nama'], 0, 1, 'L');

                    $pdf->Cell(15, 4, $_SESSION['lang']['namakaryawan'].':', 0, 0, 'L');
                    $pdf->Cell(35, 4, ':  '.$dtKary['namakaryawan'], 0, 0, 'L');

                    $pdf->Cell(15, 3, $_SESSION['lang']['jabatan'], 0, 0, 'L');
                    $pdf->Cell(35, 4, ': '.$dtKary['namajabatan'], 0, 1, 'L');
                    
                    $pdf->Cell(48, 4, "Pendapatan", 'TB', 0, 'C');
                    $pdf->Cell(48, 4, "Potongan", 'TB', 1, 'C');
                    
                    $hitungan = 0;

                    if (count($dataGajiKaryawanPlus[$dtKary['karyawanid']]) >= count($dataGajiKaryawanMinus[$dtKary['karyawanid']])) {
                        $hitungan = count($dataGajiKaryawanPlus[$dtKary['karyawanid']]);
                    }else{
                        $hitungan = count($dataGajiKaryawanMinus[$dtKary['karyawanid']]);
                    }
                    
                    $jumlahPlus = 0;
                    $jumlahMinus = 0;
                    
                    for ($mn = 0; $mn < $hitungan; ++$mn) {

                        if (empty($dataGajiKaryawanPlus[$dtKary['karyawanid']][$mn])) {
                            $pdf->Cell(25, 4, "", 0, 0, 'L');
                            $pdf->Cell(5, 4, '', 0, 0, 'L');
                            $pdf->Cell(18, 4, '', 'R', 0, 'R');
                        }else{
                            if (strlen($dataGajiKaryawanPlus[$dtKary['karyawanid']][$mn]['judul']) > 22) {
                                $pdf->SetFont('Arial', '', 4.5);
                                $pdf->Cell(25, 4, $dataGajiKaryawanPlus[$dtKary['karyawanid']][$mn]['judul'], 0, 0, 'L');
                                $pdf->SetFont('Arial', '', 6);
                            }else{
                                $pdf->Cell(25, 4, $dataGajiKaryawanPlus[$dtKary['karyawanid']][$mn]['judul'], 0, 0, 'L');
                            }
                            $pdf->Cell(5, 4, ': Rp.', 0, 0, 'L');
                            $pdf->Cell(18, 4, number_format($dataGajiKaryawanPlus[$dtKary['karyawanid']][$mn]['jumlah'], 2, '.', ','), 'R', 0, 'R');
                            $jumlahPlus += $dataGajiKaryawanPlus[$dtKary['karyawanid']][$mn]['jumlah'];
                        }
                        
                        if (empty($dataGajiKaryawanMinus[$dtKary['karyawanid']][$mn])) {
                            $pdf->Cell(25, 4, "", 0, 0, 'L');
                            $pdf->Cell(5, 4, '', 0, 0, 'L');
                            $pdf->Cell(18, 4, '', 0, 1, 'R');
                        }else{
                            if (strlen($dataGajiKaryawanMinus[$dtKary['karyawanid']][$mn]['judul']) > 22) {
                                $pdf->SetFont('Arial', '', 4.5);
                                $pdf->Cell(25, 4, $dataGajiKaryawanMinus[$dtKary['karyawanid']][$mn]['judul'], 0, 0, 'L');
                                $pdf->SetFont('Arial', '', 6);
                            }else{
                                $pdf->Cell(25, 4, $dataGajiKaryawanMinus[$dtKary['karyawanid']][$mn]['judul'], 0, 0, 'L');
                            }
                            $pdf->Cell(5, 4, ': Rp.', 0, 0, 'L');
                            $pdf->Cell(18, 4, number_format($dataGajiKaryawanMinus[$dtKary['karyawanid']][$mn]['jumlah'], 2, '.', ','), 0, 1, 'R');
                            $jumlahMinus += $dataGajiKaryawanMinus[$dtKary['karyawanid']][$mn]['jumlah'];
                        }                       
                    }
                    
                    $pdf->Cell(25, 4, $_SESSION['lang']['totalPendapatan'], 'TB', 0, 'L');
                    $pdf->Cell(5, 4, ': Rp.', 'TB', 0, 'L');
                    $pdf->Cell(18, 4, number_format($jumlahPlus, 2, '.', ','), 'TB', 0, 'R');

                    $pdf->Cell(25, 4, $_SESSION['lang']['totalPotongan'], 'TB', 0, 'L');
                    $pdf->Cell(5, 4, ': Rp.', 'TB', 0, 'L');
                    $pdf->Cell(18, 4, number_format($jumlahMinus * -1, 2, '.', ','), 'TB', 1, 'R');

                    $pdf->SetFont('Arial', 'B', 7);

                    $pdf->Cell(23, 4, "Take Home Pay", 0, 0, 'L');
                    $pdf->Cell(5, 4, ':      Rp.', 0, 0, 'L');
                    $pdf->Cell(22, 4, number_format($jumlahPlus - $jumlahMinus * -1, 2, '.', ','), 0, 0, 'R');
                    $pdf->Cell(47, 4, '', 0, 1, 'L');
                    
                    $terbilang = $jumlahPlus - $jumlahMinus * -1;
                    $blng = terbilang($terbilang, 2).' rupiah';
                    $pdf->SetFont('Arial', '', 7);
                    $pdf->Cell(23, 4, 'Terbilang', 0, 0, 'L');
                    $pdf->Cell(5, 4, ':', 0, 0, 'L');
                    $pdf->MultiCell(58, 4, $blng, 0, 'L');
                    $pdf->Ln();$pdf->Ln();
                    $pdf->Cell(96, 4, 'Pekanbaru, ... ' . $arrBln[$idBln+1].' '.$bln[0], 0, 1, 'L');
                    $pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();
                    $pdf->Cell(96, 4, $dtKary['namakaryawan'], 0, 1, 'L');
                   
                    $pdf->Ln();
                    $pdf->Ln();
                    $pdf->Ln();
                    if (140 < $pdf->GetY() && $pdf->col < 1) {
                        $pdf->AcceptPageBreak();
                    }

                    if (140 < $pdf->GetY() && 0 < $pdf->col) {
                        $r = 275 - $pdf->GetY();
                        $pdf->Cell(80, $r, '', 0, 1, 'L');
                    }

                    $pdf->cell(-1, 3, '', 0, 0, 'L');
                }
            } else {
                $pdf->Image('images/logo.jpg', $pdf->GetX(), $pdf->GetY(), 10);
                $pdf->SetX($pdf->getX() + 8);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(70, 5, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
                $pdf->SetFont('Arial', '', 5);
                $pdf->Cell(60, 3, 'NO DATA FOUND', 'T', 0, 'L');
            }

            $pdf->Output();

            break;
        case 'excel':
            $bln = explode('-', $perod);
            $idBln = (int) ($bln[1]);
            $sKomp = 'select id,name from '.$dbname.".sdm_ho_component where plus='1' and id not in ('13','14')  ";
            $qKomp = mysql_query($sKomp);
            while ($rKomp = mysql_fetch_assoc($qKomp)) {
                $arrIdKompPls[] = $rKomp['id'];
                $arrNmKomPls[$rKomp['id']] = $rKomp['name'];
            }
            $totPlus = count($arrIdKompPls);
            $brsPlus = 0;
            $sKomp = 'select id,name from '.$dbname.".sdm_ho_component where plus='0'  ";
            $qKomp = mysql_query($sKomp);
            while ($rKomp = mysql_fetch_assoc($qKomp)) {
                $arrIdKompMin[] = $rKomp['id'];
                $arrNmKomMin[$rKomp['id']] = $rKomp['name'];
            }
            $sPeriod = 'select tanggalmulai,tanggalsampai from '.$dbname.".sdm_5periodegaji where jenisgaji='H' and periode='".$perod."' and kodeorg='".substr($idAfd, 0, 4)."'";
            $qPeriod = mysql_query($sPeriod);
            $rPeriod = mysql_fetch_assoc($qPeriod);
            $mulai = tanggalnormal($rPeriod['tanggalmulai']);
            $selesi = tanggalnormal($rPeriod['tanggalsampai']);
            $stream .= "\r\n                        <table>\r\n                        <tr><td colspan=15 align=center>List Data Gaji Harian, Unit : ".$idAfd."</td></tr>\r\n                        <tr><td colspan=15 align=center>Periode : ".$mulai.' s.d. '.$selesi."</td></tr>\r\n                        </table>\r\n                        <table border=1>\r\n                        <tr>\r\n                                <td bgcolor=#DEDEDE align=center rowspan='2'>No.</td>\r\n                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['namakaryawan']."</td>\r\n                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['nik'].'/'.$_SESSION['lang']['tmk'].'</td>';
            if ('HOLDING' == $_SESSION['empl']['tipelokasitugas'] || 'KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
                $stream .= "<td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['subbagian'].'</td>';
            }

            $stream .= "<td bgcolor=#DEDEDE align=center rowspan='2'>No. Rekening</td>";
            $stream .= "<td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['tipekaryawan']."</td>\r\n                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['totLembur']."</td>\r\n                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['unit'].'/'.$_SESSION['lang']['bagian']."</td>\r\n                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['statuspajak']."</td>\r\n\r\n                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['jabatan']."</td>\r\n                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['penambah']."\r\n                                <table cellspacing=0 border=0><tr>";
            $brsPlus = 0;
            foreach ($arrIdKompPls as $lstKompPls) {
                ++$brsPlus;
                $stream .= '<td>'.$arrNmKomPls[$lstKompPls].'</td>';
                if (1 == $brsPlus) {
                    $stream .= '<td>'.$arrNmKomMin[37].'</td>';
                    $stream .= '<td>'.$arrNmKomMin[36].'</td>';
                }
            }
            $stream .= '<td bgcolor=#DEDEDE align=center>'.$_SESSION['lang']['totalPendapatan'].'</td>';
            $stream .= "</tr></table>\t</td>\r\n                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['pengurang'].'<table cellspacing=0 border=0><tr>';
            foreach ($arrIdKompMin as $lstKompMin) {
                if (20 != $lstKompMin && 19 != $lstKompMin) {
                    $stream .= '<td>'.$arrNmKomMin[$lstKompMin].'</td>';
                }
            }
            $stream .= "</tr></table>\t</td>";
            $stream .= "<td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['totalPotongan']."</td><td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['gajiBersih'].'</td>';
            $stream .= '</tr><tr>';
            $sSlip = "select distinct a.*,b.tipekaryawan,b.statuspajak,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama,b.subbagian,b.norekeningbank from\r\n               ".$dbname.'.sdm_gaji_vw a  left join '.$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n               left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan\r\n               left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode\r\n               where b.sistemgaji='Bulanan' and a.periodegaji='".$perod."' and ".$add.' '.$dtTipe."\r\n               order by b.namakaryawan asc";
            $qSlip = mysql_query($sSlip);
            $rCek = mysql_num_rows($qSlip);
            if (0 < $rCek) {
                while ($rSlip = mysql_fetch_assoc($qSlip)) {
                    if ('' != $rSlip['karyawanid']) {
                        $arrKary[$rSlip['karyawanid']] = $rSlip['karyawanid'];
                        $arrKomp[$rSlip['karyawanid']] = $rSlip['idkomponen'];
                        $arrTglMsk[$rSlip['karyawanid']] = $rSlip['tanggalmasuk'];
                        $arrNik[$rSlip['karyawanid']] = $rSlip['nik'];
                        $arrNmKary[$rSlip['karyawanid']] = $rSlip['namakaryawan'];
                        $arrBag[$rSlip['karyawanid']] = $rSlip['bagian'];
                        $arrJbtn[$rSlip['karyawanid']] = $rSlip['namajabatan'];
                        $arrTipekary[$rSlip['karyawanid']] = $rSlip['tipekaryawan'];
                        $arrStatPjk[$rSlip['karyawanid']] = $rSlip['statuspajak'];
                        $arrDept[$rSlip['karyawanid']] = $rSlip['nama'];
                        $arrSubbagian[$rSlip['karyawanid']] = $rSlip['subbagian'];
                        $arrJmlh[$rSlip['karyawanid'].$rSlip['idkomponen']] = $rSlip['jumlah'];
                        $arrRek[$rSlip['karyawanid']] = $rSlip['norekeningbank'];
                        $arrTotal[$rSlip['idkomponen']] += $rSlip['jumlah'];
                    }
                }
                $sTot = 'select tipelembur,jamaktual,karyawanid from '.$dbname.".sdm_lemburdt where substr(kodeorg,1,4)='".substr($idAfd, 0, 4)."' and tanggal between '".$rPeriod['tanggalmulai']."' and '".$rPeriod['tanggalsampai']."'";
                $qTot = mysql_query($sTot);
                while ($rTot = mysql_fetch_assoc($qTot)) {
                    $sJum = 'select jamlembur as totalLembur from '.$dbname.".sdm_5lembur where tipelembur='".$rTot['tipelembur']."'\r\n                        and jamaktual='".$rTot['jamaktual']."' and kodeorg='".substr($idAfd, 0, 4)."'";
                    $qJum = mysql_query($sJum);
                    $rJum = mysql_fetch_assoc($qJum);
                    $jumTot[$rTot['karyawanid']] += $rJum['totalLembur'];
                }
                $peng1 = 20;
                $peng2 = 19;
                foreach ($arrKary as $dtKary) {
                    ++$no;
                    $stream .= "<tr class=rowcontent>\r\n                                <td>".$no."</td>\r\n                                <td>".$arrNmKary[$dtKary]."</td>\r\n                                <td>".$arrNik[$dtKary].'</td>';
                    $cold = 9;
                    if ('HOLDING' == $_SESSION['empl']['tipelokasitugas'] || 'KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
                        $cold = 10;
                        $stream .= '<td>'.$arrSubbagian[$dtKary].'</td>';
                    }

                    $stream .= '<td>'.$arrRek[$dtKary].'</td>';
                    $stream .= '<td>'.$rNmTipe[$arrTipekary[$dtKary]]."</td>\r\n                                <td>".$jumTot[$dtKary]."</td>\r\n                                <td>".$arrDept[$dtKary]."</td>\r\n\r\n                                <td>".$arrStatPjk[$dtKary]."</td>\r\n                                <td>".$arrJbtn[$dtKary].'</td><td>';
                    $stream .= '<table width=100% border=1><tr>';
                    $arrPlus = [];
                    $s = 0;
                    $brsPlus2 = 0;
                    foreach ($arrIdKompPls as $lstKompPls) {
                        $stream .= '<td align=right>'.number_format($arrJmlh[$dtKary.$lstKompPls], 2).'</td>';
                        $arrPlus[$s] = $arrJmlh[$dtKary.$lstKompPls];
                        ++$s;
                        ++$brsPlus2;
                        if (1 == $brsPlus2) {
                            $stream .= '<td>-'.$arrJmlh[$dtKary.$peng1].'</td>';
                            $stream .= '<td>-'.$arrJmlh[$dtKary.$peng2].'</td>';
                        }
                    }
                    $totDpt = array_sum($arrPlus) - ($arrJmlh[$dtKary.$peng1] + $arrJmlh[$dtKary.$peng2]);
                    $stream .= '<td align=right>'.number_format($totDpt, 2).'</td>';
                    $stream .= '</tr></table>';
                    $stream .= '</td><td><table width=100% border=1><tr>';
                    $arrMin = [];
                    $q = 0;
                    foreach ($arrIdKompMin as $lstKompMin) {
                        if (20 != $lstKompMin && 19 != $lstKompMin) {
                            $stream .= '<td align=right>'.number_format($arrJmlh[$dtKary.$lstKompMin]).'</td>';
                            $arrMin[$q] = $arrJmlh[$dtKary.$lstKompMin];
                            ++$q;
                        }
                    }
                    $gajiBersih = $totDpt - array_sum($arrMin);
                    $stream .= '</tr></table></td>';
                    $stream .= '<td align=right>'.number_format(array_sum($arrMin), 2).'</td>';
                    $stream .= '<td align=right>'.number_format($gajiBersih, 0).'</td>';
                    $stream .= '</tr>';
                }
                $stream .= '<tr><td colspan='.$cold.' align=right>'.$_SESSION['lang']['total'].'</td><td>';
                $stream .= '<table border=1 width=100%>';
                $s = 0;
                $brsPlus2 = 0;
                $arrPlus = [];
                foreach ($arrIdKompPls as $lstKompPls) {
                    $stream .= '<td align=right>'.number_format($arrTotal[$lstKompPls], 2).'</td>';
                    $arrPlus[$s] = $arrTotal[$lstKompPls];
                    ++$s;
                    ++$brsPlus2;
                    if (1 == $brsPlus2) {
                        $stream .= '<td>-'.number_format($arrTotal[$peng1], 2).'</td>';
                        $stream .= '<td>-'.number_format($arrTotal[$peng2], 2).'</td>';
                    }
                }
                $totDpt = array_sum($arrPlus) - ($arrTotal[$peng1] + $arrTotal[$peng2]);
                $stream .= '<td align=right>'.number_format($totDpt, 2).'</td>';
                $stream .= '</tr></table>';
                $stream .= '</td><td><table width=100% border=1><tr>';
                $arrMin = [];
                $q = 0;
                foreach ($arrIdKompMin as $lstKompMin) {
                    if (20 != $lstKompMin && 19 != $lstKompMin) {
                        $stream .= '<td align=right>'.number_format($arrTotal[$lstKompMin]).'</td>';
                        $arrMin[$q] = $arrTotal[$lstKompMin];
                        ++$q;
                    }
                }
                $gajiBersih = $totDpt - array_sum($arrMin);
                $stream .= '</tr></table></td>';
                $stream .= '<td align=right>'.number_format(array_sum($arrMin), 2).'</td>';
                $stream .= '<td align=right>'.number_format($gajiBersih, 0).'</td>';
                $stream .= '</tr>';
            } else {
                $stream .= '<tr><td colspan=20>&nbsp;</td></tr>';
            }

            $stream .= '</table>Print Time:'.date('Y-m-d H:i:s').'<br />By:'.$_SESSION['empl']['name'];
            $dte = date('YmdHms');
            $nop_ = 'GajiBulananAfdeling_'.$_SESSION['empl']['lokasitugas'].$dte;
            $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
            gzwrite($gztralala, $stream);
            gzclose($gztralala);
            echo "<script language=javascript1.2>\r\n                            window.location='tempExcel/".$nop_.".xls.gz';\r\n                            </script>";

            break;
        case 'getPeriode':
            $optPeriode = "<option value''>".$_SESSION['lang']['pilihdata'].'</option>';
            $sPeriode = 'select periode from '.$dbname.".sdm_5periodegaji where kodeorg='".substr($idAfd, 1, 4)."' and jenisgaji='B'";
            $qPeriode = mysql_query($sPeriode);
            while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
                $optPeriode .= '<option value='.$rPeriode['periode'].'>'.$rPeriode['periode'].'</option>';
            }
            echo $optPeriode;

            break;
        default:
            break;
    }

?>