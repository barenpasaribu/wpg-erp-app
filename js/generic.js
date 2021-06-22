/**
 * @author antoniuslouis
 */
//platform ==================================
var scrHeight=screen.availHeight;
var scrWidth=screen.availWidth;
var platform=navigator.platform;
//****************************************

var isIE = document.all?true:false; 

function getMouseP(e) {// e is event
//this work when calling from function or html object
	var tempX = 0;
	var tempY = 0;
		if (isIE) { // grab the x-y pos.s if browser is IE
			var ScrollTop = (document.body.parentElement) ? document.body.parentElement.scrollTop:document.body.scrollTop;
			var ScrollLeft = (document.body.parentElement) ? document.body.parentElement.scrollLeft:document.body.scrollLeft;
			tempX = ScrollTop+150;
			tempY = ScrollLeft+150;			
		}
		else {  // grab the x-y pos.s if browser is NS
			tempX = e.pageX;
			tempY = e.pageY;
		}
  
	if (tempX < 0){tempX = 0;}
	if (tempY < 0){tempY = 0;}  
	arr= new Array();
	arr[0]=tempX;
	arr[1]= tempY;
	return arr; //arr[0]= x coord arr[1]=y coord
}

function getMousePDefault(e) {// e is event
//this is uses when position directly accessed from html element
//if you call this function from other function, IE browser will not work
//if you want this posible calling through function use getMouseP above
	var tempX = 0;
	var tempY = 0;
		if (isIE) { // grab the x-y pos.s if browser is IE
			try {
				tempX = ev.clientX + document.documentElement.scrollLeft;
				tempY = ev.clientY + document.documentElement.scrollTop;				
			} 
			catch (e) {		
				tempX = ev.clientX + document.body.scrollLeft;
				tempY =ev.clientY + document.body.scrollTop;	
              }
		}
		else {  // grab the x-y pos.s if browser is NS
			tempX = e.pageX;
			tempY = e.pageY;
		}
	if (tempX < 0){tempX = 0;}
	if (tempY < 0){tempY = 0;}  
	arr= new Array();
	arr[0]=tempX;
	arr[1]= tempY;
	return arr; //arr[0]= x coord arr[1]=y coord
}
//************************************************
function docHeight(){
  if(isIE)
  return(document.body.offsetHeight);
  else	
   return (document.height);
}
function docWidth(){
  if(isIE)
  return(document.body.offsetWidth);
  else	
   return (document.width);
}
//===============================================
function setOpacity(id, opacStart, opacEnd, sec) {//id=element id, opacstart= integer opacity start,sec =integer represent total time
    //speed for each frame
	millisec=sec*1000;
    var speed = Math.round(millisec / 100);
    var timer = 0;

    //determine the direction for the blending, if start and end are the same nothing happens
    if(opacStart > opacEnd) {
        for(i = opacStart; i >= opacEnd; i--) {
            t=setTimeout("changeOpac(" + i + ",'" + id + "')",(timer * speed));
            timer++;
			if(i==opacEnd)
			  clearTimeout(t);			
        }
    } else if(opacStart < opacEnd) {
        for(i = opacStart; i <= opacEnd; i++)
            {
            t=setTimeout("changeOpac(" + i + ",'" + id + "')",(timer * speed));
            timer++;
			if(i==opacEnd)
			  clearTimeout(t);
        }
    }
}

//change the opacity for different browsers
function changeOpac(opacity, id) {
	var object = document.getElementById(id).style;
    object.opacity = (opacity / 100);
    object.MozOpacity = (opacity / 100);
    object.KhtmlOpacity = (opacity / 100);
    object.filter = "alpha(opacity=" + opacity + ")";
} 
//**************************************************************
function getImgSize(imgSrc)
{
imgSrc=document.getElementById(imgSrc).src;
var newImg = new Image();
newImg.src = imgSrc;
var height = newImg.height;
var width = newImg.width;
     this.x=function(){
	  return width;
	 }
     this.y=function(){
	  return height;
	 }
//here to use
//function imgSize(d){
//s= new getImgSize(d);
//test=s.y();
//alert(test);
//}
}
//******************************************************
function chg_color(obj,tocolor)//chage object background color
{
	obj.style.backgroundColor=tocolor;
}
//================================================================
function busy_on()//set busy on
{
	
	document.getElementById('progress').style.display='';//you must have object with id=progress on your documents
	//document.getElementById('qm0').style.display='none';
	//document.getElementById('menumaster').style.display='none';
	document.body.style.cursor='wait';
}

function busy_off()//set busy off
{
	
	document.getElementById('progress').style.display='none';//you must have object with id=progress on your documents
	//document.getElementById('qm0').style.display='';
	//document.getElementById('menumaster').style.display='';
	document.body.style.cursor='default';
}
function busy_onx()//set busy on
{
	document.getElementById('progress').style.display='';//you must have object with id=progress on your documents
	document.getElementById('qm0').style.display='none';
	//document.getElementById('masterfooter').style.display='none';
	document.body.style.cursor='wait';
	
}

function busy_offx()//set busy off
{
	document.getElementById('progress').style.display='none';//you must have object with id=progress on your documents
	document.getElementById('qm0').style.display='';
	//document.getElementById('masterfooter').style.display='';
	document.body.style.cursor='default';
}
//===================================================================
function disable_on(objtodisable)//Disable Object
{
	objtodisable.disabled=true;
}
function disable_off(objtodisable)//Enable Object
{
	objtodisable.disabled=false;
}

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

 var con = createXMLHttpRequest();


function get_reponse_text(tujuan,funct)
{
	busy_on();
                    zz=verify();
                       if(zz){       
                            par=parent.location.href.replace("http://","");
                            tujuan+='&par='+par;                           
                           con.open("GET",tujuan,true);
                           con.onreadystatechange= eval(funct);
                           con.send(null);
                       }
                       else
                           window.location='logout.php';
}
function post_response_text(tujuan,param,functiontoexecute) {
	busy_on();
	zz = verify();
	if (zz) {
		par = parent.location.href.replace("http://", "");
		param += '&par=' + par;
		con.open("POST", tujuan, true);
		con.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		//con.setRequestHeader("Content-length", param.length);
		//con.setRequestHeader("Connection", "close");

		con.onreadystatechange = eval(functiontoexecute);
		con.send(param);
	} else
		window.location = 'logout.php';
}
function post_response_textx(tujuan,param,functiontoexecute)
{
busy_onx();
zz=verify();
    if(zz){
        par=parent.location.href.replace("http://","");
        param+='&par='+par;
        con.open("POST", tujuan, true);
        con.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        //con.setRequestHeader("Content-length", param.length);
        //con.setRequestHeader("Connection", "close");
		
		con.onreadystatechange = eval(functiontoexecute);
        con.send(param);
    }
    else
        window.location='logout.php';
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
function charIs(e)
{
 key=getKey(e);
 return (String.fromCharCode(key));	
}
//=============================================================================
function tanpa_kutip_dan_sepasi(e)//block quote and doublequote and space e is event
{
 key=getKey(e);
 if(key==39 || key==34 || key==38 || key==32)
    return false;
 else
    return true;
}

//==============================================================
/*disable rightclick on document
var message='';
function clickIE() 
{
	if (document.all) 
	{
		(message);
		return false;
	}
}
function clickNS(e) 
{
	if (document.layers||(document.getElementById&&!document.all)) 
	{
		if (e.which==2||e.which==3) 
			{
				(message);
				return false;
				}
			}
		}
if (document.layers) 
	{
		document.captureEvents(Event.MOUSEDOWN);
		document.onmousedown=clickNS;
	}
else
{
	document.onmouseup=clickNS;//penggunaan ini bisa bentrok dengan drag
	document.oncontextmenu=clickIE;
	}
document.oncontextmenu=new Function('return false')
*/
//document.oncontextmenu=new Function('return false')
//==========================================================
function disable_paste(e) //disable ctrl+v
{
        var forbiddenKeys = new Array('v');
        var key;
        var isCtrl;

        if(window.event)
        {
                key = window.event.keyCode;     //IE
                if(window.event.ctrlKey)
                        isCtrl = true;
                else
                        isCtrl = false;
        }
        else
        {
                key = e.which;     //firefox
                if(e.ctrlKey)
                        isCtrl = true;
                else
                        isCtrl = false;
        }
        if(isCtrl)
        {
                for(i=0; i<forbiddenKeys.length; i++)
                {
                        if(forbiddenKeys[i].toLowerCase() == String.fromCharCode(key).toLowerCase())
                        {
                                return false;
                        }
                }
        }
        return true;
}
//====================================================
function trim(stringToTrim){//trim space not support by IE
    retval=stringToTrim.replace(/^\s+|\s+$/g, "");
	return (retval);
}
//========================================================
//Numbering format
//use _formatted(x,y)     //x is string eg._formatted('123')
						  //y is decimal eg _formatted(2342.234)
						  //y or x can be blank from caller source
						  
function NumberFormat(num, inputDecimal)
{
	// constants
	this.COMMA = ',';
	this.PERIOD = '.';
	this.DASH = '-'; // v1.5.0 - new - used internally
	this.LEFT_PAREN = '('; // v1.5.0 - new - used internally
	this.RIGHT_PAREN = ')'; // v1.5.0 - new - used internally
	this.LEFT_OUTSIDE = 0; // v1.5.0 - new - currency
	this.LEFT_INSIDE = 1;  // v1.5.0 - new - currency
	this.RIGHT_INSIDE = 2;  // v1.5.0 - new - currency
	this.RIGHT_OUTSIDE = 3;  // v1.5.0 - new - currency
	this.LEFT_DASH = 0; // v1.5.0 - new - negative
	this.RIGHT_DASH = 1; // v1.5.0 - new - negative
	this.PARENTHESIS = 2; // v1.5.0 - new - negative
	this.NO_ROUNDING = -1 // v1.5.1 - new

	// member variables
	this.num;
	this.numOriginal;
	this.hasSeparators = false;  // v1.5.0 - new
	this.separatorValue;  // v1.5.0 - new
	this.inputDecimalValue; // v1.5.0 - new
	this.decimalValue;  // v1.5.0 - new
	this.negativeFormat; // v1.5.0 - new
	this.negativeRed; // v1.5.0 - new
	this.hasCurrency;  // v1.5.0 - modified
	this.currencyPosition;  // v1.5.0 - new
	this.currencyValue;  // v1.5.0 - modified
	this.places;
	this.roundToPlaces; // v1.5.1 - new

	// external methods
	this.setNumber = setNumberNF;
	this.toUnformatted = toUnformattedNF;
	this.setInputDecimal = setInputDecimalNF; // v1.5.0 - new
	this.setSeparators = setSeparatorsNF; // v1.5.0 - new - for separators and decimals
	this.setCommas = setCommasNF;
	this.setNegativeFormat = setNegativeFormatNF; // v1.5.0 - new
	this.setNegativeRed = setNegativeRedNF; // v1.5.0 - new
	this.setCurrency = setCurrencyNF;
	this.setCurrencyPrefix = setCurrencyPrefixNF;
	this.setCurrencyValue = setCurrencyValueNF; // v1.5.0 - new - setCurrencyPrefix uses this
	this.setCurrencyPosition = setCurrencyPositionNF; // v1.5.0 - new - setCurrencyPrefix uses this
	this.setPlaces = setPlacesNF;
	this.toFormatted = toFormattedNF;
	this.toPercentage = toPercentageNF;
	this.getOriginal = getOriginalNF;
	this.moveDecimalRight = moveDecimalRightNF;
	this.moveDecimalLeft = moveDecimalLeftNF;

	// internal methods
	this.getRounded = getRoundedNF;
	this.preserveZeros = preserveZerosNF;
	this.justNumber = justNumberNF;
	this.expandExponential = expandExponentialNF;
	this.getZeros = getZerosNF;
	this.moveDecimalAsString = moveDecimalAsStringNF;
	this.moveDecimal = moveDecimalNF;

	// setup defaults
	if (inputDecimal == null) {
		this.setNumber(num, this.PERIOD);
	} else {
		this.setNumber(num, inputDecimal); // v.1.5.1 - new
	}
	this.setCommas(true);
	this.setNegativeFormat(this.LEFT_DASH); // v1.5.0 - new
	this.setNegativeRed(false); // v1.5.0 - new
	this.setCurrency(false); // v1.5.1 - false by default
	this.setCurrencyPrefix('$');
	this.setPlaces(2);
}

/*
 * setInputDecimal
 * val - The decimal value for the input.
 *
 * v1.5.0 - new
 */
function setInputDecimalNF(val)
{
	this.inputDecimalValue = val;
}
function setNumberNF(num, inputDecimal)
{
	if (inputDecimal != null) {
		this.setInputDecimal(inputDecimal); // v.1.5.1 - new
	}
	
	this.numOriginal = num;
	this.num = this.justNumber(num);
}

function toUnformattedNF()
{
	return (this.num);
}

function getOriginalNF()
{
	return (this.numOriginal);
}


function setNegativeFormatNF(format)
{
	this.negativeFormat = format;
}

function setNegativeRedNF(isRed)
{
	this.negativeRed = isRed;
}

function setSeparatorsNF(isC, separator, decimal)
{
	this.hasSeparators = isC;
	
	// Make sure a separator was passed in
	if (separator == null) separator = this.COMMA;
	
	// Make sure a decimal was passed in
	if (decimal == null) decimal = this.PERIOD;
	
	if (separator == decimal) {
		this.decimalValue = (decimal == this.PERIOD) ? this.COMMA : this.PERIOD;
	} else {
		this.decimalValue = decimal;
	}
	
	this.separatorValue = separator;
}


function setCommasNF(isC)
{
	this.setSeparators(isC, this.COMMA, this.PERIOD);
}

function setCurrencyNF(isC)
{
	this.hasCurrency = isC;
}


function setCurrencyValueNF(val)
{
	this.currencyValue = val;
}


function setCurrencyPrefixNF(cp)
{
	this.setCurrencyValue(cp);
	this.setCurrencyPosition(this.LEFT_OUTSIDE);
}


function setCurrencyPositionNF(cp)
{
	this.currencyPosition = cp
}


function setPlacesNF(p)
{
	this.roundToPlaces = !(p == this.NO_ROUNDING); // v1.5.1
	this.places = (p < 0) ? 0 : p; // v1.5.1 - Don't leave negatives.
}


function toFormattedNF()
{	
	var pos;
	var nNum = this.num; // v1.0.1 - number as a number
	var nStr;            // v1.0.1 - number as a string
	var splitString = new Array(2);   // v1.5.0
	
	// round decimal places - modified v1.5.1
	// Note: Take away negative temporarily with Math.abs
	if (this.roundToPlaces) {
		nNum = this.getRounded(nNum);
		nStr = this.preserveZeros(Math.abs(nNum)); // this step makes nNum into a string. v1.0.1 Math.abs
	} else {
		nStr = this.expandExponential(Math.abs(nNum)); // expandExponential is called in preserveZeros, so call it here too
	}

	// the separator and decimal values have to be different
	// this is enforced in justNumber
	if (nStr.indexOf(this.PERIOD) == -1) {
		splitString[0] = nStr;
		splitString[1] = '';
	} else {
		splitString = nStr.split(this.PERIOD, 2);
	}

	// separators
	if (this.hasSeparators) {
		pos = splitString[0].length;
		while (pos > 0) {
			pos -= 3;
			if (pos <= 0) break;

			splitString[0] = splitString[0].substring(0,pos)
				+ this.separatorValue
				+ splitString[0].substring(pos, splitString[0].length);
		}
	}
	
	// decimal
	if (splitString[1].length > 0) {
		nStr = splitString[0] + this.decimalValue + splitString[1];
	} else {
		nStr = splitString[0];
	}
	
	// negative and currency
	// $[c0] -[n0] $[c1] -[n1] #.#[nStr] -[n2] $[c2] -[n3] $[c3]
	var c0 = '';
	var n0 = '';
	var c1 = '';
	var n1 = '';
	var n2 = '';
	var c2 = '';
	var n3 = '';
	var c3 = '';
	var negSignL = (this.negativeFormat == this.PARENTHESIS) ? this.LEFT_PAREN : this.DASH;
	var negSignR = (this.negativeFormat == this.PARENTHESIS) ? this.RIGHT_PAREN : this.DASH;
		
	if (this.currencyPosition == this.LEFT_OUTSIDE) {
		// add currency sign in front, outside of any negative. example: $-1.00	
		if (nNum < 0) {
			if (this.negativeFormat == this.LEFT_DASH || this.negativeFormat == this.PARENTHESIS) n1 = negSignL;
			if (this.negativeFormat == this.RIGHT_DASH || this.negativeFormat == this.PARENTHESIS) n2 = negSignR;
		}
		if (this.hasCurrency) c0 = this.currencyValue;
	} else if (this.currencyPosition == this.LEFT_INSIDE) {
		// add currency sign in front, inside of any negative. example: -$1.00
		if (nNum < 0) {
			if (this.negativeFormat == this.LEFT_DASH || this.negativeFormat == this.PARENTHESIS) n0 = negSignL;
			if (this.negativeFormat == this.RIGHT_DASH || this.negativeFormat == this.PARENTHESIS) n3 = negSignR;
		}
		if (this.hasCurrency) c1 = this.currencyValue;
	}
	else if (this.currencyPosition == this.RIGHT_INSIDE) {
		// add currency sign at the end, inside of any negative. example: 1.00$-
		if (nNum < 0) {
			if (this.negativeFormat == this.LEFT_DASH || this.negativeFormat == this.PARENTHESIS) n0 = negSignL;
			if (this.negativeFormat == this.RIGHT_DASH || this.negativeFormat == this.PARENTHESIS) n3 = negSignR;
		}
		if (this.hasCurrency) c2 = this.currencyValue;
	}
	else if (this.currencyPosition == this.RIGHT_OUTSIDE) {
		// add currency sign at the end, outside of any negative. example: 1.00-$
		if (nNum < 0) {
			if (this.negativeFormat == this.LEFT_DASH || this.negativeFormat == this.PARENTHESIS) n1 = negSignL;
			if (this.negativeFormat == this.RIGHT_DASH || this.negativeFormat == this.PARENTHESIS) n2 = negSignR;
		}
		if (this.hasCurrency) c3 = this.currencyValue;
	}

	nStr = c0 + n0 + c1 + n1 + nStr + n2 + c2 + n3 + c3;
	
	// negative red
	if (this.negativeRed && nNum < 0) {
		nStr = '<font color="red">' + nStr + '</font>';
	}

	return (nStr);
}


function toPercentageNF()
{
	nNum = this.num * 100;
	
	// round decimal places
	nNum = this.getRounded(nNum);
	
	return nNum + '%';
}


function getZerosNF(places)
{
		var extraZ = '';
		var i;
		for (i=0; i<places; i++) {
			extraZ += '0';
		}
		return extraZ;
}


function expandExponentialNF(origVal)
{
	if (isNaN(origVal)) return origVal;
	
	var newVal = parseFloat(origVal) + ''; // parseFloat to let JavaScript evaluate number
	var eLoc = newVal.toLowerCase().indexOf('e');

	if (eLoc != -1) {
		var plusLoc = newVal.toLowerCase().indexOf('+');
		var negLoc = newVal.toLowerCase().indexOf('-', eLoc); // search for - after the e
		var justNumber = newVal.substring(0, eLoc);
		
		if (negLoc != -1) {
			// shift decimal to the left
			var places = newVal.substring(negLoc + 1, newVal.length);
			justNumber = this.moveDecimalAsString(justNumber, true, parseInt(places));
		} else {
			// shift decimal to the right
			// Check if there's a plus sign, and if not refer to where the e is.
			// This is to account for either formatting 1e21 or 1e+21
			if (plusLoc == -1) plusLoc = eLoc;
			var places = newVal.substring(plusLoc + 1, newVal.length);
			justNumber = this.moveDecimalAsString(justNumber, false, parseInt(places));
		}
		
		newVal = justNumber;
	}

	return newVal;
} 


function moveDecimalRightNF(val, places)
{
	var newVal = '';
	
	if (places == null) {
		newVal = this.moveDecimal(val, false);
	} else {
		newVal = this.moveDecimal(val, false, places);
	}
	
	return newVal;
}

function moveDecimalLeftNF(val, places)
{
	var newVal = '';
	
	if (places == null) {
		newVal = this.moveDecimal(val, true);
	} else {
		newVal = this.moveDecimal(val, true, places);
	}
	
	return newVal;
}


function moveDecimalAsStringNF(val, left, places)
{
	var spaces = (arguments.length < 3) ? this.places : places;
	if (spaces <= 0) return val; // to avoid Mozilla limitation
			
	var newVal = val + '';
	var extraZ = this.getZeros(spaces);
	var re1 = new RegExp('([0-9.]+)');
	if (left) {
		newVal = newVal.replace(re1, extraZ + '$1');
		var re2 = new RegExp('(-?)([0-9]*)([0-9]{' + spaces + '})(\\.?)');		
		newVal = newVal.replace(re2, '$1$2.$3');
	} else {
		if (re1.test(newVal)) {
			newVal = RegExp.leftContext + RegExp.$1 + extraZ + RegExp.rightContext;
		}
		var re2 = new RegExp('(-?)([0-9]*)(\\.?)([0-9]{' + spaces + '})');
		newVal = newVal.replace(re2, '$1$2$4.');
	}
	newVal = newVal.replace(/\.$/, ''); // to avoid IE flaw
	
	return newVal;
}


function moveDecimalNF(val, left, places)
{
	var newVal = '';
	
	if (places == null) {
		newVal = this.moveDecimalAsString(val, left);
	} else {
		newVal = this.moveDecimalAsString(val, left, places);
	}
	
	return parseFloat(newVal);
}


function getRoundedNF(val)
{
	val = this.moveDecimalRight(val);
	val = Math.round(val);
	val = this.moveDecimalLeft(val);
	
	return val;
}


function preserveZerosNF(val)
{
	var i;

	// make a string - to preserve the zeros at the end
	val = this.expandExponential(val);
	
	if (this.places <= 0) return val; // leave now. no zeros are necessary - v1.0.1 less than or equal
	
	var decimalPos = val.indexOf('.');
	if (decimalPos == -1) {
		val += '.';
		for (i=0; i<this.places; i++) {
			val += '0';
		}
	} else {
		var actualDecimals = (val.length - 1) - decimalPos;
		var difference = this.places - actualDecimals;
		for (i=0; i<difference; i++) {
			val += '0';
		}
	}
	
	return val;
}


function justNumberNF(val)
{
	newVal = val + '';
	
	var isPercentage = false;
	
	// check for percentage
	// v1.5.0
	if (newVal.indexOf('%') != -1) {
		newVal = newVal.replace(/\%/g, '');
		isPercentage = true; // mark a flag
	}
		
	// Replace everything but digits - + ( ) e
	var re = new RegExp('[^\\' + this.inputDecimalValue + '\\d\\-\\+\\(\\)e]', 'g');		
	newVal = newVal.replace(re, '');
	// Replace the first decimal with a period and the rest with blank
	// The regular expression will only break if a special character
	//  is used as the inputDecimalValue
	//  e.g. \ but not .
	// By calling test, it will fill RegExp.leftContext et al
	// The leftContext is what's to the left of the first match
	// Search again in what's in the rightContext
	var tempRe = new RegExp('[' + this.inputDecimalValue + ']', 'g');
	if (tempRe.test(newVal)) {
		newVal = RegExp.leftContext + this.PERIOD + RegExp.rightContext.replace(tempRe, '');
	}
	
	// If negative, get it in -n format
	if (newVal.charAt(newVal.length - 1) == this.DASH ) {
		newVal = newVal.substring(0, newVal.length - 1);
		newVal = '-' + newVal;
	}
	else if (newVal.charAt(0) == this.LEFT_PAREN
	 && newVal.charAt(newVal.length - 1) == this.RIGHT_PAREN) {
		newVal = newVal.substring(1, newVal.length - 1);
		newVal = '-' + newVal;
	}
	
	newVal = parseFloat(newVal);
	
	if (!isFinite(newVal)) {
		newVal = 0;
  }

  if (isPercentage) {
  	newVal = this.moveDecimalLeft(newVal, 2);
  }
		
	return newVal;
}

//format angka================================================
function _formatted(x,y,dec){ //x is string eg._formatted('123')
						  //y is decimal eg _formatted(2342.234)
						  //y or x can be blank from caller source
						  //call this with _formatted(source obj)
	var numberTest = new NumberFormat(parseFloat(x.value),y);
	//numberTest.setCurrency(true);
	numberTest.setCommas(true);
	if(typeof dec=='undefined') {
		numberTest.setPlaces(2);
	} else {
		numberTest.setPlaces(dec);
	}
	//var POUND = unescape('%A3');
	//numberTest.setCurrencyPrefix(POUND);
	return(numberTest.toFormatted());
}

function conv_to_dec(x)
{
	temp=x.replace(',','');
	if(temp.indexOf('.')>-1)
		return parseFloat(temp);
	else
		return parseInt(temp);
}
//=========================================================================================
/**
 * @uthor antonius louis
 *Juhar, Indonesia
 * http://  
 *+(62) 081311351132
 */
//Rupiah dalam characterSet
//panggil load_rupiah(obj,tujuan,e) untuk menggunakan 
function load_rupiah(obj,tujuan,e)//e adalah event, tujuan adalah tempat text akan dirampilkan
{								  //obj adalah nama object yang menyimpan angka/number	
	
	tombol=getKey(e);
	if(tombol==13)
	{
		rupiahkan(obj,tujuan);
	}
	else if(angka_doang(e))
	{
		return true;
	}
	else
	{
	 return false;
	}
}
//==================================
function rupiahkan(obj,tujuan)
{
        after='';
        nilai = obj.value; 
	   while(nilai.indexOf(",")>-1)
	   {
	   	nilai=nilai.replace(",","");
	   }		
        coma=nilai.length ;
        if(nilai.lastIndexOf('.')>0)
        {
                after=nilai.substr(nilai.lastIndexOf('.')+1,nilai.length);
                c=nilai.substr(0,nilai.lastIndexOf('.'));
        }
        else
        c=nilai.substr(0,coma);

	output=document.getElementById(tujuan);
	var angka=new Array();
	angka[0]='Nol';	
	angka[1]='Satu';
	angka[2]='Dua';
    angka[3] ='Tiga';
	angka[4]='Empat';
	angka[5]='Lima';
	angka[6]='Enam';
	angka[7]='Tujuh';
	angka[8]='Delapan';
	angka[9]='Sembilan';
	tval='';
	mval='';
	jval='';
	rval='';
	raval='';
	tex='';
      say_after='';

if(after.length>0)
{
for(h=0;h<=after.length-1;h++)
{
   _o=parseInt(after.substr(h,1));
   if(h==0)
   say_after+=' koma '+angka[_o];
   else
   say_after+=' '+angka[_o];
      
}
}
//999.999.999.999.999	
//123 456 789 012 345
//012 345 678 901 234

 panjang=c.length;

t=false;
m=false;
j=false;
r=false;
ra=false;
if(panjang<4)
{
	ra=true;
}
else if(panjang<7)
{
	r=true;
}
else if(panjang<10)
{
	j=true;
}
else if(panjang<13)
{
	m=true;
}
else if(panjang<16)
{
	t=true;
}


	raval=_ra();
	rval=_r();
	jval=_j();
	mval=_m();
  	tval=_t();
	
loadgroup();	

function _t()
{
	if(panjang==15)   
       tval=nilai.substr(0,3);
	else if(panjang==14)
	   tval=nilai.substr(0,2);
	else if(panjang==13)
	   tval=nilai.substr(0,1);   
return tval;	   
}
function _m()
{
	if (panjang==15)
	   mval=nilai.substr(3,3);
	if (panjang==14)
	   mval=nilai.substr(2,3);
	if (panjang==13)
	   mval=nilai.substr(1,3);
	if (panjang==12)
	   mval=nilai.substr(0,3);
	if (panjang==11)
	   mval=nilai.substr(0,2);
	if (panjang==10)
	   mval=nilai.substr(0,1);	   	   	     
return mval;	        
}

function _j()
{
	if (panjang==15)
	   jval=nilai.substr(6,3);
	if (panjang==14)
	   jval=nilai.substr(5,3);
	if (panjang==13)
	   jval=nilai.substr(4,3);
	if (panjang==12)
	   jval=nilai.substr(3,3);
	if (panjang==11)
	   jval=nilai.substr(2,3);
	if (panjang==10)
	   jval=nilai.substr(1,3);
	if (panjang==9)
	   jval=nilai.substr(0,3);
	if (panjang==8)
	   jval=nilai.substr(0,2);
	if (panjang==7)
	   jval=nilai.substr(0,1);		   	   	            
return jval;
}

function _r()
{
	if (panjang==15)
	   rval=nilai.substr(9,3);
	if (panjang==14)
	   rval=nilai.substr(8,3);
	if (panjang==13)
	   rval=nilai.substr(7,3);
	if (panjang==12)
	   rval=nilai.substr(6,3);
	if (panjang==11)
	   rval=nilai.substr(5,3);
	if (panjang==10)
	   rval=nilai.substr(4,3);
	if (panjang==9)
	   rval=nilai.substr(3,3);
	if (panjang==8)
	   rval=nilai.substr(2,3);
	if (panjang==7)
	   rval=nilai.substr(1,3);
	if (panjang==6)
	   rval=nilai.substr(0,3);
	if (panjang==5)
	   rval=nilai.substr(0,2);
	if (panjang==4)
	   rval=nilai.substr(0,1);				       
return rval;
}
function _ra()
{
	if (panjang==15)
	   raval=nilai.substr(12,3);
	if (panjang==14)
	   raval=nilai.substr(11,3);
	if (panjang==13)
	   raval=nilai.substr(10,3);
	if (panjang==12)
	   raval=nilai.substr(9,3);
	if (panjang==11)
	   raval=nilai.substr(8,3);
	if (panjang==10)
	   raval=nilai.substr(7,3);
	if (panjang==9)
	   raval=nilai.substr(6,3);
	if (panjang==8)
	   raval=nilai.substr(5,3);
	if (panjang==7)
	   raval=nilai.substr(4,3);
	if (panjang==6)
	   raval=nilai.substr(3,3);
	if (panjang==5)
	   raval=nilai.substr(2,3);
	if (panjang==4)
	   raval=nilai.substr(1,3);
	if (panjang==3)
	   raval=nilai.substr(0,3);
	if (panjang==2)
	   raval=nilai.substr(0,2);
	if (panjang==1)
	   raval=nilai.substr(0,1);						
         
return raval;
}

function loadgroup()
{
  if(t)
  {
  	tex=ratusan(tval,' TRILIUN ');
	tex+=ratusan(mval,' MILIAR ');
	tex+=ratusan(jval,' JUTA ');
	tex+=ratusan(rval,' RIBU ');
	tex+=ratusan(raval,'');
    	         
  }
  else if(m)
  {
	tex+=ratusan(mval,' MILIAR ');
	tex+=ratusan(jval,' JUTA ');
	tex+=ratusan(rval,' RIBU ');
	tex+=ratusan(raval,'');
  }	
  else if(j)
  {
	tex+=ratusan(jval,' JUTA ');
	tex+=ratusan(rval,' RIBU ');
	tex+=ratusan(raval,'');
  }
  else if(r)
  {
	tex+=ratusan(rval,' RIBU ');
	tex+=ratusan(raval,'');
  }
 else if(ra)
  {
	tex=ratusan(raval,'');
  }  
}

function ratusan(nx,group)
{
switch (nx.length)
{
	case 2:
	    if(nx.substr(0,1)=='0')
		nx=nx.substr(1,1);
	break;
	case 3:
	   if(nx.substr(0,1)=='0')
	      nx=nx.substr(1,2);
	   if(nx.substr(0,1)=='0')
	      nx=nx.substr(1,1);
	break;
}
panj=nx.length;


if(panj==3)
{
	ix=angka[parseInt(nx.substr(0,1))];
	if(ix=='SATU')
	r1='SERATUS';
	else if(ix=='NOL')
	r1='';
	else
	r1=ix+' RATUS';

	i0=angka[nx.substr(1,1)];
	i1=angka[nx.substr(2,1)];
	if(i0=='NOL' && i1=='NOL')
	 PULUH=r1;
	else if(i0=='SATU')
	{
		if(i1=='NOL')
		PULUH=r1+' SEPULUH';
		else if(i1=='SATU')
		PULUH=r1+' SEBELAS';
		else
		PULUH=r1+' '+i1+' BELAS';
	}	
	else if(i1=='NOL')
	PULUH=r1+' '+i0+' PULUH ';
	else if(i0=='NOL' && i1!='NOL')
	PULUH=r1+' '+i1;	
	else
	PULUH=r1+' '+i0+' PULUH '+i1;
		
if(ix=='SATU' && i0=='NOL' && i1=='NOL')
	PULUH='SERATUS';
	
PULUH+=group;

return PULUH;	
}
    
if(panj==2)
{
	i0=angka[nx.substr(0,1)];
	i1=angka[nx.substr(1,1)];
	if(i0=='NOL' && i1=='NOL')
	{
	 PULUH='';	
	}
	else if(i0=='SATU')
	{
		if(i1=='NOL')
		PULUH='SEPULUH';
		else if(i1=='SATU')
		PULUH='SEBELAS';
		else
		PULUH=i1+'BELAS';
	}	
	else if(i1=='NOL')
	PULUH=i0+' PULUH ';
	else
	PULUH=i0+' PULUH '+i1;
	
PULUH+=group;
return PULUH;		
}
   if(panj==1)
    {
		PULUH=angka[parseInt(nx)];
	 if(nx=='0')
       return '';
    else
     {
	   if(PULUH=='SATU' && group==' RIBU ')
	     PULUH='SERIBU '
	   else	 
	   PULUH+=group;
	   	
       return PULUH;
	 }  
	}	

}
    try {
        if (tex.length > 0) {
            output.innerHTML = tex + say_after;
            output.value = tex + say_after;
        }
        else {
            output.innerHTML = tex;
            output.value = tex;
        }
    }
catch(x){alert('Enter some number!');}
}
//==================================================================================

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
function logout(){
	
	param='';
	post_response_text('logout.php', param, respog);
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						window.location='login.php'; 
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}	
			}
		}				
	
}
//+++tab controller++++++++++++++++++++++++++++++++++
activeTab='tab0';
function tabAction(cur,numactive,tabID,max)
{
	try {
		for (x = 0; x <= max; x++) {
			document.getElementById('tab'+tabID + x).style.backgroundImage = 'url(images/tab1.png)';
			document.getElementById('tab'+tabID  + x).style.color = '#333333';
			document.getElementById('tab'+tabID  + x).style.fontWeight = 'normal';
			document.getElementById('content'+tabID  + x).style.display = 'none'
		}
		cur.style.backgroundImage = 'url(images/tab2.png)';
		cur.style.color = '#dedede';
		cur.style.fontWeight = 'bolder';
		activeTab = 'tab'+tabID  + numactive;
		document.getElementById('content'+tabID  + numactive).style.display = '';
	}
	catch(e)
	{
		alert(e.toString()+"\nMaybe Tab's component not loaded correctly");
		
	}
}
function chgBackgroundImg(obj,img,color)
{
	if (obj.id != activeTab) {
		obj.style.backgroundImage = 'url(' + img + ')';
		obj.style.color=color;
	}
}
//+++++++++++++++++++++++++++++++++++++++++++++++++++
function verify()
{
	/*if(!window.top.left)
	{
                   // alert('You may follow the system flow')
                    window.location='logout.php';
                    return false;
	}
                    else{*/
                            return true;
                        //}
	//reminder dimatikan dan akan berjalan setelah dibuka mini windownya.
	//startReminder();
	//createMiniWin();
}

function isSaveResponse(txt)
{
	txt=txt.toUpperCase();
	if (txt.lastIndexOf('GAGAL') > -1 || txt.lastIndexOf('ERROR') > -1 || txt.lastIndexOf('WARNING') > -1)
      return false
	else
	  return true;  
}

function isSaveResponse2(txt)
{
	txt=txt.toUpperCase();
	if (txt.lastIndexOf('G4G4L') > -1 || txt.lastIndexOf('ERR0R') > -1 || txt.lastIndexOf('W4RN1NG') > -1)
      return false
	else
	  return true;  
}

function emailCheck (emailStr) {

		/* The following variable tells the rest of the function whether or not
		to verify that the address ends in a two-letter country or well-known
		TLD.  1 means check it, 0 means don't. */
		
		var checkTLD=1;
		
		/* The following is the list of known TLDs that an e-mail address must end with. */
		
		var knownDomsPat=/^(com|net|org|edu|int|mil|gov|arpa|biz|aero|name|coop|info|pro|museum)$/;
		
		/* The following pattern is used to check if the entered e-mail address
		fits the user@domain format.  It also is used to separate the username
		from the domain. */
		
		var emailPat=/^(.+)@(.+)$/;
		
		/* The following string represents the pattern for matching all special
		characters.  We don't want to allow special characters in the address. 
		These characters include ( ) < > @ , ; : \ " . [ ] */
		
		var specialChars="\\(\\)><@,;:\\\\\\\"\\.\\[\\]";
		
		/* The following string represents the range of characters allowed in a 
		username or domainname.  It really states which chars aren't allowed.*/
		
		var validChars="\[^\\s" + specialChars + "\]";
		
		/* The following pattern applies if the "user" is a quoted string (in
		which case, there are no rules about which characters are allowed
		and which aren't; anything goes).  E.g. "jiminy cricket"@disney.com
		is a legal e-mail address. */
		
		var quotedUser="(\"[^\"]*\")";
		
		/* The following pattern applies for domains that are IP addresses,
		rather than symbolic names.  E.g. joe@[123.124.233.4] is a legal
		e-mail address. NOTE: The square brackets are required. */
		
		var ipDomainPat=/^\[(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})\]$/;
		
		/* The following string represents an atom (basically a series of non-special characters.) */
		
		var atom=validChars + '+';
		
		/* The following string represents one word in the typical username.
		For example, in john.doe@somewhere.com, john and doe are words.
		Basically, a word is either an atom or quoted string. */
		
		var word="(" + atom + "|" + quotedUser + ")";
		
		// The following pattern describes the structure of the user
		
		var userPat=new RegExp("^" + word + "(\\." + word + ")*$");
		
		/* The following pattern describes the structure of a normal symbolic
		domain, as opposed to ipDomainPat, shown above. */
		
		var domainPat=new RegExp("^" + atom + "(\\." + atom +")*$");
		
		/* Finally, let's start trying to figure out if the supplied address is valid. */
		
		/* Begin with the coarse pattern to simply break up user@domain into
		different pieces that are easy to analyze. */
		
		var matchArray=emailStr.match(emailPat);
		
		if (matchArray==null) {
		
		/* Too many/few @'s or something; basically, this address doesn't
		even fit the general mould of a valid e-mail address. */
		
		alert("Email salah");
		return false;
		}
		var user=matchArray[1];
		var domain=matchArray[2];
		
		// Start by checking that only basic ASCII characters are in the strings (0-127).
		
		for (i=0; i<user.length; i++) {
		if (user.charCodeAt(i)>127) {
		alert("Email mengandung karakter yang dilarang");
		return false;
		   }
		}
		for (i=0; i<domain.length; i++) {
		if (domain.charCodeAt(i)>127) {
		alert("Email mengandung karakter yang dilarang.");
		return false;
		   }
		}
		
		// See if "user" is valid 
		
		if (user.match(userPat)==null) {
		
		// user is not valid
		
		alert("Username pada email tidak valid.");
		return false;
		}
		
		/* if the e-mail address is at an IP address (as opposed to a symbolic
		host name) make sure the IP address is valid. */
		
		var IPArray=domain.match(ipDomainPat);
		if (IPArray!=null) {
		
		// this is an IP address
		
		for (var i=1;i<=4;i++) {
		if (IPArray[i]>255) {
		alert("IP address salah!");
		return false;
		   }
		}
		return true;
		}
		
		// Domain is symbolic name.  Check if it's valid.
		 
		var atomPat=new RegExp("^" + atom + "$");
		var domArr=domain.split(".");
		var len=domArr.length;
		for (i=0;i<len;i++) {
		if (domArr[i].search(atomPat)==-1) {
		alert("Domain pada alamat email salah.");
		return false;
		   }
		}
		
		/* domain name seems valid, but now make sure that it ends in a
		known top-level domain (like com, edu, gov) or a two-letter word,
		representing country (uk, nl), and that there's a hostname preceding 
		the domain or country. */
		
		if (checkTLD && domArr[domArr.length-1].length!=2 && domArr[domArr.length-1].search(knownDomsPat)==-1) {
		alert("Email harus diakhiri dengan domain yang benar");
		return false;
		}
		
		// Make sure there's a host name preceding the domain.
		
		if (len<2) {
		alert("Hostname pada alamat email salah!");
		return false;
		}
		
		// If we've gotten this far, everything's valid!
		return true;
}

function closeDetail(){
    document.getElementById('dynamic').innerHTML = '';
    document.getElementById('dynamic').style.display = 'none';
}

function showDialog1(title,content,width,height,ev,fullPoint)
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
	   	//c.style.top = '120px';
		c.style.top = '150px';
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
		document.getElementById('dynamic1').style.top = (parseInt(pos[1])+5) + 'px';
		if((typeof fullPoint != 'undefined') && fullPoint==true) {
			document.getElementById('dynamic1').style.left = (parseInt(pos[0])-width-5)+'px';
		} else {
			document.getElementById('dynamic1').style.left = '75px';
		}
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
		if (typeof object.value === 'undefined') {
			return '';
		}
		
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
			//alert('Nominal salah');
			object.focus();
		}		
	}
}

function remove_comma(object){//object adalah textbox atau componen yang memiliki atRIBUt 'value'
	if( object === null){
		return '';
	}
	x = object.value;
	while (x.indexOf(",") > -1) {
		x = x.replace(",", "");
	}
	return x;
}

function htmlEntities(str) {
    return String(str).replace(/&amp;/g, '&').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
function remove_comma_var(nilai){//nilai adalah string yang bisa berupa 9,001.50 atau 9,0000
	while (nilai.indexOf(",") > -1) {
		nilai = nilai.replace(",", "");
	}
	return nilai;
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

function getOptionsValue(zObj) {
	if(zObj.options) {
		return zObj.options[zObj.selectedIndex].value;
	} else {
		return false;
	}
	
}

function getOptionsText(zObj) {
	if(zObj.options) {
		return zObj.options[zObj.selectedIndex].text;
	} else {
		return false;
	}
}
//===============================
//====================================khusus untuk reminder dan chat
function post_param(tujuan,param,functiontoexecute)
{

zz=verify();
    if(zz){
        par=parent.location.href.replace("http://","");
        param+='&par='+par;
	con.open("POST", tujuan, true);
	con.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	con.setRequestHeader("Content-length", param.length);
	con.setRequestHeader("Connection", "close");
	
	con.onreadystatechange = eval(functiontoexecute);
	con.send(param);
    }
    else
    window.location='logout.php';
    
}
/*
//==================================================================
//parameter reminder
var mainReminder;
var blink;
var lastMess;
var idle=1;//is idle
function startReminder(){

	//default looping 
	//1000 adalah 1 detik(a secon)
    interval=2000;
	mainReminder= window.setInterval("getReminderData()",interval);
}

function getReminderData()
{
	reminderSlave='masterReminder.php';
	container=document.getElementById('warningContainer');
	
	//cek apakah sedang terbuka atau tidak
	//jika terbuka tunda request
	xyz=document.getElementById('miniWin');
	if (xyz.style.display == '') {
	//do nothisng
	}
	else {
		if (idle == 0) {
		//if prev query has not response than wait
		}
		else {
			//post request
			post_param(reminderSlave, '', respot);
			idle = 0;//waiting for response;
		}
	}
	    function respot() {
        if (con.readyState == 4) {
            if (con.status == 200) {
				idle=1;//set idle=true
                if (!isSaveResponse(con.responseText)) {
                    container.innerHTML='Reminder Error';
					container.style.color='darkred';
					alert(con.responseText);
                } else {
                     if(trim(con.responseText)=='')
                      {
						container.style.color='#ffffff';
						container.style.weight='normal';
					  	container.innerHTML='Reminder System';
						document.getElementById('messContainer').innerHTML='No Message';					    
						window.clearInterval(blink);
						subj=document.getElementById('warningContainer');
	     				subj.style.backgroundColor='#D3DAED';						
					  }
					  else
					  {
						container.style.weight='bolder';
						container.innerHTML="You've Got Message";
						if(con.responseText!=lastMess)
						{
							container.style.color='blue';
							fillMiniWin(con.responseText);
						} 
						//jika data masih sama dengan yang sebelumnya maka diabaikan
		               lastMess=con.responseText;
					  }
                }
            } else {
                error_catch(con.status);
            }
        }
    }
}

function fillMiniWin(cont)
{
	   document.getElementById('messContainer').innerHTML=cont;	
	   blink=window.setInterval("blinkReminder()",60000);
}
function createMiniWin()
{
       if (document.getElementById('miniWin')) {
		c.style.width = '150px';
	   }
	   else {
	   	c = document.createElement('div');
	   	c.setAttribute('id', 'miniWin');
	   	c.setAttribute('class', 'miniwin');
	   	c.style.position = 'fixed';
		c.style.display='none';
	   	c.style.left = '5px';
	   	c.style.bottom = '5px';
		c.style.width = '250px';
		c.style.height = '150px';
		c.style.backgroundColor='#FFFFFF';
	   	c.style.zIndex = 1001;
		c.style.border ='black solid 1px';
	   	document.body.appendChild(c);
	   }	
	   cont="<div windth=100% height=20px style='background-color:#000000;'>";
	   cont+="<table width=100%><tr><td align=left style='color:white;'>Last Message:</td>";
	   cont+="<td align=right><span style='cursor:pointer;color:white;' onclick=minimizeMiniwin()>X</span>";
	   cont+="</td></tr></table></div><div id=messContainer style='overflow:scroll;height:120px;'>No Message</div>";
	   c.innerHTML=cont;
}

function minimizeMiniwin()
{
	document.getElementById('miniWin').style.display='none';
    window.clearInterval(blink);
	window.clearInterval(mainReminder);
	}
function displayMiniWin()
{
	document.getElementById('miniWin').style.display='';
    startReminder();
	createMiniWin();
	subj=document.getElementById('warningContainer');
	subj.style.color='#ffffff';
	subj.style.weight='normal';
	subj.style.backgroundColor='#D3DAED';
}

function blinkReminder()
{
	subj=document.getElementById('warningContainer');
	if(subj.style.backgroundColor=='orange')
	     subj.style.backgroundColor='#D3DAED';
	else
	      subj.style.backgroundColor='orange';	 
}
*/

/** New Additional Function - Adi */
zNew = {
	setNum: function() {
		for(i in document.getElementsByTagName('input')) {
			var el = document.getElementsByTagName('input')[i];
			if(typeof el.className!='undefined') {
				if (el.className.search('myinputtextnumber') != -1) {
					el.setAttribute('onmousemove','zNew.zeroEmpty(this)');
					//console.log(el);
				}
			}
		}
	},
	zeroEmpty: function(el) {
		if(el.value==0) {el.value='';}
	}
};