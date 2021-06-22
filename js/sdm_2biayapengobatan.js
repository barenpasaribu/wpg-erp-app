zPreview/**
 * @author antoniuslouis
 */
// dhyaz sep 22, 2011

function getlevel0()
{
    tanggal =document.getElementById('tanggal').value;
    param='proses=preview&tanggal='+tanggal;
    tujuan='sdm_slave_2summarykaryawan.php'; 
    post_response_text(tujuan, param, respog);

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('printContainer0').innerHTML=con.responseText;
                    document.getElementById('printContainer1').innerHTML='';
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}

function getlevel1(tanggal,region)
{
    param='proses=level1&tanggal='+tanggal+'&region='+region;
    tujuan='sdm_slave_2summarykaryawan.php'; 
//    alert(param);
    post_response_text(tujuan, param, respog);

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('printContainer1').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}

function printFile(param,tujuan,title,ev){
   tujuan=tujuan+"?"+param;  
   width='900';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}

function level1excel(ev,tujuan,tanggal,region)
{
    param='proses=excel&tanggal='+tanggal+'&region='+region;

    judul='Report Ms.Excel';	
    printFile(param,tujuan,judul,ev)	
}
function getUnit2(){
    pro=document.getElementById('ptId2');
    prod=pro.options[pro.selectedIndex].value;
    param='proses=getUnit'+'&ptId2='+prod;
    tujuan='log_slave_2gdangAccounting2.php';
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
                               document.getElementById('unitId2').innerHTML=con.responseText;
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
              }	
	 }  
}
function getlevel2(tanggal,region){
    param='proses=level1&prdIdDr2='+tanggal+'&region='+region;
    tujuan='sdm_slave_2summarykaryawan2.php'; 
//    alert(param);
    post_response_text(tujuan, param, respog);

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {                     
                    document.getElementById('printContainer5').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}
function zPreview(fileTarget,passParam,idCont) {
    var passP = passParam.split('##');
    var param = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
  // alert(param);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    
                    var res = document.getElementById(idCont);
                    res.style.display="block";
                    res.innerHTML = con.responseText;
                    document.getElementById('printContainer5').style.display="none";
                    document.getElementById('printContainer7').style.display="none";
                    document.getElementById('printContainer8').style.display="none";
                    document.getElementById('printContainer9').style.display="none";
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
    post_response_text(fileTarget+'.php?proses=preview', param, respon);

}
function zExcel(ev,tujuan,passParam){
	judul='Report Excel';
	//alert(param);	
	var passP = passParam.split('##');
    var param = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
	param+='&proses=excel';
	printFile(param,tujuan,judul,ev)	
}
function detailDt2(tpkary,bypengobatan,thn,smstr,pt,unit){
        param='tipeKary='+tpkary+'&byPeng='+bypengobatan;
        param+='&proses=getDetail2'+'&thn='+thn+'&ptId2='+pt;
        param+='&unitId2='+unit+'&smstr='+smstr;
        tujuan='sdm_slave_2biayapengobatan.php';
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
                                document.getElementById('printContainer2').style.display="none";
                                document.getElementById('printContainer5').style.display="none";
                                document.getElementById('printContainer7').innerHTML=con.responseText;
                                document.getElementById('printContainer7').style.display="block";
                                document.getElementById('printContainer8').style.display="none";
                                document.getElementById('printContainer9').style.display="none";
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
              }	
	 }  
}
//$drilkedua="style='cursor:pointer;' onclick=detailDt3('".$lstKdorg."','".$dtLstTipekary."','".$prd."','".$param['byPeng']."','".$param['thn']."','".$param['smstr']."','".$param['ptId2']."','".$param['unitId2']."')";
function detailDt3(unit,tpkary,periode,bypengobatan){
        param='tipeKary='+tpkary+'&byPeng='+bypengobatan;
        param+='&proses=getDetail3';
        param+='&periode='+periode+'&unitId2='+unit;
        tujuan='sdm_slave_2biayapengobatan.php';
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
                                document.getElementById('printContainer2').style.display="none";
                                document.getElementById('printContainer5').style.display="none";
                                document.getElementById('printContainer8').innerHTML=con.responseText;
                                document.getElementById('printContainer8').style.display="block";
                                document.getElementById('printContainer7').style.display="none";
                                document.getElementById('printContainer9').style.display="none";
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
              }	
	 }  
}
function zExcelDt(ev,tujuan,reg,bypengobatan,thn,smstr,pt,unit){
	judul='Detail Excel';
	//alert(param);	
	param='byPeng='+bypengobatan;
        param+='&thn='+thn+'&ptId2='+pt+'&regional='+reg;
        param+='&smstr='+smstr+'&unitId2='+unit;
        param+='&proses=excelgetDetail2';
	printFile(param,tujuan,judul,ev)	
}
function zExcelDt2(ev,tujuan,unit,tpkary,periode,bypengobatan){
	judul='Detail Excel';
        param='tipeKary='+tpkary+'&byPeng='+bypengobatan;
        param+='&proses=excelgetDetail3';
        param+='&periode='+periode+'&unitId2='+unit;
	alert(param);	
	printFile(param,tujuan,judul,ev)	
}
function kembali(pil){
    if(pil==0){
            document.getElementById('printContainer2').style.display="block";
            document.getElementById('printContainer5').style.display="none";
            document.getElementById('printContainer7').style.display="none";
            document.getElementById('printContainer8').style.display="none";
            document.getElementById('printContainer9').style.display="none";
    }
    if(pil==1){
            document.getElementById('printContainer2').style.display="none";
            document.getElementById('printContainer5').style.display="block";
            document.getElementById('printContainer7').style.display="none";
            document.getElementById('printContainer8').style.display="none";
            document.getElementById('printContainer9').style.display="none";
    }
    if(pil==2){
            document.getElementById('printContainer2').style.display="none";
            document.getElementById('printContainer5').style.display="none";
            document.getElementById('printContainer7').style.display="block";
            document.getElementById('printContainer8').style.display="none";
            document.getElementById('printContainer9').style.display="none";
    }
    if(pil==3){
            document.getElementById('printContainer2').style.display="none";
            document.getElementById('printContainer5').style.display="none";
            document.getElementById('printContainer8').style.display="block";
            document.getElementById('printContainer7').style.display="none";
            document.getElementById('printContainer9').style.display="none";
    }
}
function previewPengobatan(notransaksi,ev)
{
    param='notransaksi='+notransaksi;
    tujuan='sdm_slave_previewPengobatan.php';
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
                                                       title=notransaksi;
                                                       width='500';
                                                       height='400';
                                                       content="<div style='height:380px;width:480px;overflow:scroll;'>"+con.responseText+"</div>";
                                                       showDialog1(title,content,width,height,ev);
                                            }
                                    }
                                    else {
                                            busy_off();
                                            error_catch(con.status);
                                    }
                  }	
     }			
}

function getDetRegional(regional,kdbiaya){
    region=regional;
    unt=document.getElementById('unitId2');
    unt=unt.options[unt.selectedIndex].value;
    pt=document.getElementById('ptId2');
    pt=pt.options[pt.selectedIndex].value;
    tahun=document.getElementById('thn');
    tahun=tahun.options[tahun.selectedIndex].value;
    semester=document.getElementById('smstr');
    semester=semester.options[semester.selectedIndex].value;
    param='proses=getDetailRegional';
    param+='&regional='+region+'&ptId2='+pt+'&unitId2';
    param+='&thn='+tahun+'&smstr='+semester;
    if(kdbiaya!=''){
        param+='&byPeng='+kdbiaya;
    }
    tujuan='sdm_slave_2biayapengobatan1.php';
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
                                document.getElementById('printContainer2').style.display="none";
                                document.getElementById('printContainer5').style.display="block";
                                document.getElementById('printContainer5').innerHTML=con.responseText;
                                document.getElementById('printContainer7').style.display="none";
                                document.getElementById('printContainer9').style.display="none";
                                document.getElementById('printContainer8').style.display="none";
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
              }	
	 }  
    
}

function detailStaff(tpkary,tahun,smeter,pete,unitid,region,jnsby){
    param='proses=detailStaff';
    param+='&regional='+region+'&ptId2='+pt+'&unitId2='+unitid;
    param+='&thn='+tahun+'&smstr='+semester;
     if(jnsby!=''){
            param+='&byPeng='+jnsby;
        }
    tujuan='sdm_slave_2biayapengobatan1.php';
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
                                document.getElementById('printContainer2').style.display="none";
                                document.getElementById('printContainer5').style.display="none";
                                document.getElementById('printContainer7').innerHTML=con.responseText;
                                document.getElementById('printContainer7').style.display="block";
                                document.getElementById('printContainer9').style.display="none";
                                document.getElementById('printContainer8').style.display="none";
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
              }	
	 }  
}
function getDetDept(bag,reg,pt,unit,tahun,semester,jnsby){
    param='proses=getDetDept';
    param+='&regional='+reg+'&ptId2='+pt+'&unitId2='+unit;
    param+='&thn='+tahun+'&smstr='+semester+'&bagian='+bag;
    if(jnsby!=''){
            param+='&byPeng='+jnsby;
        }
    tujuan='sdm_slave_2biayapengobatan1.php';
    
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
                                document.getElementById('printContainer2').style.display="none";
                                document.getElementById('printContainer5').style.display="none";
                                document.getElementById('printContainer8').innerHTML=con.responseText;
                                document.getElementById('printContainer8').style.display="block";
                                document.getElementById('printContainer9').style.display="none";
                                document.getElementById('printContainer7').style.display="none";
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
              }	
	 }  
}
//zExcelStaff2
function zExcelStaff(ev,tujuan,tpkary,tahun,smeter,pete,unitid,region,jnsby){
	judul='Report Excel';
        param+='&regional='+region+'&ptId2='+pt+'&unitId2='+unitid;
        param+='&thn='+tahun+'&smstr='+semester;
	param+='&proses=excelStaffDet';
        if(jnsby!=''){
            param+='&byPeng='+jnsby;
        }
	printFile(param,tujuan,judul,ev)	
}
function zExcelStaff2(ev,tujuan,bag,jnsby,reg,pt,unit,tahun,semester){//'".$lstRegional."','".$param['regional']."','".$param['ptId2']."','".$param['unitId2']."','".$param['thn']."','".$param['smstr']."'
	judul='Report Excel';
        param+='&regional='+reg+'&ptId2='+pt+'&unitId2='+unit;
        param+='&thn='+tahun+'&smstr='+semester+'&bagian='+bag;
        if(jnsby!=''){
            param+='&byPeng='+jnsby;
        }
	param+='&proses=excelStaffDet2';
	printFile(param,tujuan,judul,ev)	
}
//getKaryDept('".$lstRegional."','".$lstBy."','".$dtTgs[$lstRegional]."','".$prd."')
function getKaryDept(karyid,jnsby,unt,prd){
    param='proses=detailKarywan';
    param+='&karyId='+karyid+'&byPeng='+jnsby+'&unitId2='+unt;
    param+='&periode='+prd;
    tujuan='sdm_slave_2biayapengobatan1.php';
   
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
                                document.getElementById('printContainer2').style.display="none";
                                document.getElementById('printContainer5').style.display="none";
                                document.getElementById('printContainer9').innerHTML=con.responseText;
                                document.getElementById('printContainer9').style.display="block";
                                document.getElementById('printContainer8').style.display="none";
                                document.getElementById('printContainer7').style.display="none";
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
              }	
	 }  
}
function getDetailDept(bag,jnsby,reg,pt,unit,tahun,semester){
    param='proses=getDetailDeptBy';
    param+='&bagian='+bag+'&byPeng='+jnsby+'&unitId2='+unit;
    param+='&regional='+reg+'&ptId2='+pt+'&thn='+tahun+'&smstr='+semester;
    tujuan='sdm_slave_2biayapengobatan1.php';
   
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
                                document.getElementById('printContainer2').style.display="none";
                                document.getElementById('printContainer5').style.display="none";
                                document.getElementById('printContainer8').innerHTML=con.responseText;
                                document.getElementById('printContainer8').style.display="block";
                                document.getElementById('printContainer9').style.display="none";
                                document.getElementById('printContainer7').style.display="none";
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
              }	
	 }   
}