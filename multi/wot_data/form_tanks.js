function Preview()
{
  var userid = document.getElementById('user').value;
  if (userid != "")
  {
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
    var size = $("input[name='image_size']:checked").val();
    var flag = document.getElementById('cflag').value;
    
    var settings = $("input[name='tanksize']:checked").val();
    settings += $("input[name='tankorder']:checked").val();
    if ($("input[name='tankname']").is(":checked")) settings += "1"; else settings += "0";
    if ($("input[name='tankwins']").is(":checked")) settings += "1"; else settings += "0";
    if ($("input[name='tankbattles']").is(":checked")) settings += "1"; else settings += "0";
    if ($("input[name='tankpercents']").is(":checked")) settings += "1"; else settings += "0"; 
    if ($("input[name='position_small']").is(":checked")) settings += "1"; else settings += "0"; 
    
    setCookie('player_id', userid, 9999999999);
    setCookie('server', server, 9999999999);
    setCookie('flag', flag,9999999999);
    setCookie('image_size', size,9999999999);
    setCookie('garage_settings', settings,9999999999); 
    
    var img = new Image();
    img.onload = function() {
      document.getElementById('preview').src = this.src;
    }
     if (MOD_REWRITE)
    {
        var sep = GARAGE_MOD_REWRITE_SEPARATOR;
        img.src = GARAGE_MOD_REWRITE_LINK+sep+userid+sep+server+sep+size+sep+settings+sep+flag+".png";
    } else {
        img.src = 'wot_garage.php?id='+userid+'&server='+server+'&size='+size+'&settings='+settings+'&flag='+flag;
    }
    document.getElementById('preview').src = FOLDER_BACKGROUNDS + '/loading'+size+'.png';
  }
  
  return false;
}