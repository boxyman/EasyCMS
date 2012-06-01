<?php
/**
 * Easy CMS
 *
 * @package    Config
 * @author     SimpleShop.dk
 * @copyright  (c) 2010 SimpleShop.dk
 * @license    To use, copy, modify, and/or distribute this
 *             software for any purpose with or without fee is is not
 *             allowed without written consent from the author.
 */
 
// Configuration 
$config = array();

// MySQL credentials
$config['dbtype'] = 'mysql';
$config['dbhost'] = 'localhost';
$config['dbname'] = 'easycms';
$config['dbuser'] = 'easycms';
$config['dbpassword'] = 'easycms';

// Website paths
$config['absolute_path'] = '/var/www/easycms.simpleshop.dk/website/';
$config['website_root'] = 'http://www.easycms.simpleshop.dk/';

// Website settings
$config['position'] = 1;
$config['admin_template'] = 'default';
$config['website_template'] = 'nitrogenate';
$config['website_directory'] = '';
$config['page_extension'] = '.html';

// Mail settings
$config['mail_type'] = 'MAIL'; // Choices: MAIL, SMTP
$config['mail_from'] = '';
$config['mail_smtp'] = 'smtp.gmail.com';
$config['mail_port'] = 465;
$config['mail_security'] = 'ssl';
$config['mail_subject'] = 'Mail fra '.$_SERVER['HTTP_HOST'];

// Languages
$config['language'] = 0;
$config['languages'][0] = 'Dansk';

// Language directories
$config['directory'][0] = '';

// Flags
$config['flag'][0] = 'denmark.gif';

// Misc
$config['rot'] = 8;