// JavaScript Document
function displayList()
{
	loadData();
	document.getElementById('list_ganti').style.display='block';
	document.getElementById('headher').style.display='none';
	document.getElementById('txtsearch').value='';
	document.getElementById('tgl_cari').value='';
}
function loadData()
{
	param='proses=loadData';
	tujuan='kebun_slavepengirimanBibit.php';
	post_response_text(tujuan, param, respon);
	function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
					//alert(con.responseText);
					document.getElementById('list_ganti').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}
function add_new_data()
{
	document.getElementById('list_ganti').style.display='none';
	document.getElementById('headher').style.display='block';
	document.getElementById('trans_no').disabled= true;
	bersihForm();

}
function bersihForm()
{
	document.getElementById('tgl').disabled=false;
	document.getElementById('codeOrg').disabled=false;
	document.getElementById('OrgTujuan').disabled=false;
	document.getElementById('jmlh').disabled=false;
	document.getElementById('OrgTujuan').disabled=false;
	document.getElementById('jnsBibit').disabled=false;
	document.getElementById('custId').disabled=false;
	
	document.getElementById('trans_no').value='';
	document.getElementById('tgl').value='';
	document.getElementById('codeOrg').value='';
	document.getElementById('OrgTujuan').value='';
	document.getElementById('jmlh').value='0';
	document.getElementById('OrgTujuan').value='';
	document.getElementById('jnsBibit').value='';
	document.getElementById('custId').value='';
}
function getNotrans()
{
	kdOrg=document.getElementById('codeOrg').value;
	param='proses=generateNo'+'&codeOrg='+kdOrg;
	tujuan='kebun_slavepengirimanBibit.php';
	post_response_text(tujuan, param, respon);
	function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
						document.getElementById('trans_no').value = con.responseText;
						
	             }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function knciForm()
{
	arData=document.getElementById('OrgTujuan').options[document.getElementById('OrgTujuan').selectedIndex].value
	CustData=document.getElementById('custId').options[document.getElementById('custId').selectedIndex].value;
	if((CustData!='')&&(arData!=''))
	{
		document.getElementById('custId').options[0].selected=true;
		document.getElementById('OrgTujuan').options[0].selected=true;
		alert("Permintaan Dari Dan Nama Customer Tidak Bisa Terisi Bersamaan");
	}
	else if(arData!='')
	{
		document.getElementById('custId').options[0].selected=true;
	}
	else if(CustData!='')
	{
		document.getElementById('OrgTujuan').options[0].selected=true;
	}
	
}
function opnCust()
{
	document.getElementById('custId').disabled=false;
	document.getElementById('OrgTujuan').disabled=true;
	document.getElementById('OrgTujuan').value='';
	document.getElementById('kegCode').value='';
}
function opnOrg()
{
	document.getElementById('custId').disabled=true;
	document.getElementById('OrgTujuan').disabled=false;
	document.getElementById('custId').value='';
}
function saveData()
{
	notrans=document.getElementById('trans_no').value;
	tgl=document.getElementById('tgl').value;
	codeorg=document.getElementById('codeOrg').value;
	orgtujuan=document.getElementById('OrgTujuan').value;
	jmlh=document.getElementById('jmlh').value;
	orgTujuan=document.getElementById('OrgTujuan').value;
	jnsbibit=document.getElementById('jnsBibit').value;
	custid=document.getElementById('custId').value;
	prose=document.getElementById('proses').value;
	kdKeg=document.getElementById('kegCode').value;
	param='notrans='+notrans+'&tgl='+tgl+'&codeOrg='+codeorg+'&orgTujuan='+orgtujuan+'&jmlh='+jmlh+'&jnsBibit='+jnsbibit;
	param+='&custId='+custid+'&proses='+prose+'&kdKeg='+kdKeg;
	//alert(param);
	tujuan='kebun_slavepengirimanBibit.php';
	
	function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
						displayList();
						
	             }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	post_response_text(tujuan, param, respon);
}
function fillField(norans)
{
	notrans=norans;
	param='notrans='+notrans+'&proses=getData'
	tujuan='kebun_slavepengirimanBibit.php';
	function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
					document.getElementById('proses').value='update';
					ar=con.responseText.split("###");
					document.getElementById('trans_no').value=notrans;
					document.getElementById('trans_no').disabled=true;
					document.getElementById('tgl').value=ar[1];
					document.getElementById('codeOrg').value=ar[0];
					document.getElementById('codeOrg').disabled=true;
					if(ar[4]==1)
					{
						document.getElementById('OrgTujuan').options[0].selected=true;
						
					}
					else if(ar[5]==1)
					{	
						document.getElementById('custId').options[0].selected=true;
					}
					document.getElementById('OrgTujuan').value=ar[4];
					document.getElementById('jmlh').value=ar[3];
					document.getElementById('jnsBibit').value=ar[2];
					document.getElementById('custId').value=ar[5];
					document.getElementById('kegCode').value=ar[6];
					document.getElementById('list_ganti').style.display='none';
					document.getElementById('headher').style.display='block';
						
	             }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	post_response_text(tujuan, param, respon);
	
}
function cancelSave()
{
	bersihForm();
	document.getElementById('list_ganti').style.display='block';
	document.getElementById('headher').style.display='none';						
}
function delData(norans)
{
	notrans=norans;
	param='notrans='+notrans+'&proses=delData'
	tujuan='kebun_slavepengirimanBibit.php';
	function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
						displayList();
						
	             }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	if(confirm("Are You Sure Want Delete This Data !!!"))
	post_response_text(tujuan, param, respon);
	
}
function cariTransaksi()
{
	txtSearch=document.getElementById('txtsearch').value;
	txtTgl=document.getElementById('tgl_cari').value;
	
	param='txtSearch='+txtSearch+'&txtTgl='+txtTgl+'&proses=cari_transaksi';
	//alert(param);
	tujuan='kebun_slavepengirimanBibit.php';
	post_response_text(tujuan, param, respog);			
	function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('list_ganti').style.display='block';
						document.getElementById('headher').style.display='none';
						document.getElementById('list_ganti').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
function dataKeExcel(ev,tujuan)
{
	judul=jdlExcel;
	//alert(param);	
	param='';
	printFile(param,tujuan,judul,ev)	
}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}