loadUserLevelSetting/**
 * @author {anthesis-team@mitraagroservindo.com}
*/

function showObject(obj)
{
       for(x=0;x<=max_id;x++)
       {
               vx='inputmenu'+x;
               vy='link'+x;
               vz='edit'+x;
               try{ //try onebyone
                       document.getElementById(vx).style.display='none';
               }catch(e){}
               try{ //try onebyone	
                       document.getElementById(vz).innerHTML='';		 	
               }
               catch(e){}
               try{ //try onebyone
                       if(obj!=document.getElementById(vx))
                         document.getElementById(vy).style.display='';
                       else
                         document.getElementById(vy).style.display='none';
               }
               catch(e){}	
       }
       obj.style.display='';
}

function show_sub(id,obj)//used in menu settings
{
       if (document.getElementById(id).style.display == 'none') {
               document.getElementById(id).style.display = '';
               obj.src='images/foldo.png';
               obj.setAttribute('title','Collaps');
       }
       else {
               document.getElementById(id).style.display = 'none';
               obj.src='images/foldc.png';
               obj.setAttribute('title','Expand');
       }

}

function showById(objtohide,objtoshow)//used in menu settings
{
               document.getElementById(objtoshow).style.display = '';
               document.getElementById(objtohide).style.display = 'none';
}

function inputText(val,obj)
{
       if(val=='Caption...' || val=='Action...')
       {
               obj.value='';
       }
}

function leaveText(val,obj)
{
       if(val=='' || val=='')
       {
               if(obj.id.lastIndexOf('Caption')>-1)
               {
                       obj.value='Caption...';
               }
               else
               {	
                   obj.value='Action...';	
               }
       }
}

function saveMenu(parent,caption,caption2,caption3,action,showlink,hideinput,type,newfile)
{
       objToHide=document.getElementById(showlink);
       objToShow=document.getElementById(hideinput);
       objType=document.getElementById(type);
       id_parent=document.getElementById(parent).value;
       _caption=document.getElementById(caption).value;
       _caption2=document.getElementById(caption2).value;
       _caption3=document.getElementById(caption3).value;
       _action=document.getElementById(action).value;
       clas=document.getElementById(type).options[document.getElementById(type).selectedIndex].text;
       createFile=document.getElementById(newfile).checked?'yes':'no';
       if(clas=='devider')
         _caption='---------';
     
     if(_caption2=='')
         _caption2='unspesified';
     if(_caption3=='')
         _caption3='unspesified';

    
       param='id_parent='+id_parent+'&caption='+_caption+'&action='+_action+'&class='+clas+'&create='+createFile+'&caption2='+_caption2+'&caption3='+_caption3;
       //alert(param);
       if (clas == 'Type...') {
          alert('Choose type..!');
          objType.focus();
       }
       else if (clas == 'click' && (_caption=='Caption...' || _action=='Action...')) {
          alert('Fill the title and/or action');
       }
       else if (clas == 'title' && _caption=='Caption...') {
          alert('Fill the title and/or action');
       }	
       else {
               if(confirm('Are you sure ...'))
               post_response_text('slave_saveNewMenu.php', param, respog);
       }
       function respog(){
            if(con.readyState==4)
            {
               if(con.status==200)
               {
                  busy_off();
                          if (!isSaveResponse(con.responseText)) {
                               alert('ERROR TRANSACTION,\n'+con.responseText);
                                       hideObject(objToShow);
                                       //objToHide.style.display='';
                                       showObject(objToHide);
                                       //objToShow.style.display='none';
                          }	
                          if (con.responseText.lastIndexOf('Warning') > -1) {
                               alert('ERROR TRANSACTION,\n'+con.responseText);
                                       showObject(objToHide);
                                       showObject(objToShow);
                                       //objToHide.style.display='none';
                                       //objToShow.style.display='none';
                          }   
                          else {
                                   arr=con.responseText.split(',');

                                       _id=parseInt(arr[0]);
                                       _ischildable=arr[1];
                                       max_id=_id;					
                                       _in=document.getElementById('group'+id_parent).innerHTML;

                                       if(_ischildable=='stop')
                                       {
                                       _in+="<li>";
                                               if (clas == 'title' || clas=='devider') {
                                                       //_in+="<img  src='images/menu/symbol_4.gif'> ";
                                                       _in+="<a class=lab id=lab"+_id+" onclick=edit('"+_id+"') title='Click to Change'>"+_caption+"</a><a id=edit"+_id+"></a>";	
                                                       }
                                               else {					
                                                       //_in+="<img  src='images/menu/symbol_4.gif' class=arrow> ";
                                                       _in+="<a class=lab id=lab"+_id+" onclick=edit('"+_id+"') title='Click to Change'>"+_caption+"</a><a id=edit"+_id+"></a>";
                                            }					 
                                               _in+="<input class=cbox type=checkbox id=check"+_id+" onclick=\"activate('"+_id+"');\" title='Click to Display!'>";
                                               _in+="&nbsp &nbsp <img class=dellicon title='Delete!' src='images/menu/delete1.jpg' onclick=\"delet('"+_id+"');\" id=img"+_id+">";						
                                       }
                                       else
                                       {
                                       _in+="<li class=mmgr>";
                                       if (clas == 'title' || clas=='devider') {
                                               _in+="<img  src='images/menu/arrow_10.gif'> ";
                                               _in+="<a class=lab id=lab"+_id+" onclick=edit('"+_id+"') title='Click to Change'>"+_caption+"</a><a id=edit"+_id+"></a>";	
                                               }
                                       else {					
                                               _in+="<img  src='images/foldc_.png' onclick=show_sub('child"+_id+"',this); class=arrow title='Expand' height=17px> ";
                                               _in+="<a class=lab id=lab"+_id+" onclick=edit('"+_id+"') title='Click to Change'>"+_caption+"</a><a id=edit"+_id+"></a>";
                                        }					 
                                       _in+="<input class=cbox type=checkbox id=check"+_id+" onclick=\"activate('"+_id+"');\" title='Click to Display!'>";
                                       _in+="&nbsp &nbsp <img class=dellicon title='Delete!' src='images/menu/delete1.jpg' onclick=\"delet('"+_id+"');\" id=img"+_id+">";
                                       _in += "<ul id=child" + _id + " style='display:none;'><div id=group" + _id + "></div>";
                                       _in += "<li><div id=inputmenu" + _id + " class=menuinput  style='display:none;'>";
                                       _in += "<select id=type" + _id + " onchange=checkType('" + _id + "',this)>";
                                       _in += "<option>Type...</option><option>click</option><option>title</option><option>devider</option>";
                                       _in += "</select>";
                                       _in += "<input type=text value='Caption...' maxlength=40 class=myinputtext title='Text to be shown on menu' id=newCaption" + _id + " size=12 onkeypress=\"return tanpa_kutip(event);\" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>";
                                       _in += "<input type=text value='Caption...' maxlength=40 class=myinputtext title='Text to be shown on menu' id=newCaption2" + _id + "x size=12 onkeypress=\"return tanpa_kutip(event);\" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>";
                                       _in += "<input type=text value='Caption...' maxlength=40 class=myinputtext title='Text to be shown on menu' id=newCaption3" + _id + "x size=12 onkeypress=\"return tanpa_kutip(event);\" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>";
                                       _in += "<input type=text value='Action...' maxlength=40 class=myinputtext title='Filename (without extension) that will be execute when menu clicked' id=newAction" + _id + " size=12 onkeypress=\"return tanpa_kutip(event);\" onfocus=inputText(this.value,this) onblur=leaveText(this.value,this)>";
                                       _in += "<input type=checkbox  title='Check to create this file' id=newFile" + _id +"><font color=white>Create File</font>";
                                       _in += "<input type=hidden id=master_menu" + _id + " value=" + _id + ">";
                                       //_in += "<center>";
                                       _in += "<input type=button class=mybutton value=Save onclick=saveMenu('master_menu" + _id + "','newCaption" + _id + "','newCaption2" + _id + "x','newCaption3" + _id + "x','newAction" + _id + "','link" + _id + "','inputmenu" + _id + "','type" + _id + "','newFile"+ _id +"');>";
                                       _in += "<input type=button class=mybutton value=Close onclick=showById('inputmenu" + _id + "','link" + _id + "')>";
                                       //_in += "</center>";
                                       _in += "</div>";
                                       _in += "<a class=newMenu title='Create New Link' id=link" + _id + " onclick=\"javascript:hideObject(this);showObject(document.getElementById('inputmenu" + _id + "'));\">New</a></li>";
                                       _in += "</ul>";
                   }
                                       //alert(_in);
                                       document.getElementById('group'+id_parent).innerHTML=_in;
                                   document.getElementById(caption).value='Caption...';
                                   document.getElementById(action).value='Action...';					
                                       objToHide.style.display='';
                                       objToShow.style.display='none';
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

function checkType(v,obj)
{
       type=obj.options[obj.selectedIndex].text;
       document.getElementById('newCaption'+v).disabled=false;
       document.getElementById('newAction'+v).disabled=false;
       document.getElementById('newFile'+v).disabled=false;	

       if(type=='devider')
       {
               document.getElementById('newCaption'+v).disabled=true;
               document.getElementById('newAction'+v).disabled=true;
               document.getElementById('newFile'+v).checked=false;
               document.getElementById('newFile'+v).disabled=true;
       }
       else if(type=='title')
       {
               document.getElementById('newAction'+v).disabled=true;
               document.getElementById('newFile'+v).checked=false;
               document.getElementById('newFile'+v).disabled=true;		
       }
}

function activate(menu_id)
{
       //alert(menu_id);
       obj=document.getElementById('check'+menu_id);
       if(obj.checked)
       {
               param='setHide=0&id='+menu_id;
       }
       else
       {
          param='setHide=1&id='+menu_id;	
       }
       ////change the tex backgroud 
               document.getElementById('lab'+menu_id).style.backgroundColor='#E36707';
       ////post request
               //alert(param);
               post_response_text('slave_activateMenu.php', param, respog);

       function respog(){
             if(con.readyState==4)
             {
                       if (con.status == 200) {
                                       busy_off();
                                       if (!isSaveResponse(con.responseText)) {
                                               if (obj.checked) {
                                                       obj.checked = false;
                                                       obj.setAttribute('title', 'Click to Activate');
                                               }
                                               else {
                                                       obj.checked = true;
                                                       obj.setAttribute('title', 'Click to deActivate');
                                               }
                                               alert('ERROR TRANSACTION,\n' + con.responseText);
                                       }
                                       else {
                                               if (obj.checked) 
                                                       obj.setAttribute('title', 'Click to deActivate');
                                               else 
                                                       obj.setAttribute('title', 'Click to Activate');
                                       }
                               }
                               else {
                                       busy_off();
                                       error_catch(con.status);
                               }
                       //set back the tex backgroud
                       document.getElementById('lab'+menu_id).style.backgroundColor='#FFFFFF';	
             }	
       }	
}

function delet(m_id){
       obj=document.getElementById(m_id);
       document.getElementById('lab'+m_id).style.backgroundColor='#E36707';

       param='id='+m_id;
       if(confirm('Are you sure deleting this menu....?'))
         post_response_text('slave_deleteMenu.php', param, respog);
       else
         document.getElementById('lab'+m_id).style.backgroundColor='#FFFFFF';

       function respog(){
             if(con.readyState==4)
             {
                       if (con.status == 200) {
                                       busy_off();
                                       if (!isSaveResponse(con.responseText)) {
                                               alert('ERROR TRANSACTION,\n' + con.responseText);
                                       }
                                       else {
                                               new_item=parseInt(m_id);
                                               clearFormEdit('edit'+m_id);
                                               document.getElementById('lab'+m_id).innerHTML='<i style=\'background-color:#FF0000;\'>deleted</i>';
                                               document.getElementById('img'+m_id).style.display='none';
                                               document.getElementById('check'+m_id).style.display='none';
                                       try {
                                               document.getElementById('inputmenu' + m_id).style.display = 'none';
                                          }
                                       catch(e){}//do nothing on eror  	
                                       try {
                                               document.getElementById('link' + new_item).innerHTML = 'Closed';
                                          }
                                       catch(e){}//do nothing on eror
                                       try {
                                               document.getElementById('link' + new_item).setAttribute('onclick', 'alert(\'This link has been closed\');');
                                           }
                                       catch(e){}//do nothing on eror
                                       try{
                                           document.getElementById('link' + new_item).setAttribute('title', 'This link has been closed');
                                               }
                                               catch(e){}//do nothing on eror
                                       }
                               }
                               else {
                                       busy_off();
                                       error_catch(con.status);
                               }
                       //set back the tex backgroud
                       document.getElementById('lab'+m_id).style.backgroundColor='#FFFFFF';	
             }	
       }		
}

function edit(_id)
{
       param='id='+_id;
       document.getElementById('lab'+_id).style.backgroundColor='#E36707';
   ////post request
       post_response_text('slave_getMenuForEdit.php', param, respog);

       //hide all form but this
       showObject(document.getElementById('edit'+_id));	
       function respog(){
             if(con.readyState==4)
             {
                       if (con.status == 200) {
                                       busy_off();
                                       if (!isSaveResponse(con.responseText)) {
                                               alert('ERROR TRANSACTION,\n' + con.responseText);
                                       }
                                       else {
                                               //alert('edit'+_id);
                                               document.getElementById('edit'+_id).innerHTML=con.responseText;
                                       }
                               }
                               else {
                                       busy_off();
                                       error_catch(con.status);
                               }
                       //set back the tex backgroud
                       document.getElementById('lab'+_id).style.backgroundColor='#FFFFFF';	
             }	
       }		
}

function clearFormEdit(objid)
{
       document.getElementById(objid).innerHTML='';
}

function saveEditedMenu(id)
{
       newCaption=document.getElementById('editcaption'+id).value;
       newCaption2=document.getElementById('editcaption2'+id+'x').value;
       newCaption3=document.getElementById('editcaption3'+id+'x').value;
       newAction=document.getElementById('editaction'+id).value;
       param='id='+id+'&caption='+newCaption+'&action='+newAction+'&caption2='+newCaption2+'&caption3='+newCaption3;
       if(document.getElementById('editaction'+id).disabled)
         newAction='';
       if (confirm('Are you sure changing the menu?')) {
               post_response_text('slave_saveEditedMenu.php', param, respog);
           document.getElementById('lab'+id).style.backgroundColor='#E36707';
       }
       else 
               clearFormEdit('edit' + id);	

       function respog(){
             if(con.readyState==4)
             {
                       if (con.status == 200) {
                                       busy_off();
                                       if (!isSaveResponse(con.responseText)) {
                                               alert('ERROR TRANSACTION,\n' + con.responseText);
                                       }
                                       else {
                                               document.getElementById('lab'+id).innerHTML=newCaption;
                                           clearFormEdit('edit'+id);
                                       }
                               }
                               else {
                                       busy_off();
                                       error_catch(con.status);
                               }
                       //set back the tex backgroud
                       document.getElementById('lab'+id).style.backgroundColor='#FFFFFF';	
             }	
       }	
}

function collapsAll()
{
 for(x=0;x<=max_id;x++)
 {
       try{
               document.getElementById('child'+x).style.display='none';
       }
       catch(e){}
 }	
}
function expandAll()
{
 for(x=0;x<=max_id;x++)
 {
       try{
               document.getElementById('child'+x).style.display='';
       }
       catch(e){}
 }		
}

function showMenuOrder()
{
       ctrl=document.getElementById('optionController');
       if (ctrl.innerHTML == 'Order Arrangement') {
               collapsAll();
               ctrl.innerHTML = 'Menu Settings';
               ctrl.setAttribute('title','Click to manage menu settings');
               document.getElementById('menuSettingContainer').style.display = 'none';
           document.getElementById('menuOrderContainer').style.display='';	
       }
       else {
               collapsAllOrder();
               ctrl.innerHTML = 'Order Arrangement';
               ctrl.setAttribute('title','Click to manage menu order');
       document.getElementById('menuOrderContainer').style.display='none';	
               document.getElementById('menuSettingContainer').style.display = '';
       }

}

function collapsAllOrder()
{
 for(x=0;x<=max_id;x++)
 {
       try{
               document.getElementById('orderchild'+x).style.display='none';
       }
       catch(e){}
 }	
}
function expandAllOrder()
{
 for(x=0;x<=max_id;x++)
 {
       try{
               document.getElementById('orderchild'+x).style.display='';
       }
       catch(e){}
 }		
}

function showEditor(id,sub,e)
{
  document.getElementById('ordereditorcontent').innerHTML='';
       pos= new Array();
       pos=getMouseP(e);
       //alert(pos[0]+','+pos[1]);
//*********************
// 	Get id submenu 
   param='parent='+id+'&sub='+sub;
       post_response_text('slave_getMenuOrder.php', param, respog);
       document.getElementById('orderlab'+id).style.backgroundColor='#E36707';
   function respog(){
             if(con.readyState==4)
             {
                       if (con.status == 200) {
                                       busy_off();
                                       if (!isSaveResponse(con.responseText)) {
                                               alert('ERROR TRANSACTION,\n' + con.responseText);
                                       }
                                       else {
                                               //*********************
                                               //Displays order editor
                                               document.getElementById('ordereditor').style.top=pos[1]+'px';
                                               document.getElementById('ordereditor').style.left=pos[0]+'px';
                                               document.getElementById('ordereditor').style.display='';
                                               document.getElementById('ordereditorcontent').innerHTML=con.responseText;
                                       }
                               }
                               else {
                                       busy_off();
                                       error_catch(con.status);
                               }
                       //set back the tex backgroud
                       document.getElementById('orderlab'+id).style.backgroundColor='#FFFFFF';	
             }	
         }	
//**********************	
}


function closeOrderEditor()
{
 document.getElementById('ordereditorcontent').innerHTML='';
 document.getElementById('ordereditor').style.display='none';  	
}

function change(dest,x,mx)
{
 x=parseInt(x);
 mx=parseInt(mx);
 if(dest=='up' && (x-1)==0)
       alert('It is on top');
 else if(dest=='down' && (x-1)>mx)
       alert('It is at te bottom');
 else
 {
       if(dest=='up')
               y=x-1;
       else
           y=x+1;	

   ox=document.getElementById('orderurut'+x).innerHTML;
       oy=document.getElementById('orderurut'+y).innerHTML;
       fromId=document.getElementById('orderid'+x).innerHTML;
       toId=document.getElementById('orderid'+y).innerHTML;

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
                                                 //if success, then change display
                                                 cangeOrderDisplay();	
                                               }
                                       }
                                       else {
                                               busy_off();
                                               error_catch(con.status);
                                       }
                     }	
        }	

       param='from='+fromId+'&to='+toId+'&orderfrom='+ox+'&orderto='+oy;
       post_response_text('slave_changeMenuOrder.php', param, respog);



  function cangeOrderDisplay()
   {
       xid=document.getElementById('orderid'+x);
       xtype=document.getElementById('ordertype'+x);
       xcaption=document.getElementById('ordercaption'+x);
       xaction=document.getElementById('orderaction'+x);
       xurut=document.getElementById('orderurut'+x);

       yid=document.getElementById('orderid'+y);
       ytype=document.getElementById('ordertype'+y);
       ycaption=document.getElementById('ordercaption'+y);
       yaction=document.getElementById('orderaction'+y);
       yurut=document.getElementById('orderurut'+y);

         //penampungan
         xoid=xid.innerHTML;
         xotype=xtype.innerHTML;
         xocaption=xcaption.innerHTML;
         xoaction=xaction.innerHTML;
         xourut=xurut.innerHTML; 

         //replace//change positon
         xid.innerHTML     =yid.innerHTML;
         xtype.innerHTML   =ytype.innerHTML;
         xcaption.innerHTML=ycaption.innerHTML;
         xaction.innerHTML =yaction.innerHTML;
       //  xurut.innerHTML   =yurut.innerHTML; 
         yid.innerHTML     =xoid;
         ytype.innerHTML   =xotype;
         ycaption.innerHTML=xocaption;
         yaction.innerHTML =xoaction;
       //  yurut.innerHTML   =xourut;
   }	  	
 }
}

//==============================================
//menu privillages
function turnOn(access)
{
       param='acname='+access;
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
                                                       if(access=='level')
                                                          {
                                                                  document.getElementById('coldetail').innerHTML="<font color=#DD3333>Off <img id=privilball src=images/buttongreen.png class=privilball onclick=turnOn('detail') title='Click to Activate'></font>";
                                                              document.getElementById('collevel').innerHTML ="<font color=#00AA00>Active</font>";
                                                          }
                                                       else
                                                       {
                                                                 document.getElementById('collevel').innerHTML="<font color=#DD3333>Off <img id=privilball src=images/buttongreen.png class=privilball onclick=turnOn('level') title='Click to Activate'></font>";
                                                                 document.getElementById('coldetail').innerHTML ="<font color=#00AA00>Active</font>";
                                                       }   
                                               }
                                       }
                                       else {
                                               busy_off();
                                               error_catch(con.status);
                                       }
                     }	
        }	

       if(confirm('Are you sure want to use \''+access+'\' as privillage type ?'))
                post_response_text('slave_changeMenuLevel.php', param, respog);	
}

function loadMenuLevelSetting(obj,evt)
{
 hideDetailForm('globalakses','ctrmenu');  
  hideDetailForm('ctr','ctrmenu');//hide detail from
       obj.style.backgroundColor='#E36707';
   pos=getMouseP(evt);
       param='';
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
                             document.getElementById('content').innerHTML=con.responseText;
                                                         document.getElementById('ctr').style.display='';
                                                         document.getElementById('ctr').style.top=pos[1]+'px';
                                                         document.getElementById('ctr').style.left=pos[0]+'px';
                                               }
                                       }
                                       else {
                                               busy_off();
                                               error_catch(con.status);
                                       }
                     }	
        }
       post_response_text('slave_getMenuLevelForm.php', param, respog);	
}

function hideThis(objid)
{
 document.getElementById('content').innerHTML='';
 document.getElementById('ctr').style.display='none';
 document.getElementById(objid).style.backgroundColor='#FFFFFF'; 	
}

function updateMenuLevel(obj,levl,mid)
{
       param='level='+levl+'&id='+mid;
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
                                                       obj.style.backgroundColor='#FFFFFF';	
                                               }
                                       }
                                       else {
                                               busy_off();
                                               error_catch(con.status);
                                       }
                     }	
        }
       obj.style.backgroundColor='#E35D01';
       post_response_text('slave_saveMenuLevel.php', param, respog);	
}

//=============================
//user privillages
function loadUserLevelSetting(obj,evt)
{
 hideDetailForm('globalakses','ctrmenu') ;   
  hideDetailForm('ctr','ctrmenu');//hide detail from
       obj.style.backgroundColor='#E36707';
   pos=getMouseP(evt);
       param='';
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
                             document.getElementById('content').innerHTML=con.responseText;
                                                         document.getElementById('ctr').style.display='';
                                                         document.getElementById('ctr').style.top=pos[1]+'px';
                                                         document.getElementById('ctr').style.left=pos[0]+'px';
                                               }
                                       }
                                       else {
                                               busy_off();
                                               error_catch(con.status);
                                       }
                     }	
        }
       post_response_text('slave_getUserLevelForm.php', param, respog);		
}

function setAccessLevel(obj,uname,level)
{
       obj.style.backgroundColor='#E36707';
       param='un='+uname+'&newlevel='+level;
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
                                                       obj.style.backgroundColor='#FFFFFF';
                                               }
                                       }
                                       else {
                                               busy_off();
                                               error_catch(con.status);
                                       }
                     }	
        }
       post_response_text('slave_getUpdateUserLevel.php', param, respog);		
}

function loadDetailPrivillageSetting(obj,evt)
{
   hideDetailForm('globalakses','ctrmenu') ;   
   hideDetailForm('ctr','ctrmenu');//hide detail from    
   hideDetailForm();//clear all
   obj.style.backgroundColor='#E36707';
   pos=getMouseP(evt);
   param='';
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
                                                      document.getElementById('content').innerHTML=con.responseText;
                                                     document.getElementById('ctr').style.display='';
                                                     document.getElementById('ctr').style.top=pos[1]+'px';
                                                     document.getElementById('ctr').style.left=pos[0]+'px';
                                           }
                                   }
                                   else {
                                           busy_off();
                                           error_catch(con.status);
                                   }
                 }	
    }
   post_response_text('slave_getDetailPrivillageForm.php', param, respog);		
}
function setMapUserMenu(ev,rowobj,uname)
{
       rowobj.style.backgroundColor='#E36707';
   pos=getMouseP(ev);
       param='uname='+uname;
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
                             document.getElementById('contentmenu').innerHTML=con.responseText;
                                                         document.getElementById('ctrmenu').style.display='';
                                                         document.getElementById('ctrmenu').style.top=pos[1]+'px';
                                                         document.getElementById('ctrmenu').style.left=pos[0]+'px';
                                                         rowobj.style.backgroundColor='#E8F2FE';//class standardrow color
                                               }
                                       }
                                       else {
                                               busy_off();
                                               error_catch(con.status);
                                       }
                     }	
        }
       post_response_text('slave_getMapUserMenu.php', param, respog);	
}

function hideDetailForm()
{
       document.getElementById('lab0').style.backgroundColor='#FFFFFF';
       document.getElementById('lab1').style.backgroundColor='#FFFFFF';
       //document.getElementById('lab2').style.backgroundColor='#FFFFFF';
       document.getElementById('lab3').style.backgroundColor='#FFFFFF';
       //document.getElementById('lab4').style.backgroundColor='#FFFFFF';
       x=arguments.length;
       for(z=0;z<x;z++)
       document.getElementById(arguments[z]).style.display='none';
}

function changePrivillage(menuid,uname,obj)
{
       if(obj.checked)
               action='add';
   else
           action='remove';
       document.getElementById('orderlab'+menuid).style.backgroundColor='#E36707';	
       param='uname='+uname+'&menuid='+menuid+'&action='+action;		
       function respog()
       {
                     if(con.readyState==4)
                     {
                               if (con.status == 200) {
                                               busy_off();
                                               if (!isSaveResponse(con.responseText)) {
                                                       alert('ERROR TRANSACTION,\n' + con.responseText);
                                                   if(obj.checked)
                                                          obj.checked=false;
                                                       else
                                                          obj.checked=true;
                                               }
                                               else {
                                                       //alert(con.responseText);
                                               }
                                       }
                                       else {
                                               busy_off();
                                               error_catch(con.status);
                                       }
                     }
         document.getElementById('orderlab'+menuid).style.backgroundColor='#FFFFFF';		  	
        }
       post_response_text('slave_changeDetailPrivillage.php', param, respog);	
}

function resetDetailPrivillage(uname)
{

       param='uname='+uname;		
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
                        clearCheckBox();
                                               }
                                       }
                                       else {
                                               busy_off();
                                               error_catch(con.status);
                                       }
                     }

        }
 if(confirm('Are yous sure clearing '+uname+' Privillage..?'))	 
       post_response_text('slave_clearDetailPrivillage.php', param, respog);		
}

function clearCheckBox(){
       for (x = 0; x <= max_id; x++) {
               vz = 'cx' + x;

               try { //try onebyone
                       document.getElementById(vz).checked = false;
               } 
               catch (e) {
               }
       }
}	

//=========================================================================
//parentchild settings

function setThis(id,cap,e,typ)
{
       if (document.getElementById('parent').innerHTML == '') {
               document.getElementById('parent').innerHTML = cap;
               document.getElementById('idparent').value = id;
               document.getElementById('parent').style.backgroundColor="#6666FF";
               document.getElementById('legend').innerHTML = "Step #2: <b>Choose Child..!</b>:";
       }
       else
       {
               if (id == document.getElementById('idparent').value) {
                 alert('Choose other for child');
               }
               else if(typ.toLowerCase()=='master')
               {
                       alert('Main menu can not uses as child');
               }
               else {
                       document.getElementById('child').innerHTML = cap;
                       document.getElementById('idchild').value = id;
                       document.getElementById('child').style.backgroundColor="#66ff66";
                       document.getElementById('menuOrderContainer').style.display = "none";
                       document.getElementById('nav').style.display='';
               }
       }
}

function clearBracket()
{
               document.getElementById('parent').innerHTML = '';
               document.getElementById('idparent').value = '';
               document.getElementById('parent').style.backgroundColor="#FFFFFF";
               document.getElementById('legend').innerHTML = "Step #1: <b>Choose Parent..!</b>:";
               document.getElementById('child').innerHTML = '';
               document.getElementById('idchild').value = '';
               document.getElementById('child').style.backgroundColor="#ffffff";
               document.getElementById('menuOrderContainer').style.display = "";
               document.getElementById('nav').style.display='none';	
}

function saveSetting()
{
       _parent=document.getElementById('idparent').value;
       _child=document.getElementById('idchild').value;

       if(_parent=='' || _child=='')
       {
               alert('Action Fail');
               clearBracket()
       }
       else
       {
          param='parent='+_parent+'&child='+_child;
          if(confirm('Are you sure..?'))
          {
          post_response_text('slave_saveParentChildSetting.php', param, respog);	   	

          }	
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
                                                       alert('Done');
                                                       window.location.reload();
                                               }
                                       }
                                       else {
                                               busy_off();
                                               error_catch(con.status);
                                       }
                     }	  	
        }	
}

function turnOff()
{
       if(confirm('Turning off security causes each user has the same privillages,\nand all menu will be granted. Are you sure..?'))
       {
          post_response_text('slave_turnOffPrivillage.php','', respog);	   			
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
                                                       alert('Done');
                                                       window.location.reload();
                                               }
                                       }
                                       else {
                                               busy_off();
                                               error_catch(con.status);
                                       }
                     }	  	
        }		
}

function changeMyPassword(uname)
{
       p1=document.getElementById('pw1').value;
       p2=document.getElementById('pw2').value;
       p3=document.getElementById('pw3').value;
       if(p2!=p3)
       {
               alert('Password baru tidak sesuai satu sama lain');
       }
       else
       {
               if(p1.length<6 || p2.length <6 || p3.length <6)
               {
                       alert('Password minimum 6 character');
               }
               else
               {
                       param='uname='+uname+'&p1='+p1+'&p2='+p2;
                       tujuan='slave_changeMyPassword.php';
                       if(confirm('Are you sure changing your password...?'))
                               post_response_text(tujuan,param, respog);	   					
               }
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
                                                       alert('Your password has been changed, please relogin...');
                                                       parent.location='logout.php';
                                                       //parent.window.location.reload();
                                               }
                                       }
                                       else {
                                               busy_off();
                                               error_catch(con.status);
                                       }
                     }	  	
        }		
}

function copyPrivileges()
{
   pengguna=document.getElementById('pengguna');
   pengguna=pengguna.options[pengguna.selectedIndex].value;
   dari=document.getElementById('dari');
   dari=dari.options[dari.selectedIndex].value;
   param='dari='+dari+'&pengguna='+pengguna;
   if(dari==pengguna)
       alert('Pengguna tidak boleh sama');
   else
       {
           tujuan='slave_copyPrivileges.php';
           if(confirm('Are you sure ...?'))
                   post_response_text(tujuan,param, respog);           
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
                           alert('Done');
                   }
               }
               else {
                       busy_off();
                       error_catch(con.status);
               }
         }	  	
        }        
}

function loadGlobal(obj,evt){ 
  hideDetailForm('ctr','globalakses');//hide detail from
  hideDetailForm('ctrmenu','globalakses') ;   
  hideDetailForm();//clear all
   obj.style.backgroundColor='#E36707';
   pos=getMouseP(evt);
   param='';
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
                                                      document.getElementById('contentglobal').innerHTML=con.responseText;
                                                     document.getElementById('globalakses').style.display='';
                                                     document.getElementById('globalakses').style.top=pos[1]+'px';
                                                     document.getElementById('globalakses').style.left=pos[0]+'px';
                                           }
                                   }
                                   else {
                                           busy_off();
                                           error_catch(con.status);
                                   }
                 }	
    }
   post_response_text('slave_getDetailMenu.php', param, respog);
}

function changeToGlobal(menuid,obj){
      obj.style.backgroundColor='#E36707';
      document.getElementById('orderlab'+menuid).style.backgroundColor='#E36707';
     if(obj.checked==true){
        if(confirm('Global cccess will include access for parent menu, are you sure..?'))
            {                 
                  param='menuid='+menuid+'&aksi=add';
                  post_response_text('slave_setGlobalAccess.php', param, respog);                         
             }
            else
                {
                   obj.style.backgroundColor='#FFFFFF';
                   document.getElementById('orderlab'+menuid).style.backgroundColor='#FFFFFF';             
                   obj.checked=false;
                }             
     }  
      else {      
            if(confirm('This will remove this menu from all user(Not include parent menu), are you sure..?'))
                {                 
                      param='menuid='+menuid+'&aksi=remove';
                     post_response_text('slave_setGlobalAccess.php', param, respog);                             
                 }
                else
                    {
                       obj.style.backgroundColor='#FFFFFF';
                       document.getElementById('orderlab'+menuid).style.backgroundColor='#FFFFFF';             
                       obj.checked=true;
                    }                 
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
                                                   alert('Done');
                                                    obj.style.backgroundColor='#FFFFFF';
                                                    document.getElementById('orderlab'+menuid).style.backgroundColor='#FFFFFF';
                                            }
                                    }
                                    else {
                                            busy_off();
                                            error_catch(con.status);
                                    }
                  }	
     }      
}

function closeThis(){
    document.getElementById('contentglobal').innerhtml='';
    document.getElementById('globalakses').style.display='none';
    document.getElementById('lab4').style.backgroundColor='#FFFFFF';
}

function validat(uname,ev)
{
  key=getKey(ev);
  if(key==13){
        changeMyPassword(uname);
  } else {
    if(key==39 || key==34 || key==38)
        return false;
    else
        return true;
  }	
}
