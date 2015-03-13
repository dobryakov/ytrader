<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/<?=$this->CURRENT_SITE->id?>/main.css" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="container" id="page">

	<?php require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'_header.php'); ?>

	<div style="width: 1210px; margin: 0 auto 0 auto;">
		<?php echo $content; ?>
    </div>

	<?php require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'_footer.php'); ?>

</div>

</body>
</html>
