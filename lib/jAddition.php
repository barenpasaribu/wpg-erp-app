<?php



function createHours($id = 'hours_select', $selected = null)
{
    $r = range(0, 23);
    $selected = (null === $selected ? date('h') : $selected);
    $select = '<select name="'.$id.'" id="'.$id."\">\n";
    foreach ($r as $hour) {
        if (1 === strlen($hour)) {
            $hour = '0'.$hour;
        }

        $select .= '<option value="'.$hour.'"';
        $select .= ($hour === $selected ? ' selected="selected"' : '');
        $select .= '>'.$hour."</option>\n";
    }
    $select .= '</select>';

    return $select;
}

function createMinutes($id = 'minute_select', $selected = null)
{
    $minutes = range(0, 59);
    $selected = (in_array($selected, $minutes, true) ? $selected : 0);
    $select = '<select name="'.$id.'" id="'.$id."\">\n";
    foreach ($minutes as $min) {
        if (1 === strlen($min)) {
            $min = '0'.$min;
        }

        $select .= '<option value="'.$min.'"';
        $select .= ($min === $selected ? ' selected="selected"' : '');
        $select .= '>'.str_pad($min, 2, '0')."</option>\n";
    }
    $select .= '</select>';

    return $select;
}

?>