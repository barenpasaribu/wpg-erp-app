function simpan()
{
	id=document.getElementById('id').value;        
	kodeorg=document.getElementById('kodeorg').value;
	produk=document.getElementById('produk').value;
	namaitem=document.getElementById('namaitem').value;
	standard=document.getElementById('standard').value;
	satuan=document.getElementById('satuan').value;
	
        method=document.getElementById('method').value;
	//alert(kodeorg);

	
	if(kodeorg=='' || produk=='' || namaitem=='' || standard=='' || satuan=='' )
	{
		alert('Field masih kosong');
		return;
	}

	param='kodeorg='+kodeorg+'&produk='+produk+'&namaitem='+namaitem+'&standard='+standard+'&satuan='+satuan+'&method='+method+'&id='+id;
	
	//alert(param);

	tujuan='pabrik_slave_5kelengkapanloses.php';
    post_response_text(tujuan, param, respog);		
	//}
	
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
                                                        document.location.reload();
                        //cancel();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
}


function del(id)
{
	param='method=delete'+'&id='+id;
	//alert(param);
	tujuan='pabrik_slave_5kelengkapanloses.php';
	post_response_text(tujuan, param, respog);
        if(confirm('Yakin akan hapus data ?'))
            post_response_text(tujuan, param, respog);
	function respog()
	{
		  if(con.readyState==4)
		  {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else 
					{
                                                alert('Data berhasil dihapus.');
						document.location.reload();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}

function edit(id,kodeorg,produk,namaitem,standard,satuan)
{
	document.getElementById('id').value=id;
	document.getElementById('kodeorg').value=kodeorg;
	document.getElementById('produk').value=produk;
	document.getElementById('namaitem').value=namaitem;
	document.getElementById('standard').value=standard;
	document.getElementById('satuan').value=satuan;        
        document.getElementById('method').value='update';
}