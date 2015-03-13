<table id="footer" cellpadding="0" cellspacing="0">
	<tr>
		<td id="footer_left">
			<span style="font-size: 26px;"><a href="/">SITE NAME</a></span>
			<br/>
			Bookmark this!
			<div id="notify2257">
				18 U.S.C. 2257 Record-Keeping Requirements Compliance Statement.
				<a href="http://www...com/contacts/" target="<?=TARGET?>">Report abuse</a>
			</div>
		</td>
		<td id="footer_right">
			<?
			$this->widget('application.views.widgets.CategoriesWidget',array(
				/*'model'=>$model,*/
				'rows'=> $this->CURRENT_SITE->categories_rows ? $this->CURRENT_SITE->categories_rows : 10,
				'cols'=> $this->CURRENT_SITE->categories_cols ? $this->CURRENT_SITE->categories_cols : 6,
				'show_header'=>false,
				/*'content_type_only'=>ContenttypeController::CONTENT_TYPE_EMBEDS,*/
			));
			?>
		</td>
	</tr>
</table>
