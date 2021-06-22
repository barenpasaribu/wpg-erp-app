function createLI(parent,orgs) {
	var el = parent == '' ? document.getElementById('org_' + parent) : document.getElementById('div_org_' + parent);
	if (el != null) {
		var li = "";
		for (var i = 0; i < orgs.length; i++) {
			var org = orgs[i];
			li = li + "<li class='mmgr'> " +
				"<img title='expand' class='arrow' src='images/foldc_.png'  height='22px' onclick=\"getOrgChilds(this,'div_org_" + org.kodeorganisasi + "','" + org.kodeorganisasi + "');\"> " +
				"<b class='elink' id='el" + org.kodeorganisasi + "' style='height:22px;font:20' " +
				"	onclick=\"showOrg('"+parent+"','" + org.kodeorganisasi + "','" + org.namaorganisasi + "');\"  style='height:22px' title='Click to change'>" + org.kodeorganisasi + ": " + org.namaorganisasi + "</b> " +
				"<ul id=ul_org_" + org.kodeorganisasi + " style='display:none'>" +
				"	<div childcount='0' id=div_org_" + org.kodeorganisasi + (parent==''? " style='width:500px;height:500px;overflow:scroll;'" :'') + " ></div>  " +
				"</ul></li> ";
		}
		li = li + addButtonOrg(parent);
		el.innerHTML = li;
	}
};

function addButtonOrg(parent) {
	var caption = (parent=='' ? 'Tambah': 'Tambah Sub '+parent);
	var li = "<li id='li_"+parent+"' class='mmgr'> " +
		"<img  class='elink' title='Create Child' src='images/plus.png' " +
		"style='width:18px;height:18px;cursor:pointer' " +
		"onclick=\"showOrg('"+parent+"','','');\"> " +
		"<b class='elink'   style='height:22px;font:20' " +
		"	onclick=\"showOrg('"+parent+"','');\"  style='height:22px' title='Tambah Sub "+parent+"'>"+caption+"</b> "
		"</li>";
	return li;
};
var orgRows=0;
var isAdd=false;
var codeExist=false;
function orgList(sender,datas,currentpage) {
	orgRows = datas.datas;
	createLI(datas.parent, datas.datas);
};
function getOrgChilds(obj,divContainer,code){
	var div = document.getElementById(divContainer);
	var title = obj.getAttribute('title');
	var ul =  document.getElementById('ul_org_'+code);
	if (title!=null) {
		if (title=='expand') {
			if (div != null) {
				// var count = Number(div.getAttribute('childcount'));
				// get data from server if 0
				// if (count == 0) {
					getData(ConstructPage,code);
				// }
				obj.setAttribute('title', 'Collaps');
				obj.setAttribute('src', 'images/foldo.png');
				if (ul!=null){
					ul.removeAttribute('style');
				}
			}
		} else {
			if (ul!=null){
				ul.setAttribute('style', 'display:none');
			}
			obj.setAttribute('title', 'expand');
			obj.setAttribute('src', 'images/foldc_.png');
		}
	}
}
function getData(obj,parent){
	obj.listSection.init();
	obj.listSection.onListDataReady = orgList;
	obj.listSection.onReadyStateChange = obj.listSection.dataReadyState;
	obj.listSection.global.ajaxClass.ajaxFunc(obj.listSection, 'get', obj.crudUrl.listrow+'&parent='+parent, '');
};
function showOrg(parent,code,nama){
	isAdd=(code=='');
	ConstructPage.entrySection.init();
	ConstructPage.entrySection.prepareSetEntry();
	ConstructPage.entrySection.onPrepareEntry = prepareEntryForm;
	ConstructPage.entrySection.onResetElement = resetElement;
	ConstructPage.entrySection.global.onAfterExecute = entryAfterExecute;
	ConstructPage.entrySection.global.initGlobalProperty('', true,
		ConstructPage.crudUrl.edit+'&parent=' + parent + '&code='+code);
};
function resetElement(entrySection_){
}
function prepareEntryForm(entrySection_,data,columnsize){
	var div = document.getElementById('container');
	if (div!=null){
		div.innerHTML='';
	}
	var w=600;
	var h=420;
	entrySection_.headerElements=[];
	var nama =data.header.data.namaorganisasi==null ?'':data.header.data.namaorganisasi;
	var html=
		"<div id='container'><fieldset>" +
		"	<legend><input type=text  style='width:250px' value='" + nama +  "' readonly=readonly /></legend>" +
		"	<div style='overflow:auto;width="+w+"px;height:"+h+"px;'>" +
		"		<table cellspacing=1 border=0 class=sortable><tbody class=rowcontent>";

	var index=0;
	var isbreak=false;
	var tr='';
	while(true) {
		if (!isbreak) tr = tr + "<tr>";
		for (var x = 0; x < entrySection_.columnSize; x++) {
			isbreak = index > data.header.inputs.length - 1;
			if (isbreak) {
				break;
			}
			var header = data.header.inputs[index];
			entrySection_.headerElements.push(header['field']);
			tr = tr +
				"<td style='padding-right:20px;font-size:12px'>\n" +
				"   <label for='" + header['field'] + "'>" + header['caption'] + "</label>\n" +
				"</td>"
			;
			tr = tr +
				"<td style='padding-right:20px;font-size:12px'>\n";
			for (var i = 0; i <= header['elements'].length - 1; i++) {
				var elements = header['elements'][i];
				tr = tr + entrySection_.createElement(elements);
			}
			tr = tr + "</td>";
			index = index + 1;
		}
		if (!isbreak) tr = tr + "</tr>\r\n";
		if (isbreak) {break;}
	}
	html = html + tr;
	html = html + "</tbody>" +
		"<tfoot><tr><td colspan=3 align=center>" +
		"<button class=mybutton onclick='save()' >Simpan</button></td></tr></tfoot></table></div>";
	var width = w;
	var height =h +30;
	var ev = 'event';
	var title = "Detail Struktur Organisasi";
	ConstructPage.showDialog1(title,html,width,height,ev);
	ConstructPage.entrySection.resetElements(ConstructPage.entrySection.headerElements);
	ConstructPage.entrySection.bindDataToElement(data.header.data);
}
function checkCodeOrg2(data){
	var data__=JSON.parse(data);
	if (!codeExist){
		ConstructPage.saveHeader();
	} else {
		alert("Kode Organisasi sudah ada");
		document.getElementById('kodeorganisasi').focus();
	}
}
function checkCodeOrg(code){
	ConstructPage.entrySection.onReadyStateChange = checkCodeOrg2;
	ConstructPage.entrySection.global.initGlobalProperty('', true,
		ConstructPage.crudUrl.checkcode+'&code='+code);
}
function save(){
	var param = ConstructPage.entrySection.paramKey(ConstructPage.entrySection.headerKeys, null);
	var params = param.split('=');
	if (isAdd) {
		checkCodeOrg(params[1]);
	} else {
		ConstructPage.saveHeader();
	}
};
function entryAfterExecute(obj){
	if (isAdd) {
		var tmp = document.getElementById('tipe');
		if (tmp != null) {
			tmp.selectedIndex = 0;
		}
		tmp = document.getElementById('alokasi');
		if (tmp != null) {
			tmp.selectedIndex = 0;
		}
		tmp = document.getElementById('noakun');
		if (tmp != null) {
			tmp.selectedIndex = 0;
		}
	}
}
function bindDataToElement(obj,org) {
	var el = org.induk == '' ? document.getElementById('org_') : document.getElementById('div_org_' + org.induk);
	if (isAdd && org.kodeorganisasi != null) {
		if (el != null) {
			el.removeChild(el.childNodes[el.childNodes.length - 1]);
			el.innerHTML = el.innerHTML +
				"<li class='mmgr'> " +
				"<img title='expand' class='arrow' src='images/foldc_.png'  height='22px' onclick=\"getOrgChilds(this,'div_org_" + org.kodeorganisasi + "','" + org.kodeorganisasi + "');\"> " +
				"<b class='elink' id='el" + org.kodeorganisasi + "' style='height:22px;font:20' " +
				"	onclick=\"showOrg('" + org.induk + "','" + org.kodeorganisasi + "','" + org.namaorganisasi + "');\"  style='height:22px' title='Click to change'>" + org.kodeorganisasi + ": " + org.namaorganisasi + "</b> " +
				"<ul id=ul_org_" + org.kodeorganisasi + " style='display:none'>" +
				"	<div childcount='0' id=div_org_" + org.kodeorganisasi + (org.induk == '' ? " style='width:500px;height:500px;overflow:scroll;'" : '') + " ></div>  " +
				"</ul></li> "
				+
				addButtonOrg(org.induk);
		}
	}
};
ConstructPage.listSection.showPage=false;
ConstructPage.sectionToBuild={
	build:{
		list:true,
		entry:true,
	}
};
ConstructPage.crudUrl={
	'entry':'main_orgChart.php?proses=entry',
	'list':'main_orgChart.php?proses=list',
	'edit':'main_orgChart.php?proses=edit',
	'listrow':'main_orgChart.php?proses=listrow',
	'saveheader':'main_orgChart.php?proses=saveheader',
	'deleteheader':'main_orgChart.php?proses=deleteheader',
	'checkcode':'main_orgChart.php?proses=checkcode',
};
ConstructPage.onInit=function(obj) {
	getData(obj,'');
	obj.entrySection.columnSize = 1;
	obj.entrySection.entryModule = 'Detail Struktur Organisasi';
	obj.entrySection.headerKeys = {'kodeorganisasi': ''};
	obj.entrySection.onBindDataToElement = bindDataToElement;
}
ConstructPage.init();

//
// /*
//
//
//  * @uthor:anthesis-team@mitraagroservindo.com
// activeOrg='';
//  * Indonesia 2009
//  */
// orgVal   ='';
// clos 	  =1;//this will STOP on the #9th child
// function saveOrg()
// {
// 	_orgcode    = trim(document.getElementById('orgcode').value);
// 	_orgname    = trim(document.getElementById('orgname').value);
// 	_orgtype    = trim(document.getElementById('orgtype').value);
// 	_orgadd     = trim(document.getElementById('orgadd').value);
// 	_orgcity    = trim(document.getElementById('orgcity').value);
// 	_orgcountry = document.getElementById('orgcountry').options[document.getElementById('orgcountry').selectedIndex].value;
// 	_alokasi 	= document.getElementById('alokasi').options[document.getElementById('alokasi').selectedIndex].value;
// 	_noakun 	= document.getElementById('noakun').options[document.getElementById('noakun').selectedIndex].value;
// 	_orgzip     = trim(document.getElementById('orgzip').value);
// 	_orgtelp    = trim(document.getElementById('orgtelp').value);
// 	_detail     = trim(document.getElementById('orgdetail').value);
// //response++++++++++++++++++++++++++++++++++++++++
// 	function respog(){
// 		//save active org on memory incase slow server response
// 		id         = activeOrg;
// 		newCaption = _orgcode;
// 		if(con.readyState==4)
// 		{
// 			if (con.status == 200) {
// 				busy_off();
// 				if (!isSaveResponse(con.responseText)) {
// 					alert('ERROR TRANSACTION,\n' + con.responseText);
// 				}
// 				else {
// 					if (id == 'HQ') {
// 						//just reload when org is HQ
// 						window.location.reload();
// 					}
// 					else if(id.lastIndexOf('_new')>-1)
// 					{
// 						if (clos<9) {
// 							nex=clos+1;
// 							ne = "<li class=mmgr>";
// 							ne += "<img title=expand class=arrow src='images/foldc_.png' height=17px  onclick=show_sub('gr" + _orgcode + "',this);>";
// 							ne += "<a class=elink id='el" + _orgcode + "'  onclick=\"javascript:activeOrg=this.id;orgVal='" + orgVal + "';getCurrent('" + _orgcode + "');setpos('inputorg',event);\">" + _orgcode + "</a>";
// 							ne += "<ul id=gr" + _orgcode + " style='display:none;'>";
// 							ne += "<div id=main" + _orgcode + ">";
// 							ne += "</div>";
// 							ne += "<li class=mmgr>";
// 							ne += "<a id='" + _orgcode + "_new' class=elink title='Create Child'  onclick=\"javascript:orgVal='" + _orgcode + "';clos="+nex+";activeOrg='" + _orgcode + "_new';setpos('inputorg',event);\">New Org<a>";
// 							ne += "</li>";
// 							ne += "</ul>";
// 							ne += "</li>";
// 						}
// 						else
// 						{
// 							ne = "<li class=mmgr>";
// 							ne += "<img title=expand class=arrow src='images/menu/arrow_8.gif'>";
// 							ne += "<a class=elink id='el" + _orgcode + "'  onclick=\"javascript:activeOrg=this.id;orgVal='" + orgVal + "';getCurrent('" + _orgcode + "');setpos('inputorg',event);\">" + _orgcode + "</a>";
// 							ne += "</li>";
// 						}
// 						//alert('main'+orgVal);
// 						document.getElementById('main'+orgVal).innerHTML+=ne;
// 					}
// 					else {
// 						document.getElementById(id).innerHTML = newCaption;
// 						clearForm();
// 					}
// 					hideById('inputorg');
// 					clearForm();
// 				}
// 			}
// 			else {busy_off();error_catch(con.status);}
// 		}
// 	}
// //++++++++++++++++++++++++++++++++++++++++++++++++
//
// 	if(_orgcode.length==0 || _orgname.length==0)
// 	{
// 		alert('Org. Code and Org.Name is NULL');
// 	}
// 	else
// 	{
// 		if(confirm('Save new Organization, Are you sure..?'))
// 		{
// 			param ='parent='	+orgVal;
// 			param+='&orgcode='	+_orgcode;
// 			param+='&orgname='	+_orgname;
// 			param+='&orgtype='	+_orgtype;
// 			param+='&orgadd='	+_orgadd;
// 			param+='&orgcity='	+_orgcity;
// 			param+='&orgcountry='+_orgcountry;
// 			param+='&orgzip='	+_orgzip;
// 			param+='&orgtelp='	+_orgtelp;
// 			param+='&orgdetail='+_detail;
// 			param+='&alokasi='+_alokasi;
// 			param+='&noakun='+_noakun;
// 			post_response_text('slave_saveNewOrg.php', param, respog);
// 			//alert(param);
// 		}
// 	}
// }
//
// function clearForm()
// {
// 	document.getElementById('orgcode').value ='';
// 	document.getElementById('orgname').value ='';
// 	document.getElementById('orgtype').value ='';
// 	document.getElementById('orgadd').value  ='';
// 	document.getElementById('orgcity').value ='';
// 	document.getElementById('orgzip').value  ='';
// 	document.getElementById('orgtelp').value ='';
// 	document.getElementById('alokasi').options[0].selected =true;
// 	document.getElementById('noakun').options[0].selected =true;
// }
//
// function saveOrganisasi(inputs){
// 	var param='option=save';//&nopp='+rnopp+'&kolom='+kolom;
//
// 	for (var i=0;i<inputs.length;i++){
// 		param=param + '&' + inputs[i] +'=' + document.getElementById(inputs[i]).value;
// 	}
// 	tujuan='slave_getCurrentOrg.php';
// 	function respog()
// 	{
// 		if(con.readyState==4)
// 		{
// 			if (con.status == 200) {
// 				busy_off();
// 				if (!isSaveResponse(con.responseText)) {
// 					alert('ERROR TRANSACTION,\n' + con.responseText);
// 				}
// 				else {
// 					//alert(con.responseText);
// 					alert(' Sukses simpan data');
// 					return con.responseText;
// 				}
// 			}
// 			else {
// 				busy_off();
// 				error_catch(con.status);
// 			}
// 		}
// 	}
// 	post_response_text(tujuan, param, respog);
// }
// function getCurrent(code,nama) {
// 	param = 'code=' + code;
// 	post_response_text('slave_getCurrentOrg.php', param, respon);
//
// 	function respon() {
// 		if (con.readyState == 4) {
// 			if (con.status == 200) {
// 				busy_off();
// 				if (!isSaveResponse(con.responseText)) {
// 					alert('ERROR TRANSACTION,\n' + con.responseText);
// 				} else {
// 					if (con.responseText != '-1') {
// 						//alert(con.responseText);
// 						fillForm(nama,con.responseText);
// 					} else
// 						clearForm();
// 				}
// 			} else {
// 				busy_off();
// 				error_catch(con.status);
// 			}
// 		}
// 	}
//
// 	function fillForm(nama,arrtex) {
// 		width = '850';
// 		height = '450';
// 		content = "<div id=container></div>";
// 		ev = 'event';
// 		title = "Detail Struktur Organisasi";
// 		showDialog1(title, content, width, height, ev);
// 		var html=
// 			"<fieldset>" +
// 			"	<legend><input type=text id=rnopp name=rnopp style='width:250px' value='" + nama +  "' readonly=readonly /></legend>" +
// 			"	<div style='overflow:auto;width=850px;height:350px;'>" +
// 			"		<table cellspacing=1 border=0 class=sortable><tbody class=rowcontent>";
// 		var data = JSON.parse(arrtex);
// 		var inputs = new Array();
// 		for (var i=0;i<=data.length-1;i++){
// 			html = html + "<tr><td>"+data[i]['caption']+"</td><td>&nbsp;:&nbsp;</td>";
// 			inputs.push(data[i]['id']);
// 			if (data[i]['reference'].length==0){
// 				html = html + "<td><input type=text id='"+data[i]['id']+ "' name='"+data[i]['id']+ "' " + (data[i]['id']=='kodeorganisasi' ? "readonly":"") +
// 					" class=myinputtext style='width:250px'  value='" +data[i]['value']+ "'  /></td>";
// 			} else {
// 				var value =data[i]['value'];
// 				var option = "<select  id='"+data[i]['id']+ "' name='"+data[i]['id']+ "'><option value=''></option>";
// 				for (var j=0;j<=data[i]['reference'].length-1;j++){
// 					var opt = data[i]['reference'][j];
// 					option = option+ "<option value='"+opt["value"]+"' " + (opt["value"]==value ? " selected " : "") + ">"+ opt["caption"]+"</option>";
// 				}
// 				option = option + "</select>";
// 				html = html + "<td>"+option+"</td>";
// 			}
// 		}
// 		html = html + "</tbody>" +
// 			"<tfoot><tr><td colspan=3 align=center>" +
// 			"<button class=mybutton onclick='saveOrganisasi("+JSON.stringify(inputs)+")' >Simpan</button></td></tr></tfoot></table>";
// 		document.getElementById('container').innerHTML=html;
//
//
// 		// arr=arrtex.split('|');
// 		// document.getElementById('orgcode').value =arr[0];
// 		// document.getElementById('orgname').value =arr[1];
// 		// //document.getElementById('orgtype').value =arr[2];
// 		// obj=document.getElementById('orgtype');
// 		// for(xY=0;xY<obj.length;xY++)
// 		// {
// 		// 	if(obj.options[xY].value==arr[2])
// 		// 	{
// 		// 		obj.options[xY].selected=true;
// 		// 	}
// 		// }
// 		// document.getElementById('orgadd').value  =arr[3];
// 		// document.getElementById('orgcity').value =arr[5];
// 		// document.getElementById('orgzip').value  =arr[6];
// 		// document.getElementById('orgtelp').value =arr[4];
// 		// curr=0;
// 		// ctobj=document.getElementById('orgcountry');
// 		// ct=ctobj.length;
// 		// for (x = 0; x < ct; x++) {
// 		// 	if (ctobj.options[x].value == arr[7]) //check if country code is match with option value, then select it
// 		// 		ctobj.options[x].selected=true;
// 		// }
// 		// alobj=document.getElementById('alokasi');
// 		// al=alobj.length;
// 		// for (x = 0; x < al; x++) {
// 		// 	if (alobj.options[x].value == arr[8])
// 		// 		alobj.options[x].selected=true;
// 		// }
// 		// alobj=document.getElementById('noakun');
// 		// al=alobj.length;
// 		// for (x = 0; x < al; x++) {
// 		// 	if (alobj.options[x].value == arr[9])
// 		// 		alobj.options[x].selected=true;
// 		// }
//
// 	}
// }
//
// function setpos(id,e)
// {
// 	// pos=getMouseP(e);
// 	// document.getElementById(id).style.top=pos[1]+'px';
// 	// document.getElementById(id).style.left=pos[0]+'px';
// 	// document.getElementById(id).style.display='';
// }
