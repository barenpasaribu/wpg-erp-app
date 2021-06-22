
<html>
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Print Barcode : <?php echo $pcode; ?></title>
	<script src="<?=base_url()?>assets/js/jquery-1.7.2.min.js"></script>
	<script src="<?=base_url()?>assets/belakang/dist/js/html2canvas.js"></script>
</head>

<body>
	
	<center>
		<?php
            $this->load->library('TCPDF2DBarcode');
            // set Barcode39 object
            $bc = new TCPDF2DBarcode("$pcode");
            // set text size
            $bc->barcode_text_size = 1;

            $prod_name = '';
            $prod_price = '';
            $prod_code = '';

            $prodDtaResult = $this->db->query("SELECT * FROM field WHERE FIELDNO = '$pcode' ");
            $prodDtaRows = $prodDtaResult->num_rows();
            if ($prodDtaRows == 1) {
                $prodDtaData = $prodDtaResult->result();

                $prod_name = $prodDtaData[0]->FIELDNO;
                $prod_desc = $prodDtaData[0]->FIELDNO;

                unset($prodDtaData);
            }
            unset($prodDtaResult);
            unset($prodDtaRows);

            // display new barcode
           
        ?>
         <script type='text/javascript'>
            function screenshot(){
                html2canvas(document.body).then(function(canvas) {
           
                    document.body.appendChild(canvas);
                });
            }
        </script>
        <table border="0" style="border-collapse: collapse; margin-bottom: 0px;" width="140px" height="auto">
		
			<!-- <tr>
				<input type='button' id='but_screenshot' value='Cetak' onclick='screenshot();'><br/>
			</tr> -->
			<tr>
				<td style="font-family: Arial, Helvetica, sans-serif; text-align: center; font-size: 18px;">
					<?php echo $prod_name;?>
				</td>
			</tr>
				<tr>
				<td style="font-family: Arial, Helvetica, sans-serif; text-align: center; font-size: 18px;">
					<?php echo $prod_desc; ?>
				</td>
			</tr>
				<tr>
				<td style="font-family: Arial, Helvetica, sans-serif; text-align: center; font-size: 12px;">
					<?php echo $bc->getBarcodeSVGcode(4, 2, 'black');?>
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
