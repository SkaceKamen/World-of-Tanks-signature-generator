var img = new Image();
img.src = FOLDER_BACKGROUNDS + '/loading0.png';
img = new Image();
img.src = FOLDER_BACKGROUNDS + '/loading1.png';

var divA = document.getElementById("adv1");
var divB = document.getElementById("advb");
var posUpdated = false;
divA.style.height = "0px";
divA.style.overflow = "hidden";
divB.onclick = function() { show("adv1","adv2",this); };
divB.style.backgroundImage = "url('" + FOLDER_IMAGES + "/plus.png')";

function show(id1,id2,from)
{
    var div1 = document.getElementById(id1);
    var div2 = document.getElementById(id2);
    
    if (div1.style.height == "0px")
    {
      subShow(id1,0,div2.clientHeight);
      from.style.backgroundImage = "url('" + FOLDER_IMAGES + "/minus.png')";
    } else if (div1.style.height == div2.clientHeight+"px") {
      subHide(id1,div2.clientHeight);
      from.style.backgroundImage = "url('" + FOLDER_IMAGES + "/plus.png')";
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
    
    if (userid.indexOf('/') != -1)
    {
        var sp = userid.split('/');
        var end = sp.length - 1;
        while(sp[end] == '' && end > 0) {
            end--;
        }
        if (sp[end] != '')
        {
            userid = sp[end];
        }
        
    }
    if (userid.indexOf('-') != -1)
    {
        var sp = userid.split('-');
        userid = sp[0];
    }
  
    var server = document.getElementById('server').value;
    var tankWins = $('tankwins')
    var tankBattles = $('tankbattles');
    var flag = document.getElementById('cflag').value;
    var font = document.getElementById('font').value;
    var back = 0;
    var lines = "";
    var settings = "";
    
    for(var i = 0; i < KEYS_ALL.length; i++)
    {
        for(var o = 0; o < 3; o++)
        {
            if ($("input[name='" + KEYS_ALL[i] + o.toString() + "']").is(":checked"))
                lines += "1";
            else
                lines += "0";
        }
    }
    
    back = $("input[name='background']:checked").val();
    
    size = $("input[name='image_size']:checked").val();
    settings = $("input[name='tanksize']:checked").val();
    settings += $("input[name='tankorder']:checked").val();
    if ($("input[name='tankname']").is(":checked")) settings += "1"; else settings += "0";
    if ($("input[name='tankwins']").is(":checked")) settings += "1"; else settings += "0";
    if ($("input[name='tankbattles']").is(":checked")) settings += "1"; else settings += "0";
    if ($("input[name='tankpercents']").is(":checked")) settings += "1"; else settings += "0"; 
    if ($("input[name='position_small']").is(":checked")) settings += "1"; else settings += "0";
    if ($("input[name='server_label']").is(":checked")) settings += "1"; else settings += "0"; 
    settings += $("input[name='effeciency_rating']:checked").val();
    if ($("input[name='clan_name']").is(":checked")) settings += "1"; else settings += "0"; 
    if ($("input[name='clan_image']").is(":checked")) settings += "1"; else settings += "0"; 

    //Someone leave without posting form, better save his data!
    setCookie('player_id', userid, 9999999999);
    setCookie('server', server, 9999999999);
    setCookie('flag', flag,9999999999);
    setCookie('background', back,9999999999);
    setCookie('image_size', size,9999999999);
    setCookie('font_size', font,9999999999);
    setCookie('settings', settings,9999999999); 
    setCookie('lines', lines,9999999999);  
     
    var img = new Image();
    img.onload = function() {
      document.getElementById('preview').src = this.src;
    }
    if (MOD_REWRITE)
    {
        var sep = MOD_REWRITE_SEPARATOR;
        img.src = MOD_REWRITE_LINK+sep+userid+sep+server+sep+back+sep+size+sep+lines+sep+settings+sep+flag+sep+font+".png";
    } else {
        img.src = 'wot_signature.php?id='+userid+'&server='+server+'&img='+back+'&size='+size+'&lines='+lines+'&settings='+settings+'&flag='+flag+'&font='+font;
    }
    document.getElementById('preview').src = FOLDER_BACKGROUNDS + '/loading'+size+'.png';
  }
  return false;
}

function sig_category(category)
{
    var selected = category;
    if (category === undefined)
        selected = 'all';
    $(".sig_buttons div").each(function() {
        if ($(this).attr('id') == 'cat_' + selected)
        {
            $(this).addClass('selected');
        } else {
            $(this).removeClass('selected');
        }  
    });
    
    var back = getCookie('background');
    var html = '';
    var total = BACKGROUNDS_TOTAL;
    var list = false;
    if (category !== undefined)
    {
        total = CATEGORIES[category].length;
        list = CATEGORIES[category];
    } else {
		list = [];
		for(var i in CATEGORIES)
			for(var x = 0; x < CATEGORIES[i].length; x++)
				list.push(CATEGORIES[i][x]);
		total = list.length;
	}
    for(var i = 0; i < total; i++)
    {
        var num = i;
        if (list != false)
            num = list[i];
        html += "" + 
          "<div class='singature_float'>" +
            "<img src='" + FOLDER_BACKGROUNDS + "/wot" + num + ".png' alt='' onclick='sig_selected(" + num + ")'>" +
            "<div class='check'>" + 
              "<input type='radio' id='background" + num + "' name='background' value='" + num + "'" + ((back == num)?'checked="checked"':'') + ">" +
            "</div>" +
          "</div>";
    }
    html += "<div class='clr'></div>";
    $("#siglist").html(html);
}

function Generate()
{
  return true;
}

function sig_selected(sigid)
{
  document.getElementById("background"+sigid).checked="true";
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