<?php
    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/eagrolib.php';
    $notransaksi = $_POST['notransaksi'];
    $karid = $_POST['karid'];
    $str = 'select * from '.$dbname.".sdm_pjdinasht\r\n      where karyawanid=".$karid." and notransaksi='".$notransaksi."'";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        echo "<?xml version='1.0' ?>\r\n\t     <pjd>\r\n\t\t\t <karyawanid>".(('' != $bar->karyawanid ? $bar->karyawanid : '*'))."</karyawanid>\r\n\t\t\t <tipedinas>".(('' != $bar->tipe_perjalanan_dinas ? $bar->tipe_perjalanan_dinas : '*'))."</tipedinas>\r\n\t\t\t <kodeorg>".(('' != $bar->kodeorg ? $bar->kodeorg : '*'))."</kodeorg>\r\n\t\t\t <persetujuan>".(('' != $bar->persetujuan ? $bar->persetujuan : '*'))."</persetujuan>\r\n\t\t\t  <persetujuan2>".(('' != $bar->persetujuan2 ? $bar->persetujuan2 : '*'))."</persetujuan2>\r\n\t\t\t <hrd>".(('' != $bar->hrd ? $bar->hrd : '*'))."</hrd>\r\n\t\t\t <tujuan3>".(('' != $bar->tujuan3 ? $bar->tujuan3 : '*'))."</tujuan3>\r\n\t\t\t <tujuan2>".(('' != $bar->tujuan2 ? $bar->tujuan2 : '*'))."</tujuan2>\r\n\t\t     <tujuan1>".(('' != $bar->tujuan1 ? $bar->tujuan1 : '*'))."</tujuan1>\r\n\t\t\t <tanggalperjalanan>".(('' != $bar->tanggalperjalanan ? tanggalnormal($bar->tanggalperjalanan) : '*'))."</tanggalperjalanan>\r\n\t\t\t <tanggalkembali>".(('' != $bar->tanggalkembali ? tanggalnormal($bar->tanggalkembali) : '*'))."</tanggalkembali>\r\n\t\t\t <uangmuka>".(('' != $bar->uangmuka ? $bar->uangmuka : '*'))."</uangmuka>\r\n\t\t\t <tugas1>".(('' != $bar->tugas1 ? $bar->tugas1 : '*'))."</tugas1>\r\n\t\t\t <tugas2>".(('' != $bar->tugas2 ? $bar->tugas2 : '*'))."</tugas2>\r\n\t\t\t <tugas3>".(('' != $bar->tugas3 ? $bar->tugas3 : '*'))."</tugas3>\r\n\t\t\t <tugaslain>".(('' != $bar->tugaslain ? $bar->tugaslain : '*'))."</tugaslain>\r\n\t\t\t <tujuanlain>".(('' != $bar->tujuanlain ? $bar->tujuanlain : '*'))."</tujuanlain>\r\n\t\t\t <pesawat>".(('' != $bar->pesawat ? $bar->pesawat : '*'))."</pesawat>\r\n\t\t\t <darat>".(('' != $bar->darat ? $bar->darat : '*'))."</darat>\r\n\t\t\t <laut>".(('' != $bar->laut ? $bar->laut : '*'))."</laut>\r\n\t\t\t <mess>".(('' != $bar->mess ? $bar->mess : '*'))."</mess>\r\n\t\t\t <notransaksi>".(('' != $bar->notransaksi ? $bar->notransaksi : '*'))."</notransaksi>\r\n\t\t\t <hotel>".(('' != $bar->hotel ? $bar->hotel : '*'))."</hotel>\r\n\t\t\t <mobilsewa>".(('' != $bar->mobilsewa ? $bar->mobilsewa : '*'))."</mobilsewa>\r\n\t\t\t <ket>".(('' != $bar->keterangan ? $bar->keterangan : '*'))."</ket>\r\n\t\t </pjd>";
    }

?>