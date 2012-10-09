<?php
function sort_objects($objects, $key, $asc = true)
{
    for($i = count($objects) - 2; $i >= 0; $i-=1)
    {
        for($k = $i; $k < count($objects) - 1 && $objects[$k]->$key < $objects[$k+1]->$key; $k+=1)
        {
            $t = $objects[$k + 1];
            $objects[$k + 1] = $objects[$k];
            $objects[$k] = $t;
        }
    } 
    if ($asc)
    {
        $objects = array_reverse($objects);
    }
    
    return $objects;
}
?>