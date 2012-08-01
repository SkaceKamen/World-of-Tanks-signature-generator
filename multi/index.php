<?php
$site_url = "http://YOUR_SITE";
$sigCount = 109;

$preklad = array(
  array(
    array("Battles Participated","Victories","Defeats","Battles Survived","Total Experience","Average Experience per Battle","Maximum Experience per Battle",
          "Base Capture","Base Defense","Damage","Enemies Destroyed","Targets Detected","Hit Ratio"),
    array("Global Position")
  ),
  array(
    array("Účast v bitvách","Vítězství","Porážek","Přezito bitev","Celkem zkušeností","Průměr zkušeností na bitvu","Nejvíc zkušeností za bitvu",
          "Obsazení základny","Obrana základny","Poškození","Zničeno nepřátel","Cílů detekováno", "Přesnost"),
    array("Celková pozice")
  ),
  array(
    array("Проведено боёв","Побед","Проигрышей","Выжил в битвах","Суммарный опыт","Средний опыт за бой","Максимальный опыт за бой",
          "Очки захвата базы","Очки защиты базы","Нанесенные повреждения","Уничтожено","Обнаружено","Процент попадания"),
    array("Общий Место")
  )
);
$position_available = array(1,1,0,0,1,1,0,1,1,1,1,1,0);

foreach($_POST as $key=>$value)
  $_POST[$key] = htmlspecialchars($value);

function mysetcookie($var,$val)
{
  setcookie($var, $val);
  $_COOKIE[$var] = $val;
}
 
$_POST['img'] = (int)(@$_POST['img']);
$_POST['imgID'] = (int)(@$_POST['imgID']);
if ($_POST['imgID'] != 0) $_POST['img'] = $_POST['imgID'];
if ((int)(@$_POST['tankorder']) > 9) $_POST['tankorder'] = 0;
if ($_POST['img'] > $sigCount) $_POST['img'] = 0;


if (isset($_POST['id']))
  mysetcookie('LastID', $_POST['id']);
if (isset($_POST['size']))
  mysetcookie('LastSize', $_POST['size']);
if (isset($_POST['ovoce']))
{
  if ($_POST['check_2_0'] == 1)
    $advanced='1';
  else
    $advanced='0';
  for($i = 0; $i < count($preklad[0][0]); $i+=1)
    if ($_POST["check_value_$i"] == '1')
      $advanced .= '1';
    else
      $advanced .= '0';
  $advanced_ext = '';
  for($i = 0; $i < count($preklad[0][0]); $i+=1)
    if ($_POST["check_position_$i"]=="1")
      $advanced_ext .= '1';
    else
      $advanced_ext .= '0';
  
  if ($_POST['tanksize'] == 'Big')  $settings='1'; else if ($_POST['tanksize'] == 'none') $settings='2'; else if ($_POST['tanksize'] == 'mini') $settings='3'; else $settings='0';
  $settings .= (int)$_POST["tankorder"];
  $settings .= (int)$_POST["positionSmall"];
  $settings .= (int)$_POST["tankname"];
  $settings .= (int)$_POST["tanknumbers"];
      
  mysetcookie("LastAdvanced", $advanced);
  mysetcookie("LastAdvancedExt", $advanced_ext);
  mysetcookie("LastSettings", $settings);
}

if (isset($_POST['img']))
  mysetcookie('LastImg', $_POST['img']);

if (isset($_POST['font']))
  mysetcookie('LastFont', $_POST['font']);
  
if (isset($_POST['flag']))
  mysetcookie('LastFlag', $_POST['flag']);
    
require 'header.php';
      
      if ($jazyk == 1)
      {
        $jazyk = 1;
        $texty = array(
          'title' => 'Generátor signatur',
          'napoveda' => 'Pro zjištění ID si otevřete Váš profil na worldoftanks.com (nebo eu) a zkopírujte si adresu. Měla by vypadat nějak takhle:<br>http://game.worldoftanks.eu/accounts/<strong>500347336-SkaceKachna</strong>/<br> Zvýrazněná část je ID hráče, které zadáte do pole.',
          'id' => 'Zadejte Vaše ID',
          'server' => 'Server',
          'button' => 'Generovat',
          'button_save' => 'Uložit',
          'posted_title' => 'Vaše signatura:',
          'alert' => 'Pro zrychlení načítání budou data obnovována pouze jednou denně.<br><br>',
          'size' => 'Velikost',
          'history' => 'Historie',
          'history_text' => 'Ukládat historii',
          'advanced' => 'Pokročilá nastavení',
          'show' => 'Vypsat',
          'tanksize' => 'Velikost ikon tanků',
          'preview' => 'Náhled',
          'flag' => 'Vlajka',
          'font' => 'Velikost písma',
          'fontdesc' => 'Zadejte 0 pro automatickou velikost',
          'tankorder' => 'Seřadit tanky podle',
          'tankorder_0' => 'Počet bitev',
          'tankorder_1' => 'Tier',
          'category_0' => 'Vše',
          'category_1' => 'Originální rendery',
          'category_2' => 'Originální wallpapery',
          'category_3' => 'Historické',
          'category_4' => 'Ostatní',
          'category_5' => 'Kresby',
          'position' => 'Pozice',
          'value' => 'Hodnota',
          'tankname' => 'Zobrazit název tanku',
          'tanknumbers' => 'Zobrazit u tanku počet bitev a vítězství',
          'positionSmall' => 'Zobrazit pouze pozice < 1000'
        );
      } else {
        $jazyk = 0;
        $texty = array(
          'title' => 'Signature Generator',
          'napoveda' => 'To get your ID, open your profile on worldoftanks.com (or your server). Adress will look somelike that:<br>http://game.worldoftanks.eu/accounts/<strong>500347336-SkaceKachna</strong>/<br> Bold part of adress is your ID, witch you enter to textbox.',
          'id' => 'Enter Your ID',
          'server' => 'Server',
          'button' => 'Generate',
          'button_save' => 'Save',
          'posted_title' => 'Your signature:',
          'alert' => 'To incerase speed of loading, your singature will refresh only once per day.<br><br>',
          'size' => 'Size',
          'history' => 'History',
          'history_text' => 'Save history',
          'advanced' => 'Advanced settings',
          'show' => 'Show',
          'tanksize' => 'Size of tanks icons',
          'preview' => 'Preview',
          'flag' => 'Flag',
          'font' => 'Font size',
          'fontdesc' => 'Enter 0 for default size',
          'tankorder'=> 'Order tanks by',
          'tankorder_0' => 'Battles',
          'tankorder_1' => 'Tier',
          'category_0' => 'All',
          'category_1' => 'Original Renders',
          'category_2' => 'Original Wallpapers',
          'category_3' => 'Historical',
          'category_4' => 'Misc',
          'category_5' => 'Paintings',
          'position' => 'Position',
          'value' => 'Value',
          'tankname' => 'Show tank name',
          'tanknumbers' => 'Show number of battles and wins for tank',
          'positionSmall' => 'Show only positions < 1000'
        );
      }
      if (isset($_POST['id']) && $_POST['id']!='' && isset($_POST['server']) && $_POST['server']!='' && isset($_POST['img']) && $_POST['img']!='')
      {
        $advanced = $_COOKIE['LastAdvanced'];
        $advanced_ext = $_COOKIE['LastAdvancedExt'];
        $settings = $_COOKIE['LastSettings'];
          
        if ($_POST['flag'] == '')
          $_POST['flag'] = -1;
        
        $link = "$site_url/sig2@$_POST[id]@$_POST[server]@$_POST[img]@$_POST[size]@$advanced@$advanced_ext@$settings@$_POST[flag]@$_POST[font].png";
          
        echo "<h2>$texty[posted_title]</h2>";
        echo "<div class='sig'><img src='$link' alt=''></div>";

        echo "<h3>PHPBB</h3>";
        echo "<div class='code'>[url=http://www.worldoftanks.com][img]".$link."[/img][/url]</div>";
        echo "<h3>WOT Forum</h3>";
        echo "<div class='code'>[img]".$link."[/img]</div>";
        
        echo "<h3>HTML</h3>";
        echo "<div class='code'>".htmlspecialchars("<a href=http://www.worldoftanks.com><img src='$link' alt='' border=0></a>")."</div>";
        echo "<div class='napoveda'><div class='icon'>!</div>$texty[alert]</div>";
      } else {
      ?>
      <form method='post' onsubmit='return Generate()'>
        <input type='hidden' id='imgID' name='imgID' value='0'>
        <div class="napoveda"><div class="icon">?</div><?php echo $texty['napoveda']?><div class="clear"></div></div>
        <table width='100%'>
          <tr><td align='right'><label for="id"><?php echo $texty['id']?>:</label></td><td><input type="text" name="id" id="user" value="<?php if (isset($_COOKIE['LastID'])) echo $_COOKIE['LastID']?>" class="textinput"></td></tr>
          <tr><td align='right'><label for="server"><?php echo $texty["server"]?>:</label></td><td><select name="server" id="server"><option value="eu">EU</option><option value="com">US</option><option value="ru">RU</option><option value="sea">SEA</option></select></td></tr>
          <tr><td align='right'><label for="size"><?php echo $texty["size"]?>:</label></td><td><input type="radio" name="size" onChange="Preview()" id="size0" value="0" checked="true"<?php if ($_COOKIE["LastSize"] == 0) echo " checked=\"true\"";?>> 400x100 (WoT forums)<br><input type="radio" id="size1" onChange="Preview()" name="size" value="1"<?php if ($_COOKIE["LastSize"] == 1) echo " checked=\"true\"";?>> 512x128 (Standard size)</td></tr>
          <tr><td align='right'><label for="flag"><?php echo $texty["flag"]?>:</label></td><td>
              <select name="flag" id="cflag" onChange="Preview()">
                <option value="-1">-</option>
                <?php
                $d = opendir("flags/");
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
                    if ($ex[0] == strtolower($zeme))
                      echo "<option value='$ex[0]' selected='true'>$ex[0]</option>";
                    else
                      echo "<option value='$ex[0]'>$ex[0]</option>";
                }
                ?>
              </select>
          </td></tr>
          <tr><td colspan='2'>
            <div class="advanced">
              <?php
              if (!isset($_COOKIE['LastAdvanced']) || $_COOKIE['LastAdvanced']=='')
                 $_COOKIE['LastAdvanced'] = '111111111111111';
              if (!isset($_COOKIE['LastAdvancedExt']) || $_COOKIE['LastAdvancedExt']=='')
                 $_COOKIE['LastAdvancedExt'] = '11111111111111';
              if (!isset($_COOKIE['LastSettings']) || $_COOKIE['LastSettings']=='')
                $_COOKIE["LastSettings"] = "01101";
              $advanced = str_split($_COOKIE['LastAdvanced']);
              $advanced_ext = str_split($_COOKIE['LastAdvancedExt']);
              $settings = $_COOKIE['LastSettings'];
              
              echo "<div id='advb'>$texty[advanced]</div>";
              echo "<div id='adv1'>";
              echo "<div id='adv2'>";
              echo "<table><tr>";
              echo "<td><strong>$texty[show]:</strong></td>";
              echo "<td>";
              echo "<table>";
              echo '<tr><td>'.$preklad[$jazyk][1][0].'</td><td></td>';
              echo '<td><input type="checkbox" name="check_2_0" onChange="Preview()" value="1" id="check_2_0"' . (($advanced[0]==1) ? ' checked="true"' : '') . '> Pozice</td></tr>'; 
              for($i = 0; $i < count($preklad[$jazyk][0]); $i+=1)
              {
                echo '<tr>';
                echo '<td>'.$preklad[$jazyk][0][$i].'</td>';
                echo '<td><input type="checkbox" name="check_value_'.$i.'" id="check_value_'.$i.'" value="1" onChange="Preview()"'; if ($advanced[1+$i] == 1) echo ' checked = "checked"'; echo '> '.$texty['value'].'</td>';
                if ($position_available[$i]) {
                  echo '<td><input type="checkbox" name="check_position_'.$i.'" id="check_position_'.$i.'" value="1" onChange="Preview()"'; if ($advanced_ext[$i] == 1) echo ' checked = "checked"'; echo '> '.$texty['position'].'</td>';
                } else {
                  echo '<td><input type="hidden" name="check_position_'.$i.'" id="check_position_'.$i.'" value="1"></td>';
                }
                echo '</tr>';
              }
              echo "<tr><td colspan='2' align='center'><input type='checkbox' name='positionSmall' id='positionSmall' value='1' onChange='Preview()'"; if ($settings[2] == 1) echo ' checked = "checked"'; echo "> $texty[positionSmall]</td></tr>";
              echo "</table></td></tr>";
              echo "<tr><td><b>$texty[tanksize]</b>:</td><td>";
              echo "<input type='radio' name='tanksize' value='none' onChange='Preview()' id='tankNone'"; if ($settings[0] == 2) echo " checked='true'"; echo "> 0x0<br>";
              echo "<input type='radio' name='tanksize' onChange='Preview()' value='mini' id='tankMini'";   
              if ($settings[0] == 3) echo " checked='true'";
              echo "> 84x24<br><input type='radio' name='tanksize' onChange='Preview()' value='small' id='tankSmall'";   
              if ($settings[0] == 0) echo " checked='true'";
              echo "> 55x31<br><input type='radio' name='tanksize' onChange='Preview()' value='Big' id='tankBig'";
              if ($settings[0] == 1) echo " checked='true'";
              echo "> 86x66</td></tr>";
              
              echo "<tr><td><strong>$texty[tankorder]</strong></td><td>";
              echo "<input type='radio' name='tankorder' onChange='Preview()' value='0'";
              if ($advanced[14] == 0) echo " checked='true'";
              echo "> $texty[tankorder_0]<br>";
              echo "<input type='radio' name='tankorder' onChange='Preview()' value='1' id='tankOrder'";
              if ($advanced[14] == 1) echo " checked='true'";
              echo "> $texty[tankorder_1]";
              echo "</td></tr>";
              
              echo "<tr><td></td><td><input type='checkbox' name='tankname' id='tankname' value='1' onChange='Preview()'"; if ($settings[3] == 1) echo ' checked = "checked"'; echo "> $texty[tankname]<br>";
              echo "<input type='checkbox' name='tanknumbers' id='tanknumbers' value='1' onChange='Preview()'"; if ($settings[4] == 1) echo ' checked = "checked"'; echo "> $texty[tanknumbers]</td></tr>";
              
              echo "<tr><td><strong>$texty[font]</strong>:</td><td><input type='text' name='font' onChange='Preview()' value='";
              if (isset($_COOKIE['LastFont']) && $_COOKIE['LastFont']!="") echo $_COOKIE['LastFont']; else echo "0";
              echo "' id='font'><br>($texty[fontdesc])";
              echo "</td></tr>";
              
              echo "</table></div></div>";
              ?>
            </div></td></tr>
        </table>
      </div>
    </div>
    <div class="sig_buttons">
      <div onclick="sig_category(0)" class="button selected" id="cat0"><?php echo $texty["category_0"]?></div>
      <div onclick="sig_category(1)" class="button" id="cat1"><?php echo $texty["category_1"]?></div>
      <div onclick="sig_category(2)" class="button" id="cat2"><?php echo $texty["category_2"]?></div>
      <div onclick="sig_category(3)" class="button" id="cat3"><?php echo $texty["category_3"]?></div>
      <div onclick="sig_category(5)" class="button" id="cat5"><?php echo $texty["category_5"]?></div>
      <div onclick="sig_category(4)" class="button" id="cat4"><?php echo $texty["category_4"]?></div>
      <div class="clear"></div>
    </div>
    <div class="sig_list">
      <div class="inside" id="siglist">
        <?php
          if ($_COOKIE["LastImg"]=="")
            $_COOKIE["LastImg"] = 1;
          for($i=1;$i<=$sigCount;$i+=1)
            { echo "<div class='singature_float'><img src='sig/wot$i.png' alt='' onclick='sig_selected($i)'><div class='check'><input type='radio' id='img$i' name='img' value='$i' "; if ($i == $_COOKIE["LastImg"]) echo " checked='true'"; echo "></div></div>"; }
        ?>
        <div class="clear"></div>
      </div>
    </div>
    <div class="page">
      <div class="main">
        <div align="center">
          <div class='preview'><img id='preview' src='sig/wotpreview.png'></div>
          <input type="submit" name="ovoce" value="<?php echo $texty['button']?>" class="button"> <button class="button" onclick="return Preview();"><?php echo $texty["preview"];?></button>
        </div>
      <script type="text/javascript" src="form.js"></script>
      <script type="text/javascript" src="ovoce.js"></script>
      </div>
      </form>
      <?php }
require 'footer.php';
?>
