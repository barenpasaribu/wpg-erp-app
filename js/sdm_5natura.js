/**
 * @author antoniuslouis
 */


function simpanJ()
{
	kodeorg=document.getElementById('kodeorg');
	kodeorg=kodeorg.options[kodeorg.selectedIndex].value;
	tahun=document.getElementById('tahun').value;
	kode=document.getElementById('kode');
	kode=kode.options[kode.selectedIndex].value;
	//jumlah=document.getElementById('jumlah').value;
	jumlah=remove_comma(document.getElementById('jumlah'));        
	jumlahuang=remove_comma(document.getElementById('jumlahuang'));        
                keterangan=document.getElementById('keterangan').value;	
	met=document.getElementById('method').value;
	if(trim(tahun)=='' )
	{
		alert('Tahun masih kosong');
		document.getElementById('tahun').focus();
	}
	else
	{
		param='kodeorg='+kodeorg+'&tahun='+tahun+'&method='+met;
		param+='&kode='+kode+'&jumlah='+jumlah+'&jumlahuang='+jumlahuang;
		param+='&keterangan='+keterangan;
		tujuan='sdm_slave_save_5natura.php';
                
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
						else {
							//alert(con.responseText);
							document.getElementById('container').innerHTML=con.responseText;
                                                        cancelJ();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
		
}

function fillField(kodeorg,tahun,kelompok,keterangan,jumlah,jumlahuang)
{
	
	jk=document.getElementById('kodeorg');
	for(x=0;x<jk.length;x++)
	{
		if(jk.options[x].value==kodeorg)
		{
			jk.options[x].selected=true;
		}
	}
	jk.disabled=true;
	
	jk=document.getElementById('kode');
	for(x=0;x<jk.length;x++)
	{
		if(jk.options[x].value==kelompok)
		{
			jk.options[x].selected=true;
		}
	}
	jk.disabled=true;

	document.getElementById('tahun').value=tahun;
                document.getElementById('tahun').disabled=true;
                
	document.getElementById('jumlah').value=jumlah;
	document.getElementById('jumlahuang').value=jumlahuang;
	document.getElementById('keterangan').value=keterangan;
	document.getElementById('method').value='update';
}

function cancelJ()
{
        document.getElementById('kodeorg').disabled=false;
        document.getElementById('tahun').disabled=false;
        document.getElementById('kode').disabled=false;
	document.getElementById('jumlah').value=1;
	document.getElementById('jumlahuang').value=1;
	document.getElementById('keterangan').value='';
	document.getElementById('method').value='insert';		
}

function changeValueNatura() {
	if(document.getElementById('jumlah').value != 1) {
		document.getElementById('jumlahuang').value = 1;
	}
}

function changeValueRp() {
	if(document.getElementById('jumlahuang').value != 1) {
		document.getElementById('jumlah').value = 1;
	}
}