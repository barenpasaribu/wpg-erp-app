/**
 * @author antoniuslouis
 */

//================================================================
function busy_on()//set busy on
{
	document.getElementById('progress').style.display='';//you must have object with id=progress on your documents
	document.body.style.cursor='wait';
}

function busy_off()//set busy off
{
	document.getElementById('progress').style.display='none';//you must have object with id=progress on your documents
	document.body.style.cursor='default';
}
//===================================================================
//=============================================================

function createXMLHttpRequest() {
   try { return new ActiveXObject("Msxml2.XMLHTTP"); } 
   catch (e) {}
   try { return new ActiveXObject("Microsoft.XMLHTTP"); } 
   catch (e) {}
   try { return new XMLHttpRequest(); } 
   catch(e) {}
   alert("XMLHttpRequest Tidak didukung oleh browser");
   return null;
 }

 var coss = createXMLHttpRequest();


function get_reponse_text(tujuan,funct)
{
	busy_on();
	coss.open("GET",tujuan,true);
	coss.onreadystatechange= eval(funct);
	coss.send(null);
}
function post_response_text(tujuan,param,functiontoexecute)
{
busy_on()
coss.open("POST", tujuan, true);
coss.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
coss.setRequestHeader("Content-length", param.length);
coss.setRequestHeader("Connection", "close");

coss.onreadystatechange = eval(functiontoexecute);
coss.send(param);
}
function error_catch(x)
{
	switch (x){
      case 203:
	  alert('Dibutuhkan Authority');
	  break;
	  case 400:
	  alert('Error Server');
	  break;
	  case 403:
	  alert('Anda dilarang masuk');
	  break;
	  case 404:
	  alert('File tidak ditemukan');
	  break;
	  case 405:
	  alert('Method tidak diijinkan');
	  break;
	  case 407:
	  alert('Proxy Error');
	  break;
	  case 408:
	  alert('Permintaan terlalu lama');
	  break;
	  case 409:
	  alert('Query Conflict');
	  break;
	  case 414:
	  alert('ULI terlalu panjang');
	  break;
	  case 412:
	  alert('Variable terlalu banyak');
	  break;
	  case 415:
	  alert('Unsupported Media Type');
	  break;
	  case 500:
	  alert('Server busy, try submit later');
	  break;
	  case 502:
	  alert('Bad gateway');
	  break;
	  case 505:
	  alert('Browser anda terlalu tua');	    
      break;
}
}
//=============================================================
function getKey(e)//get key code e is event
{
        var key;
        if(window.event) {
               // for IE, e.keyCode or window.event.keyCode can be used
               key = e.keyCode;
        }
        else if(e.which) {
               key = e.which;
        }
        else {
               // no event, so pass through
               return true;
        }
      return key;
}
//========================================================================
function tanpa_kutip(e)//block quote and doublequote e is event
{
  key=getKey(e);
  if(key==39 || key==34 || key==38)
  return false;
  else
  return true;
}
function char_only(e)
{
  key=getKey(e);
  if((key <65 || key>122) && (key!=true && key!=32 && key!=8))
  return false;
  else
  return true;  	
}

function charAndNum(e)
{
  key=getKey(e);
  if((key <48 || key>122) && (key!=8 && key!=127 && key!=47 && key!=32 && key!=true))
  return false;
  else
  return true;  	
}
function charAndNumAndStrip(e)
{
  key=getKey(e);
  if((key <48 || key>122) && (key!=8 && key!=127 && key!=47 && key!=32 && key!=true&& key!=45))
  return false;
  else
  return true;  	
}

//===========================================================================
function angka_doang(e)//only numeric e is event
{
 key=getKey(e);
 if((key<48 || key>57) && (key!=8 && key!=46  && key!=127 && key!=true))
  return false;
 else
 {
     return true;
 }
}

//====================================================
function trim(stringToTrim){//trim space not support by IE
    retval=stringToTrim.replace(/^\s+|\s+$/g, "");
	return (retval);
}

function lockScreen(type)
{
	try{
	  if(document.getElementById('lock')){
	     document.getElementById('lock').style.display ='';
		 document.getElementById('front').style.display='';
		 if (trim(type).toLowerCase() == 'wait') {
            document.getElementById('front').innerHTML="<img src='images/progress.gif'><br><b>P l e a s e &nbsp  w a i t . ...!</b>";		 	
		 }
		 else if (trim(type).toLowerCase() == 'progress') {
		 	tempstr="<div id=progressLegend></div>";
			tempstr+="<div id=progressBar class=pBarBackground><div id=progressBarTop class=pBarTop></div></div>";
			document.getElementById('front').innerHTML=tempstr;
		 	} 
		 else{}							 	
	   }
	  else{
		dheight=docHeight();
		dwidth =docWidth();
		if(dheight<600)
		   dheight=600;
		c=document.createElement('div');
		c.setAttribute('id','lock');
		document.body.appendChild(c);	
		c.style.position='fixed';
		c.style.top='0px';
		c.style.left='0px';
	    c.style.width=dwidth+'px';	
	    c.style.height=dheight+'px';
		c.style.backgroundColor='#999999';	
        c.style.zIndex=1000;		
		test=document.createElement('div');
		test.setAttribute('id','front');
		test.setAttribute('class','dragdyn');
		document.body.appendChild(test);	
		test.style.position='fixed';
		test.style.top=(dheight/2)+'px';
		test.style.left=(dwidth/2-100)+'px';
		test.style.textAlign='center';
		test.style.padding='10px';
	//	test.style.backgroundColor='#8AC4F0';
		test.style.border='#A9CAF5 solid 1px';	
		test.style.zIndex=1001;	
		  if(trim(type).toLowerCase()=='wait'){
			test.innerHTML="<img src='images/progress.gif'><br><b>P l e a s e &nbsp  w a i t . ...!</b>";
		  }
		  else if(type=='progress'){
		 	tempstr="<div id=progressLegend class=pLegend>Progress Bar:</div>";
			tempstr+="<div id=progressBar class=pBarBackground><div id=progressBarTop class=pBarTop></div></div>";
			test.innerHTML=tempstr;		  	
		  } 
		  else{
		  	//default do nothing
		  }	
	  }
	}
	catch(e){}
    setOpacity('lock',0,30, 1);	
}
function unlockScreen()
{
	document.getElementById('lock').style.display='none';
	document.getElementById('front').style.display='none';
}
//====================================hide show object
function hideObject(obj)
{
	obj.style.display='none';
}
function showObject(obj)
{
	obj.style.display='';
}

function hideById(id)
{
	document.getElementById(id).style.display='none';
}
function showById(id)
{
	document.getElementById(id).style.display='';
}

function chgBackgroundImg(obj,img,color)
{
	if (obj.id != activeTab) {
		obj.style.backgroundImage = 'url(' + img + ')';
		obj.style.color=color;
	}
}
//+++++++++++++++++++++++++++++++++++++++++++++++++++


function isSaveResponse(txt)
{
	txt=txt.toUpperCase();
	if (txt.lastIndexOf('GAGAL') > -1 || txt.lastIndexOf('ERROR') > -1 || txt.lastIndexOf('WARNING') > -1)
      return false
	else
	  return true;  
}


function showDialog1(title,content,width,height,ev)
{
       if (document.getElementById('dynamic1')) {
		c.style.width = width+'px';
	   }
	   else {
	   	c = document.createElement('div');
	   	c.setAttribute('id', 'dynamic1');
	   	c.setAttribute('class', 'drag');
	   	c.style.position = 'absolute';
	   	c.style.display = 'none';
	   	c.style.top = '120px';
	   	c.style.left = '100px';
		c.style.width = width+'px';
	   	c.style.paddingTop = '3px';
	   	c.style.zIndex = 1000;
	   	document.body.appendChild(c);
	   }
        cont="<b style='color:#FFFFFF;'>"+title+"</b><img src=images/closebig.gif align=right onclick=closeDialog() title='Close detail' class=closebtn onmouseover=\"this.src='images/closebigon.gif';\" onmouseout=\"this.src='images/closebig.gif';\"><br><br>";
	    cont+="<div style='background-color:#FFFFFF;border:#777777 solid 2px;height:"+height+"px'>";
	    cont+=content;
	    cont+="</div>";
		document.getElementById('dynamic1').innerHTML=cont;
			pos = new Array();
            pos = getMouseP(ev);
            document.getElementById('dynamic1').style.top = pos[1] + 'px';
            document.getElementById('dynamic1').style.left = '75px';
		document.getElementById('dynamic1').style.display='';
}

function showDialog2(title,content,width,height,ev)
{
	
	if (document.getElementById('dynamic2')) {
		c.style.width = width+'px';
	} else {
	   	c = document.createElement('div');
	   	c.setAttribute('id', 'dynamic2');
	   	c.setAttribute('class', 'drag');
	   	c.style.position = 'absolute';
	   	c.style.display = 'none';
	   	c.style.top = '120px';
	   	c.style.left = '100px';
		c.style.width = width+'px';
	   	c.style.paddingTop = '3px';
	   	c.style.zIndex = 2000;
	   	document.body.appendChild(c);
	}
        cont="<b style='color:#FFFFFF;'>"+title+"</b><img src=images/closebig.gif align=right onclick=closeDialog2() title='Close detail' class=closebtn onmouseover=\"this.src='images/closebigon.gif';\" onmouseout=\"this.src='images/closebig.gif';\"><br><br>";
	cont+="<div style='background-color:#FFFFFF;border:#777777 solid 2px;height:"+height+"px'>";
	cont+=content;
	cont+="</div>";
	document.getElementById('dynamic2').innerHTML=cont;
	pos = new Array();
	pos = getMouseP(ev);
	document.getElementById('dynamic2').style.top = pos[1] + 'px';
	document.getElementById('dynamic2').style.left = '75px';
	document.getElementById('dynamic2').style.display='';
}

function closeDialog()
{
	document.getElementById('dynamic1').innerHTML='';
	document.getElementById('dynamic1').style.display='none';
	if(document.getElementById('dynamic2')) {
		closeDialog2();
	}
}

function closeDialog2()
{
	document.getElementById('dynamic2').innerHTML='';
	document.getElementById('dynamic2').style.display='none';
}

function change_number(object)
{
	   while(object.value.indexOf(",")>-1)
	   {
	   	object.value=object.value.replace(",","");
	   }
	//number format cleared and verified
	str=object.value.replace(".","");
	rex=/[^0-9]/;
	if ((!str.match(rex)) || (parseFloat(str)==0.00)) {
			try{
				object.value=_formatted(object);
				}
			catch(ex)
				{
				alert(ex.toString());
				}
	}
	else {
		if (object.value.length > 0) {
			alert('Nominal salah');
			object.focus();
		}		
	}
}

function remove_comma(object){//object adalah textbox atau componen yang memiliki atribut 'value'
	x = object.value;
	while (x.indexOf(",") > -1) {
		x = x.replace(",", "");
	}
	return x;
}
function remove_comma_var(nilai){//nilai adalah string yang bisa berupa 9,001.50 atau 9,0000
	while (nilai.indexOf(",") > -1) {
		nilai = nilai.replace(",", "");
	}
	return nilai;
}


function busy_on()//set busy on
{
	document.getElementById('progress').style.display='';//you must have object with id=progress on your documents
	document.body.style.cursor='wait';
}

function busy_off()//set busy off
{
	document.getElementById('progress').style.display='none';//you must have object with id=progress on your documents
	document.body.style.cursor='default';
}


function remove_kutip(object){
	x = object.value;
	while (x.indexOf('"') > -1) {
		x = x.replace('"', '');
	}
	while (x.indexOf("'") > -1) {
		x = x.replace("'", "");
	}
	object.value=x;
}
