// JavaScript Document

function savehk(fileTarget,passParam) 
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
    param+="&method=insert";
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } 
                else 
                {
                    loadData();
                    cancelIsi();
                    alert('Done.');
                }
            } 
            else 
            {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text(fileTarget+'.php', param, respon);
}

function deletehk(periode)
{
    fileTarget='sdm_slave_5hkEfektif';
    param='periode='+periode+'&method=delete';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } 
                else 
                {
                    loadData();
                    cancelIsi();
                }
            } 
            else 
            {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    if(confirm('Hapus data periode '+periode+'?'))post_response_text(fileTarget+'.php', param, respon);
}

function loadData()
{
    param='method=loadData';
    tujuan='sdm_slave_5hkEfektif';
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

function cancelIsi()
{
    document.getElementById('periode').value='';
    document.getElementById('hariminggu').value='';
    document.getElementById('harilibur').value='';
    document.getElementById('hkefektif').value='';
    document.getElementById('catatan').value='';
}

function DaysInMonth(y,m)
{
    return new Date(y,m,0).getDate();
}

function resetcontainer(){
    return;
}

function tambah()
{
    periode=document.getElementById('periode').value;
    tahunbulan = periode.split('-');
    jumlahhari=DaysInMonth(tahunbulan[0],tahunbulan[1]);
    hariminggu=document.getElementById('hariminggu').value;
    harilibur=document.getElementById('harilibur').value;
    hkefektif=jumlahhari-hariminggu-harilibur;
    if(periode!='')
    document.getElementById('hkefektif').value=hkefektif;
}