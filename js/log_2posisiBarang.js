
function batal()
{
	document.location.reload();
	
}

function searchPO(title,content,ev)
{
	width='500';
	height='400';
	showDialog1(title,content,width,height,ev);
	//alert('asdasd');
}
function resetOption(id){
	l=document.getElementById(id);
	l.selectedIndex=0;
}
function setDataPO(nopo)
{
	// l=document.getElementById('cariNopo').options[document.getElementById('cariNopo').selectedIndex].value;

	l=document.getElementById('nopo');

	for(a=0;a<l.length;a++)
	{
		if(l.options[a].value==nopo)
		{
			l.options[a].selected=true;
			break;
		}
	}

	closeDialog();
}
function findPO()
{
	nmPO=document.getElementById('nmPO').value;
	param='method=getPO'+'&noPo='+nmPO;
	tujuan='log_slave_2posisiBarang.php';
	console.log(param);
	post_response_text(tujuan, param, respog);
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					document.getElementById('containerPO').innerHTML=con.responseText;
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}

function cariNoPo(title,ev)
{
	content= "<div>";
	content+="<fieldset>No PO:<input type=text id=noPo class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=25><button class=mybutton onclick=goCariPo()>Go</button></fieldset>";
	content+="<div id=containercari style=\"height:300px;width:735px;overflow:scroll;\"></div></div>";
	//display window
	title=title+' PO:';
	width='750';
	height='350';
	showDialog1(title,content,width,height,ev);	
}



function goCariPo()
{
	noPo=trim(document.getElementById('noPo').value);
	if(noPo.length<4)
	{   
		alert('Text too short');
		return;
	}
	else
	{   
		param='method=goCariPo'+'&noPo='+noPo;
		tujuan = 'log_slave_2posisiBarang.php';
		post_response_text(tujuan, param, respog);			
	}
	//alert(param);
	function respog(){
			if (con.readyState == 4) {
					if (con.status == 200) {
							busy_off();
							if (!isSaveResponse(con.responseText)) {
									alert('ERROR TRANSACTION,\n' + con.responseText);
							}
							else {
									document.getElementById('containercari').innerHTML=con.responseText;
							}
					}
					else {
							busy_off();
							error_catch(con.status);
					}
			}
	}	
}



function goPickPO(nopo)
{
        document.getElementById('nopo').value=nopo;
		closeDialog();
}


/*
function cari()
{
	nopo=trim(document.getElementById('nopo').value);
	param='nopo='+nopo+'&method=loadData';
	//param='txt='+txt+'&tgl='+tgl+'&method=loadData';
	tujuan='log_slave_2posisiBarang.php';
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







function cariBast(num)
{
		txt=trim(document.getElementById('txt').value);
		
	
		
		param='method=loadData';
		param+='&page='+num+'&txt='+txt;
		
		//param='txt='+txt+'&tgl='+tgl+'&status='+status+'&method=loadData';
		
		
		tujuan = 'log_slave_2posisiBarang.php';
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



function loadData () 
{
	param='method=loadData';
	tujuan='log_slave_2posisiBarang.php';
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
*/


