// MODEL FUNCTION //
function CariData(){
	var unit 	= document.getElementById('unitDt').options[document.getElementById('unitDt').selectedIndex].value;
	var gudang 	= document.getElementById('gudang2').options[document.getElementById('gudang2').selectedIndex].value;
	var periode 	= document.getElementById('periode2').options[document.getElementById('periode2').selectedIndex].value;
	var method 		= 'CariListDataHeader';
	
	if(unit == ''){
		alert('Unit tidak boleh kosong');
		return;		
	}
	if(gudang == ''){
		alert('Gudang tidak boleh kosong');
		return;		
	}
	if(periode == ''){
		alert('Periode tidak boleh kosong');
		return;		
	}
	
	var param = "method="+method+"&unit="+unit+"&gudang="+gudang+"&periode="+periode;
	var tujuan = 'slave_get_posting_stok_opname.php';
	post_response_text(tujuan, param, respog);
	
	function respog(){
		if(con.readyState==4){
			if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {alert('ERROR TRANSACTION,\n' + con.responseText);}
                else {
					document.getElementById('tbodyheader').innerHTML=con.responseText;
                }
            } else {
				busy_off();
				error_catch(con.status);
            }
		}
	}
}
function CariDetailData(reffno){
	var method 		= 'CariListDataDetail';
	
	var param = "method="+method+"&reffno="+reffno;
	var tujuan = 'slave_get_posting_stok_opname.php';
	post_response_text(tujuan, param, respog);
	
	function respog(){
		if(con.readyState==4){
			if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {alert('ERROR TRANSACTION,\n' + con.responseText);}
                else {
					document.getElementById('tbodydetail').innerHTML=con.responseText;
					ShowDetail();
                }
            } else {
				busy_off();
				error_catch(con.status);
            }
		}
	}
}
function getGudangDt() {
    unt = document.getElementById('unitDt').options[document.getElementById('unitDt').selectedIndex].value;
    param = 'unitDt='+unt+'&proses=getGudang';
    tujuan ='log_slaveStokOpname.php';
    post_response_text(tujuan, param, respog);
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
								alert('ERROR TRANSACTION,\n' + con.responseText);
						}
						else {
								document.getElementById('gudang2').innerHTML=con.responseText;
						}
				}
				else {
						busy_off();
						error_catch(con.status);
				}
			}
		}
}

// CONTROLLER FUNCTION //
function UpdateStatus(id){
	busy_on();
	var Status = document.getElementById('status_'+id).options[document.getElementById('status_'+id).selectedIndex].value;
	var method = 'UpdateStatus';
	var Periode = document.getElementById('periode2').options[document.getElementById('periode2').selectedIndex].value;
	var gudang = document.getElementById('gudang2').options[document.getElementById('gudang2').selectedIndex].value;
	
	param='id='+id+'&Status='+Status+'&method='+method+'&Periode='+Periode+'&gudang='+gudang;
	tujuan='slave_save_posting_stok_opname.php';
	post_response_text(tujuan, param, respog);

	function respog() {
		if(con.readyState==4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {alert(con.responseText);}
				else {
					alert('Suksess');
					location.reload();
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}	
	}	
}

// VIEW FUNCTION //
function ShowDetail(){
	document.getElementById('fieldsetdetails').style.display='block';
}
function SetOptStatus(){
	var Status = document.getElementById('find_type').options[document.getElementById('find_type').selectedIndex].value;

	if(Status == 'Status'){
		document.getElementById('katakunci_text').style.display='none';
		document.getElementById('katakunci_select').style.display='block';
	} else {
		document.getElementById('katakunci_text').style.display='block';
		document.getElementById('katakunci_select').style.display='none';
	}
}
