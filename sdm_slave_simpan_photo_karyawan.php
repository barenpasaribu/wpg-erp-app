<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$karyawanid = $_POST['karyawanid'];
$path = 'photokaryawan';
if ($_FILES['photo']['size'] <= $_POST['MAX_FILE_SIZE']) {
    if (is_dir($path)) {
        writeFile($path);
        chmod($path, 511);
    } else {
        if (mkdir($path)) {
            writeFile($path);
            chmod($path, 511);
        } else {
            echo "<script> alert('Gagal, Can`t create folder for uploaded file');</script>";
            exit(0);
        }
    }
} else {
    echo '<script>File size is '.filesize($_FILES['photo']['tmp_name']).', greater then allowed</script>';
}

function writeFile($path)
{
    global $karyawanid;
    global $conn;
    global $dbname;
    $dir = $path;
    $ext = preg_split('/[.]/D', basename($_FILES['photo']['name']));
    $ext = $ext[count($ext) - 1];
    $ext = strtolower($ext);
    if ('jpg' == $ext || 'jpeg' == $ext || 'gif' == $ext || 'png' == $ext || 'bmp' == $ext) {
        $path = $dir.'/'.$karyawanid.'.'.$ext;

        try {
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $path)) {
                $str = 'update '.$dbname.".datakaryawan set photo='".$path."'\r\n\t\t\t\t      where karyawanid=".$karyawanid;
                mysql_query($str);
                if (0 < mysql_affected_rows($conn)) {
                    echo "<script>\r\n\t\t\t\t\tparent.document.getElementById('displayphoto').removeAttribute('src');\r\n\t\t\t\t\tparent.document.getElementById('displayphoto').setAttribute('src','".$path."');\r\n\t\t\t\t\t//parent.document.getElementById('displayphoto').getAttribute('src').value;\r\n\t\t\t\t\t</script>";
                }

                chmod($path, 509);
            }
        } catch (Exception $e) {
            echo '<script>alert("Error Writing File'.addslashes($e->getMessage()).'");</script>';
        }
    } else {
        echo "<script>alert('Filetype not support');</script>";
    }
}

?>