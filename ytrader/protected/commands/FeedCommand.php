<?php

class FeedCommand extends CConsoleCommand {
    
	public function run($args) {
        // тут делаем то, что нам нужно
        
        $feed = Sponsorfeed::model()->find("id>0 ORDER BY RAND()");
        
        if ($feed && $feed->url) {
        	echo ("getting rss feed ".$feed->url."\n");
        	$xml = simplexml_load_string(file_get_contents($feed->url), 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOERROR | LIBXML_NOWARNING);
        	if ($xml) {
    			foreach ($xml->channel->item as $item) {
    				if (trim($item->link)) {
    					
    					// сначала проверим, может быть галерея с таким урлом уже есть?
    					$s1 = Sponsorgallery::model()->find("url LIKE '".trim($item->link)."'");
    					
    					$s2 = false;
    					$s3 = false;
    					// если есть title и description, проверим и их тоже
    					if (mb_strlen($item->title)>20) {
    						$s2 = Sponsorgallery::model()->find("name LIKE '".mysql_escape_string(trim($item->title))."'");
    					}
    					if (mb_strlen($item->description)>100) {
    						$s2 = Sponsorgallery::model()->find("description LIKE '".mysql_escape_string(trim($item->description))."'");
    					}
    					
    					if (!$s1 && !$s2 && !$s3) {
    						
    						echo ("adding gallery ".trim($item->link)."\n");
	    					
	    					$sponsorgallery = new Sponsorgallery;
	    					if ($item->title) {
	    						$sponsorgallery->name = (string) trim($item->title);
	    					}
	    					if ($item->description) {
	    						$sponsorgallery->description = trim($item->description);
	    					}
	    					$sponsorgallery->url = trim($item->link);
	    					
	    					$site = $feed->site;
	    					$sponsorgallery->site_id = $site->id;
	    					$sponsorgallery->tags = $site->tags;
	    					
	    					$sponsorgallery->content_type = $feed->content_type;
	    					
	    					$sponsorgallery->save();
	    					
    					} else {
    						echo "duplicate gallery ".$item->link."\n";
    					}
    				}
    			}
        	}
        }
        
	}
    
}

?>