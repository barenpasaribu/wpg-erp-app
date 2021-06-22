var AjaxClass = {
    createXMLHttpRequest:function () {
        try {
            return new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
        }
        try {
            return new ActiveXObject("Microsoft.XMLHTTP");
        } catch (e) {
        }
        try {
            return new XMLHttpRequest();
        } catch (e) {
        }
        alert("XMLHttpRequest Tidak didukung oleh browser");
        return null;
    },
    busyOn:function ()//set busy on
    {
        document.getElementById('progress').style.display = '';//you must have object with id=progress on your documents
        document.body.style.cursor = 'wait';
    },
    busyOff:function ()//set busy off
    {
        document.getElementById('progress').style.display = 'none';//you must have object with id=progress on your documents
        document.body.style.cursor = 'default';
    },
    verify:function () {
    },
    ajaxFunc:function (sender,type,url,param,async=true) {
        sender.global.isProccessing=true;
        var ac=this;
        var sender_=sender;
        var con = this.createXMLHttpRequest();
        this.busyOn();
        zz = verify();
        if (zz) {
            // par = parent.location.href.replace("http://", "");
            // param += '&par=' + par;
            con.open(type, url, async);
            if ((typeof param)=='string') {
                // if (type=='POST') {typeof stringValue
                con.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            }
            con.onreadystatechange =function() {
                // Check if the request is compete and was successful
                if(this.readyState === 4 && this.status === 200) {
                    // process the response from ajax
                    if (sender_!=null) {
                        if (sender_.onReadyStateChange != null) {
                            sender_.onReadyStateChange(con.responseText);
                            sender_.global.isProccessing=false;
                        }
                    }
                    ac.busyOff();
                    con = null;
                }
                ac.busyOff();
            };
            con.send(param);
        } else window.location = 'logout.php';
    },
};

var GlobalProperty={
    isProccessing:false,
    ajaxClass:AjaxClass,
    templateData:null,
    containerId:'',
    useAjaxForTemplate:false,
    ajaxURL:'',
    myOwner:null,
    onBeforeExecute:null,
    onAfterExecute:null,
    applyContainer:function(template){
        if (this.containerId!='') {
            var element = document.getElementById(this.containerId);
            if (element) {
                document.getElementById(this.containerId).innerHTML = template;
            } else {
                alert('Element with id : ' + this.containerId + ' not found');
            }
        }
    },
    ajaxResponse:function(data){
        if (!this.useAjaxForTemplate){
            this.applyContainer(data);
        } else {
            this.templateData = data;
            if (this.myOwner.onTemplateDataReady!=null){
                this.applyContainer(this.myOwner.onTemplateDataReady(data));
            }
        }
        this.isProccessing=false;
        if (this.onAfterExecute != null) {
            this.onAfterExecute(this.myOwner)
        }
    },
    initGlobalProperty : function(containerId_,useAjaxForTemplate_=false,ajaxURL_='') {
        this.containerId = containerId_;
        this.useAjaxForTemplate = useAjaxForTemplate_;
        this.ajaxURL = ajaxURL_;
        if (useAjaxForTemplate_ && ajaxURL_!='') {
            if (this.onBeforeExecute != null) {
                this.onBeforeExecute(this.myOwner)
            }
            this.ajaxClass.ajaxFunc(this.myOwner, 'get', ajaxURL_, '');
        }
    },
};

var FunctionalSection= {
    owner:null,
    global: GlobalProperty,
    onReadyStateChange:null,
    onTemplateDataReady:null,
    init: function () {
        this.onReadyStateChange=this.getHtml;
        this.onTemplateDataReady=this.setHtml;
        this.global.myOwner = this;
    },
    getHtml: function (data) {
        this.global.ajaxResponse(data);
    },
    setHtml:function(data){
        return data;
    }
};
//
var ListSection={
    owner:null,
    global: GlobalProperty,
    tableId:'',
    /* header table
    * the data are :
    * {
    *   headers : [],
    *   actions : []
    * }
    */
    tableHeader:{},
    // /* action colums */
    // actionColumns:[],
    /*
    arrayFilters will contains the definition for filterin
    the data are :
    [{'database_field-1':'html_input_name-1'},..,{'database_field-n':'html_input_name-n'}]
     */
    arrayFilters:[],
    totalRows:0,
    showRowPerPage:10,
    currentPage: 1,
    listDataUrl:'',
    /*
    onListDataReady : set table rows with data from ajax response
    param :
    - sender : this,
    - datas  : data from ajax
    - page   : current page
     */
    onListDataReady:null,
    /*
    onActionColumnRender : render colums action for data
    param :
    - sender : this,
    - data   : current row from onListDataReady
    - page   : current page
     */
    onActionColumnRender:null,
    onReadyStateChange:null,
    onTemplateDataReady:null,
    showPage:true,
    init: function () {
        this.onReadyStateChange=this.getTable;
        this.onTemplateDataReady=this.setTable;
        this.global.myOwner = this;
    },
    getTable: function (data) {
        this.global.ajaxResponse(data);
        this.buildTableHeader();
        this.goToPage(1);
    },
    setTable:function(data){
        return data;
    },
    buildTableHeader:function() {
        var head = "<tr class='rowheader'>";
        for (var i = 0; i <= this.tableHeader.headers.length - 1; i++) {
            head = head + "<td align='center' style='width:10%'>" + this.tableHeader.headers[i] + "</td>";
        }
        if (this.tableHeader.actions.length>0) {
            head = head + "<td colspan='" + this.tableHeader.actions.length + "' align='center' style='width:10%'>Aksi</td>";
        }
        head = head + "</tr>";
        var table = document.getElementById(this.tableId);
        if (table!=null){
            table.tHead.innerHTML=head;
        }
    },
    totalSpan:function(){
        return this.tableHeader.headers.length+this.tableHeader.actions.length;
    },
    varifyShowRowPerPage:function(){
        if ( this.showRowPerPage==0 ){
            this.showRowPerPage=10;
        }
        if (this.showRowPerPage<0){
            this.showRowPerPage=-1*this.showRowPerPage;
        }
    },
    makePage: function (page) {
        var table = document.getElementById(this.tableId);
        var tfoot=null;
        if (table!=null){
            tfoot = table.tFoot ;
        }
        if (this.totalRows == 0) {
            tfoot.innerHTML ="<td colspan='"+this.totalSpan()+"' style='text-align:center; vertical-align:middle;'><font style='color: #ffffff'>Tidak ada data</font></td>";
        } else if (this.totalRows > 0) {
            this.varifyShowRowPerPage();
            var makePage = '';
            var totalpage = Math.ceil(this.totalRows / this.showRowPerPage);

            (totalpage < 1 ? (totalpage = 1) : null);
            var select = "<select style:'width:50px' id='pageNumber' onchange=\"ConstructPage.listSection.choosePage(this)\">";
            for (i = 1; i <= totalpage; i++) {
                select = select + "<option value='" + i + "' " + (i == page ? "selected" : "") + ">" + i + "</option>";
            }
            select = select + "</select>";
            makePage = makePage + "<img id='first' src='images/skyblue/first.png'";
            if (page > 1) {
                makePage = makePage + " style='cursor:pointer' onclick=\"ConstructPage.listSection.goToPage(1," + this.showRowPerPage + ")\"";
            }

            makePage = makePage + '>&nbsp;';
            makePage = makePage + "<img id='prev' src='images/skyblue/prev.png'";
            if (page > 1) {
                makePage = makePage + " style='cursor:pointer' onclick=\"ConstructPage.listSection.goToPage(" + (page - 1) + "," + this.showRowPerPage + ")\"";
            }
            makePage = makePage + '>&nbsp;';
            makePage = makePage + select;
            makePage = makePage + "<img id='next'  src='images/skyblue/next.png'";
            if (page < totalpage) {
                makePage = makePage + " style='cursor:pointer' onclick=\"ConstructPage.listSection.goToPage(" + (page + 1) + "," + this.showRowPerPage + ")\"";
            }

            makePage = makePage + '>&nbsp;';
            makePage = makePage + "<img id='last'  src='images/skyblue/last.png'";
            if (page < totalpage) {
                makePage = makePage + " style='cursor:pointer' onclick=\"ConstructPage.listSection.goToPage(" + totalpage + ',' + this.showRowPerPage + ")\"";
            }

            makePage = makePage + '>';
            if (tfoot!=null) {
                tfoot.innerHTML = "<td colspan='"+this.totalSpan()+"' style='text-align:center; vertical-align:middle;'>" + makePage + "</td>";
            }
        }
    },
    dataReadyState:function(datas){
        if (this.onListDataReady!=null){
            var datas__=JSON.parse(datas);
            this.totalRows = datas__['totalrows'];
            this.onListDataReady(this,datas__,this.currentPage);
            if (this.showPage) this.makePage(this.currentPage);
        }
    },
    choosePage:function(obj){
        var pageVal = obj.options[obj.selectedIndex].value;
        this.goToPage(Number(pageVal), this.showRowPerPage);
    },
    goToPage: function (page) {
        if (this.owner.crudUrl.listrow!=null) {
            if (this.owner.crudUrl.listrow != '') {
                // if (this.listDataUrl!='') {
                this.currentPage = page;
                if (this.showPage) {
                    this.varifyShowRowPerPage();
                    var where = this.searchTrans();
                    var param = "page=" + page;
                    param += "&shows=" + this.showRowPerPage;
                    param += "&where=" + where;
                }
                this.onReadyStateChange = this.dataReadyState;
                // var url_ = this.listDataUrl + '&' + param;
                var url_ =this.owner.crudUrl.listrow + '&' + param;
                this.global.ajaxClass.ajaxFunc(this, 'get', url_, param);
            }
        }
    },
    searchTrans: function () {
        var arrayFilters_ = [];
        for (var i = 0; i <= this.arrayFilters.length - 1; i++) {
            var key = Object.keys(this.arrayFilters[i]);
            var val = this.arrayFilters[i][key[0]];
            arrayFilters_.push([
                key[0],
                getValue(val)
            ]);
        }
        return JSON.stringify(arrayFilters_);
    },
    prepareDeleteHeader:function(){
        this.onReadyStateChange=this.deleteHeaderResponse;
    },
    deleteHeaderResponse:function(data){
        var data__=JSON.parse(data);
        if (data__.success){
            this.goToPage(data__.page);
        }else {
            alert(data__.message);
        }
    }
};

var EntrySection= {
    owner: null,
    global: GlobalProperty,
    columnSize: 1,
    onReadyStateChange: null,
    onTemplateDataReady: null,
    onPrepareEntry: null,
    onResetElement: null,
    onBindDataToElement: null,
    onValidateInputs: null,
    statusEntry: '',
    entryModule: '',
    headerElements: [],
    detailElements: [],
    detailRowDatas: [],
    headerKeys: [],
    detailKeys: [],
    rowFields: [],
    headerData: null,
    detailData: null,
    init: function () {
        this.onReadyStateChange = this.getEntry;
        this.onTemplateDataReady = this.setEntry;
        this.global.myOwner = this;
    },
    getEntry: function (data) {
        this.global.ajaxResponse(data);
    },
    setEntry: function (data) {
        var ret = '';
        if (this.onPrepareEntry != null) {
            var data__ = JSON.parse(data);
            this.headerData = data__.header.data;
            ret = this.onPrepareEntry(this, data__, this.columnSize);
        }
        return ret;
    },
    //"elements":[{"field":"nobatch","type":"text","class":"myinputtext","style":"width:150px","maxlength":25,"disabled":"disabled","value":""}]}
    createElement: function (element) {
        var html = '';
        if (element.type == 'text' || element.type == 'checkbox' || element.type == 'hidden') {
            html = html + "<input " + this.extractElementAttribute(element) + "/> ";
        } else if (element.type == 'memo' || element.type == 'textarea') {
            html = html + "<textarea " + this.extractElementAttribute(element) + ">" + element.value + "</textarea> ";
        } else if (element.type == 'select') {
            html = html + "<select " + this.extractElementAttribute(element) + "> ";
            var options = element['options'][0];
            for (var i = 0; i <= options.length - 1; i++) {
                // html = html + "<option value='" + element['options'][i]['value'] + "' "+ (element['options']['currentValue']==element['options'][i]['value'] ? "selected ":"")+
                var option = options[i];
                var keys = Object.keys(options[i]);
                if (i == 0) {
                    html = html + "<option value='" + keys[0] + "' selected>";
                } else {
                    html = html + "<option value='" + keys[0] + "' " + (element.value == keys[0] ? "selected " : "") + ">";
                }
                html = html + (element.showOptionValue ? keys[0] + ' - ' : '') +
                    option[keys[0]] + "</option>";
            }
            html = html + "</select> ";
        }
        if (element.type == 'button') {
            html = html + "<button " + this.extractElementAttribute(element) + ">" + element['caption'] + "</button>";
        }
        return html;
    },
    extractElementAttribute: function (element) {
        var prop = '';
        if (element.type == 'button') {
            prop = prop + "id='" + element.field + "' name='" + element.field + "' " +
                "onclick=\"" + element.onclick + "\" class='" + element.class + "'";
        } else {
            var keys = Object.keys(element);
            for (var i = 0; i <= keys.length - 1; i++) {
                var val = element[keys[i]];
                if (keys[i] != 'field') prop = prop + keys[i] + "='" + val + "' ";
            }
            prop = prop + "id='" + element.field + "' name='" + element.field + "' ";
        }
        return prop;
    },
    getDetailKeyValue: function (row) {
        var keys = Object.keys(this.detailKeys);
        for (var i = 0; i <= keys.length - 1; i++) {
            var key = keys[i];
            var val = row[keys[i]];
            this.detailKeys[key] = val;
        }
        return this.detailKeys;
    },
    extractRow: function (row) {
        var td = '';
        for (var i = 0; i <= this.rowFields.length - 1; i++) {
            var field = this.rowFields[i];
            if (!isNaN(row[field])) {
                td = td + "<td align='right'>" + Intl.NumberFormat().format(row[field]) + "&nbsp;</td>";
            } else {
                row[field] = (row[field] == null ? '' : row[field].replace(/___________/g, ''));
                td = td + "<td style='width:10%'>" + row[field] + "</td>";
            }
        }
        return td;
    },
    paramKey: function (keyfields, ref = null) {
        var param = '';
        var keys = Object.keys(keyfields);
        for (var i = 0; i <= keys.length - 1; i++) {
            if (ref != null) {
                param = param + keys[i] + '=' + (ref[keys[i]] == null ? '' : ref[keys[i]].replace(/___________/g, '')) + (i == keys.length - 1 ? '' : '&');
            } else {
                param = param + keys[i] + '=' + getValue(keys[i]) + (i == keys.length - 1 ? '' : '&');
            }
        }
        return param;
    },
    resetElements: function (elements) {
        for (var i = 0; i <= elements.length - 1; i++) {
            var el = document.getElementById(elements[i]);
            if (el != null) {
                el.value = '';
            }
        }
        if (this.onResetElement != null) {
            this.onResetElement(this);
        }
    },
    bindDataToElement: function (data) {
        var keys = Object.keys(data);
        for (var i = 0; i <= keys.length - 1; i++) {
            var el = document.getElementById(keys[i]);
            var isarray = Array.isArray(data[keys[i]]);
            var value = (isarray ? '' : data[keys[i]] == null ? '' : data[keys[i]].replace(/___________/g, ''));
            if (el != null) {
                el.value = value;
            }
        }
        if (this.onBindDataToElement != null) {
            this.onBindDataToElement(this, data);
        }
    },
    saveHeaderResponse: function (data) {
        var data__ = JSON.parse(data);
        if (data__.success) {
            this.bindDataToElement(data__.data);
            var btn = document.getElementById("btnSaveDetail");
            if (btn!=null) btn.disabled = false;
            btn = document.getElementById("btnResetDetail") ;
            if (btn!=null) btn.disabled = false;
        } else {
            alert(data__.message);
        }
    },
    prepareSaveHeader: function () {
        this.onReadyStateChange = this.saveHeaderResponse;
    },
    saveDetailResponse: function (data) {
        var data__ = JSON.parse(data); 
        if (data__.success) {
            this.resetElements(this.detailElements);
            this.bindData(data__);
        } else {
            alert(data__.message);
        }
    },
    prepareSaveDetail: function () {
        this.onReadyStateChange = this.saveDetailResponse;
    },
    prepareSetEntry: function () {
        this.onReadyStateChange = this.getEntry;
    },
    prepareDeleteDetail: function () {
        this.onReadyStateChange = this.saveDetailResponse;
    },
    bindData: function (data) {
        var data__ = data.data;
        if (data__.header != null) {
            this.bindDataToElement(data__.header);
        }
        if (data__.detail != null) {
            var id = "tableDetail" + this.entryModule;
            var table = document.getElementById(id);
            if (table != null) {
                // if (data__.detail.action=='delete') {table.tBodies[0].innerHTML='';}
                // table.tBodies[0].innerHTML='';
                var html = '';
                // if (data__.detail.action == 'add') {
                //     html = table.tBodies[0].innerHTML;
                // }
                // if (data__.detail.action == 'edit') {
                    table.tBodies[0].innerHTML = '';
                // }
                for (var i = 0; i <= data__.detail.rows.length - 1; i++) {
                    var varData = (JSON.stringify(data__.detail.rows[i])).replace(/"/g, '\'');
                    html = html +
                        "<tr class='rowcontent'>\n" +
                        "   <td><img id='editDetail' class='zImgBtn' src='images/skyblue/edit.png' onclick=\"var data = " + varData + "; ConstructPage.editDetail(data);\"></td>\n" +
                        "   <td><img id='deleteDetail' class='zImgBtn' src='images/skyblue/delete.png' onclick=\"var data = " + varData + "; ConstructPage.deleteDetail(data);\"'></td>\n" +
                        this.extractRow(data__.detail.rows[i]) + "</tr>";
                }
                table.tBodies[0].innerHTML = html;
            }
        }
    },
    validateInputs: function (elements) {
        var ret = true;
        for (var i = 0; i < elements.length; i++) {
            if (!ret) {
                break;
            }
            var el = document.getElementById(elements[i]);
            if (el != null) {
                if (el.getAttribute('required') != null) {
                    // console.log(elements[i] + ' ' + el.tagName);
                    switch (el.tagName) {
                        case 'INPUT':
                        case 'TEXTAREA':
                            ret = ret & (el.value != '');
                            if (!ret) {
                                alert(el.getAttribute('caption') + ' wajib diisi');
                                el.focus();
                            }
                            break;
                        case 'CHECKBOX':
                            ret = ret & (el.checked);
                            if (!ret) {
                                alert(el.getAttribute('caption') + ' wajib dicentang');
                                el.focus();
                            }
                            break;
                        case 'SELECT':
                            if (el.getAttribute('multiple') == null) {
                                ret = ret & (el.value != '');
                                if (!ret) {
                                    alert(el.getAttribute('caption') + ' wajib dipilih opsinya');
                                    el.focus();
                                }
                            }
                            break;
                    }
                }
            }
        }
        if (ret) {
            if (this.onValidateInputs != null) {
                ret = ret & this.onValidateInputs(this, elements);
            }
        }
        return ret;
    }
};

var ConstructPage = {
    sectionToBuild:null,
    functionalSection : FunctionalSection,
    listSection : ListSection,
    entrySection : EntrySection,
    ajaxClass:AjaxClass,
    onInit:null,
    crudUrl:{},
    onGetParams:null,
    init:function() {
        this.entrySection.owner=this;
        this.listSection.owner=this;
        this.functionalSection.owner=this;
        if (this.onInit!==null){
            this.onInit(this);
        }
    },
    showDialog1:function (title,content,width,height,ev,fullPoint) {
        function getBrowserWidth() {
            if (self.innerWidth) {
                return self.innerWidth;
            }

            if (document.documentElement && document.documentElement.clientWidth) {
                return document.documentElement.clientWidth;
            }

            if (document.body) {
                return document.body.clientWidth;
            }
        }
        function getBrowserHeight() {
            if (self.innerHeight) {
                return self.innerHeight;
            }

            if (document.documentElement && document.documentElement.clientHeight) {
                return document.documentElement.clientHeight;
            }

            if (document.body) {
                return document.body.clientHeight;
            }
        }

        if (document.getElementById('dynamic1')) {
            c.style.width = width + 'px';
        } else {
            c = document.createElement('div');
            c.setAttribute('id', 'dynamic1');
            c.setAttribute('class', 'drag');
            c.style.position = 'absolute';
            c.style.display = 'none';
            //c.style.top = '120px';
            c.style.top = '150px';
            c.style.left = '100px';
            c.style.width = width + 'px';
            c.style.paddingTop = '3px';
            c.style.zIndex = 1000;
            document.body.appendChild(c);
        }
        cont = "<b style='color:#FFFFFF;'>" + title + "</b><img src=images/closebig.gif align=right onclick=closeDialog() title='Close detail' class=closebtn onmouseover=\"this.src='images/closebigon.gif';\" onmouseout=\"this.src='images/closebig.gif';\"><br><br>";
        cont += "<div style='background-color:#FFFFFF;border:#777777 solid 2px;height:" + height + "px'>";
        cont += content;
        cont += "</div>";
        document.getElementById('dynamic1').innerHTML = cont;
        pos = new Array();
        pos = getMouseP(ev);
        browserWidth=getBrowserWidth();
        browserHeight=getBrowserHeight();
        var scroll = window.scrollY;
        var top = Math.ceil((window.innerHeight-height)/2) + scroll;
        document.getElementById('dynamic1').style.top = top + 'px';
        document.getElementById('dynamic1').style.left = Math.ceil((browserWidth-width)/2) + 'px';
        document.getElementById('dynamic1').style.display = '';
    },
    /*
    we are using asynchronous ajax, that's mean, ajax callback function processed using different thread from main thread
    we need to delay next ajax execution, wait until the before one finished
     */
    delay:function(callback) {
        var global=this;
        var timeoutId = null;
        var seconds = 1 * 0.5 * 1000;
        var date1=new Date();
        var callback__=callback;
        if (navigator.userAgent.indexOf("Firefox") != -1) {
            timeoutId = setTimeout(function () {
                timeoutId = null;
                if (global.isProccessing){
                    global.delay(callback__);
                } else {
                    if (callback__ != null) {
                        callback__(global);
                    }
                }
                // console.log(((new  Date())-date1)/1000);
            }, seconds);

        } else {
            timeoutId = window.setTimeout(function () {
                timeoutId = null;
                if (global.isProccessing){
                    global.delay(callback__);
                } else {
                    if (callback__ != null) {
                        callback__(global);
                    }
                }
                // console.log(((new  Date())-date1)/1000);
            }, seconds);
        }
    },
    showAdd : function() {
        this.listSection.global.applyContainer('');
        this.entrySection.init();
        this.entrySection.statusEntry='Tambah';
        // this.entrySection.global.initGlobalProperty('contentBoxlist', true, 'keu_batch.php?proses=entry');
        this.entrySection.global.initGlobalProperty('contentBoxlist', true, this.crudUrl.entry);
    },
    defaultList : function(){
        this.functionalSection.global.applyContainer('');
        this.listSection.init();
        // this.listSection.global.initGlobalProperty('contentBoxlist', true, 'keu_batch.php?proses=list');
        this.listSection.global.initGlobalProperty('contentBoxlist', true, this.crudUrl.list);
    },
    searchTrans : function(){
        this.listSection.goToPage(1);
    },
    showEdit : function(id) {
        this.entrySection.init();
        this.entrySection.statusEntry = 'Edit';
        this.entrySection.prepareSetEntry();
        this.entrySection.global.initGlobalProperty('contentBoxlist', true,
            this.crudUrl.edit+'&code=' + id);
        function onDelay(obj) {
            var btn1 =document.getElementById("btnSaveDetail");
            if (btn1!=null) document.getElementById("btnSaveDetail").disabled=false;
            var btn1 =document.getElementById("btnResetDetail");
            if (btn1!=null) document.getElementById("btnResetDetail").disabled=false;
        }

        this.delay(onDelay);
    },
    deleteHeader : function(id,page){
        if (confirm('Anda yakin ingin hapus data ini ?')){
            var param = "code="+id+"&page="+page;
            this.listSection.prepareDeleteHeader();
            this.listSection.global.ajaxClass.ajaxFunc(this.listSection,'post',this.crudUrl.deleteheader,param);
        }
    },
    showPDF : function(id,ev) { 
        var param = '';
        if (this.onGetParams != null) {
            param = this.onGetParams(this.entrySection,id, 'pdf');
        } else {
            param = "code="+id;
        }
        if (param != '') {
            this.showDialog1('Print PDF', "<iframe frameborder=0 style='width:795px;height:400px'" +
                " src='" + this.crudUrl.pdf + "&" + param + "'></iframe>", '800', '400', ev);
        }
    },
    showPreview : function(id,ev){ var param = '';
        if (this.onGetParams != null) {
            param = this.onGetParams(this.entrySection,id, 'preview');
        } else {
            param = "code="+id;
        }
        if (param != '') {
            this.showDialog1('Preview', "<iframe frameborder=0 style='width:795px;height:400px'" +
                " src='" + this.crudUrl.preview + "&" + param + "'></iframe>", '800', '400', ev);
        }
    },
    saveHeader: function(){
        if (this.entrySection.validateInputs(this.entrySection.headerElements)) {
            var param = '';
            if (this.onGetParams != null) {
                param = this.onGetParams(this.entrySection,'', 'header');
            } else {
                for (var i = 0; i < this.entrySection.headerElements.length; i++) {
                    var element = this.entrySection.headerElements[i];
                    param = param + element + '=' + getValue(element) + (i == this.entrySection.headerElements.length - 1 ? '' : '&');
                }
            }
            this.entrySection.prepareSaveHeader();
            this.entrySection.global.ajaxClass.ajaxFunc(this.entrySection, 'post', this.crudUrl.saveheader, param);
        }
    },
    resetHeader: function(){
        this.entrySection.resetElements(this.entrySection.headerElements);
    },
    saveDetail: function(){
        if (this.entrySection.validateInputs(this.entrySection.detailElements)) {
            var param = ''
            if (this.onGetParams != null) {
                param = this.onGetParams(this.entrySection,'', 'detail');
            } else {
                param = this.entrySection.paramKey(this.entrySection.headerKeys, null) + '&';
                for (var i = 0; i < this.entrySection.detailElements.length; i++) {
                    var element = this.entrySection.detailElements[i];
                    param = param + element + '=' + getValue(element) + (i == this.entrySection.detailElements.length - 1 ? '' : '&');
                }
            }
            this.entrySection.prepareSaveDetail();
            this.entrySection.global.ajaxClass.ajaxFunc(this.entrySection, 'post', this.crudUrl.savedetail, param);
        }
    },
    resetDetail: function(){
        this.entrySection.resetElements(this.entrySection.detailElements);
    },
    editDetail: function(row){
        this.entrySection.bindDataToElement(row);
    },
    deleteDetail: function(row){
        if (confirm('Anda yakin ingin hapus data ini ?')){
            var param = this.entrySection.paramKey(this.entrySection.detailKeys,row);
            this.entrySection.prepareDeleteDetail();
            this.entrySection.global.ajaxClass.ajaxFunc(this.entrySection,'post',this.crudUrl.deletedetail,param);
        }
    },
}