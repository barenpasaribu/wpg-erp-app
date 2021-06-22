// JavaScript Document

//reset data
function batalJampeng()
{
	document.getElementById('kd_pabrik').value='';
	document.getElementById('kd_pabrik').disabled=false;
	document.getElementById('kpsitas_olah').value='';
	document.getElementById('start_hour').value='';
	document.getElementById('end_hour').value='';
	document.getElementById('hari_olah').value='';
	document.getElementById('kapasitaslori').value='';
}

//simpan data
function simpanJampeng()
{
	koderorg=trim(document.getElementById('kd_pabrik').value);
	kapasitasolah=trim(document.getElementById('kpsitas_olah').value);
  	jam_mulai=trim(document.getElementById('start_hour').value);
	jam_selesai=trim(document.getElementById('end_hour').value);
	berlakusampai=trim(document.getElementById('hari_olah').value);
	kapasitaslori=trim(document.getElementById('kapasitaslori').value);
	method=document.getElementById('method').value;
		param='koderorg='+koderorg+'&kapasitasolah='+kapasitasolah+'&jam_mulai='+jam_mulai+'&jam_selesai='+jam_selesai;
		param+='&berlakusampai='+berlakusampai+'&kapasitaslori='+kapasitaslori+'&method='+method;
		tujuan='pabrik_slave_save_pbrik_jam.php';
	//alert(param);
	if (kapasitasolah == '' || berlakusampai == '') 
	{
		alert('Data inconsistent');
	}
	else {
		if(confirm('Apakah anda yakin ?'))
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
							//document.getElementById('container').innerHTML=con.responseText;
							load_data();
							batalJampeng();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 } 	
}

function delJampeng(koderorg)
{
        param='koderorg='+koderorg;
		param+='&method=delete';
		tujuan='pabrik_slave_save_pbrik_jam.php';
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
						else {
							//alert(con.responseText);
							document.getElementById('container').innerHTML=con.responseText;
							document.getElementById('tr_'+num).style.display = 'none';
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 } 	
}
//get data from database terus ditampilkan ke dalam form
function fillField(koderorg,kapasitasolah,jammulai,jamselesai,berlakusampai,kapasitaslori)
{
	
	kd_pabrik		=document.getElementById('kd_pabrik');
	kd_pabrik.value	=koderorg;
	kd_pabrik.disabled=true;
	kpsitas_olah		=document.getElementById('kpsitas_olah');
	kpsitas_olah.value	=kapasitasolah;
	start_hour		    =document.getElementById('start_hour');
	start_hour.value	=jammulai;
	end_hour			=document.getElementById('end_hour');
	end_hour.value=jamselesai;
	hari_olah			=document.getElementById('hari_olah');
	hari_olah.value		=berlakusampai;
	kapasitaslori1		=document.getElementById('kapasitaslori');
	kapasitaslori1.value	=kapasitaslori;
	
	//alert("val="+koderorg+','+kapasitasolah+','+jam_mulai+','+minute_mulai+','+jam_selesai+','+minute_selesai+','+berlakusampai);
	document.getElementById('method').value='update';
}
function getValue(idSrc,idTarget)
{
		var tSrc = document.getElementById(idSrc);
		var tTarget = document.getElementById(idTarget);
		
		tTarget.value = tSrc.innerHTML;
}
function load_data()
{
	param='method=load_data';
	tujuan='pabrik_slave_save_pbrik_jam.php';
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
							//document.getElementById('tr_'+num).style.display = 'none';
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 } 	
	 post_response_text(tujuan, param, respog);
}