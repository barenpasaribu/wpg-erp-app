function cari()
{
	txt=trim(document.getElementById('txt').value);
	
	

	param='txt='+txt+'&method=loadData';
	
	//param='txt='+txt+'&tgl='+tgl+'&method=loadData';
	tujuan='vhc_slave_persetujuanProject.php';
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
							}
						}
						else {
							busy_off();
							error_catch(con.status);
						}
				  }	
		 }
		 post_response_text(tujuan, param, respog);
}








function agree()
{
	width='300';
	height='10';
	//nopp=document.getElementById('nopp_'+id).value;
	content="<div id=containerd align=center></div>";
	ev='event';
	title="Persetujuan Atau Penolakan Form";
	showDialog1(title,content,width,height,ev);
	//get_data_pp();	
}

////////////////////////////////////////////////////////////11111111111111111111

function getApvForm(kode,setujuKe){

	agree();//ind
	param='method=getApvForm'+'&kode='+kode+'&setujuKe='+setujuKe;
	//alert(param);
	tujuan='vhc_slave_persetujuanProject.php';
	post_response_text(tujuan, param, respog);
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
                                    document.getElementById('containerd').innerHTML=con.responseText;
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
    
}

function saveApvForm(setujuKe)
{ 
	
	noKode=document.getElementById('noKode').value;
	apv=document.getElementById('apv').value;
	
	param='method=saveApvForm'+'&noKode='+noKode+'&apv='+apv+'&setujuKe='+setujuKe;
	//alert(param);return;
	tujuan='vhc_slave_persetujuanProject.php';
	//if(confirm('Anda yakin ingin menyetujui document '+ nodo +' ??'))
	if(confirm('Process Project number '+ noKode +' ??'))	
	{
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
					else 
					{
						loadData();
						closeDialog();	
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}
}







function cariBast(num)
{
		txt=trim(document.getElementById('txt').value);
		
	
		
		param='method=loadData';
		param+='&page='+num+'&txt='+txt;
		
		//param='txt='+txt+'&tgl='+tgl+'&status='+status+'&method=loadData';
		
		
		tujuan = 'vhc_slave_persetujuanProject.php';
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						//displayList();
						
						document.getElementById('container').innerHTML=con.responseText;
						//loadData();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}


function batal()
{
	document.location.reload();	
}


function loadData () 
{
	param='method=loadData';
	tujuan='vhc_slave_persetujuanProject.php';
    post_response_text(tujuan, param, respog);
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
                                   // alert(con.responseText);
                                    document.getElementById('container').innerHTML=con.responseText;
									
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
}



