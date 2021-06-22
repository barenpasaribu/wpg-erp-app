<?php
	// include_once 'lib/zForm.php';
	include_once 'lib/eagrolib.php';
	// include_once 'lib/zMysql.php';
	include_once 'config/connection.php';

	require_once 'master_validation.php';
	// include 'lib/eagrolib.php';
	// include 'lib/zLib.php';
	// include 'lib/zFunction.php';

	echo open_body();

    include 'master_mainMenu.php';
    // $to = "jherm4n@gmail.com";
    // $subject = "Email Anthesis";
    // $body = "Isi pesan nya disini";
    // kirimEmailWindows($to, $subject, $body);
    // die();
?>
	<div style='width:100%;'>
		<fieldset>
			<legend><span class=judul>&nbsp;</span></legend>
			<div id='contentBox' style='overflow:auto;'>
				<fieldset><legend><?= $_SESSION['lang']['list']; ?></legend>
				<table id="example" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody id="dataGeneral">
                <?php
                    $str = 'SELECT * FROM pabrik_5general 
                            WHERE 
                                kodeorg="'.$_SESSION['empl']['lokasitugas'].'" 
                            order by 
                                code ASC';
                    $res = mysql_query($str);
                    $no = 0;
                    while ($bar = mysql_fetch_object($res)) {
                ?>
                    <tr class="rowcontent" id="tr_<?= $no ?>">
                        <td title="<?= $bar->description ?>"><?= $bar->code ?></td>
                        <td> <input type="text" id="<?= $bar->code ?>" value="<?= $bar->nilai ?>" onchange="ubahNilai('<?= $bar->code ?>');"></td>
                    </tr>
                <?php 
                    $no++;
                    } 
                ?>
                
            </tbody>
        </table>
				</fieldset>
			</div>
		</fieldset>
    </div>
	<script type="text/javascript" src="/lib/awan/jQuery-3.3.1/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="/lib/awan/DataTables-1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="/lib/awan/sweetalert2/dist/sweetalert2.min.js"></script>
	<!-- <script type="text/javascript" src="js/zTools.js"></script> -->
	<script type="text/javascript" src="js/pabrik_5general.js"></script>
	<script type="text/javascript">
		$( document ).ready(function() {
			$('head').append($('<link rel="stylesheet" type="text/css" />').attr('href', '/lib/awan/DataTables-1.10.21/css/jquery.dataTables.min.css'));
			$('head').append($('<link rel="stylesheet" type="text/css" />').attr('href', '/lib/awan/DataTables-1.10.21/css/dataTables.bootstrap4.min.css'));
			$('head').append($('<link rel="stylesheet" type="text/css" />').attr('href', '/lib/awan/DataTables-1.10.21/css/dataTables.jqueryui.min.css'));
			$('head').append($('<link rel="stylesheet" type="text/css" />').attr('href', '/lib/awan/sweetalert2/dist/sweetalert2.min.css'));
            $('#example').DataTable();
            // $('#dataGeneral').empty();
            // loadData();
            // Swal.fire('Oops...', 'Something went wrong!', 'error');
		});
	</script>
	
	</body>
</html>