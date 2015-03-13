<table id="header_line" border="0">
	<tr id="header_line_row">
		<td id="header_line_left">
			<a href="/">Site Home</a>
		</td>
		<td id="header_line_right">
			<a href="http://www...com/contacts/" target="<?=TARGET?>">Contacts and support</a>
		</td>
	</tr>
</table>

<table id="header" border="0">
	<tr id="header_row">
		<td id="header_left">

			<a href="/"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/<?=$this->CURRENT_SITE->id?>/logo.jpg" border="0" alt="<?=$this->CURRENT_SITE->name?>" id="logo"></a>

		</td>
		<td id="header_middle">
			<h1><?=$this->pageTitle?></h1>
		</td>
		<td id="header_right">

			<? if (PRODUCTION) { ?>
			<div id="addthis" class="addthis_toolbox addthis_default_style addthis_32x32_style">
				<a class="addthis_button_preferred_1"></a>
				<a class="addthis_button_preferred_2"></a>
				<a class="addthis_button_preferred_3"></a>
				<a class="addthis_button_preferred_4"></a>
				<a class="addthis_button_compact"></a>
				<a class="addthis_counter addthis_bubble_style"></a>
			</div>
			<script type="text/javascript" src="http://s7.addthis.com/js/300/addthis_widget.js"></script>
			<? } ?>

		</td>
	</tr>
</table>

