<?php

class RichcategoriesWidget extends CWidget
{
	public $model;
	public $rows;
	public $cols;
	public $content_type;
	public $_sections;
    public $show_header;

	public function init()
	{
		$controller = $this->owner;
        $content_types = ContenttypeController::listTypes();

        foreach ($content_types as $content_type) {

            $this->content_type = $content_type;
            $this->_sections = array();

            // проверяем, есть ли хотя бы одна секция с таким content_type,
			// чтобы не выводить никакого мусора если таковой нет
            if ($section = Section::model()->cache(DEFAULT_CACHE_TIME)->find("site_id=".$this->owner->CURRENT_SITE->id." AND content_type & ".$this->content_type)) {

                // рисуем шаблон
                $this->render('richcategories', array(
                    "controller" => $controller,
                    "content_type" => $content_type,
                    "rows" => $this->rows,
                    "cols" => $this->cols,
                    "header" => ucfirst(ContenttypeController::getString($content_type)).':',
                    "show_header" => (boolean) $this->show_header,
					/*"cropprofile" => Cropprofile::model()->find("section_id=".$section->id." AND assignment & ".Cropprofile::ASSIGN_FACE),*/
                ));

            }

        }

	}

	public function getNext()
	{
		if (!$this->_sections) {
			$this->_sections = Section::model()->cache(DEFAULT_CACHE_TIME)->findAll("site_id=".$this->owner->CURRENT_SITE->id." AND content_type & ".$this->content_type." AND galleries_count > 0 ORDER BY name");
			$section = reset($this->_sections);
		} else {
			$section = next($this->_sections);
		}
		return $section;
	}

	public function run()
	{
	}
}

?>