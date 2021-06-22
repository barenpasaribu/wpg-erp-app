/*
 * @uthor:anthesis-team@mitraagroservindo.com
 * Indonesia 2009
 */

 activeOrg='';
 orgVal   ='';
 clos 	  =1;//this will STOP on the #9th child
 function saveOrg()
 {
        _kdstruktur    = trim(document.getElementById('kdStruktur').value);
        _karyId = document.getElementById('karyId').options[document.getElementById('karyId').selectedIndex].value;
        _kdJbtn = document.getElementById('kdJbtn').options[document.getElementById('kdJbtn').selectedIndex].value;
        _maildt    = trim(document.getElementById('maildt').value);
        _detail 	= document.getElementById('detailDt').options[document.getElementById('detailDt').selectedIndex].value;
        _alokasi 	= document.getElementById('alokasi').options[document.getElementById('alokasi').selectedIndex].value;

//response++++++++++++++++++++++++++++++++++++++++
           function respog(){
                //save active org on memory incase slow server response
                        id         = activeOrg;
                        newCaption = _kdstruktur;
              if(con.readyState==4)
              {
                        if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                if (id == 'HQ') {
                                                //just reload when org is HQ
                                                window.location.reload();
                                                }
                                                else if(id.lastIndexOf('_new')>-1)
                                                {
                                                  if (clos<9) {
                                                        nex=clos+1;
                                                        ne = "<li class=mmgr>";
                                                        ne += "<img title=expand class=arrow src='images/foldc_.png' height=17px  onclick=show_sub('gr" + _karyId + "',this);>";
                                                        ne += "<a class=elink id='el" + _karyId + "'  onclick=\"javascript:activeOrg=this.id;orgVal='" + orgVal + "';getCurrent('" + _karyId + "');setpos('inputorg',event);\">" + _karyId + "</a>";
                                                        ne += "<ul id=gr" + _kdstruktur + " style='display:none;'>";
                                                        ne += "<div id=main" + _kdstruktur + ">";
                                                        ne += "</div>";
                                                        ne += "<li class=mmgr>";
                                                        ne += "<a id='" + _karyId + "_new' class=elink title='Create Child'  onclick=\"javascript:orgVal='" + _karyId + "';clos="+nex+";activeOrg='" + _karyId + "_new';setpos('inputorg',event);\">New Org<a>";
                                                        ne += "</li>";
                                                        ne += "</ul>";
                                                        ne += "</li>";
                                                  }
                                                  else
                                                  {
                                                        ne = "<li class=mmgr>";
                                                        ne += "<img title=expand class=arrow src='images/menu/arrow_8.gif'>";
                                                        ne += "<a class=elink id='el" + _karyId + "'  onclick=\"javascript:activeOrg=this.id;orgVal='" + orgVal + "';getCurrent('" + _karyId + "');setpos('inputorg',event);\">" + _karyId + "</a>";
                            ne += "</li>";					  	
                                                  }						
                          //alert('main'+orgVal);
                                                   document.getElementById('main'+orgVal).innerHTML+=ne;							
                                                }
                                                else {
                                                        document.getElementById(id).innerHTML = newCaption;
                                                        clearForm();
                                                }
                                          hideById('inputorg');
                                          clearForm();	
                                        }
                                }
                                else {busy_off();error_catch(con.status);}	
              }	
           }
//++++++++++++++++++++++++++++++++++++++++++++++++

        if(_kdstruktur.length==0)
        {
                alert('Org. Code and Org.Name is NULL');
        }		
        else
        {
                if(confirm('Saving, are you sure..?'))
                {
                        param ='parent='	+orgVal;
                        param+='&kdStruk='	+_kdstruktur;
                        param+='&karyId='	+_karyId;
                        param+='&kdJbtn='	+_kdJbtn;
                        param+='&mailDt='	+_maildt;
                        param+='&detail='	+_detail;
                        param+='&alokasi='+_alokasi;												
                  post_response_text('sdm_slave_orgchart.php', param, respog);
              //alert(param);
           }	
        }
 }

 function clearForm()
 {
        document.getElementById('kdStruktur').value ='';
        document.getElementById('karyId').value ='';
        document.getElementById('kdJbtn').value ='';
        document.getElementById('maildt').value  ='';
        document.getElementById('detailDt').value ='';
        document.getElementById('alokasi').options[0].selected =true;
        document.getElementById('kdStruktur').disabled=false;
 }


function getCurrent(code)
{
        param='code='+code;
        post_response_text('sdm_slaveGetCurrentOrgChart.php', param, respon);
   function respon(){
      if(con.readyState==4)
      {
                if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        if (con.responseText != '-1') {
                                                //alert(con.responseText);
                                                fillForm(con.responseText);
                                        }
                                        else 
                                                clearForm();	  
                                }
                        }
                        else {busy_off();error_catch(con.status);}	
      }	
   }	
  function  fillForm(arrtex)
  {
        arr=arrtex.split('|');
        document.getElementById('kdStruktur').value =arr[0];
        obj=document.getElementById('karyId');
        for(xY=0;xY<obj.length;xY++)
        {
                if(obj.options[xY].value==arr[1])
                {
                        obj.options[xY].selected=true;
                }
        }
        obj2=document.getElementById('kdJbtn');
        for(xY=0;xY<obj2.length;xY++)
        {
                if(obj2.options[xY].value==arr[2])
                {
                        obj2.options[xY].selected=true;
                }
        }
        document.getElementById('maildt').value =arr[3];
        ctobj=document.getElementById('detailDt');
        ct=ctobj.length;
        for (x = 0; x < ct; x++) {
                if (ctobj.options[x].value == arr[4]) //check if country code is match with option value, then select it
             ctobj.options[x].selected=true;
        }
        curr=0;

        alobj=document.getElementById('alokasi');
        al=alobj.length;
        for (x = 0; x < al; x++) {
                if (alobj.options[x].value == arr[5]) 
             alobj.options[x].selected=true;
        }	
        document.getElementById('kdStruktur').disabled=true;	
  } 	
}

function setpos(id,e)
{
        pos=getMouseP(e);
        document.getElementById(id).style.top=pos[1]+'px';
        document.getElementById(id).style.left=pos[0]+'px';
        document.getElementById(id).style.display='';	
}
