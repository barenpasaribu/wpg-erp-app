<?php

    require_once 'master_validation.php';
    include 'lib/eagrolib.php';
    include 'lib/zLib.php';
    require_once 'config/connection.php';
    $lokasitugas = $_POST['lokasitugas'];
    $periode = $_POST['periode'];
    $mitmk = $periode.'1231';

    $getKaryawanBHL = "SELECT * FROM sdm_5tipekaryawan";
    $dataKaryawanBHL = fetchData($getKaryawanBHL);
    $wherebhl = null;
    $kode_pkwtt = null;
    foreach ($dataKaryawanBHL as $key => $value) {
        if ($value['isharian'] == 1) {
            $wherebhl .= " AND tipekaryawan != " . $value['id'];
        }
        if ($value['tipe'] == "PKWTT") {
            $kode_pkwtt = $value['id'];
        }
        
    }

    if ('HOLDING' != $_SESSION['empl']['tipelokasitugas']) {
        $str1 = '   select 
                    karyawanid,namakaryawan,tanggalmasuk,lokasitugas,kodegolongan 
                    from 
                    '.$dbname.".datakaryawan
                    where 
                    lokasitugas='".$lokasitugas."' 
                    ".$wherebhl."
                    and isduplicate = 0
                    and tanggalkeluar IS NULL
                    and tanggalmasuk<>'0000-00-00' 
                    and tanggalmasuk<".$mitmk.
                    " ORDER BY namakaryawan ASC";
    } else {
        $str1 = '   select 
                    karyawanid,namakaryawan,tanggalmasuk,lokasitugas,kodegolongan 
                    from 
                    '.$dbname.".datakaryawan
                    where 
                    lokasitugas like '".$lokasitugas."' 
                    ".$wherebhl."
                    and isduplicate = 0
                    and tanggalkeluar IS NULL
                    and tanggalmasuk<>'0000-00-00' 
                    and tanggalmasuk<".$mitmk.
                    " ORDER BY namakaryawan ASC";
    }
    // print_r($str1);
    // die();
    $res1 = mysql_query($str1);
    $max = mysql_num_rows($res1);
    echo '  <button class=mybutton onclick=simpanAwal('.$max.')>
                '.$_SESSION['lang']['save']."
            </button>
            <table class=sortable cellspacing=1 border=0>
                <thead>
                    <tr class=rowheader>
                        <td>".$_SESSION['lang']['nokaryawan']."</td>
                        <td>".$_SESSION['lang']['namakaryawan']."</td>
                        <td>".$_SESSION['lang']['tanggalmasuk']."</td>
                        <td>".$_SESSION['lang']['dari']."</td>
                        <td>".$_SESSION['lang']['tanggalsampai']."</td>
                        <td>".$_SESSION['lang']['periode']."</td>
                        <td>".$_SESSION['lang']['hakcuti']."</td>
                        <td>".'Lokasi Tugas'."</td>
                    </tr>
                </thead>";
    echo "<tbody id=container>";
    $no = -1;
    while ($bar1 = mysql_fetch_object($res1)) {
        $x = readTextFile('config/jumlahcuti.lst');
        
        if(substr($bar1->tanggalmasuk,0,4) >= $periode){
            $hakcuti = 0;
        }else{
            if (0 < (int) $x) {
                $hakcuti = $x;
            } else {
                $hakcuti = 12;
            }
        }
        
        ++$no;
        
                
        

        // ambil kontrak kalau gk ada kontrak dari tmk $kode_pkwtt
        $getKontrakKaryawan = " SELECT 
                                    * 
                                FROM 
                                    sdm_karyawan_kontrak 
                                WHERE 
                                    karyawanid = '".$bar1->karyawanid."' 
                                ORDER BY 
                                    tanggal_akhir DESC
                                LIMIT 
                                    1";
        $dataKontrakKaryawan = fetchData($getKontrakKaryawan);

        $tanggalawan = tanggalnormal("0000-00-00");
        // ambil kontrak kalau gk ada kontrak dari tmk || $kode_pkwtt
        if (!empty($dataKontrakKaryawan[0])) {
            // cek dia tetap apa kontrak 
            if ($dataKontrakKaryawan[0]['tipe_karyawan'] == $kode_pkwtt) {
                if ($dataKontrakKaryawan[0]['status_cuti'] == 0) {
                    if (substr($dataKontrakKaryawan[0]['tanggal_mulai'],0,4) >= $periode) {
                        $hakcuti = 0;
                    }
                }
            }else{
                if ($dataKontrakKaryawan[0]['status_cuti'] == 0) {
                    $hakcuti = 0;
                }
            }
            $tanggalawan = $dataKontrakKaryawan[0]['tanggal_mulai'];
            
        }else{
            $tanggalawan = $bar1->tanggalmasuk;
            
        }

        $tgl = substr(str_replace('-', '', $tanggalawan), 4, 4);

        $dari = mktime(0, 0, 0, substr($tgl, 0, 2), substr($tgl, 2, 2), $periode);
        $dari = date('d-m-Y', $dari);
        $sampai = mktime(0, 0, 0, substr($tgl, 0, 2), substr($tgl, 2, 2), $periode + 1);

        $queryGetJumlahTambahan = " SELECT nilai FROM setup_parameterappl 
                                    WHERE kodeaplikasi='HR' 
                                    AND 
                                    kodeorg = '".$lokasitugas."' 
                                    AND 
                                    kodeparameter = 'MBC".$lokasitugas."'";
        $queryAct = mysql_query($queryGetJumlahTambahan);
        $hasil = mysql_fetch_object($queryAct);

        if(!empty($hasil)){
            $sampai = date('Y-m-d', $sampai);
            $statusDate = " month";
            $bulanTambah = $hasil->nilai.$statusDate;
            $sampai = date_create($sampai);
            
            date_add($sampai, date_interval_create_from_date_string($bulanTambah));
            $sampai = date_format($sampai,"d-m-Y");
        }else{
            $sampai = date('d-m-Y', $sampai);
        }
        
        echo '  <tr class=rowcontent id=baris'.$no.">
                    <td id=karyawanid".$no.'>'.$bar1->karyawanid."</td>
                    <td id=nama".$no.'>'.$bar1->namakaryawan."</td>
                    <td>".tanggalnormal($tanggalawan)."</td>
                    <td id=dari".$no.'>'.$dari."</td>
                    <td id=sampai".$no.'>'.$sampai."</td>
                    <td id=periode".$no.'>'.$periode."</td>
                    <td id=hak".$no.'>'.$hakcuti."</td>
                    <td id=kodeorg".$no.'>'.substr($bar1->lokasitugas, 0, 4)."</td>
                </tr>";
    }
    echo "      </tbody>
            <tfoot>
            </tfoot>
        </table>";
?>