/**
 * @author antoniuslouis
 */

function searchBarang(title,content,ev)
{
	width='500';
	height='400';
	showDialog1(title,content,width,height,ev);
	//alert('asdasd');
}

function findBarang()
{
	txt=trim(document.getElementById('namabrg').value);
	if(txt=='')
	{
		alert('Text is obligatory');
	}
	else
	{
		param='txtfind='+txt;
		tujuan='log_slave_get_barang.php';
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
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  	
}

function setKodeBarang(kelompok,kode,nama,satuan)
{
	 document.getElementById('namadisabled').value=nama;
	 document.getElementById('kodebarang').innerHTML=kode;
	 closeDialog();
	 getConversionList(kelompok,kode,nama);
	 document.getElementById('captionbarang').innerHTML="["+kode+"]-"+nama;
	 document.getElementById('captionsatuan').innerHTML=satuan;
	 document.getElementById('captionbarang1').innerHTML="["+kode+"]-"+nama;
	 document.getElementById('captionsatuan1').innerHTML=satuan;
	 document.getElementById('satuansource').value=satuan;
}

function  getConversionList(kelompok,kode,nama,satuan)
{
		param='kelompok='+kelompok+'&kode='+kode+'&satuan='+satuan;
		tujuan='log_slave_get_conversion_list.php';
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
							//alert(con.responseText);
							document.getElementById('containersatuan').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 } 	
}


function simpanKonversi()
{
	kodebarang=trim(document.getElementById('kodebarang').innerHTML);
	dari=trim(document.getElementById('satuansource').value);
	ke=trim(document.getElementById('satuandest').value);
    jumlah=trim(document.getElementById('jumlah').value);
	method=document.getElementById('method').value;
	keterangan=document.getElementById('keterangan').value;
		param='jumlah='+jumlah+'&kodebarang='+kodebarang+'&dari='+dari+'&ke='+ke;
		param+='&method='+method+'&keterangan='+keterangan;
		tujuan='log_slave_save_conversion.php';
	if (dari == '' || ke == '' || parseFloat(jumlah) < 0.0001) {
		alert('Data inconsistent');
	}
	else {
		if(confirm('Are you sure?'))
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
							document.getElementById('containersatuan').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 } 	
}

function batalKonversi()
{
	document.getElementById('kodebarang').innerHTML='';
	document.getElementById('satuansource').value='';
	document.getElementById('satuandest').value='';
    document.getElementById('jumlah').value='';
	document.getElementById('method').value='insert';	
	 document.getElementById('namadisabled').value='';
	 document.getElementById('captionbarang').innerHTML="";
	 document.getElementById('captionsatuan').innerHTML="";
	 document.getElementById('satuansource').value="";	
	 document.getElementById('keterangan').value="";
}

function delConversi(kodebarang,dari,ke)
{
        param='kodebarang='+kodebarang+'&dari='+dari+'&ke='+ke;
		param+='&method=delete';
		tujuan='log_slave_save_conversion.php';
		if(confirm('Deleting, Are you sure?'))
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
							//alert(con.responseText);
							document.getElementById('containersatuan').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 } 	
}

function ambilBarang(mayor)
{
    param='mayor='+mayor;
    tujuan='log_slave_getConversionList.php';
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
                                //alert(con.responseText);
                                document.getElementById('containerdetail').innerHTML=con.responseText;
                            }
                        }
                        else {
                            busy_off();
                            error_catch(con.status);
                        }
              }	
	 }    
}

