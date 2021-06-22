function simpan()
{
  //  alert('masuk');
	 
	regional=document.getElementById('regional').value;
        kodeorg=document.getElementById('kodeorg').value;
	kodeblok=document.getElementById('kodeblok').value;
	jarak=document.getElementById('jarak').value;
	
        method=document.getElementById('method').value;
	//alert(kodeorg);

	
	if(regional=='' || kodeorg=='' || kodeblok=='' || jarak=='' )
	{
		alert('Field masih kosong');
		return;
	}
   
    param='method=save'+'&regional='+regional+'&kodeorg='+kodeorg+'&kodeblok='+kodeblok+'&jarak='+jarak;
	//alert(param);

	tujuan='vhc_slave_save_jarakblok.php';
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
        if(confirm('Anda yakin ingin dihapus?'))
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



function getBlok()
{
	kodeorg=document.getElementById('kodeorg').value;
        param='method=getBlok'+'&kodeorg='+kodeorg;
	//alert(param);
	tujuan='vhc_slave_save_jarakblok.php';
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
                                            //alert(con.responseText);
                                            document.getElementById('kodeblok').innerHTML=con.responseText;    
						
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}
