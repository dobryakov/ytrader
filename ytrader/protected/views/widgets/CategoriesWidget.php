<?php

class CategoriesWidget extends CWidget
{
	//public $model;
	public $rows;
	public $cols;
	public $content_type;
	public $content_type_only;
	public $_sections;
    public $show_header;
	public $table_css_class = 'categories_table';

	public function init()
	{
		$controller = $this->owner;

		/*foreach ($controller->CURRENT_SITE->sections as $section) {
			// проверяем битмаску content_type
			if ($section->content_type & $this->content_type) {
				$sections[] = $section;
			}
		}*/

		//$content_type_name = ContenttypeController::getString($section->content_type);
		// рисуем шаблон

		if (!$this->content_type_only) {
        	$content_types = ContenttypeController::listTypes();
		} else {
			$content_types = array($this->content_type_only);
		}

        foreach ($content_types as $content_type) {

            $this->content_type = $content_type;
            $this->_sections = array();

            if (Section::model()->cache(DEFAULT_CACHE_TIME)->find("site_id=".$this->owner->CURRENT_SITE->id." AND content_type & ".$this->content_type." ORDER BY name")) {

                $this->render('categories', array(
                    "controller" => $controller,
                    "rows" => $this->rows,
                    "cols" => $this->cols,
                    "header" => ucfirst(ContenttypeController::getString($content_type)).":",
                    "show_header" => (boolean) $this->show_header,
					"table_css_class" => $this->table_css_class,
                ));

            }

        }

	}

	public function getNext()
	{
		if (!$this->_sections) {
			$this->_sections = Section::model()->cache(DEFAULT_CACHE_TIME)->findAll("site_id=".$this->owner->CURRENT_SITE->id." AND galleries_count > 0 AND content_type & ".$this->content_type." ORDER BY name ASC");
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