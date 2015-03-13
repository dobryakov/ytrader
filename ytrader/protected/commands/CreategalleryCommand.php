<?php

class CreategalleryCommand extends CConsoleCommand {
    
	public function run($args) {
        // тут делаем то, что нам нужно
        echo ("transfer sponsorgallery to gallery...\n");

		// для ускорения процесса обрабатываем сразу несколько
		for ($i=1;$i<=20;$i++) {
			// получаем секцию
			// TODO: получать рандомную секцию или ту секцию, которая нуждается в галереях
			$section = Section::model()->find("id>0 ORDER BY RAND()");

			// берём её тэги
			$tags = explode(',',$section->tags);

			// по каждому тэгу
			foreach ($tags as $tag) {
				if (trim($tag)) {
					// запрашиваем одну спонсоргалерею
					$q = "gallery_id < 1 AND content_type = ".$section->content_type." AND tags LIKE '%".trim($tag)."%' ORDER BY id DESC";
					echo ("searching sponsorgallery: ".$q."\n");
					$sponsorgallery = Sponsorgallery::model()->find($q);
					// заносим её в галереи
					if ($sponsorgallery) {
						// практически все параметры копируются в целях денормализации базы данных
						$gallery = new Gallery();
						$gallery->section_id = $section->id;
						$gallery->site_id = $section->site_id;
						$gallery->sponsorgallery_id = $sponsorgallery->id;
						$gallery->tags = $sponsorgallery->tags;
						$gallery->name = $sponsorgallery->name;
						$gallery->description = $sponsorgallery->description;
						$gallery->content_type = $sponsorgallery->content_type;
						$gallery->t_create = time();
						$gallery->save();
						echo "adding gallery ".$gallery->id." from sponsorgallery ".$sponsorgallery->id."\n";
						// TODO: записывать тэги
						// маркируем эту спонсоргалерею как отданную сайту, чтобы она не попала на другие сайты
						if ($gallery->id) {
							$sponsorgallery->gallery_id = $gallery->id;
							$sponsorgallery->save();
						}
					}
				}
			}
		}
    }
    
}

?>