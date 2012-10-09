<?php
class Signature
{
    public $width;
    public $height;
    public $size;
    
    public $player;
    public $keys;
    
    public $flag;
    
    public $clan_name;
    public $clan_image;
    
    public $tanksize;
    public $tankorder;
    public $tankname;
    public $tankwins;
    public $tankbattles;
    public $tankpercentage;

    public $server_label;
    public $effeciency_rating;

    public $small_positions;

    public $font_size;
    
    public $image;
    
    public $background;
    
    public function __construct($size)
    {
        $this->size = (int)$size;
        switch($size)
        {
            case 0: $this->width = 400; $this->height = 100; break;
            case 1: $this->width = 512; $this->height = 128; break;
        }
    }
    
    public function render()
    {
        global $translation;
    
        //Create blank image
        $img = imagecreatefrompng(WOT_FOLDER_BACKGROUNDS . '/empty_' . $this->size . '.png');
        imagealphablending($img, true);
        imagesavealpha($img, true);
        
        //Allocate colors
        $c_white = imagecolorallocate($img, 255, 255, 255);
        $c_gray = imagecolorallocate($img, 200, 200, 200);
        $c_black = imagecolorallocate($img, 0, 0, 0);
        $c_purple = imagecolorallocate($img, 254, 254, 254);
        
        if ($this->background != 0)
        {
            //Load background        
            $bg = imagecreatefrompng(WOT_FOLDER_BACKGROUNDS . '/wot' . $this->background . '.png');
            //This image is used to dark image
            $tm = imagecreatefrompng(WOT_FOLDER_BACKGROUNDS . '/black.png');
            
            //Copy background and dark image to the signature
            imagecopyresampled($img, $bg, 0, 0, 0, 0, $this->width, $this->height, 512, 128);
            imagecopyresized($img, $tm, 0, 0, 0, 0, $this->width, $this->height, 512, 128);
        }
        
        //Get font properties, by signature size
        $font = WOT_FOLDER_FONTS . '/CALIBRI.TTF';
         
        switch($this->size)
        {
          case 0: $titlesize = 14; $fontsize = 8; break;
          case 1: $titlesize = 16; $fontsize = 10; break;
        }
        
        //or use custom size
        if ($this->font_size != 0) {
          $fontsize = $this->font_size;
        }
        
        //Get server name
        $text = strtoupper($this->player->server) . ' server';
        
        //Draw server name
        if ($this->server_label)
        {
            $box = imagettfbbox($fontsize, 0, $font, $text);
            $this->imagettftextoutline($img, $fontsize, 0, $this->width - $box[2] - 8, 4 - $box[7], $c_white, $font, $text, 1, $c_black);
        }
        if ($this->effeciency_rating)
        {
            $server_box = @$box;
            if ($this->effeciency_rating == 1)
                $text = $this->player->effeciency_rating;
            else
                $text = $this->player->effeciency_rating_new;
            $desc = $this->getErDesc($text);
            $color = imagecolorallocate($img, $desc['color'][0], $desc['color'][1], $desc['color'][2]);
            $box = imagettfbbox($fontsize, 0, $font, $text);
            $this->imagettftextoutline($img, $fontsize, 0, $this->width - $box[2] - 8 - ($server_box[2] + 8)*($this->server_label), 4 - $box[7], $color, $font, $text, 1, $c_black);    
        }
        
        $fontB = WOT_FOLDER_FONTS . '/ARIALBD.TTF';
        $box = imagettfbbox($titlesize, 0, $fontB, $this->player->name);
        $xx = 8;
        
        //Draw clan icon
        if ($this->clan_image && WOT_ALLOW_CLAN_IMAGE && $this->player->clan_image)
        {
            $filename = WOT_FOLDER_CACHE_CLANS . '/clan_' . $this->player->clan_short . '.png';
            if (!file_exists($filename))
            {
                $url = $this->player->server_url . $this->player->clan_image;
                file_put_contents($filename, file_get_contents($url));
            }
            $icon = imagecreatefrompng($filename);
            imagecopyresampled($img, $icon, $xx, $yy, 0, 0, 24, 24, 24, 24);
            $xx += 24;
        }
        
        //Draw flag
        if ($this->flag != '' && file_exists(WOT_FOLDER_FLAGS . '/' . $this->flag . '.gif'))
        {
          $flagi = imagecreatefromgif(WOT_FOLDER_FLAGS . '/' . $this->flag . '.gif');
          imagecopy($img, $flagi, $xx, 6 + $this->size, 0, 0, imagesx($flagi), imagesy($flagi));
          $xx += 20;
        }
        
        //Draw nick
        $text = $this->player->name;
        if ($this->player->clan_short != '' && $this->clan_name)
            $text = '[' . $this->player->clan_short . '] ' . $text;
        
        $this->imagettftextoutline($img, $titlesize, 0, $xx, 4 - $box[7], $c_white, $fontB, $text, 1, $c_black);
        
        //TH is height of title
        $th = -$box[7];
        
        switch($this->tanksize)
        {
            case 0:
              $tankx = 10;
              $tankwidth = 55;
              $tankheight = 31;
              $tanksrcwidth = 55;
              $tanksrcheight = 31;
              $tanksfolder = WOT_FOLDER_SMALL;
            break;
            case 1:
              $tankx = 19;
              $tankwidth = 86;
              $tankheight = 66;
              $tanksrcwidth = 130;
              $tanksrcheight = 100; 
              $tanksfolder = WOT_FOLDER_BIG;
            break;
            case 3:
              $tankx = 0;
              $tankwidth = 84;
              $tankheight = 24;
              $tanksrcwidth = 84;
              $tanksrcheight = 24; 
              $tanksfolder = WOT_FOLDER_CONTUR;
            break;
        }
        
        $lines = array();
        foreach($this->keys as $key=>$values)
        {
            $value = '';
            if ($values[0])
                $value = $this->player->data[$key]->value;
            if ($values[1] && $this->player->data[$key]->position != 0 && (!$this->small_positions || $this->player->data[$key]->position < 1000))
                if ($value == '')
                    $value = $this->player->data[$key]->position . '.';
                else
                    $value .= '('.($this->player->data[$key]->position).'.)';
            if ($values[2] && $this->player->data[$key]->percentage != 0)
                if ($value == '')
                    $value = $this->player->data[$key]->percentage . '%';
                else
                    $value .= '('.($this->player->data[$key]->percentage).'%)';
            
            if ($value != '')
                $lines[] = array($translation[$key], $value);    
        }
        
        
        //Now, find out, if the lines can fit on more cols, to leave more space for tanks
        $max_width = 0;
        $xx = 8;
        $yy = 4 - $box[7] + 2;
        $sy = $yy;
        $sx = $xx;
    
        $fail = false;
        foreach($lines as $line)
        {
          //Should we move to next col?
          if ($yy - $box[7] + 4 > $this->height - $tankheight - 4)
          {
            $xx += $max_width + 4; //Add xx
            $yy = 4 + $th + 2; //Reset YY
            $max_width = 0;    //Reset max width of col
          }
          
          $str = "$line[0]: $line[1]"; //Get str
          $box = imagettfbbox($fontsize, 0, $font, $str); //Get str size
    
          $w = $box[2]; //Line width
          if ($w > $max_width)
          {
            $max_width = $w;
            if ($xx + $max_width > $this->width-4) //If is max width over signature size, we cant fit lines to more cols...
            {
              $fail = true;
              break;
            }
          }
          $yy += -$box[7] + 1 + 1 * $this->size; //Move to next line
        }
        
        if ($fail) //If the check failed
          $maxheight = $this->height;
        else
          $maxheight = $this->height - $tankheight - 4; 
        
        //Now draw value names
        
        $xx = $sx;
        $yy = $sy;
        $col = 0;
        $max_width = array();
        $max_width[$col] = 0;  
        
        foreach($lines as $key=>$line)
        {
          if ($yy - $box[7] + 4 > $maxheight)
          {
            $xx += $max_width[$col] + 5;
            $yy = 4 + $th + 2;
            $col+=1;
            $max_width[$col] = 0;
          }
          $h[$key] = 0;
          $str = "$line[0]: ";
          $box = imagettfbbox($fontsize, 0, $font, $str);
          $this->imagettftextoutline($img, $fontsize, 0, $xx, $yy - $box[7], $c_gray, $font, $str, 1, $c_black);
          $h[$key] = -$box[7];
          $str = "$line[0]: $line[1]";
          $box = imagettfbbox($fontsize, 0, $font, $str);
          $w = $box[2];
          
          if ($w > $max_width[$col])
            $max_width[$col] = $w;
          $yy += $h[$key] + 1 + 1 * $this->size;
        }
        
        //Now draw values
        
        $xx = $sx;
        $yy = $sy;
        $col = 0;
        $lasth = 0;
        
        foreach($lines as $key=>$line)
        {
          if ($yy + $lasth + 4 > $maxheight)
          {
            $xx += $max_width[$col] + 5;
            $col += 1;
            $yy = 4 + $th + 2;
          }
    
          $str = $line[1];
          $box = imagettfbbox($fontsize, 0, $font, $str);
          $this->imagettftextoutline($img, $fontsize, 0, $xx + $max_width[$col] - $box[2], $yy + $h[$key], $c_white, $font, $str, 1, $c_black);
          
          $yy += $h[$key] + 1 + 1 * $this->size;
          $lasth = $h[$key];
        }
        $yy -= $lasth + 1 + 1 * $this->size;
    
        /**
          Draw tanks
        **/
    
        if ($this->tanksize != 2)
        {
          //Allocate color of background
          $c_awhite = imagecolorallocatealpha($img, 0, 0, 0, 80);
          
          //Can tanks fit to last col, or move to next col
          $ly = $yy;
          $yy = $this->height - $tankheight - 4;
          if ($ly > $yy)                            
            $xx += $max_width[count($max_width)-1];
          if ($fail)
            $minx = $xx;
          else
            $minx = 8;
            
          //Calculate starting xx
          $xx = $this->width - 4 - floor(($this->width - $minx - 4) / ($tankwidth + 4)) * ($tankwidth + 4);
          $first = true;
          
          //Order tanks
          $key = 'battles';
          switch($this->tankorder)
          {
              case 1: $key = 'wins'; break;
              case 2: $key = 'tier'; break;
              case 3: $key = 'percentage'; break;
          }
          $tanks = $this->player->sort_tanks($key);
          
          foreach($tanks as $tank)
          {
            //Does this tank fit to siganture?
            if ($xx + $tankwidth + 4 < $this->width)
            {
              //Draw info for first tank
              if ($first)
              {
                $tank->battles = 'B:' . $tank->battles;
                $tank->wins = 'W:' . $tank->wins;
                $tank->percentage = 'W:' . $tank->percentage;
                $first = false;
              }
            
              //Load tank image
              $tk = imagecreatefrompng($tanksfolder . '/' . $tank->image);
              //Draw tank background
              imagefilledrectangle($img, $xx, $yy, $xx+$tankwidth-1, $yy+$tankheight-1, $c_awhite);
              if ($this->tanksize == 0)
              {
                //Draw special tank background
                if ($tank->nation != '')
                {
                  $bg = imagecreatefrompng(WOT_FOLDER_SMALL . '/' . $tank->nation . '.png');
                  imagecopy($img, $bg, $xx, $yy, 0, 0, $tankwidth, 31);
                }
              }
              
              $tscr = $tanksrcwidth;
              $tw = $tankwidth;
              $diff = 0;
              if ($this->tanksize == 3)
              {
                  $real_width = imagesx($tk);
                  $diff = ($tankwidth - $real_width)/2;
                  $tscr = $real_width;
                  $tw = $real_width;  
              } 

              //Draw tank image
              imagecopyresampled($img, $tk, $xx + $diff, $yy, $tankx, 0, $tw, $tankheight, $tscr, $tanksrcheight);
              
              //Draw tank name
              if ($this->tankname == 1)
              {
                $name = $tank->name;
                if (strlen($name) > floor($tankwidth/imagefontwidth(1)))
                {
                    $name = substr($name,0,floor($tankwidth/imagefontwidth(1)));
                }
                imagestring($img, 1, $xx, $yy, $name, $c_white);
              }
              //Draw tank battles/wins count
              if ($this->tankbattles == 1)
              {
                  imagestring($img, 1, $xx, $yy + $tankheight - imagefontheight(1), $tank->battles, $c_white);
              }
              if ($this->tankwins == 1)
              {  
                  imagestring($img, 1, $xx + $tankwidth - strlen($tank->wins) * imagefontwidth(1), $yy + $tankheight - imagefontheight(1), $tank->wins, $c_white);
              } else if ($this->tankpercentage) {
                  imagestring($img, 1, $xx + $tankwidth - strlen($tank->percentage . '%') * imagefontwidth(1), $yy + $tankheight - imagefontheight(1), $tank->percentage . '%', $c_white);     
              }
              
              //Move to next tank
              $xx += $tankwidth + 4;
            } else break;
          }
        }
        
        return $img;
    }
    
    private function getErDesc($value)
    {
        $ret = array('desc' => 'Bad', 'color' => array(0xDD,0xDD,00));
      	if ($value >= 600) {
        		$ret['desc'] = "Below average";
        		$ret['color'] = array(0xDD,0x33,0x33);
      	}
      	if ($value >= 900) {
        		$ret['desc'] = "Average";
        		$ret['color'] = array(0xFF,0xCC,0x33);
      	}
      	if ($value >= 1200) {
        		$ret['desc'] = "Good";
        		$ret['color'] = array(0x99,0xFF,0x33);
      	}
      	if ($value >= 1500) {
        		$ret['desc'] = "Great";
        		$ret['color'] = array(0x33,0xFF,0x33);
      	}
      	if ($value >= 1800) {
        		$ret['desc'] = "Epic";
        		$ret['color'] = array(0xCC,0x66,0xCC);
      	}
        return $ret;
    }
    
    private function imagettftextoutline($image,$size,$angle,$x,$y,$color,$fontfile,$text,$outlinewidth = 1,$outlinecolor = 0)
    {
      	imagettftext($image, $size, $angle, $x - $outlinewidth,$y - $outlinewidth, $outlinecolor, $fontfile, $text);
      	imagettftext($image, $size, $angle, $x - $outlinewidth,$y + $outlinewidth, $outlinecolor, $fontfile, $text);
      	imagettftext($image, $size, $angle, $x + $outlinewidth,$y - $outlinewidth, $outlinecolor, $fontfile, $text);
      	imagettftext($image, $size, $angle, $x + $outlinewidth,$y + $outlinewidth, $outlinecolor, $fontfile, $text);
      	imagettftext($image, $size, $angle, $x - $outlinewidth, $y,$outlinecolor, $fontfile, $text);
      	imagettftext($image, $size, $angle, $x + $outlinewidth, $y,$outlinecolor, $fontfile, $text);
      	imagettftext($image, $size, $angle, $x, $y - $outlinewidth,$outlinecolor, $fontfile, $text);
      	imagettftext($image, $size, $angle, $x, $y + $outlinewidth,$outlinecolor, $fontfile, $text);
      	imagettftext($image, $size, $angle, $x, $y, $color, $fontfile,$text);
    }
}
?>