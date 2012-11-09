<?php
/**
 *  WOT SIGNATURE GENERATOR
 *  Created by Zípek Jan (zipek.cz, menxmenx@gmail.com)
 *  
 *  File: wot_index_garage.php
 *  Description: Settings of picture containing garage
 *     
*/   

if (!WOT_GARAGE_ENABLED)
    exit;

/*
 * DEFINITION:
 *    STAT => [VALUE_AVAIBLE, POSITION_AVAIBLE, PERCENTAGE_AVAIBLE] 
*/   

$backgrounds = glob(WOT_FOLDER_BACKGROUNDS . '/wot*.png');

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
if (isset($_COOKIE['image_size'])) $values['image_size'] = $_COOKIE['image_size'];
if (isset($_COOKIE['garage_settings'])) $values['settings'] = $_COOKIE['garage_settings'];

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
            } else {
                $values['player_id'] = '';
            }
        }
        if (strpos($values['player_id'], '-'))
        {
            $explode = explode('-', $values['player_id']);
            $values['player_id'] = (int)$explode[0];
        }
    }
} else {
	$values['player_id'] = '';
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

if (isset($values['image_size']))
{
    $values['image_size'] = (int)$values['image_size'];
    if ($values['image_size'] < 1 || $values['image_size'] > 1) 
        $values['image_size'] = 0;   
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
    
    $values['lines'] = $lines;
    $values['settings'] = $settings;
    
    //Delicious cookies
    setcookie('player_id', $values['player_id'],9999999999);
    setcookie('server', $values['server'],9999999999);
    setcookie('flag', $values['flag'],9999999999);
    setcookie('image_size', $values['image_size'],9999999999);
    setcookie('garage_settings', $values['settings'],9999999999);   
    
    $show_form = false;           
}

if (!isset($values['settings']))
    $values['settings'] = WOT_GARAGE_DEFAULT_SETTINGS;

$settings = str_split($values['settings']);

if ($show_form):
?>
<div class="napoveda"><div class="icon">?</div><?php echo $translation['napoveda']?><div class="clear"></div></div>

<form method='post'>
<table width='100%'>
  <tr>
    <td align='right'><label for="id"><?php echo $translation['id']?>:</label></td>
    <td><input type="text" name="player_id" id="user" value="<?php echo $values['player_id']?>" class="textinput"></td>
  </tr>
  <tr>
    <td align='right'><label for="server"><?php echo $translation['server']?>:</label></td>
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
    <td align='right'><label for="flag"><?php echo $translation['flag']?>:</label></td>
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
    <td align='right'><label for="size"><?php echo $translation["size"]?>:</label></td>
    <td>
      <input type="radio" name="image_size" id="size0" onChange="Preview()" value="0"<?php checked(@$values['image_size'], 0)?>> 400x100 (WoT forums)<br>
      <input type="radio" name="image_size" id="size1" onChange="Preview()"value="1"<?php checked(@$values['image_size'], 1)?>> 512x128 (Standard size)
    </td>
  </tr>
  <tr>
    <td align='right'><label><?php echo $translation['settings']?>:</label></td>
    <td>
      <table>
        <tr>
          <td><b><?php echo $translation['tanksize']?></b>:</td>
          <td>
            <input type='radio' name='tanksize' onChange='Preview()' value='1'<?php checked($settings[0],1)?>> 84x24<br>
            <input type='radio' name='tanksize' onChange='Preview()' value='0'<?php checked($settings[0],0)?>> 55x31<br>
            <input type='radio' name='tanksize' onChange='Preview()' value='2'<?php checked($settings[0],2)?>> 86x66<br>
            <input type='radio' name='tanksize' onChange='Preview()' value='3'<?php checked($settings[0],2)?>> <?php echo $translation['tanksize_3']?><br>
          </td>
        </tr>
        <tr>
          <td><strong><?php echo $translation['tankorder']?></strong></td>
          <td>
            <input type='radio' name='tankorder' onChange='Preview()' value='0'<?php checked($settings[1],0)?>> <?php echo $translation['tankorder_0']?><br>
            <input type='radio' name='tankorder' onChange='Preview()' value='1'<?php checked($settings[1],1)?>> <?php echo $translation['tankorder_1']?><br>
            <input type='radio' name='tankorder' onChange='Preview()' value='2'<?php checked($settings[1],2)?>> <?php echo $translation['tankorder_2']?>
          </td>
        </tr>
      </table>
    
      <table>
        <tr><td><input type='checkbox' name='tankname' value='1' onChange='Preview()'<?php checked($settings[2])?>></td><td><?php echo $translation['tankname']?></td></tr>
        <tr><td><input type='checkbox' name='tankwins' value='1' onChange='Preview()'<?php checked($settings[3])?>></td><td><?php echo $translation['tankwins']?></td></tr>
        <tr><td><input type='checkbox' name='tankbattles' value='1' onChange='Preview()'<?php checked($settings[4])?>></td><td><?php echo $translation['tankbattles']?></td></tr>
        <tr><td><input type='checkbox' name='tankpercents' value='1' onChange='Preview()'<?php checked($settings[5])?>></td><td><?php echo $translation['tankpercents']?></td></tr>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td align='center' colspan='2'>
      <div class='sig'><img src='<?php echo WOT_FOLDER_BACKGROUNDS?>/preview.png' id='preview'></div>
      <input type='submit' name='kachna' value='<?php echo $translation['button']?>'> <button onclick='return Preview();'><?php echo $translation['preview']?></button>
      <script type='text/javascript' src='<?php echo WOT_FOLDER_DATA?>/form_tanks.js'></script>
    </td>
  </tr>
</table>
</form>
<?php
else: 
  if (WOT_GARAGE_MOD_REWRITE)
  {
      $sep = WOT_GARAGE_MOD_REWRITE_SEPARATOR;
      $link = $SITE_URL . '/' . WOT_GARAGE_MOD_REWRITE_LINK . $sep . $values['player_id'] . $sep . $values['server'] . $sep . $values['image_size'] . $sep . $values['settings'] . $sep . $values['flag'] . '.png'; 
  } else {
      $link = $SITE_URL . "/wot_signature.php?id={$values['player_id']}&server={$values['server']}&size={$values['image_size']}&settings={$values['settings']}&flag={$values['flag']}";
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
