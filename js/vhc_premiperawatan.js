//1625
function getData(){
    kodeorg=document.getElementById('kodeorg').value;
    periode=document.getElementById('periode');
    periode=periode.options[periode.selectedIndex].value;
    kdprm=document.getElementById('kdpremi');
    kdprm=kdprm.options[kdprm.selectedIndex].value;
  
    param='kodeorg='+kodeorg+'&periode='+periode+'&kdpremi='+kdprm+'&proses=preview';
    tujuan='vhc_slave_premiperawatan.php';
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
                      }
                    }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }	
    }  
}
function saveAll(jmlhRw){
     
    strUrl='';
    for(derd=1;derd<=jmlhRw;derd++){
        
           try{
             if(strUrl!=''){
                 strUrl+='&karyId['+derd+']='+document.getElementById('karyId_'+derd).value
                        +'&premiDt['+derd+']='+document.getElementById('premiDt_'+derd).value;
             }else{
                 strUrl+='&karyId['+derd+']='+document.getElementById('karyId_'+derd).value
                        +'&premiDt['+derd+']='+document.getElementById('premiDt_'+derd).value;
             }
           }
         catch(e){}
     }
    kodeorg=document.getElementById('kodeorg').value;
    periode=document.getElementById('periode');
    periode=periode.options[periode.selectedIndex].value;
    param='kodeorg='+kodeorg+'&periode='+periode+'&proses=saveAll'+'&jmlhRow='+jmlhRw;
    param+=strUrl;
    tujuan='vhc_slave_premiperawatan.php';
    if(confirm("Are you sure, this process need some time")){
        post_response_text(tujuan, param, respog);
    }
    
    function respog(){
      if(con.readyState==4)
      {
            if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                    }
                    else {
                           document.getElementById('container').innerHTML="Finish, Please confirm your work in Tab List";
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

function loadData(){
    param='proses=loadData';
    tujuan='vhc_slave_premiperawatan.php';
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

function getExcel(ev,tujuan,jbtn){
        kodeorg=document.getElementById('kodeorg').value;
        periode=document.getElementById('periode');
        periode=periode.options[periode.selectedIndex].value;
    width='250';
    height='180';
    content="<iframe frameborder=0 width=100% height=280 src='"+tujuan+"?proses=excel&kodeorg="+kodeorg+"&periode="+periode+"&jabratan="+jbtn+"'></iframe>"
    showDialog1('Excel ',content,width,height,ev); 
}
function delData(kdorg,prd){
    param='kodeorg='+kdorg+'&periode='+prd;
    param+='&proses=delData';
    tujuan='vhc_slave_premiperawatan.php';
    if(confirm("Are you sure delete this all data")){
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