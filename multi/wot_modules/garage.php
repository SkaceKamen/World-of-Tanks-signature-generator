<?php
class Garage
{
    public $width;
    public $height;
    public $size;
    
    public $player;
    
    public $flag;
    
    public $tanksize;
    public $tankorder;
    public $tankname;
    public $tankwins;
    public $tankbattles;
    public $tankpercentage;
    
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
    
        $img = imagecreatetruecolor($this->width, $this->height);
        
        imagealphablending($img, true);
        imagesavealpha($img, true);
      
        $c_transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefill($img, 0, 0, $c_transparent);
        
        $c_white = imagecolorallocate($img, 255, 255, 255);
        $c_gray = imagecolorallocate($img, 150, 150, 150);
        $c_black = imagecolorallocate($img, 0, 0, 0);
        $c_purple = imagecolorallocate($img, 254, 254, 254);
        
        $xx = 2;
        $yy = 2;
        $font = WOT_FOLDER_FONTS . '/ARIALBD.TTF';
        $fontsize = 10;
        
        if ($this->flag != '' && file_exists(WOT_FOLDER_FLAGS . '/' . $this->flag . '.gif'))
        {
          $flagi = imagecreatefromgif(WOT_FOLDER_FLAGS . '/' . $this->flag . '.gif');
          imagecopy($img, $flagi, $xx, 6 + $this->size, 0, 0, imagesx($flagi), imagesy($flagi));
          $xx += 20;
        }
        
        $name = $this->player->name;
        if ($this->player->clan_short != '')
            $name = '[' . $this->player->clan_short . '] ' . $name;
        
        $box = imagettfbbox($fontsize, 0, $font, $name);
        $this->imagettftextoutline($img, $fontsize, 0, $xx + 2, $yy - $box[7], $c_white, $font, $name, 1, $c_gray);
        
        $text = $translation['garage'];
        $box = imagettfbbox($fontsize, 0, $font, $text);
        $this->imagettftextoutline($img, $fontsize, 0, $this->width - 6 - $box[2], $yy - $box[7], $c_white, $font, $text, 1, $c_gray);
        
        $yy -= $box[7] - 4;
        
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
            case 1: case 3:
              $tankx = 19;
              $tankwidth = 86;
              $tankheight = 66;
              $tanksrcwidth = 130;
              $tanksrcheight = 100; 
              $tanksfolder = WOT_FOLDER_BIG;
            break;
            case 2:
              $tankx = 0;
              $tankwidth = 84;
              $tankheight = 24;
              $tanksrcwidth = 84;
              $tanksrcheight = 24; 
              $tanksfolder = WOT_FOLDER_CONTUR;
            break;
        }
        
        $c_awhite = imagecolorallocatealpha($img, 120, 120, 120, 60);
        
        $key = 'battles';
        switch($this->tankorder)
        {
            case 1: $key = 'wins'; break;
            case 2: $key = 'tier'; break;
            case 3: $key = 'percentage'; break;
        }
        $tanks = $this->player->sort_tanks($key);
        
        $tanks_space = floor(($this->width - 4) / ($tankwidth + 2));
        $empty_space = $this->width - $tanks_space * ($tankwidth + 2);
        
        $xx = 2 + $empty_space / 2;
        $sx = $xx;
        $yy += imagefontheight(1);
        $first = true;
        
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
              if ($this->tanksize == 2)
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
                imagestring($img, 1, $xx + 1, $yy, $name, $c_white);
              }
              //Draw tank battles/wins count
              if ($this->tankbattles == 1)
              {
                  imagestring($img, 1, $xx + 1, $yy + $tankheight - imagefontheight(1), $tank->battles, $c_white);
              }
              if ($this->tankwins == 1)
              {  
                  imagestring($img, 1, $xx + $tankwidth - strlen($tank->wins) * imagefontwidth(1) - 1, $yy + $tankheight - imagefontheight(1), $tank->wins, $c_white);
              } else if ($this->tankpercentage) {
                  imagestring($img, 1, $xx + $tankwidth - strlen($tank->percentage . '%') * imagefontwidth(1) - 1, $yy + $tankheight - imagefontheight(1), $tank->percentage . '%', $c_white);     
              }
              
              //Move to next tank
              $xx += $tankwidth + 4;
              if ($tanksfolder != WOT_FOLDER_SMALL && $this->tanksize == 3) {
                  $sx = $xx;
                  $tankx = 10;
                  $tankwidth = 55;
                  $tankheight = 31;
                  $tanksrcwidth = 55;
                  $tanksrcheight = 31;
                  $tanksfolder = WOT_FOLDER_SMALL;
              }
          } else {
            //if ($this->tanksize == 3 || $this->tanksize == 2) {
              $yy += $tankheight + 2;
              if ($yy > $this->height - ($tankheight + 2))
                break;
              $xx = $sx;
            //} else break;
          }
        }
        
        return $img;
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