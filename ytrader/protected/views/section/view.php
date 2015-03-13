<?php
/* @var $this SectionController */
/* @var $model Section */

$this->breadcrumbs=array(
	'Sections'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Section', 'url'=>array('index')),
	array('label'=>'Create Section', 'url'=>array('create')),
	array('label'=>'Update Section', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Section', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Section', 'url'=>array('admin')),
);
?>

<?php
if ($this->CURRENT_SECTION) {

		?>
		<table border="0" cellpadding="0" cellspacing="0" id="section_table">
			<tr>
				<td>
					<div id="face_thumbs">
						<?
							// блок тумб на морде
							$this->widget('application.views.widgets.FaceWidget',array(
								'model'=>$model,
								'rows' => $controller->CURRENT_SITE->section_rows ? $controller->CURRENT_SITE->section_rows : 30,
								'cols' => $controller->CURRENT_SITE->section_cols ? $controller->CURRENT_SITE->section_cols : 5,
							));
						?>
					</div>

					<div id="face_traders">
						<?php
							// топ трейдеров
							$this->widget('application.views.widgets.TraderstopWidget',array(
								'model'=>$model,
								'rows'=>1,
								'cols'=>3,
							));
						?>
					</div>
				</td>
				<td class="face_suggest_divider"></td>
				<td>
					<div id="sponsorlist_div">
						Best <?=$model->name?> pay sites:
						<?
						// список тематических спонсоров
						$this->widget('application.views.widgets.SponsorlistWidget',array(
							'model'=>$model,
							'rows' => isset($controller->CURRENT_SITE->sponsorlist_rows) && $controller->CURRENT_SITE->sponsorlist_rows ? $controller->CURRENT_SITE->sponsorlist_rows : 3,
							'cols' => isset($controller->CURRENT_SITE->sponsorlist_cols) && $controller->CURRENT_SITE->sponsorlist_cols ? $controller->CURRENT_SITE->sponsorlist_cols : 1,
						));
						?>
						<p>Free categories:</p>
						<style type="text/css">
							.section_categories_table td
							{
								padding-bottom: 10px;
							}
							.section_categories_table td a
							{
								font-size: 20px;
							}
						</style>
						<?
						$this->widget('application.views.widgets.CategoriesWidget',array(
							/*'model'=>$model,*/
							'rows'=> $this->CURRENT_SITE->categories_rows ? $this->CURRENT_SITE->categories_rows : 30,
							'cols'=> 1,
							'show_header'=>false,
							'table_css_class'=>'section_categories_table',
							/*'content_type_only'=>ContenttypeController::CONTENT_TYPE_EMBEDS,*/
						));
						?>
					</div>
				</td>
			</tr>
		</table>

		<?

}
?>