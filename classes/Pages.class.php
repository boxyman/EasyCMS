<?php
/**
 * Easy CMS
 *
 * @package    Pages
 * @author     SimpleShop.dk
 * @copyright  (c) 2010 SimpleShop.dk
 * @license    To use, copy, modify, and/or distribute this
 *             software for any purpose with or without fee is is not
 *             allowed without written consent from the author.
 */
class Pages
{
    private $config;
	private $db;
	
    function __construct($config, $db)
    {
        $this->config = $config;
		$this->db = $db;
    }
    
    // Page
    public function show_page($page = '')
    {
        if ($page == '')
        {
            $page = 'index';
        }
        
		$language = (int)$this->config['language'];
        
		$sql = "SELECT * FROM pages WHERE seo_url='$page' AND status='1' AND language='$language'";
		$result = $this->db->query($sql);
        $num = $this->db->num_rows($result);
        $row = $result->fetch_assoc();

        if($num == 0)
        {
    		$sql = "SELECT * FROM pages WHERE page_id='2' AND language='$language'";
    		$result = $this->db->query($sql);
            $row = $result->fetch_assoc();
            header('HTTP/1.1 404 Not Found');
        }
        
        $placeholders = array();
        
        preg_match_all("~##([^#]+)##~", $row['description'], $placeholders);
        
        foreach ($placeholders[0] AS $placeholder)
        {
            $data = explode(':', substr($placeholder, 2, -2));
            
            if ($data[0] == 'FORM')
            {
                $form = $this->form_creator(strtolower($data[1]));
                $row['description'] = str_ireplace('<p>'.$placeholder.'</p>', $form['content'], $row['description']);
                $row['description'] = str_ireplace($placeholder, $form['content'], $row['description']);
            }
        }
        
        if ($row['template'] == '')
        {
            $row['template'] = $this->config['website_template'];
        }
        
        if ($_POST)
        {
            $this->form_processor($_POST);
            $row['description'] = $form['extra1'];
        }
        
		return $row;
    }
    
    // Navigation bar
    public function show_navigation($position = 1)
    {
        $output = '';
		$position = (int)$position;
        $language = (int)$this->config['language'];
        
		$sql = "SELECT title,seo_url FROM pages WHERE position='$position' AND status='1' AND navigation_visible='1' AND language='$language' ORDER BY sortorder";
		$result = $this->db->query($sql);

        $output .= '<ul>';
        while ($row = $result->fetch_assoc())
        {
            if ($row['seo_url'] == 'index')
            {
                $output .= '<li><a href="'.$this->config['website_directory'].'/">'.$row['title'].'</a></li>';
            }
            else
            {
                $output .= '<li><a href="'.$this->config['website_directory'].'/'.$row['seo_url'].$this->config['page_extension'].'">'.$row['title'].'</a></li>';
            }
        }
        $output .= '</ul>';

        return $output;
    }

    // Configuration
    public function configuration()
    {
        $output = array();
        
		$sql = "SELECT * FROM configuration";
		$result = $this->db->query($sql);
        
        while ($row = $result->fetch_assoc())
        {
            $output[$row['label']] = $row['content'];
        }

        return $output;
    }

    // Get a content block from configuration
    public function config_block($label)
    {
		$sql = "SELECT type, content FROM configuration WHERE label='$label'";
		$result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        if($row['type'] == 'image')
        {
            $row['content'] = '<img src="'.$row['content'].'" alt="" title="" />';
        }
        echo $row['content'];
    }

    // Create a form
    public function form_creator($label)
    {
		$sql = "SELECT content, extra1 FROM configuration WHERE label='$label'";
		$result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        
        // Clear <p></p> surrounding <form> tag
        preg_match_all("~<p>##OPEN([^#]+)##</p>~", $row['content'], $data);
        $string = str_ireplace('<p>', '', $data[0][0]);
        $string = str_ireplace('</p>', '', $string);
        $row['content'] = str_ireplace($data[0][0], $string, $row['content']);

        // Clear <p></p> surrounding </form> tag
        preg_match_all("~<p>##CLOSE##</p>~", $row['content'], $data);
        $string = str_ireplace('<p>', '', $data[0][0]);
        $string = str_ireplace('</p>', '', $string);
        $row['content'] = str_ireplace($data[0][0], $string, $row['content']);

        // Clear <p></p> surrounding <inpu> tag with receiver e-mail
        preg_match_all("~<p>##REC([^#]+)##</p>~", $row['content'], $data);
        $string = str_ireplace('<p>', '', $data[0][0]);
        $string = str_ireplace('</p>', '', $string);
        $row['content'] = str_ireplace($data[0][0], $string, $row['content']);
        
        // Find all placeholders for form tags
        preg_match_all("~##([^#]+)##~", $row['content'], $data);
        
        $i = 0;
        $tags = array();
        
        // Replace placeholders with form tags
        foreach ($data[0] AS $tag)
        {
            if (strpos($tag, 'REC'))
            {
                $placeholder = explode(':', $tag);
                $tags[$i] = '<input type="hidden" name="rec" value="'.$this->str_rot(substr(strtolower($placeholder[1]), 0, -2), $this->config['rot']).'" />';
            }
            
            if (strpos($tag, 'INPUT'))
            {
                $placeholder = explode(':', $tag);
                $tags[$i] = '<input type="text" name="'.substr(strtolower($placeholder[1]), 0, -2).'" />';
            }

            if (strpos($tag, 'TEXTAREA'))
            {
                $placeholder = explode(':', $tag);
                $tags[$i] = '<textarea name="'.strtolower($placeholder[1]).'" cols="'.(int)$placeholder[2].'" rows="'.(int)$placeholder[3].'"></textarea>';
            }

            if (strpos($tag, 'OPEN'))
            {
                $placeholder = explode(':', $tag);
                $tags[$i] = '<form action="#" method="post" name="'.substr(strtolower($placeholder[1]), 0, -2).'">';
            }

            if (strpos($tag, 'CLOSE'))
            {
                $tags[$i] = '</form>';
            }

            if (strpos($tag, 'BUTTON'))
            {
                $placeholder = explode(':', $tag);
                $tags[$i] = '<input type="submit" value="'.substr($placeholder[1], 0, -2).'" />';
            }

            $i++;
        }
        
        $row['content'] = str_ireplace($data[0], $tags, $row['content']);
        
        return $row;
    }

    // Process form
    public function form_processor($post)
    {
        // Clean $_POST array
        require_once $this->config['absolute_path'].'modules/htmlpurifier-4.2.0/library/HTMLPurifier.auto.php';
        $purifier = new HTMLPurifier();
        $post = $purifier->purifyArray($post);
        
        $message = '';
        foreach ($_POST AS $name => $value)
        {
            if ($name == 'rec')
            {
                $email = $this->str_rot($value, -$this->config['rot']);
            }
            else
            {
                $message .= ucfirst($name).':'."\n";
                $message .= $value."\n\n";
            }
        }
        
        $this->mail_processor($message, $email);
    }

    // Process mail content
    public function mail_processor($output, $email, $type = 'MAIL')
    {
		$mail = '';
		
        if ($type == 'MAIL')
        {
            $output = chunk_split(base64_encode($output));
            
            $mime_boundary = $this->config['mail_from'].md5(time());
    		$headers = "From: ".$this->config['mail_from']." <".$this->config['mail_from'].">\n";
    		$headers .= "Reply-To: ".$email." <".$email.">\n";
    		$headers .= "MIME-Version: 1.0\n";
    		$headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";
    		$mail .= "--$mime_boundary\n";
    		$mail .= "Content-Type: text/plain; charset=utf8\n";
    		$mail .= "Content-Transfer-Encoding: base64\n\n";
            $mail .= $output."\n";
            $mail .= "--$mime_boundary--\n\n";

            mail($email, $this->config['mail_subject'], $mail, $headers);
        }
        elseif ($type == 'SMTP')
        {
            echo 'Not supported yet!';
            
            /*
            require_once $this->config['absolute_path'].'modules/Swift-4.0.6/lib/swift_required.php';

            //Create the Transport
            $transport = Swift_MailTransport::newInstance();
            
            //Create the Mailer using your created Transport
            $mailer = Swift_Mailer::newInstance($transport);
    		
    		//Create a message
    		$mail = Swift_Message::newInstance('Test')
    		  ->setFrom('webshop@simpleshop.dk')
    		  ->setTo('webshop@simpleshop.dk')
    		  ->setBody($output)
    		  ;
    		  
    		//Send the message
    		$result = $mailer->send($mail);
            print_r($result);
            */

        }
    }
    
    // Encrypt/Decrypt a string e.g. email
    // E.g. encrypt: $n = 8, decrypt: $n = -8
    public function str_rot($string, $n = 8) {
       
        $length = strlen($string);
        $result = '';
       
        for($i = 0; $i < $length; $i++) {
            $ascii = ord($string{$i});
           
            $rotated = $ascii;
           
            if ($ascii > 64 && $ascii < 91) {
                $rotated += $n;
                $rotated > 90 && $rotated += -90 + 64;
                $rotated < 65 && $rotated += -64 + 90;
            } elseif ($ascii > 96 && $ascii < 123) {
                $rotated += $n;
                $rotated > 122 && $rotated += -122 + 96;
                $rotated < 97 && $rotated += -96 + 122;
            }
           
            $result .= chr($rotated);
        }
       
        return $result;
    }

    // Create Google Analytics tracking code
    public function ga_tracker($account)
    {
        $output = '';
        $output .= '<script type="text/javascript">';
        $output .= 'var _gaq = _gaq || [];';
        $output .= "_gaq.push(['_setAccount', '$account']);";
        $output .= "_gaq.push(['_trackPageview']);";
        $output .= '(function() {';
        $output .= "var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;";
        $output .= "ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';";
        $output .= "var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);";
        $output .= "})();";
        $output .= '</script>';

        return $output;
    }

	// Flags
    public function flags()
    {
		$sql = "SELECT language FROM pages GROUP BY language";
		$result = $this->db->query($sql);
        
		$output = '<ul id="flags">';
		
        while ($row = $result->fetch_assoc())
        {
			$sql = "SELECT seo_url FROM pages WHERE language = $row[language] AND status = '1' ORDER BY sortorder LIMIT 1";
			$pages = $this->db->query($sql);
			
			$page = $pages->fetch_assoc();
			
            $output .= '<li><a href="'.$this->config['directory'][$row['language']].'/'.$page['seo_url'].'.html"><img src="/templates/website/'.$this->config['website_template'].'/images/'.$this->config['flag'][$row['language']].'" alt="'.$this->config['languages'][$row['language']].'" title="'.$this->config['languages'][$row['language']].'" /></a><li>';
        }
		
        $output .= '</ul>';

        return $output;
    }
}