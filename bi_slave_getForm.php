<?php



session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
$id = $_POST['id'];
$optTahun = "<option value=''>Pilih Tahun</option>";
for ($x = 0; $x <= 5; ++$x) {
    $optTahun .= "<option value='".(date('Y') - $x)."'>".(date('Y') - $x).'</option>';
}
switch ($id) {
    case 'bi_penerimaanTBS_form':
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='PABRIK' order by namaorganisasi";
        $res = mysql_query($str);
        $optPks = '';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        echo '<table><tr><td>'.$_SESSION['lang']['tahun'].'</td><td>:<select id=tahun>'.$optTahun."</select></td></tr>\r\n                  <tr><td>PKS</td><td><select id=pks> \r\n                   ".$optPks."\r\n                 </select>   \r\n                 </tr></table>\r\n                 <center><button onclick=get001('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_distribusi_penerimaan_form':
        $optBul = "<option value=''>Periode...</option>";
        for ($d = 0; $d <= 40; ++$d) {
            $x = mktime(0, 0, 0, date('m') - $d, 15, date('Y'));
            $optBul .= "<option value='".date('Ym', $x)."'</option>".date('m-Y', $x).'</option>';
        }
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='PABRIK' order by namaorganisasi";
        $res = mysql_query($str);
        $optPks = '';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        echo "<table><tr><td>Kode.Org</td><td><select id=pks> \r\n                   ".$optPks."\r\n                 </select></td></tr>\r\n                 <tr><td colspan=2><select id=dari>".$optBul.'</select> s/d <select id=sampai>'.$optBul."</select></td></tr>\r\n                  </table>\r\n                   <center><button onclick=get002('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_produksiPKS_form':
        $optBul = "<option value=''>Periode...</option>";
        for ($d = 0; $d <= 40; ++$d) {
            $x = mktime(0, 0, 0, date('m') - $d, 15, date('Y'));
            $optBul .= "<option value='".date('Ym', $x)."'</option>".date('m-Y', $x).'</option>';
        }
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='PABRIK' order by namaorganisasi";
        $res = mysql_query($str);
        $optPks = "<option value=''>Seluruhnya..</option>";
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        echo "<table><tr><td>Kode.Org</td><td><select id=pks> \r\n                   ".$optPks."\r\n                 </select></td></tr>\r\n                 <tr><td colspan=2><select id=dari>".$optBul.'</select> s/d <select id=sampai>'.$optBul."</select></td></tr>\r\n                  </table>\r\n                   <center><button onclick=get002('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_kapasitasPKS_form':
        $optBul = "<option value=''>Periode...</option>";
        for ($d = 0; $d <= 40; ++$d) {
            $x = mktime(0, 0, 0, date('m') - $d, 15, date('Y'));
            $optBul .= "<option value='".date('Ym', $x)."'</option>".date('m-Y', $x).'</option>';
        }
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='PABRIK' order by namaorganisasi";
        $res = mysql_query($str);
        $optPks = "<option value=''>Seluruhnya</option>";
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        echo "<table><tr><td>Kode.Org</td><td><select id=pks> \r\n                   ".$optPks."\r\n                 </select></td></tr>\r\n                 <tr><td colspan=2><select id=dari>".$optBul.'</select> s/d <select id=sampai>'.$optBul."</select></td></tr>\r\n                  </table>\r\n                   <center><button onclick=get002('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_produksiVsBudhetPKS_form':
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='PABRIK' order by namaorganisasi";
        $res = mysql_query($str);
        $optPks = '';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        echo '<table><tr><td>'.$_SESSION['lang']['tahun'].'</td><td>:<select id=tahun>'.$optTahun."</select></td></tr>\r\n                      <tr><td>PKS</td><td><select id=pks> \r\n                       ".$optPks."\r\n                     </select>   \r\n                     </tr></table>\r\n                     <center><button onclick=get001('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_biayaProduksiPKS_form':
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='PABRIK' order by namaorganisasi";
        $res = mysql_query($str);
        $optPks = '';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        echo '<table><tr><td>'.$_SESSION['lang']['tahun'].'</td><td>:<select id=tahun>'.$optTahun."</select></td></tr>\r\n                      <tr><td>PKS</td><td><select id=pks> \r\n                       ".$optPks."\r\n                     </select>   \r\n                     </tr></table>\r\n                     <center><button onclick=get001('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_produksiVsBudhetPKSAnnually_form':
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='PABRIK' order by namaorganisasi";
        $res = mysql_query($str);
        $optPks = '';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        echo '<table><tr><td>'.$_SESSION['lang']['tahun']."</td><td>:<select id=tahun style='width:50px;'>".$optTahun."</select> S/d <select id=tahun1  style='width:50px;'>".$optTahun."</select></td></tr>\r\n                      <tr><td>PKS</td><td><select id=pks> \r\n                       ".$optPks."\r\n                     </select>   \r\n                     </tr></table>\r\n                     <center><button onclick=get003('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_biayaProduksiPKSVsBudghet_form':
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='PABRIK' order by namaorganisasi";
        $res = mysql_query($str);
        $optPks = '';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        echo '<table><tr><td>'.$_SESSION['lang']['tahun'].'</td><td>:<select id=tahun>'.$optTahun."</select></td></tr>\r\n                      <tr><td>PKS</td><td><select id=pks> \r\n                       ".$optPks."\r\n                     </select>   \r\n                     </tr></table>\r\n                     <center><button onclick=get001('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_pengolahanPKSHarian_form':
        $optBul = "<option value=''>Periode...</option>";
        for ($d = 0; $d <= 40; ++$d) {
            $x = mktime(0, 0, 0, date('m') - $d, 15, date('Y'));
            $optBul .= "<option value='".date('Ym', $x)."'</option>".date('m-Y', $x).'</option>';
        }
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='PABRIK' order by namaorganisasi";
        $res = mysql_query($str);
        $optPks = '';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        echo "<table>\r\n                         <tr><td>PKS</td><td><select id=pks> \r\n                       ".$optPks."\r\n                        </select>   \r\n                         </tr>\r\n                         <tr><td>".$_SESSION['lang']['periode']."</td><td>:<select id=tahun style='width:100px;'>".$optBul."</select></td></tr>\r\n                      </table>\r\n                     <center><button onclick=get001('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_stokCPOPK_form':
        $optBul = "<option value=''>Periode...</option>";
        for ($d = 0; $d <= 40; ++$d) {
            $x = mktime(0, 0, 0, date('m') - $d, 15, date('Y'));
            $optBul .= "<option value='".date('Ym', $x)."'</option>".date('m-Y', $x).'</option>';
        }
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='PABRIK' order by namaorganisasi";
        $res = mysql_query($str);
        $optPks = '';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        echo "<table>\r\n                         <tr><td>PKS</td><td><select id=pks> \r\n                       ".$optPks."\r\n                        </select>   \r\n                         </tr>\r\n                         <tr><td>".$_SESSION['lang']['tanggal']."</td><td>:<input class=myinputtext id=tanggal name=tanggal onmousemove=setCalendar(this.id) onkeypress='return false;' maxlength=10 style='width:100px;' type=text></td></tr>\r\n                      </table>\r\n                     <center><button onclick=get004('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_sdmBiayaLembur_form':
        $optBul = "<option value=''>Periode...</option>";
        for ($d = 0; $d <= 40; ++$d) {
            $x = mktime(0, 0, 0, date('m') - $d, 15, date('Y'));
            $optBul .= "<option value='".date('Ym', $x)."'</option>".date('m-Y', $x).'</option>';
        }
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.'.organisasi where length(kodeorganisasi)=4 order by namaorganisasi';
        $res = mysql_query($str);
        $optPks = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        echo "<table><tr><td>Org</td><td><select id=pks style='width:100px;'> \r\n                   ".$optPks."\r\n                 </select></td></tr>\r\n                 <tr><td colspan=2><select id=dari>".$optBul.'</select> s/d <select id=sampai>'.$optBul."</select></td></tr>\r\n                  </table>\r\n                   <center><button onclick=get002('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_sdmTurnOverStaff_form':
        $optBul = "<option value=''>Periode...</option>";
        for ($d = 0; $d <= 40; ++$d) {
            $x = mktime(0, 0, 0, date('m') - $d, 15, date('Y'));
            $optBul .= "<option value='".date('Ym', $x)."'</option>".date('m-Y', $x).'</option>';
        }
        $str = 'select id,tipe from '.$dbname.'.sdm_5tipekaryawan order by id';
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->id."'>".$bar->id.'-'.$bar->tipe.'</option>';
        }
        echo '<table><tr><td>'.$_SESSION['lang']['tipekaryawan']."</td><td><select id=pks style='width:100px;'> \r\n                   ".$optPks."\r\n                 </select></td></tr>\r\n                 <tr><td colspan=2><select id=dari>".$optBul.'</select> s/d <select id=sampai>'.$optBul."</select></td></tr>\r\n                  </table>\r\n                   <center><button onclick=get002('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_sdmBiayaPengobatan_form':
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.'.organisasi where length(kodeorganisasi)=4 order by namaorganisasi';
        $res = mysql_query($str);
        $optPks = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        echo '<table><tr><td>'.$_SESSION['lang']['tahun'].'</td><td>:<select id=tahun>'.$optTahun."</select></td></tr>\r\n                      <tr><td>Org</td><td><select id=pks style='width:150px;'> \r\n                       ".$optPks."\r\n                     </select>   \r\n                     </tr></table>\r\n                     <center><button onclick=get001('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_sdmBiayaPengobatanDiagnosa_form':
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.'.organisasi where length(kodeorganisasi)=4 order by namaorganisasi';
        $res = mysql_query($str);
        $optPks = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        echo '<table><tr><td>'.$_SESSION['lang']['tahun'].'</td><td>:<select id=tahun>'.$optTahun."</select></td></tr>\r\n                      <tr><td>Org</td><td><select id=pks style='width:150px;'> \r\n                       ".$optPks."\r\n                     </select>   \r\n                     </tr></table>\r\n                     <center><button onclick=get001('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_sdmBiayaPengobatanPerawatan_form':
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.'.organisasi where length(kodeorganisasi)=4 order by namaorganisasi';
        $res = mysql_query($str);
        $optPks = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        echo '<table><tr><td>'.$_SESSION['lang']['tahun'].'</td><td>:<select id=tahun>'.$optTahun."</select></td></tr>\r\n                      <tr><td>Org</td><td><select id=pks style='width:150px;'> \r\n                       ".$optPks."\r\n                     </select>   \r\n                     </tr></table>\r\n                     <center><button onclick=get001('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_sdmBiayaPengobatanperRs_form':
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.'.organisasi where length(kodeorganisasi)=4 order by namaorganisasi';
        $res = mysql_query($str);
        $optPks = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        echo '<table><tr><td>'.$_SESSION['lang']['tahun'].'</td><td>:<select id=tahun>'.$optTahun."</select></td></tr>\r\n                      <tr><td>Org</td><td><select id=pks style='width:150px;'> \r\n                       ".$optPks."\r\n                     </select>   \r\n                     </tr></table>\r\n                     <center><button onclick=get001('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_sdmFrekuensiPengobatan_form':
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.'.organisasi where length(kodeorganisasi)=4 order by namaorganisasi';
        $res = mysql_query($str);
        $optPks = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        echo '<table><tr><td>'.$_SESSION['lang']['tahun'].'</td><td>:<select id=tahun>'.$optTahun."</select></td></tr>\r\n                      <tr><td>Org</td><td><select id=pks style='width:150px;'> \r\n                       ".$optPks."\r\n                     </select>   \r\n                     </tr></table>\r\n                     <center><button onclick=get001('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_sdmGaji_form':
        $optBul = "<option value=''>Periode...</option>";
        for ($d = 0; $d <= 40; ++$d) {
            $x = mktime(0, 0, 0, date('m') - $d, 15, date('Y'));
            $optBul .= "<option value='".date('Y-m', $x)."'</option>".date('m-Y', $x).'</option>';
        }
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.'.organisasi where length(kodeorganisasi)=4 order by namaorganisasi';
        $res = mysql_query($str);
        $optPks = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        echo "<table><tr><td>Org</td><td><select id=pks style='width:100px;'> \r\n                   ".$optPks."\r\n                 </select></td></tr>\r\n                 <tr><td colspan=2><select id=dari>".$optBul.'</select> s/d <select id=sampai>'.$optBul."</select></td></tr>\r\n                  </table>\r\n                   <center><button onclick=get002('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_agrProduksi':
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where length(kodeorganisasi)=4 and tipe='KEBUN' order by namaorganisasi";
        $res = mysql_query($str);
        $optPks = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        echo '<table><tr><td>'.$_SESSION['lang']['tahun'].'</td><td>:<select id=tahun>'.$optTahun."</select></td></tr>\r\n                      <tr><td>Org</td><td><select id=pks style='width:150px;'> \r\n                       ".$optPks."\r\n                     </select>   \r\n                     </tr></table>\r\n                     <center><button onclick=get001('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_agrBiayaPanen':
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where length(kodeorganisasi)=4 and tipe='KEBUN' order by namaorganisasi";
        $res = mysql_query($str);
        $optPks = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        echo '<table><tr><td>'.$_SESSION['lang']['tahun'].'</td><td>:<select id=tahun>'.$optTahun."</select></td></tr>\r\n                      <tr><td>Org</td><td><select id=pks style='width:150px;'> \r\n                       ".$optPks."\r\n                     </select>   \r\n                     </tr></table>\r\n                     <center><button onclick=get001('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_agrBiayaTM':
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where length(kodeorganisasi)=4 and tipe='KEBUN' order by namaorganisasi";
        $res = mysql_query($str);
        $optPks = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        echo '<table><tr><td>'.$_SESSION['lang']['tahun'].'</td><td>:<select id=tahun>'.$optTahun."</select></td></tr>\r\n                      <tr><td>Org</td><td><select id=pks style='width:150px;'> \r\n                       ".$optPks."\r\n                     </select>   \r\n                     </tr></table>\r\n                     <center><button onclick=get001('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_agrBiayaTBM':
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where length(kodeorganisasi)=4 and tipe='KEBUN' order by namaorganisasi";
        $res = mysql_query($str);
        $optPks = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        echo '<table><tr><td>'.$_SESSION['lang']['tahun'].'</td><td>:<select id=tahun>'.$optTahun."</select></td></tr>\r\n                      <tr><td>Org</td><td><select id=pks style='width:150px;'> \r\n                       ".$optPks."\r\n                     </select>   \r\n                     </tr></table>\r\n                     <center><button onclick=get001('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_agrCTM':
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where length(kodeorganisasi)=4 and tipe='KEBUN' order by namaorganisasi";
        $res = mysql_query($str);
        $optPks = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        $optBul = "<option value=''>Periode...</option>";
        for ($d = 0; $d <= 40; ++$d) {
            $x = mktime(0, 0, 0, date('m') - $d, 15, date('Y'));
            $optBul .= "<option value='".date('Y-m', $x)."'</option>".date('m-Y', $x).'</option>';
        }
        echo "<table>\r\n                       <tr><td>Org</td><td><select id=pks style='width:150px;'> \r\n                       ".$optPks."\r\n                     </select>   \r\n                     </tr>\r\n                        <tr><td>".$_SESSION['lang']['sampai'].'</td><td>:<select id=tahun>'.$optBul."</select></td></tr>\r\n                      </table>\r\n                     <center><button onclick=get001('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_agrStokBibit':
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where length(kodeorganisasi)=4 and tipe='KEBUN' order by namaorganisasi";
        $res = mysql_query($str);
        $optPks = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        $optBul = "<option value=''>Periode...</option>";
        for ($d = 0; $d <= 40; ++$d) {
            $x = mktime(0, 0, 0, date('m') - $d, 15, date('Y'));
            $optBul .= "<option value='".date('Y-m', $x)."'</option>".date('m-Y', $x).'</option>';
        }
        echo "<table>\r\n                       <tr><td>Org</td><td><select id=pks style='width:150px;'> \r\n                       ".$optPks."\r\n                     </select>   \r\n                     </tr>\r\n                        <tr><td>".$_SESSION['lang']['sampai'].'</td><td>:<select id=tahun>'.$optBul."</select></td></tr>\r\n                      </table>\r\n                     <center><button onclick=get001('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_agrStokBibitv2':
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where length(kodeorganisasi)=4 and tipe='KEBUN' order by namaorganisasi";
        $res = mysql_query($str);
        $optPks = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        $optBul = "<option value=''>Periode...</option>";
        for ($d = 0; $d <= 40; ++$d) {
            $x = mktime(0, 0, 0, date('m') - $d, 15, date('Y'));
            $optBul .= "<option value='".date('Y-m', $x)."'</option>".date('m-Y', $x).'</option>';
        }
        echo "<table>\r\n                       <tr><td>Org</td><td><select id=pks style='width:150px;'> \r\n                       ".$optPks."\r\n                     </select>   \r\n                     </tr>\r\n                        <tr><td>".$_SESSION['lang']['sampai'].'</td><td>:<select id=tahun>'.$optBul."</select></td></tr>\r\n                      </table>\r\n                     <center><button onclick=get001('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_agrPemupukan':
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where length(kodeorganisasi)=4 and tipe='KEBUN' order by namaorganisasi";
        $res = mysql_query($str);
        $optPks = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        $optBul = "<option value=''>Periode...</option>";
        for ($d = 0; $d <= 40; ++$d) {
            $x = mktime(0, 0, 0, date('m') - $d, 15, date('Y'));
            $optBul .= "<option value='".date('Y-m', $x)."'</option>".date('m-Y', $x).'</option>';
        }
        echo "<table>\r\n                       <tr><td>Org</td><td><select id=pks style='width:150px;'> \r\n                       ".$optPks."\r\n                     </select>   \r\n                     </tr>\r\n                        <tr><td>".$_SESSION['lang']['sampai'].'</td><td>:<select id=tahun>'.$optBul."</select></td></tr>\r\n                      </table>\r\n                     <center><button onclick=get001('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_trkBiayaPerKendaraan_form':
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where length(kodeorganisasi)=4 and tipe in ('KEBUN','TRAKSI') order by namaorganisasi";
        $res = mysql_query($str);
        $optPks = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        echo '<table><tr><td>'.$_SESSION['lang']['tahun'].'</td><td>:<select id=tahun>'.$optTahun."</select></td></tr>\r\n                      <tr><td>Org</td><td><select id=pks style='width:150px;'> \r\n                       ".$optPks."\r\n                     </select>   \r\n                     </tr></table>\r\n                     <center><button onclick=get001('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_trkpenggunaanBBM_form':
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where length(kodeorganisasi)=4 and tipe in ('KEBUN','TRAKSI') order by namaorganisasi";
        $res = mysql_query($str);
        $optPks = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        while ($bar = mysql_fetch_object($res)) {
            $optPks .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'-'.$bar->namaorganisasi.'</option>';
        }
        echo '<table><tr><td>'.$_SESSION['lang']['tahun'].'</td><td>:<select id=tahun>'.$optTahun."</select></td></tr>\r\n                      <tr><td>Org</td><td><select id=pks style='width:150px;'> \r\n                       ".$optPks."\r\n                     </select>   \r\n                     </tr></table>\r\n                     <center><button onclick=get001('".$id."') class=mybutton>".$_SESSION['lang']['proses'].'</button></center>';

        break;
    case 'bi_purRealisasiKapitalDanNon':
        $optRegional .= "<option value=''>".$_SESSION['lang']['all'].'</option>';
        $sRegion = 'select distinct regional from '.$dbname.".bgt_regional where regional not in ('DKI','LAMPUNG') order by regional asc";
        $qRegion = mysql_query($sRegion) || exit(mysql_error($conns));
        while ($rRegion = mysql_fetch_assoc($qRegion)) {
            $optRegional .= "<option value='".$rRegion['regional']."'>".$rRegion['regional'].'</option>';
        }
        $arrTipe = [1 => 'Kapital', 2 => 'Non Kapital'];
        $optPt = $optTipe = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        foreach ($arrTipe as $lstTipe => $dtTipe) {
            $optTipe .= "<option value='".$lstTipe."'>".$dtTipe.'</option>';
        }
        $optperiode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $optPt = $optperiode;
        $sOrg = 'select distinct periode from '.$dbname.'.setup_periodeakuntansi order by periode desc';
        $qOrg = mysql_query($sOrg) || exit(mysql_error($conns));
        while ($rOrg = mysql_fetch_assoc($qOrg)) {
            $optperiode .= '<option value='.$rOrg['periode'].'>'.$rOrg['periode'].'</option>';
        }
        $arr = '##periode';
        $derk = 1;
        echo "\r\n        <table cellspacing=\"1\" border=\"0\" >\r\n        <tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id='periode' style=\"width:120px;\">".$optperiode.'</select></td></tr>';
        $arr .= '##jenis';
        echo "<tr><td colspan=\"4\" align=center><input type=hidden id=jenis value='global'/>\r\n        <button onclick=\"get005('bi_purRealisasiKapitalDanNon','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['proses']."</button>\r\n        </td></tr>\r\n        </table>\r\n        ";

        break;
    case 'bi_purRealisasiKlmpk':
        $optperiode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $optPt = $optperiode;
        $sOrg = 'select distinct periode from '.$dbname.'.setup_periodeakuntansi order by periode desc';
        $qOrg = mysql_query($sOrg) || exit(mysql_error($conns));
        while ($rOrg = mysql_fetch_assoc($qOrg)) {
            $optperiode .= '<option value='.$rOrg['periode'].'>'.$rOrg['periode'].'</option>';
        }
        $arr = '##periode';
        $derk = 1;
        echo "\r\n        <table cellspacing=\"1\" border=\"0\" >\r\n        <tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id='periode' style=\"width:120px;\">".$optperiode.'</select></td></tr>';
        $arr .= '##jenis';
        echo "<tr><td colspan=\"4\" align=center><input type=hidden id=jenis value='global'/>\r\n        <button onclick=\"get005('bi_purRealisasiKlmpk','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['proses']."</button>\r\n        </td></tr>\r\n        </table>\r\n        ";

        break;
}

?>