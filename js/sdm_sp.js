/**
 * @author antoniuslouis
 * @editor awan
 */

 function cekSP() {
        tanggalberlaku = document.getElementById('tanggalberlaku').value;
        karyawanid = document.getElementById('karyawanid').value;
        if (karyawanid == '' || tanggalberlaku == '') {
                alert('Tanggal dan Karyawan perlu diisi.');
        }
        else {
                param = 'karyawanid=' + karyawanid;
                param += '&tanggalberlaku=' + tanggalberlaku + '&proses=cekSP';
                tujuan = 'sdm_slave_get_save_data.php';
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
                                        data = JSON.parse(con.responseText);
                                        if (data != null) {
                                                alert("Karyawan masih memiliki surat peringatan yang berlaku");
                                        }
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }	
 }

function parseDong(tex)
{
        xml=tex.toString();
        xmlobject = (new DOMParser()).parseFromString(xml, "text/xml");	

        jenissp	=xmlobject.getElementsByTagName('jenissp')[0].firstChild.nodeValue;
        jenissp=jenissp.replace("*","");
        karyawanid=xmlobject.getElementsByTagName('karyawanid')[0].firstChild.nodeValue;
        karyawanid=karyawanid.replace("*","");
        tanggal	=xmlobject.getElementsByTagName('tanggal')[0].firstChild.nodeValue;
        tanggal=tanggal.replace("*","");
        tanggalberlaku          = xmlobject.getElementsByTagName('tanggalberlaku')[0].firstChild.nodeValue;
        tanggalberlaku          = tanggalberlaku.replace("*","");
        masaberlaku=xmlobject.getElementsByTagName('masaberlaku')[0].firstChild.nodeValue;
        masaberlaku=masaberlaku.replace("*","");
        paragraf1=xmlobject.getElementsByTagName('paragraf1')[0].firstChild.nodeValue;
        paragraf1=paragraf1.replace("*","");
        pelanggaran=xmlobject.getElementsByTagName('pelanggaran')[0].firstChild.nodeValue;
        pelanggaran=pelanggaran.replace("*","");
        paragraf3=xmlobject.getElementsByTagName('paragraf3')[0].firstChild.nodeValue;
        paragraf3=paragraf3.replace("*","");
        paragraf4=xmlobject.getElementsByTagName('paragraf4')[0].firstChild.nodeValue;
        paragraf4=paragraf4.replace("*","");
        penandatangan=xmlobject.getElementsByTagName('penandatangan')[0].firstChild.nodeValue;
        penandatangan=penandatangan.replace("*","");
        jabatan=xmlobject.getElementsByTagName('jabatan')[0].firstChild.nodeValue;
        jabatan=jabatan.replace("*","");
        tembusan1=xmlobject.getElementsByTagName('tembusan1')[0].firstChild.nodeValue;
        tembusan1=tembusan1.replace("*","");
        tembusan2=xmlobject.getElementsByTagName('tembusan2')[0].firstChild.nodeValue;
        tembusan2=tembusan2.replace("*","");		
        tembusan3=xmlobject.getElementsByTagName('tembusan3')[0].firstChild.nodeValue;
        tembusan3=tembusan3.replace("*","");		
        tembusan4=xmlobject.getElementsByTagName('tembusan4')[0].firstChild.nodeValue;
        tembusan4=tembusan4.replace("*","");
        nosp=xmlobject.getElementsByTagName('nomor')[0].firstChild.nodeValue;
        nosp=nosp.replace("*","");		

        verifikasi=xmlobject.getElementsByTagName('verifikasi')[0].firstChild.nodeValue;
        verifikasi=verifikasi.replace("*","");
        dibuat=xmlobject.getElementsByTagName('dibuat')[0].firstChild.nodeValue;
        dibuat=dibuat.replace("*","");
		
        jabatanverifikasi=xmlobject.getElementsByTagName('jabatanverifikasi')[0].firstChild.nodeValue;
        jabatanverifikasi=jabatanverifikasi.replace("*","");
		
        jabatandibuat=xmlobject.getElementsByTagName('jabatandibuat')[0].firstChild.nodeValue;
        jabatandibuat=jabatandibuat.replace("*","");        

        jk=document.getElementById('jenissp');
                for(x=0;x<jk.length;x++)
                {
                        if(jk.options[x].value==jenissp)
                        {
                                jk.options[x].selected=true;
                        }
                }

        jk=document.getElementById('karyawanid');
                for(x=0;x<jk.length;x++)
                {
                        if(jk.options[x].value==karyawanid)
                        {
                                jk.options[x].selected=true;
                        }
                }	
        jk=document.getElementById('masaberlaku');
                for(x=0;x<jk.length;x++)
                {
                        if(jk.options[x].value==masaberlaku)
                        {
                                jk.options[x].selected=true;
                        }
                }
        document.getElementById('tanggalsp').value=tanggal;
        document.getElementById('tanggalberlaku').value=tanggalberlaku;
        document.getElementById('paragraf1').value=paragraf1;
        document.getElementById('pelanggaran').value=pelanggaran;
        document.getElementById('paragraf3').value=paragraf3;
        document.getElementById('paragraf4').value=paragraf4;
        document.getElementById('penandatangan').value=penandatangan;
        document.getElementById('jabatan').value=jabatan;
        document.getElementById('tembusan1').value=tembusan1;
        document.getElementById('tembusan2').value=tembusan2;
        document.getElementById('tembusan3').value=tembusan3;
        document.getElementById('tembusan4').value=tembusan4;
        document.getElementById('nosp').value=nosp;
        
        document.getElementById('verifikasi').value=verifikasi;
        document.getElementById('dibuat').value=dibuat;
        document.getElementById('jabatan1').value=jabatanverifikasi;
        document.getElementById('jabatan2').value=jabatandibuat;  
        
        document.getElementById('method').value='update';	
        tabAction(document.getElementById('tabFRM0'),0,'FRM',1);										
}






function saveSP()
{
        jenissp		= 	document.getElementById('jenissp');
        jenissp 	=	jenissp.options[jenissp.selectedIndex].value;
        karyawanid	= 	document.getElementById('karyawanid');
        karyawanid 	=	karyawanid.options[karyawanid.selectedIndex].value;
        masaberlaku	= 	document.getElementById('masaberlaku');
        masaberlaku     =	masaberlaku.options[masaberlaku.selectedIndex].value;
        nosp		=       document.getElementById('nosp').value;
        tanggalsp	=       document.getElementById('tanggalsp').value;
        tanggalberlaku	=       document.getElementById('tanggalberlaku').value;
        paragraf1	=       document.getElementById('paragraf1').value;
        pelanggaran	=       document.getElementById('pelanggaran').value;
        paragraf3	=       document.getElementById('paragraf3').value;
        paragraf4	=       document.getElementById('paragraf4').value;
        
        penandatangan	=       trim(document.getElementById('penandatangan').value);
        jabatan		=       document.getElementById('jabatan').value;
        verifikasi	=       trim(document.getElementById('verifikasi').value);
        jabatan1	=       document.getElementById('jabatan1').value;
        dibuat	        =       trim(document.getElementById('dibuat').value);
        jabatan2	=       document.getElementById('jabatan2').value;
        
        tembusan1	=       document.getElementById('tembusan1').value;
        tembusan2	=       document.getElementById('tembusan2').value;
        tembusan3	=       document.getElementById('tembusan3').value;
        tembusan4	=       document.getElementById('tembusan4').value;
        method		=       document.getElementById('method').value;


        if (jenissp == '' || karyawanid == '' || masaberlaku == '' || tanggalsp == '' || tanggalberlaku == '' || pelanggaran == '' || penandatangan == '') {
                alert('Transaction type, Employee, Doc.Date, Effective Date and Signer are obligatory');
        }
        else {
                param = 'jenissp=' + jenissp + '&karyawanid=' + karyawanid;
                param += '&masaberlaku=' + masaberlaku+'&nosp='+nosp;
                param += '&tanggalsp=' + tanggalsp + '&tanggalberlaku=' + tanggalberlaku + '&paragraf1=' + paragraf1;
                param += '&paragraf3=' + paragraf3 + '&paragraf4=' + paragraf4;
                param += '&pelanggaran=' + pelanggaran + '&penandatangan=' + penandatangan;
                param += '&jabatan=' + jabatan + '&tembusan1=' + tembusan1;
                param += '&tembusan2=' + tembusan2 + '&tembusan3=' + tembusan3;
                param += '&tembusan4=' + tembusan4 + '&method=' + method;
                param += '&verifikasi=' + verifikasi + '&dibuat=' + dibuat;
                param += '&jabatan1=' + jabatan1 + '&jabatan2=' + jabatan2;
				
				
				//alert(param);
				
                if (confirm('Saving, are you sure..?')) {
                        tujuan = 'sdm_slave_saveSP.php';
                        post_response_text(tujuan, param, respog);
                }
        }

        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        alert(con.responseText);
                                        clearForm();
                                        loadList();
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }	

}

function clearForm()
{
        jenissp	= document.getElementById('jenissp');
        jenissp.options[0].selected=true;
        karyawanid		= document.getElementById('karyawanid');
        karyawanid.options[0].selected=true;
        masaberlaku	= document.getElementById('masaberlaku');
        masaberlaku.options[0].selected=true;
        document.getElementById('tanggalsp').value='';
        document.getElementById('tanggalberlaku').value='';
        document.getElementById('paragraf1').value='';
        document.getElementById('pelanggaran').value='';
        document.getElementById('paragraf3').value='';
        document.getElementById('paragraf4').value='';
        document.getElementById('penandatangan').value='';
        document.getElementById('jabatan').value='';
        document.getElementById('tembusan1').value='';
        document.getElementById('tembusan2').value='';
        document.getElementById('tembusan3').value='';
        document.getElementById('tembusan4').value='';
        document.getElementById('method').value='insert';		
};

function loadList()
{      num=0;
                param='&page='+num;
                tujuan = 'sdm_slave_getSPList.php';
                post_response_text(tujuan, param, respog);

        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        document.getElementById('containerlist').innerHTML=con.responseText;
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }				
}

function cariSP(num)
{
        tex=trim(document.getElementById('txtbabp').value);
                param='&page='+num;
                if(tex!='')
                        param+='&tex='+tex;
                tujuan = 'sdm_slave_getSPList.php';

                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('containerlist').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}

function delSP(nosp,karid)
{
        param='nosp='+nosp+'&method=delete&karyawanid='+karid;
                tujuan='sdm_slave_saveSP.php';
                if(confirm('Deleting Document '+nosp+', are you sure..?'))
                  post_response_text(tujuan, param, respog);	
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                            loadList();
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}

function previewSP(nosp,ev)
{
        param='nosp='+nosp;
        tujuan = 'sdm_slave_printSP_pdf.php?'+param;	
 //display window
   title=nosp;
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);

}

function editSP(nosp,nokaryawan)
{

                param='karyawanid='+nokaryawan+'&nosp='+nosp;
                tujuan = 'sdm_slave_getSPForEdit.php';
                post_response_text(tujuan, param, respog);		


        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        parseDong(con.responseText);
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }		
}

function  filterK(){
    lokasitugas=document.getElementById('lokasitugas');
    lokasitugas=lokasitugas.options[lokasitugas.selectedIndex].value;
    tipekaryawan=document.getElementById('tipekaryawan');
    tipekaryawan=tipekaryawan.options[tipekaryawan.selectedIndex].value;
    
                param='lokasitugas='+lokasitugas+'&tipekaryawan='+tipekaryawan;
                tujuan = 'sdm_slave_getSPList.php';
                post_response_text(tujuan, param, respog);
                
                
         function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                      document.getElementById('karyawanid').innerHTML=con.responseText;
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }   
    
}