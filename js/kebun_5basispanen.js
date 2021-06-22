/**
 * @author antoniuslouis
 */
function loadData(num){
    	
        param='method=loadData';
        param+='&page='+num;
	tujuan='kebun_slave_save_5basispanen.php';
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
function simpanJ(){
	met=document.getElementById('method').value;
        regid=document.getElementById('regId');
        regid=regid.options[regid.selectedIndex].value;
        jenis=document.getElementById('jnsId');
        jenis=jenis.options[jenis.selectedIndex].value;
        dtbjr=document.getElementById('bjr').value;
        dtbasis=document.getElementById('basisjjg').value;
        rpper=document.getElementById('rpperkg').value;
        instif=document.getElementById('insentif').value;
        dnd=document.getElementById('denda');
        dnd=dnd.options[dnd.selectedIndex].value;
        param='jnsId='+jenis+'&bjr='+dtbjr+'&method='+met+'&regId='+regid;
        param+='&basisjjg='+dtbasis+'&rpperkg='+rpper+'&denda='+dnd+'&insentif='+instif;
        tujuan='kebun_slave_save_5basispanen.php';
        if(met=='update'){
            oldreg=document.getElementById('oldReg').value;
            oldjns=document.getElementById('oldJns').value;
            oldbjr=document.getElementById('oldBjr').value;
            param+='&oldReg='+oldreg+'&oldJns='+oldjns+'&oldBjr='+oldbjr;
        }
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
                                                        cancelJ();
							loadData();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
		
}
//'".$bar1->kodeorg."','".$bar1->jenis."','".$bar1->bjr."','".$bar1->basisjjg."','".$bar1->rplebih."','".$bar1->dendabasis."','".$bar1->rptopografi."'
function fillField(kdorg,jns,bjr,bsis,rplbh,dndbsis,tpgrafi){
    document.getElementById('oldReg').value=kdorg;
    document.getElementById('oldJns').value=jns;
    document.getElementById('oldBjr').value=bjr;
    jk=document.getElementById('regId');
    for(x=0;x<jk.length;x++){
            if(jk.options[x].value==kdorg)
            {
                    jk.options[x].selected=true;
            }
    }
    jke=document.getElementById('jnsId');
    for(x=0;x<jke.length;x++){
            if(jke.options[x].value==jns)
            {
                    jke.options[x].selected=true;
            }
    }
    document.getElementById('bjr').value=bjr;
    document.getElementById('basisjjg').value=bsis;
    document.getElementById('rpperkg').value=rplbh;
    document.getElementById('denda').value=dndbsis;
    document.getElementById('insentif').value=tpgrafi;
    document.getElementById('method').value='update';
}

function cancelJ(){
	document.getElementById('oldReg').value='';
	document.getElementById('oldJns').value='';
	document.getElementById('oldBjr').value='';
        document.getElementById('regId').value='';
        document.getElementById('jnsId').value='';
        document.getElementById('bjr').value='';
        document.getElementById('basisjjg').value='';
        document.getElementById('rpperkg').value='';
        document.getElementById('denda').value='';
        document.getElementById('insentif').value='';
	document.getElementById('method').value='insert';		
}
