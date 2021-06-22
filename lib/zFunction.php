<?php


function ambilLokasiTugasDanTurunannya($returntype, $lokasitugas)
{
    global $dbname;
    global $conn;
    $arr = [];
    $list = '';
    $option = '';
    $str = 'select distinct kodeorganisasi,namaorganisasi,tipe from '.$dbname.".organisasi where kodeorganisasi='".$lokasitugas."' and tipe not in('BLOK','STENGINE','STATION') order by kodeorganisasi";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        if ('PT' == $bar->tipe) {
            continue;
        }

        if ('array' == $returntype) {
            array_push($arr, $bar->kodeorganisasi);
        } else {
            if ('' == $list && 'list' == $returntype) {
                $list = $bar->kodeorganisasi;
            } else {
                if ('' != $list && 'list' == $returntype) {
                    $list .= '|'.$bar->kodeorganisasi;
                } else {
                    $option .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
                }
            }
        }

        $str1 = 'select kodeorganisasi,namaorganisasi,tipe from '.$dbname.".organisasi where induk='".$bar->kodeorganisasi."' and tipe not in('BLOK','STENGINE','STATION') order by kodeorganisasi";
        $res1 = mysql_query($str1);
        while ($bar1 = mysql_fetch_object($res1)) {
            if ('PT' == $bar1->tipe) {
                continue;
            }

            if ('array' == $returntype) {
                array_push($arr, $bar1->kodeorganisasi);
            } else {
                if ('' == $list && 'list' == $returntype) {
                    $list = $bar1->kodeorganisasi;
                } else {
                    if ('' != $list && 'list' == $returntype) {
                        $list .= '|'.$bar1->kodeorganisasi;
                    } else {
                        $option .= "<option value='".$bar1->kodeorganisasi."'>".$bar1->namaorganisasi.'</option>';
                    }
                }
            }

            $str2 = 'select kodeorganisasi,namaorganisasi,tipe from '.$dbname.".organisasi where induk='".$bar1->kodeorganisasi."' and tipe not in('BLOK','STENGINE','STATION') order by kodeorganisasi";
            $res2 = mysql_query($str2);
            while ($bar2 = mysql_fetch_object($res2)) {
                if ('PT' == $bar2->tipe) {
                    continue;
                }

                if ('array' == $returntype) {
                    array_push($arr, $bar2->kodeorganisasi);
                } else {
                    if ('' == $list && 'list' == $returntype) {
                        $list = $bar2->kodeorganisasi;
                    } else {
                        if ('' != $list && 'list' == $returntype) {
                            $list .= '|'.$bar2->kodeorganisasi;
                        } else {
                            $option .= "<option value='".$bar2->kodeorganisasi."'>".$bar2->namaorganisasi.'</option>';
                        }
                    }
                }

                $str3 = 'select kodeorganisasi,namaorganisasi,tipe from '.$dbname.".organisasi where induk='".$bar2->kodeorganisasi."' and tipe not in('BLOK','STENGINE','STATION') order by kodeorganisasi";
                $res3 = mysql_query($str3);
                while ($bar3 = mysql_fetch_object($res3)) {
                    if ('PT' == $bar3->tipe) {
                        continue;
                    }

                    if ('array' == $returntype) {
                        array_push($arr, $bar3->kodeorganisasi);
                    } else {
                        if ('' == $list && 'list' == $returntype) {
                            $list = $bar3->kodeorganisasi;
                        } else {
                            if ('' != $list && 'list' == $returntype) {
                                $list .= '|'.$bar3->kodeorganisasi;
                            } else {
                                $option .= "<option value='".$bar3->kodeorganisasi."'>".$bar3->namaorganisasi.'</option>';
                            }
                        }
                    }

                    $str4 = 'select kodeorganisasi,namaorganisasi,tipe from '.$dbname.".organisasi where induk='".$bar3->kodeorganisasi."' and tipe not in('BLOK','STENGINE','STATION') order by kodeorganisasi";
                    $res4 = mysql_query($str4);
                    while ($bar4 = mysql_fetch_object($res4)) {
                        if ('PT' == $bar4->tipe) {
                            continue;
                        }

                        if ('array' == $returntype) {
                            array_push($arr, $bar4->kodeorganisasi);
                        } else {
                            if ('' == $list && 'list' == $returntype) {
                                $list = $bar4->kodeorganisasi;
                            } else {
                                if ('' != $list && 'list' == $returntype) {
                                    $list .= '|'.$bar4->kodeorganisasi;
                                } else {
                                    $option .= "<option value='".$bar4->kodeorganisasi."'>".$bar4->namaorganisasi.'</option>';
                                }
                            }
                        }

                        $str5 = 'select kodeorganisasi,namaorganisasi,tipe from '.$dbname.".organisasi where induk='".$bar4->kodeorganisasi."' and tipe not in('BLOK','STENGINE','STATION') order by kodeorganisasi";
                        $res5 = mysql_query($str5);
                        while ($bar5 = mysql_fetch_object($res5)) {
                            if ('PT' == $bar5->tipe) {
                                continue;
                            }

                            if ('array' == $returntype) {
                                array_push($arr, $bar5->kodeorganisasi);
                            } else {
                                if ('' == $list && 'list' == $returntype) {
                                    $list = $bar5->kodeorganisasi;
                                } else {
                                    if ('' != $list && 'list' == $returntype) {
                                        $list .= '|'.$bar5->kodeorganisasi;
                                    } else {
                                        $option .= "<option value='".$bar5->kodeorganisasi."'>".$bar5->namaorganisasi.'</option>';
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    if ('array' == $returntype) {
        return $arr;
    }

    if ('list' == $returntype) {
        return $list;
    }

    return $option;
}

function namakaryawan($db, $conn, $userid)
{
    $namakaryawan = '';
    $strx = 'select namakaryawan from '.$db.'.datakaryawan where karyawanid='.$userid;
    $resx = mysql_query($strx);
    while ($barx = mysql_fetch_object($resx)) {
        $namakaryawan = $barx->namakaryawan;
    }

    return $namakaryawan;
}

function ambilUnitPembebananBarang($returntype = 'array')
{
    global $dbname;
    global $conn;
    $arr = [];
    $list = '';
    $option = '';
    $str = 'select distinct kodeorganisasi,namaorganisasi,tipe from '.$dbname.".organisasi \r\n\t      where length(kodeorganisasi)=4\r\n\t\t  and induk!=''\r\n\t\t  and kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by namaorganisasi desc";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        if ('PT' == $bar->tipe) {
            continue;
        }

        if ('array' == $returntype) {
            array_push($arr, $bar->kodeorganisasi);
        } else {
            if ('' == $list && 'list' == $returntype) {
                $list = $bar->kodeorganisasi;
            } else {
                if ('' != $list && 'list' == $returntype) {
                    $list .= '|'.$bar->kodeorganisasi;
                } else {
                    $option .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
                }
            }
        }
    }
    if ('array' == $returntype) {
        return $arr;
    }

    if ('list' == $returntype) {
        return $list;
    }

    return $option;
}

function ambilSubUnit($returntype, $induk)
{
    global $dbname;
    global $conn;
    $arr = [];
    $list = '';
    $option = '';
    $str = 'select distinct kodeorganisasi,namaorganisasi,tipe from '.$dbname.".organisasi where induk='".$induk."' order by kodeorganisasi";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        if ('PT' == $bar->tipe) {
            continue;
        }

        if ('array' == $returntype) {
            array_push($arr, $bar->kodeorganisasi);
        } else {
            if ('' == $list && 'list' == $returntype) {
                $list = $bar->kodeorganisasi;
            } else {
                if ('' != $list && 'list' == $returntype) {
                    $list .= '|'.$bar->kodeorganisasi;
                } else {
                    $option .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
                }
            }
        }
    }
    if ('array' == $returntype) {
        return $arr;
    }

    if ('list' == $returntype) {
        return $list;
    }

    return $option;
}

function getVhcCode($returntype, $kodeunit)
{
    global $dbname;
    global $conn;
    $arr = [];
    $list = '';
    $option = '';
    $str = 'select * from '.$dbname.".vhc_5master where kodeorg='".$kodeunit."'\r\n\t or kodeorg in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$kodeunit."')\r\n\t order by kodevhc";
    $res = mysql_query($str);
    $no = 0;
    while ($bar1 = mysql_fetch_object($res)) {
        $no++;
        $str = 'select namajenisvhc from '.$dbname.".vhc_5jenisvhc where jenisvhc='".$bar1->jenisvhc."'";
        $res1 = mysql_query($str);
        $namabarang = '';
        while ($bar = mysql_fetch_object($res1)) {
            $namabarang = $bar->namajenisvhc;
        }
        if ('array' == $returntype) {
            array_push($arr, $bar1->kodevhc);
        } else {
            if ('' == $list && 'list' == $returntype) {
                $list = $bar1->kodevhc;
            } else {
                if ('' != $list && 'list' == $returntype) {
                    $list .= '|'.$bar1->kodevhc;
                } else {
                    $option .= "<option value='".$bar1->kodevhc."'>[".$bar1->kodevhc.']-'.$namabarang.'</option>';
                }
            }
        }
    }
    if ('array' == $returntype) {
        return $arr;
    }

    if ('list' == $returntype) {
        return $list;
    }

    return $option;
}

function getGudangPT($returntype, $gudang)
{
    global $dbname;
    global $conn;
    $arr = [];
    $list = '';
    $option = '';
    $str = 'select distinct kodeorg from '.$dbname.".log_5masterbarangdt where kodegudang='".$gudang."' \r\n\t      order by kodeorg";
    $res = mysql_query($str);
    $no = 0;
    while ($bar1 = mysql_fetch_object($res)) {
        $no++;
        $str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$bar1->kodeorg."'";
        $res1 = mysql_query($str);
        while ($bar = mysql_fetch_object($res1)) {
            $namapt = $bar->namaorganisasi;
        }
        if ('array' == $returntype) {
            array_push($arr, $bar1->kodeorg);
        } else {
            if ('' == $list && 'list' == $returntype) {
                $list = $bar1->kodeorg;
            } else {
                if ('' != $list && 'list' == $returntype) {
                    $list .= '|'.$bar1->kodeorg;
                } else {
                    $option .= "<option value='".$bar1->kodeorg."'>[".$bar1->kodeorg.']-'.$namapt.'</option>';
                }
            }
        }
    }
    if ('array' == $returntype) {
        return $arr;
    }

    if ('list' == $returntype) {
        return $list;
    }

    return $option;
}

function getKegiatanBlok($returntype, $blok)
{
    global $dbname;
    global $conn;
    $arr = [];
    $list = '';
    $option = '';
    $str = 'select statusblok from '.$dbname.".setup_blok where kodeorg='".$blok."'";
    $res = mysql_query($str);
    $no = 0;
    while ($bar1 = mysql_fetch_object($res)) {
        $no++;
        if ('TM' == $bar1->statusblok) {
            $str = 'select kodekegiatan,kelompok,namakegiatan from '.$dbname.".setup_kegiatan where (kelompok='TM' or kelompok='PNN') order by kelompok,namakegiatan";
        } else {
            $str = 'select kodekegiatan,kelompok,namakegiatan from '.$dbname.".setup_kegiatan where kelompok='".$bar1->statusblok."' order by kelompok,namakegiatan";
        }

        $res1 = mysql_query($str);
        while ($bar = mysql_fetch_object($res1)) {
            if ('array' == $returntype) {
                array_push($arr, $bar1->kodekegiatan);
            } else {
                if ('' == $list && 'list' == $returntype) {
                    $list = $bar1->kodekegiatan;
                } else {
                    if ('' != $list && 'list' == $returntype) {
                        $list .= '|'.$bar1->kodekegiatan;
                    } else {
                        $option .= "<option value='".$bar->kodekegiatan."'>[".$bar->kelompok.']-'.$bar->namakegiatan.'</option>';
                    }
                }
            }
        }
    }
    if ('array' == $returntype) {
        return $arr;
    }

    if ('list' == $returntype) {
        return $list;
    }

    return $option;
}

function ambilSeluruhGudang($returntype, $kecuali)
{
    global $dbname;
    global $conn;
    $arr = [];
    $list = '';
    $option = '';
    $no = 0;
    $no++;
    $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi \r\n\t\t      where tipe='GUDANG' and kodeorganisasi<>'".$kecuali."' order by namaorganisasi desc";
    $res1 = mysql_query($str);
    while ($bar = mysql_fetch_object($res1)) {
        if ('array' == $returntype) {
            array_push($arr, $bar1->kodeorganisasi);
        } else {
            if ('' == $list && 'list' == $returntype) {
                $list = $bar1->kodeorganisasi;
            } else {
                if ('' != $list && 'list' == $returntype) {
                    $list .= '|'.$bar1->kodeorganisasi;
                } else {
                    $option .= "<option value='".$bar->kodeorganisasi."'>[".$bar->kodeorganisasi.']-'.$bar->namaorganisasi.'</option>';
                }
            }
        }
    }
    if ('array' == $returntype) {
        return $arr;
    }

    if ('list' == $returntype) {
        return $list;
    }

    return $option;
}

?>