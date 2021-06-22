//JS 

function cariBast(num)
{		
		kodedetail=document.getElementById('kodedetail').value;
		param='method=loadData';
		param+='&page='+num;
		param+='&kodedetail='+kodedetail;
		
		tujuan = 'setup_slave_mtuang.php';
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


function cancel()
{	
	document.getElementById('kodetambah').value='';
	document.getElementById('matauangtambah').value='';
	document.getElementById('simboltambah').value='';	
	document.getElementById('kodeisotambah').value='';
	document.getElementById('method').value='insert';
	document.location.reload();	
}




function loadData (kode) 
{
	document.getElementById('kodedetail').value=kode;
	param='method=loadData'+'&kode='+kode;
	tujuan='setup_slave_mtuang.php';
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





function delhead(kode,matauang,simbol,kodeiso)
{
	if(confirm('Anda yakin untuk menghapus '+ kode +' ?'))
	param='method=delhead'+'&kode='+kode+'&matauang='+matauang+'&simbol='+simbol+'&kodeiso='+kodeiso;
	//alert(param);
	tujuan='setup_slave_mtuang.php';
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
					else 
					{
						cancel();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}

function deldetail(kode,daritanggal,jam)
{
	param='method=deldetail'+'&kode='+kode+'&daritanggal='+daritanggal+'&jam='+jam;
	//alert(param);
	tujuan='setup_slave_mtuang.php';
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
					else 
					{
						//loadData();
						cariBast();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}


function simpanbaru()
{
	kodetambah=document.getElementById('kodetambah').value;
	matauangtambah=document.getElementById('matauangtambah').value;
	simboltambah=document.getElementById('simboltambah').value;
	kodeisotambah=document.getElementById('kodeisotambah').value;
	method=document.getElementById('method').value;
	
	param='kodetambah='+kodetambah+'&matauangtambah='+matauangtambah+'&simboltambah='+simboltambah+'&kodeisotambah='+kodeisotambah+'&method='+method;
	tujuan='setup_slave_mtuang.php';
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
							cancel();							
                           // loadData();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
}



function simpandetail()
{
	kodedet=document.getElementById('kodedet').value;
	tgl=document.getElementById('tgl').value;
	jm=document.getElementById('jm').value;
	mn=document.getElementById('mn').value;
	kursdet=document.getElementById('kursdet').value;
	method=document.getElementById('method').value;
	param='method=simpandetail'+'&tgl='+tgl+'&jm='+jm+'&mn='+mn+'&kursdet='+kursdet+'&kodedet='+kodedet;
	//alert(param);
	tujuan='setup_slave_mtuang.php';
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
							cariBast();

						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
}



function edithead(kode)
{
	kodehead=kode;
	kodeheadedit=document.getElementById('kode'+kode).value;
	matauangheadedit=document.getElementById('matauang'+kode).value;
	simbolheadedit=document.getElementById('simbol'+kode).value;
	kodeisoheadedit=document.getElementById('kodeiso'+kode).value;
	methodheadedit=document.getElementById('method').value;
	
	if(confirm('Anda yakin untuk merubah data '+ kode +' ?'))
	
	
	param='method=edithead'+'&kodeheadedit='+kodeheadedit+'&matauangheadedit='+matauangheadedit+'&simbolheadedit='+simbolheadedit+'&kodeisoheadedit='+kodeisoheadedit+'&kodehead='+kodehead;
	//alert(param);
	tujuan='setup_slave_mtuang.php';
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
							cancel();							
                           // loadData();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
}


