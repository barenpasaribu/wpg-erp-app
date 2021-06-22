function replaceDate(){
	var hasil;
	var sdate = document.getElementById("paradate").value;
	var hasil = sdate.slice(0,4)+ "-" + sdate.slice(5,7) + "-" + sdate.slice(8,10);
	//alert('running');
	document.getElementById("tempdate").value = hasil;
}
function replaceStart(){
	var hasil;
	var sdate = document.getElementById("startdate").value;
	var hasil = sdate.slice(0,4)+ "-" + sdate.slice(5,7) + "-" + sdate.slice(8,10);
	document.getElementById("varstart").value = hasil;
}
function replaceEnd(){
	var hasil;
	var sdate = document.getElementById("enddate").value;
	var hasil = sdate.slice(0,4)+ "-" + sdate.slice(5,7) + "-" + sdate.slice(8,10);
	document.getElementById("varend").value = hasil;
}
function showtable(){
	var ev = 'event';
	param='tglAwal=' + document.getElementById("varstart").value;
	param= param + '&tglAkhir=' + document.getElementById("varend").value;
	tujuan = 'pmn_slave_tbsrendemen_data.php?'+param;	
	//alert(param);
	title="Data Rerendemen";
	width='800';
	height='400';
	content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>";
	showDialog1(title,content,width,height,ev);
}
function showdata(){
		var valid = "";
        var tgl = document.getElementById('tempdate').value;
		var org = document.getElementById('idPabrikTahun').value;
		//alert('RUnning');
		//alert(dppCPO+ " ! " + dppPK + " ! " + dppCK);
		//alert(" ! " + valid);
        if (trim(valid) == ''){
            tgl = trim(tgl);
            var param = 'tgl=' + tgl + '&org=' + org + '&method=list';
			param = param + '&';	
            var tujuan = 'pmn_slave_tbsrendemen.php';
            //alert(param);
            //post_response_text(tujuan, param, respog);
        }		
        function respog() {
            if (con.readyState == 4) {
                if (con.status == 200) {
                    busy_off();
                        if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                        } else {
                             //alert(con.responseText);
                            document.getElementById('container').innerHTML = con.responseText;
                        }
                } else {
                    busy_off();
                    error_catch(con.status);
                }
            }
        }
}
function getTransaksi(){ 
	var tgl = document.getElementById('tempdate').value;
	var org = document.getElementById('idPabrikTahun').value;
    param= 'tgl=' + tgl + '&org=' + org + '&method=list';
    tujuan='pmn_slave_rendemenAll.php';
	if(tgl==''){
		replaceDate();
		alert('Mohon isi tanggal terlebih dahulu');
	}else{
		post_response_text(tujuan, param, respog);
	}
    
    function respog(){
        if (con.readyState == 4) {
           if (con.status == 200) {
			   busy_off();
			   if (!isSaveResponse(con.responseText)) {
				   alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					showById('printPanel');
					document.getElementById('container').innerHTML=con.responseText;
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}
    }		
}
function listData(){
	var x = document.getElementById('dataForm');
	if (x.style.display === "none") {
		x.style.display = "block";
	} else {
		x.style.display = "none";
	}					
}
function cleardata(param){
	var result;
	if(param =='1'){
		result = confirm("Apa anda yakin ingin membatalkan proses");
		if(result == true){
			do_load('pmn_tbsrendemen.php');
		}
	}else{
		do_load('pmn_tbsrendemen.php');
	}
/* con = 'Apa anda yakin ingin membatalkan proses?';
result = confirm("Apa anda yakin ingin membatalkan proses");
if(result == true){
	do_load('pmn_tbsrendemen.php');
} */
}
function cleardata2(param){
	var result;
	if(param =='1'){
		result = confirm("Apa anda yakin ingin membatalkan proses");
		if(result == true){
			do_load('pmn_rendemenAll.php');
		}
	}else{
		do_load('pmn_rendemenAll.php');
	}
/* con = 'Apa anda yakin ingin membatalkan proses?';
result = confirm("Apa anda yakin ingin membatalkan proses");
if(result == true){
	do_load('pmn_tbsrendemen.php');
} */
}
function previewBast(notransaksi,ev){	
	if(notransaksi == "Err"){
		alert('Silakan pilih Tanggal dan Organisasi terlebih dahulu');
	}else{
		//var r = confirm('Pastikan anda telah menyimpan data ');
        //if (r == true) {
			param='notransaksi='+notransaksi;
			tujuan = 'pmn_slave_print_tbsrendemen.php?'+param;	
			//alert('running');
			title=notransaksi;
			width='700';
			height='400';
			content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>";
			showDialog1(title,content,width,height,ev);
		//}
	}
}
function previewExcel(notransaksi,ev){	
	param='notransaksi='+notransaksi;
	tujuan = 'pmn_slave_excel_tbsrendemen.php?'+param;	
	//alert('running');
	title=notransaksi;
	width='900';
	height='500';
	content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>";
	showDialog1(title,content,width,height,ev);
}

function getOerRate(){
	var y = (parseFloat(document.getElementById("pricekg").value) - parseFloat(document.getElementById("respk").value)) / parseFloat(document.getElementById("resCPO").value) *100;
	document.getElementById("totalOer").value = y.toFixed(2);
}
function getOerKontrak(){
    var totalRowCount = document.getElementById("conter").rows.length;
	var y = document.getElementById("respk").value;
	var z = document.getElementById("resCPO").value;
    for (i = 0; i < totalRowCount; i++) {
     	var x = document.getElementById("conter").rows[i].cells[3].innerHTML;
		x = x.replace(",", "");
		var k = parseFloat(x);
		var r = (k-y)/z *100;
		r = r.toFixed(2)
		document.getElementById("conter").rows[i].cells[6].innerHTML = r + '%';
    }
	getOerRate();
	document.getElementById("confoot").rows[0].cells[6].innerHTML = document.getElementById("totalOer").value + '%';
}
function getCountAll(){
	countCPO(document.getElementById("resultCPO"));
	countPK(document.getElementById("resultpk"));
	countCk(document.getElementById("resultcangkang"));
	resCPO(document.getElementById("resCPO"));
	resPK(document.getElementById("respk"));
	resCk(document.getElementById("rescangkang"));
	amountOer(document.getElementById("amountOer"));
	amountPK(document.getElementById("amountPK"));
	amountCk(document.getElementById("amountCk"));
	jualOer(document.getElementById("jualOer"));
	jualCk(document.getElementById("jualCk"));
	jualPK(document.getElementById("jualPK"));
	cpoprodcost(document.getElementById("cpoprodcost"));
	pkprodcost(document.getElementById("pkprodcost"));
	ckprodcost(document.getElementById("ckprodcost"));
	kandircost(document.getElementById("kandircost"));
	hasil(document.getElementById("totalHasil"));
	totcostCpo(document.getElementById("totcostCpo"));
	totcostPK(document.getElementById("totcostPK"));
	totcostCk(document.getElementById("totcostCk"));
	decreasCost(document.getElementById("decreasCost"));
	totalCost(document.getElementById("totalCost"));
	getOerRate();
}
function getMarginOer(){
	var y = parseFloat(document.getElementById("actualOer").value) - parseFloat(document.getElementById("totalOer").value);
	document.getElementById("marginper").value = y.toFixed(2);
}
function countCPO(x){
	var y = (document.getElementById("CPOPrice").value / document.getElementById("ppncpo").value) - document.getElementById("costCPO").value - document.getElementById("TransCPO").value;
	var z = parseFloat(document.getElementById("CPOPrice").value) / parseFloat(document.getElementById("ppncpo").value);
	document.getElementById("dppOer").value = z.toFixed(2);
	x.value = y.toFixed(2);
}
function countPK(x){
	var y = (document.getElementById("pkPrice").value / document.getElementById("ppnpk").value) - document.getElementById("costpk").value - document.getElementById("Transpk").value;
	var z = parseFloat(document.getElementById("pkPrice").value) / parseFloat(document.getElementById("ppnpk").value);
	document.getElementById("dppPK").value = z.toFixed(2);
	x.value = y.toFixed(2);
}
function countCk(x){
	var y = (document.getElementById("cangkangPrice").value / document.getElementById("ppnck").value) - document.getElementById("costcangkang").value - document.getElementById("Transcangkang").value;
	var z = parseFloat(document.getElementById("cangkangPrice").value) / parseFloat(document.getElementById("ppnck").value);
	document.getElementById("dppCk").value = z.toFixed(2);
	x.value = y.toFixed(2);
}
function resCPO(x){
	if(document.getElementById("OERpabrikCPO") && document.getElementById("OERpabrikCPO").value && document.getElementById("OERpabrikCPO").value > 0){
		var y = document.getElementById("resultCPO").value * (document.getElementById("OERpabrikCPO").value / 100);
	}else{
		var y = document.getElementById("resultCPO").value;
	}
	countCPO(document.getElementById("resultCPO"));
	countPK(document.getElementById("resultpk"));
	countCk(document.getElementById("resultcangkang"));
	x.value = y;
	getOerKontrak();
}
function resPK(x){
	if(document.getElementById("OERpabrikpk") && document.getElementById("OERpabrikpk").value && document.getElementById("OERpabrikpk").value > 0){		
		var y = document.getElementById("resultpk").value * (document.getElementById("OERpabrikpk").value / 100);
	}else{
		var y = parseFloat(document.getElementById("resultpk").value);
	}
	countCPO(document.getElementById("resultCPO"));
	countPK(document.getElementById("resultpk"));
	countCk(document.getElementById("resultcangkang"));
	x.value = y.toFixed(2);
	getOerKontrak();
}

function resCk(x){
	if(document.getElementById("OERpabrikcangkang") && document.getElementById("OERpabrikcangkang").value && document.getElementById("OERpabrikcangkang").value > 0){
		var y = document.getElementById("resultcangkang").value * (parseFloat(document.getElementById("OERpabrikcangkang").value) / 100);
	}else{
		var y = parseFloat(document.getElementById("resultcangkang").value);
	}
	countCPO(document.getElementById("resultCPO"));
	countPK(document.getElementById("resultpk"));
	countCk(document.getElementById("resultcangkang"));
	x.value = y.toFixed(2);
	getOerKontrak();
}
function amountOer(x){
	var y = (document.getElementById("actualOer").value / 100) * document.getElementById("tonasetotal").value;
	x.value = y.toFixed(2);
	document.getElementById("oerCostCpo").value = y.toFixed(2);
	document.getElementById("tripCostCpo").value = y.toFixed(2);
	getMarginOer();
}
function jualOer(x){
	var y = document.getElementById("amountOer").value * document.getElementById("dppOer").value;
	x.value = y.toFixed(2);
}
function amountPK(x){
	var y = (document.getElementById("actualPK").value/100) * document.getElementById("tonasetotal6").value;
	x.value = y.toFixed(2);
	document.getElementById("oerCostPK").value = y.toFixed(2);
	document.getElementById("tripCostPK").value = y.toFixed(2);
	getMarginOer();
}
function jualPK(x){
	var y = document.getElementById("amountPK").value * document.getElementById("dppPK").value;
	x.value = y.toFixed(2);
	getOerRate();
}
function amountCk(x){
	var y = (document.getElementById("actualCk").value/100) * document.getElementById("tonasetotal6").value;
	x.value = y.toFixed(2);
	document.getElementById("oerCostCk").value = y.toFixed(2);
	document.getElementById("tripCostCk").value = y.toFixed(2);
	getMarginOer();
}
function jualCk(x){
	var y = document.getElementById("amountCk").value * document.getElementById("dppCk").value;
	x.value = y.toFixed(2);
}
function hasil(x){
	getOerRate();
	getOerKontrak();
	getMarginOer();
	var y = parseFloat(document.getElementById("jualOer").value) + parseFloat(document.getElementById("jualPK").value) + parseFloat(document.getElementById("jualCk").value);
	x.value = y.toFixed(2);
}
function cpoprodcost(x){
	var y = parseFloat(document.getElementById("oerCostCpo").value) * parseFloat(document.getElementById("amountcpoprod").value);
	x.value = y.toFixed(2);
}
function pkprodcost(x){
	var y = parseFloat(document.getElementById("oerCostPK").value) * parseFloat(document.getElementById("amountpkprod").value);
	x.value = y.toFixed(2);
}
function ckprodcost(x){
	var y = parseFloat(document.getElementById("amountckprod").value) * parseFloat(document.getElementById("oerCostCk").value);
	x.value = y.toFixed(2);
}
function kandircost(x){
	var y = parseFloat(document.getElementById("tonasetotal").value) * parseFloat(document.getElementById("amountkandir").value);
	x.value = y.toFixed(2);
}
function totcostCpo(x){
	var y = parseFloat(document.getElementById("tripCostCpo").value) * parseFloat(document.getElementById("transCostCpo").value);
	x.value = y.toFixed(2);
}
function totcostPK(x){
	var y = parseFloat(document.getElementById("tripCostPK").value) * parseFloat(document.getElementById("transCostPK").value);
	x.value = y.toFixed(2);
}
function totcostCk(x){
	var y = parseFloat(document.getElementById("tripCostCk").value) * parseFloat(document.getElementById("transCostCk").value);
	x.value = y.toFixed(2);
}
function decreasCost(x){
	var y = parseFloat(document.getElementById("amountdecreas").value) * parseFloat(document.getElementById("tonasetotal").value);
	x.value = y.toFixed(2);
}
function totalCost(x){
	var y = parseFloat(document.getElementById("totalPurchase").value);
	y = y + parseFloat(document.getElementById("cpoprodcost").value);
	y= y + parseFloat(document.getElementById("pkprodcost").value);
	//y= y +	parseFloat(document.getElementById("ckprodcost").value);
	y= y + parseFloat(document.getElementById("kandircost").value);
	y= y + parseFloat(document.getElementById("totcostCpo").value);
	y= y + parseFloat(document.getElementById("totcostPK").value);
	//y= y + parseFloat(document.getElementById("totcostCk").value);
	y= y + parseFloat(document.getElementById("decreasCost").value);
	x.value = y;
}
function labafinal(){
	getCountAll();
	getOerRate();
	getOerKontrak();
	getMarginOer();
	var y = parseFloat(document.getElementById("totalHasil").value) - parseFloat(document.getElementById("totalCost").value);
	document.getElementById("labarugi").value = y.toFixed(2);
}
function simpan(){
		var valid = "";
        var tgl = document.getElementById('tempdate').value;
		var org = document.getElementById('organisasi').value;
		var CPOPrice = document.getElementById('CPOPrice').value;
        var pkPrice = document.getElementById('pkPrice').value;
        var cangkangPrice = document.getElementById('cangkangPrice').value;
		var ppncpo = document.getElementById('ppncpo').value;
        var ppnck = document.getElementById('ppnck').value;
        var ppnpk = document.getElementById('ppnpk').value;
        var biayaCPOdt = document.getElementById('costCPO').value;
        var biayaPKdt = document.getElementById('costpk').value;
        var biayaCKdt = document.getElementById('costcangkang').value;
        var transCPOdt = document.getElementById('TransCPO').value;
        var transPKdt = document.getElementById('Transpk').value;
        var transCKdt = document.getElementById('Transcangkang').value;
        var oerCPOdt = document.getElementById('OERpabrikCPO').value;
        var oerPKdt = document.getElementById('OERpabrikpk').value;
        var oerCKdt = document.getElementById('OERpabrikcangkang').value;

        var marginper = document.getElementById('marginper').value;
        var actCPO = document.getElementById('actualOer').value;
        var actPK = document.getElementById('actualPK').value;
        var actCK = document.getElementById('actualCk').value;
		var dppCPO = document.getElementById('dppOer').value;
        var dppPK = document.getElementById('dppPK').value;
        var dppCK = document.getElementById('dppCk').value;
		
        var biayaCPO = document.getElementById('amountcpoprod').value;
        var biayaPK = document.getElementById('amountpkprod').value;
        var biayaCK = document.getElementById('amountckprod').value;		
		var biayakandir = document.getElementById('amountkandir').value;	
        var transCPO = document.getElementById('transCostCpo').value;
        var transPK = document.getElementById('transCostPK').value;
        var transCK = document.getElementById('transCostCk').value;
		var decreas = document.getElementById('amountdecreas').value;
		
        var totalBiaya = document.getElementById('totalCost').value;
        var totalResult = document.getElementById('totalHasil').value;
		var totalPurch = document.getElementById('totalBeli').value;
		var labaRugi = document.getElementById('labarugi').value;
		var tonase = document.getElementById('tonasetotal').value;
		var pricekg = document.getElementById('pricekg').value;
		var met = document.getElementById('method').value;

		//alert(dppCPO+ " ! " + dppPK + " ! " + dppCK);
		//alert(" ! " + valid);
        if (trim(valid) == ''){
				var r = confirm('Simpan data Rendemen untuk tanggal ' + tgl);
                if (r == true) {
                        tgl = trim(tgl);
                        var param = 'tgl=' + tgl + '&org=' + org + '&method=' + met;
						param = param + '&CPOPrice=' + CPOPrice;
						param = param + '&pkPrice=' + pkPrice;
						param = param + '&cangkangPrice=' + cangkangPrice;
						param = param + '&margin=' + marginper;
						param = param + '&ppncpo=' + ppncpo;
						param = param + '&ppnck=' + ppnck;
						param = param + '&ppnpk=' + ppnpk;
						param = param + '&biayaCPOdt=' + biayaCPOdt;
						param = param + '&biayaPKdt=' + biayaPKdt;
						param = param + '&biayaCKdt=' + biayaCKdt;
						param = param + '&transCPOdt=' + transCPOdt;
						param = param + '&transPKdt=' + transPKdt;
						param = param + '&transCKdt=' + transCKdt;
						param = param + '&oerCPOdt=' + oerCPOdt;
						param = param + '&oerPKdt=' + oerPKdt;
						param = param + '&oerCKdt=' + oerCKdt;
						param = param + '&actCPO=' + actCPO;
						param = param + '&actPK=' + actPK;
						param = param + '&actCK=' + actCK;
						param = param + '&dppCPO=' + dppCPO;
						param = param + '&dppPK=' + dppPK;
						param = param + '&dppCK=' + dppCK;
						param = param + '&biayaCPO=' + biayaCPO;
						param = param + '&biayaPK=' + biayaPK;
						param = param + '&biayaCK=' + biayaCK;		
						param = param + '&biayakandir=' + biayakandir;	
						param = param + '&transCPO=' + transCPO;
						param = param + '&transPK=' + transPK;
						param = param + '&transCK=' + transCK;
						param = param + '&decreas=' + decreas;
						param = param + '&totalBiaya=' + totalBiaya;
						param = param + '&totalResult=' + totalResult;
						param = param + '&totalPurch=' + totalPurch;
						param = param + '&labaRugi=' + labaRugi;
						param = param + '&tonase=' + tonase;
						param = param + '&pricekg=' + pricekg;	
                        var tujuan = 'pmn_slave_tbsrendemen.php';
                        //alert(param);
                        post_response_text(tujuan, param, respog);
                }else{
					document.getElementById('labarugi').focus();
				}
        }else{
            document.getElementById('labarugi').focus();
        }
		
        function respog() {
            if (con.readyState == 4) {
                if (con.status == 200) {
                    busy_off();
                        if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                        } else {
                             //alert(con.responseText);
                            document.getElementById('container').innerHTML = con.responseText;
                        }
                } else {
                    busy_off();
                    error_catch(con.status);
                }
            }
        }
}
function getList() {
/*         var tgl = document.getElementById('tempdate').value;
        var org = document.getElementById('idPabrikTahun');
        org = kelompokvhc.options[kelompokvhc.selectedIndex].value;
        param = 'date=' + tgl + '&selectOrg=' + org + '&method=getdata';
        tujuan = 'pmn_tbsrendmen.php'; */
		alert(tujuan + " " + param);
/*         post_response_text(tujuan, param, respog);

        function respog() {
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                } else {
                                        //alert(con.responseText);
                                        document.getElementById('container').innerHTML = con.responseText;
                                }
                        } else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        } */
}

function countRendemen(){
	//alert('running');
	document.getElementById('manual').style.display = 'block';
	document.getElementById('result').style.display = 'block';
	document.getElementById('result2').style.display = 'block';
	var CPOPrice = parseFloat(document.getElementById('CPOPrice').value);
	var CPOsoldPrice = parseFloat(document.getElementById('CPOsoldPrice').value);
	var costCPO = parseFloat(document.getElementById('costCPO').value);
	var realCPO = parseFloat(document.getElementById('realCPO').value);
	var transCPO = parseFloat(document.getElementById('transCPO').value);
	var oerCPO = parseFloat(document.getElementById('oerCPO').value);
	
	var PKPrice = parseFloat(document.getElementById('PKPrice').value);
	var PKsoldPrice = parseFloat(document.getElementById('PKsoldPrice').value);
	var costPK = parseFloat(document.getElementById('costPK').value);
	var realPK = parseFloat(document.getElementById('realPK').value);
	var transPK = parseFloat(document.getElementById('transPK').value);
	var oerPK = parseFloat(document.getElementById('oerPK').value);
	
	var CkPrice = parseFloat(document.getElementById('CkPrice').value);
	var CksoldPrice = parseFloat(document.getElementById('CksoldPrice').value);
	var costCk = parseFloat(document.getElementById('costCk').value);
	var realCk = parseFloat(document.getElementById('realCk').value);
	var transCk = parseFloat(document.getElementById('transCk').value);
	var oerCk = parseFloat(document.getElementById('oerCk').value);
	var i;
	var j;
	var cpo;
	var pk;
	var ck;
	for(i=1; i <= 3; i++){
		document.getElementById('priceCPO['+i+']').innerHTML = CPOsoldPrice;
		document.getElementById('pricePK['+i+']').innerHTML = PKsoldPrice;
		document.getElementById('priceCANGKANG['+i+']').innerHTML = CksoldPrice;
		document.getElementById('oerCPO['+i+']').innerHTML = oerCPO;
		document.getElementById('oerPK['+i+']').innerHTML = oerPK;
		document.getElementById('oerCANGKANG['+i+']').innerHTML = oerCk;
		switch(i){
			case 3:
				document.getElementById('olahCPO['+i+']').innerHTML = realCPO;
				document.getElementById('olahPK['+i+']').innerHTML = realPK;
				document.getElementById('olahCANGKANG['+i+']').innerHTML = realCk;
				document.getElementById('angkutCPO['+i+']').innerHTML = '-';
				document.getElementById('angkutPK['+i+']').innerHTML = '-';
				document.getElementById('angkutCANGKANG['+i+']').innerHTML = '-';
				cpo = document.getElementById('priceCPO['+i+']').innerHTML / document.getElementById('ppnCPO['+i+']').innerHTML;
				cpo = cpo - document.getElementById('olahCPO['+i+']').innerHTML;
				cpo = cpo * document.getElementById('oerCPO['+i+']').innerHTML / 100;
				pk = document.getElementById('pricePK['+i+']').innerHTML / document.getElementById('ppnPK['+i+']').innerHTML;
				pk = pk - document.getElementById('olahPK['+i+']').innerHTML;
				pk = pk * document.getElementById('oerPK['+i+']').innerHTML /100;
				ck = document.getElementById('priceCANGKANG['+i+']').innerHTML / document.getElementById('ppnCANGKANG['+i+']').innerHTML;
				ck = ck - document.getElementById('olahCANGKANG['+i+']').innerHTML;
				ck = ck * document.getElementById('oerCANGKANG['+i+']').innerHTML / 100;
			break;

		  default:
				document.getElementById('olahCPO['+i+']').innerHTML = costCPO;
				document.getElementById('olahPK['+i+']').innerHTML = costPK;
				document.getElementById('olahCANGKANG['+i+']').innerHTML = costCk;
				document.getElementById('angkutCPO['+i+']').innerHTML = transCPO;
				document.getElementById('angkutPK['+i+']').innerHTML = transPK;
				document.getElementById('angkutCANGKANG['+i+']').innerHTML = transCk;
				cpo = document.getElementById('priceCPO['+i+']').innerHTML / document.getElementById('ppnCPO['+i+']').innerHTML;
				cpo = cpo - document.getElementById('olahCPO['+i+']').innerHTML;
				cpo = cpo - document.getElementById('angkutCPO['+i+']').innerHTML;
				cpo = cpo * document.getElementById('oerCPO['+i+']').innerHTML / 100;
				pk = document.getElementById('pricePK['+i+']').innerHTML / document.getElementById('ppnPK['+i+']').innerHTML;
				pk = pk - document.getElementById('olahPK['+i+']').innerHTML;
				pk = pk - document.getElementById('angkutPK['+i+']').innerHTML;
				pk = pk * document.getElementById('oerPK['+i+']').innerHTML /100;
				ck = document.getElementById('priceCANGKANG['+i+']').innerHTML / document.getElementById('ppnCANGKANG['+i+']').innerHTML;
				ck = ck - document.getElementById('olahCANGKANG['+i+']').innerHTML;
				ck = ck - document.getElementById('angkutCANGKANG['+i+']').innerHTML;
				ck = ck * document.getElementById('oerCANGKANG['+i+']').innerHTML /100;
		  break;
		}
		document.getElementById('hargatbsCPO['+i+']').innerHTML = cpo.toFixed(2);
		document.getElementById('hargatbsPK['+i+']').innerHTML = pk.toFixed(2);
		document.getElementById('hargatbsCANGKANG['+i+']').innerHTML = ck.toFixed(2);
	}
	var r;
	var tableRef = document.getElementById('result2');
	var baseoer = parseFloat(oerCPO) + 2.5;
	var tpricecpo = parseFloat(document.getElementById('hargatbsCPO[1]').innerHTML);
	var ppnpricecpo = parseFloat(document.getElementById('hargatbsCPO[2]').innerHTML);
	var costpricecpo = parseFloat(document.getElementById('hargatbsCPO[3]').innerHTML);
	var tpricepk = parseFloat(document.getElementById('hargatbsPK[1]').innerHTML);
	var tpricecangkang = parseFloat(document.getElementById('hargatbsCANGKANG[1]').innerHTML);
	var tpricepk2 = parseFloat(document.getElementById('hargatbsPK[3]').innerHTML);
	var tpricecangkang2 = parseFloat(document.getElementById('hargatbsCANGKANG[3]').innerHTML);
	var npricecpo;
 	if(tableRef.rows.length > 40){
		for(rows=1; rows <= 50; rows++){
			tableRef.deleteRow(2);
		}
	}
	if(oerCPO >= 100){
	baseoer= 20.5;
	}
	for(r=1; r <= 50; r++){
		npricecpo = tpricecpo * baseoer /100;
		var row   = tableRef.insertRow(2);
		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);
		var cell3 = row.insertCell(2);
		var cell4 = row.insertCell(3);
		var cell5 = row.insertCell(4);
		var cell6 = row.insertCell(5);
		var cell7 = row.insertCell(6);
		cell1.innerHTML = baseoer.toFixed(2) + '%';
		cell2.innerHTML = npricecpo.toFixed(2);
		cell3.innerHTML = tpricepk.toFixed(2);
		cell4.innerHTML = tpricecangkang.toFixed(2);
		cell5.innerHTML = (parseFloat(npricecpo) + parseFloat(tpricepk) + parseFloat(tpricecangkang)).toFixed(2);
		cell6.innerHTML = ((ppnpricecpo * baseoer /100) + parseFloat(tpricepk) + parseFloat(tpricecangkang)).toFixed(2);
		cell7.innerHTML = ((costpricecpo * baseoer / 100) + parseFloat(tpricepk2) + parseFloat(tpricecangkang2)).toFixed(2);
		if(baseoer.toFixed(2) == oerCPO){
			cell1.style.backgroundColor = "yellow"; 
		}
		baseoer = baseoer - 0.1;
	}
}

function tambahRow() {
    var row = document.getElementById("inputRow");
    var table = document.getElementById("manual");
	var numrow=table.rows.length;
	numrow = numrow+1;
    var clone = row.cloneNode(true);
    clone.id = 'brs' + numrow;
    table.appendChild(clone);
    var table = document.getElementById("manual");
	var numrow=table.rows.length-1;
	table.rows[numrow].cells[0].innerHTML="<div data-tip='Persentase Rendemen'><input type='text' id='rendemen"+numrow+"' style='width: 120px;' value='' onchange='manualRendemen(this)' /></div>";
	table.rows[numrow].cells[7].innerHTML='<button onclick="tambahRow()"> + </button>' + '<button onclick=deleteRow('+numrow+')> - </button>';
}

function manualRendemen(x){
	var baseoer = parseFloat(x.value);
	var tpricecpo = parseFloat(document.getElementById('hargatbsCPO[1]').innerHTML);
	var ppnpricecpo = parseFloat(document.getElementById('hargatbsCPO[2]').innerHTML);
	var costpricecpo = parseFloat(document.getElementById('hargatbsCPO[3]').innerHTML);
	var tpricepk = parseFloat(document.getElementById('hargatbsPK[1]').innerHTML);
	var tpricepk2 = parseFloat(document.getElementById('hargatbsPK[3]').innerHTML);
	var tpricecangkang = parseFloat(document.getElementById('hargatbsCANGKANG[1]').innerHTML);
	var tpricecangkang2 = parseFloat(document.getElementById('hargatbsCANGKANG[3]').innerHTML);
	var npricecpo;
	npricecpo = tpricecpo * baseoer /100;
	//cell1.innerHTML = baseoer.toFixed(2) + '%';
    var table = document.getElementById("manual");
	var numrow=table.rows.length-1;
    table.rows[numrow].cells[1].innerHTML=npricecpo.toFixed(2);
    table.rows[numrow].cells[2].innerHTML=tpricepk.toFixed(2);
    table.rows[numrow].cells[3].innerHTML=tpricecangkang.toFixed(2);
	table.rows[numrow].cells[4].innerHTML=(parseFloat(npricecpo) + parseFloat(tpricepk) + parseFloat(tpricecangkang)).toFixed(2);
	table.rows[numrow].cells[5].innerHTML=((ppnpricecpo * baseoer /100) + parseFloat(tpricepk) + parseFloat(tpricecangkang)).toFixed(2);
	table.rows[numrow].cells[6].innerHTML=((costpricecpo * baseoer / 100) + parseFloat(tpricepk2) + parseFloat(tpricecangkang2)).toFixed(2);
}
function deleteRow(numrow){
    var rowid = numrow + 1;
	var row = document.getElementById('brs'+rowid);
    row.parentNode.removeChild(row);
}
function simpanAll(parax){

 	var CPOPrice = parseFloat(document.getElementById('CPOPrice').value);
	var CPOsoldPrice = parseFloat(document.getElementById('CPOsoldPrice').value);
	var costCPO = parseFloat(document.getElementById('costCPO').value);
	var realCPO = parseFloat(document.getElementById('realCPO').value);
	var transCPO = parseFloat(document.getElementById('transCPO').value);
	var oerCPO = parseFloat(document.getElementById('oerCPO').value);

	var PKPrice = parseFloat(document.getElementById('PKPrice').value);
	var PKsoldPrice = parseFloat(document.getElementById('PKsoldPrice').value);
	var costPK = parseFloat(document.getElementById('costPK').value);
	var realPK = parseFloat(document.getElementById('realPK').value);
	var transPK = parseFloat(document.getElementById('transPK').value);
	var oerPK = parseFloat(document.getElementById('oerPK').value);
	
	var CkPrice = parseFloat(document.getElementById('CkPrice').value);
	var CksoldPrice = parseFloat(document.getElementById('CksoldPrice').value);
	var costCk = parseFloat(document.getElementById('costCk').value);
	var realCk = parseFloat(document.getElementById('realCk').value);
	var transCk = parseFloat(document.getElementById('transCk').value);
	var oerCk = parseFloat(document.getElementById('oerCk').value);
	var met;
    met= document.getElementById('method').value;
	//'brs' + numrow;
 	var maxrow = document.getElementById("manual").rows.length;
	var numrow = 0;
	var param = 'tgl=' + parax + '&method=' + met;
	var nn = 1;
	param = param +'&CPOPrice=' + CPOPrice;
	param = param + '&CPOsoldPrice=' + CPOsoldPrice;
	param = param + '&costCPO=' + costCPO;
	param = param + '&realCPO=' + realCPO;
	param = param + '&transCPO=' + transCPO;
	param = param + '&oerCPO=' + oerCPO;
	param = param + '&PKPrice=' + PKPrice;
	param = param + '&PKsoldPrice=' + PKsoldPrice;
	param = param + '&costPK=' + costPK;
	param = param + '&realPK=' + realPK;
	param = param + '&transPK=' + transPK;
	param = param + '&oerPK=' + oerPK;
	param = param + '&CkPrice=' + CkPrice;
	param = param + '&CksoldPrice=' + CksoldPrice;
	param = param + '&costCk=' + costCk;
	param = param + '&realCk=' + realCk;
	param = param + '&transCk=' + transCk;
	param = param + '&oerCk=' + oerCk;
	param = param + '&jmlhbaris=' + maxrow;
    maxrow = maxrow+10;
 	for(numrow = 2; numrow <= maxrow; numrow++){	
           // alert('row'+numrow+' max='+maxrow);
		if(document.getElementById('rendemen'+numrow)!= null){
			param = param + '&brs['+nn+']=' + document.getElementById('rendemen'+numrow).value;
			nn = nn + 1;
            //alert(param);
		}
	}
	var r = confirm('Simpan data Rendemen untuk tanggal ' + parax);
    if (r == true) {                    
		var tujuan = 'pmn_slave_tbsrendemenAll.php';						
        //alert(param);
        post_response_text(tujuan, param, respog);
    }else{
		document.getElementById('labarugi').focus();
	}
		
        function respog() {
            if (con.readyState == 4) {
                if (con.status == 200) {
                    busy_off();
                        if (!isSaveResponse(con.responseText)) {
                           alert('ERROR TRANSACTION,\n' + con.responseText);
                        } else {
                             //alert(con.responseText);
                            document.getElementById('container').innerHTML = con.responseText;
                        }
                } else {
                    busy_off();
                    error_catch(con.status);
                }
            }
        }
}
function showtableAll(){
	var ev = 'event';
	//param='tglAwal=' + document.getElementById("varstart").value;
	//param= param + '&tglAkhir=' + document.getElementById("varend").value;
	//alert('param');
    tujuan = 'pmn_slave_rendemenAll_data.php?';	
	title="Data Rerendemen";
	width='900';
	height='600';
	content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>";
	showDialog1(title,content,width,height,ev);
}
function previewBastAll(notransaksi,ev){	
	if(notransaksi == "Err"){
		alert('Silakan pilih Tanggal dan Organisasi terlebih dahulu');
	}else{
		//var r = confirm('Pastikan anda telah menyimpan data ');
        //if (r == true) {
			param='notransaksi='+notransaksi;
			tujuan = 'pmn_slave_print_tbsrendemenAll.php?'+param;	
			//alert('running');
			title=notransaksi;
			width='700';
			height='400';
			content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>";
			showDialog1(title,content,width,height,ev);
		//}
	}
}
function previewExcelAll(notransaksi,ev){	
	param='notransaksi='+notransaksi;
	tujuan = 'pmn_slave_excel_tbsrendemenAll.php?'+param;	
	//alert('running');
	title=notransaksi;
	width='200';
	height='100';
	content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>";
	showDialog1(title,content,width,height,ev);
}
/* 	cell1.innerHTML = "new";
	cell2.innerHTML ="new";
	cell3.innerHTML = "new";
	cell4.innerHTML = "new";
	cell5.innerHTML = "new";
	cell6.innerHTML = "new";
	cell7.innerHTML = "new"; */