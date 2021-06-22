// JavaScript Document

function angkadowang(e)//only numeric e is event
{
    key=getKey(e);
    if((key<48 || key>57) && (key!=8 && key!=127 && key!=true))
    return false;
    else
    {
        return true;
    }
}

function loadData()
{
    param='method=loadData';
    tujuan='kebun_slave_5stproduksi';
    post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('container').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}

function saveFranco(fileTarget,passParam) {
    param='';
    var passP = passParam.split('##');
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
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

function fillField(jenis,tanah,umur,produksi)
{
    document.getElementById('bibit').value=jenis;
    document.getElementById('oldjb').value=jenis;
    document.getElementById('umur').value=umur;
    document.getElementById('oldum').value=umur;
    document.getElementById('tanah').value=tanah;
    document.getElementById('oldkt').value=tanah;
    document.getElementById('produksi').value=produksi;
    document.getElementById('method').value="update";
}

function cancelIsi()
{
    document.getElementById('bibit').value='';
    document.getElementById('oldjb').value='';
    document.getElementById('umur').value='';
    document.getElementById('oldum').value='';
    document.getElementById('tanah').value='';
    document.getElementById('oldkt').value='';
    document.getElementById('produksi').value='';
    document.getElementById('method').value="insert";
}
