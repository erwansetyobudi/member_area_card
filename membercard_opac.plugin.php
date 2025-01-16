<?php
/**
 * Plugin Name: Digital Member Card
 * Plugin URI: https://github.com/erwansetyobudi/member_area_card
 * Description: This plugin show digital card ini member area. Modified form plugin Membercard OPAC Drajat Hasan
 * Version: 1.0.0
 * Author: Erwan Setyo Budi
 * 

 */
use SLiMS\Plugins;
$plugins = Plugins::getInstance();

if (str_replace(['.','v'], '', SENAYAN_VERSION_TAG) >= '961') {
    $plugins->registerMenu('opac', 'member', __DIR__ . '/pages/member.inc.php');
    $plugins->registerMenu('opac', 'member_card', __DIR__ . '/pages/member_card.inc.php');
}