
<html>
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Print Barcode : <?php echo $pcode; ?></title>
	<script src="<?=base_url()?>assets/js/jquery-1.7.2.min.js"></script>
</head>

<body>
	
	<center>
		<?php
            $this->load->library('Barcode39');
            // set Barcode39 object
            $bc = new Barcode39("$pcode");
            // set text size
            $bc->barcode_text_size = 1;

            $prod_name = '';
            $prod_price = '';
            $prod_code = '';

            $prodDtaResult = $this->db->query("SELECT * FROM job WHERE JOBCODE = '$pcode' ");
            $prodDtaRows = $prodDtaResult->num_rows();
            if ($prodDtaRows == 1) {
                $prodDtaData = $prodDtaResult->result();

                $prod_name = $prodDtaData[0]->JOBCODE;
                $prod_desc = $prodDtaData[0]->JOBDESC;

                unset($prodDtaData);
            }
            unset($prodDtaResult);
            unset($prodDtaRows);

            // display new barcode
            $bc->draw('./assets/barcode/'.$pcode.'.gif');
        ?>
        <table border="0" style="border-collapse: collapse; margin-bottom: 0px;" width="140px" height="auto">
			<tr>
				<td style="font-family: Arial, Helvetica, sans-serif; text-align: center; font-size: 12px;">
					<?php echo $prod_code; ?>
				</td>
			</tr>
			<tr>
				<td style="font-family: Arial, Helvetica, sans-serif; text-align: center; font-size: 12px;">
					<img src="<?=base_url()?>assets/barcode/<?php echo $pcode; ?>.gif" />
				</td>
			</tr>
			<tr>
				<td style="font-family: Arial, Helvetica, sans-serif; text-align: center; font-size: 11px;">
					<?php echo $prod_desc;?>
				</td>
			</tr>
			<tr>
				
			</tr>
		</table>
	</center>
	
<script type="text/javascript">
	$(window).load(function() { window.print(); });
</script>
		
</body>
</html>
