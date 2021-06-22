function rowDataList(sender,datas,currentpage) {
    var result =  datas.datas;
    var rows = '';
    for (var i = 0; i < result.length; i++) {
        rows = rows + "<tr class='rowcontent'>";
        rows = rows + "<td>&nbsp;" + result[i]['novp'] + "</td>";
        rows = rows + "<td>&nbsp;" + result[i]['tanggal'] + "</td>";
        rows = rows + "<td>&nbsp;" + result[i]['nopo'] + "</td>";
        rows = rows + "<td>&nbsp;" + result[i]['noinv1'] + "</td>";
        rows = rows + "<td>&nbsp;" + result[i]['penjelasan'] + "</td>";
        if (sender.onActionColumnRender!=null){
            rows = rows + sender.onActionColumnRender(sender,result[i],currentpage);
        }
        rows = rows + "</tr>";
    }
    var table = document.getElementById(sender.tableId);
    table.tBodies[0].innerHTML = rows;
};
function columActionRender(sender,data,currentpage){
    var rows = '';
    if (data['posting']==0) {
	rows = rows + "<td><img src='images/skyblue/edit.png' class='zImgBtn' onclick=\"ConstructPage.showEdit('" + data['novp'] + "');\" title='Edit'></td>";
    	rows = rows + "<td><img src='images/skyblue/delete.png' class='zImgBtn' onclick=\"ConstructPage.deleteHeader('" + data['novp'] + "'," + currentpage + ");\" title='Delete'></td>";
        rows = rows + "<td><img src='images/skyblue/posting.png' class='zImgBtn' onclick=\"postingData('" + data['novp'] + "'," + currentpage + ");\"  title='Posting'></td>";
    } else {
        rows = rows + "<td><img src='images/skyblue/posted.png' class='zImgBtn' title='Posted'></td>";
    }
    rows = rows + "<td><img src='images/skyblue/pdf.jpg' class='zImgBtn' onclick=\"ConstructPage.showPDF('" + data['novp'] + "',event);\" title='Print Data Detail'></td>";
    rows = rows + "<td><img src='images/skyblue/zoom.png' class='zImgBtn' onclick=\"ConstructPage.showPreview('" + data['novp'] + "',event);\" title='Print Data Detail'></td>";
    return rows;
};
function prepareEntryForm(sender,data,columnsize){

    sender.headerElements=['rpInvoice','jumlah'];
    sender.detailElements=[];
    //build header form
    var html = "<fieldset style='float:left'>\n" +
        "        <legend id='title_Form'><b>"+sender.statusEntry+" Header</b></legend>\n" +
        "        <div id='Tambah'>\n" +
        "            <table border='0' cellspacing='0' cellpadding='1'>\n" +
        "                <tbody>";
    var index=0;
    var isbreak=false;
    var tr='';
    while(true) {
        if (!isbreak) tr = tr + "<tr>";
        for (var x = 0; x < sender.columnSize; x++) {
            isbreak = index > data.header.inputs.length - 1;
            if (isbreak) {
                break;
            }
            var header = data.header.inputs[index];
            sender.headerElements.push(header['field']);
            tr = tr +
                "<td style='padding-right:20px;font-size:12px'>\n" +
                "   <label for='" + header['field'] + "'>" + header['caption'] + "</label>\n" +
                "</td>"
            ;
            tr = tr +
                "<td style='padding-right:20px;font-size:12px'>\n";
            for (var i = 0; i <= header['elements'].length - 1; i++) {
                var elements = header['elements'][i];
                tr = tr + sender.createElement(elements);
            }
            tr = tr + "</td>";
            index = index + 1;
        }
        if (!isbreak) tr = tr + "</tr>\r\n";
        if (isbreak) {break;}
    }
    sender.headerElements.push('nobatch');
    tr = tr + "\n" +
        "                    <tr>\n" +
        "                        <td colspan='"+(sender.columnSize*2)+"'>\n" +
        "                            <input type='hidden' name='nobatch' id='nobatch' value=''/>\n" +
        "                            <button id='saveHeader' name='btnSaveHeader' onclick=\"ConstructPage.saveHeader()\">Simpan</button>\n" +
        "                            <button id='resetHeader' name='btnResetHeader' onclick='ConstructPage.resetHeader()'>Reset</button>\n" +
        "                        </td>\n" +
        "                    </tr>\n";
    html = html + tr;
    html = html +"</tbody></table>\n" +
        "        </div>\n" +
        "    </fieldset>";

    html = html +
        " <div id='detailField' style='clear:both'><fieldset>\n" +
        "&nbsp;&nbsp;Nilai invoice&nbsp;:&nbsp;<input id='rpInvoice' name='rpInvoice' class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='' disabled='disabled'> " +
        "            <legend><b>Detail</b></legend>\n" +
        "            <div >\n" +
        "                <div >\n" +
        "                    <fieldset>\n" +
        "                        <legend id='detailLegend'><b>Form "+sender.entryModule+" : <span id='detailSpan'>Mode "+sender.statusEntry+"</span></b></legend>\n" +
        "                        <table>\n" +
        "                            <tbody>";

    index=0;
    isbreak=false;
    tr='';
    while(true) {
        if (!isbreak) tr = tr + "<tr>";
        for (var x = 0; x < sender.columnSize; x++) {
            isbreak = index>data.detail.length-1;
            if (isbreak) {break;}
            var detail=data.detail[index];
            tr = tr +
                "<td style='padding-right:20px;font-size:12px'>\n" +
                "   <label for='"+detail['field']+"'>"+detail['caption']+"</label>\n" +
                "</td>"
            ;
            tr = tr +
                "<td style='padding-right:20px;font-size:12px'>\n";
            for (var i=0;i<=detail['elements'].length-1;i++){
                var elements=detail['elements'][i];
                sender.detailElements.push(elements['field']);
                tr = tr + sender.createElement(elements);
            }
            tr = tr + "</td>";
            index=index+1;
        }
        if (!isbreak) tr = tr + "</tr>\r\n";
        if (isbreak) {break;}
    }
    tr = tr + "\n" +
        "                    <tr>\n" +
        "                        <td colspan='"+(sender.columnSize*2)+"'>\n" +
        "                            <button type='button' id='btnSaveDetail' disabled name='saveDetail' onclick=\"ConstructPage.saveDetail()\">Simpan</button>"+
        "                            <button type='button' id='btnResetDetail' disabled name='resetDetail' onclick='ConstructPage.resetDetail()'>Batal</button> " +
        "                        </td>\n" +
        "                    </tr>";
    html = html + tr;
    html = html + "\n" +
        "                            </tbody>\n" +
        "                        </table>\n" +
        "                    </fieldset>\n" +
        "                </div>\n" +
        "                <div>\n" +
        "                    <fieldset>\n" +
        "                        <legend><b>Tabel "+sender.entryModule+"</b></legend>\n" +
        "                        <div style='max-height:200px;overflow:auto'>\n" +
        "                            <table class='sortable' cellspacing='1' border='0' id='tableDetail"+sender.entryModule+"'>\n" +
        "                                <thead >\n" +
        "                                    <tr class='rowheader'>\n"+
        "                                       <td colspan='2' style='width:10%'>Aksi</td>\n";
    var td='';
    for (var ii=0;ii<data.detailList.tableHeader.length;ii++){
        td = td + "<td align='center' style='width:10%'>"+data.detailList.tableHeader[ii]+"</td>";
    }
    html = html + td + "</tr></thead>";
    html = html + "<tbody>\n";
    sender.rowFields=data.detailList.rowFields;
    for (var i=0;i<data.detailList.tableRows.length;i++) {
        var row = data.detailList.tableRows[i];
        var key = JSON.stringify(sender.getDetailKeyValue(row));
        var varData=(JSON.stringify(row)).replace(/"/g,'\'');
        html = html +
            "<tr class='rowcontent'>\n" +
            "   <td><img id='editDetail' class='zImgBtn' src='images/skyblue/edit.png' onclick=\"var data = "+varData+"; ConstructPage.editDetail(data);\"></td>\n" +
            "   <td><img id='deleteDetail' class='zImgBtn' src='images/skyblue/delete.png' onclick=\"var data = "+varData+"; ConstructPage.deleteDetail(data);\"'></td>\n" +
            sender.extractRow(row)+"</tr>";
    }
    html = html + "</tbody>\n" +
        "                                <tfoot></tfoot>\n" +
        "                            </table>\n" +
        "                        </div>\n" +
        "                    </fieldset>\n" +
        "                </div>\n" +
        "            </div> \n" +
        "        </fieldset>\n" +
        "    </div>";

    return html;
};
ConstructPage.sectionToBuild={
    build:{
        functional:true,
        list:true,
        entry:true,
    }
};
ConstructPage.crudUrl={
    'entry':'keu_vp.php?proses=entry',
    'list':'keu_vp.php?proses=list',
    'edit':'keu_vp.php?proses=edit',
    'listrow':'keu_vp.php?proses=listrow',
    'savedetail':'keu_vp.php?proses=savedetail',
    'deletedetail':'keu_vp.php?proses=deletedetail',
    'saveheader':'keu_vp.php?proses=saveheader',
    'deleteheader':'keu_vp.php?proses=deleteheader',
    'pdf':'keu_vp.php?proses=pdf',
    'preview':'keu_vp.php?proses=preview',
};
function delayCallback(obj) {
    if (obj.sectionToBuild.build.list) {
        obj.listSection.init();
        obj.listSection.showRowPerPage=20;
        obj.listSection.tableId = 'listTable';
        obj.listSection.tableHeader = {
            headers: ['No. Voucher Payable', 'Tanggal', 'No PO', 'NO. Tagihan/ Invoice', 'Keterangan'],
            actions: ['Edit', 'Delete', 'Posting','PDF', 'preview'],
        };
        obj.listSection.arrayFilters=[
            {'novp':'searchNoVP'},
            {'tanggal':'searchTglVP'},
        ];
        obj.listSection.onListDataReady = rowDataList;
        obj.listSection.onActionColumnRender = columActionRender;
        obj.listSection.global.initGlobalProperty('contentBoxlist', true, 'keu_vp.php?proses=list');
    }
};
ConstructPage.onInit=function(obj) {
    if (obj.sectionToBuild.build.functional) {
        obj.functionalSection.init();
        obj.functionalSection.global.initGlobalProperty('contentBoxmenu', true, 'keu_vp.php?proses=functional');
    }
    obj.entrySection.columnSize = 2;
    obj.entrySection.entryModule = 'Voucher Payable';
    obj.entrySection.headerKeys = {'novp': ''};
    obj.entrySection.detailKeys = {'novp': '', 'noakun': ''};
    obj.entrySection.headerElements=['rpInvoice','jumlah'];
    obj.entrySection.onPrepareEntry = prepareEntryForm;
    obj.entrySection.global.onAfterExecute = entryAfterExecute;
    obj.entrySection.onResetElement = resetElement;
    obj.entrySection.onBindDataToElement = bindDataToElement;
    obj.entrySection.onValidateInputs = validateInputs;
    obj.delay(delayCallback);
};
ConstructPage.onGetParams=getParams;
ConstructPage.init();
function resetElement(obj){
    clearSelect('nopos');
    clearSelect('noinvoices');
}
function bindDataToElement(obj,data){  
    resetElement(); 
    for (var i=0;i<data.nopos.length;i++){
        addOptionSelect('nopos', data.nopos[i].nopo, data.nopos[i].nopo);
    }
    for (var i=0;i<data.invoices.length;i++){
        addOptionSelect('noinvoices', data.invoices[i].noinv, data.invoices[i].noinv);
    }
}
function validateInputs(obj,elements){
    var ret = true;
    for (var i=0;i<elements.length;i++){
        if (!ret) {break;}
        if (elements[i]=='nopos' || elements[i]=='noinvoices'){
            ret = ret & (getAllOption(elements[i]) != '[]');
            var el = document.getElementById(elements[i]);
            if (!ret) {alert(el.getAttribute('caption') + ' wajid diisi');el.focus();}
        }
    }
    return ret;
}
function getAllOption(select){
    var select__ =  document.getElementById(select);
    var values=[];
    if (select__!=null) {
        var length = select__.options.length;
        for (i = 0; i < length; i++) {
            values.push(select__.options[i].value.replace(',','#$%'));
        }
    }
    return JSON.stringify(values);
}
function getParams(obj,id,tipe){
    var param='';
    switch (tipe) {
        case 'header':
            for (var i = 0; i < obj.headerElements.length; i++) {
                var element = obj.headerElements[i];
                if (element=='nopos' || element=='noinvoices'){
                    param = param + element + '=' + getAllOption(element) + (i == obj.headerElements.length - 1 ? '' : '&');
                } else {
                    param = param + element + '=' + getValue(element) + (i == obj.headerElements.length - 1 ? '' : '&');
                }
            }
            break;
        case 'detail':
            param = this.entrySection.paramKey(this.entrySection.headerKeys, null) + '&';
            for (var i = 0; i < this.entrySection.detailElements.length; i++) {
                var element = this.entrySection.detailElements[i];
                param = param + element + '=' + getValue(element) + (i == this.entrySection.detailElements.length - 1 ? '' : '&');
            }
            break;
        case 'pdf':
        case 'preview':
            param = "code="+id;
            break;
    }
    if (param!='') param=param.replace(/"/g,'\'');
    return param;
}
function entryAfterExecute(obj){
    var tmp =  document.getElementById('noakun');
    if (tmp!=null){tmp.selectedIndex=0;}
    tmp =  document.getElementById('matauang');
    if (tmp!=null){tmp.selectedIndex=0;}
    tmp =  document.getElementById('dk');
    if (tmp!=null){tmp.selectedIndex=0;}

    if (obj.headerData!=null) {
        var total = Intl.NumberFormat().format(Number(obj.headerData.totaljumlah));
        var tmp = document.getElementById('rpInvoice');
        if (tmp != null) {
            tmp.value = total;
        }
        tmp = document.getElementById('jumlah');
        if (tmp != null) {
            tmp.value = total;
        }
    }
}
function getNoInvoice(title, content, ev){
    width = '850';
    height = '620';
    showDialog1(title, content, width, height, ev);

    param = '';
    tujuan = 'keu_vp.php?proses=getinvoiceform';
    post_response_text(tujuan = 'keu_vp.php?proses=getinvoiceform', param, respog);

    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //alert(con.responseText);
                    document.getElementById('formPencariandata').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function findNoinvoice(tipetransaksi) {
    txt = trim(document.getElementById('no_brg').value);
    idSupplier = document.getElementById('supplierIdcr').value;
    nopocr = document.getElementById('nopocr').value;
    param = 'txtfind=' + txt;

    if (idSupplier != '') {
        param += '&idSupplier=' + idSupplier;
    }
    if (nopocr != '') {
        param += '&nopocr=' + nopocr;
    }
    //alert(param);
    tujuan = 'keu_vp.php?proses=getinvoices';
    if ((txt == '') && (idSupplier == '') && (nopocr == '')) {
        alert("Field can't obligatory");
    } else post_response_text(tujuan , param, respog);


    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //alert(con.responseText);
                    document.getElementById('container2').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function setPo(page,row) {
    page.bindDataToElement(row);
    closeDialog();
}
// var showPerPage = 10;
//
// function getValue(id) {
//     var tmp = document.getElementById(id);
//
//     if(tmp) {
//         if(tmp.options) {
//             return tmp.options[tmp.selectedIndex].value;
//         } else if(tmp.nodeType=='checkbox') {
//             if(tmp.checked==true) {
//                 return 1;
//             } else {
//                 return 0;
//             }
//         } else {
//             return tmp.value;
//         }
//     } else {
//         return false;
//     }
// }
//
// /* Search
//  * Filtering Data
//  */
// function searchTrans() {
//     var notrans = document.getElementById('sNoTrans');
//     var tanggal = getValue('sTanggal');
//     if(tanggal!='') {
//         var tmpTanggal = tanggal.split('-');
//         var tanggalR = tmpTanggal[2]+"-"+tmpTanggal[1]+"-"+tmpTanggal[0];
//     } else {
//         var tanggalR = '';
//     }
//     var where = '[["novp","'+notrans.value+'"],["tanggal","'+tanggalR+'"]]';
//
//     goToPages(1,showPerPage,where);
// }
//
// /* Paging
//  * Paging Data
//  */
// function defaultList() {
//     goToPages(1,showPerPage);
// }
//
// function goToPages(page,shows,where) {
//     if(typeof where != 'undefined') {
//         var newWhere = where.replace(/'/g,'"');
//     }
//     var workField = document.getElementById('workField');
//     var param = "page="+page;
//     param += "&shows="+shows;
//     if(typeof where != 'undefined') {
//         param+="&where="+newWhere;
//     }
//
//     function respon() {
//         if (con.readyState == 4) {
//             if (con.status == 200) {
//                 busy_off();
//                 if (!isSaveResponse(con.responseText)) {
//                     alert('ERROR TRANSACTION,\n' + con.responseText);
//                 } else {
//                     //=== Success Response
//                     workField.innerHTML = con.responseText;
//                 }
//             } else {
//                 busy_off();
//                 error_catch(con.status);
//             }
//         }
//     }
//
//     post_response_text('keu_slave_vp.php?proses=showHeadList', param, respon);
// }
//
// function choosePage(obj,shows,where) {
//     var pageVal = obj.options[obj.selectedIndex].value;
//     goToPages(pageVal,shows,where);
// }
//
// /* Halaman Manipulasi Data
//  * Halaman add, edit, delete
//  */
// function showAdd() {
//     var workField = document.getElementById('workField');
//     var param = "";
//
//     function respon() {
//         if (con.readyState == 4) {
//             if (con.status == 200) {
//                 busy_off();
//                 if (!isSaveResponse(con.responseText)) {
//                     alert('ERROR TRANSACTION,\n' + con.responseText);
//                 } else {
//                     //=== Success Response
//                     workField.innerHTML = con.responseText;
//                 }
//             } else {
//                 busy_off();
//                 error_catch(con.status);
//             }
//         }
//     }
//
//     post_response_text('keu_slave_vp.php?proses=showAdd', param, respon);
// }
//
// function showEditFromAdd() {
//     var workField = document.getElementById('workField');
//     var trans = document.getElementById('novp');
//     var param = "novp="+trans.value+"&kodeorg="+getValue('kodeorg')+
//         "&noakun="+getValue('noakun2a')+"&tipetransaksi="+getValue('tipetransaksi');
//
//     function respon() {
//         if (con.readyState == 4) {
//             if (con.status == 200) {
//                 busy_off();
//                 if (!isSaveResponse(con.responseText)) {
//                     alert('ERROR TRANSACTION,\n' + con.responseText);
//                 } else {
//                     //=== Success Response
//                     workField.innerHTML = con.responseText;
//                     showDetail();
//                 }
//             } else {
//                 busy_off();
//                 error_catch(con.status);
//             }
//         }
//     }
//
//     post_response_text('keu_slave_vp.php?proses=showEdit', param, respon);
// }
//
// function showEdit(num) {
//     var workField = document.getElementById('workField');
//     var trans = document.getElementById('novp_'+num).getAttribute('value');
//     var param = "numRow="+num+"&novp="+trans;
//
//     function respon() {
//         if (con.readyState == 4) {
//             if (con.status == 200) {
//                 busy_off();
//                 if (!isSaveResponse(con.responseText)) {
//                     alert('ERROR TRANSACTION,\n' + con.responseText);
//                 } else {
//                     //=== Success Response
//                     workField.innerHTML = con.responseText;
//                     showDetail();
//                 }
//             } else {
//                 busy_off();
//                 error_catch(con.status);
//             }
//         }
//     }
//
//     post_response_text('keu_slave_vp.php?proses=showEdit', param, respon);
// }
//
// /* Manipulasi Data
//  * add, edit, delete
//  */
// function addDataTable() {
//     var param = "novp="+getValue('novp')+"&tanggal="+getValue('tanggal'),
// 		listInv = getById('listInvoice').childNodes.length;
// 	param += "&tanggalterima="+getValue('tanggalterima')+"&tanggalbayar="+getValue('tanggalbayar')+
// 		"&tanggaljatuhtempo="+getValue('tanggaljatuhtempo');
//     param += "&nopo="+getValue('nopo')+"&penjelasan="+getValue('penjelasan');
//     for(var i=0;i<listInv;i++){
// 		param += "&noinv[]="+getById('noinv_'+i).innerHTML;
// 	}
//
//     function respon() {
//         if (con.readyState == 4) {
//             if (con.status == 200) {
//                 busy_off();
//                 if (!isSaveResponse(con.responseText)) {
//                     alert('ERROR TRANSACTION,\n' + con.responseText);
//                 } else {
//                     //=== Success Response
//                     //alert('Added Data Header');
// 					document.getElementById('novp').value = con.responseText;
//                     showEditFromAdd();
//                 }
//             } else {
//                 busy_off();
//                 error_catch(con.status);
//             }
//         }
//     }
//
//     post_response_text('keu_slave_vp.php?proses=add', param, respon);
// }
//
// function editDataTable() {
//     var param = "novp="+getValue('novp')+"&tanggal="+getValue('tanggal');
// 	param += "&tanggalterima="+getValue('tanggalterima')+"&tanggalbayar="+getValue('tanggalbayar')+
// 		"&tanggaljatuhtempo="+getValue('tanggaljatuhtempo');
//     param += "&nopo="+getValue('nopo')+"&penjelasan="+getValue('penjelasan');
//
//     function respon() {
//         if (con.readyState == 4) {
//             if (con.status == 200) {
//                 busy_off();
//                 if (!isSaveResponse(con.responseText)) {
//                     alert('ERROR TRANSACTION,\n' + con.responseText);
//                 } else {
//                     //=== Success Response
//                     //alert(con.responseText);
//                     defaultList();
//                 }
//             } else {
//                 busy_off();
//                 error_catch(con.status);
//             }
//         }
//     }
//
//     post_response_text('keu_slave_vp.php?proses=edit', param, respon);
// }
//
// /*
//  * Detail
//  */
// function showDetail() {
//     var detailField = document.getElementById('detailField'),
//         novp = document.getElementById('novp').value,
//         param = "novp="+novp;
//
//     function respon() {
//         if (con.readyState == 4) {
//             if (con.status == 200) {
//                 busy_off();
//                 if (!isSaveResponse(con.responseText)) {
//                     alert('ERROR TRANSACTION,\n' + con.responseText);
//                 } else {
//                     //=== Success Response
//                     detailField.innerHTML = con.responseText;
//                 }
//             } else {
//                 busy_off();
//                 error_catch(con.status);
//             }
//         }
//     }
//
//     post_response_text('keu_slave_vp_detail.php?proses=showDetail', param, respon);
// }
//
// function deleteData(num) {
//     var novp = document.getElementById('novp_'+num).getAttribute('value'),
//         param = "novp="+novp;
//
//     function respon() {
//         if (con.readyState == 4) {
//             if (con.status == 200) {
//                 busy_off();
//                 if (!isSaveResponse(con.responseText)) {
//                     alert('ERROR TRANSACTION,\n' + con.responseText);
//                 } else {
//                     //=== Success Response
//                     var tmp = document.getElementById('tr_'+num);
//                     tmp.parentNode.removeChild(tmp);
//                 }
//             } else {
//                 busy_off();
//                 error_catch(con.status);
//             }
//         }
//     }
//
// 	if(confirm("Anda akan menghapus data transaksi "+novp+"\nAnda yakin?"))
// 		post_response_text('keu_slave_vp.php?proses=delete', param, respon);
// }
//
// /* Posting Data
//  */
function postingData(novp,page) {
    // var novp = document.getElementById('novp_'+numRow).getAttribute('value'),
		param = "novp="+novp;
    var page__=page;

	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    ConstructPage.listSection.goToPage(page__);
                    // x=document.getElementById('tr_'+numRow);
					// x.cells[4].innerHTML='';
					// x.cells[5].innerHTML='';
                    // x.cells[6].innerHTML="<img class=\"zImgOffBtn\" title=\"Posting\" src=\"images/skyblue/posted.png\">";
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

    if(confirm('Posting '+novp+'\nThis transaction will released. are you sure?')) {
        post_response_text('keu_slave_vp_posting.php', param, respon);
    }
}
//
// function printPDF(ev) {
//     // Prep Param
//     param = "proses=pdf";
//
//     showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
//         " src='keu_slave_vp_print.php?"+param+"'></iframe>",'800','400',ev);
//     var dialog = document.getElementById('dynamic1');
//     dialog.style.top = '50px';
//     dialog.style.left = '15%';
// }
//
// function detailPDF(numRow,ev) {
//     // Prep Param
//     var novp = document.getElementById('novp_'+numRow).getAttribute('value');
//     param = "proses=pdf&novp="+novp;
//
//     showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
//         " src='keu_slave_vp_print_detail.php?"+param+"'></iframe>",'800','400',ev);
//     var dialog = document.getElementById('dynamic1');
//     dialog.style.top = '50px';
//     dialog.style.left = '15%';
// }
//
/**
 * getPO
 * Show dialog for get nomor
 */
function getPO(ev) {
	// var param = "tipe=PO&nokonosemen="+getValue('nokonosemen')+"&kodept="+getValue('kodept'),
	//     tujuan='keu_slave_vp_popup.php?'+param;
    var param = "proses=getinvoiceform&tipe=PO";//&nokonosemen="+getValue('nokonosemen')+"&kodept="+getValue('kodept'),
    tujuan='keu_vp.php?'+param;

    post_response_text(tujuan, param, respog);

	function respog()
	{
		if(con.readyState==4)
		{
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				} else {
                    showDialog1('Find PO',"<div id='popupCont'></div>",'800','400',ev);
					document.getElementById('popupCont').innerHTML = con.responseText;
                    var dialog = document.getElementById('dynamic1');
                    dialog.style.top = '50px';
                    dialog.style.left = '15%';
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
	// showDialog1('Find PO',"<iframe frameborder=0 style='width:795px;height:400px'"+
        // " src='keu_slave_vp_popup.php?"+param+"'></iframe>",'800','400',ev);
}
/**
 * findPO
 * Display List of PO
 */
function findPO() {
	var po = document.getElementById('po').value,
		param='po='+po+'&tipe='+getValue('tipe'),
        tujuan='keu_slave_vp_po.php?proses=po';

	post_response_text(tujuan, param, respog);

	function respog()
	{
		if(con.readyState==4)
		{
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				} else {
                    var contPO = document.getElementById('hasilPO'),
                        contInvoice = document.getElementById('hasilInvoice');
                    contPO.innerHTML = con.responseText;
                    contPO.style.display = "";
                    contInvoice.style.display = "none";
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}
function findDetailBatch(kode) {
    var param='kode='+kode;
    tujuan='keu_slave_vp_po.php?proses=batch';

    post_response_text(tujuan, param, respog);

    function respog()
    {
        if(con.readyState==4)
        {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    var contPO = document.getElementById('hasilPO'),
                        contInvoice = document.getElementById('hasilInvoice');
                    contInvoice.innerHTML = con.responseText;
                    contInvoice.style.display = "";
                    contPO.style.display = "none";
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
/**
 * findInvoice
 * Display Invoice List of PO
 */
function findInvoice(obj) {
	var param='po='+obj.getAttribute('nopo')+'&tipe='+obj.getAttribute('tipe');
        tujuan='keu_slave_vp_po.php?proses=invoice';

	post_response_text(tujuan, param, respog);

	function respog()
	{
		if(con.readyState==4)
		{
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				} else {
                    var contPO = document.getElementById('hasilPO'),
                        contInvoice = document.getElementById('hasilInvoice');
                    contInvoice.innerHTML = con.responseText;
                    contInvoice.style.display = "";
                    contPO.style.display = "none";
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}
function clearSelect(selectId){
    var select__=document.getElementById(selectId);
    if (select__!=null) {
        var length = select__.options.length;
        for (var i = length; i>0; i--) {
            select__.options[i-1] = null;
        }
    }
}
function addOptionSelect(selectId,value,caption){
    var select__=document.getElementById(selectId);
    if (select__!=null) {
        var opt = document.createElement('option');
        opt.appendChild(document.createTextNode(caption));
        opt.value = value;
        select__.appendChild(opt);
    }
}
function setPoInv(nobatch) {
    document.getElementById('nobatch').value=nobatch;
    var idx = 0;
    var chk = null;
    // var nopos =  document.getElementById('nopos');
    // var noinvoices =  document.getElementById('noinvoices');
    clearSelect('nopos');
    clearSelect('noinvoices');
    var totalInvoice=0;
    var totalPPN=0;
    while (true) {
        chk =  document.getElementById('el_inv_' + idx);
        if (chk == null) {
            break;
        } else {
            if (chk.getAttribute('disabled')==null) {
                if (chk.checked) {
                    var inv = getById('hidden_inv_el_inv_' + idx).value;
                    addOptionSelect('noinvoices', inv, inv);
                    var nopo = getById('hidden_nopo_el_inv_' + idx).value;
                    addOptionSelect('nopos', nopo, nopo);
                    var nilai = getById('hidden_nilai_el_inv_' + idx).value;
                    nilai = nilai.replace(/,/g, '') ;
                    totalInvoice = totalInvoice + Number(nilai);
                    var ppn = getById('hidden_ppn__el_inv_' + idx).value;
                    ppn = ppn.replace(/,/g, '') ;
                    totalPPN = totalPPN + Number(ppn);
                }
            }
        }
        idx=idx+1;
    }
    document.getElementById('rpInvoice').value=Intl.NumberFormat().format(totalInvoice+totalPPN);
    document.getElementById('jumlah').value=Intl.NumberFormat().format(totalInvoice+totalPPN);
    closeDialog();
}
/**
 * setPoInv
 * Set No PO dan Invoice
 */
// function setPoInv() {
//     var tbody = getById('t_inv_body'),
//         nopo = getInner('t_inv_nopo'),
//         rows = tbody.childNodes.length,
// 		listInv = getById('listInvoice'),
// 		totalRp = getById('totalRpInv'),
//         res = {};
//
//     // Get Result
//     res.nopo = nopo;
//     res.invoice = new Array();
// 	var totalInvoice = 0;
//     for (var i=0;i<rows;i++) {
//         var tmp = getById('el_inv_'+i),
//             tmp2 = getById('t_noinvoice_'+i),
// 			tmp3 = getById('t_nilaiinvoice_'+i).getAttribute('value');
//
//         if (tmp.checked) {
//             res.invoice.push(tmp2.innerHTML);
// 			totalInvoice += parseFloat(tmp3);
//         }
//     }
//     if (res.invoice.length>4) {
//         alert("Maximum Invoice selected is 4");return;
//     }
//
//     // Set Result
//     document.getElementById('nopo').value = res.nopo;
// 	totalRp.value = totalInvoice;
// 	var tmpInv = "";
//     for (i in res.invoice) {
// 		tmpInv += "<div id='noinv_"+i+"'>"+res.invoice[i]+"</div>";
//     }
//     listInv.innerHTML = tmpInv;
//     closeDialog();
// }
//
/**
 * selAll
 * Select All untuk list PO/Kontrak/SJ/Kono
 */
function selAll() {
	var tbodyLen = getById('t_inv_body').childNodes.length;
	for(var i=0;i<tbodyLen;i++) {
	    var chk = getById('el_inv_'+i);
	    if (chk!=null) {
	        if (chk.getAttribute('disabled')==null) {
                chk.setAttribute('checked', true);
            }
        }
	}
}
/**
 * zoom
 * Show List Invoice
 */
function zoom(numRow,ev) {
	var novp = document.getElementById('novp_'+numRow).getAttribute('value'),
		tujuan='keu_slave_vp.php?proses=showInvoice';

	param = "proses=pdf&novp="+novp;
    post_response_text(tujuan, param, respog);

	function respog()
	{
		if(con.readyState==4)
		{
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				} else {
                    showDialog1('Invoice List',"<div id=listInvoiceHeader style='overflow: auto;max-height: 100px;'>"+
						con.responseText+"</div>",'250','100',ev);
					var dialog = document.getElementById('dynamic1');
					dialog.style.top = dialog.style.top+15;
					dialog.style.left = '75%';
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}