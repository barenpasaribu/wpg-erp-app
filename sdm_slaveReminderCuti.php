<?php



require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$periode = date('m');
$str = 'select namakaryawan,lokasitugas,tanggalmasuk from '.$dbname.".datakaryawan\r\n      where MONTH(tanggalmasuk)=".$periode." and tanggalmasuk not like '".date('Y-m')."%'\r\n      and tipekaryawan=5 and (tanggalkeluar is NULL or tanggalkeluar>'".date('Y-m-d')."')\r\n      and lokasitugas like 'H0%' and lokasitugas not like '%HO' order by lokasitugas,namakaryawan,tanggalmasuk";
$res = mysql_query($str);
if (0 < mysql_num_rows($res)) {
    $stream = "<table>\r\n              <thead>\r\n              <tr>\r\n              <td>No.</td>\r\n              <td>Nama</td>\r\n              <td>Tanggal Masuk</td>\r\n              <td>Lokasi Tugas</td>\r\n              </tr>\r\n              </thead>\r\n              <tbody>\r\n              ";
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $stream .= '<tr><td>'.$no."</td>\r\n                        <td>".$bar->namakaryawan."</td>\r\n                        <td>".tanggalnormal($bar->tanggalmasuk)."</td>\t\r\n                        <td>".$bar->lokasitugas."</td>  \r\n                      </tr>";
    }
    $stream .= "</tbody>\r\n               <tfoot>\r\n               </tfoot>\r\n               </table>";
    $to = '';
    $str = 'select nilai from '.$dbname.".setup_parameterappl where kodeparameter='RCUTI-H0RO'";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $to = trim($bar->nilai);
    }
    $subject = '[Notifikasi] Hak Cuti karyawan Central HIP Modo periode '.date('Y-m');
    $body = "<html>\r\n                 <head>\r\n                 <body>\r\n                   <dd>Dengan Hormat,</dd><br>\r\n                   <br>\r\n                   Berikut ini adalah karyawan yang akan memperoleh cuti baru bulan ini:\r\n                   <br>\r\n                    ".$stream."\r\n                   <br>\r\n                   Regards,<br>\r\n                   eAgro Plantation Management Software.\r\n                 </body>\r\n                 </head>\r\n               </html>\r\n               ";
    if ('' != $to) {
        $kirim = kirimEmailWindows($to, $subject, $body);
    }
}

$str = 'select namakaryawan,lokasitugas,tanggalmasuk from '.$dbname.".datakaryawan\r\n      where MONTH(tanggalmasuk)=".$periode." and tanggalmasuk not like '".date('Y-m')."%'\r\n      and tipekaryawan=5 and (tanggalkeluar is NULL or tanggalkeluar>'".date('Y-m-d')."')\r\n      and lokasitugas like '%HO' order by lokasitugas,namakaryawan,tanggalmasuk";
$res = mysql_query($str);
if (0 < mysql_num_rows($res)) {
    $stream = "<table>\r\n              <thead>\r\n              <tr>\r\n              <td>No.</td>\r\n              <td>Nama</td>\r\n              <td>Tanggal Masuk</td>\r\n              <td>Lokasi Tugas</td>\r\n              </tr>\r\n              </thead>\r\n              <tbody>\r\n              ";
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $stream .= '<tr><td>'.$no."</td>\r\n                        <td>".$bar->namakaryawan."</td>\r\n                        <td>".tanggalnormal($bar->tanggalmasuk)."</td>\t\r\n                        <td>".$bar->lokasitugas."</td>  \r\n                      </tr>";
    }
    $stream .= "</tbody>\r\n               <tfoot>\r\n               </tfoot>\r\n               </table>";
    $to = '';
    $str = 'select nilai from '.$dbname.".setup_parameterappl where kodeparameter='RCUTI-H0HO'";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $to = trim($bar->nilai);
    }
    $subject = '[Notifikasi] Hak Cuti karyawan HO periode '.date('Y-m');
    $body = "<html>\r\n                 <head>\r\n                 <body>\r\n                   <dd>Dengan Hormat,</dd><br>\r\n                   <br>\r\n                   Berikut ini adalah karyawan HO yang akan memperoleh cuti baru bulan ini:\r\n                   <br>\r\n                    ".$stream."\r\n                   <br>\r\n                   Regards,<br>\r\n                   eAgro Plantation Management Software.\r\n                 </body>\r\n                 </head>\r\n               </html>\r\n               ";
    if ('' != $to) {
        $kirim = kirimEmailWindows($to, $subject, $body);
    }
}

?>