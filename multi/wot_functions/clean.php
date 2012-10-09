<?php
function clean($value)
{
    if (is_array($value))
    {
        foreach($value as &$v)
            $v = clean($v);
        return $value;
    } else {
        return str_replace("'",'',htmlspecialchars($value));
    }
}
?>