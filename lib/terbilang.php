<?php



function kekata($x)
{
    $x = abs($x);
    $angka = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
    $temp = '';
    if ($x < 12) {
        $temp = ' '.$angka[$x];
    } else {
        if ($x < 20) {
            $temp = kekata($x - 10).' belas';
        } else {
            if ($x < 100) {
                $temp = kekata($x / 10).' puluh'.kekata($x % 10);
            } else {
                if ($x < 200) {
                    $temp = ' seratus'.kekata($x - 100);
                } else {
                    if ($x < 1000) {
                        $temp = kekata($x / 100).' ratus'.kekata($x % 100);
                    } else {
                        if ($x < 2000) {
                            $temp = ' seribu'.kekata($x - 1000);
                        } else {
                            if ($x < 1000000) {
                                $temp = kekata($x / 1000).' ribu'.kekata($x % 1000);
                            } else {
                                if ($x < 1000000000) {
                                    $temp = kekata($x / 1000000).' juta'.kekata($x % 1000000);
                                } else {
                                    if ($x < 1000000000000) {
                                        $temp = kekata($x / 1000000000).' milyar'.kekata(fmod($x, 1000000000));
                                    } else {
                                        if ($x < 1E+15) {
                                            $temp = kekata($x / 1000000000000).' trilyun'.kekata(fmod($x, 1000000000000));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    return $temp;
}

function terbilang($x, $style)
{
    if ($x < 0) {
        $hasil = 'minus '.trim(kekata($x));
    } else {
        $hasil = trim(kekata($x));
    }

    switch ($style) {
        case 1:
            $hasil = ucwords($hasil);

            break;
        case 2:
            $hasil = ucwords($hasil);

            break;
        case 3:
            $hasil = ucwords($hasil);

            break;
        default:
            $hasil = ucwords($hasil);

            break;
    }

    return $hasil;
}

?>