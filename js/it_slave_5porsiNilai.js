/**
 * @author antoniuslouis
 */
function simpanPendidikan(fileTarget,passParam)
{
    var passP = passParam.split('##');
    var param = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
	//alert(param);
   //alert(param);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    //var res = document.getElementById(idCont);
//                    res.innerHTML = con.responseText;
						loadData();
						cancelIsi();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
 
    post_response_text(fileTarget+'.php', param, respon);

}
function loadData()
{
	param='method=loadData';
	tujuan='it_slave_5porsiNilai';
        post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    //var res = document.getElementById(idCont);
//                    res.innerHTML = con.responseText;
					  document.getElementById('container').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}
function fillField(kdkeg,nil)
{
    //$arr="##kode##nilKode##ket##method";
	document.getElementById('kode').value=kdkeg;
        document.getElementById('kode').disabled=true;
	document.getElementById('jmlhPorsi').value=nil;
	document.getElementById('method').value='insert';
}

function cancelIsi()
{
	document.getElementById('jmlhPorsi').value='';
	document.getElementById('kode').value='';
	document.getElementById('method').value='insert';
        document.getElementById('kode').disabled=false;
}

function delPendidikan(eduid)
{

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
							loadData();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }	
		param='kode='+eduid+'&method=delete'
		tujuan='it_slave_5porsiNilai.php';
        if(confirm('Anda Yakin Ingin Menghapus Data..?'))
		     post_response_text(tujuan, param, respog);	
}
