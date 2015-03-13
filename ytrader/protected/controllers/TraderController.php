<?php

/**
 * У каждого трейдера есть поля daily_in, daily_out
 * При каждом ине/ауте в них инкрементируются значения, чтобы иметь статистику в реальном времени,
 * но по крону (recount) эти счётчики пересчитываются по уникальным посетителям
 */
class TraderController extends Controller
{
	public function actionIndex($id)
	{
		// ничего не рендерим, просто отправляем человека в трейд
        // $id здесь означает section_id, чтобы знать по какой секции идёт трейд
		//$this->render('index');
        $trader = Trader::model()->getTopTrader($id);
        if ($trader) {

            // засчитываем out
            $out = new Out;
            $out->trader_id = $trader->id;
            $out->ip = ip2long($this->getRemoteAddr());
            $out->t = time();
            $out->save();

            // инкрементируем временный счётчик аутов для трейдера
            $trader->daily_out = $trader->daily_out + 1;
            $trader->save();

            // посылаем заголовок для редиректа
            return $trader->url;

        } else {
            // TODO: оформить exout traffic
            return false;
        }
	}

    public function actionIn()
    {
        // проверяем, является ли входящий запрос - визитом от трейдера
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];
            $query = parse_url($referer);
            $host = $query['host'];
            $host = str_replace('www.', '', $host);

			// может быть это наш же хост? тогда и в базу лезть не надо
			if ($host == $this->CURRENT_SITE->host) {
				return;
			}

            $host = mysql_escape_string($host);
            // ищем трейдера в базе
            $trader = Trader::model()->cache(DEFAULT_CACHE_TIME)->find("section_id=".$this->CURRENT_SECTION." AND host='".$host."'");
            if ($trader) {
                // инкрементируем счётчик
                $trader->daily_in = $trader->daily_in+1;
				$trader->save();
                // записываем in в статистику
                $in = new In;
                $in->trader_id = $trader->id;
                $in->ip = ip2long($this->getRemoteAddr());
                $in->t = time();
                $in->save();
            }
        }
    }

	public function actionOut($id)
	{
		$trader = Trader::model()->cache(DEFAULT_CACHE_TIME)->find("id=".intval($id));
		if ($trader) {
			// инкрементируем временный счётчик
			$trader->daily_out = $trader->daily_out + 1;
			$trader->save();
			// записываем out в статистику
			$out = new Out;
			$out->trader_id = $trader->id;
			$out->ip = ip2long($this->getRemoteAddr());
			$out->t = time();
			$out->save();
			// делаем редирект
			header("Location: ".$trader->url);
		}
	}

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}