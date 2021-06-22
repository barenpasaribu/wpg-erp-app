// JavaScript Document
function getKdvhc(kdtrak,kdvh)
{
    if((kdtrak==0)||(kdvh==0))
    {
        kdTraksi=document.getElementById('kdTraksi').options[document.getElementById('kdTraksi').selectedIndex].value;
        param='kdTraksi='+kdTraksi+'&proses=getKdVhc';
    }
    else
    {
           kdTraksi= kdtrak;
           kodevhc=kdvh;
           param='kdTraksi='+kdTraksi+'&proses=getKdVhc';
           param+='&kdVhc='+kodevhc;
    }
       // alert(param);
        tujuan='budget_slave_total_jam_vhc.php';
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
                                                        document.getElementById('kdVhc').innerHTML=con.responseText;
                                                        if(kdtrak!=''||kdvh!='')
                                                        {
                                                            getData();    
                                                        }
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	
}
function saveHead()
{
    thnBdget=document.getElementById('thnBudget').value;
    totJamThn=document.getElementById('totJamThn').value;
    kdTraksi=document.getElementById('kdTraksi').options[document.getElementById('kdTraksi').selectedIndex].value;
    kdVhc=document.getElementById('kdVhc').options[document.getElementById('kdVhc').selectedIndex].value;
    kdUnit=document.getElementById('kdUnit').options[document.getElementById('kdUnit').selectedIndex].value;
    if(thnBdget==''||totJamThn==''||kdTraksi==''||kdVhc==''||kdUnit=='')
    {
        alert("Fields are required");
        return;
    }

    if(thnBdget.length<4) 
    {
        alert("Budget year incorrect");
        return;
    }
        param='kdTraksi='+kdTraksi+'&proses=cekHead'+'&thnBudget='+thnBdget+'&totJamThn='+totJamThn+'&kdVhc='+kdVhc+'&kdUnit='+kdUnit;

        tujuan='budget_slave_total_jam_vhc.php';
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
                                                        //document.getElementById('kdVhc').innerHTML=con.responseText;
                                                        document.getElementById('thnBudget').disabled=true;
                                                        document.getElementById('totJamThn').disabled=true;
                                                        document.getElementById('kdTraksi').disabled=true;
                                                        document.getElementById('kdVhc').disabled=true;
                                                        document.getElementById('kdUnit').disabled=true;
                                                        document.getElementById('saveDt').disabled=true;
                                                        document.getElementById('printContainer').style.display='block';
                                                        b=1;
                                                        //ar=con.responseText.split("###");
                                                        for(a=0;a<=11;a++)
                                                        {
                                                            document.getElementById('jam_x'+b).disabled=true;
                                                             document.getElementById('jam_x'+b).value=con.responseText; 
                                                             document.getElementById('jam_x'+b).disabled=false;
                                                             b++;
                                                        }
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 

}
function getData()
{
    document.getElementById('printContainer').style.display='block';
    thnBudget=document.getElementById('thnBudget').value;
    kdTraksi=document.getElementById('kdTraksi').options[document.getElementById('kdTraksi').selectedIndex].value;
    kdVhc=document.getElementById('kdVhc').options[document.getElementById('kdVhc').selectedIndex].value;
    kdUnit=document.getElementById('kdUnit').options[document.getElementById('kdUnit').selectedIndex].value;
    param='kdTraksi='+kdTraksi+'&proses=getDataEdit'+'&thnBudget='+thnBudget+'&kdVhc='+kdVhc+'&kdUnit='+kdUnit;
    tujuan='budget_slave_total_jam_vhc.php';
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
                                                        b=1;
                                                        ar=con.responseText.split("###");
                                                        for(a=0;a<=11;a++)
                                                        {
                                                            document.getElementById('jam_x'+b).disabled=true;
                                                             document.getElementById('jam_x'+b).value=ar[a]; 
                                                             document.getElementById('jam_x'+b).disabled=false;
                                                             b++;
                                                        }


                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	

}
function saveHead2()
{
    thnBudget=document.getElementById('thnBudget').value;
    totJamThn=document.getElementById('totJamThn').value;
    kdTraksi=document.getElementById('kdTraksi').options[document.getElementById('kdTraksi').selectedIndex].value;
    kdVhc=document.getElementById('kdVhc').options[document.getElementById('kdVhc').selectedIndex].value;
    kdUnit=document.getElementById('kdUnit').options[document.getElementById('kdUnit').selectedIndex].value;
    pros=document.getElementById('proses').value;
    param='kdTraksi='+kdTraksi+'&proses='+pros+'&thnBudget='+thnBudget+'&totJamThn='+totJamThn+'&kdVhc='+kdVhc+'&kdUnit='+kdUnit;
    tujuan='budget_slave_total_jam_vhc.php';
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
                                                        batal();

                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	

}
function saveJam(totRow)
{
        strUrl = '';
        thnBudget=document.getElementById('thnBudget').value;
        totJamThn=document.getElementById('totJamThn').value;
        kdTraksi=document.getElementById('kdTraksi').options[document.getElementById('kdTraksi').selectedIndex].value;
        kdVhc=document.getElementById('kdVhc').options[document.getElementById('kdVhc').selectedIndex].value;
        kdUnit=document.getElementById('kdUnit').options[document.getElementById('kdUnit').selectedIndex].value;
        pros=document.getElementById('proses').value;
        for(i=1;i<=totRow;i++)
        {
            try{
                if(strUrl != '')
                {
                        strUrl += '&arrJam['+i+']='+document.getElementById('jam_x'+i).value;
                }
                else
                {
                     strUrl += '&arrJam['+i+']='+document.getElementById('jam_x'+i).value;
                }
            }
            catch(e){}
        }
        param='kdTraksi='+kdTraksi+'&proses='+pros+'&thnBudget='+thnBudget+'&totJamThn='+totJamThn+'&kdVhc='+kdVhc+'&totRow='+totRow;
        param+='&kdUnit='+kdUnit;
        if(strUrl!='')
        {    
            param+=strUrl;
        }
        //alert(param);
        tujuan='budget_slave_total_jam_vhc.php';
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
                                                        batal();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	

}
function loadData()
{
        param='proses=loadData';
        thnBudget=document.getElementById('thndBudgetHead').options[document.getElementById('thndBudgetHead').selectedIndex].value;
        kdVhc=document.getElementById('kdVhcHead').options[document.getElementById('kdVhcHead').selectedIndex].value;
        kdUnit=document.getElementById('kdUnit').options[document.getElementById('kdUnit').selectedIndex].value;
        if(thnBudget!='')
        {
            param+='&thnBudget='+thnBudget;
        }
        if(kdVhc!='')
        {
            param+='&kdVhc='+kdVhc;
        }
        if(kdUnit!='')
        {
            param+='&kdUnit='+kdUnit;
        }
        //alert(param);
        tujuan='budget_slave_total_jam_vhc.php';
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

function deleteData(tahunbudget,kodevhc,unitalokasi,kodetraksi)
{

        param='kdTraksi='+kodetraksi+'&proses=deleteData'+'&thnBudget='+tahunbudget+'&kdUnit='+unitalokasi+'&kdVhc='+kodevhc;
        tujuan='budget_slave_total_jam_vhc.php';
        if(confirm("Delete, are you sure ?"))
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
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
              }	
         } 
}
function fillField(tahunbudget,kodevhc,unitalokasi,kodetraksi,jumlahjam)
{
    getKdvhc(kodetraksi,kodevhc);
    document.getElementById('thnBudget').disabled=true;
    document.getElementById('tmblSave').disabled=true;
    document.getElementById('kdTraksi').disabled=true;
    document.getElementById('kdVhc').disabled=true;
    document.getElementById('kdUnit').disabled=true;
    document.getElementById('thnBudget').value=tahunbudget;
    document.getElementById('totJamThn').value=jumlahjam;
    document.getElementById('kdTraksi').value=kodetraksi;
    document.getElementById('kdUnit').value=unitalokasi;
    document.getElementById('proses').value='update';
    document.getElementById('saveDt').disabled=true;
    document.getElementById('totJamThn').disabled=false;
}
function cariBast(num)
{
                param='proses=loadData';
                param+='&page='+num;
                tujuan = 'budget_slave_total_jam_vhc.php';
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
function batal()
{
         document.getElementById('proses').value='saveData';
    document.getElementById('kdVhc').innerHTML="<option value=''>"+pilih+"</option>";
    document.getElementById('kdUnit').value='';
    document.getElementById('totJamThn').value='';
    //document.getElementById('thnBudget').value='';
    document.getElementById('kdTraksi').value='';
    document.getElementById('thnBudget').disabled=false;
    document.getElementById('totJamThn').disabled=false;
    document.getElementById('kdTraksi').disabled=false;
    document.getElementById('kdVhc').disabled=false;
    document.getElementById('kdUnit').disabled=false;
    document.getElementById('printContainer').style.display='none';
    document.getElementById('tmblSave').innerHTML="";
    document.getElementById('tmblSave').innerHTML="<button onclick='saveHead()' class='mybutton' name='saveDt' id='saveDt'>"+save+"</button>&nbsp;<button onclick='batal()' class='mybutton' name='btl' id='btl'>"+btl+"</button>";  
    for(q=1;1<13;q++)
    {
        document.getElementById('jam_x'+q).value='';
    }
}



function dataKeExcel(ev,tujuan)
{
        kdBrg		=document.getElementById('kdBrg').value;
        kdPbrk  =document.getElementById('kdPbrk').value;
        tgl =document.getElementById('tglTrans').value;

        //gudang	=gudang.options[gudang.selectedIndex].value;
        judul='Report Ms.Excel';	
        param='kdBrg='+kdBrg+'&kdPbrk='+kdPbrk+'&tgl='+tgl;
        //alert(param);
        printFile(param,tujuan,judul,ev)	
}
function dataKePDF(ev)
{
        kdBrg	=document.getElementById('kdBrg').value;
        kdPbrk  =document.getElementById('kdPbrk').value;
        tgl =document.getElementById('tglTrans').value;

        tujuan='pabrik_slaveLaporanTimbanganPdf.php';
        judul='Report PDF';		
        param='kdBrg='+kdBrg+'&kdPbrk='+kdPbrk+'&tgl='+tgl;
        //alert(param);
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
