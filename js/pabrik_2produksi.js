function getLaporan()
{
    kodeorg = document.getElementById('kodeorg').value;
    tanggal_awal = document.getElementById('tanggal_awal').value;
	tanggal_akhir = document.getElementById('tanggal_akhir').value;
	
	if (kodeorg == "" || tanggal_awal == "" || tanggal_akhir == "") {
		Swal.fire('Oops...', "Semua fill harus dipilih.", 'error');
	}else{
		param='kodeorg='+kodeorg+'&tanggal_awal='+tanggal_awal+'&tanggal_akhir='+tanggal_akhir;
		tujuan='pabrik_slave_2produksi_harian.php';
		post_response_text(tujuan, param, respog);
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
				else {;
					document.getElementById('table_laporan_produksi_harian').innerHTML=con.responseText;
					$('#data_laporan_pabrik_produksi').DataTable();
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}	
	 } 		
	
}

function cetakExcel(kodeorg,periode,ev = 'event')
{
    param='kodeorg='+kodeorg+'&periode='+periode;
    tujuan = 'pabrik_slave_produksi_harian_excel.php?method=excel&'+param;	
    //display window
    title=periode;
    width='700';
    height='400';
    content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
    showDialog1(title,content,width,height,ev);
}

