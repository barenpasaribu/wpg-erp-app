/*@uth:nangkoel@gmail.com
 * 
 */
//gform=document.getElementById('GRAPHFORM');
//ginfo=document.getElementById('GRAPHINFO');
//gresult=document.getElementById('GRAPHRESULT');
 
var isIE = document.all ? true : false;

function showviewbox(evt)
{
  g_svgdoc = evt.target.ownerDocument;
  g_root = g_svgdoc.documentElement;
  innersvg = g_svgdoc.getElementById("map");
  //get original viewBox values
  //var vb = g_root.getAttribute("viewBox");
  var vb = innersvg.getAttribute("viewBox");
  if(vb) {

    var vba = vb.split(" "); //comes out with four string array
    curorig_x = orig_x = Number(vba[0]);
    curorig_y = orig_y = Number(vba[1]);
    curorig_width = orig_width = Number(vba[2]);
    curorig_height = orig_height = Number(vba[3]);
    }
    alert(curorig_x+" "+curorig_y+" "+curorig_width+" "+curorig_height);
}

function resetperiode()
{
    document.getElementById("periode").value='';
}

function isitanggal()
{
    periode=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
    var per = periode.split("-"); 
    d = new Date(per[0], per[1],0);
    var month = d.getMonth() + 1;
    var day = d.getDate();
    var year = d.getFullYear();
    tanggal1=day+'-'+(month<=9?'0'+month:month)+'-'+year;
    tanggal0='01'+'-'+per[1]+'-'+per[0];
    document.getElementById("tanggal0").value=tanggal0;
    document.getElementById("tanggal1").value=tanggal1;    
}

function zoommap(nilai)
{ 
    zoom = document.getElementById("zoom").value;
    origwidth = document.getElementById("origwidth").value;
    origheight = document.getElementById("origheight").value;
    innersvg = document.getElementById("map");
    var vb = innersvg.getAttribute("viewBox");
    if(vb) {
        var vba = vb.split(" "); 
        curorig_x = orig_x = Number(vba[0]);
        curorig_y = orig_y = Number(vba[1]);
        curorig_width = orig_width = Number(vba[2]);
        curorig_height = orig_height = Number(vba[3]);
    }
    
    beforewidth=curorig_width;
    beforeheight=curorig_height;
    
    zoom=zoom*nilai;
    curorig_width=origwidth*zoom;
    curorig_height=origheight*zoom;
    
    curorig_x=curorig_x-(curorig_width-beforewidth)/2;
    curorig_y=curorig_y-(curorig_height-beforeheight)/2;
 
    viewbox = curorig_x+' '+curorig_y+' '+curorig_width+' '+curorig_height;
    innersvg.setAttribute("viewBox",viewbox); 
    document.getElementById("zoom").value=zoom;
}

function movemap(sumbu,nilai)
{
    innersvg = document.getElementById("map");
    var vb = innersvg.getAttribute("viewBox");
    if(vb) {
        var vba = vb.split(" "); 
        curorig_x = orig_x = Number(vba[0]);
        curorig_y = orig_y = Number(vba[1]);
        curorig_width = orig_width = Number(vba[2]);
        curorig_height = orig_height = Number(vba[3]);
    }
    
    if(sumbu=='x'){
        nilai=curorig_height/nilai;
        curorig_x=curorig_x+nilai;
    }
    if(sumbu=='y'){
        nilai=curorig_height/nilai;
        curorig_y=curorig_y+nilai;
    }
    
    viewbox = curorig_x+' '+curorig_y+' '+curorig_width+' '+curorig_height;
    innersvg.setAttribute("viewBox",viewbox); 
}
  
function geserklik3(evt)
{
    posX=Number(document.getElementById('posx').value);
    posY=Number(document.getElementById('posy').value);
    awalx=Number(document.getElementById('posorigx').value);
    awaly=Number(document.getElementById('posorigy').value);
        
    g_svgdoc = evt.target.ownerDocument;
    g_root = g_svgdoc.documentElement;
    innersvg = g_svgdoc.getElementById("map");
    var vb = innersvg.getAttribute("viewBox");
    if(vb) {
        var vba = vb.split(" "); 
        curorig_x = orig_x = Number(vba[0]);
        curorig_y = orig_y = Number(vba[1]);
        curorig_width = orig_width = Number(vba[2]);
        curorig_height = orig_height = Number(vba[3]);
    }
    
    perbandinganx=570/curorig_width; // 1000 width window MAPHRESULT
    perbandingany=570/curorig_height; // 400 height window MAPHRESULT
    
    geserx=(posX-awalx)/perbandinganx;
    gesery=(posY-awaly)/perbandingany;    
    
    curorig_x=curorig_x-geserx;
    curorig_y=curorig_y-gesery;
    
    document.getElementById('posorigx').value=posX; // update posisi awal
    document.getElementById('posorigy').value=posY; // update posisi awal
    
    viewbox = curorig_x+' '+curorig_y+' '+curorig_width+' '+curorig_height;
    innersvg.setAttribute("viewBox",viewbox);
}  

function mulaiklik(evt)
{
    document.getElementById('drag').value=1;
    posisiawal=geserklik2(evt);
    if(posisiawal) {
        var vba = posisiawal.split(" "); 
        awalx = Number(vba[0]);
        awaly = Number(vba[1]);
    }
    document.getElementById('posorigx').value=awalx;
    document.getElementById('posorigy').value=awaly;   
}

function selesaiklik(evt)
{
    document.getElementById('drag').value=0;
}

function geserklik(evt)
{
    if(document.getElementById('drag').value==1)
    {
        geserklik2(evt); // dapatkan posisi mouse
        geserklik3(evt); // gambar pergeseran
    }
}

function geserklik2(ev)
{
    var _x;
    var _y;
    if (!isIE) {
            _x = ev.pageX;
            _y = ev.pageY;
    }
    if (isIE) {
            _x = ev.clientX + document.body.scrollLeft;
            _y = ev.clientY + document.body.scrollTop;
    }
    posX = _x;
    posY = _y;
    
    document.getElementById('posx').value=posX;
    document.getElementById('posy').value=posY;
    
    return(posX+' '+posY);
}

function showmap(viewbox)
{
    kebun=document.getElementById('kebun').options[document.getElementById('kebun').selectedIndex].value;
    
    gresult=document.getElementById('MAPHRESULT');
    param='id=show_map&viewbox='+viewbox+'&kebun='+kebun;
    tujuan='bi_slave_getForm_map.php';
    post_response_text(tujuan, param, respog);
    function respog()
    {
        if(con.readyState==4)
        {
            if (con.status == 200) 
            {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else 
                { 
                    gresult.innerHTML=con.responseText;
                    resetvalues();
                    resetoption();
                    document.getElementById("MAPHCONTROL").style.display='block';
                    document.getElementById('MAPHCONTROLINFO').innerHTML='';
                    document.getElementById("kontroldivisi").checked=false;
                    document.getElementById("kontroltahuntanam").checked=false;
                    document.getElementById('textblock').checked=true;
                }
            }
            else 
            {
                busy_off();
                error_catch(con.status);
            }
        }	
    }  
}

function resetvalues()
{
    innersvg = document.getElementById("map");
    var vb = innersvg.getAttribute("viewBox");
    if(vb) {
        var vba = vb.split(" "); 
        curorig_x = orig_x = Number(vba[0]);
        curorig_y = orig_y = Number(vba[1]);
        curorig_width = orig_width = Number(vba[2]);
        curorig_height = orig_height = Number(vba[3]);
    }
    document.getElementById("origwidth").value=curorig_width;
    document.getElementById("origheight").value=curorig_height;  
    document.getElementById("zoom").value=1;    
}

function resetoption()
{
    document.getElementById('option').value='';
    document.getElementById('tanggal0').value='';
    document.getElementById('tanggal1').value='';
    document.getElementById('suboption').innerHTML='<option value=></option>';
    document.getElementById('periode').value='';
    
}

function resetkebun()
{
    resetoption();
//    resetvalues();
    document.getElementById('MAPHRESULT').innerHTML='';
                    document.getElementById("MAPHCONTROL").style.display='hidden';
                    document.getElementById('MAPHCONTROLINFO').innerHTML='';
                    document.getElementById("kontroldivisi").checked=false;
                    document.getElementById("kontroltahuntanam").checked=false;
                    document.getElementById('textblock').checked=true;
                    document.getElementById('textcity').checked=true;
}

function loadGraphForm_map()
{
    glist=document.getElementById('MAPOPTLIST');
    gform=document.getElementById('MAPFORM');
    document.getElementById('MAPHRESULT').innerHTML='';
    dest=glist.options[glist.selectedIndex].value;
    if(dest=='')
    {
        
    }
    else
    {     
        param='id='+dest;
        tujuan='bi_slave_getForm_map.php';
        post_response_text(tujuan, param, respog);
    }
    function respog()
    {
        if(con.readyState==4)
        {
            if (con.status == 200) 
            {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else 
                {
                    gform.innerHTML=con.responseText;
                }
            }
            else 
            {
                busy_off();
                error_catch(con.status);
            }
        }	
    }  
}

function gantul2(tulisan,target) 
{ 
//    document.getElementById('CLICKINFO').innerHTML=tulisan;
}

function gantul(kodeorg) 
{
//    document.getElementById(kodeorg).style.fill='red';

////    document.getElementById('legend').innerHTML=kodeorg;
//    param='kodeorg='+kodeorg+'&id=legend';
//    tujuan='bi_slave_getForm_map.php';
//    post_response_text(tujuan, param, respog);
//    function respog()
//    {
//        if(con.readyState==4) 
//        {
//            if (con.status == 200) {
//                busy_off();
//                if (!isSaveResponse(con.responseText)) {
//                    alert('ERROR TRANSACTION,\n' + con.responseText);
//                }
//                else {
////                    document.getElementById('MAPHINFO').innerHTML=con.responseText;
//                }
//            }
//            else {
//                busy_off();
//                error_catch(con.status);
//            }
//        }	
//    }  
}

function cekoption()
{
//    kebun=document.getElementById('kebun').options[document.getElementById('kebun').selectedIndex].value;
    option=document.getElementById('option').options[document.getElementById('option').selectedIndex].value;
//    param='id=cekoption&option='+option+'&kebun='+kebun;
    param='id=cekoption&option='+option;
    document.getElementById('suboption').innerHTML='<option value=></option>';
    tujuan='bi_slave_getForm_map.php';
    if(kebun!='')
    post_response_text(tujuan, param, respog);
    function respog()
    {
        if(con.readyState==4)
        {
            if (con.status == 200) 
            {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else 
                {
                    if(con.responseText!='')document.getElementById('suboption').innerHTML=con.responseText;
                }
            }
            else 
            {
                busy_off();
                error_catch(con.status);
            }
        }	
    }      
}

function pilihoption() 
{
    tanggal0=document.getElementById('tanggal0').value;
    tanggal1=document.getElementById('tanggal1').value;
    kebun=document.getElementById('kebun').options[document.getElementById('kebun').selectedIndex].value;
    option=document.getElementById('option').options[document.getElementById('option').selectedIndex].value;
    suboption=document.getElementById('suboption').options[document.getElementById('suboption').selectedIndex].value;
    periode=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
    param='id=option&kebun='+kebun+'&option='+option+'&tanggal0='+tanggal0+'&tanggal1='+tanggal1+'&suboption='+suboption+'&periode='+periode;
    tujuan='bi_slave_getForm_map.php';
    ginfo=document.getElementById('MAPHCONTROLINFO');
                    document.getElementById("kontroldivisi").checked=false;
                    document.getElementById("kontroltahuntanam").checked=false;
     
    if((option=='produksi'||option=='biayapanen'||option=='biayatm'||option=='biayatbm')&&periode==''){
        alert('For Produksi/Biaya Panen option, fill Periode');            
    }else{
        if((option=='')||(tanggal0=='')){
            alert('Please fill Option, Tanggal');    
        }else{
            post_response_text(tujuan, param, respog);
        }
        
    }
    function respog()
    {
        if(con.readyState==4) 
        {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    // legend
                    hasilz = con.responseText.split("******"); 
                    hasil=hasilz[0];
                    ginfo.innerHTML=hasilz[1];
                    
                    // content
                    hasil=con.responseText;
                    var vba = hasil.split("****");
                    for(var i = 0, len = vba.length; i < len; ++i) {
                        try
                        {
                            var qwe = vba[i].split("**");
                                document.getElementById(qwe[0]).style.fill=qwe[1];
                                document.getElementById(qwe[0]).title='bisa diganti';
                                document.getElementById(qwe[0]).setAttribute('title', qwe[2]);
                                document.getElementById(qwe[0]).setAttribute('onclick', 'gantul2(\''+qwe[2]+'\')');
//                            document.getElementById(vba[i]).style.fill='red';
                        } 
                        catch(err)
                        {
                        //Handle errors here
                        }        
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

function pilihcek(cek)
{
    kebun=document.getElementById('kebun').options[document.getElementById('kebun').selectedIndex].value;
    if(cek=='textblock'){
        if(document.getElementById('textblock').checked==true)tampil="block";
        else tampil="none";
    }
    if(cek=='textcity'){
        if(document.getElementById('textcity').checked==true)tampil="block";
        else tampil="none";
    }
    if(cek=='pathroad'){
        if(document.getElementById('pathroad').checked==true)tampil="block";
        else tampil="none";
    }
    if(cek=='pathriver'){
        if(document.getElementById('pathriver').checked==true)tampil="block";
        else tampil="none";
    }
    param='id=show_cek&kebun='+kebun+'&cek='+cek;
    tujuan='bi_slave_getForm_map.php';
    post_response_text(tujuan, param, respog);
    function respog()
    {
        if(con.readyState==4)
        {
            if (con.status == 200) 
            {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else 
                { 
                    // content
                    hasil=con.responseText;
                    var vba = hasil.split("**");
                    for(var i = 0, len = vba.length; i < len; ++i) {
                        try
                        {
                            if(cek=='textblock'||cek=='textcity')
                            {
                                idx='t'+vba[i];
                                    if(idx!='t')document.getElementById(idx).style.display=tampil;
                            }
                            if(cek=='pathroad'||cek=='pathriver')
                            {
                                idx=vba[i];
                                    if(idx!='')document.getElementById(idx).style.display=tampil;
                            }
                        } 
                        catch(err)
                        {
                        //Handle errors here
                        }        
                    }   
                }
            }
            else 
            {
                busy_off();
                error_catch(con.status);
            }
        }	
    }  
}

function pilihkontrol(kontrol)
{
    kebun=document.getElementById('kebun').options[document.getElementById('kebun').selectedIndex].value;
    innersvg = document.getElementById("map");
    viewbox = innersvg.getAttribute("viewBox");
    
    gresult=document.getElementById('MAPHRESULT');
    ginfo=document.getElementById('MAPHCONTROLINFO');
    param='id=show_kontrol&viewbox='+viewbox+'&kebun='+kebun+'&kontrol='+kontrol;
    tujuan='bi_slave_getForm_map.php';
    post_response_text(tujuan, param, respog);
    function respog()
    {
        if(con.readyState==4)
        {
            if (con.status == 200) 
            {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else 
                {
//                    gresult.innerHTML=con.responseText;
                    hasil = con.responseText.split("####"); 
                    gresult.innerHTML=hasil[0];
                    ginfo.innerHTML=hasil[1];
                    resetoption();
                }
            }
            else 
            {
                busy_off();
                error_catch(con.status);
            }
        }	
    }  
}

//function loadGraphForm()
//{
//    glist            =document.getElementById('GRAPHOPTLIST');
//    gform        =document.getElementById('GRAPHFORM');
//    document.getElementById('GRAPHRESULT').innerHTML='';
//    dest=glist.options[glist.selectedIndex].value;
//    if(dest=='')
//     {}
//     else
//    {     
//        param='id='+dest;
//        tujuan='bi_slave_getForm.php';
//        post_response_text(tujuan, param, respog);
//    }
//    function respog()
//    {
//              if(con.readyState==4)
//              {
//                            if (con.status == 200) 
//                            {
//                                    busy_off();
//                                    if (!isSaveResponse(con.responseText)) {
//                                                    alert('ERROR TRANSACTION,\n' + con.responseText);
//                                    }
//                                    else 
//                                    {
//                                            gform.innerHTML=con.responseText;
//                                    }
//                            }
//                            else 
//                            {
//                                    busy_off();
//                                    error_catch(con.status);
//                            }
//              }	
//     }  
//}
//
//function get001(dest)
//{
//    tahun=document.getElementById('tahun');
//    tahun=tahun.options[tahun.selectedIndex].value;
//    pks=document.getElementById('pks'); 
//    pks=pks.options[pks.selectedIndex].value;
//    param=dest+'.php?tahun='+tahun+'&pks='+pks+'&jenis=global';
//    if(tahun=='')
//        alert('Tahun belum diisi');
//    else
//    getGraph(param);
//}
//
//function get002(dest)
//{
//    awal=document.getElementById('dari');
//    awal=awal.options[awal.selectedIndex].value;
//    sampai=document.getElementById('sampai');
//    sampai=sampai.options[sampai.selectedIndex].value;   
//    pks=document.getElementById('pks'); 
//    pks=pks.options[pks.selectedIndex].value; 
//    param=dest+'.php?awal='+awal+'&pks='+pks+'&sampai='+sampai+'&jenis=global';
//    if(awal =='' || sampai =='' || sampai<=awal)
//        alert('Periode salah');
//    else
//    getGraph(param);
//}
//
//function get003(dest)
//{
//    tahun=document.getElementById('tahun');
//    tahun=tahun.options[tahun.selectedIndex].value;
//    tahun1=document.getElementById('tahun1');
//    tahun1=tahun1.options[tahun1.selectedIndex].value;
//    pks=document.getElementById('pks'); 
//    pks=pks.options[pks.selectedIndex].value;
//    param=dest+'.php?tahun='+tahun+'&tahun1='+tahun1+'&pks='+pks+'&jenis=global';
//    if(tahun=='' || tahun>tahun1)
//        alert('Periode tahun salah');
//    else
//    getGraph(param);
//}
//
//function getGraph(dest)
//{
//   gresult       =document.getElementById('GRAPHRESULT');
//    gresult.innerHTML="<iframe width=1000px height=560px frameborder=no src="+param+"></iframe>";
//}