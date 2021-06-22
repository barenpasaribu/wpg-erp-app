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
	tujuan='it_slave_5stNilaiKegiatan';
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
function fillField(kdkeg,kt,sat,nilsbk,bik,ckp,krg)
{
    //$arr="##kdkegiatan##ket##satuan##nilsngtbaik##nilbaik##nilckp##nilkrg##method";
	document.getElementById('kdkegiatan').value=kdkeg;
        document.getElementById('kdkegiatan').disabled=true;
	document.getElementById('ket').value=kt;
	document.getElementById('satuan').value=sat;
	document.getElementById('nilsngtbaik').value=nilsbk;
        document.getElementById('nilbaik').value=bik;
        document.getElementById('nilckp').value=ckp;
        document.getElementById('nilkrg').value=krg;
	document.getElementById('method').value='update';
}

function cancelIsi()
{
	document.getElementById('kdkegiatan').value='';
	document.getElementById('ket').value='';
	document.getElementById('satuan').value='';
	document.getElementById('nilsngtbaik').value='0';
        document.getElementById('nilbaik').value='0';
        document.getElementById('nilckp').value='0';
        document.getElementById('nilkrg').value='1000000000';
	document.getElementById('method').value='insert';
        document.getElementById('kdkegiatan').disabled=false;
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
		param='kdkegiatan='+eduid+'&method=delete';
		tujuan='it_slave_5stNilaiKegiatan.php';
        if(confirm('Anda Yakin Ingin Menghapus Data..?'))
		     post_response_text(tujuan, param, respog);	
}
