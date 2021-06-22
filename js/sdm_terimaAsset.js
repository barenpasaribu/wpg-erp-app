let cfilter = "",
        ctable = "",
        caction = "",
        selected = [],
        aSelected = [];
let cbtnEdit = "<img class=\"tblEdit\" src='" + imgEdit + "' style='cursor:pointer' title='Rubah' width='15px'>";
let cbtnDelete = "<img class=\"tblDelete\" src='" + imgDelete + "' style='cursor:pointer' title='Hapus' width='15px'>";
let cbtnPdf = "<img class=\"tblPdf\" src='" + imgPdf + "' style='cursor:pointer' title='PDF' width='15px'>";

let data_unit = JSON.stringify(aOrganisasi);
let tglberakhir_old = '';

$("#modal-input").css("display", "none");

function formatddmmyyyy(pdate) {
        let dd = pdate.substr(8, 2);
        let mm = pdate.substr(5, 2);
        let yyyy = pdate.substr(0, 4);
        return dd + '-' + mm + '-' + yyyy;
}

function formatyyyymmdd(pdate) {
        let dd = pdate.substr(0, 2);
        let mm = pdate.substr(3, 2);
        let yyyy = pdate.substr(6, 4);
        return yyyy + '-' + mm + '-' + dd;
}

function init_Button(row) {
        // cbtnEdit = "<button class=\"tblEdit\">Rubah</button>";
        // cbtnDelete = "<button class=\"tblDelete\">Hapus</button>";
        return cbtnEdit + ' &nbsp&nbsp ' + cbtnDelete + ' &nbsp&nbsp ' + cbtnPdf;
}

function init_Search() {
        $('#data-table tfoot th').each(function (index) {
                if (index == 1 || index == 2 || index == 3 || index == 5 || index == 6 || index == 7 || index == 8 || index == 9 || index == 11) {
                        let ctitle = $(this).text();
                        $(this).html('<input type="text" placeholder="Cari ' + ctitle + '" />');
                }
        });
}

function init_DialogInput() {
        $("#modal-input").dialog({
                width: "auto",
                position: {
                        my: "center center",
                        at: "center center",
                        of: "#container"
                },
                closeOnEscape: false,
                autoOpen: false,
                modal: true,
                show: {
                        effect: "fade",
                        duration: 500
                        // effect: "blind",
                        // duration: 1000
                },
                hide: {
                        effect: "explode",
                        duration: 500
                },
                close: function (event, ui) {
                        $("#modal-input").dialog("option", "fade", null);
                }
        });
}

function load_Datatable() {
        ctable = $('#data-table').DataTable({
                "language": {
                        "lengthMenu": "Tampil _MENU_ records per halaman",
                        "zeroRecords": "Data Kosong",
                        "info": "Halaman _PAGE_ of _PAGES_",
                        "infoEmpty": "Tidak ada records",
                        "infoFiltered": "(Saring Dari _MAX_ Jumlah records)",
                        "search": "Cari:",
                        "zeroRecords": "Tidak ada record yang ditemukan",
                        "paginate": {
                                "previous": "Sebelum",
                                "next": "Sesudah",
                                "first": "Pertama",
                                "last": "Terakhir"
                        }
                },
                "order": [
                        [3, 'desc']
                ],
                "scrollY": "300px",
                "scrollX": true,
                "processing": true,
                "serverSide": true,
                "searching": true,
                'pageLength': 10,
                'lengthMenu': [
                        [10, 20, 25, 50, -1],
                        [10, 20, 25, 50, 'All']
                ],
                "ajax": {
                        "url": "sdm_terimaAsset_datatable.php",
                        "type": "POST",
                        "data": {
                                "filter": function () {
                                        return data_unit
                                }
                        }
                },
                "columnDefs": [{
                                "targets": 0,
                                "data": null,
                                "className": "dt-body-center",
                                "render": function (data, type, row, meta) {
                                        return init_Button(row)
                                }
                                // "defaultContent": cbutton
                        },
                        {
                                "targets": [4],
                                "visible": false,
                                "searchable": false
                        },
                        {
                                "targets": [1, 3, 5, 7],
                                "className": "dt-body-center"
                        },
                        {
                                targets: [9, 10],
                                class: "wrapok"
                        }
                ],
                'rowCallback': function (row, data, dataIndex) {
                        // Get row ID
                        // var rowId = data[0];

                        //-- Buat row color condition
                        let lBerakhir = (data[11] === "" ? false : true); // tgl.Berakhir

                        if (lBerakhir) {
                                // $('td', row).css('color', 'white');
                                $('td', row).css('background-color', '#ff9999');
                        }
                },
                dom: 'Blfrtip',
                buttons: {
                        dom: {
                                button: {
                                        tag: 'button',
                                        className: ''
                                }
                        },
                        buttons: [{
                                        text: 'Tambah',
                                        className: 'copyButton',
                                        titleAttr: 'Tambah Data',
                                        action: function (e, dt, node, config) {
                                                getRowSelected(true, []);
                                                addNew([]);
                                        },
                                        enabled: true
                                },
                                {
                                        text: 'Refresh',
                                        className: 'excelButton',
                                        titleAttr: 'Refresh',
                                        action: function (e, dt, node, config) {
                                                refresh();
                                        },
                                        enabled: true
                                }
                        ]
                }
        });
}

//-- Array Maping Detil row
function getRowSelected(lnew, rowdata) {
        aSelected = {
                'trseq': (lnew ? "0" : rowdata[0]),
                'kodeorg': (lnew ? "" : rowdata[1]),
                'namaorg': (lnew ? "" : rowdata[2]),
                // 'tglterima': (lnew ? "" : formatddmmyyyy(rowdata[3])),
                'tglterima': (lnew ? "" : rowdata[3]),
                'karyawanid': (lnew ? "" : rowdata[4]),
                'nik': (lnew ? "" : rowdata[5]),
                'namakaryawan': (lnew ? "" : rowdata[6]),
                'kodeasset': (lnew ? "" : rowdata[7]),
                'namaasset': (lnew ? "" : rowdata[8]),
                'keteranganasset': (lnew ? "" : rowdata[9]),
                'keterangan': (lnew ? "" : rowdata[10]),
                // 'tglberakhir': (lnew ? "" : formatddmmyyyy(rowdata[11]))
                'tglberakhir': (lnew ? "" : rowdata[11])
        };
}

//-- Disabled input
function disabled_input(ldisabled) {
        $("#unit").prop("disabled", ldisabled);
        $("#tglterima").prop("disabled", ldisabled);
        $("#karyawanid").prop("disabled", ldisabled);
        $("#kodeasset").prop("disabled", ldisabled);
        $("#keterangan").prop("disabled", ldisabled);
        $("#tglberakhir").prop("disabled", ldisabled);
}

//-- Reset Input
function reset_input() {
        $("#unit").val(null).trigger("change");
        $("#namaunit").val("");
        $("#tglterima").val("");
        $("#karyawanid").val(null).trigger("change");
        $("#nik").val("");
        $("#namakaryawan").val("");
        $("#kodeasset").val(null).trigger("change");
        $("#namaasset").val("");
        $("#keteranganasset").val("");
        $("#keterangan").val("");
        $("#tglberakhir").val("");
}

//--- Prepare input
function get_input(paction, rowdata) {
        if (paction == 'ADD') {
                reset_input();
                disabled_input(false);
        } else if (paction == 'EDIT') {
                disabled_input(true);
                $("#trseq").val(aSelected['trseq']);
                $("#unit").select2("trigger", "select", {
                        data: {
                                id: aSelected['kodeorg'],
                                text: aSelected['namaorg']
                        }
                });
                $("#namaunit").val(aSelected['namaorg']);
                $("#tglterima").val(aSelected['tglterima']);
                $("#karyawanid").select2("trigger", "select", {
                        data: {
                                id: aSelected['karyawanid'],
                                text: aSelected['namakaryawan']
                        }
                });
                $("#nik").val(aSelected['nik']);
                $("#namakaryawan").val(aSelected['namakaryawan']);
                $("#kodeasset").select2("trigger", "select", {
                        data: {
                                id: aSelected['kodeasset'],
                                text: aSelected['namaasset']
                        }
                });
                $("#namaasset").val(aSelected['namaasset']);
                $("#keteranganasset").val(aSelected['keteranganasset']);
                $("#keterangan").val(aSelected['keterangan']);
                $("#tglberakhir").val(aSelected['tglberakhir']);

                // $("#tglterima").prop("disabled", false);
                $("#karyawanid").prop("disabled", false);
                $("#keterangan").prop("disabled", false);
                $("#tglberakhir").prop("disabled", false);

        } else if (paction == 'DELETE') {

                disabled_input(true);

                $("#trseq").val(aSelected['trseq']);
                $("#unit").select2("trigger", "select", {
                        data: {
                                id: aSelected['kodeorg'],
                                text: aSelected['namaorg']
                        }
                });
                $("#namaunit").val(aSelected['namaorg']);
                $("#tglterima").val(aSelected['tglterima']);
                $("#karyawanid").select2("trigger", "select", {
                        data: {
                                id: aSelected['karyawanid'],
                                text: aSelected['namakaryawan']
                        }
                });
                $("#nik").val(aSelected['nik']);
                $("#namakaryawan").val(aSelected['namakaryawan']);
                $("#kodeasset").select2("trigger", "select", {
                        data: {
                                id: aSelected['kodeasset'],
                                text: aSelected['namaasset']
                        }
                });
                $("#namaasset").val(aSelected['namaasset']);
                $("#keteranganasset").val(aSelected['keteranganasset']);
                $("#keterangan").val(aSelected['keterangan']);
                $("#tglberakhir").val(aSelected['tglberakhir']);
        }
}

//-- Add data
function addNew(aRowdata) {
        caction = "ADD";
        $("#modal-input").dialog("open");
        $("#modal-input").dialog("option", "title", 'Tambah Data');
        $("#proses_data").html("Simpan");
        get_input(caction, aRowdata);
}

//-- Edit Data
function editData(aRowdata) {
        caction = "EDIT";
        $("#modal-input").dialog("open");
        $("#modal-input").dialog("option", "title", 'Rubah Data');
        $("#proses_data").html("Simpan");
        get_input(caction, aRowdata);
}

//-- Delete Data
function deleteData(aRowdata) {
        caction = "DELETE";
        $("#modal-input").dialog("open");
        $("#modal-input").dialog("option", "title", 'Hapus Data');
        $("#proses_data").html("Hapus");
        get_input(caction, aRowdata);
}

//-- Refresh Data
function refresh(ctype) {
        ctable.ajax.reload(null, false);
}

//-- Validasi input
function input_validasi() {
        let loke = false;
        let unit = $("#unit").val();
        let karyawanid = $("#karyawanid").val();
        let kodeasset = $("#kodeasset").val();
        let tglterima = $("#tglterima").val();
        let tglberakhir = $("#tglberakhir").val();

        unit = (unit == null ? '' : unit);
        karyawanid = (karyawanid == null ? '' : karyawanid);
        kodeasset = (kodeasset == null ? '' : kodeasset);
        tglterima = ((tglterima == null || tglterima == '') ? '' : formatyyyymmdd(tglterima));
        tglberakhir = (tglberakhir == null || tglberakhir == '' ? '' : formatyyyymmdd(tglberakhir));

        if (unit.length < 1) {
                alert('Unit tidak boleh kosong..!');
        } else if (karyawanid.length < 1) {
                alert('Karyawan tidak boleh kosong..!');
        } else if (kodeasset.length < 1) {
                alert('Kode Asset tidak boleh kosong..!');
        } else if (tglterima.length < 1) {
                alert('Tanggal terima tidak boleh kosong..!');
        } else if (tglberakhir.length > 1 && tglberakhir < tglterima) {
                alert('Tanggal berakhir harus >= Tanggal terima....!');
        } else {
                loke = true;
        }
        return loke;
}

function setFormatSelect(result) {
        let r = result.text.split('|');
        let cstr = '';
        let i;
        for (i = 0; i < r.length; i++) {
                if (i == 1) {
                        cstr += '<td width="100px">' + r[i] + "</td>";
                } else {
                        cstr += '<td width="200px">' + r[i] + "</td>";
                }
                cstr += '<td width="10px">&nbsp&nbsp</td>';
        }

        let $results = $(
                "<table><tr>" + cstr + "</tr></table>"
        );
        return $results;
}

function exportPDF(namaorg, karyawanid, nik, namakaryawan, tglterima) {

        let url_target = "sdm_terimaasset_form.php";
        let rows_selected = [namaorg, karyawanid, nik, namakaryawan, tglterima];
        $.ajax({
                type: 'POST',
                url: "slave_terimaAsset.php",
                data: {
                        tipe: 'LIST_TERIMA_ASSET',
                        value: rows_selected
                },
                success: function (result) {
                        window.open(url_target);
                }
        });
}


$(document).ready(function () {

        init_Search();
        init_DialogInput();
        load_Datatable();

        //-- Column Search
        ctable.columns().every(function (index) {
                if (index == 1 || index == 2 || index == 3 || index == 5 || index == 6 || index == 7 || index == 8 || index == 9 || index == 11) {
                        var that = this;
                        $('input', this.footer()).on('keyup change clear', function () {
                                if (that.search() !== this.value) {
                                        that
                                                .search(this.value)
                                                .draw();
                                }
                        });
                }
        });

        //---  Edit 
        $('#data-table tbody').on('click', '.tblEdit', function () {
                let rowdata = ctable.row($(this).parents('tr')).data();
                getRowSelected(false, rowdata);
                tglberakhir_old = $("#tglberakhir").val();
                editData(rowdata);
        });

        //--  Delete 
        $('#data-table tbody').on('click', '.tblDelete', function () {
                let rowdata = ctable.row($(this).parents('tr')).data();
                getRowSelected(false, rowdata);
                deleteData(rowdata);
        });

        //-- Export PDF
        $('#data-table tbody').on('click', '.tblPdf', function () {
                let rowdata = ctable.row($(this).parents('tr')).data();
                getRowSelected(false, rowdata);
                exportPDF(aSelected['namaorg'], aSelected['karyawanid'], aSelected['nik'], aSelected['namakaryawan'], aSelected['tglterima'], );

        });

        //-- Cancel moda input
        $("#cancel_data").click(function () {
                $("#modal-input").dialog("close");
        });

        $("#unit").select2({
                placeholder: "Cari Unit ...",
                width: '200px',
                allowClear: true,
                ajax: {
                        url: "slave_getmaster_list.php",
                        type: "post",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                                return {
                                        q: params.term,
                                        table: 'UNIT',
                                        filter: cunit,
                                        page: params.page
                                };
                        },
                        processResults: function (data, params) {
                                params.page = params.page || 1;
                                return {
                                        results: data.items,
                                        pagination: {
                                                more: (params.page * 30) < data.total_count
                                        }
                                };
                        },
                        cache: false
                },
                // templateSelection: setFormat
                templateResult: setFormatSelect

        });

        $("#karyawanid").select2({
                placeholder: "Cari Karyawan ...",
                width: '600px',
                allowClear: true,
                ajax: {
                        url: "slave_getmaster_list.php",
                        type: "post",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                                return {
                                        q: params.term,
                                        table: 'KARYAWAN',
                                        filter: $("#unit").val(),
                                        page: params.page
                                };
                        },
                        processResults: function (data, params) {
                                params.page = params.page || 1;
                                return {
                                        results: data.items,
                                        pagination: {
                                                more: (params.page * 30) < data.total_count
                                        }
                                };
                        },
                        cache: false
                },
                // templateSelection: setFormat
                templateResult: setFormatSelect

        });

        $("#kodeasset").select2({
                placeholder: "Cari Asset ...",
                width: '600px',
                allowClear: true,
                ajax: {
                        url: "slave_getmaster_list.php",
                        type: "post",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                                return {
                                        q: params.term,
                                        table: 'ASSET',
                                        filter: $("#unit").val(),
                                        page: params.page
                                };
                        },
                        processResults: function (data, params) {
                                params.page = params.page || 1;
                                return {
                                        results: data.items,
                                        pagination: {
                                                more: (params.page * 30) < data.total_count
                                        }
                                };
                        },
                        cache: false
                },
                // templateSelection: setFormat
                templateResult: setFormatSelect

        });

        $('#unit').on('select2:select', function (e) {
                let code = e.params.data['id'];
                let aname = e.params.data['text'].split("|");
                $("#unit").val(code); /* unit */
                $("#namaunit").val(aname[1]); /* nama unit */
                $("#karyawanid").val(null).trigger("change");
                $("#kodeasset").val(null).trigger("change");

        });

        $('#karyawanid').on('select2:select', function (e) {
                let code = e.params.data['id'];
                let aname = e.params.data['text'].split("|");
                $("#karyawanid").val(code); /* karyawanid */
                $("#nik").val(aname[0]); /* nik */
                $("#namakaryawan").val(aname[1]); /* nama karyawan */
                // $("#kodeasset").val(null).trigger("change");
        });

        $('#kodeasset').on('select2:select', function (e) {
                let code = e.params.data['id'];
                let aname = e.params.data['text'].split("|");
                $("#kodeasset").val(code); /* kode asset */
                $("#namaasset").val(aname[1]); /* nama asset */
                $("#keteranganasset").val(aname[2]); /* nama asset detail */
        });

        //-- Simpan data
        $("#proses_data").click(function () {
                if (input_validasi()) {
                        let loginname = clogin;
                        let action = caction;
                        let trseq = $("#trseq").val();
                        let kodeorg = $("#unit").val();
                        let karyawanid = $("#karyawanid").val();
                        let kodeasset = $("#kodeasset").val();
                        let tglterima = $("#tglterima").val();
                        let keterangan = $("#keterangan").val();
                        let tglberakhir = $("#tglberakhir").val();

                        tglterima = formatyyyymmdd(tglterima);
                        tglberakhir = (tglberakhir === '0000-00-00' || tglberakhir === '' ? '' : formatyyyymmdd(tglberakhir));

                        $.ajax({
                                url: "sdm_terimaAsset_crud.php",
                                method: "POST",
                                data: {
                                        loginname: loginname,
                                        action: action,
                                        trseq: trseq,
                                        kodeorg: kodeorg,
                                        tglterima: tglterima,
                                        karyawanid: karyawanid,
                                        kodeasset: kodeasset,
                                        keterangan: keterangan,
                                        tglberakhir: tglberakhir
                                },
                                success: function (result) {
                                        let objResult = JSON.parse(result);
                                        if (objResult.flag === "0") {
                                                alert('Koneksi gagal..');
                                        } else if (objResult.flag === "1") {
                                                alert(objResult.pesan);
                                        } else {
                                                alert(objResult.pesan);
                                                $("#modal-input").dialog("close");
                                                ctable.ajax.reload(null, false);
                                        }
                                }
                        });
                };
        });

});