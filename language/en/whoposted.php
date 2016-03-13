<?php
/**
*
* Who Posted In This Topic [English]
*
* @package language
* @version 1.0.0
* @copyright (c) 2016 crizzo.de
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'WHOPOSTED'	=> 'Who Posted',
));
