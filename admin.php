<?php
/**
 * Easy CMS
 *
 * @package    Backend boostrap
 * @author     SimpleShop.dk
 * @copyright  (c) 2010 SimpleShop.dk
 * @license    To use, copy, modify, and/or distribute this
 *             software for any purpose with or without fee is is not
 *             allowed without written consent from the author.
 */

// Get configuration
require_once ('config.php');

// Autoload classes
function __autoload($class_name)
{
    if(file_exists($config['absolute_path'].'classes/' . $class_name . '.class.php'))
	{
    	require_once (''.$config['absolute_path'].'classes/' . $class_name . '.class.php');
    } else 
	{
    	echo 'Valid absolute path missing in config: '.$_SERVER["DOCUMENT_ROOT"];
    }
}

// HTML purifier
require_once $config['absolute_path'].'modules/htmlpurifier-4.2.0/library/HTMLPurifier.auto.php';
$purifier = new HTMLPurifier();

// Setup enviroment
$db = new Database($config);
$config['template_pages_path'] = $config['website_root'].'templates/admin/'.$config['admin_template'].'/';
$admin = new Admin($config, $db, $purifier);
require_once(''.$config['absolute_path'].'templates/admin/'.$config['admin_template'].'/index.php');?>