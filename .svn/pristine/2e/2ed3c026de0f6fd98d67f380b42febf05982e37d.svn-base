<!--
/*
function pageBack(){
    history.back();
}
function updateform(id, upid, oldstr)
{
    document.getElementById(upid).innerHTML = 
	"<form><input type='text' value='" + oldstr + "' id='input2' onBlur=''></form>test<hr>";
    document.getElementById('input2' ).select();
    document.getElementById('input2' ).focus();
}
*/
function textareazoom(){
	var f=document.forms,i,j;
	for(i=0;i<f.length;i++){
		e=f[i].elements;
		for(j=0;j<e.length;j++){
			if(e[j].type.match("textarea")){
				e[j].rows*=2;
				e[j].cols*=1.5;
			}
		}
	}
	document.f_edit.content.focus();
	return false;
}
function table_trance(){
	var d=document,q="table",i,j,k,y,r,c,t;
//	for(i=0;t=d.getElementsByTagName(q)[i];++i){
		t=d.getElementsByTagName(q)[0];
		var w=0,N=t.cloneNode(0);
		N.width="";
		N.height="";
		N.border=1;
		for(j=0;r=t.rows[j];++j){
			for(y=k=0;c=r.cells[k];++k){
				var z,a=c.rowSpan,b=c.colSpan,v=c.cloneNode(1);
				v.rowSpan=b;
				v.colSpan=a;
				v.width="";
				v.height="";
				if(!v.bgColor)v.bgColor=r.bgColor;
				while(w<y+b) N.insertRow(w++).p=0;
				while(N.rows[y].p>j) ++y;
				N.rows[y].appendChild(v);
				for(z=0;z<b;++z) N.rows[y+z].p+=a;
				y+=b;
			}
		}
		t.parentNode.replaceChild(N,t);
//	}
}
function add_kigou(kigou) {
	document.getElementById('content' ).value = document.getElementById('content' ).value + kigou;
	document.f_edit.submit();
	return false;
}
function add_time() {
	wk_date = new Date();
	h = wk_date.getHours();
	m = wk_date.getMinutes();
	if(h < 10) h = "0" + h;
	if(m < 10) m = "0" + m;
	document.getElementById('content' ).value = document.getElementById('content' ).value + (h + ":" + m);
	document.f_edit.content.focus();
	return false;
}
function add_date() {
	wk_date = new Date();
	y = wk_date.getFullYear();
	t = wk_date.getMonth() + 1;
	d = wk_date.getDate();
	if(t < 10) t = "0" + t;
	if(d < 10) d = "0" + d;
	document.getElementById('content' ).value = document.getElementById('content' ).value + (y + "/" + t + "/" + d);
	document.f_edit.content.focus();
	return false;
}
function delete_check(){
	conf_msg = "削除して宜しいですか？"
	return(confirm(conf_msg));
}
function ClickUrl()
{
    if (document.all) {
        var a = document.selection.createRange();
        var b = a.parentElement(); 
        var c = b.createTextRange(); 
        c = document.body.createTextRange(); 
        c.moveToElementText(b); 
        c.setEndPoint("EndToStart", a); 
        d = document.body.createTextRange();
        d.moveToElementText(b);
        d.setEndPoint("StartToEnd", a);
        r = new RegExp("https?://[a-zA-Z0-9\./_&%?=@,:;#-]*?$");
        s = new RegExp("^[a-zA-Z0-9\./_&%?=@,:;#-]*");
        var url = "";
        if ((a.text == "http" || a.text == "https") && (ss = d.text.match(s))) {
            url = a.text + ss;
        } else if ((rr = c.text.match(r)) && (ss = d.text.match(s))) {
            url = rr + a.text + ss;
        }

		if(url.length>0)
			window.open(url);
    }
}
//  TAB inside textarea (and textbox)?
//  http://dotnetjunkies.com/WebLog/familjones/archive/2004/04/01/10607.aspx
function HandleKeyDown(obj)
{
   var tabKeyCode = 9;
   if (event.keyCode == tabKeyCode && event.srcElement == obj) {
      obj.selection = document.selection.createRange();
      obj.selection.text = String.fromCharCode(tabKeyCode);
      event.returnValue = false;
  	  document.f_edit.content.focus();
	  return false;
   }
}

function changeURL() {
	var doc = document.getElementsByTagName("td");
	var sHTML;
	var regURL = new RegExp("(s?https?://[-_.!〜*'()a-zA-Z0-9;/?:@&=+$,%#]+)","g");
	for(var i=0 ; i<doc.length ; i++){
		sHTML = doc[i].innerHTML;
		doc[i].innerHTML = sHTML.replace(regURL,'<a href="$1" target="_blank">$1</a>');
	}
}
