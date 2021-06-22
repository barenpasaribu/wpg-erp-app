// JavaScript Document



function bersih()
{
	//	$arrPrm="##loksi##ipAdd##idRemote##userName##passwrd##dbnm##port";
	document.getElementById('loksi').value='';
	document.getElementById('ipAdd').value='';
	document.getElementById('idRemote').value='';
	document.getElementById('userName').value='';
	document.getElementById('passwrd').value='';
	document.getElementById('dbnm').value='';
	document.getElementById('port').value='';
}
function cancelSave()
{
	bersih();
	loadData();
}
function loadData()
{
	param='proses=LoadData';
	tujuan='setup_slave_remoteTimbangan.php';
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
							//alert(con.responseText);
							document.getElementById('contain').innerHTML=con.responseText;
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
		param='proses=LoadData';
		param+='&page='+num;
		tujuan = 'setup_slave_remoteTimbangan.php';
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('contain').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}

function saveData(passParam)
{
  var passP =  passParam.split('##');
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
	pros=document.getElementById('proses').value;
	param+='&proses='+pros;
	tujuan='setup_slave_remoteTimbangan.php';
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
							//alert(con.responseText);
							loadData();
							bersih();
							
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  
}
function fillField(idRmte)
{
	idRemote=idRmte;
	param='idRemote='+idRemote+'&proses=showData';
	tujuan='setup_slave_remoteTimbangan.php';
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
						//	alert(con.responseText);
							//loadData();
							ar=con.responseText.split("###");
							document.getElementById('idRemote').value=ar[0];
							document.getElementById('loksi').value=ar[1];
							document.getElementById('ipAdd').value=ar[2];
							document.getElementById('userName').value=ar[3];
							document.getElementById('passwrd').value=ar[4];
							document.getElementById('port').value=ar[5];
							document.getElementById('dbnm').value=ar[6];
							document.getElementById('proses').value='update';
							
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  

	
}
function deldata(idRmte)
{
	idRemote=idRmte;
	param='idRemote='+idRemote+'&proses=delData';
	//alert(param);
	tujuan='setup_slave_remoteTimbangan.php';
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
						//	alert(con.responseText);
							loadData();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  
	 if(confirm("Are You Sure Want Delete This Data"))
	 	post_response_text(tujuan, param, respog);
}
function printPDF(kdorg,tgl,ev) {
    // Prep Param
	kdORg=kdorg;
	daTtgl=tgl;
	param='kdOrg='+kdORg+'&daTtgl='+daTtgl;
    param += "&proses=pdf";
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='kebun_curahHujanPdf.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}
function cariCurah()
{
	kdOrg=document.getElementById('unitOrg').value;
	daTtgl=document.getElementById('tgl_cari').value;
	param='kdOrg='+kdOrg+'&daTtgl='+daTtgl+'&proses=cariData';
	//alert(param);
	tujuan='setup_slave_remoteTimbangan.php';
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
						//	alert(con.responseText);
							document.getElementById('contain').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  
	
}
/*t_response_text(tujuan, param, respog);
	function respog()
	{
		      if(con.readyState==4)
		      {
			        if (con.status == 200) {
						busy_off();
						if (!isSaveR*/