function getMaterialNumber(mayor){	
	param='mayor='+mayor;
	tujuan='log_slave_get_material_number.php';
    post_response_text(tujuan, param, respog);
    document.getElementById('caption').innerHTML=document.getElementById('kelompokbarang').options[document.getElementById('kelompokbarang').selectedIndex].text;
	ser=document.getElementById('kelompokbarang');
    for(g=0;g<ser.length;g++){
		if(ser.options[g].value==mayor){
		   ser.options[g].selected=true;	
		}
    }
	
	
    function respog(){
        if(con.readyState==4){
			if (con.status == 200) {
							busy_off();
							if (!isSaveResponse(con.responseText)) {
								alert('ERROR\n' + con.responseText);
							}
							else {
								document.getElementById('kodebarang').value=con.responseText;
							}
					}
					else {
							busy_off();
							error_catch(con.status);
					}
        }	
	}	
}

function getMaterialMember(mayor){
	param='mayor='+mayor;
	tujuan='log_slave_get_material_member.php';
	post_response_text(tujuan, param, respog);
	document.getElementById('caption').innerHTML=document.getElementById('kelompokbarang').options[document.getElementById('kelompokbarang').selectedIndex].text;
        function respog(){
		  if(con.readyState==4){
					if (con.status == 200) {
									busy_off();
									if (!isSaveResponse(con.responseText)) {
											alert('ERROR\n' + con.responseText);
									}
									else {
											document.getElementById('contain').innerHTML=con.responseText;
									}
							}
							else {
									busy_off();
									error_catch(con.status);
							}
		  }	
         }
        document.getElementById('method').value='insert';		
}

function fillField(kelompokbarang,kodebarang,namabarang,satuan,minstok,nokartubin,konversi,inactive,kodeorder,merk,partnumber,inv_code,keterangan){		
	document.getElementById("form").style.display = "";
	document.getElementById("upload").style.display = "none";
	document.getElementById("listdata").style.display = "none";
	kel=document.getElementById('kelompokbarang');
        for(g=0;g<kel.length;g++){
                if(kel.options[g].value==kelompokbarang){
                   kel.options[g].selected=true;	
                }
        }

    document.getElementById('kodebarang').value=kodebarang;
	document.getElementById('namabarang').value=namabarang;
	
    sat=document.getElementById('satuan');
        for(g=0;g<sat.length;g++){		
                if(sat.options[g].value==satuan){
                   sat.options[g].selected=true;	
                }
        }	
	document.getElementById('kodeorder').value=kodeorder;
	document.getElementById('merk').value=merk;
	document.getElementById('partnumber').value=partnumber;
	document.getElementById('invkode').value=inv_code;
	document.getElementById('minstok').value=minstok;
	document.getElementById('nokartu').value=nokartubin;
	document.getElementById('keterangan').value=keterangan;
	document.getElementById('konversi').value=konversi;
	document.getElementById('method').value='update';
	kel.disabled = true;
}

function delBarang(kodebarang,mayor){
  tujuan='log_slave_get_material_member.php';
   param='kodebarang='+kodebarang+'&mayor='+mayor+'&method=delete';
   if(confirm(document.getElementById('alertqdelete').value)){
         post_response_text(tujuan, param, respog);
   }
        function respog(){
		  if(con.readyState==4){
					if (con.status == 200) {
									busy_off();
									if (!isSaveResponse(con.responseText)) {
											alert('ERROR\n' + con.responseText);
									}
									else {
									   document.getElementById('contain').innerHTML=con.responseText;
									   cancelBarang();
									}
							}
							else {
									busy_off();
									error_catch(con.status);
							}
		  }	
         }  	
}

function cancelBarang(){
	location.reload(true);
}

function simpanBarangBaru(){
		inv_code = document.getElementById('invkode').value;
        tujuan='log_slave_get_material_member.php';
		method	=document.getElementById('method').value;	
        konversi=document.getElementById('konversi').value			
        kl		=document.getElementById('kelompokbarang');
        kl		=trim(kl.options[kl.selectedIndex].value);        
        kdbarang=trim(document.getElementById('kodebarang').value);	
        nmbrg	=trim(document.getElementById('namabarang').value);
        satuan	=document.getElementById('satuan');
        satuan	=satuan.options[satuan.selectedIndex].value;
		kodeorder	=document.getElementById('kodeorder').value;
		merk	=document.getElementById('merk').value;
		partnumber	=document.getElementById('partnumber').value;
        minstok	=document.getElementById('minstok').value;		
        nokartu =document.getElementById('nokartu').value;		
		keterangan	=document.getElementById('keterangan').value;
				
        param='mayor='+kl+'&kodebarang='+kdbarang+'&namabarang='+nmbrg+'&satuan='+satuan+'&minstok='+minstok+'&konversi='+konversi+'&nokartu='+nokartu+'&method='+method;
		param+='&merk='+merk+'&partnumber='+partnumber+'&inv_code='+inv_code+'&keterangan='+keterangan;	
		param+='&kodeorder='+kodeorder;
		
			 if(kl=='') {
				alert(document.getElementById('a2').innerHTML + ' ' + document.getElementById('alertrequired').value);			 
			 }		
			 else if(nmbrg==''){
				alert(document.getElementById('a4').innerHTML + ' ' + document.getElementById('alertrequired').value);			  
			 }				
			 else if(satuan=='') {
				alert(document.getElementById('a5').innerHTML + ' ' + document.getElementById('alertrequired').value);			 
			 }					 
     		 else {
				 if(confirm(document.getElementById('alertqinsert').value)){
					 post_response_text(tujuan, param, respog); 
				 }
			 } 
		      
	function respog(){
	  if(con.readyState==4){
			if (con.status == 200) {
							busy_off();
							if (!isSaveResponse(con.responseText)) {
									alert('ERROR\n' + con.responseText);
							}
							else {
								document.getElementById('contain').innerHTML=con.responseText;
								alert(document.getElementById('alertinsert').value);
								location.reload(true);
							}
					}
					else {
							busy_off();
							error_catch(con.status);
					}
	  }	
   }  	
}

function increaseKodeBarang(kdbarang){
  x=parseInt(kdbarang);
  x=x+1;
  if(x<10)
     x='0000'+x;
  else if(x<100)
     x='000'+x;
  else if(x<1000)
     x='00'+x;
  else if(x<10000)
     x='0'+x;	 	 	 
  document.getElementById('kodebarang').value=x;	
}

function cariBarang(){
        tujuan='log_slave_get_material_member.php';
        txtcari=document.getElementById('txtcari').value;
        filter=document.getElementById('filter').value;
		if(filter=='all') {
			document.getElementById('txtcari').value='';
		}
        param='txtcari='+txtcari+'&method='+'refresh_data';	
		param+='&filter='+filter;
        post_response_text(tujuan, param, respog);
        function respog(){
		  if(con.readyState==4){
					if (con.status == 200) {
									busy_off();
									if (!isSaveResponse(con.responseText)) {
											alert('ERROR\n' + con.responseText);
									}
									else {
											document.getElementById('contain').innerHTML=con.responseText;
									}
							}
							else {
									busy_off();
									error_catch(con.status);
							}
		  }	
        }  	
}

function masterbarangPDF(ev){
  klbarang=document.getElementById('kelompokbarang');
  klbarang=trim(klbarang.options[klbarang.selectedIndex].value);
  if(klbarang=='')
  alert(document.getElementById('reqmaterialgrup').value + ' ' + document.getElementById('alertrequired').value);
  else{
        namatable='log_5masterbarang';
        kondisi  ='kelompokbarang=\''+klbarang+'\'';
        kolom	 ='kelompokbarang,kodebarang,namabarang,satuan';
        param='table='+namatable+'&kondisi='+kondisi+'&kolom='+kolom+'&klbarang='+klbarang;
        content="<iframe src=\"log_slave_5masterbarang_pdf.php?"+param+"\" style='width:498px;height:398px;'></iframe>";
    showDialog1("MASTER BARANG",content,'500','400',ev);
  }
}

nameV='winbarang';
x=0;
function editDetailbarang(kodebarang,ev){
	x+=1;
	nx=nameV+x;
	tujuan='log_slave_edit_material_detail.php?kodebarang='+kodebarang;
    content="<iframe name="+nx+" src="+tujuan+" frameborder=0 width=600px height=700px></iframe>";   
    showDialog1("Upload Foto Item:"+kodebarang,content,'600','700',ev);
}

function simpanPhoto(){
        nx=nameV+x;
        spec=eval(nx+".document.getElementById('spec').value");
        kodebarang=eval(nx+".document.getElementById('kodebarangx').value");
        if(spec==''){
                if(confirm(document.getElementById('alertqinsert').value)){
                   simpan(kodebarang,nx);				   
                }
        }
        else{
                simpan(kodebarang,nx);
        }
        function simpan(kodebarang,nx){
                eval(nx+".document.getElementById('"+kodebarang+"').action='log_slave_savePhotoBarang.php'");	
                eval(nx+".document.getElementById('"+kodebarang+"').submit()");
				closeDialog(); 
				location.reload(true);				
        }
}

function viewDetailbarang(kodebarang,ev){
    tujuan='log_slave_material_picture_detail.php?kodebarang='+kodebarang;
    content="<iframe name=disPhotobarang src="+tujuan+" frameborder=0 width=800 height=600></iframe>";   
    showDialog1("Detail : "+kodebarang,content,'800','600',ev);
}

function setInactive(kodebarang){
	xstatus=document.getElementById('br'+kodebarang).checked==true?1:0;
	param='kodebarang='+kodebarang+'&status='+xstatus;
    tujuan='log_slave_update_masterBarang.php';
    post_response_text(tujuan, param, respog);
        function respog(){
		  if(con.readyState==4){
					if (con.status == 200) {
									busy_off();
									if (!isSaveResponse(con.responseText)) {
											alert('ERROR\n' + con.responseText);
											if(document.getElementById('br'+kodebarang).checked==true)
												document.getElementById('br'+kodebarang).checked=false;
											else
												document.getElementById('br'+kodebarang).checked=true;
									}
									else {

									}
							}
							else {
									busy_off();
									error_catch(con.status);
											if(document.getElementById('br'+kodebarang).checked==true)
												document.getElementById('br'+kodebarang).checked=false;
											else
												document.getElementById('br'+kodebarang).checked=true;						
							}
		  }	
    }  	
}

function loadData(){
        param='method=refresh_data';
        tujuan = 'log_slave_get_material_member.php';
        post_response_text(tujuan, param, respog);
        function respog(){
			  if(con.readyState==4){
					if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
							alert('ERROR\n' + con.responseText);
						}
						else {
							document.getElementById('contain').innerHTML=con.responseText;
						}
					}
					else {
							busy_off();
							error_catch(con.status);
					}
			  }	
         }		
}

function cariBast(num){
	txtcari=document.getElementById('txtcari').value;
	filter=document.getElementById('filter').value;
	if(filter=='all') {
		document.getElementById('txtcari').value='';
	}	
	param='txtcari='+txtcari+'&method='+'refresh_data';	
	param+='&filter='+filter;	
	param+='&page='+num;
	tujuan = 'log_slave_get_material_member.php';
	post_response_text(tujuan, param, respog);			
	function respog(){
			if (con.readyState == 4) {
					if (con.status == 200) {
							busy_off();
							if (!isSaveResponse(con.responseText)) {
									alert('ERROR\n' + con.responseText);
							}
							else {
									document.getElementById('contain').innerHTML=con.responseText;
							}
					}
					else {
							busy_off();
							error_catch(con.status);
					}
			}
	}	
}

function uploadfoto(kdbarang){
	document.getElementById("upload").style.display = "";
	document.getElementById("form").style.display = "none";
	document.getElementById("kodebarangx").value = kdbarang;
	document.getElementById("statimg").value = "saveimage";	
	document.getElementById("listdata").style.display = "none";
}

function editfoto(kodebarang,depan,samping,atas,spesifikasi,link1,link2,link3,link4){
	document.getElementById("spec").value = spesifikasi;
	document.getElementById("link1").value = link1;
	document.getElementById("link2").value = link2;
	document.getElementById("link3").value = link3;	
	document.getElementById("link4").value = link4;	
	document.getElementById("file1").value = depan;	
	document.getElementById("file2").value = samping;	
	document.getElementById("file3").value = atas;
	document.getElementById("uploadgbr").style.display = "";
	document.getElementById("formitem").style.display = "none";	
	document.getElementById("kodebarangx").value = kodebarang;
	document.getElementById("statimg").value = "update";	
}


function savefoto(){
	kdbrg = document.getElementById("kodebarangx").value;
	spec  = document.getElementById("spec").value;
	link1 = document.getElementById("link1").value;
	link2 = document.getElementById("link2").value;
	link3 = document.getElementById("link3").value;
	link4 = document.getElementById("link4").value;
	file1 = document.getElementById("file1").value;
	file2 = document.getElementById("file2").value;
	file3 = document.getElementById("file3").value;
	
	param='kdbrg='+kdbrg+'&spec='+spec+'&link1='+link1+'&link2='+link2+'&link3='+link3+'&link4='+link4+'&file1='+file1+'&file2='+file2+'&file3='+file3;
	tujuan='log_slave_savePhotoBarang.php';	
	
	function respog(){
		  if(con.readyState==4){
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR\n' + con.responseText);
					}
					else {
						location.reload(true);
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	 }

	if(confirm(document.getElementById('alertqinsert').value)){
		post_response_text(tujuan, param, respog);		
	}	 
}

function filteritemperusahaan(kodelompok){	
	param='kodelompok='+kodelompok;
	tujuan='log_slave_get_material_number.php';
    post_response_text(tujuan, param, respog);

    function respog(){
        if(con.readyState==4){
			if (con.status == 200) {
							busy_off();
							if (!isSaveResponse(con.responseText)) {
								alert('ERROR\n' + con.responseText);
							}
							else {
								document.getElementById('content').value=con.responseText;
							}
					}
					else {
							busy_off();
							error_catch(con.status);
					}
        }	
	}	
}

function baru(){
	document.getElementById("form").style.display = "";
	document.getElementById("listdata").style.display = "none";
	document.getElementById("upload").style.display = "none";
	document.getElementById('kelompokbarang').value='';
	document.getElementById('kodebarang').value='';
	document.getElementById('namabarang').value='';
	document.getElementById('satuan').value='';
	document.getElementById('kodeorder').value='';
	document.getElementById('merk').value='';
	document.getElementById('partnumber').value='';
	document.getElementById('minstok').value='';
	document.getElementById('nokartu').value='';
	document.getElementById('keterangan').value='';
	document.getElementById('kelompokbarang').disabled = false;
	document.getElementsByClassName('inv_code').value='';
	document.getElementById('invkode').value='';
}

function listdata() {
	window.location.assign("log_5masterbarang.php");
}

function saveimage(){
	link1 = document.getElementById('link1').value;
	link2 = document.getElementById('link2').value;
	link3 = document.getElementById('link3').value;
	link4 = document.getElementById('link4').value;
	spec  = document.getElementById('spec').value;
	file1 = document.getElementById('file1').value;
	file2 = document.getElementById('file2').value;
	file3 = document.getElementById('file3').value;
	kdbrg = document.getElementById('kodebarangx').value;
	method = document.getElementById('statimg').value;
	
	if(file1=="") {
			alert(document.getElementById('lbl_depan').innerHTML + ' ' + document.getElementById('alertrequired').value);
	} 
	else {
			param='link1='+link1+'&link2='+link2+'&link3='+link3+'&link4='+link4+'&spec='+spec+'&file1='+file1+'&file2='+file2+'&file3='+file3+'&kdbrg='+kdbrg+'&method='+method;
			tujuan='log_slave_get_material_member.php';
			if(confirm(document.getElementById('alertqinsert').value)){
				post_response_text(tujuan, param, respog);
			}

			function respog(){
				if(con.readyState==4){
				if(con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)){
						alert('ERROR\n' + con.responseText);
					}
					else {
						alert(con.responseText);
						window.location.reload();
					}
				}
				else {
						busy_off();
						error_catch(con.status);
				}
				}
			}		
	}	
}

function editimg(kodebarang,depan,samping,atas,spesifikasi,link1,link2,link3,link4){
	document.getElementById('link1').value = link1;
	document.getElementById('link2').value = link2;
	document.getElementById('link3').value = link3;
	document.getElementById('link4').value = link4;
	document.getElementById('spec').value = spesifikasi;
	document.getElementById('file1').value = depan;
	document.getElementById('file2').value = samping;
	document.getElementById('file3').value = atas;
	document.getElementById('kodebarangx').value = kodebarang;	
	document.getElementById("upload").style.display = "";
	document.getElementById("form").style.display = "none";
	document.getElementById("statimg").value = "updateimage";	
	document.getElementById("listdata").style.display = "none";	
}