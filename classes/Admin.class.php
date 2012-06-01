<?php
/**
 * Easy CMS
 *
 * @package    Admin
 * @author     SimpleShop.dk
 * @copyright  (c) 2010 SimpleShop.dk
 * @license    To use, copy, modify, and/or distribute this
 *             software for any purpose with or without fee is is not
 *             allowed without written consent from the author.
 */
class Admin
{
    private $config;
	private $db;
    private $purifier;
	
    function __construct($config, $db, $purifier)
    {
        $this->config = $config;
		$this->db = $db;
        $this->purifier = $purifier;
    }
    public function processor($type = '')
    {
        switch ($type)
        {
            case "add":
                $output = $this->page_form();
                break;
            case "save_page":
                $this->save_page();
                $output = '<h1>Side gemt.</h1>';
                break;
            case "delete":
                $this->delete_page();
                $output = '<h1>Side slettet.</h1>';
                break;
            case "config":
                $output = $this->config_form();
                break;
            case "save_config":
                $this->save_config();
                $output = '<h1>Konfiguration gemt.</h1>';
                break;
            default:
                $output = '';
        }
        return $output;
    }
    public function cleaninput($input)
    {
        $search = array(
			'@<script[^>]*?>.*?</script>@si', // Strip out javascript
            '@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
            '@<![\s\S]*?--[ \t\n\r]*>@' // Strip multi-line comments
		);
        $output = preg_replace($search, '', $input);
        return $output;
    }
    public function seo_url($data)
    {
        $patterns = array();
        $replacements = array();
        $patterns[0] = utf8_encode(' - ');
        $patterns[1] = utf8_encode('æ');
        $patterns[2] = utf8_encode('ø');
        $patterns[3] = utf8_encode('å');
        $patterns[4] = utf8_encode('Æ');
        $patterns[5] = utf8_encode('Ø');
        $patterns[6] = utf8_encode('Å');
        $patterns[7] = utf8_encode(',');
        $patterns[8] = utf8_encode('.');
        $patterns[9] = utf8_encode('%');
        $patterns[10] = utf8_encode('(');
        $patterns[11] = utf8_encode(')');
        $patterns[12] = utf8_encode('/');
        $patterns[13] = utf8_encode(' ');
        $patterns[14] = utf8_encode('!');
        $patterns[15] = utf8_encode('ö');
        $patterns[16] = utf8_encode('ä');
        $patterns[17] = utf8_encode('é');
        $patterns[18] = utf8_encode('´');
        $patterns[19] = utf8_encode("'");
        $replacements[0] = '-';
        $replacements[1] = 'ae';
        $replacements[2] = 'oe';
        $replacements[3] = 'aa';
        $replacements[4] = 'ae';
        $replacements[5] = 'oe';
        $replacements[6] = 'aa';
        $replacements[7] = '-';
        $replacements[8] = '';
        $replacements[9] = 'procent';
        $replacements[10] = '-';
        $replacements[11] = '-';
        $replacements[12] = '-';
        $replacements[13] = '-';
        $replacements[14] = '';
        $replacements[15] = 'oe';
        $replacements[16] = 'ae';
        $replacements[17] = 'e';
        $replacements[18] = '';
        $replacements[19] = '';
        $data = strtolower(str_ireplace($patterns, $replacements, $data));
		
        // Only allow slashes, dashes, and lowercase letters
		$data = preg_replace('/[^a-z0-9-\/]/', '-', strtolower($data));
        
        $data = strtolower(str_ireplace('quot','',$data));

		// Strip multiple dashes
		$data = preg_replace('/-{2,}/', '-', $data);

		// Trim an ending or starting dashes
		$data = trim($data, '-');
                        
        return $data;
    }
    public function list_actions()
    {
        $output = '';
        $output .= '<ul>';
        $output .= '<li><a href="/" target="_blank"><img src="'.$this->config['template_pages_path'].'images/buttons/magnifier.png" alt="Se website" title="Se website" /> Se website</a></li>';
        $output .= '<li><a href="admin.php?type=add"><img src="'.$this->config['template_pages_path'].'images/buttons/page_add.png" alt="Ny side" title="Ny side" /> Ny side</a></li>';
        $output .= '</ul>';
        return $output;
    }
    public function list_pages()
    {
        $output = '';
		
		$sql_lang = "SELECT language FROM pages GROUP BY language";
		$result_lang = $this->db->query($sql_lang);
		
		while ($row_lang = $result_lang->fetch_assoc())
		{
			$output .= '<h1>'.$this->config['languages'][$row_lang['language']].'</h1>';
			
			$sql = "SELECT * FROM pages WHERE language = $row_lang[language] ORDER BY sortorder";
			$result = $this->db->query($sql);
			
			$output .= '<ul>';
			while ($row = $result->fetch_assoc())
			{
				if ($row['page_id'] < 100)
				{
					$output .= '<li><a href="admin.php?type=add&amp;page='.$row['page_id'].'"><img src="'.$this->config['template_pages_path'].'images/buttons/page_edit.png" alt="Rediger side" title="Rediger side" /></a> '.$row['sortorder'].' '.$row['title'].'</li>';
				} else
				{
					$output .= '<li><a href="admin.php?type=add&amp;page='.$row['page_id'].'"><img src="'.$this->config['template_pages_path'].'images/buttons/page_edit.png" alt="Rediger side" title="Rediger side" /></a><a href="admin.php?type=delete&amp;page='.$row['page_id'].'" class="delete-page"><img src="'.$this->config['template_pages_path'].'images/buttons/delete.png" alt="Slet side" title="Slet side" /></a> '.$row['sortorder'].' '.$row['title'].'</li>';
				}
			}
			$output .= '</ul>';
		}
        return $output;
    }
    public function list_configuration()
    {
        $output = '';

		$sql = "SELECT * FROM configuration";
		$result = $this->db->query($sql);
        
        $output .= '<ul>';
        while ($row = $result->fetch_assoc())
        {
            $output .= '<li><a href="admin.php?type=config&amp;id='.$row['id'].'"><img src="'.$this->config['template_pages_path'].'images/buttons/page_edit.png" alt="Rediger" title="Rediger" /></a>'.$row['title'].'</li>';
        }
        $output .= '</ul>';
        return $output;
    }
    public function page_form()
    {
        if (isset($_REQUEST['page']))
        {
            $page_id = (int)$_REQUEST['page'];
            $output = '';

    		$sql = "SELECT * FROM pages WHERE page_id='$page_id'";
    		$result = $this->db->query($sql);
            $row = $result->fetch_assoc();
        }
        
        $output .= '<form action="admin.php?type=save_page" method="post">';
        $output .= '<fieldset id="page_form">';
        $output .= '<legend>'.$row['title'].'</legend>';
        $output .= '<button class="button_save" type="submit">Gem side</button>';
        $output .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/admin.php">Fortryd</a>';
        $output .= '<br /><br />';
        if($page_id < 100) 
        {
            $output .= '<input type="hidden" id="status" name="status" value="1" />';
        }
        $output .= '<ol>';
        
        if($page_id > 99 OR !isset($_REQUEST['page'])) 
        {
            $active = '';
            $inactive = '';
            if($row['status'] == 1 OR !isset($_REQUEST['page']))
            {
                $active = ' selected="selected"';
            }
            else
            {
                $inactive = ' selected="selected"';
            }
            
            $output .= '<li>';
            $output .= '<label for="status">Status</label>';
            $output .= '<select name="status" id="status">'."\n";
            $output .= '<option value="1"'.$active.'>Aktiv</option>'."\n";
            $output .= '<option value="0"'.$inactive.'>Inaktiv</option>'."\n";
            $output .= '</select>'."\n";
            $output .= '</li>';
        }
        
        $output .= '<li>';
        $output .= '<label for="page_parent">Overordnet side</label>';
        $output .= '<select name="page_parent" id="page_parent">'."\n";
        $output .= '<option value="0">Ingen</option>'."\n";

		$sql = "SELECT page_id, title FROM pages ORDER BY title";
		$parent = $this->db->query($sql);
        
        while($parent_row = $parent->fetch_assoc()) {
            if($row['page_parent'] == $parent_row['page_id']) {
                $selected = ' selected="selected"';
            } else {
                $selected = '';
            }
            $output .= '<option value="'.$parent_row['page_id'].'"'.$selected.'>'.$parent_row['title'].'</option>'."\n";
        }
        $output .= '</select>'."\n";
        $output .= '</li>';

        $output .= '<li>';
        $output .= '<label for="position">Position</label>';
        $output .= '<select name="position" id="position">'."\n";
        for ( $counter = 1; $counter <= $this->config['position']; $counter += 1) {
            if($counter == $row['position']) {
                $selected = ' selected="selected"';
            } else {
                $selected = '';
            }
            $output .= '<option value="'.$counter.'"'.$selected.'>'.$counter.'</option>'."\n";
        }
        $output .= '</select>'."\n";
        $output .= '</li>';

        $output .= '<li>';
        $output .= '<label for="language">Sprog</label>';
        $output .= '<select name="language" id="language">'."\n";
        
        foreach($this->config['languages'] AS $language_id => $language_name) {
            if($language_id == $row['language']) {
                $selected = ' selected="selected"';
            } else {
                $selected = '';
            }
            $output .= '<option value="'.$language_id.'"'.$selected.'>'.$language_name.'</option>'."\n";
        }
        $output .= '</select>'."\n";
        $output .= '</li>';

        $output .= '<li>';
        $output .= '<label for="title">Titel</label>';
        $output .= '<input class="input_text" type="text" id="title" name="title" value="'.$row['title'].'" />';
        $output .= '</li>';

        $output .= '<li>';
        $output .= '<label for="sortorder">Sortering</label>';
        $output .= '<input class="input_text" type="text" id="sortorder" name="sortorder" value="'.$row['sortorder'].'" />';
        $output .= '</li>';

        $output .= '<li>';
        $output .= '<label for="description">Beskrivelse</label>';
        $output .= '<textarea id="description" name="description" class="wysiwyg" cols="5" rows="5">'.$row['description'].'</textarea>';
        $output .= '</li>';

        $output .= '<li>';
        $output .= '<label for="seo_title">SEO titel</label>';
        $output .= '<input class="input_text" type="text" id="seo_title" name="seo_title" value="'.$row['seo_title'].'" />';
        $output .= '</li>';

        $output .= '<li>';
        $output .= '<label for="seo_description">SEO beskrivelse</label>';
        $output .= '<textarea id="seo_description" name="seo_description" cols="5" rows="5">'.$row['seo_description'].'</textarea>';
        $output .= '</li>';

        $output .= '<li>';
        $output .= '<label for="seo_keywords">SEO n&oslash;gleord</label>';
        $output .= '<textarea id="seo_keywords" name="seo_keywords" cols="5" rows="5">'.$row['seo_keywords'].'</textarea>';
        $output .= '</li>';

        $output .= '</ol>';
        $output .= '</fieldset>';
        $output .= '<input type="hidden" id="page_id" name="page_id" value="'.$row['page_id'].'" />';
        $output .= '<button class="button_save" type="submit">Gem side</button>';
        $output .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/admin.php">Fortryd</a>';
        $output .= '</form>';
        return $output;
    }
    public function save_page()
    {
        $page_id = (int)$_POST['page_id'];
        $status = (int)$_POST['status'];
        $page_parent = (int)$_POST['page_parent'];
        $position = (int)$_POST['position'];
        $language = (int)$_POST['language'];
        $sortorder = (int)$_POST['sortorder'];
        $title = trim($this->purifier->purify($_POST['title']));
        $description = trim($this->purifier->purify(htmlspecialchars_decode($_POST['description'])));
        $seo_title = trim($this->purifier->purify($_POST['seo_title']));
        $seo_description = trim($this->purifier->purify($_POST['seo_description']));
        $seo_keywords = trim($this->purifier->purify($_POST['seo_keywords']));
        if ($page_id == 1)
        {
            $seo_url = 'index';
        } 
        elseif ($page_id == 2)
        {
            $seo_url = '404';
        }
        else
        {
            $seo_url = $this->seo_url($title);
        }
        if ($page_id > 0)
        {
    		$sql = "UPDATE pages SET page_parent='$page_parent', language='$language', status='$status', position='$position', sortorder='$sortorder', title='$title', description='$description', seo_url='$seo_url', seo_title='$seo_title', seo_description='$seo_description', seo_keywords='$seo_keywords' WHERE page_id='$page_id'";
    		$result = $this->db->query($sql);
        } else
        {
    		$sql = "INSERT INTO pages VALUES ('','$page_parent','$language','$status','1','$position','$sortorder','','$seo_url','$seo_title','$seo_description','$seo_keywords','','$title','$description','')";
    		$result = $this->db->query($sql);
        }
        
        header('location: /admin.php');
        exit;
    }
    public function delete_page()
    {
        $page_id = (int)$_REQUEST['page'];
        
		$sql = "DELETE FROM pages WHERE page_id='$page_id'";
		$result = $this->db->query($sql);

        header('location: /admin.php');
        exit;
    }

    public function config_form()
    {
        $id = $_GET['id'];
        
		$sql = "SELECT * FROM configuration WHERE id='$id'";
		$result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        
        $output .= '<form action="admin.php?type=save_config" method="post">';
        $output .= '<fieldset id="config_form">';
        $output .= '<legend>'.$row['title'].'</legend>';
        $output .= '<button class="button_save" type="submit">Gem</button>';
        $output .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/admin.php">Fortryd</a>';
        $output .= '<br /><br />';
        $output .= '<ol>';
        
        if ($row['type'] == 'plain' OR $row['type'] == 'wysiwyg')
        {
            $output .= '<li>';
            $output .= '<label for="description">Indhold</label>';
            $output .= '<textarea id="description" name="content" class="'.$row['type'].'" cols="5" rows="5">'.$row['content'].'</textarea>';
            $output .= '</li>';
        }

        if ($row['type'] == 'image')
        {
            $output .= '<li>';
            $output .= '<label for="config_image">Billede</label>';
            $output .= '<input class="input_text" type="text" id="config_image" name="content" value="'.$row['content'].'" readonly="readonly" />';
            $output .= '</li>';
        }

        if ($row['type'] == 'form')
        {
            $output .= '<li>';
            $output .= '<label for="description">Formular</label>';
            $output .= '<textarea id="description" name="content" class="wysiwyg" cols="5" rows="5">'.$row['content'].'</textarea>';
            $output .= '</li>';
            $output .= '<li>';
            $output .= '<label for="extra1">Bekræftelse</label>';
            $output .= '<textarea id="extra1" name="extra1" class="wysiwyg" cols="5" rows="5">'.$row['extra1'].'</textarea>';
            $output .= '</li>';
        }

        $output .= '</ol>';
        $output .= '</fieldset>';
        $output .= '<input type="hidden" id="config_id" name="config_id" value="'.$row['id'].'" />';
        $output .= '<button class="button_save" type="submit">Gem</button>';
        $output .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/admin.php">Fortryd</a>';
        $output .= '</form>';
        return $output;
    }

    public function save_config()
    {
        $id = (int)$_POST['config_id'];
        
        foreach ($_POST AS $field => $value)
        {
            if ($field != 'config_id')
            {
                $field = $this->purifier->purify($field);
                
                $value = trim($value);
        
        		$sql = "UPDATE configuration SET $field='$value' WHERE id='$id'";
        		$result = $this->db->query($sql);
            }
        }
        
        header('location: /admin.php');
        exit;
    }
}