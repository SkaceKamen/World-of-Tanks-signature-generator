<?php
/**
 *  WOT SIGNATURE GENERATOR
 *  Created by Zípek Jan (zipek.cz, menxmenx@gmail.com)
 *  
 *  File: wot_index_lines.php
 *  Description: Settings of picture containing statistics
 *     
*/   

/*
 * DEFINITION:
 *    STAT => [VALUE_AVAIBLE, POSITION_AVAIBLE, PERCENTAGE_AVAIBLE] 
*/   

$keys_all = array(
  'integrated_rating'  => array(1,1,0),
  'battles'            => array(1,1,0),
  'battle_wins'        => array(1,1,1),
  'losses'             => array(1,0,1),
  'survived_battles'   => array(1,0,1),
  'xp'                 => array(1,1,0),
  'battle_avg_xp'      => array(1,1,0),
  'max_xp'             => array(1,0,0),
  'ctf_points'         => array(1,1,0),
  'dropped_ctf_points' => array(1,1,0),
  'damage_dealt'       => array(1,1,0),
  'frags'              => array(1,1,0),
  'spotted'            => array(1,0,0),
  'hits_percents'      => array(0,0,1)
);

$show_form = true;

$values = array();

/** Load last settings from cookies (if there are any) **/
if (isset($_COOKIE['player_id'])) $values['player_id'] = $_COOKIE['player_id'];
if (isset($_COOKIE['server'])) $values['server'] = $_COOKIE['server'];
if (isset($_COOKIE['flag'])) $values['flag'] = $_COOKIE['flag'];
if (isset($_COOKIE['background'])) $values['background'] = $_COOKIE['background'];
if (isset($_COOKIE['image_size'])) $values['image_size'] = $_COOKIE['image_size'];
if (isset($_COOKIE['font_size'])) $values['font_size'] = $_COOKIE['font_size'];
if (isset($_COOKIE['lines'])) $values['lines'] = $_COOKIE['lines'];
if (isset($_COOKIE['settings'])) $values['settings'] = $_COOKIE['settings'];

$values = array_merge($values, $POST);

/** Validate received values **/
if (isset($values['player_id']))
{
    $values['player_id'] = trim($values['player_id']);
    if ($values['player_id'] != '')
    {
        if (strpos($values['player_id'], '/') != 0)
        {
            $explode = explode('/', $values['player_id']);
            $end = count($explode) - 1;
            while($explode[$end] == '' && $end > 0) {
                $end--;
            }
            if ($explode[$end] != '')
            {
                $values['player_id'] = $explode[$end];
                if (strpos($values['player_id'], '-'))
                {
                    $explode = explode('-', $values['player_id']);
                    $values['player_id'] = (int)$explode[0];
                }
            } else {
                $values['player_id'] = '';
            }
        }
    }
}

if (isset($values['server']))
{
    $values['server'] = strtolower($values['server']);
    if (!in_array($values['server'], $wot_servers))
        $values['server'] = WOT_DEFAULT_SERVER;    
}

if (isset($values['flag']))
{
    $values['flag'] = strtolower($values['flag']);
    if (!file_exists(WOT_FOLDER_FLAGS . '/' . $values['flag'] . '.gif'))
    {
        $values['flag'] = '-1';
    }
}

if (isset($values['background']))
{
    $values['background'] = strtolower((int)$values['background']);
    if (!file_exists(WOT_FOLDER_BACKGROUNDS . '/wot' . (int)$values['background'] . '.png') && $values['background'] != 0)
        $values['background'] = '1';    
}

if (isset($values['image_size']))
{
    $values['image_size'] = (int)$values['image_size'];
    if ($values['image_size'] < 1 || $values['image_size'] > 1) 
        $values['image_size'] = 0;   
}

if (isset($values['font_size']))
{
    $values['font_size'] = (int)$values['font_size'];
}

//Parse lines and settings
if (isset($values['kachna']))
{
    $lines = '';
    foreach($keys_all as $key => $avaible)
    {
        for($i = 0; $i < 3; $i+=1)
            if ($avaible[$i] && isset($values[$key . $i]) && $values[$key . $i])
                $lines .= '1';
            else
                $lines .= '0';
    }
    
    $settings = '';
    $settings .= (int)(@$values['tanksize']);
    $settings .= (int)(@$values['tankorder']);
    $settings .= (int)(@$values['tankname']); 
    $settings .= (int)(@$values['tankwins']);
    $settings .= (int)(@$values['tankbattles']); 
    $settings .= (int)(@$values['tankpercentage']);
    $settings .= (int)(@$values['position_small']);
    $settings .= (int)(@$values['server_label']);
    $settings .= (int)(@$values['effeciency_rating']);
    $settings .= (int)(@$values['clan_name']);
    $settings .= (int)(@$values['clan_image']);
    
    $values['lines'] = $lines;
    $values['settings'] = $settings;
    
    //Delicious cookies
    setcookie('player_id', $values['player_id'],9999999999);
    setcookie('server', $values['server'],9999999999);
    setcookie('flag', $values['flag'],9999999999);
    setcookie('background', $values['background'],9999999999);
    setcookie('image_size', $values['image_size'],9999999999);
    setcookie('font_size', $values['font_size'],9999999999);
    setcookie('lines', $values['lines'],9999999999);
    setcookie('settings', $values['settings'],9999999999);   
    
    $show_form = false;           
}


if (!isset($values['lines']))
    $values['lines'] = WOT_DEFAULT_LINES;
if (!isset($values['settings']))
    $values['settings'] = WOT_DEFAULT_SETTINGS;

$lines = str_split($values['lines']);
$settings = str_split($values['settings']);

if ($show_form):

//Get backgrounds
$backgrounds = glob(WOT_FOLDER_BACKGROUNDS . '/wot*.png');

//Load backgrounds categories
$categories = array();
if (file_exists(WOT_FOLDER_BACKGROUNDS . '/categories.dat'))
{
    $cat_lines = explode(PHP_EOL, file_get_contents(WOT_FOLDER_BACKGROUNDS . '/categories.dat'));
    foreach($cat_lines as $line)
    {
        $ex = explode(':', trim($line));
        if (!isset($categories[$ex[1]]))
        {
            $categories[$ex[1]] = array();
        }
        if (strpos($ex[0], '-'))
        {
            $range = explode('-', $ex[0]);
            for($i = (int)$range[0]; $i <= (int)$range[1]; $i++)
                $categories[$ex[1]][] = $i;
        } else {
            $categories[$ex[1]][] = (int)$ex[0];
        }
    }
}
?>
<form method='post' onsubmit='return Generate()'>
  <input type='hidden' id='imgID' name='imgID' value='0'>
  <div class="napoveda"><div class="icon">?</div><?php echo $translation['napoveda']?><div class="clear"></div></div>
  <table>
    <tr>
      <td align='right'><label for="id"><?php echo $translation['id']?>:</label></td>
      <td><input type="text" name="player_id" id="user" value="<?php echo @$values['player_id']?>" class="textinput"></td>
    </tr>
    <tr>
      <td align='right'><label for="server"><?php echo $translation["server"]?>:</label></td>
      <td>
        <select name="server" id="server">
          <option value="eu"<?php selected(@$values['server'],'eu')?>>EU</option>
          <option value="com"<?php selected(@$values['server'],'com')?>>US</option>
          <option value="ru"<?php selected(@$values['server'],'ru')?>>RU</option>
          <option value="sea"<?php selected(@$values['server'],'sea')?>>SEA</option>
        </select>
      </td>
    </tr>
    <tr>
      <td align='right'><label for="size"><?php echo $translation["size"]?>:</label></td>
      <td>
        <input type="radio" name="image_size" id="size0" onChange="Preview()" value="0"<?php checked(@$values['image_size'], 0)?>> 400x100 (WoT forums)<br>
        <input type="radio" name="image_size" id="size1" onChange="Preview()"value="1"<?php checked(@$values['image_size'], 1)?>> 512x128 (Standard size)
      </td>
    </tr>
    <tr>
      <td align='right'><label for="flag"><?php echo $translation["flag"]?>:</label></td>
      <td>
        <select name="flag" id="cflag" onChange="Preview()">
          <option value="-1">-</option>
          <?php
          $d = opendir(WOT_FOLDER_FLAGS);
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
              if ($ex[0] == $values['flag'])
                echo "<option value='$ex[0]' selected='true'>$ex[0]</option>";
              else
                echo "<option value='$ex[0]'>$ex[0]</option>";
          }
          ?>
        </select>
      </td>
    </tr>
    <tr>
    <td colspan='2'>
      <div class="advanced">
        <div id='advb'><?php echo $translation['advanced']?></div>
          <div id='adv1'>
            <div id='adv2'>
              <table>
                <tr>
                  <td><strong><?php echo $translation['show']?>:</strong></td>
                  <td>
                    <table>
                      <thead>
                        <tr>
                          <th></th>
                          <th><?php echo $translation['value']?></th>
                          <th><?php echo $translation['position']?></th>
                          <th><?php echo $translation['percents']?></th>
                    <?php
                    $i = 0;
                    foreach($keys_all as $key=>$values):
                    ?>
                      <tr>
                        <td><?php echo $translation[$key]?>:</td>
                        <td><?php if ($values[0]): ?><input type="checkbox" onChange="Preview()" name="<?php echo $key?>0" id="<?php echo $key?>0" value="1"<?php checked(@$lines[($i*3)+0])?>><?php endif;?></td>
                        <td><?php if ($values[1]): ?><input type="checkbox" onChange="Preview()" name="<?php echo $key?>1" id="<?php echo $key?>1" value="1"<?php checked(@$lines[($i*3)+1])?>><?php endif;?></td>
                        <td><?php if ($values[2]): ?><input type="checkbox" onChange="Preview()" name="<?php echo $key?>2" id="<?php echo $key?>2" value="1"<?php checked(@$lines[($i*3)+2])?>><?php endif;?></td>
                      </tr>
                    <?php
                      $i++;
                    endforeach;
                    ?>
                      <tr>
                        <td colspan='4' align='center'>
                          <input type='checkbox' name='position_small' id='positionSmall' value='1' onChange='Preview()'<?php checked($settings[2],1)?>> <?php echo $translation['positionSmall']?>
                        </td>
                      </tr>
                    </table>  
                  </td>
                </tr>
                <tr>
                  <td><b><?php echo $translation['server']?>:</b></td>
                  <td><input type='checkbox' name='server_label' value='1' onchange='Preview()'<?php checked(@$settings[7],1)?>> <?php echo $translation['server_label']?></td>
                <tr>
                  <td><b><?php echo $translation['effeciency_rating']?>:</b></td>
                  <td>
                    <input type='radio' name='effeciency_rating' onChange='Preview()' value='0'<?php checked(@$settings[8], 0)?>> <?php echo $translation['eff_none']?><br>
                    <input type='radio' name='effeciency_rating' onChange='Preview()' value='1'<?php checked(@$settings[8], 1)?>> <?php echo $translation['eff_old']?><br>
                    <input type='radio' name='effeciency_rating' onChange='Preview()' value='2'<?php checked(@$settings[8], 2)?>> <?php echo $translation['eff_new']?><br>
                  </td>
                </tr>
                <tr>
                  <td><b><?php echo $translation['clan']?>:</b></td>
                  <td>
                    <input type='checkbox' name='clan_name' onChange='Preview()' value='1'<?php checked(@$settings[9], 1)?>> <?php echo $translation['clan_name']?><br>
                    <?php if (WOT_ALLOW_CLAN_IMAGE):?><input type='checkbox' name='clan_image' onChange='Preview()' value='1'<?php checked(@$settings[10], 1)?>> <?php echo $translation['clan_image']?><?php endif;?>
                  </td>
                </tr>
                <tr>
                  <td><b><?php echo $translation['tanksize']?></b>:</td>
                  <td>
                    <input type='radio' name='tanksize' onChange='Preview()' value='2' id='tankNone'<?php checked($settings[0], 2)?>> 0x0<br>
                    <input type='radio' name='tanksize' onChange='Preview()' value='0' id='tankMini'<?php checked($settings[0], 3)?>> 84x24<br>
                    <input type='radio' name='tanksize' onChange='Preview()' value='3' id='tankSmall'<?php checked($settings[0], 0)?>> 55x31<br>
                    <input type='radio' name='tanksize' onChange='Preview()' value='1' id='tankBig'<?php checked($settings[0], 1)?>> 86x66
                  </td>
                </tr>
                <tr>
                  <td><strong><?php echo $translation['tankorder']?></strong></td>
                  <td>
                    <input type='radio' name='tankorder' onChange='Preview()' value='0'<?php checked($settings[1], 0)?>> <?php echo $translation['tankorder_0']?><br>
                    <input type='radio' name='tankorder' onChange='Preview()' value='1'<?php checked($settings[1], 1)?>> <?php echo $translation['tankorder_1']?><br>
                    <input type='radio' name='tankorder' onChange='Preview()' value='2'<?php checked($settings[1], 2)?>> <?php echo $translation['tankorder_2']?>
                  </td>
                </tr>
                <tr>
                  <td></td>
                  <td>
                    <input type='checkbox' name='tankname' id='tankname' value='1' onChange='Preview()'<?php checked($settings[2])?>> <?php echo $translation['tankname']?><br>
                    <input type='checkbox' name='tankwins' id='tankwins' value='1' onChange='Preview()'<?php checked($settings[3])?>> <?php echo $translation['tankwins']?><br>
                    <input type='checkbox' name='tankbattles' id='tankbattles' value='1' onChange='Preview()'<?php checked($settings[4])?>> <?php echo $translation['tankbattles']?><br>
                    <input type='checkbox' name='tankpercents' id='tankpercents' value='1' onChange='Preview()'<?php checked($settings[5])?>> <?php echo $translation['tankpercents']?>
                  </td>
                </tr>
                <tr>
                  <td><strong><?php echo $translation['font']?></strong>:</td>
                  <td>
                    <input type='text' name='font_size' id='font' onChange='Preview()' value='<?php echo (int)(@$values['font_size'])?>'><br>
                    (<?php echo $translation['fontdesc']?>)
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </td>
    </tr>
  </table>
  </div>
  </div>
  <?php if (count($categories) > 0): ?>
  <div class="sig_buttons">  
    <div onclick="sig_category()" class="button selected" id="cat_all"><?php echo @$translation['category_all']?></div>  
    <?php foreach($categories as $category=>$bgs): ?>
    <div onclick="sig_category('<?php echo $category?>')" class="button" id="cat_<?php echo $category?>"><?php echo @$translation[trim('category_' . $category)]?></div>  
    <?php endforeach; ?>
    <div class="clear"></div>
  </div>
  <?php endif; ?>
  <div class="sig_list">
    <div class="inside" id="siglist">
    <?php
      for($i = 1;$i <= count($backgrounds);$i += 1):
      ?>
        <div class='singature_float'>
          <img src='<?php echo WOT_FOLDER_BACKGROUNDS?>/wot<?php echo $i?>.png' alt='' onclick='sig_selected(<?php echo $i?>)'>
          <div class='check'>
            <input type='radio' id='background<?php echo $i?>' name='background' value='<?php echo $i?>'<?php if ($i == @$values['background']):?> checked="checked"<?php endif;?>>
          </div>
        </div>
      <?php
      endfor;
    ?>
    <div class="clear"></div>
    </div>
  </div>
  <div class="page">
  <div class="main">
  <div align="center">
    <div class='preview'><img id='preview' src='<?php echo WOT_FOLDER_BACKGROUNDS . '/preview.png'?>'></div>
    <input type="submit" name="kachna" value="<?php echo $translation['button']?>" class="button"> <button class="button" onclick="return Preview();"><?php echo $translation["preview"];?></button>
  </div>
  <script type="text/javascript">
    var KEYS_ALL = <?php echo json_encode(array_keys($keys_all))?>;
    var BACKGROUNDS_TOTAL = <?php echo count($backgrounds)?>;
    var CATEGORIES = <?php echo json_encode($categories)?>; 
  </script>
  <script type="text/javascript" src="wot_data/form.js"></script>   
  <script type="text/javascript">sig_category();</script>
  </div>
</form>
<?php
else: 
  if (WOT_MOD_REWRITE)
  {
      $sep = WOT_MOD_REWRITE_SEPARATOR;
      $link = $SITE_URL . '/' . WOT_MOD_REWRITE_LINK . $sep . $values['player_id'] . $sep . $values['server'] . $sep . $values['background'] . $sep . $values['image_size'] . $sep . $values['lines'] . $sep . $values['settings'] . $sep . $values['flag'] . $sep . $values['font_size'] . '.png'; 
  } else {
      $link = $SITE_URL . "/wot_signature.php?id={$values['player_id']}&server={$values['server']}&img={$values['background']}&size={$values['image_size']}&lines={$values['lines']}&settings={$values['settings']}&flag={$values['flag']}&font={$values['font_size']}";
  }
?>
  <h2><?php echo $translation['posted_title']?></h2>
  <div class='sig'><img src='<?php echo $link?>' alt=''></div>

  <h3>PHPBB</h3>
  <div class='code'>[url=http://www.worldoftanks.com][img]<?php echo $link?>[/img][/url]</div>
  <h3>WOT Forum</h3>
  <div class='code'>[img]<?php echo $link?>[/img]</div>
        
  <h3>HTML</h3>
  <div class='code'><?php echo htmlspecialchars("<a href=http://www.worldoftanks.com><img src='$link' alt='' border=0></a>")?></div>
  <div class='napoveda'><div class='icon'>!</div><?php echo $translation['alert']?></div>
<?php
endif;
?>