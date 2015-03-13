<?php

class CropCommand extends CConsoleCommand {
	
	private $gallery_link='';
	private $gallery_url='';
	private $gallery_link_parsed='';
	private $base_href=false;
	private $gallery_html='';
	private $gallery_thumblinks=array();
    private $CIMImage;

	var $er;
	var $html;
	var $movie_ext=array('wmv','mpg');
	
	const IMG_QUALITY = 90;
	const MAX_DOWNLOAD_TASK = 20;
	const MAX_CROP_TASK = 40;
	const GALLERY_CURL_TIMEOUT =30;
	const IMAGE_CURL_TIMEOUT = 30;
	const EMBED_CURL_TIMEOUT = 600;

	public function run($args)
	{

		// выставляем константы
		if (PHP_OS == 'Linux') {
			define('CROP_METHOD', 'im');
		} else {
			define('CROP_METHOD', 'gd');
		}

		$this->galleryToGrab();
		$this->grabGallery();
		$this->parseGalleryMeta();
		$this->parseGallery();
		for ($i=1;$i<=self::MAX_DOWNLOAD_TASK;$i++) { $this->downloadImage(); }
		for ($i=1;$i<=self::MAX_CROP_TASK;$i++) { $this->cropImage(); }
		$this->downloadEmbed();

	}

	/**
	 * берём рандомную галеру в статусе NEW и ставим её в очередь на граббинг
	 */
	private function galleryToGrab()
	{
		$gallery = Gallery::model()->find("crop_status=".Gallery::STATUS_NEW." ORDER BY id DESC");
		if ($gallery) {
			$gallery->crop_status = Gallery::STATUS_GRABBING;
			$gallery->save();
			Queue::push(Queue::GALLERIES_TO_GRAB, $gallery->id);
		}
	}

	/**
	 * берём галерею из очереди на граббинг и скачиваем на локальный диск
	 */
	private function grabGallery()
	{
		/** @var $gallery Gallery */
		$gallery_id = Queue::pull(Queue::GALLERIES_TO_GRAB);
		if ($gallery_id) {
			$gallery = Gallery::model()->findByPk($gallery_id);
			if ($gallery) {
				// скачиваем локально
				$filename = $this->download($gallery->sponsorgallery->url, null, self::GALLERY_CURL_TIMEOUT);
				// ставим в очередь на парсинг
				Queue::push(Queue::GALLERIES_TO_PARSE, array('id' => $gallery->id, 'filename' => $filename));
				// ставим в очередь на разбор мета-тэгов
				Queue::push(Queue::GALLERIES_TO_GETMETA, array('id' => $gallery->id, 'filename' => $filename));
				// переводим в статус "сграблена"
				$gallery->crop_status = Gallery::STATUS_GRABBED;
				$gallery->save();
			}
		}
	}

	/**
	 * парсим галерею и вытаскиваем мета-тэги
	 */
	private function parseGalleryMeta()
	{
		$g = Queue::pull(Queue::GALLERIES_TO_GETMETA);
		if ($g) {

			$meta = get_meta_tags($g['filename']);
			if (isset($meta['description'])) {

				$gallery = Gallery::model()->findByPk($g['id']);
				if ($gallery) {

					$gallery->sponsorgallery->description = $meta['description'];

					if (!$gallery->sponsorgallery->name) {
						preg_match("|<title>(.+)<\/title>|i", file_get_contents($g['filename']), $title_matches);
						if (isset($title_matches[1])) {
							$gallery->sponsorgallery->name = $title_matches[1];
						}
					}

					$gallery->sponsorgallery->save();

				}
			}
		}
	}

	/**
	 * парсим галерею и получаем ссылки на тумбы
	 */
	private function parseGallery()
	{
		// достаём из очереди на парсинг
		$g = Queue::pull(Queue::GALLERIES_TO_PARSE);
		if ($g) {
			$gallery = Gallery::model()->findByPk($g['id']);
			if ($gallery) {
				// переводим в статус "кропится"
				$gallery->crop_status = Gallery::STATUS_CROPPING;
				$gallery->save();
				// парсим контент
				if ($gallery->sponsorgallery->content_type == ContenttypeController::CONTENT_TYPE_IMAGES) {
					return $this->parseIGallery($g);
				}
				if ($gallery->sponsorgallery->content_type == ContenttypeController::CONTENT_TYPE_EMBEDS) {
					return $this->parseEGallery($g);
				}
			}
		}
	}

	/**
	 * парсим галерею картинок (I - Images)
	 */
	private function parseIGallery($g)
	{
		$gallery = Gallery::model()->findByPk($g['id']);
		if ($gallery) {
			// разбираем контент
			$this->setGalleryLink($g['filename']);

			/** @var $gallery Gallery */
			$gallery_url = parse_url($gallery->sponsorgallery->url);
			var_dump($gallery_url);
			$path = $gallery_url['path'];
			if (substr($path, strlen($path)-1, 1) == '/') {
				// берём как есть
			} else {
				// берём dirname
				$path = dirname($path);
			}
			$real_url = $gallery_url['scheme'].'://'.$gallery_url['host'].'/'.trim($path, '/').'/';
			$this->setGalleryUrl($real_url);
			var_dump($real_url);

			$this->read();
			$this->parse();
			$images = $this->get_thumb_urls();

			echo ("images:\n");
			print_r($images);

			if (is_array($images) && isset($images['fulls']) && (count($images['fulls'])>0)) {
				foreach ($images['fulls'] as $img_src) {

					$img_src = $this->makeAbsUrl($img_src, $gallery->sponsorgallery->url);

					// создаём объект Image
					$image = new Image();
					$image->gallery_id = $gallery->id;
					$image->source_url = $img_src;
					$image->save(); // чтобы объект приобрёл свой id из базы данных
					echo("parseIGallery: creating Image ".$image->id."\n");

					// ставим урлы картинок в очередь на скачивание
					Queue::push(Queue::IMAGES_TO_GRAB, array("image_id" => $image->id, "gallery_id" => $g['id'], "img_src" => $img_src));

				}
				unlink($g['filename']);
			} else {
				// не удалось распарсить тумбы, отмечаем как плохую
				$gallery->crop_status = Gallery::STATUS_BAD;
				$gallery->save();
				throw new CException("Can not parse thumbs from sponsorgallery");
			}
		}
	}

	/**
	 * парсим галерею флэшек (E - Embed)
	 */
	private function parseEGallery($g)
	{
		$gallery = Gallery::model()->findByPk($g['id']);
		if ($gallery) {
			// TODO:

			$variants = array(
				array(
					'embed' => '@embed(.+?)file=(.+?).flv(.+?)>@',
					'image' => '@embed(.+?)image=(.+?).jpg(.+?)>@',
					'suffix' => 'flv',
					'number' => 2
				),
				array(
					'embed' => "@file','(.+?).flv'@",
					'image' => "@image','(.+?).jpg'@",
				),
				array(
					'embed' => "@file=(.+?).flv@",
					'image' => "@image=(.+?).jpg@",
				),
				array(
					'embed' => "@file=(.+?).mp4@",
					'image' => "@image=(.+?).jpg@",
					'suffix' => 'mp4',
				),
				array(
					'embed' => "@movieURL=(.+?).mp4@",
					'image' => "@thumbnailURL=(.+?).jpg@",
					'suffix' => 'mp4',
				),
				array(
					'embed' => "@'file': '(.+?).mp4@",
					'image' => "@'image': '(.+?).jpg@",
					'suffix' => 'mp4',
				),
				array(
					'embed' => "@src=(.+?).mp4@",
					'image' => "@poster=(.+?).jpg@",
					'suffix' => 'mp4',
				),
			);

			// пробуем получить контент различными способами (вариантами)
			$content = file_get_contents($g['filename']);
			$links_flv = false;
			$links_jpg = false;
			foreach ($variants as $variant) {
				$embed_regexp = $variant['embed'];
				$image_regexp = $variant['image'];
				$suffix = isset($variant['suffix']) ? $variant['suffix'] : 'flv';
				$regexp_number = isset($variant['number']) ? $variant['number'] : 1;
				preg_match_all($embed_regexp, $content, $matches_flv);
				preg_match_all($image_regexp, $content, $matches_jpg);
				if (isset($matches_flv[$regexp_number]) && isset($matches_jpg[$regexp_number]) && $matches_flv[$regexp_number] && $matches_jpg[$regexp_number]) {
					// это наш результат
					$links_flv = $matches_flv[$regexp_number];
					$links_jpg = $matches_jpg[$regexp_number];
					break;
				}
			}
			if (!$links_flv || !$links_jpg) {
				$gallery->crop_status = Gallery::STATUS_BAD;
				$gallery->save();
				throw new CException('Can not parse links to embed content');
			}
			foreach ($links_flv as $k=>$flv) {

				// создаём объект Image
				$image = new Image();
				$image->gallery_id = $gallery->id;
				$image->source_url = $flv;
				$image->save(); // чтобы объект приобрёл свой id из базы данных
				echo("parseEGallery: creating Image ".$image->id."\n");

				// ставим урл картинки в очередь на скачивание и дальнейший кроппинг
				$img_src = $this->makeAbsUrl($links_jpg[$k].'.jpg', $gallery->sponsorgallery->url);
				Queue::push(Queue::IMAGES_TO_GRAB, array("image_id" => $image->id, "gallery_id" => $g['id'], "img_src" => $img_src));
				// ставим флэшку в очередь на скачивание
				$flv_src = $this->makeAbsUrl($flv.'.'.$suffix, $gallery->sponsorgallery->url);
				Queue::push(Queue::EMBEDS_TO_GRAB, array("image_id" => $image->id, "gallery_id" => $g['id'], "flv_src" => $flv_src, 'suffix' => $suffix));

			}
		}
	}

	/**
	 * скачиваем картинку
	 */
	private function downloadImage()
	{
		$i = Queue::pull(Queue::IMAGES_TO_GRAB);
		if ($i) {
			$gallery = Gallery::model()->findByPk($i['gallery_id']);
			if ($gallery) {

				// воссоздаём объект Image, к которому прикрепляется эта флэшка
				if (!isset($i['image_id'])) {
					throw new CException('Image_id is not set');
				}
				$image = Image::model()->findByPk($i['image_id']);
				if (!$image) {
					throw new CException('Image object not found while downloading embed');
				}

				// скачиваем и ставим в очередь на кроппинг
				$filename = null;
				try {
					$filename = $this->download($i['img_src'], $gallery->sponsorgallery->url, self::IMAGE_CURL_TIMEOUT);
				} catch (CException $e) {
					$image->delete();
					echo ($e->getMessage()."\n");
				}
				if ($filename && $image && $image->id) {
					echo ("file successfully download to ".$filename." with Image object ".$image->id."\n");
					Queue::push(Queue::IMAGES_TO_CROP, array("image_id" => $image->id, "gallery_id" => $i['gallery_id'], "filename" => $filename));
				}

			}
		}
	}

	/**
	 * кропим картинку
	 */
	private function cropImage()
	{
		// берем из очереди на кроппинг
		$i = Queue::pull(Queue::IMAGES_TO_CROP);
		if ($i) {

			$gallery = Gallery::model()->findByPk($i['gallery_id']);
			if ($gallery) {
				// получаем кроп-профайлы для данной секции
				$cropprofiles = $gallery->section->cropprofiles;
				if (!$cropprofiles) {
					Yii::log('No cropprofiles for section '.$gallery->section->id, CLogger::LEVEL_WARNING);
					throw new CException('No cropprofiles for section '.$gallery->section->id."\n");
				}

				if (isset($i['image_id']) && $i['image_id']>0) {
					// воссоздаём объект Image
					$image = Image::model()->findByPk($i['image_id']);
					if ($image) {
						echo(__METHOD__.": object Image restored by id ".$i['image_id']."\n");
					} else {
						echo(__METHOD__.": can't restore Image by id ".$i['image_id']."\n");
					}
				} else {
					// если нам не передан image_id - создаём объект Image
					$image = new Image();
					$image->gallery_id = $gallery->id;
					$image->save(); // чтобы объект приобрёл свой id из базы данных
					echo(__METHOD__.": creating object Image ".$image->id."\n");
				}
				if (!$image) {
					throw new CException('Image object not found while cropping');
				}

				foreach ($cropprofiles as $cropprofile) {
					$thumb_file_path = $image->getThumbFilepath($cropprofile->id);
					echo "cropping by cropprofile ".$cropprofile->id." at ".$cropprofile->width.'x'.$cropprofile->height." to ".$thumb_file_path."\n";
					$this->crop($i['filename'], $thumb_file_path, $cropprofile->width, $cropprofile->height);
					sleep(1);
					if (!file_exists($thumb_file_path)) {
						$image->delete();
						throw new CException('Image not saved to disk after cropping');
					}
				}

				// если хотя бы одна тумба откроплена - отмечаем галеру как откропленную,
				// хоть мы реально и не знаем все ли тумбы откроплены
				/*if ($gallery->crop_status <> Gallery::STATUS_CROPPED) {
					$gallery->crop_status = Gallery::STATUS_CROPPED;
					$gallery->save();
				}*/

				// отмечаем изображение как откропленное
				$image->status = Image::STATUS_CROPPED;
				$image->save();

				// проверяем, все ли известные нам изображения галереи откроплены
				// и если да - ставим галерее статус cropped
				$gallery_is_cropped = true;
				foreach ($gallery->images as $im) {
					if ($im->status != Image::STATUS_CROPPED) {
						$gallery_is_cropped = false;
						break;
					}
				}
				if ($gallery_is_cropped) {
					$gallery->crop_status = Gallery::STATUS_CROPPED;
					$gallery->save();
					echo ("[+] gallery ".$gallery->id." is successfully cropped\n");
				} else {
					echo ("[.] gallery ".$gallery->id." not cropped yet, wait...\n");
				}

				//$image->save();
				unlink($i['filename']);
			}

		}
	}

	private function downloadEmbed()
	{
		$i = Queue::pull(Queue::EMBEDS_TO_GRAB);
		if ($i) {
			$gallery = Gallery::model()->findByPk($i['gallery_id']);
			if ($gallery && $gallery->sponsorgallery) {

				// воссоздаём объект Image, к которому прикрепляется эта флэшка
				if (!isset($i['image_id'])) {
					throw new CException('Image_id is not set');
				}
				$image = Image::model()->findByPk($i['image_id']);
				if (!$image) {
					throw new CException('Image object not found while downloading embed');
				}

				// скачиваем
				$filename = null;
				try {
					$filename = $this->download($i['flv_src'], $gallery->sponsorgallery->url, self::EMBED_CURL_TIMEOUT);
				} catch (CException $e) {
					/** var $e CException */
					$image->delete();
					echo($e->getMessage()."\n");
				}

				if ($filename) {
					// перемещаем в постоянное хранилище
					/**	var @image Image */
					$cropprofile = Cropprofile::model()->find("section_id=".$image->gallery->section_id." AND assignment & ".Cropprofile::ASSIGN_PAGE);
					$target_filename = $image->getFlvFilepath($cropprofile->id, $i['suffix']);
					echo("move embed file from ".$filename." to ".$target_filename."\n");

					if (!is_dir(dirname($target_filename))) {
						mkdir(dirname($target_filename), 0777, true);
					}
					rename($filename, $target_filename);
					sleep(1);
					if (!file_exists($target_filename)) {
						throw new CException('Embed is downloaded, but not saved to disk');
					}
				}

			}
		}
	}

	// ----------------------------------------------------------------------------------------

	/**
	 * скачивает контент во временный каталог, возвращает путь к скаченному файлу
	 */
	private function download($url, $referer = null, $timeout = 30)
	{
		echo ("download: ".$url."\n");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // allow redirects
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); // times out after 30s
		curl_setopt($ch, CURLOPT_REFERER, $referer);

		$t = time();
		$curl_result = curl_exec($ch); // run the whole process
		curl_close($ch);
		if ((time() - ($t - 2)) >= $timeout) {
			echo ("possible CURL timeout exceed\n");
		}

		if ($curl_result) {
			$filename = Yii::app()->basePath.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'download'.DIRECTORY_SEPARATOR.uniqid();
			if (!is_dir(dirname($filename))) {
				mkdir (dirname($filename), 0777, true);
			}
			file_put_contents($filename, $curl_result);
			sleep(1);
			if (!file_exists($filename)) {
				throw new CException("URL downloaded, but not saved to disk");
			}
			if (filesize($filename) < 1) {
				throw new CException("Downloaded file size is too low");
			}
			return $filename;
		} else {
			throw new CException("URL not downloaded");
		}
	}

	// --------------------------------------------------

	public function _run($args) {
        // тут делаем то, что нам нужно

        // выставляем константы
        if (PHP_OS == 'Linux') {
            define('CROP_METHOD', 'im');
        } else {
            define('CROP_METHOD', 'gd');
        }

        // создаём объекты
        $this->CIMImage = new CIMImage();

        // берём галерею, которую нужно сграбить
        $gallery = Gallery::model()->find("crop_status = ".Gallery::STATUS_NEW." ORDER BY RAND()");
        if ($gallery) {
        	$url = $gallery->sponsorgallery->url;
        	echo ("grabbing gallery ".$url."\n");
        	if ($url) {
        		
        		// считываем основные данные - title, description
        		if (!$gallery->sponsorgallery->description) {
	        		$meta = get_meta_tags($url);
	        		if (isset($meta['description'])) {
	        			$gallery->sponsorgallery->description = $meta['description'];
	        		}
        		}
        		if (!$gallery->sponsorgallery->name) {
        			preg_match("|<title>(.+)<\/title>|i", file_get_contents($url), $title_matches);
        			if (isset($title_matches[1])) {
	        			$gallery->sponsorgallery->name = $title_matches[1];
    	    		}
        		}
        		$gallery->sponsorgallery->save();

                if ($gallery->sponsorgallery->content_type == ContenttypeController::CONTENT_TYPE_IMAGES) {

                    // парсим тумбы (это остатки от старого класса image cropper в dgr)
                    $this->setGalleryLink($url);
                    $this->read();
                    $this->parse();
                    $images = $this->get_thumb_urls();
                    if (is_array($images) && isset($images['fulls']) && (count($images['fulls'])>0)) {
                        $error = false;
                        foreach ($images['fulls'] as $img_src) {
                            echo "saving image ".$img_src."\n";
                            $image = new Image();
                            $image->gallery_id = $gallery->id;
                            $image->source_url = $img_src;
                            $image->save();
                            if ($image->id) {
                                // скачиваем саму картинку
                                $image_source_path = $image->sourcepath;
                                var_dump($image_source_path);
                                //file_put_contents($image_source_path, file_get_contents($image->source_url));

                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $image->source_url); // set url to post to
                                curl_setopt($ch, CURLOPT_FAILONERROR, 1);
                                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // allow redirects
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
                                curl_setopt($ch, CURLOPT_TIMEOUT, 30); // times out after 30s
                                curl_setopt($ch, CURLOPT_REFERER, $url);
                                //curl_setopt($ch, CURLOPT_POST, 1); // set POST method
                                //curl_setopt($ch, CURLOPT_POSTFIELDS, "url=index%3Dbooks&field-keywords=PHP+MYSQL"); // add POST fields
                                $curl_result = curl_exec($ch); // run the whole process
                                curl_close($ch);

                                if ($curl_result) {
                                    file_put_contents($image_source_path, $curl_result);
									chmod($image_source_path, 0777);
                                }

                                if (!file_exists($image_source_path)) {
                                    echo "WARNING!: image source is not downloaded\n";
									Yii::log('image source is not downloaded', CLogger::LEVEL_WARNING);
                                    $error = true;
                                    break;
                                }
                            }
                        }
                        if (!$error) {
                            $gallery->crop_status = Gallery::STATUS_GRABBED;
                            $gallery->save();
                        } else {
							// TODO: дописать маркировку спонсоргалерей как-нибудь status_bad
							$gallery->delete();
						}
                    }
                }

                if ($gallery->sponsorgallery->content_type == ContenttypeController::CONTENT_TYPE_EMBEDS) {

                    $content = file_get_contents($url);
                    echo ("grabing by type flv, html content ".mb_strlen($content)." bytes \n");

                    if ($content) {

                        $error = false;
                        $suffix = 'flv';
                        $links_flv = array();
                        $links_jpg = array();

                        preg_match_all("@embed(.+?)file=(.+?).flv(.+?)>@", $content, $matches_flv);
                        preg_match_all("@embed(.+?)image=(.+?).jpg(.+?)>@", $content, $matches_jpg);
                        //var_dump($matches_flv[2]);
                        //var_dump($matches_jpg[2]);

                        if (isset($matches_flv[2]) && isset($matches_jpg[2]) && $matches_flv[2] && $matches_jpg[2]) {
                            // это наш результат
                            $links_flv = $matches_flv[2];
                            $links_jpg = $matches_jpg[2];
                        } else {

                            // пытаемся получить ссылки на flv и jpg в формате 1
                            preg_match_all("@file','(.+?).flv'@",$content,$matches_flv);
                            preg_match_all("@image','(.+?).jpg'@",$content,$matches_jpg);
                            //var_dump($matches_flv);
                            //var_dump($matches_jpg);

                            if (isset($matches_flv[1]) && isset($matches_jpg[1]) && $matches_flv[1] && $matches_jpg[1]) {
                                // это наш результат
                                $links_flv = $matches_flv[1];
                                $links_jpg = $matches_jpg[1];
                            } else {

                                // пытаемся получить ссылки на flv и jpg в формате 2
                                preg_match_all("@file=(.+?).flv@",$content,$matches_flv);
                                preg_match_all("@image=(.+?).jpg@",$content,$matches_jpg);
                                //var_dump($matches_flv);
                                //var_dump($matches_jpg);

                                if (isset($matches_flv[1]) && isset($matches_jpg[1]) && $matches_flv[1] && $matches_jpg[1]) {
                                    // это наш результат
                                    $links_flv = $matches_flv[1];
                                    $links_jpg = $matches_jpg[1];
                                } else {

                                    // пытаемся получить ссылки на mp4 и jpg (формат 3)
                                    preg_match_all("@file=(.+?).mp4@", $content, $matches_flv);
                                    preg_match_all("@image=(.+?).jpg@", $content, $matches_jpg);

                                    if (isset($matches_flv[1]) && isset($matches_jpg[1]) && $matches_flv[1] && $matches_jpg[1]) {
                                        // это наш результат
                                        $suffix = 'mp4';
                                        $links_flv = $matches_flv[1];
                                        $links_jpg = $matches_jpg[1];

                                    } else {

                                        // пытаемся получить ссылки на mp4 и jpg (формат 4)
                                        preg_match_all("@movieURL=(.+?).mp4@", $content, $matches_flv);
                                        preg_match_all("@thumbnailURL=(.+?).jpg@", $content, $matches_jpg);

                                        if (isset($matches_flv[1]) && isset($matches_jpg[1]) && $matches_flv[1] && $matches_jpg[1]) {
                                            // это наш результат
                                            $suffix = 'mp4';
                                            $links_flv = $matches_flv[1];
                                            $links_jpg = $matches_jpg[1];

                                            foreach ($links_flv as $k=>$v) {
                                                $links_flv[$k] = urldecode($v);
                                            }
                                            foreach ($links_jpg as $k=>$v) {
                                                $links_jpg[$k] = urldecode($v);
                                            }

                                        } else {

											// пытаемся получить ссылки на mp4 и jpg (формат 5)
											preg_match_all("@'file': '(.+?).mp4@", $content, $matches_flv);
											preg_match_all("@'image': '(.+?).jpg@", $content, $matches_jpg);

											if (isset($matches_flv[1]) && isset($matches_jpg[1]) && $matches_flv[1] && $matches_jpg[1]) {
												// это наш результат
												$suffix = 'mp4';
												$links_flv = $matches_flv[1];
												$links_jpg = $matches_jpg[1];

												foreach ($links_flv as $k=>$v) {
													$links_flv[$k] = urldecode($v);
												}
												foreach ($links_jpg as $k=>$v) {
													$links_jpg[$k] = urldecode($v);
												}
											} else {

												// пытаемся получить ссылки на mp4 и jpg (формат 6)
												preg_match_all("@src=(.+?).mp4@", $content, $matches_flv);
												preg_match_all("@poster=(.+?).jpg@", $content, $matches_jpg);

												if (isset($matches_flv[1]) && isset($matches_jpg[1]) && $matches_flv[1] && $matches_jpg[1]) {
													// это наш результат
													$suffix = 'mp4';
													$links_flv = $matches_flv[1];
													$links_jpg = $matches_jpg[1];

													foreach ($links_flv as $k=>$v) {
														$links_flv[$k] = urldecode($v);
													}
													foreach ($links_jpg as $k=>$v) {
														$links_jpg[$k] = urldecode($v);
													}
												}

											}

										}

                                    }

                                }

                            }
                        }

                        if ($links_flv && $links_jpg) {

                            // если мы их хоть как-то получили, надо их записать
                            foreach ($links_flv as $k=>$flv) {

                                // создаём условную "картинку"
                                $image = new Image();
                                $image->gallery_id = $gallery->id;
                                $image->save();

                                // скачиваем флэшку
                                $flv_source_path = trim($image->getFlvsourcepath($suffix));
                                var_dump("flv source path: ".$flv_source_path);

                                $flv_url = trim($flv).'.'.$suffix;
                                // это абсолютный или относительный путь?
                                $flv_url = $this->makeAbsUrl($flv_url, $url);
                                var_dump("flv url ".$flv_url);

                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $flv_url); // set url to post to
                                curl_setopt($ch, CURLOPT_FAILONERROR, 1);
                                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // allow redirects
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
                                curl_setopt($ch, CURLOPT_TIMEOUT, 120); // times out after 120s
                                curl_setopt($ch, CURLOPT_REFERER, $url);
                                //curl_setopt($ch, CURLOPT_POST, 1); // set POST method
                                //curl_setopt($ch, CURLOPT_POSTFIELDS, "url=index%3Dbooks&field-keywords=PHP+MYSQL"); // add POST fields
                                $curl_result = curl_exec($ch); // run the whole process
                                if ($e = curl_error($ch)) {
                                    var_dump($e);
                                }
                                curl_close($ch);

                                if ($curl_result) {
                                    file_put_contents($flv_source_path, $curl_result);
									chmod($flv_source_path, 0777);
                                }

                                if (!file_exists($flv_source_path)) {
                                    echo "WARNING! ".$suffix." source is not downloaded\n";
									Yii::log($suffix." source is not downloaded", CLogger::LEVEL_WARNING);
                                    $error = true;
                                    break;
                                }

                                // скачиваем соответствующую картинку
                                $jpg_source_path = trim($image->getSourcepath());
                                var_dump("jpg source path: ".$jpg_source_path);
                                $jpg_url = trim($links_jpg[$k]).'.jpg';
                                // это абсолютный или относительный путь?
                                $jpg_url = $this->makeAbsUrl($jpg_url, $url);
                                var_dump("jpg url ".$jpg_url);

                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $jpg_url); // set url to post to
                                curl_setopt($ch, CURLOPT_FAILONERROR, 1);
                                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // allow redirects
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
                                curl_setopt($ch, CURLOPT_TIMEOUT, 30); // times out after 120s
                                curl_setopt($ch, CURLOPT_REFERER, $url);
                                //curl_setopt($ch, CURLOPT_POST, 1); // set POST method
                                //curl_setopt($ch, CURLOPT_POSTFIELDS, "url=index%3Dbooks&field-keywords=PHP+MYSQL"); // add POST fields
                                $curl_result = curl_exec($ch); // run the whole process
                                if ($e = curl_error($ch)) {
                                    var_dump($e);
                                }
                                curl_close($ch);

                                if ($curl_result) {
                                    file_put_contents($jpg_source_path, $curl_result);
									chmod($jpg_source_path, 0777);
                                }

                                if (!file_exists($jpg_source_path)) {
                                    echo "WARNING! jpg source is not downloaded\n";
									Yii::log("jpg source is not downloaded", CLogger::LEVEL_WARNING);
                                    $error = true;
                                    break;
                                }

                            }

                        } else {
							$error = true;
							echo ("can not parse links to content files\n");
							Yii::log("can not parse links to content files", CLogger::LEVEL_WARNING);
						}

                        if (!$error) {
                            $gallery->crop_status = Gallery::STATUS_GRABBED;
							$gallery->save();
							$gallery->sponsorgallery->suffix = $suffix;
							$gallery->sponsorgallery->save();
                        } else {
							// TODO: дописать маркировку спонсоргалерей как-нибудь status_bad
                            //$gallery->crop_status = Gallery::STATUS_NEW;
							//$sponsorgallery = $gallery->sponsorgallery;
							//$sponsorgallery->gallery_id = 0;
							//$sponsorgallery->save();
							$gallery->delete();
                        }

                    }

                }

        	}
        }

        // теперь кропим
        // берём галерею, которую нужно скропить (скорее всего это та же самая, но мы возьмём новый объект)
        $gallery = Gallery::model()->find("crop_status = ".Gallery::STATUS_GRABBED." ORDER BY RAND()");
        if ($gallery) {
        	
        	// получаем кроп-профайлы для данной секции
        	$cropprofiles = $gallery->section->cropprofiles;
			if (!$cropprofiles) {
				Yii::log('No cropprofiles for section '.$gallery->section->id, CLogger::LEVEL_WARNING);
				throw new Exception('No cropprofiles for section '.$gallery->section->id."\n");
			}

        	$url = $gallery->sponsorgallery->url;
        	echo ("cropping gallery ".$url."\n");
        	$images = $gallery->images;
        	if (is_array($images)) {
        		foreach ($images as $image) {
        			var_dump($image->sourcepath);
        			foreach ($cropprofiles as $cropprofile) {

                        // если это картинки - делаем тумбу
                        // если это flv, сначала делаем тумбу из картинки, а сам flv будем обрабатывать дальше
                        $thumb_file_path = $image->getThumbFilepath($cropprofile->id);
                        echo "cropping by cropprofile ".$cropprofile->id." at ".$cropprofile->width.'x'.$cropprofile->height." to ".$thumb_file_path."\n";
                        $this->crop($image->sourcepath, $thumb_file_path, $cropprofile->width, $cropprofile->height);

        			}

					if (file_exists($image->sourcepath)) {
        				unlink($image->sourcepath);
					}

                    if ($gallery->sponsorgallery->content_type == ContenttypeController::CONTENT_TYPE_EMBEDS) {
                        // перекладываем flv из временного каталога в хранилище, доступное из веба
                        $source_file_path = trim($image->getFlvsourcepath($suffix));
                        echo ($suffix." source file path ".$source_file_path."\n");
                        $target_file_path = $image->getFlvFilePath($cropprofile->id, $suffix);
                        echo ($suffix." target file path ".$target_file_path."\n");
                        if (!is_dir(dirname($target_file_path))) {
                            mkdir (dirname($target_file_path), 0777, true);
                        }
                        copy($source_file_path, $target_file_path);
                        unlink($source_file_path);
                        echo "copy ".$suffix." from ".$source_file_path." to ".$target_file_path."\n";
                    }

                }
        		$gallery->crop_status = Gallery::STATUS_CROPPED;
        		$gallery->save();
        	}
        }
    }

    /**
     * @param $file имя файла, к которому нужно приклеить (если требуется) полный URL
     * @param $url адрес страницы (галерея), на которой был найден этот файл
     */
    function makeAbsUrl($file, $url)
    {
		$file = urldecode($file);
		$url = urldecode($url);
        if (@parse_url($file, PHP_URL_HOST)) {
            // абсолютный
			echo ($file." looks like absolute path\n");
        } else {
            // относительный, нужно приклеить URL страницы
			echo ($file." looks like relative path, need to join with ".$url."\n");
            $h = (parse_url($url));
			$d = dirname($h['path']);
            $file = $h['scheme'].'://'.$h['host'].rtrim($d,'/').'/'.$file;
			echo ("result path: ".$file."\n");
        }
        return $file;
    }
    
    function setGalleryLink($link)
	{
		$this->gallery_link=$link;
		$t=parse_url($link);
		$t_path=split('/',$t['path']);
		array_pop($t_path);
		//$this->gallery_link_parsed=$t['scheme'].'://'.$t['host'].join('/',$t_path).'/';
		//print_r($this->gallery_link_parsed); die;
	}

	function setGalleryUrl($url)
	{
		$this->gallery_url = $url;
	}
	
	function read()
	{
		if ($this->gallery_link)
		{
			$this->gallery_html=file_get_contents($this->gallery_link);
			$this->html=$this->gallery_html;
			if (strstr($this->html,'<base href'))
			{
				preg_match("@base href=['|\"](.+)['|\"]@",$this->html,$matches);
				//print_r($matches); die;
				if (isset($matches[1])) { $this->base_href=$matches[1]; }
			}
		}
	}
	
	function parse()
	{
		if ($this->gallery_html)
		{
			
			$html=$this->gallery_html;
			$html=preg_replace("|\n|",'',$html);
			$html=preg_replace("|\r|",'',$html);
			$html=preg_replace("|\s\s+|",'',$html);
			
			preg_match_all("@href=(\"|')(.+?)\.jpg(\"|')(.+?)\.jpg(.+?)<\/a>@i",$html,$matches);
			
			if (isset($matches[0]))
			{
				foreach ($matches[0] as $href)
				{
					preg_match_all("@href=(\"|')(.+?)\.jpg(\"|')@i",$href,$matches2);
					//print_r($matches2);
					if (isset($matches2[2][0]))
					{
						$thumblink=$matches2[2][0];
						//print_r($thumblink);
						if (strstr($thumblink,'http://'))
						{
							$this->gallery_thumblinks[]=$thumblink;
						}
						//elseif (strpos($thumblink,'/')==0)
						//{
						//	$this->gallery_thumblinks[]=parse_url($this->gallery_link,PHP_URL_SCHEME).'://'.parse_url($this->gallery_link,PHP_URL_HOST).$thumblink;
						//}
						else 
						{
							$this->gallery_thumblinks[]=rtrim($this->gallery_url, '/').'/'.ltrim($thumblink,'/');
						}
					}
				}
			}

			echo ("this->gallery_thumblinks\n");
			print_r($this->gallery_thumblinks);
			
		}
	}
	
	function get_thumb_urls()
   {
      if (strlen($this->html)==0) {$this->er = "HTML of gallery is empty"; return array();}
      $ret = preg_match_all("~<a[^>]+href\s*=[\s'\"]*(".implode('|', $this->movie_ext)."|[^>\s'\"]*\.(?:jpeg|jpg|".implode('|', $this->movie_ext)."))[^>]*>(.*?)(?:<\/td|<\s*\/a\s*)>~is", $this->html, $hrefs);
      if (!$ret) return array();
      $res=array();
      for ($i=0; $i<count($hrefs[2]); $i++)
      {
         $ret=preg_match("/img[^>]+src\s*=[\s'\"]*([^>\s'\"]*\.(?:jpeg|jpg|gif))[^><]*/i", $hrefs[2][$i], $imgs);
         if (!$ret) continue;
         $res['fulls'] []= $hrefs[1][$i];
         $res['thumbs'] []= $imgs[1];
         preg_match("/\.[a-z]+$/i", basename($hrefs[1][$i]), $ext);
      }

      foreach ($res['fulls'] as $k=>$v)
      {
      	if (strstr($v,'http://')===false)
      	{
			$this->base_href = $this->gallery_url;
      		if ($this->base_href)
      		{
      			echo ("base href exists<br>");
      			if (substr($res['fulls'][$k],0,1)=='/')
      			{
      				$p_url=parse_url($this->base_href);
      				$base_url=$p_url['scheme'].'://'.$p_url['host'];
      				$res['fulls'][$k]=$base_url.$res['fulls'][$k];
      			}
      			else 
      			{
      				$res['fulls'][$k]=$this->base_href.$res['fulls'][$k];
      			}
      		}
      		else 
      		{
      			echo ("base href does not exists<br>\n");
      			if (substr($res['fulls'][$k],0,1)=='/')
      			{
      				$p_url=parse_url($this->gallery_link_parsed);
      				$base_url=$p_url['scheme'].'://'.$p_url['host'];
      				$res['fulls'][$k]=$base_url.$res['fulls'][$k];     				
      			}
      			else 
      			{
      				$res['fulls'][$k]=$this->gallery_link_parsed.(ltrim($res['fulls'][$k],'/'));
      			}
      		}
      	}
      }
      
      foreach ($res['thumbs'] as $k=>$v)
      {
      	if (strstr($v,'http://')===false)
      	{
      		$res['thumbs'][$k]=$this->gallery_link_parsed.(ltrim($res['thumbs'][$k],'/'));
      	}
      }
            
      return $res;
   }
   
   function crop($source, $target, $width, $height)
	{
		if (CROP_METHOD=='gd')
		{
			return $this->gd_crop($source, $target, $width, $height);
		}
		if (CROP_METHOD=='im')
		{
			return $this->im_crop($source, $target, $width, $height);
		}
	}
   
   private function imageOrientation($image)
	{
		if (imagesx($image)>=imagesy($image))
		{
			return 'landscape';
		}
		else 
		{
			return 'portrait';
		}
	}
	
	function gd_crop($source_filename, $target_filename, $width, $height)
	{
			
		$source=imagecreatefromjpeg($source_filename);
		
		$destination=imagecreatetruecolor($width, $height);
						
		$source_w=imagesx($source);
		$source_h=imagesy($source);
		
		$dest_w=imagesx($destination);
		$dest_h=imagesy($destination);
		
		if ($this->imageOrientation($source)=='portrait')
		{
			$new_w=$dest_w;
			$k=$dest_w/$source_w;
			$new_h=intval($source_h*$k);
			$ax=0;
			$ay=($source_h/2)-($dest_h/(2*$k));
			$bx=$source_w;
			$by=($source_h/2)+($dest_h/(2*$k));
		}
		else 
		{
			$new_h=$dest_h;
			$k=$dest_h/$source_h;
			$new_w=intval($source_w*$k);
			$ay=0;
			$ax=($source_w/2)-($dest_w/(2*$k));
			$by=$source_h;
			$bx=($source_w/2)+($dest_w/(2*$k));
		}
		
		//echo ("resizing coordinates: Ax=".$ax.",Ay=".$ay." to Bx=".$bx.",By=".$by."<br>");
		
		imagecopyresampled($destination,$source,0,0,$ax,$ay,$dest_w,$dest_h,($bx-$ax),($by-$ay));
		
		if (!is_dir(dirname($target_filename))) {
			mkdir(dirname($target_filename), 0777, true);
		}
		
		echo ("saving ".$target_filename."\n");
		imagejpeg($destination,$target_filename,self::IMG_QUALITY);
		chmod($target_filename, 0777);
		
	}

    function im_crop($source_filename, $target_filename, $width, $height)
    {

        if (!$this->CIMImage) {
			$this->CIMImage = new CIMImage();
		}
		$this->CIMImage->CropCenter($source_filename,$target_filename,$width,$height);
        echo ("saving ".$target_filename." using imagemagick\n");

    }
	
}


class CIMImage
{

    function Resize($sSrc, $sDest, $iMaxWidth, $iMaxHeight)
    {
        $sCommand = "/usr/bin/convert -size {$iMaxWidth}x{$iMaxHeight} {$sSrc} -auto-orient -thumbnail {$iMaxWidth}x{$iMaxHeight} {$sDest}";
        @exec($sCommand);
        return file_exists($sDest);
    }

    function CropCenter($sSrc, $sDest, $iWidth, $iHeight)
    {

        $ims = getimagesize($sSrc);
		$sourceWidth  = $ims[0];
		$sourceHeight = $ims[1];

		$sourceOrientation = $sourceWidth >= $sourceHeight ? 'landscape' : 'portrait';
		$destOrientation   = $iWidth >= $iHeight ? 'landscape' : 'portrait';

		// коэффициенты отношения исходного размера к требуемому
		$scaleWidth = $sourceWidth / $iWidth;
		$scaleHeight = $sourceHeight / $iHeight;
		// выбираем наименьший коэффициент
		$scale = $scaleWidth >= $scaleHeight ? $scaleHeight : $scaleWidth;
		if ($scale < 1) { $scale = 1; }
		// размеры для кропа исходного изображения
		$cropWidth  = $iWidth * intval($scale);
		$cropHeight = $iHeight * intval($scale);

		// отступы от левого верхнего угла
		//$topMargin = intval(($sourceWidth - $cropWidth)/2);
		//$leftMargin = intval(($sourceHeight - $cropHeight)/2);
		$topMargin = 0;
		$leftMargin = 0;

        /*$iMaxWidth	= $iWidth;
        $iMaxHeight	= $iHeight;

        if ($ims[0] > $ims[1])
        {
            $fRatio = $ims[0] / $ims[1];
            $iMaxWidth	= intval($iMaxHeight * $fRatio);
        }
        else
        {
            $fRatio = $ims[1] / $ims[0];
            $iMaxHeight	= intval($iMaxWidth * $fRatio);
        }*/

		if (!is_dir(dirname($sDest))) {
			mkdir(dirname($sDest), 0777, true);
		}

        //$sCommand = "/usr/bin/convert -size {$ims[0]}x{$ims[1]} {$sSrc} -filter Blackman -modulate 110,102,100 -sharpen 1x1 -enhance -thumbnail {$iWidth}x{$iHeight} -gravity center -crop {$ims[0]}x{$ims[1]}+0+0 -auto-orient +repage {$sDest}";
		$sCommand = "/usr/bin/convert -quality 90 -crop {$cropWidth}x{$cropHeight}+{$topMargin}+{$leftMargin} -resize {$iWidth}x{$iHeight} -filter Blackman -modulate 110,102,100 -sharpen 1x1 -enhance {$sSrc} {$sDest}";
        echo($sCommand."\n");
        @exec($sCommand);
		chmod($sDest, 0777);

        return file_exists($sDest);
    }


}

?>