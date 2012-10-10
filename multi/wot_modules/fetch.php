<?php
class PlayerStat
{
    public $key;
    public $value;
    public $position;
    public $percentage;
    
    public function __construct($key, $value, $position = null, $percentage = null)
    {
        $this->key = $key;
        $this->value = $value;
        $this->position = $position;
        $this->percentage = $percentage;
    }
}

class PlayerTank
{
    public $name;
    public $image;
    public $nation;
    public $tier;
    
    public $vehicle_class;
    
    public $battles;
    public $wins;
    
    public function __construct($nation, $tier, $name, $image, $battles, $wins, $vehicle_class)
    {
        $this->nation = $nation;
        $this->tier = $tier;
        $this->name = $name;
        $this->image = $image;
        $this->battles = $battles;
        $this->wins = $wins;
        $this->vehicle_class = $vehicle_class;
        $this->percentage = round(($wins/$battles)*100);
    }
}

class Player
{
    public $server;
    public $server_url;
    public $id;
    
    public $found = false;
    
    public $name;
    public $clan_short;
    public $clan_name;
    public $clan_image;
    
    public $data = array();
    public $tanks = array();
    
    public $achievements;
    
    public $type = 0; //0 = json, 1 = html
    public $cache;
    
    public $effeciency_rating;
    public $effeciency_rating_new;

    public function __construct($server, $player_id, $cache_dir = '', $type = 0)
    {
        $this->server = $server;
        $this->player_id = $player_id;
        $this->cache = $cache_dir;
        $this->type = $type;
        $this->load(); 
    }
    
    public function load()
    {     
        if ($this->type == 0)
        {
            $filename = $this->cache . '/' . date('d_m_y') . '_' . $this->server . '_' . $this->player_id . '.json'; 
        
            $base = 'http://worldoftanks.'. $this->server;
            if ($this->server == 'sea') {
                $base = 'http://worldoftanks-sea.com';
            }
            $this->server_url = $base;
        
            if (!file_exists($filename))
            {
                $url = $base .'/uc/accounts/' .$this->player_id . '/api/1.5/?source_token=WG-WoT_Assistant-1.3.1';
                $try = 0;
                do
                {
                    $json = @file_get_contents($url);
                    $try++;
                    if ($try > 5)
                    {
                        //Failed to load json
                        $this->found = false;
                        return;
                    }
                } while (strlen($json) == 0);
                file_put_contents($filename, $json);     
            }
            
            $this->found = $this->loadJson($filename);
        } else {
            $this->found = $this->loadHtml();
        }
        
        /** Generate effeciency rating **/
        //Thanks "AgeofStrife" for creating script co calculate effeciency rating
        $avgTier = 0;
        $tdCount = 0;
        $spgCount = 0;
        $medCount = 0;
        $hvyCount = 0;
        $lgtCount = 0;
        foreach($this->tanks as $tank)
        {
            $avgTier += $tank->tier * $tank->battles;
            switch ($tank->vehicle_class)
            {
        			case 'AT-SPG': //td
        				$tdCount = $tdCount + $tank->battles; break;
        			case 'SPG': //arty
        				$spgCount = $spgCount + $tank->battles; break;
        			case 'mediumTank': //med
        				$medCount = $medCount + $tank->battles; break;
        			case 'heavyTank': //heavy
        				$hvyCount = $hvyCount + $tank->battles; break;
        			case 'lightTank': //light
        				$lgtCount = $lgtCount + $tank->battles; break;
        		}
        }
      
        
        $battles = $this->data['battles']->value;
      	$avgDamage = $this->data['damage_dealt']->value / $battles;
      	$avgFrags = $this->data['frags']->value / $battles;
      	$avgSpotted = $this->data['spotted']->value / $battles;
      	$avgCapture = $this->data['ctf_points']->value / $battles;
      	$avgDefense = $this->data['dropped_ctf_points']->value / $battles;
      	$avgTankTier = round($avgTier / $battles , 2);
      
      	$this->effeciency_rating = round($avgDamage * (1.5 / $avgTankTier +  0.2) + $avgFrags * (350 - 20 * $avgTankTier) + $avgSpotted * 200 + 150 * ($avgCapture + $avgDefense));
        
        $tdPC = $tdCount / $battles;
      	$spgPC = $spgCount / $battles;
      	$lgtPC = $lgtCount / $battles;
      	$medPC = $medCount / $battles;
      	$hvyPC = $hvyCount / $battles;

      	$avgFrags = $avgFrags * 20 * (1 + $tdPC*(10/1.33) + $spgPC*(10/1.33) + $lgtPC*5 + $medPC*5 + $hvyPC*(10/1.3));
      	$avgDamage = $avgDamage * 2.5 * (1 + $tdPC*(80/57) + $spgPC*1.6 + $lgtPC*(0.2+2/30) + $medPC*0.4 + $hvyPC*1.6) / $avgTankTier;
      	$avgSpotted = $avgSpotted * 20 * (1 - $spgPC + $lgtPC*10 + $medPC*5);
      	$avgDefense = $avgDefense * 30 * (1 + $lgtPC*(3+1/3) + $medPC*(6+2/3));
      	$avgCapture = $avgCapture * 10 * (1 - $spgPC + $lgtPC*(6+2/3) + $medPC*(6+2/3));
      	$effRatingNew = $avgDamage + $avgFrags + $avgSpotted + $avgCapture + $avgDefense;
      	$effRatingNew = round($effRatingNew);
      	$this->effeciency_rating_new = $effRatingNew;
    
    }
    
    public function loadJson($file)
    {
        $json = file_get_contents($file);    
        
        $data = json_decode($json);
        
        if (strtolower($data->status) != 'ok')
            return false;

        $data = $data->data;
        
        $this->name = $data->name;
        if (isset($data->clan))
        {
            $this->clan_short = isset($data->clan->clan->abbreviation) ? $data->clan->clan->abbreviation : '';
            $this->clan_name = isset($data->clan->clan->name) ? $data->clan->clan->name : '';
            $this->clan_image = isset($data->clan->clan->emblems_urls->small) ? $data->clan->clan->emblems_urls->small : '';
        }
        
        $keys = array(
            'ratings' => array('integrated_rating','battles','battle_wins','xp','battle_avg_xp',
                          'ctf_points','dropped_ctf_points','damage_dealt','frags','spotted'),
            'summary' => array('losses','survived_battles'),
            'experience' => array('max_xp'),
            'battles' => array('hits_percents')
        );
        
        foreach($keys as $key => $stats)
        {
            foreach($stats as $stat)
            {
                if ($key == 'ratings')
                {
                    $position = $data->$key->$stat->place;
                    $value = $data->$key->$stat->value;
                } else {
                    $position = null;
                    $value = $data->$key->$stat;
                }
                
                $this->data[$stat] = new PlayerStat($stat, $value, $position);
            }
        }
        
        if (isset($this->data['battles']))
        {
            $battles = $this->data['battles']->value;
            if (isset($this->data['battle_wins']))
                $this->data['battle_wins']->percentage = round((($this->data['battle_wins']->value)/$battles)*100,0);
            if (isset($this->data['losses']))
                $this->data['losses']->percentage = round((($this->data['losses']->value)/$battles)*100,0);
            if (isset($this->data['survived_battles']))
                $this->data['survived_battles']->percentage = round((($this->data['survived_battles']->value)/$battles)*100,0);        
        }
        
        if (isset($this->data['hits_percents']))
        {
            $this->data['hits_percents']->percentage = $this->data['hits_percents']->value;
        }
                  
        foreach($data->vehicles as $tank)
        {
            $url = explode('/', $tank->image_url);
            $image = end($url);
            $this->tanks[] = new PlayerTank($tank->nation, $tank->level, $tank->localized_name, $image, $tank->battle_count, $tank->win_count, $tank->class);
        }
        
        
        $this->achievements = $data->achievements;

        return true;
    }
    
    public function loadHtml()
    {
        switch(strtolower($this->server))
        {
          case 'sea': $url = "http://worldoftanks-sea.com/community/accounts/{$this->id}/"; break;
          default: $url="http://worldoftanks.{$this->server}/community/accounts/{$this->$id}/"; break;
        }
        
        /** DOWNLOAD DATA **/
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $string = curl_exec($ch);
        curl_close($ch);
        
        /** GET LOGIN **/
        $login = $this->get_part($string,'<h1>','</h1>');
        
        /** WAS PROFIE FOUND? **/
        if ($string == '' || $login == '' || $login == ' PAGE NOT FOUND ')
        {
            return false;
        }
        else
        {
            $this->name = $login;
        
            //IF USER HAS CLAN
            $s = '<a class="b-link-clan"';
            if (strpos($string,$s)!=0)
            {
              $clan = substr($string,strpos($string,$s)+strlen($s),300);
              $clan = substr($clan,strpos($clan,'[')+1,strpos($clan,']')-strpos($clan,'[')-1);
              $this->clan_short = $clan;
            }
        
            /** 
             GET ITEMS
            **/
            
            //Global rating is located elsewhere, than the rest of items
            if ($server == 'ru')
              $items = array('Общий рейтинг');
            else
              $items = array('Global Rating');
            
            $keys = array('integrated_rating');
            
            $result = array();  
            $history = array();
            foreach($items as $key => $item)
            {
              //First, get the whole line
              $str = $this->get_part($string, '<td><span>'.$item.'</span></td>', '</tr>');
              //Eliminate value, we want only position
              $value = $this->get_part($str, '<td class="right value">','</td>');
              $str = str_replace('<td class="right value">'.$value.'</td>','',$str);
              $str = $this->get_part($str, '<td class="right value">','</td>');
              //Remove link, if it's present
              if (strpos($str, '>'))
                $str = $this->get_part($str,'>','<');
              //Remove spaces
              $str = str_replace(' ','',$str);
              $position = str_replace('&nbsp;',' ',$str);
              $position .= '.';
              $item = $preklad[$jazyk][1][$key];
              
              $this->data[] = new PlayerStat($keys[$key], $value, $position);
              
              /*if ($advanced[0] == 1)
                $result[] = array($item,$value);*/
            }
              
            //Now get rest of the items
            //Alias is alias used to get position
            if ($server == 'ru') {
                $items = array('Проведено боёв','Побед','Проигрышей','Выжил в битвах','Суммарный опыт','Средний опыт за бой','Максимальный опыт за бой',
                               'Очки захвата базы','Очки защиты базы','Нанесенные повреждения','Уничтожено','Обнаружено','Процент попадания');
                $aliases = array('Проведено боёв','Побед','Проигрышей','Выжил в битвах','Суммарный опыт','Средний опыт за бой','Максимальный опыт за бой',
                               'Захват базы','Защита базы','Нанесенные повреждения','Уничтожено врагов','Обнаружено врагов','Процент попадания');
            } else {
                $items = array('Battles Participated','Victories','Defeats','Battles Survived','Total Experience','Average Experience per Battle','Maximum Experience per Battle',
                               'Capture Points','Defense Points','Damage','Destroyed','Detected','Hit Ratio');
                $aliases = array('Battles Participated','Victories','Defeats','Battles Survived','Total Experience','Average Experience per Battle','Maximum Experience per Battle',
                                 'Capture Points','Defense Points','Damage','Targets Destroyed','Targets Detected','Hit Ratio');
            }
            $keys = array('battles','battle_wins','losses','survived_battles','xp','battle_avg_xp','max_xp',
                          'ctf_points','dropped_ctf_points','damage_dealt','frags','spotted','hits_percents');
            
            foreach($items as $key=>$item)
            {
                //Get line
                $str = $this->get_part($string,'<td class=""> '.$item.': </td>','</tr>');
            
                $str = $this->get_part($str,'<td class="td-number-nowidth">','</td>');
                
                //Replace spaces
                $str = str_replace('&nbsp;',' ',$str);
                //It has %, replace the original value with %
                if (strpos($str,'(')) {
                  $str = $this->get_part($str,'(',')');
                }
                //If item has position!
                if (strpos($string,'<td><span>'.$aliases[$key]) && $advanced_ext[$key] == '1')
                {
                  $str2 = $this->get_part($string,'<td><span>'.$aliases[$key],'</tr>');
          
                  $_str = $this->get_part($str2, '<td class="right value">', '</td>');
                  $str2 = str_replace('<td class="right value">'.$_str.'</td>','',$str2);
          
                  $str2 = str_replace(' ','',$str2);
                  $str2 = trim($this->get_part($str2, '<tdclass="rightvalue">', '</td>'));
                  if (strpos($str2, '>'))
                    $str2 = $this->get_part($str2,'>','<');
          
                  $str2 = str_replace('&nbsp;',' ',$str2);
                  $str2 .= '.';
              } else {
                  $str2 = '';
              }
              $this->data[] = new PlayerStat($keys[$key], trim($str), trim($str2));
            }
            
            /**
             GET TANKS
             **/ 
        
            //Get entire table
            $s = '<table  class="t-statistic">';
            $tanky = substr($string,strpos($string,$s)+strlen($s),strlen($string));
            $tanky = substr($tanky,0,strpos($tanky,'</table>'));
            
            //Split string on table rows
            $radky = explode('<tr>',$tanky);
            $this->tanks = array();
            foreach($radky as $str)
            {
                //Get tank name
                $name = trim(strip_tags($this->get_part($str,'<td class="value">','</td>')));
                
                //Get tank image
                $img = $this->get_part($str,'<img class="png" src="','"');
                $img = substr($img,1,strlen($img));
                while(strpos($img,'/')!=0)
                  $img = substr($img,strpos($img,'/')+1,strlen($img));
                
                //Get tank background  
                if (strpos($str,'js-usa td-armory-icon')!=0)
                  $nation = 'usa';
                if (strpos($str,'js-germany td-armory-icon')!=0)
                  $nation = 'germany';
                if (strpos($str,'js-ussr td-armory-icon')!=0)
                  $nation = 'ussr';
                if (strpos($str,'js-france td-armory-icon')!=0)
                  $nation = 'france';
                
                //Get rest of data
                $tier = trim(strip_tags($this->get_part($str,'<span class="level">','</span>')));  
                $battles = str_replace('&nbsp;', ' ', $this->get_part($str,'<td class="right value">','</td>'));  //Battles
                $str = $this->get_part($str,'<td class="right value">','</tr>');
                $wins = str_replace('&nbsp;', ' ', $this->get_part($str,'<td class="right value">','</td>')); //Wins
          
                $name = iconv('UTF-8', 'Latin2', $name); //Encode tank name to Latin2 (used by imagestring)
                
                if ($name!='')
                    $this->tanks[] = new PlayerTank($icon, $this->get_tier($tier), $name, $img, $battles, $wins);
            }
        }
        
        return true;
    }
    
    public function get_tier($str)
    {
        $str = trim($str);
        switch($str)
        {
            case 'X': return 10; break;
            case 'IX': return 9; break;
            case 'VIII': return 8; break;
            case 'VII': return 7; break;
            case 'VI': return 6; break;
            case 'V': return 5; break;
            case 'IV': return 4; break;
            case 'III': return 3; break;
            case 'II': return 2; break;
            case 'I': return 1; break;
        }
        return 0;
    }
    
    public function get_part($str,$from,$to)
    {
        $value = substr($str,strpos($str,$from)+strlen($from),300);
        $value = substr($value,0,strpos($value,$to));
        return $value;    
    }
    
    public function sort_tanks($key, $desc = true)
    {
        $this->tanks = sort_objects($this->tanks, $key, !$desc);
        return $this->tanks;            
    }
}
?>