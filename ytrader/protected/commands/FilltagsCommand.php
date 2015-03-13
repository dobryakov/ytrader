<?php

class FilltagsCommand extends CConsoleCommand {
    
	public function run($args) {
        // тут делаем то, что нам нужно
        
        $sponsorgalleries = Sponsorgallery::model()->findAll("gallery_id>0");
		foreach ($sponsorgalleries as $sponsorgallery) {
			if ($sponsorgallery->gallery) {
				$sponsorgallery->gallery->tags = $sponsorgallery->tags;
				$sponsorgallery->gallery->name = $sponsorgallery->name;
				$sponsorgallery->gallery->description = $sponsorgallery->description;
				$sponsorgallery->gallery->content_type = $sponsorgallery->content_type;
				$sponsorgallery->gallery->site_id = $sponsorgallery->gallery->section->site_id;
				$sponsorgallery->gallery->save();
			}
		}
        
	}
    
}

?>