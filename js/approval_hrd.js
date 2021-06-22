/**
 * @author Developer
 */
function simpanDep(){
	kodeorg    = document.getElementById('kodeorg').options[document.getElementById('kodeorg').selectedIndex].value;
	app 	   = document.getElementById('app').options[document.getElementById('app').selectedIndex].value;
	met		   = document.getElementById('method').value;
    karyawanid = document.getElementById('karyawanid').options[document.getElementById('karyawanid').selectedIndex].value;

	param='kodeorg='+kodeorg+'&app='+app+'&method='+met+'&karyawanid='+karyawanid;
	tujuan='setup_slave_save_approval_hrd.php';
	
	if(app==''){
		alert(document.getElementById('kodeapp').value + ' ' + document.getElementById('alertrequired').value);
	} 
	else if(kodeorg==''){
		alert(document.getElementById('kodeorg1').value + ' ' + document.getElementById('alertrequired').value);
	}
	else if(karyawanid==''){
		alert(document.getElementById('persetujuan').value + ' ' + document.getElementById('alertrequired').value);
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
						location.reload(true);
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

function dellField(kodeorg,app,karyawanid){
    met	   = 'delete';
    param  = 'kodeorg='+kodeorg+'&app='+app+'&method='+met+'&karyawanid='+karyawanid;
    tujuan = 'setup_slave_save_approval_hrd.php';
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
							location.reload(true);	
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

function getKaryawan(kodeorg){
		karyawanid = document.getElementById('karyawanid').value;
        param='karyawanid='+karyawanid+'&kodeorg='+kodeorg;
        tujuan='log_filter_approval.php';
        post_response_text(tujuan, param, respog);
        function respog(){
		  if(con.readyState==4){
					if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
								alert('ERROR\n' + con.responseText);
						}
						else {
							document.getElementById('karyawanid').innerHTML=trim(con.responseText);
							document.getElementById('karyawanid').disabled = false;	
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		  }	
        }  	
}

function loadData(){
        param='method=refresh_data';
        tujuan = 'setup_slave_save_approval_hrd.php';
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
		filter  = document.getElementById('filter').value;
		keyword = document.getElementById('keyword').value;
	
		param='method=refresh_data';
		param+='&page='+num;
		param+='&filter='+filter+'&keyword='+keyword;
		
		tujuan = 'setup_slave_save_approval_hrd.php';
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

function baru(){
	document.getElementById('form').style.display = '';
	document.getElementById('listdata').style.display = 'none';
	document.getElementById('kode').value='';
	document.getElementById('nama').value='';
	document.getElementById('rate').value='';
	document.getElementById('minDuedate').value='';
	document.getElementById('minPQ').value='';
	document.getElementById('method').value='insert';
}
function cancelDep(){
	document.getElementById('app').options[0].selected=true;
	document.getElementById('kodeorg').options[0].selected=true;
	document.getElementById('karyawanid').options[0].selected=true;
	document.getElementById('karyawanid').disabled=true;
}

function listdata(){
	document.getElementById('form').style.display = 'none';
	document.getElementById('listdata').style.display = '';
}

function search(){
	filter  = document.getElementById('filter').value;
	keyword = document.getElementById('keyword').value;
	if(filter==''){
		document.getElementById('keyword').value='';
	}
	else {
		if(keyword=='') {
			alert(document.getElementById('findword').innerHTML + ' ' + document.getElementById('alertrequired').value);
		}
	}

	param='filter='+filter+'&keyword='+keyword+'&method=refresh_data';
	tujuan = 'setup_slave_save_approval_hrd.php';
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