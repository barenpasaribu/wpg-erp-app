//1625
function saveData(){
    tngi=document.getElementById('tinggi').value;
    volc=document.getElementById('vol').value;
    kodeorg=document.getElementById('kodeorg').value;
    tngki=document.getElementById('kdTangki');
    tngki=tngki.options[tngki.selectedIndex].value;
    oldtngk=document.getElementById('oldkdTangki').value;
    oldtingg=document.getElementById('oldTinggi').value;
    pros=document.getElementById('proses').value;
   
    param='kodeorg='+kodeorg+'&tinggi='+tngi+'&vol='+volc+'&proses='+pros+'&kdTangki='+tngki;
    if(oldtngk!=''){
        param+='&oldkdTangki='+oldtngk;
    }
    if(oldtingg!=''){
        param+='&oldTinggi='+oldtingg;
    }
    tujuan='pabrik_slave_5volumetangki.php';
    post_response_text(tujuan, param, respog);
    
    
    function respog(){
      if(con.readyState==4)
      {
            if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                    }
                    else {
                           loadData();
                           clearForm();
                      }
                    }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }	
    }  
}
function clearForm(){
    document.getElementById('tinggi').value='';
    document.getElementById('vol').value='';
    document.getElementById('kdTangki').value='';
    document.getElementById('oldTinggi').value='';
    document.getElementById('oldkdTangki').value='';
    document.getElementById('proses').value='saveAll';
} 

function loadData(){
    kdTang=document.getElementById('tangkiCr');
    kdTang=kdTang.options[kdTang.selectedIndex].value;
    tingCr=document.getElementById('tinggiCm').value;
    param='proses=loadData'+'&tangkiCr='+kdTang+'&tinggiCm='+tingCr;
    tujuan='pabrik_slave_5volumetangki.php';
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
                                document.getElementById('container').innerHTML=con.responseText;
                                loadData2();
                                
                      }
                    }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }	
    }  
}
function loadData2(){
    kdTang=document.getElementById('tangkiCr2');
    kdTang=kdTang.options[kdTang.selectedIndex].value;
    kdOrg=document.getElementById('kodeOrg2');
    kdOrg=kdOrg.options[kdOrg.selectedIndex].value;
    param='proses=loadData2'+'&tangkiCr2='+kdTang+'&kdOrg='+kdOrg;
    tujuan='pabrik_slave_5volumetangki.php';
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
                                aer=1;
                                document.getElementById('containerlist').innerHTML=con.responseText;
                      }
                    }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }	
    }  
}

function getExcel(ev,tujuan){
    kdTang=document.getElementById('tangkiCr2');
    kdTang=kdTang.options[kdTang.selectedIndex].value;
    kdOrg=document.getElementById('kodeOrg2');
    kdOrg=kdOrg.options[kdOrg.selectedIndex].value;
    param='&tangkiCr2='+kdTang+'&kdOrg='+kdOrg;
    width='250';
    height='180';
    content="<iframe frameborder=0 width=100% height=280 src='"+tujuan+"?proses=excel"+param+"'></iframe>"
    showDialog1('Excel ',content,width,height,ev); 
}
function delData(kdorg,prd,ting){
    param='kodeorg='+kdorg+'&kdTangki='+prd+'&tinggiCm='+ting;
    param+='&proses=delData';
    tujuan='pabrik_slave_5volumetangki.php';
    if(confirm("Yakin akan hapus data ?")){
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

function fillField(kodetangki,tinggicm,volume){
     jk=document.getElementById('kdTangki');
        for(x=0;x<jk.length;x++)
        {
                if(jk.options[x].value==kodetangki)
                {
                        jk.options[x].selected=true;
                }
        }
    document.getElementById('tinggi').value=tinggicm;
    document.getElementById('vol').value=volume;
    document.getElementById('kdTangki').value=kodetangki;
    document.getElementById('oldTinggi').value=tinggicm;
    document.getElementById('oldkdTangki').value=kodetangki;
    document.getElementById('proses').value='updateData';
}
function masterPDF(table,column,cond,page,event) {
	// Prep Param
       
	param = "table="+table;
	param += "&column="+column;
	
	// Prep Condition
	param += "&cond="+cond;
	
	// Post to Slave
	if(page==null) {
		page = 'null';
	}
	if(page=='null') {
		page = "slave_master_pdf";
	}
	
	showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px' src='"+page+".php?proses=pdf&"+param+"'></iframe>",'800','400',event);
	var dialog = document.getElementById('dynamic1');
	dialog.style.top = '50px';
	dialog.style.left = '15%';
}