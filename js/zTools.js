/** z Class */
z = {
	elSearch: function (id,ev,parent) {
		var cont = "<div><input id='elSearchBox' class=myinputtext type=text onkeypress='var tmp=getKey(event);if(tmp==13){z.doSearch(\""+id+"\"",
			popup = document.getElementById('dynamic1'),el;
		if(typeof parent != 'undefined') {
			cont += ',"'+parent+'"';
			el = getById(parent).firstChild;
		} else {
			el = getById(id);
		}
		cont +=");}'><button class=mybutton onclick='z.doSearch(\""+id+"\""
		if(typeof parent != 'undefined') {
			cont += ',"'+parent+'"';
		}
		cont +=")'>Search</button></div>";
		if(el.disabled==false) {
			cont += "<fieldset><legend>Result</legend><div id='elSearchResult' "+
				"style='max-height: 250px;overflow-y: scroll;'><div></fieldset>";
			if(popup)
				closeDialog();
			showDialog1('Find '+id,cont,'250','300',ev,true);
		}
	},

	doSearch: function(id,parent) {
		var el,
			result = getById('elSearchResult'),
			query = getById('elSearchBox').value,
			tmpStr;
		tmpStr = '<table class=sortable>';
		tmpStr += '<thead><tr class=rowheader><td>Kode</td><td>Nama</td></tr></thead>';
		tmpStr += '<tbody>';
		if(typeof parent != 'undefined') {
			el = getById(parent).firstChild;
		} else {
			el = getById(id)
		}
		for(i in el.options) {
			var elText = el.options[i].text,
				elValue = el.options[i].value,
				show = false;
			if(elText) {
				elText = elText.toLowerCase();
				if(elText.search(query.toLowerCase())>-1) {
					show = true;
				}
			}
			if(elValue) {
				elValue = elValue.toLowerCase();
				if(elValue.search(query.toLowerCase())>-1) {
					show = true;
				}
			}

			if(show) {
				tmpStr += "<tr class=rowcontent onclick='z.passParam(\""+id+"\",\""+el.options[i].value+"\"";
				if(typeof parent != 'undefined') {
					tmpStr += ',"'+parent+'"';
				}
				tmpStr += ")'><td>"+el.options[i].value+"</td><td>"+el.options[i].text+"</td></tr>";
			}
		}
		tmpStr += '</tbody></table>';
		result.innerHTML = tmpStr;
	},

	passParam: function(id,value,parent) {
		var el;
		if(typeof parent != 'undefined') {
			el = getById(parent).firstChild;
		} else {
			el = getById(id)
		}
		if(el.disabled==false) {
			for(i in el.options) {
				if(el.options[i].value==value) {
					el.selectedIndex = i;
					if ("createEvent" in document) {
						var evt = document.createEvent("HTMLEvents");
						evt.initEvent("change", false, true);
						el.dispatchEvent(evt);
					}
					else
						el.fireEvent("onchange");
					closeDialog();return;
				}
			}
		}
	},

	/** Number Format for onkeyup, check decimal point up to 2 digit */
	numberFormat: function(id, dec) {
		var el = getById(id),val;
		el.value=remove_comma(el);
		val = el.value;
		if(typeof dec=='undefined') dec=2;

		if(val.search(/\./)>-1) {
			var tmp = val.split('.');
			if(tmp[1].length>0) {
				if(tmp[1].length>dec) {
					el.value = _formatted(el,null,dec);
				} else {
					el.value = _formatted(el,null,tmp[1].length);
				}
			}
		} else {
			el.value = _formatted(el,null,0);
		}
	},

	/** Manual Event Trigger
	 * @param	element/string	el		Registered Element or Element Id
	 * @param	string			type	Event to be triggered
	 */
	trigger: function(el, type) {
		if(typeof el=='string') {
			el = document.getElementById(el);
		}

		if ("createEvent" in document) {
			var evt = document.createEvent("HTMLEvents");
			evt.initEvent(type, false, true);
			el.dispatchEvent(evt);
		}
		else
			el.fireEvent("on"+type);
	},

	/** Check if element has class */
	hasClass: function(element, className) {
		return element.className && new RegExp("(^|\\s)" + className + "(\\s|$)").test(element.className);
	}
};

/* Function autoFill
 * Fungsi untuk mengisi element dengan suatu nilai
 * I : element,nilai
 * O : element dengan value val
 */
function autoFill(id,val) {
	// Check if element exist
	if(!id) {
		alert('DOM Definition Error');
		exit;
	}

	if(id.options) {
		// Options Element
		var index = 0;
		for(i=0;i<id.options.length;i++) {
			if(id.options[i].value==val) {
				id.selectedIndex = i;
				break;
			}
		}
	} else if(id.getAttribute('type')=='checkbox') {
		// Options Checkbox
		if(val==0) {
			id.checked = true;
		} else {
			id.checked = false;
		}
	} else {
		// Options Text
		id.value = val;
	}
}

/* Function getValue
 * Fungsi mengambil nilai suatu element
 * I : id element
 * O : nilai
 */
function getValue(id) {
	var tmp = document.getElementById(id);

	if(tmp==null) {
		//alert("DOM Definition Error : "+id);
		return '';
	}
	if(tmp.getAttribute('type')=='checkbox') {
		if(tmp.checked==true) {
			return 1;
		} else {
			return 0;
		}
		// } else if(tmp.options) {
	} else  if(tmp.tagName=='SELECT') {
		var val ='';
		for(a=0;a<tmp.length;a++) {
			if (tmp.options[a].selected == true) {
				val = tmp.options[a].value;
				break;
			}
		}
		return  val;
		// return tmp.options[tmp.selectedIndex].value;
	}
	// else if(tmp.getAttribute('type')=='text') {
	else if(tmp.tagName=='INPUT') {
		// console.log(id + ' ' + tmp.value);
		return tmp.value;
	}
	else if(tmp.getAttribute('type')=='textarea') {
		return tmp.value;
	}
	else if(tmp.getAttribute('type')=='button') {
		return tmp.value;
	}
	else if(tmp.hasAttribute('value')) {
		if(tmp.getAttribute('value')!='') {
			return tmp.getAttribute('value');
		} else {
			return tmp.value;
		}
	} else {
		if(tmp.innerHTML!='')
		{return tmp.innerHTML;}
		else
		{return tmp.value;	}
	}
}


/* Function getAttr
 * Fungsi mengambil value attribute dari object
 * I : id element
 * O : nilai
 */
function getAttr(id, attrName) {
	return getById(id).getAttribute(attrName);
}

/* Function getInner
 * Fungsi mengambil innerHTML dari object
 * I : id element
 * O : nilai
 */
function getInner(id) {
	return getById(id).innerHTML;
}

/* Function getById
 * Fungsi mengambil object berdasar ID
 * I : id element
 * O : object
 */
function getById(id) {
	var el = document.getElementById(id);
	if(el) {
		return el;
	} else {
		if(console) {
			console.log("DOM Definition Error: "+id);
		} else {
			alert("DOM Definition Error: "+id);
		}
	}
}
