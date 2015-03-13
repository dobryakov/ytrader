<?php

define('PRODUCTION', (boolean) (PHP_OS == 'Linux'));
if (!PRODUCTION) { $start_time = microtime(true); }

// change the following paths if necessary
$yii=dirname(__FILE__).'/../framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
defined('DEFAULT_CACHE_TIME') or define('DEFAULT_CACHE_TIME', PRODUCTION ? 300 : 15);
defined('DEFAULT_CLICKS_CACHE') or define('DEFAULT_CLICKS_CACHE', PRODUCTION ? 300 : 15);
defined('DEFAULT_USERTAGS_CACHE') or define('DEFAULT_USERTAGS_CACHE', PRODUCTION ? 3600 : 120);
defined('DEFAULT_TAGPAGE_CACHE') or define('DEFAULT_TAGPAGE_CACHE', PRODUCTION ? 12 * DEFAULT_CACHE_TIME : DEFAULT_CACHE_TIME);
defined('DEFAULT_TAGGALLERIES_CACHE') or define('DEFAULT_TAGGALLERIES_CACHE', PRODUCTION ? 12 * DEFAULT_CACHE_TIME : DEFAULT_CACHE_TIME);
defined('ALLOW_LAST_MODIFIED') or define('ALLOW_LAST_MODIFIED', PRODUCTION ? true : false);
defined('TARGET') or define('TARGET', PRODUCTION ? "_blank" : "");
defined('STATIC_HOST_MAX_NUMBER') or define('STATIC_HOST_MAX_NUMBER', PRODUCTION ? 5 : false);
//defined('RANK_MIN_SHOWS') or define('RANK_MIN_SHOWS', 5);
//defined('RANK_MIN_CLICKS') or define('RANK_MIN_CLICKS', 2);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
Yii::createWebApplication($config)->run();

if (!PRODUCTION) { echo ("generation time: ".(microtime(true) - $start_time)); }