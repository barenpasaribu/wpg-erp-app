<?php
    require_once 'includes/header.php';
?>
<!-- Add jQuery library -->
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>

<!-- Add mousewheel plugin (this is optional) -->
<script type="text/javascript" src="<?=base_url()?>assets/js/fancybox/lib/jquery.mousewheel-3.0.6.pack.js"></script>

<!-- Add fancyBox -->
<link rel="stylesheet" href="<?=base_url()?>assets/js/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
<script type="text/javascript" src="<?=base_url()?>assets/js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>

<!-- Optionally add helpers - button, thumbnail and/or media -->
<link rel="stylesheet" href="<?=base_url()?>assets/js/fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" type="text/css" media="screen" />
<script type="text/javascript" src="<?=base_url()?>assets/js/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
<script type="text/javascript" src="<?=base_url()?>assets/js/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.6"></script>

<link rel="stylesheet" href="<?=base_url()?>assets/js/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" type="text/css" media="screen" />
<script type="text/javascript" src="<?=base_url()?>assets/js/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>

<script type="text/javascript">
	$(document).ready(function() {
		$(".fancybox").fancybox();
	});
	function openReceipt(ele){
		var myWindow = window.open(ele, "", "width=380, height=550");
	}	
</script>




<div class="page-wrapper">
 <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-12 d-flex no-block align-items-center">
                        <h4 class="page-title">Daftar Data Kodeblok</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Kodeblok</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Daftar Data Kodeblok</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

	<br><br>
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
	
       
	  <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Data Kode Blok</h5>
                                <div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered">
                                        <thead>
                                          <tr>
								    	<th width="10%">No</th>
								    	<th width="20%">JOBCODE</th>
								    	<th width="20%">JOBDESC</th>
									    <th width="15%">Barcode</th>
									</tr>
                                        </thead>
                                        <tbody>
                                        <?php
								$no = 1;
								foreach ($job as $t) { ?>
									<tr>
										<th><?php echo $no++;?></th>
										<th><?php echo $t['JOBCODE'];?></th>
										<th><?php echo $t['JOBDESC'];?></th>
										<th>
											<a onclick="openReceipt('<?=base_url()?>job/cetak_barcode?pcode=<?php echo $t['JOBCODE']; ?>')" style="text-decoration: none; cursor: pointer;" title="Print Barcode">
		 									<i class="fas fa-barcode"></i>
											</a>
										</th>
									</tr>
								 <?php } ?>
                                        </tbody>
                                        <tfoot>
                                           <tr>
								    	<th width="10%">No</th>
								    	<th width="20%">JOBCODE</th>
								    	<th width="20%">JOBDESC</th>
									    <th width="15%">Barcode</th>
									</tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>


				
				
</div><!-- Right Colmn // END -->
	<script src="<?=base_url()?>assets/belakang/design/extra-libs/multicheck/datatable-checkbox-init.js"></script>
    <script src="<?=base_url()?>assets/belakang/design/extra-libs/multicheck/jquery.multicheck.js"></script>
    <script src="<?=base_url()?>assets/belakang/design/extra-libs/DataTables/datatables.min.js"></script>
	 <script>
        $('#zero_config').DataTable();
    </script>
	
<?php
    require_once 'includes/footer.php';
?>