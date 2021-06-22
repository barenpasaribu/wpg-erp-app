/**
 * @author antoniuslouis
 */
function simpanDep()
{
	kode=document.getElementById('kode').value;
	nama=document.getElementById('nama').value;
	met=document.getElementById('method').value;
        akn=document.getElementById('noakunId').value;
	if(trim(kode)=='')
	{
		alert('Code is empty');
		document.getElementById('kode').focus();
	}
	if(trim(nama)=='')
	{
		alert('Name is empty');
		document.getElementById('nama').focus();
	}
	else
	{
		kode=trim(kode);
		nama=trim(nama);
		param='kode='+kode+'&nama='+nama+'&method='+met+'&noakun='+akn;
		tujuan='budget_slave_kode_budget.php';
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
                                                        cancelDep();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
		
}

function fillField(kode,nama,akun)
{
    document.getElementById('kode').value=kode;
    document.getElementById('kode').disabled=true;
    document.getElementById('nama').value=nama;
    document.getElementById('method').value='update';
    document.getElementById('noakunId').value=akun;
}

function cancelDep()
{
        document.getElementById('kode').disabled=false;
	document.getElementById('kode').value='';
	document.getElementById('nama').value='';
	document.getElementById('method').value='insert';
        document.getElementById('noakunId').value='';
}
