function batchDataList(sender,datas,currentpage) {
    var result =  datas.datas;
    var rows = '';
    for (var i = 0; i < result.length; i++) {
        rows = rows + "<tr class='rowcontent'>";
        rows = rows + "<td>&nbsp;" + result[i]['nobatch'] + "</td>";
        rows = rows + "<td>&nbsp;" + result[i]['tglbatch'] + "</td>";
        // rows = rows + "<td>&nbsp;" + datas[i]['noinvoice'] + "</td>";
        // rows = rows + "<td>&nbsp;" + datas[i]['supplier'] + "</td>";
        rows = rows + "<td align='right'>" + Intl.NumberFormat().format(result[i]['totalinvoice']) + "&nbsp;</td>";
        rows = rows + "<td align='right'>" + Intl.NumberFormat().format(result[i]['totalppn']) + "&nbsp;</td>";
        rows = rows + "<td align='right'>" + Intl.NumberFormat().format(result[i]['totalpph']) + "&nbsp;</td>";
        rows = rows + "<td>&nbsp;" + result[i]['status'] + "</td>";
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
    if(data['status']==0){ 
    rows = rows + "<td><img src='images/skyblue/edit.png' class='zImgBtn' onclick=\"ConstructPage.showEdit('" + data['nobatch'] + "');\" title='Edit'></td>";
    rows = rows + "<td><img src='images/skyblue/delete.png' class='zImgBtn' onclick=\"ConstructPage.deleteHeader('" + data['nobatch'] + "'," + currentpage + ");\" title='Delete'></td>";
    }else{ 
    rows = rows + "<td></td>";
    rows = rows + "<td></td>";
    }
    // rows = rows + "<td><img src='images/skyblue/pdf.jpg' class='zImgBtn' onclick=\"ConstructPage.showPDF('" + data['nobatch'] + "',event);\" title='Print Data Detail'></td>";
    // rows = rows + "<td><img src='images/skyblue/zoom.png' class='zImgBtn' onclick=\"ConstructPage.showPreview('" + data['nobatch'] + "',event);\" title='Print Data Detail'></td>";
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
            isbreak = index>data.header.inputs.length-1;
            if (isbreak) {break;}
            var header=data.header.inputs[index];
            sender.headerElements.push(header['field']);
            tr = tr +
                "<td style='padding-right:20px;font-size:12px'>\n" +
                "   <label for='"+header['field']+"'>"+header['caption']+"</label>\n" +
                "</td>"
                ;
            tr = tr +
                "<td style='padding-right:20px;font-size:12px'>\n";
            for (var i=0;i<=header['elements'].length-1;i++){
                var elements=header['elements'][i];
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
        "                            <button id='saveHeader' name='btnSaveHeader' onclick=\"ConstructPage.saveHeader()\">Simpan</button>\n" +
        "                            <button id='resetHeader' name='btnResetHeader' onclick='ConstructPage.resetHeader()'>Reset</button>\n" +
        "                        </td>\n" +
        "                    </tr>";
    html = html + tr;
    html = html +"</tbody></table>\n" +
        "        </div>\n" +
        "    </fieldset>";

    html = html +
        " <div id='detailField' style='clear:both'><fieldset>\n" +
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
    if(document.getElementById('nobatch')!=''){
    tr = tr + "\n" +
        "                    <tr>\n" +
        "                        <td colspan='"+(sender.columnSize*2)+"'>\n" +
        "                            <button type='button' id='btnSaveDetail'  name='saveDetail' onclick=\"ConstructPage.saveDetail()\">Simpan</button>"+
        "                            <button type='button' id='btnResetDetail'  name='resetDetail' onclick='ConstructPage.resetDetail()'>Batal</button> " +
        "                        </td>\n" +
        "                    </tr>";
    } else {
    tr = tr + "\n" +
        "                    <tr>\n" +
        "                        <td colspan='"+(sender.columnSize*2)+"'>\n" +
        "                            <button type='button' id='btnSaveDetail' disabled name='saveDetail' onclick=\"ConstructPage.saveDetail()\">Simpan</button>"+
        "                            <button type='button' id='btnResetDetail' disabled name='resetDetail' onclick='ConstructPage.resetDetail()'>Batal</button> " +
        "                        </td>\n" +
        "                    </tr>";
    }
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
    'entry':'keu_batch.php?proses=entry',
    'list':'keu_batch.php?proses=list',
    'edit':'keu_batch.php?proses=edit',
    'listrow':'keu_batch.php?proses=listrow',
    'savedetail':'keu_batch.php?proses=savedetail',
    'deletedetail':'keu_batch.php?proses=deletedetail',
    'saveheader':'keu_batch.php?proses=saveheader',
    'deleteheader':'keu_batch.php?proses=deleteheader',
};
function delayCallback(obj) {
    if (obj.sectionToBuild.build.list) {
        obj.listSection.init();
        obj.listSection.showRowPerPage=20;
        obj.listSection.tableId = 'listTable';
        obj.listSection.tableHeader = {
            headers: ['No Batch', 'Tanggal Batch', 'Total Invoice', 'Total Ppn', 'Total Pph', 'Status'],
            actions: ['Edit', 'Delete'],
        };
        obj.listSection.arrayFilters=[
            {'nobatch':'searchNoBatch'},
            {'tglbatch':'searchTglBatch'},
        ];
        obj.listSection.onListDataReady = batchDataList;
        obj.listSection.onActionColumnRender = columActionRender;
        obj.listSection.global.initGlobalProperty('contentBoxlist', true, 'keu_batch.php?proses=list');
    }
};
ConstructPage.onInit=function(obj){
    if (obj.sectionToBuild.build.functional ) {
        obj.functionalSection.init();
        obj.functionalSection.global.initGlobalProperty('contentBoxmenu',true,'keu_batch.php?proses=functional');
    }
    obj.entrySection.columnSize=2;
    obj.entrySection.entryModule='Batch';
    obj.entrySection.headerKeys={'nobatch':''};
    obj.entrySection.detailKeys={'nobatch':'','noinvoice':''};
    obj.entrySection.onPrepareEntry = prepareEntryForm;
    obj.delay(delayCallback);
};

ConstructPage.init();

function getNoInvoice(title, content, ev){
    width = '850';
    height = '620';
    showDialog1(title, content, width, height, ev);

    param = '';
    tujuan = 'keu_batch.php?proses=getinvoiceform';
    post_response_text(tujuan = 'keu_batch.php?proses=getinvoiceform', param, respog);

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
    tujuan = 'keu_batch.php?proses=getinvoices';
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