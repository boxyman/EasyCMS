<?php
/**
 * Easy CMS
 *
 * @package    Frontend boostrap
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

// Instantiate classes
$db = new Database($config);
$page = new Pages($config, $db);

// Get page
$webpage = $page->show_page($_REQUEST['page']);

// Get page configuration
$configuration = $page->configuration();

// Get paths
$config['template_pages_path'] = $config['website_root'].'templates/website/'.$webpage['template'].'/';
require_once(''.$config['absolute_path'].'templates/website/'.$webpage['template'].'/index.php');?>