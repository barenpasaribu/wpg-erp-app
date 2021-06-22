/**
 * @author antoniuslouis
 */

function loadData()
{
        nik=document.getElementById('karyidCari').options[document.getElementById('karyidCari').selectedIndex].value;
        
	param='proses=loadData'+'&pelaksana='+nik;
	tujuan='it_slave_requestManagement.php';
	post_response_text(tujuan, param, respog);
	
		function respog(){
			if (con.readyState == 4) {
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

function cariBast(num)
{
     nik=document.getElementById('karyidCari').options[document.getElementById('karyidCari').selectedIndex].value;
     param='proses=loadData'+'&pelaksana='+nik;
		param+='&page='+num;
		tujuan = 'it_slave_requestManagement.php';
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
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
function dtReset()
{
    document.getElementById('karyidCari').value='';
    loadData();
}

function savePelaksana(notrans,nod)
{
    pelaks=document.getElementById('pelaksana_'+nod).options[document.getElementById('pelaksana_'+nod).selectedIndex].value;
    param='notransaksi='+notrans+'&proses=updatePelaksana'+'&pelaksana='+pelaks;
    tujuan = 'it_slave_requestManagement.php';
    post_response_text(tujuan, param, respog);			
    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                    alert("Done");
                                    loadData();
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }  
}
function detailData(ev,tujuan,prm)
{
    width='450';
    height='280';
    content="<iframe frameborder=0 width=100% height=280 src='"+tujuan+"?proses=getDetail&notransaksi="+prm+"'></iframe>"
    showDialog1('Detail ',content,width,height,ev); 
}

function tolakApp(ev)
{
    width='250';
    height='150';
    alert("test");
    content="<div id='isiForm'> </div>";
    showDialog1('Tolak Form',content,width,height,ev); 
}
function tolakForm(ev,filnya,notrans)
{
    tolakApp(ev);
    param='notransaksi='+notrans+'&proses=getForm';
    tujuan = filnya;
    post_response_text(tujuan, param, respog);			
    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                    document.getElementById('isiForm').innerHTML=con.responseText;
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }   
}
function tolakDt(notrans)
{
    ket=document.getElementById('ketTolak').value;
    param='notransaksi='+notrans+'&proses=updateTolak'+'&ketTolak='+ket;
    tujuan = 'it_slave_requestManagement.php';
    post_response_text(tujuan, param, respog);			
    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                    alert("Done");
                                    closeDialog();
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }   
}
function saveJm(notrans,brs)
{
    jmhJam=document.getElementById('standr_'+brs).value;
    param='notransaksi='+notrans+'&proses=updateJam'+'&standardJam='+jmhJam;
    tujuan = 'it_slave_requestManagement.php';
    post_response_text(tujuan, param, respog);			
    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                    alert("Done");
                                    closeDialog();
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }   
}

