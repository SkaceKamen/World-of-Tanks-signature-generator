function vytvorXHR()
{
    var xhr;
    try{
      xhr = new XMLHttpRequest();
    }catch(e){
      var MSXmlVerze = new Array('MSXML2.XMLHttp.6.0','MSXML2.XMLHttp.5.0','MSXML2.XMLHttp.4.0','MSXML2.XMLHttp.3.0','MSXML2.XMLHttp.2.0','Microsoft.XMLHttp');
      for(var i = 0; i <= MSXmlVerze.length; i ++){
        try{
          xhr = new ActiveXObject(MSXmlVerze[i]);
          break;
        }catch(e){
        }
      }
    }
    if(!xhr)
      alert("Došlo k chybě při vytváření objektu XMLHttpRequest!");
    else
      return xhr;
}
var xhr1 = vytvorXHR();
xhr1.loading = false;
xhr1.varName = "xhr1";
var xhr2 = vytvorXHR();
xhr2.loading = false;
xhr2.varName = "xhr2";

function Nacist(xhr,element,url,repeat,repeat_time,loadfnc)
{
  xhr.open("GET",url,true);
  xhr.element = element;
  xhr.LoadUrl = url;
  xhr.loading = true;
  if (!loadfnc)
    loadfnc = function () {}
  xhr.loaded = loadfnc;
  if (!repeat_time)
    repeat_time = 100;
  xhr.repeat = repeat;
  xhr.repeatTime = repeat_time;
  xhr.onreadystatechange = function()
  {
    if(this.readyState == 4)
    {
      if(this.status == 200)
      {
        if (this.element!="")
          if (this.responseText!=document.getElementById(this.element).innerHTML)
            document.getElementById(this.element).innerHTML = this.responseText;
        this.loaded();
        if (this.repeat)
          setTimeout("Nacist("+this.varName+",'"+this.element+"','"+this.LoadUrl+"',"+this.repeat+","+this.repeatTime+")",this.repeatTime);
      }
      this.loading = false;
    }
  };
  xhr.send();
}