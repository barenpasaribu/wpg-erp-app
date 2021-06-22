/* Show Data List */
function list() {
    var periodegaji = document.getElementById('periodegaji');
    var jenis = document.getElementById('jenis');
    var listBtn = document.getElementById('listBtn');
    var cancelBtn = document.getElementById('cancelBtn');
    var listCon = document.getElementById('listPosting');
    var jnsGaji = document.getElementById('jnsGaji');
	var agama = document.getElementById('agama');
    var tahun = document.getElementById('tahun');
    var tanggal = document.getElementById('tanggal');
    var param = "periodegaji="+getValue('periodegaji')+"&jenis="+getValue('jenis')+"&jnsGaji="+getValue('jnsGaji')+"&agama="+getValue('agama');
    param+='&tahun='+tahun.value+'&tanggal='+tanggal.value;

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //== Success Response
                    periodegaji.setAttribute('disabled','disabled');
                    jenis.setAttribute('disabled','disabled');
                    jnsGaji.setAttribute('disabled','disabled');
                    agama.setAttribute('disabled','disabled');
                    listBtn.setAttribute('disabled','disabled');
                    tahun.setAttribute('disabled','disabled');
                    cancelBtn.removeAttribute('disabled');
                    listCon.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('sdm_slave_bonus.php?proses=list', param, respon);
}

/* Clear */
function cancel() {
    var periodegaji = document.getElementById('periodegaji');
    var jenis = document.getElementById('jenis');
    var listBtn = document.getElementById('listBtn');
    var cancelBtn = document.getElementById('cancelBtn');
    var listCon = document.getElementById('listPosting');
    var jnsGaji = document.getElementById('jnsGaji');
    var tahun = document.getElementById('tahun');
    
    periodegaji.removeAttribute('disabled');
    jenis.removeAttribute('disabled');
    jnsGaji.removeAttribute('disabled');
    listBtn.removeAttribute('disabled');
    tahun.removeAttribute('disabled');
    cancelBtn.setAttribute('disabled','disabled');
    listCon.innerHTML = '';
}
function saveAll(maxRow,currentRow)
{
    if(confirm('Anda Yakin?'))
    saveIt(maxRow,currentRow);
}
/* Save to SDM Gaji */
function saveIt(maxRow,num) {
    var tr = document.getElementById('tr_'+num);
    var id = document.getElementById('id_'+num).getAttribute('value');
    var pengali = document.getElementById('pengali_'+num+'_text').value;
    var jumlah = document.getElementById('jumlah_'+num+'_text').value;
    var saveBtn = document.getElementById('save_'+num);
    var param = "periodegaji="+getValue('periodegaji')+"&jenis="+getValue('jenis');
    param += "&id="+id+"&pengali="+pengali+"&jumlah="+jumlah;
    
    document.getElementById('tr_'+num).style.backgroundColor='orange';
    
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById('tr_'+num).style.backgroundColor='red';
                    unlockScreen();
                } else {
                    //== Success Response
                    document.getElementById('tr_'+num).style.backgroundColor='green';
                    saveBtn.setAttribute('disabled','disabled');
                    num=num+1;
                    if(num<=maxRow)
                    {
                            lockScreen('wait');
                            saveIt(maxRow,num);
                    }
                    else
                        {
                            unlockScreen();
                            alert('Done');
                        }
                }
                saveBtn.removeAttribute('disabled');
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('sdm_slave_bonus.php?proses=post', param, respon);
    
}
function saveItDt(num) {
    var tr = document.getElementById('tr_'+num);
    var id = document.getElementById('id_'+num).getAttribute('value');
    var pengali = document.getElementById('pengali_'+num+'_text').value;
    var jumlah = document.getElementById('jumlah_'+num+'_text').value;
    var saveBtn = document.getElementById('save_'+num);
    var param = "periodegaji="+getValue('periodegaji')+"&jenis="+getValue('jenis');
    param += "&id="+id+"&pengali="+pengali+"&jumlah="+jumlah;
    
    document.getElementById('tr_'+num).style.backgroundColor='orange';
    saveBtn.setAttribute('disabled','disabled');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById('tr_'+num).style.backgroundColor='red';
                    unlockScreen();
                } else {
                    //== Success Response
                    document.getElementById('tr_'+num).style.backgroundColor='green';
                }
                saveBtn.removeAttribute('disabled');
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('sdm_slave_bonus.php?proses=post', param, respon);
    
}

/* Update Jumlah sesuai pengali gaji pokok */
function updJumlah(num) {
    var tr = document.getElementById('tr_'+num);
    var gajipokok = document.getElementById('gajipokok_'+num).getAttribute('value');
    var pengali = document.getElementById('pengali_'+num+'_text');
    var jumlah = document.getElementById('jumlah_'+num+'_text');
    test=parseFloat(gajipokok)*parseFloat(pengali.value);
    if(isNaN(test))
        {
            jumlah.value = 0;
        }
        else
            {
                jumlah.value=test;
            }
    
    tr.style.background = '#D7EBFA';
}
function zExcel(ev,tujuan,passParam)
{
	judul='Rekap THR/Bonus Excel';
	//alert(param);	
	var passP = passParam.split('##');
            var tahun = document.getElementById('tahun');
            var tanggal = document.getElementById('tanggal').value;
    var param = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
	param+='&proses=excel&tahun='+tahun.value+'&tanggal='+tanggal;
	//alert(param);
	printFile(param,tujuan,judul,ev)	
}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='250';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}