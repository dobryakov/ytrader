<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<!--link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/<?=$this->CURRENT_SITE->id?>/screen.css" media="screen, projection" /-->
	<!--link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/<?=$this->CURRENT_SITE->id?>/print.css" media="print" /-->
	<!--[if lt IE 8]>
	<!--link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/<?=$this->CURRENT_SITE->id?>/ie.css" media="screen, projection" /-->
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/<?=$this->CURRENT_SITE->id?>/main.css" />
	<!--link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/<?=$this->CURRENT_SITE->id?>/form.css" /-->

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="container" id="page">

	<div id="logo"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/<?=$this->CURRENT_SITE->id?>/logo.png" border="0" alt=""/></div>

	<?php echo $content; ?>

	<?php require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'_footer.php'); ?>

</div>

</body>
</html>
