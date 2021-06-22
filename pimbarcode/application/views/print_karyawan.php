
<html>
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Print Barcode : <?php echo $nik; ?></title>
	<script src="<?=base_url()?>assets/js/jquery-1.7.2.min.js"></script>
	<script src="<?=base_url()?>assets/belakang/dist/js/html2canvas.js"></script>
</head>

<body>
	
	<center>
		<?php
      //      $this->load->library('Barcode39');
            // set Barcode39 object
      //      $bc = new Barcode39("$pcode");
            // set text size
       //     $bc->barcode_text_size = 1;

        //    $prod_name = '';
        //    $prod_price = '';
         //   $prod_code = '';

         //   $prodDtaResult = $this->db->query("SELECT * FROM employee WHERE EMPCODE = '$pcode' ");
          //  $prodDtaRows = $prodDtaResult->num_rows();
          //  if ($prodDtaRows == 1) {
          //      $prodDtaData = $prodDtaResult->result();

          //      $prod_name = $prodDtaData[0]->EMPCODE;
           //     $prod_nama = $prodDtaData[0]->NAME;

            //    unset($prodDtaData);
           // }
           // unset($prodDtaResult);
           // unset($prodDtaRows);

            // display new barcode
           // $bc->draw('./assets/barcode/'.'TBISWAI0028'.'.gif');
		
// set the barcode content and type
		$this->load->library('TCPDF2DBarcode');
		$niks =$nik;	$barcodeobj = new TCPDF2DBarcode("$nik");
		 $prodDtaResult = $this->db->query("SELECT * FROM employee WHERE EMPCODE = '$nik' ");
            $prodDtaRows = $prodDtaResult->num_rows();
            if ($prodDtaRows == 1) {
                $prodDtaData = $prodDtaResult->result();

                $prod_name = $prodDtaData[0]->EMPCODE;
                $prod_nama = $prodDtaData[0]->NAME;
                $prod_unit = $prodDtaData[0]->UNITID;
                $prod_photo = $prodDtaData[0]->PHOTO;


                unset($prodDtaData);
            }
            unset($prodDtaResult);
            unset($prodDtaRows);


// output the barcode as HTML object
			
        ?>
        <script type='text/javascript'>
            function screenshot(){
                html2canvas(document.body).then(function(canvas) {
           
                    document.body.appendChild(canvas);
                });
            }
        </script>
        <table border="0" style="border-collapse: collapse; margin-bottom: 0px;" width="140px" height="auto">

			<tr rowspan='2'>
				<td style="font-family: Arial, Helvetica, sans-serif; text-align: center; font-size: 18px;">
					<?php echo $nik.' - '.$prod_unit; ?>
				</td>
			</tr>
			<tr>
				<td style="font-family: Arial, Helvetica, sans-serif; text-align: center; font-size: 18px;">
					<?php echo $prod_nama;?>
				</td>
			</tr>
        <tr>
        <td style="font-family: Arial, Helvetica, sans-serif; text-align: center; font-size: 18px;">
          <img src="http://wilianperkasa.id/anthesis-erp/<?php echo $prod_photo; ?>" height="300px" width="250px">
        </td>
      </tr>
		
			<tr>
				<td style="font-family: Arial, Helvetica, sans-serif; text-align: center; font-size: 12px;">
					<?php echo $barcodeobj->getBarcodeSVGcode(2, 1, 'black');?>
				</td>
			</tr>

			<tr>
			<!-- 	<input type='button' id='but_screenshot' value='Cetak' onclick='screenshot();'><br/> -->
			</tr>
		</table>
		
	
	


        <!-- Script -->
        
		</center>
</body>
</html>
