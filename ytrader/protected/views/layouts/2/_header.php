<div id="header">
	<a href="/"><?=$this->CURRENT_SITE->name?></a>
	&nbsp;|&nbsp;
	<a href="<?=Yii::app()->createUrl("section/view", array("id"=>$this->section_name))?>"><?=$this->section_name?>
	<?=ucfirst(ContenttypeController::getString(Section::model()->find("id=".$this->section_id)->content_type))?>
	</a>
</div>