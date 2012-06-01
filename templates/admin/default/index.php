<?php $content = $admin->processor($_REQUEST['type']);
$actions = $admin->list_actions();
$list_pages = $admin->list_pages();
$list_config = $admin->list_configuration();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="da" xml:lang="da">
<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta name="robots" content="none" />
    <meta http-equiv="imagetoolbar" content="no" />
	<title>Administration</title>
	<meta name="Description" content="" />
	<meta name="Keywords" content="" />
	<link href="<?php echo $config['template_pages_path'];?>css/reset.css" media="screen" rel="Stylesheet" type="text/css" />
    <link href="<?php echo $config['template_pages_path'];?>css/styles.css" media="screen" rel="Stylesheet" type="text/css" />
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script language="javascript" type="text/javascript" src="/javascript/confirm_delete.js"></script>
    <script language="javascript" type="text/javascript" src="/javascript/tinybrowser.js"></script>
	<?php if($_REQUEST['type']=='add' OR $_REQUEST['type']=='config'){?>
	<script language="javascript" type="text/javascript" src="/modules/tiny_mce/tiny_mce.js"></script>
	<script language="javascript" type="text/javascript" src="/modules/tiny_mce/plugins/tinybrowser/tb_tinymce.js.php"></script>
    <script language="javascript" type="text/javascript" src="/modules/tiny_mce/plugins/tinybrowser/tb_standalone.js.php"></script>
	<script language="javascript" type="text/javascript">
	tinyMCE.init({
		mode : "specific_textareas",
		editor_selector: "wysiwyg",
		theme : "advanced",
		language : 'da',
        relative_urls : false,
        remove_script_host : false,
        <?php
        $num = 1;
        if ($handle = opendir($config['absolute_path'].'modules/tiny_mce/plugins/template/custom')) {
            echo 'template_templates : ['."\n";
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
					if ($num == 1) {
						echo '{'."\n";
					} else
					{
						echo ',{'."\n";
					}
            		echo 'title : "Skabelon '.$num.'",'."\n";
            		echo 'src : "/modules/tiny_mce/plugins/template/custom/'.$file.'",'."\n";
            		echo 'description : "Skabelon '.$num.'"'."\n";
					echo '}'."\n";
                    $num++;
                }
            }
            echo '],'."\n";
        }
        ?>
		file_browser_callback : "tinyBrowser",
		plugins : "tinyautosave,safari,table,save,advhr,advimage,advlink,iespell,inlinepopups,media,searchreplace,contextmenu,paste,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
		extended_valid_elements : "iframe[src|width|height|name|align]",
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,forecolor,backcolor,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,tablecontrols",
		theme_advanced_buttons2 : "cut,copy,pastetext,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,charmap,code,hr,visualaid,|,link,unlink,anchor,|,image,media,|,template,|,tinyautosave",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_resizing : false
	});
	</script>
	<?php }?>
</head>
<body>
<div id="wrapper">
	<div id="header">
		<img src="<?php echo $config['template_pages_path'];?>images/logo.jpg" alt="" title="" />
	</div>
	<hr />
    <?php if (!isset($_GET['type'])) { ?>
	<div id="navigation">
		<?php echo $actions;?>
		<?php echo $list_pages;?>
        <?php echo $list_config;?>
	</div>
    <?php } ?>
	<div id="content">
		<?php echo $content;?>
	</div>
	<div style="clear:both;"></div>
	<hr />
	<div id="copyright">
		&copy; 2010 <a href="http://www.simpleshop.dk" target="_blank">SimpleShop.dk</a> - v0.6
	</div>
</div>
</body>
</html>