<?php
/**
 * Created by PhpStorm.
 * User: OLADEJI BUSARI
 * Date: 5/17/2018
 * Time: 8:27 AM
 */

 function odds_prob($odds)
{

    // return $odds;

    return number_format((1 / ($odds==0?1:$odds)) * 100,2);
}


?>
