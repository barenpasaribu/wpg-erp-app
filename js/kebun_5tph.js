/**
 * @author antoniuslouis
 */
// dhyaz sep 22, 2011

function saveTph()
{
    kodeorg=document.getElementById('kodeorg');
    kodeorg=kodeorg.options[kodeorg.selectedIndex].value;
    notph=document.getElementById('notph').value;
    keterangan=document.getElementById('keterangan').value;
    tomb=document.getElementById('tombol');
    aksi=tomb.getAttribute('state',2);
    param='kodeorg='+kodeorg+'&notph='+notph+'&keterangan='+keterangan+'&aksi='+aksi;
    if(kodeorg=='')
        {
            alert('Kodeorganisasi masih kosong');
        }
    else if(notph==''){
        alert('No.Tph Pasih Kosong');
    }    
    else
     {
         tujuan='kebun_slave_5tph.php';
         post_response_text(tujuan, param, respon); 
     }   

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {                   
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {                      
                        document.getElementById('keterangan').value='';
                        document.getElementById('notph').value='';
                        document.getElementById('contain').innerHTML=con.responseText;
                        tomb=document.getElementById('tombol');
                        tomb.removeAttribute('state');                             
                        tomb.setAttribute('state', 'save');
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    } 
}

function getList(kodeorg)
{
    param='kodeorg='+kodeorg+'&aksi=list';
        tujuan='kebun_slave_5tph.php';
         post_response_text(tujuan, param, respon);  
         
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {                   
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {                      
                        document.getElementById('keterangan').value='';
                        document.getElementById('notph').value='';
                        document.getElementById('contain').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
}

function editData(kodeorg,notph,keterangan)
{
    document.getElementById('notph').value=notph;
    document.getElementById('keterangan').value=keterangan;
    opt=document.getElementById('kodeorg');
    	for(x=0;x<opt.length;x++)
	{
		if(opt.options[x].value==kodeorg)
		{
			opt.options[x].selected=true;
		}
	}
     tomb=document.getElementById('tombol');   
     tomb.removeAttribute('state');
     tomb.setAttribute('state', 'edit');
}

function deleteData(kodeorg,notph)
{
    
    param='kodeorg='+kodeorg+'&notph='+notph+'&aksi=del';
if(confirm('Delete, are you sure ?'))
     {
         tujuan='kebun_slave_5tph.php';
         post_response_text(tujuan, param, respon); 
     }   

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {                   
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {                      

                        tomb=document.getElementById('tombol');
                        tomb.removeAttribute('state');                             
                        tomb.setAttribute('state', 'save');
                        document.getElementById('contain').innerHTML=con.responseText;                        
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }  
    }
}

function cancelTph()
{
     document.getElementById('keterangan').value='';
    document.getElementById('notph').value='';
    tomb=document.getElementById('tombol');
    tomb.removeAttribute('state');                             
    tomb.setAttribute('state', 'save');   
}