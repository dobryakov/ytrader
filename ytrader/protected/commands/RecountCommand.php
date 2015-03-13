<?php

class RecountCommand extends CConsoleCommand {
    
	public function run($args) {
        // тут делаем то, что нам нужно

		// проверяем время когда запускались в прошлый раз
		$recount_flag = dirname(__FILE__).'/recount.time';
		if (file_exists($recount_flag)) {
			$last_recount_time = file_get_contents($recount_flag);
			if ($last_recount_time > (time() - 60*15)) {
				return;
			}
		}
		file_put_contents($recount_flag, time());

        $sections = Section::model()->findAll();
		foreach ($sections as $section) {
			$galleries = $section->galleries;
			foreach ($galleries as $gallery) {
				$image = $gallery->bestimage;
                if ($image) {
                    //$rank = ($image->clicks) / (1+$image->shows);
                    // ранк лучшего изображения делим на возраст галереи в месяцах
                    $gallery_age_months = abs(round((time() - ($gallery->t_create)) / (60*60*24*30), 1));
                    $gallery->rank = $image->rank / ($gallery_age_months + 1);
                    echo "gallery ".$gallery->id." age ".$gallery_age_months." best image ".$image->id." clicks ".$image->clicks." shows ".$image->shows." rank ".$image->rank."\n";
                    $gallery->save();
                }
			}
		}

		$sponsorsites = Sponsorsite::model()->findAll();
		$max_content_rank = 0;
		foreach ($sponsorsites as $sponsorsite) {
			$ranks = array();
			foreach ($sponsorsite->galleries as $sponsorgallery) {
				if ($sponsorgallery->gallery) {
					$ranks[] = $sponsorgallery->gallery->rank;
				}
			}
			if ($ranks) {
				// среднее арифметическое
				$avg = array_sum($ranks) / count($ranks);
				echo ("sponsorsite ".$sponsorsite->id." content_rank ".$avg."\n");
				$sponsorsite->content_rank = $avg;
				if ($avg > $max_content_rank) {
					$max_content_rank = $avg;
				}
				$sponsorsite->save();
			}
		}

		if ($max_content_rank>0) {
			foreach ($sponsorsites as $sponsorsite) {
				$sponsorsite->stars = $sponsorsite->content_rank * 5 / $max_content_rank;
				$sponsorsite->save();
				echo ("sponsorsite ".$sponsorsite->id." stars ".$sponsorsite->stars."\n");
			}
		}

		$yesterday = time() - 60*60*24;

		$outs = Yii::app()->db->createCommand()
			->select('trader_id id,COUNT(DISTINCT(ip)) c')
			->from('out o')
			->group('trader_id')
			->limit(99999999)
			->queryAll();

		foreach ($outs as $out) {
			$trader = Trader::model()->find("id=".$out['id']);
			if ($trader) {
				$trader->daily_out = $out['c'];
				echo ("trader ".$trader->id." daily out ".$out['c']."\n");
				$trader->save();
			}
		}

		$ins = Yii::app()->db->createCommand()
			->select('trader_id id,COUNT(DISTINCT(ip)) c')
			->from('in i')
			->group('trader_id')
			->limit(99999999)
			->queryAll();

		foreach ($ins as $in) {
			$trader = Trader::model()->find("id=".$in['id']);
			if ($trader) {
				$trader->daily_in = $in['c'];
				echo ("trader ".$trader->id." daily in ".$in['c']."\n");
				$trader->save();
			}
		}

		Yii::app()->db->createCommand()->delete('in', 't<:t', array(':t'=>$yesterday));
		Yii::app()->db->createCommand()->delete('out', 't<:t', array(':t'=>$yesterday));

        $galleries = Gallery::model()->findAll("show_status=".Gallery::SHOW_STATUS_NEW);
        foreach ($galleries as $gallery) {
            if ($gallery->bestimage && $gallery->bestimage->rank) {
                $gallery->show_status = Gallery::SHOW_STATUS_ROTATED;
                echo "setting show status rotated to gallery ".$gallery->id."\n";
                $gallery->save();
            }
        }

        $galleries = Gallery::model()->findAll("show_status=".Gallery::SHOW_STATUS_ROTATED);
        foreach ($galleries as $gallery) {
            if ($gallery->bestimage && $gallery->bestimage->rank) {
                $gallery->rank = round($gallery->bestimage->rank, 8);
                echo "setting rank ".$gallery->rank." to gallery ".$gallery->id."\n";
                $gallery->save();
            }
        }

		// берём из очереди показы и считаем
		for ($i=0;$i<100;$i++) {
			$show = Queue::pull(Queue::IMAGE_INC_SHOWS);
			if (!$show) { break; }
			if (isset($show['image_id'])) {
				/** @var $image Image */
				/*$image = Image::model()->findByPk($show['image_id']);
				if ($image) {
					echo ("setting shows ".($image->shows + 1)." to image ".$image->id."\n");
					$image->shows = $image->shows + 1;
					$image->save();
				}*/
				Yii::app()->db->createCommand("UPDATE `image` SET shows=shows+1 WHERE `id`=" . intval($show['image_id']))->execute();
			}
		}

		// берём из очереди клики и считаем
		for ($i=0;$i<100;$i++) {
			$click = Queue::pull(Queue::IMAGE_INC_CLICKS);
			if (!$click) { break; }
			if (isset($click['image_id'])) {
				/** @var $image Image */
				/*$image = Image::model()->findByPk($click['image_id']);
				if ($image) {
					echo ("setting clicks ".($image->clicks + 1)." to image ".$image->id."\n");
					$image->clicks = $image->clicks + 1;
					$image->save();
				}*/
				Yii::app()->db->createCommand("UPDATE `image` SET clicks=clicks+1 WHERE `id`=" . intval($click['image_id']))->execute();
			}
		}

		// считаем количество галерей в секциях
		$sections = Section::model()->findAll();
		foreach ($sections as $section) {

				$q = Yii::app()->db->createCommand()
					->select('COUNT(DISTINCT(id)) c')
					->from('gallery g')
					->where('crop_status='.Gallery::STATUS_CROPPED.' AND section_id='.$section->id)
					->limit(99999999)
					->queryScalar();

				$section->galleries_count = intval($q);

				$q = Yii::app()->db->createCommand()
					->select('t_create t')
					->from('gallery g')
					->where('crop_status='.Gallery::STATUS_CROPPED.' AND section_id='.$section->id)
					->order('t_create DESC')
					->limit(1)
					->queryScalar();

				echo ("section ".$section->id." t ".intval($q)."\n");
				$section->t = intval($q);

				$section->save();

			}

	}
    
}

?>