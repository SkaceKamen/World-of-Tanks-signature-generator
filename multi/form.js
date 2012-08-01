var img = new Image();
img.src = 'sig/wotloading0.png';
img = new Image();
img.src = 'sig/wotloading1.png';


var divA = document.getElementById("adv1");
var divB = document.getElementById("advb");
var posUpdated = false;
divA.style.height = "0px";
divA.style.overflow = "hidden";
divB.onclick = function() { show("adv1","adv2",this); };
divB.style.backgroundImage = "url('plus.png')";

function show(id1,id2,from)
{
  var div1 = document.getElementById(id1);
  var div2 = document.getElementById(id2);
  
  if (div1.style.height == "0px")
  {
    subShow(id1,0,div2.clientHeight);
    from.style.backgroundImage = "url('minus.png')";
  } else if (div1.style.height == div2.clientHeight+"px") {
    subHide(id1,div2.clientHeight);
    from.style.backgroundImage = "url('plus.png')";
  }
}
function subShow(id,height,maxheight)
{
  var div = document.getElementById(id);
  if (height < maxheight - 1)
  {
    height += (maxheight - height) / 10;
    div.style.height = height+"px";
    setTimeout("subShow('"+id+"',"+height+","+maxheight+")",10);
  } else {
    div.style.height = maxheight + "px";
  }
}
function subHide(id,height)
{
  var div = document.getElementById(id);
  if (height > 1)
  {
    height -= height / 10;
    div.style.height = height+"px";
    setTimeout("subHide('"+id+"',"+height+")",10);
  } else {
    div.style.height = "0px";
  }
}

function Preview()
{
  var userid = document.getElementById('user').value;
  
  if (userid != "")
  {
    if (!posUpdated)
    {
      updatePreviewPos();
      posUpdated = true;
    }
  
    var server = document.getElementById('server').value;
    var size0 = document.getElementById('size0').checked;
    var size1 = document.getElementById('size1').checked;
    var tankMini = document.getElementById('tankMini').checked;
    var tankSmall = document.getElementById('tankSmall').checked;
    var tankBig = document.getElementById('tankBig').checked;
    var tankNone = document.getElementById('tankNone').checked;
    var tankOrder = document.getElementById('tankOrder').checked;
    var tankName = document.getElementById('tankname').checked; 
    var tankNumbers = document.getElementById('tanknumbers').checked;
    var positionSmall = document.getElementById('positionSmall').checked;  
    var flag = document.getElementById('cflag').value;
    var font = document.getElementById('font').value;
    var back = 0;
    var advanced = "";
    var advanced_ext = "";
    var settings = "";
    if (document.getElementById("check_2_0").checked) advanced = "1"; else advanced = "0";
    for(i = 0; i < 13; i+=1)
    {
      if (document.getElementById("check_value_"+i).checked)
        advanced += "1";
      else
        advanced += "0";
    }
    for(i = 0; i < 13; i+=1)
    {
      if (document.getElementById("check_position_"+i).checked)
        advanced_ext += "1";
      else
        advanced_ext += "0";
    }
    for(i = 1; i <= 104; i+=1)
      if (document.getElementById('img'+i) != null)
        if (document.getElementById('img'+i).checked)
        {
          back = i;
          break;
        }
    
    if (tankBig) settings = "1";
     else if (tankMini) settings = "3";
     else if (tankNone) settings = "2";
     else settings = "0";
    if (size0) size = 0; else size = 1;
    if (tankOrder) settings+="1"; else settings+="0";
    if (positionSmall) settings+="1"; else settings+="0";
    if (tankName) settings+="1"; else settings+="0";
    if (tankNumbers) settings+="1"; else settings+="0";
    
    var img = new Image();
    img.onload = function() {
      document.getElementById('preview').src = this.src;
    }
    img.src = "sig2@"+userid+"@"+server+"@"+back+"@"+size+"@"+advanced+"@"+advanced_ext+"@"+settings+"@"+flag+"@"+font+".png";
    document.getElementById('preview').src = 'sig/wotloading'+size+'.png';
  }
  return false;
}

function sig_category(catid)
{
  if (!xhr1.loading)
  {
    document.getElementById('siglist').innerHTML = '<center><img src="Throbber.gif" alt="Loading"></center>';
    Nacist(xhr1,"siglist","getSig.php?catid="+catid,false,0,null);
    for(var i = 0; i < 6; i+=1)
      document.getElementById('cat'+i).style.background = "#1BA0E1";
    document.getElementById('cat'+catid).style.background = "#000";
  }
}

function Generate()
{
  /*nacteno = function() {
    setTimeout("document.forms[0].submit();",500);
  }
  Nacist(xhr2,"siglist","getSig.php?catid=0",false,0,nacteno); 
  document.forms[0].submit();
  return false;*/
  return true;
}

function sig_selected(sigid)
{
  document.getElementById("img"+sigid).checked="true";
  document.getElementById("imgID").value = sigid;
  Preview();
  setCookie("LastImg",sigid,356);
}

function setCookie(c_name, value, exdays)
{
  var exdate = new Date();
  exdate.setDate(exdate.getDate() + exdays);
  var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
  document.cookie=c_name + "=" + c_value;
}

function f_pageHeight()
{
  var viewportHeight;
  if (window.innerHeight && window.scrollMaxY) {
    viewportHeight = window.innerHeight + window.scrollMaxY;
  } else if (document.body.scrollHeight > document.body.offsetHeight) {
    // all but explorer mac
    viewportHeight = document.body.scrollHeight;
  } else {
    // explorer mac...would also work in explorer 6 strict, mozilla and safari
    viewportHeight = document.body.offsetHeight;
  };
  
  return viewportHeight;
}

function f_clientHeight() {
	return f_filterResults (
		window.innerHeight ? window.innerHeight : 0,
		document.documentElement ? document.documentElement.clientHeight : 0,
		document.body ? document.body.clientHeight : 0
	);
}

function f_scrollTop() {
	return f_filterResults (
		window.pageYOffset ? window.pageYOffset : 0,
		document.documentElement ? document.documentElement.scrollTop : 0,
		document.body ? document.body.scrollTop : 0
	);
}
function f_filterResults(n_win, n_docel, n_body) {
	var n_result = n_win ? n_win : 0;
	if (n_docel && (!n_result || (n_result > n_docel)))
		n_result = n_docel;
	return n_body && (!n_result || (n_result > n_body)) ? n_body : n_result;
}

function updatePreviewPos()
{
  var p = document.getElementById('preview');
  if (f_pageHeight() - f_scrollTop() < f_clientHeight() + 128)
  {
    p.style.position = 'relative';
    p.style.bottom = "0px";
  } else {
    p.style.position = 'fixed';
    p.style.bottom = "5px";
  }

  setTimeout("updatePreviewPos()",200);
}