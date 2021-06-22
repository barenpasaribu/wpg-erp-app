/**
 * @author antoniuslouis
 */
function simpanIntraco()
{
	kodeorg=document.getElementById('kodeorg').value;
	jenis=document.getElementById('jenis').value;
	akunpiutang=document.getElementById('akunpiutang').value;
        akunhutang=document.getElementById('akunhutang').value;
	kodeorgbef=document.getElementById('kodeorgbef').value;
	jenisbef=document.getElementById('jenisbef').value;
	noakunbef=document.getElementById('noakunbef').value;
	met=document.getElementById('method').value;
        param='kodeorg='+kodeorg+'&jenis='+jenis+'&akunpiutang='+akunpiutang+'&kodeorgbef='+kodeorgbef;
        param+='&jenisbef='+jenisbef+'&noakunbef='+noakunbef+'&method='+met+'&akunhutang='+akunhutang;
        tujuan='keu_slave_5intracosave.php';
	if (confirm("Are you sure?")) {
		post_response_text(tujuan, param, respog);
	}
	
	function respog()
	{
              if(con.readyState==4)
              {
                if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    if((kodeorg=='')||(jenis=='')||(noakun=='')) alert('ERROR TRANSACTION, possibly caused by:\n- null fields.\n'); else 
                                    alert('ERROR TRANSACTION, possibly caused by:\n- duplicate entry, or \n- bad connection.\n'); 
                            }
                            else {
                                    //alert(con.responseText);
                                    document.getElementById('container').innerHTML=con.responseText;
                                    document.getElementById('kodeorgbef').value=document.getElementById('kodeorg').value;
                                    document.getElementById('jenisbef').value=document.getElementById('jenis').value;
                                    document.getElementById('noakunbef').value=document.getElementById('akunpiutang').value;
                                    document.getElementById('method').value='insert';
                            }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }
		
}

function hapusIntraco()
{
	kodeorg=document.getElementById('kodeorg').value;
	jenis=document.getElementById('jenis').value;
	akunpiutang=document.getElementById('akunpiutang').value;
	met='delete';
        param='kodeorg='+kodeorg+'&jenis='+jenis+'&akunpiutang='+akunpiutang+'&method='+met;
        tujuan='keu_slave_5intracosave.php';
	if(confirm("Are you sure?")){
        post_response_text(tujuan, param, respog);		
		
	}	
	
	function respog()
	{
              if(con.readyState==4)
              {
                if (con.status == 200) {
                        busy_off();
                        if (!isSaveResponse(con.responseText)) {
                                if((kodeorg=='')||(jenis=='')||(noakun=='')) alert('ERROR TRANSACTION, possibly caused by:\n- null fields.\n'); else 
                                alert('ERROR TRANSACTION, possibly caused by:\n- duplicate entry, or \n- bad connection.\n'); 
                        }
                        else {
                                //alert(con.responseText);
                                document.getElementById('container').innerHTML=con.responseText;
                                document.getElementById('kodeorgbef').value=document.getElementById('kodeorg').value;
                                document.getElementById('jenisbef').value=document.getElementById('jenis').value;
                                document.getElementById('noakunbef').value=document.getElementById('akunpiutang').value;
                                document.getElementById('method').value='insert';
                        }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
              }	
	 }
		
}


function fillField(kodeorg,jenis,noakun,noakun1)
{
	document.getElementById('kodeorg').value=kodeorg;
    document.getElementById('jenis').value=jenis;
	document.getElementById('akunpiutang').value=noakun;
        document.getElementById('akunhutang').value=noakun1;
	document.getElementById('kodeorgbef').value=kodeorg;
    document.getElementById('jenisbef').value=jenis;
	document.getElementById('noakunbef').value=noakun;
	document.getElementById('method').value='update';
}

function cancelIntraco()
{
    document.getElementById('kodeorg').value='';
	document.getElementById('jenis').value='';
	document.getElementById('akunpiutang').value='';
        document.getElementById('akunhutang').value='';
	document.getElementById('method').value='insert';		
}
