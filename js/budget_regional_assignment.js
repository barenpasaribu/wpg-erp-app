/**
 * @author antoniuslouis
 */
function simpanDep()
{
	kode=document.getElementById('organisasi');
	nama=document.getElementById('regional');
        organisasi=kode.options[kode.selectedIndex].value;
        regional=nama.options[nama.selectedIndex].value;
	met=document.getElementById('method').value;
	if(trim(organisasi)=='')
	{
		alert('Organisasi is empty');
		document.getElementById('kode').focus();
	}
	else
	if(trim(regional)=='')
	{
		alert('Regional is empty');
		document.getElementById('nama').focus();
	}
        else
	{
		organisasi=trim(organisasi);
		regional=trim(regional);
		param='organisasi='+organisasi+'&regional='+regional+'&method='+met;
		tujuan='budget_slave_regional_assignment.php';
		paramcek='organisasi='+organisasi+'&regional='+regional;
		tujuancek='budget_slave_regional_assignment_cek.php';
                
                
        post_response_text(tujuancek, param, respogcek);		
                
//        post_response_text(tujuan, param, respog);		
	}

    function respogcek(){
        if (con.readyState == 4) {
            if (con.status == 200) {
		busy_off();
		if (!isSaveResponse(con.responseText)) {
		} else {
                    if(con.responseText){
//kasih peringatan sudah ada data                        
//                        if(confirm(con.responseText))
                        alert(con.responseText);
//                        post_response_text(tujuan, param, respog);	
                    }else{
                        post_response_text(tujuan, param, respog);    
                    }
		}
            } else {
                busy_off();
		error_catch(con.status);
            }
        }
    }		
 

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
							document.getElementById('container').innerHTML=con.responseText;
                                                        //cancelDep();
                                                        alert('Done.');
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
		
}

function deleteDep(kode,nama)
{
	met='delete';
	{
		param='organisasi='+kode+'&regional='+nama+'&method='+met;
		tujuan='budget_slave_regional_assignment.php';
                        if(confirm('Delete?'))
        post_response_text(tujuan, param, respog);		
	}
	
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
							document.getElementById('container').innerHTML=con.responseText;
//                                                        cancelDep();
                                                        alert('Done.');
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
		
}

function cancelDep()
{   
    document.getElementById('organisasi').value='';
    document.getElementById('regional').value='';
}