/**
 * @author antoniuslouis
 */

function lihatpdf(ev,tujuan)
{   
    jabatan =document.getElementById('jabatan').options[document.getElementById('jabatan').selectedIndex].value;
    judul='Report PDF';	
    param='jabatan='+jabatan+'&kamar=pdf';
    printFile(param,tujuan,judul,ev)	        
}

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='600';
   height='250';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>";
   showDialog1(title,content,width,height,ev); 	
}