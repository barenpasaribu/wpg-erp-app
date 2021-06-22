<?php



function cekAkun($noakun)
{
    $akunTanaman = ['12601', '12602', '12603', '12604', '12605', '12606', '12607', '12608', '12609', '12610', '12611', '12612', '12613', '12614', '12615', '12616', '12617', '12801', '12802', '61101', '61102', '62101', '62102', '62103', '62104', '62105', '62106', '62107', '62108', '62109', '62110', '62111'];
    $akun = substr(str_replace(' ', '', $noakun), 0, 5);
    $default = false;
    foreach ($akunTanaman as $val) {
        if ($akun == $val) {
            $default = true;
        }
    }

    return $default;
}

function cekAkunPiutang($noakun)
{
    $akunPiutang = ['11401', '11402', '11404'];
    $akun = substr(str_replace(' ', '', $noakun), 0, 5);
    $default = false;
    foreach ($akunPiutang as $val) {
        if ($akun == $val) {
            $default = true;
        }
    }

    return $default;
}

function cekAkunHutang($noakun)
{
    $akunHutang = ['2111101', '2111102', '2111201', '2111202', '1140500'];
    $akun = substr(str_replace(' ', '', $noakun), 0, 7);
    $default = false;
    foreach ($akunHutang as $val) {
        if ($akun == $val) {
            $default = true;
        }
    }

    return $default;
}

function cekAkunTrans($noakun)
{
    $akunTrans = ['41102'];
    $akun = substr(str_replace(' ', '', $noakun), 0, 5);
    $default = false;
    foreach ($akunTrans as $val) {
        if ($akun == $val) {
            $default = true;
        }
    }

    return $default;
}

?>