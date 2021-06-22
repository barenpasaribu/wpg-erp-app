<?php



require 'lib/fpdf.php';

class PDF extends FPDF_ImageAlpha extends FPDF
{
    public $tmpFiles = [];

    public function Image($file, $x, $y, $w = 0, $h = 0, $type = '', $link = '', $isMask = false, $maskImg = 0)
    {
        if (!isset($this->images[$file])) {
            if ('' === $type) {
                $pos = strrpos($file, '.');
                if (!$pos) {
                    $this->Error('Image file has no extension and no type was specified: '.$file);
                }

                $type = substr($file, $pos + 1);
            }

            $type = strtolower($type);
            $mqr = get_magic_quotes_runtime();
            set_magic_quotes_runtime(0);
            if ('jpg' === $type || 'jpeg' === $type) {
                $info = $this->_parsejpg($file);
            } else {
                if ('png' === $type) {
                    $info = $this->_parsepng($file);
                    if ('alpha' === $info) {
                        return $this->ImagePngWithAlpha($file, $x, $y, $w, $h, $link);
                    }
                } else {
                    $mtd = '_parse'.$type;
                    if (!method_exists($this, $mtd)) {
                        $this->Error('Unsupported image type: '.$type);
                    }

                    $info = $this->$mtd($file);
                }
            }

            set_magic_quotes_runtime($mqr);
            if ($isMask) {
                $info['cs'] = 'DeviceGray';
            }

            $info['i'] = count($this->images) + 1;
            if (0 < $maskImg) {
                $info['masked'] = $maskImg;
            }

            $this->images[$file] = $info;
        } else {
            $info = $this->images[$file];
        }

        if (0 === $w && 0 === $h) {
            $w = $info['w'] / $this->k;
            $h = $info['h'] / $this->k;
        }

        if (0 === $w) {
            $w = ($h * $info['w']) / $info['h'];
        }

        if (0 === $h) {
            $h = ($w * $info['h']) / $info['w'];
        }

        if (1.7 <= (float) FPDF_VERSION) {
            if ($isMask) {
                $x = (('P' === $this->CurOrientation ? $this->CurPageSize[0] : $this->CurPageSize[1])) + 10;
            }
        } else {
            if ($isMask) {
                $x = (('P' === $this->CurOrientation ? $this->CurPageFormat[0] : $this->CurPageFormat[1])) + 10;
            }
        }

        $this->_out(sprintf('q %.2f 0 0 %.2f %.2f %.2f cm /I%d Do Q', $w * $this->k, $h * $this->k, $x * $this->k, ($this->h - ($y + $h)) * $this->k, $info['i']));
        if ($link) {
            $this->Link($x, $y, $w, $h, $link);
        }

        return $info['i'];
    }

    public function ImagePngWithAlpha($file, $x, $y, $w = 0, $h = 0, $link = '')
    {
        $tmp_alpha = tempnam('.', 'mska');
        $this->tmpFiles[] = $tmp_alpha;
        $tmp_plain = tempnam('.', 'mskp');
        $this->tmpFiles[] = $tmp_plain;
        list($wpx, $hpx) = getimagesize($file);
        $img = imagecreatefrompng($file);
        $alpha_img = imagecreate($wpx, $hpx);
        for ($c = 0; $c < 256; ++$c) {
            ImageColorAllocate($alpha_img, $c, $c, $c);
        }
        for ($xpx = 0; $xpx < $wpx; ++$xpx) {
            for ($ypx = 0; $ypx < $hpx; ++$ypx) {
                $color_index = imagecolorat($img, $xpx, $ypx);
                $alpha = 255 - (($color_index >> 24) * 255) / 127;
                imagesetpixel($alpha_img, $xpx, $ypx, $alpha);
            }
        }
        imagepng($alpha_img, $tmp_alpha);
        imagedestroy($alpha_img);
        $plain_img = imagecreatetruecolor($wpx, $hpx);
        imagecopy($plain_img, $img, 0, 0, 0, 0, $wpx, $hpx);
        imagepng($plain_img, $tmp_plain);
        imagedestroy($plain_img);
        $maskImg = $this->Image($tmp_alpha, 0, 0, 0, 0, 'PNG', '', true);
        $this->Image($tmp_plain, $x, $y, $w, $h, 'PNG', $link, false, $maskImg);
    }

    public function Close()
    {
        parent::Close();
        foreach ($this->tmpFiles as $tmp) {
            @unlink($tmp);
        }
    }

    public function _putimages()
    {
        $filter = ($this->compress ? '/Filter /FlateDecode ' : '');
        reset($this->images);
        while (list($file, $info) = each($this->images)) {
            $this->_newobj();
            $this->images[$file]['n'] = $this->n;
            $this->_out('<</Type /XObject');
            $this->_out('/Subtype /Image');
            $this->_out('/Width '.$info['w']);
            $this->_out('/Height '.$info['h']);
            if (isset($info['masked'])) {
                $this->_out('/SMask '.($this->n - 1).' 0 R');
            }

            if ('Indexed' === $info['cs']) {
                $this->_out('/ColorSpace [/Indexed /DeviceRGB '.(strlen($info['pal']) / 3 - 1).' '.($this->n + 1).' 0 R]');
            } else {
                $this->_out('/ColorSpace /'.$info['cs']);
                if ('DeviceCMYK' === $info['cs']) {
                    $this->_out('/Decode [1 0 1 0 1 0 1 0]');
                }
            }

            $this->_out('/BitsPerComponent '.$info['bpc']);
            if (isset($info['f'])) {
                $this->_out('/Filter /'.$info['f']);
            }

            if (isset($info['parms'])) {
                $this->_out($info['parms']);
            }

            if (isset($info['trns']) && is_array($info['trns'])) {
                $trns = '';
                for ($i = 0; $i < count($info['trns']); ++$i) {
                    $trns .= $info['trns'][$i].' '.$info['trns'][$i].' ';
                }
                $this->_out('/Mask ['.$trns.']');
            }

            $this->_out('/Length '.strlen($info['data']).'>>');
            $this->_putstream($info['data']);
            unset($this->images[$file]['data']);
            $this->_out('endobj');
            if ('Indexed' === $info['cs']) {
                $this->_newobj();
                $pal = ($this->compress ? gzcompress($info['pal']) : $info['pal']);
                $this->_out('<<'.$filter.'/Length '.strlen($pal).'>>');
                $this->_putstream($pal);
                $this->_out('endobj');
            }
        }
    }

    public function _parsepng($file)
    {
        $f = fopen($file, 'rb');
        if (!$f) {
            $this->Error("Can't open image file: ".$file);
        }

        if (fread($f, 8) !== chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10)) {
            $this->Error('Not a PNG file: '.$file);
        }

        fread($f, 4);
        if ('IHDR' !== fread($f, 4)) {
            $this->Error('Incorrect PNG file: '.$file);
        }

        $w = $this->_readint($f);
        $h = $this->_readint($f);
        $bpc = ord(fread($f, 1));
        if (8 < $bpc) {
            $this->Error('16-bit depth not supported: '.$file);
        }

        $ct = ord(fread($f, 1));
        if (0 === $ct) {
            $colspace = 'DeviceGray';
        } else {
            if (2 === $ct) {
                $colspace = 'DeviceRGB';
            } else {
                if (3 === $ct) {
                    $colspace = 'Indexed';
                } else {
                    fclose($f);

                    return 'alpha';
                }
            }
        }

        if (0 !== ord(fread($f, 1))) {
            $this->Error('Unknown compression method: '.$file);
        }

        if (0 !== ord(fread($f, 1))) {
            $this->Error('Unknown filter method: '.$file);
        }

        if (0 !== ord(fread($f, 1))) {
            $this->Error('Interlacing not supported: '.$file);
        }

        fread($f, 4);
        $parms = '/DecodeParms <</Predictor 15 /Colors '.((2 === $ct ? 3 : 1)).' /BitsPerComponent '.$bpc.' /Columns '.$w.'>>';
        $pal = '';
        $trns = '';
        $data = '';
        do {
            $n = $this->_readint($f);
            $type = fread($f, 4);
            if ('PLTE' === $type) {
                $pal = fread($f, $n);
                fread($f, 4);
            } else {
                if ('tRNS' === $type) {
                    $t = fread($f, $n);
                    if (0 === $ct) {
                        $trns = [ord(substr($t, 1, 1))];
                    } else {
                        if (2 === $ct) {
                            $trns = [ord(substr($t, 1, 1)), ord(substr($t, 3, 1)), ord(substr($t, 5, 1))];
                        } else {
                            $pos = strpos($t, chr(0));
                            if (false !== $pos) {
                                $trns = [$pos];
                            }
                        }
                    }

                    fread($f, 4);
                } else {
                    if ('IDAT' === $type) {
                        $data .= fread($f, $n);
                        fread($f, 4);
                    } else {
                        if ('IEND' === $type) {
                            break;
                        }

                        fread($f, $n + 4);
                    }
                }
            }
        } while ($n);
        if ('Indexed' === $colspace && empty($pal)) {
            $this->Error('Missing palette in '.$file);
        }

        fclose($f);

        return ['w' => $w, 'h' => $h, 'cs' => $colspace, 'bpc' => $bpc, 'f' => 'FlateDecode', 'parms' => $parms, 'pal' => $pal, 'trns' => $trns, 'data' => $data];
    }
}

?>