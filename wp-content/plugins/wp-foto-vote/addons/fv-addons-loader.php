<?php

defined('ABSPATH') or die("No script kiddies please!");

/**
 * Load all default addons and extensions
*/

function fv_default_addons_load()
{
    include 'countdown-default/addon__countdown-default.php';
    include 'final-countdown/addon__final-countdown.php';
    include 'form-simple-rounded/addon__form-simple-rounded.php';
    //include 'agree-rules/addon__agree_rules.php';
    include 'gallery/fv_addon__gallery.php';
}
