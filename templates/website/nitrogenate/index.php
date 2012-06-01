<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="da" xml:lang="da">
<head>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
    <meta http-equiv="imagetoolbar" content="no" />
	<title><?php echo $webpage['seo_title'];?></title>
	<meta name="Description" content="<?php echo $webpage['seo_description'];?>" />
	<meta name="Keywords" content="<?php echo $webpage['seo_keywords'];?>" />
	<link href="<?php echo $config['template_pages_path'];?>css/reset.css" media="screen" rel="Stylesheet" type="text/css" />
    <link href="<?php echo $config['template_pages_path'];?>css/styles.css" media="screen" rel="Stylesheet" type="text/css" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
    <?php
    if(trim($configuration['ga_nossl']) != '') {
        echo $configuration['ga_nossl']."\n";
    }
    ?>
</head>
<body>
<div id="header">
    <div id="logo">
	   <a href="/"><?php $page->config_block('logo');?></a>
    </div>
	<div id="navigation">
		<?php echo $page->show_navigation();?>
		<div style="clear:both"></div>
	</div>
</div>
<div style="clear:both"></div>
<div id="wrapper">
	<div id="content">
		<?php echo $webpage['description'];?>
	</div>
	<div id="sidebar">
		<?php $page->config_block('sidebar');?>
	</div>
</div>
<div style="clear:both"></div>
<div id="footer">
	<?php echo $configuration['footer'];?>
</div>
</body>
</html>