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
	tujuan='it_slave_5stKepuasan';
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
function fillField(kdkeg,nil,kt)
{
    //$arr="##kode##nilKode##ket##method";
	document.getElementById('kode').value=kdkeg;
        document.getElementById('kode').disabled=true;
        document.getElementById('nilKode').disabled=true;
	document.getElementById('nilKode').value=nil;
	document.getElementById('ket').value=kt;
	document.getElementById('method').value='update';
}

function cancelIsi()
{
	document.getElementById('nilKode').value='';
	document.getElementById('ket').value='';
	document.getElementById('kode').value='';
	document.getElementById('method').value='insert';
        document.getElementById('kode').disabled=false;
        document.getElementById('nilKode').disabled=false;
}

function delPendidikan(eduid,kdrt)
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
		param='kode='+eduid+'&method=delete'+'&nilKode='+kdrt;
		tujuan='it_slave_5stKepuasan.php';
        if(confirm('Anda Yakin Ingin Menghapus Data..?'))
		     post_response_text(tujuan, param, respog);	
}
