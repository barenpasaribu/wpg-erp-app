//JS 

function simpanbjr()
{
        tahunbudget=document.getElementById('tahunbudget').value;
        kodeorg=document.getElementById('kodeorg');
        kodeorg=kodeorg.options[kodeorg.selectedIndex].value;
        thntanam=document.getElementById('thntanam').value;
        bjr=document.getElementById('bjr').value;
        met=document.getElementById('method').value;
        oldtahunbudget=document.getElementById('oldtahunbudget').value;
        oldkodeorg=document.getElementById('oldkodeorg').value;
        oldthntanam=document.getElementById('oldthntanam').value;

        if(trim(tahunbudget)=='')
        {
                alert('Budget year required');
                document.getElementById('tahunbudget').focus();
        }
        else if(tahunbudget.length<4) 
    {
        alert('Budget year incorrect');
        return;
    }
        else if(trim(kodeorg)=='')
        {
                alert('Org code required');
                document.getElementById('kodeorg').focus();
        }
        else if(trim(thntanam)=='')
        {
                alert('Planting year required');
                document.getElementById('thntanam').focus();
        }
        else if(trim(bjr)=='')
        {
                alert('FFB avg weight required');
                document.getElementById('bjr').focus();
        }
        else
        {
                tahunbudget=trim(tahunbudget);
                kodeorg=trim(kodeorg);
                thntanam=trim(thntanam);
                bjr=trim(bjr);

        param='tahunbudget='+tahunbudget+'&kodeorg='+kodeorg+'&thntanam='+thntanam+'&bjr='+bjr+'&method='+met;
        param+='&oldtahunbudget='+oldtahunbudget+'&oldkodeorg='+oldkodeorg+'&oldthntanam='+oldthntanam;
        //alert(param);

        tujuan='setup_slave_save_anggaran_bjr.php';
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
                                                        cancelbjr();							
                            loadData();
                                                        //document.getElementById('container').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }
}

function fillField(thn,kode,thntnm,bj)
{
        document.getElementById('tahunbudget').value=thn;
        document.getElementById('oldtahunbudget').value=thn;
        document.getElementById('kodeorg').value=kode;
        document.getElementById('oldkodeorg').value=kode;
        document.getElementById('bjr').value=bj;
        document.getElementById('thntanam').value=thntnm;
        document.getElementById('oldthntanam').value=thntnm;
}

function cancelbjr()
{
        document.getElementById('tahunbudget').value='';
        document.getElementById('kodeorg').value='';
        document.getElementById('thntanam').value='';
        document.getElementById('bjr').value='';	
        document.getElementById('method').value='insert';	
}

function angka (b,ainput)
{
        var goodInput = ainput;
        var evt = (b)?b:window.event;
        var key_code = (document.all)?evt.keyCode:evt.which;
        if (key_code == 0 || key_code == 8) return true;
        if (goodInput.indexOf(String.fromCharCode(key_code)) == -1)
        {
                return false;
        }
        else
        return true;
} 


function closebjr()
{

    thnttp=document.getElementById('thnttp').options[document.getElementById('thnttp').selectedIndex].value;
        lkstgs=document.getElementById('lkstgs').options[document.getElementById('lkstgs').selectedIndex].value;
        if(trim(thnttp)=='')
        {
                alert('Budget year required');
                return;
        }
        if(trim(lkstgs)=='')
        {
                alert('Org code required');
                return;
        }
        param='thnttp='+thnttp+'&lkstgs='+lkstgs+'&method=closebjr';
        //alert(param);
    tujuan='setup_slave_save_anggaran_bjr.php';
    if(confirm("Close data for "+lkstgs+" period "+thnttp+" ?"))
    post_response_text(tujuan, param, respog);
        function respog()
        {
                      if(con.readyState==4)
                      {
                                if (con.status == 200) 
                                        {
                                                busy_off();
                                                if (!isSaveResponse(con.responseText)) 
                                                {
                                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                                }
                                                else 
                                                {
                                                        loadData();
                                                        cancelbjr();
                                                }
                                        }
                                        else 
                                        {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  
}	

function getThn()
{
        param='method=getThn';	
        //alert(param);
        tujuan='setup_slave_save_anggaran_bjr.php';
    post_response_text(tujuan, param, respog);

        function respog()
        {
                  if(con.readyState==4)
                  {
                                if (con.status == 200)
                                {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) 
                                        {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else 
                                        {
                                                document.getElementById('thnttp').innerHTML=con.responseText;
                                        }
                                }
                                else 
                                {
                                        busy_off();
                                        error_catch(con.status);
                                }
                  }	
        } 	
}

function getOrg()
{
        param='method=getOrg';	
        //alert(param);
        tujuan='setup_slave_save_anggaran_bjr.php';
    post_response_text(tujuan, param, respog);

        function respog()
        {
                  if(con.readyState==4)
                  {
                                if (con.status == 200)
                                {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) 
                                        {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else 
                                        {
                                                document.getElementById('lkstgs').innerHTML=con.responseText;
                                                getThn();
                                        }
                                }
                                else 
                                {
                                        busy_off();
                                        error_catch(con.status);
                                }
                  }	
        } 	
}

function loadData () 
{
        param='method=loadData';
        tujuan='setup_slave_save_anggaran_bjr.php';
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
                                    document.getElementById('containerData').innerHTML=con.responseText;
                                    getOrg();
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
         }  
}
