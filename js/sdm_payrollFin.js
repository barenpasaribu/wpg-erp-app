/**
 * @author Developer E-Komoditi Solutions Indonesia
 */
function vLine(obj,no)
{
        if (obj.checked) {
                document.getElementById('ids' + no).disabled = false;
                document.getElementById('name' + no).disabled = false;
                document.getElementById('kdjrn' + no).disabled = false;
               
        }
        else
        {
                document.getElementById('ids' + no).disabled = true;
                document.getElementById('name' + no).disabled = true;
                document.getElementById('kdjrn' + no).disabled = true;
               		
        }
}

function saOneLine(no)
{
                id		=document.getElementById('idx' + no).value;
                kdjrn		=document.getElementById('kdjrn' + no).value;
				
        bankAccountColor(no,'orange');	 
        if (trim(kdjrn) != '' ) {
                param = 'ids=' + id + '&kdjrn=' + kdjrn;
               
                post_response_text('sdm_slaveSaveDataKodeJurnalGaji.php', param, respon);
        }
        else
        {
                alert('Kode Jurnal harus diisi');
        }

   function respon(){
                if (con.readyState == 4) {
                    if (con.status == 200) {
                        busy_off();
                        if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                                                bankAccountColor(no,'red');
                                        }
                        else {
                                                bankAccountColor(no,'#E8F2FC');
                                }
                    }
                    else {
                        busy_off();
                        error_catch(con.status);
                    }
                }
            }		
}

function bankAccountColor(no,color)
{
        document.getElementById('ids' + no).style.backgroundColor=color;
        document.getElementById('name' + no).style.backgroundColor=color;
        document.getElementById('kdjrn' + no).style.backgroundColor=color;
        
}

function saveAll(max)
{
        jumldata=0;
        dosaveAll(max);
}

function dosaveAll(max)
{
    jumldata+=1;
        if (jumldata <= max) {
                if (document.getElementById('check' + jumldata).checked) {
                        id = trim(document.getElementById('idx' + jumldata).innerHTML);
                        kdjrn = document.getElementById('kdjrn' + jumldata).value;
					
                        if (trim(kdjrn) != '' ) {
                                param = 'ids=' + id + '&kdjrn=' + kdjrn;
                                post_response_text('sdm_slaveSaveDataKodeJurnalGaji.php', param, respon);
                        }
                        else {
                                alert('Kode Jurnal harus diisi');
                        }
                }
                else
                {
                 dosaveAll(max);	
                }
        }
        else
        {
                alert('Finish');
        }
   function respon(){
                if (con.readyState == 4) {
                    if (con.status == 200) {
                        busy_off();
                        if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                                                bankAccountColor(jumldata,'red');
                                        }
                        else {
                                                bankAccountColor(jumldata,'#E8F2FC');
                                                dosaveAll(max);
                                }
                    }
                    else {
                        busy_off();
                        error_catch(con.status);
                    }
                }
            }		
}
