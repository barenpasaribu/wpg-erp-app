/**
 * @author antoniuslouis
 */
function simpanRencanalahan()
{
	namalokasi	=trim(document.getElementById('namalokasi').value);
	desa		=trim(document.getElementById('desa').value);
	kecamatan	=trim(document.getElementById('kecamatan').value);
	kabupaten	=trim(document.getElementById('kabupaten').value);
	provinsi	=trim(document.getElementById('provinsi').value);
	negara		=trim(document.getElementById('negara').value);
	kontak		=trim(document.getElementById('kontak').value);
	tanggal		=trim(document.getElementById('tanggal').value);
	peruntukan	=trim(document.getElementById('peruntukan').value);	
	method		=document.getElementById('method').value;	
    param='namalokasi='+namalokasi+'&desa='+desa+'&kecamatan='+kecamatan;
	param+='&kabupaten='+kabupaten+'&provinsi='+provinsi+'&negara='+negara+'&kontak='+kontak;
    param+='&method='+method+'&peruntukan='+peruntukan+'&tanggal='+tanggal;
	if(confirm('Save/Simpan..?'))
	{
		if (namalokasi == '') {
		}
		else {
			tujuan = 'rencana_slaveSimpanRencana.php';
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
							document.getElementById('container').innerHTML=con.responseText;
						batalRencanalahan();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }	
}

function fillField(peruntukan,desa,kecamatan,kabupaten,provinsi,negara,tanggal,nama,kontak)
{
	document.getElementById('namalokasi').value=nama;
	document.getElementById('namalokasi').disabled=true;
	document.getElementById('desa').value=desa;
	document.getElementById('kecamatan').value=kecamatan;
	document.getElementById('kabupaten').value=kabupaten;
	document.getElementById('provinsi').value=provinsi;
	document.getElementById('negara').value=negara;
	document.getElementById('kontak').value=kontak;
	document.getElementById('tanggal').value=tanggal;
	document.getElementById('peruntukan').value=peruntukan;	
	document.getElementById('method').value='update';	
}

function delRencana(nama)
{
	document.getElementById('method').value='delete';
    param='namalokasi='+nama+'&method=delete';
	if(confirm('Delete/Hapus '+nama+'..?'))
	{
		tujuan='rencana_slaveSimpanRencana.php';
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
							batalRencanalahan();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }		
}

function batalRencanalahan()
{
	document.getElementById('namalokasi').value='';
	document.getElementById('namalokasi').disabled=false;
	document.getElementById('desa').value='';
	document.getElementById('kecamatan').value='';
	document.getElementById('kabupaten').value='';
	document.getElementById('provinsi').value='';
	document.getElementById('negara').value='';
	document.getElementById('kontak').value='';
	document.getElementById('tanggal').value='';
	document.getElementById('peruntukan').value='';	
	document.getElementById('method').value='insert';	
}

function saveRencanaKoordinat()
{
	lokasi	=document.getElementById('lokasi').options[document.getElementById('lokasi').selectedIndex].value;
	dpl		=trim(document.getElementById('dpl').value);
	jls		=trim(document.getElementById('jls').value);
	mls		=trim(document.getElementById('mls').value);
	dls		=trim(document.getElementById('dls').value);
	jbt		=trim(document.getElementById('jbt').value);
	mbt		=trim(document.getElementById('mbt').value);
	dbt		=trim(document.getElementById('dbt').value);
	method	=trim(document.getElementById('method').value);
	param='lokasi='+lokasi+'&dpl='+dpl+'&jls='+jls+'&mls='+mls;
	param+='&dls='+dls+'&jbt='+jbt+'&mbt='+mbt+'&dbt='+dbt+'&method='+method;
		tujuan='rencana_slaveSimpanKoordinat.php';
	if(lokasi=='' || dpl=='' || jls=='' || mls=='' || dls=='' || jbt=='' || mbt=='' || dbt=='')
	{
		alert('Data not complete/data tidak lengkap');
	}
	else
	{
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
							batalRencanaKoordinat();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }	
}



function batalRencanaKoordinat()
{
	document.getElementById('lokasi').options[0].selected=true;
	document.getElementById('dpl').value='';
	document.getElementById('jls').value='';
	document.getElementById('mls').value='';
	document.getElementById('dls').value='';
	document.getElementById('jbt').value='';
	document.getElementById('mbt').value='';
	document.getElementById('dbt').value='';
	document.getElementById('method').value='insert';	
}

function loadKoordinat(xname)
{
	param='lokasi='+xname+'&method=none';
		tujuan='rencana_slaveSimpanKoordinat.php';
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

function fillFieldKoordinat(nama,jls,mls,dls,jbt,mbt,dbt,dpl)
{
	z=document.getElementById('lokasi');
	for(x=0;x<z.length;x++)
	{
		if(z.options[x].value=nama)
		{
			z.options[x].selected=true;
		}
	}
	document.getElementById('dpl').value=dpl;
	document.getElementById('jls').value=jls;
	document.getElementById('mls').value=mls;
	document.getElementById('dls').value=dls;
	document.getElementById('jbt').value=jbt;
	document.getElementById('mbt').value=mbt;
	document.getElementById('dbt').value=dbt;
	document.getElementById('method').value='update';
}

function delRencanaKoordinat(nama,jls,mls,dls,jbt,mbt,dbt)
{
	param='lokasi='+nama+'&method=delete&jls='+jls+'&mls='+mls+'&dls='+dls+'&jbt='+jbt+'&mbt='+mbt+'&dbt='+dbt;
	tujuan='rencana_slaveSimpanKoordinat.php';
	if(confirm('Deleting ?'))
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
//======================================================================================================
function saveRencanaStatus()
{
	lokasi		=document.getElementById('lokasi').options[document.getElementById('lokasi').selectedIndex].value;
	pic			=trim(document.getElementById('pic').value);
	tanggal		=trim(document.getElementById('tanggal').value);
	status		=trim(document.getElementById('status').value);
	keterangan	=trim(document.getElementById('keterangan').value);
	method		=trim(document.getElementById('method').value);
	param+='lokasi='+lokasi+'&pic='+pic+'&tanggal='+tanggal
	param+='&status='+status+'&keterangan='+keterangan+'&method='+method;
	tujuan='rencana_slaveSimpanStatus.php';
	if(lokasi=='' || tanggal=='' || keterangan=='')
	{
		alert('Data not complete/data tidak lengkap');
	}
	else
	{
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
							batalRencanaStatus();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }	
}



function batalRencanaStatus()
{
	document.getElementById('lokasi').options[0].selected=true;
	document.getElementById('pic').value='';
	document.getElementById('status').value='';
	document.getElementById('keterangan').value='';
	document.getElementById('method').value='insert';
	document.getElementById('lokasi').disabled=false;
	document.getElementById('tanggal').disabled=false;	
}

function loadRencanaStatus(xname)
{
	param='lokasi='+xname+'&method=none';
		tujuan='rencana_slaveSimpanStatus.php';
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

function fillFieldStatus(lokasi,tanggal,status,pic,fieldketerangan)
{
	z=document.getElementById('lokasi');
	for(x=0;x<z.length;x++)
	{
		if(z.options[x].value=lokasi)
		{
			z.options[x].selected=true;
		}
	}
	document.getElementById('lokasi').disabled=true;
	document.getElementById('tanggal').disabled=true;
	document.getElementById('tanggal').value=tanggal;
	document.getElementById('status').value=status;
	document.getElementById('keterangan').value=document.getElementById(fieldketerangan).innerHTML;
	document.getElementById('pic').value=pic;
	document.getElementById('method').value='update';
}

function delRencanaStatus(nama,tanggal)
{
	param='lokasi='+nama+'&method=delete&tanggal='+tanggal;
	tujuan='rencana_slaveSimpanStatus.php';

	if(confirm('Deleting ?'))
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
//=====================================================================

function loadRencanaUbahStatus(xnama)
{
	param='namalokasi='+xnama;
		tujuan='rencana_slaveSimpanUbahStatus.php';
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
///================================================
function simpanStatusTerakhir()
{
	lokasi=document.getElementById('lokasi');
	lokasi=lokasi.options[lokasi.selectedIndex].value;
	status=document.getElementById('status');
	status=status.options[status.selectedIndex].value;	
	tujuan='rencana_slaveSimpanUbahStatus.php';
	param='namalokasi='+lokasi+'&status='+status+'&method=update';;
	if(lokasi=='')
	{
		alert('Data not complete/data tidak lengkap');
	}
	else
	{
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
