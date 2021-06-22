<?php



function readLst($file)
{
    $comment = '#';
    $fp = fopen($file, 'r');
    $list = [];
    $lin = 0;
    while (!feof($fp)) {
        $line = fgets($fp, 4096);
        if (!mb_ereg('^#', $line) && '' !== $line) {
            $pieces = explode('=', $line);
            foreach ($pieces as $content) {
                $list[$lin][] = $content;
            }
            ++$lin;
        }
    }
    fclose($fp);

    return $list;
}

function lst2opt($arr, $kode, $nama)
{
    $resArr = [];
    foreach ($arr as $row) {
        $resArr[$row[$kode]] = $row[$nama];
    }

    return $resArr;
}

?>