<?php
	$this->widget('application.views.widgets.RichcategoriesWidget',array(
		'rows'=> $controller->CURRENT_SITE->face_rows ? $controller->CURRENT_SITE->face_rows : 20,
		'cols'=> $controller->CURRENT_SITE->face_cols ? $controller->CURRENT_SITE->face_cols : 7,
	));
?>