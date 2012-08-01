<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <meta name="generator" content="PSPad editor, www.pspad.com">
  <title>World of tanks Signature Creator</title>
  <style>
  * { margin: 0px; padding: 0px; }
  BODY { font-family: Arial; }
  select { border: 1px solid #EEE; padding: 3px; width: 64px; }
  td { padding: 5px; background: #fff; }
  label { display: block; text-align: right; color: #555; }
  
  .warning { font-size: 12pt; font-weight: bold; padding: 5px; }
  
  .page  { width: 800px; margin: 20px auto; }
  .head { text-align: center; margin-bottom: 20px; }
  .values td { padding: 1px; }
  .small { font-size: 10px; display: block; text-align: right; }
  .form { background: #F0F0F0; }
  .backgrounds { max-height: 400px; overflow: auto; }
  .backgrounds .background { float: left; margin: 5px; background: #F0F0F0; text-align: center; }
  .clr { clear: both; }
  
  .message { text-align: center; margin: 5px; }
  .message h2 { font-size: 15px; }
  </style>
  </head>
  <body>
  
    <div class="page">
      <div class="head"><h1>WoT Signature Generator: settings</h1><div class='warning'>Don't leave wot_settings.php on your server. Delete wot_settings.php after you configure your signature!</div></div>
      <div class="main">
        <?php
        //Values
        $stat_values = array("Global Position","Battles Participated","Victories","Defeats","Battles Survived","Total Experience","Average Experience per Battle","Maximum Experience per Battle",
                  "Base Capture","Base Defense","Damage","Enemies Destroyed","Targets Detected","Hit Ratio");
        $position_available = array(1,1,0,0,1,1,0,1,1,1,1,1,0);  //Values whit allows fetching positions

        //Load posted values. This version doesn't have any defenses
        
        if (isset($_POST['ok']))
        {
          $_POST["img"] = (int)$_POST["img"];
          
          //Values
          $advanced = '0';
          $advanced_ext = '';
          for($i = 0; $i < count($stat_values); $i+=1)
          {
            if ($_POST["check_value_$i"]=="1")
              $advanced.='1';
            else
              $advanced.='0';
            if ($_POST["check_position_$i"]=="1")
              $advanced_ext.='1';
            else
              $advanced_ext.='0';
          }

          if ($_POST["tanksize"] == "Big")  $settings='1'; else if ($_POST["tanksize"] == "none") $settings='2'; else $settings='0';
          $settings .= (int)$_POST["tankorder"];
          $settings .= (int)$_POST["positionSmall"];
          $settings .= (int)$_POST["tankname"];
          $settings .= (int)$_POST["tanknumbers"];
          
          //Now, save them to config file
          $f = fopen('wot_settings.dat','w');
          fwrite($f,"$_POST[id]|$_POST[server]|$_POST[img]|$_POST[size]|$advanced|$advanced_ext|$settings|$_POST[flag]|$_POST[hsize]|$_POST[cache]|$_POST[languages]");
          fclose($f);
          
          echo '<div class="message"><h2>Your settings was saved</h2><img src="wot_signature.php" alt="result"></div>';
        } else {
          $str = file_get_contents('wot_settings.dat');
          $values = explode('|',$str);
          $_POST['id'] = $values[0];
          $_POST['server'] = $values[1];
          $_POST['img'] = $values[2];
          $_POST['size'] = $values[3];
          $advanced = $values[4];
          $advanced_ext = $values[5];
          $settings = $values[6];
          $_POST['flag'] = $values[7];
          $_POST['hsize'] = $values[8];
          $_POST['cache'] = $values[9];
          $_POST['languages'] = $values[10]; 
        }
        
        $dir = $_SERVER['SCRIPT_NAME'];
        $ex = explode('/',$dir);
        $ex[count($ex)-1] = '';
        $dir = implode('/', $ex);
        ?>
        
        <form method='POST'>
          <table width='100%' class='form'>
            <tr><td><label>Link</label></td><td>
              <?php echo "http://$_SERVER[SERVER_NAME]".$dir."wot_signature.png";?>
            </td></tr>
            <tr><td><label>WoT ID</label></td><td>
              <input type='text' name='id' value='<?php echo $_POST['id']?>'>
              <select name='server'>
                <option value='eu'<?php if ($_POST['server']=='eu') echo ' selected="selected"';?>>EU</option>
                <option value='com'<?php if ($_POST['server']=='com') echo ' selected="selected"';?>>NA</option>
                <option value='ru'<?php if ($_POST['server']=='ru') echo ' selected="selected"';?>>RU</option>
                <option value='sea'<?php if ($_POST['server']=='sea') echo ' selected="selected"';?>>SEA</option>
              </select>
            </td></tr>
            <tr><td><label>Size</label></td><td>
              <input type="radio" name="size" value="0"<?php if ($_POST["size"] == 0) echo ' checked="checked"';?>> 400x100 (WoT forums)<br>
              <input type="radio" name="size" value="1"<?php if ($_POST["size"] == 1) echo ' checked="checked"';?>> 512x128 (Standard size)
            </td></tr>
            <tr><td><label>Flag</label></td><td>
              <select name="flag">
                <option value="-1">-</option>
                <?php
                $d = opendir("wot_data/flags/");
                $found = array();
                while($s = readdir($d))
                {
                  if ($s!=".." && $s!=".")
                  {
                    $found[] = $s;
                  }
                }
                closedir($d);
                sort($found);
                foreach($found as $s)
                {
                    $ex = explode(".",$s);
                    if ($ex[0] == strtolower($_POST['flag']))
                      echo "<option value='$ex[0]' selected='true'>$ex[0]</option>";
                    else
                      echo "<option value='$ex[0]'>$ex[0]</option>";
                }
                ?>
              </select>
            </td></tr>
            <tr><td><label>Values</label></td><td><table class='values'>
            <?php
              for($i = 0; $i < count($stat_values); $i+=1)
              {
                echo '<tr><td>'.$stat_values[$i].'</td>';
                echo '<td><input type="checkbox" name="check_value_'.$i.'" value="1"'; if ($advanced[1+$i] == 1) echo ' checked = "checked"'; echo '> value</td>';
                if ($position_available[$i]) {
                  echo '<td><input type="checkbox" name="check_position_'.$i.'" value="1"'; if ($advanced_ext[$i] == 1) echo ' checked = "checked"'; echo '> position</td>';
                } else {
                  echo '<td><input type="hidden" name="check_position_'.$i.'" id="check_position_'.$i.'" value="1"></td>';
                }
                echo '</tr>';
              }
            ?></table>
              <input type='checkbox' name='positionSmall' value='1'<?php if ($settings[2] == 1) echo ' checked = "checked"';?>> Show only positions &lt; 1000
            </td></tr>
            <tr><td><label>Tanks size</label></td><td>
              <input type='radio' name='tanksize' value='none'<?php if ($settings[0] == 2) echo " checked='true'";?>> 0x0<br>
              <input type='radio' name='tanksize' value='mini'<?php if ($settings[0] == 3) echo " checked='true'";?>> 84x24<br>
              <input type='radio' name='tanksize' value='small'<?php if ($settings[0] == 0) echo " checked='true'";?>> 55x31<br>
              <input type='radio' name='tanksize' value='Big'<?php if ($settings[0] == 1) echo " checked='true'";?>> 86x66
            </td></tr>
            <tr><td><label>Tanks order</label></td><td>
              <input type='radio' name='tankorder' value='0'<?php if ($settings[1] == 0) echo " checked='true'";?>> Order by battles<br>
              <input type='radio' name='tankorder' value='1'<?php if ($settings[1] == 1) echo " checked='true'";?>> Order by tier
            </td></tr>
            <tr><td><label>Tanks</label></td><td>
              <input type='checkbox' name='tankname' value='1'<?php if ($settings[3] == 1) echo ' checked = "checked"';?>> Show tank name<br>
              <input type='checkbox' name='tanknumbers' value='1'<?php if ($settings[4] == 1) echo ' checked = "checked"';?>> Show tank battles/wins
            </td></tr>
            <tr><td><label>Font size</label><span class='small'>0 for default size</span></td><td>
              <input type='text' name='hsize' value='<?php echo $_POST['hsize']?>'>
            </td></tr>
            <tr><td><label>Cache</label></td><td>
              <input type='checkbox' name='cache' value='1'<?php if ($_POST['cache'] == 1) echo ' checked = "checked"';?>> Cache generated image
            </td></tr>
            <tr><td><label>Languages</label></td><td>
              <input type='checkbox' name='languages' value='1'<?php if ($_POST['languages'] == 1) echo ' checked = "checked"';?>> Generate signature in other than default language
            </td></tr>
            <tr><td><label>Background</label></td><td>
              <div class='backgrounds'>
                <div class='in'>
                <?php
                $d = opendir("wot_data/backgrounds/");
                while($s = readdir($d))
                {
                  if ($s!=".." && $s!=".")
                  {
                    $ex = explode('.',$s);
                    $num = $ex[0];
                    echo '<div class="background"><img src="wot_data/backgrounds/'.$s.'" alt="" onclick="document.getElementById(\'img'.$num.'\').checked = \'true\';"><br><input type="radio" id="img'.$num.'" name="img" value="'.$num.'"'; if ($_POST['img'] == $num) echo ' checked="true"'; echo '></div>';
                  }
                }
                closedir($d);
                ?>
                <div class='clr'></div>
                </div>
              </div>
            </td></tr>
            <?php if (is_writable('wot_settings.dat')) : ?>
            <tr><td colspan='2' align='center'><input type='submit' name='ok' value='Save settings'></td></tr>
            <?php else : ?>
            <tr><td colspan='2' align='center' style="color:red;font-weight:bold;">Your wot_settings.dat file is not writable!</td></tr>
            <?php endif; ?>
          </table>
        </form> 
      </div>
    </div>
  </body>
</html>