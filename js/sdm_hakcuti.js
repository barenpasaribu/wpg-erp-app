/**
 * @author Developer E-komoditi Solutions Indonesia
 */
 

function generateCuti()
{
	idkry = document.getElementById('idkry').value;
	tahunhc = document.getElementById('tahuns').value;
	periodehc1 = document.getElementById('hakcutidari').value;
	periodehc2 = document.getElementById('hakcutisampai').value;
	jumlahhc= document.getElementById('jumlahhakcuti').value;
	
	//kode=document.getElementById('kodeijin').value;
	//jenisijin=document.getElementById('jenisijin').value;
	//met=document.getElementById('method').value;
	if(trim(periodehc1)=='' || trim(periodehc2) == '' || trim(jumlahhc) == '')
	{
		alert('Periode Hak Cuti dan Jumlah Hak Cuti harus diisi');
		//document.getElementById('kodeijin').focus();
	}
	else
	{
		//kode=trim(kode);
		//jenisijin=trim(jenisijin);
		if (confirm('Anda yakin ingin memberi hak cuti untuk karyawan dengan ID '+ idkry +' untuk periode '+ tahunhc +' ...?')) {
			idkry = trim(idkry);
			tahunhc = trim(tahunhc);
			periodehc1 = trim(periodehc1);
			periodehc2 = trim(periodehc2);
			jumlahhc = trim(jumlahhc);
			
			param='idkry='+idkry+'&tahunhc='+tahunhc+'&periodehc1='+periodehc1+'&periodehc2='+periodehc2+'&jumlahhc='+jumlahhc+'&method=generate';
			tujuan='sdm_slave_save_hakcuti.php';
			post_response_text(tujuan, param, respog);	
		}
			
	}
	
	function respog()
	{
		      if(con.readyState==4)
		      {
			        if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
							alert('ERROR TRANSACTION,\n' + con.responseText);
						}
						else {
							//alert(con.responseText);
							alert('Karyawan dengan ID '+ idkry +' untuk periode '+ tahunhc +' sudah diberikan hak cuti sebanyak '+ jumlahhc+ ' hari');
							//document.getElementById('container').innerHTML=con.responseText;
							document.getElementById('hakcutidari').value = '';
							document.getElementById('hakcutisampai').value = '';
							document.getElementById('jumlahhakcuti').value = '';
							location.reload();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
		
}

function batalGenerate(){
	document.getElementById('hakcutidari').value = '';
	document.getElementById('hakcutisampai').value = '';
	document.getElementById('jumlahhakcuti').value = '';
}


