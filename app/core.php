<?php
/**
 * URL Shortener Core Engine
 */

// Framework Version
define("_VERSION", "1.1");

// InApp Safety
define("_INAPP", TRUE);
define("_STATE", "PROD");

// Path Constants
define("ROOT", dirname(dirname(__FILE__)));
define("APP", ROOT . '/app');
define("PUB", ROOT . "/public");
define("CORE", ROOT . "/core");

define("CONTROLLER", APP . "/controllers");
define("MODELS", APP . "/models");
define("MIDDLEWARE", APP . "/middleware");
define("LIBRARY", ROOT . "/vendor");
define("UPLOADS", PUB . "/content");
define("STORAGE", ROOT . "/storage");
define("LOGS", STORAGE . "/logs");
define("LOCALE", STORAGE . "/languages");
define("PLUGIN", STORAGE . "/plugins");

include(CORE . "/functions/core.php"); 
include(CORE . "/GemError.class.php");
include(LIBRARY . "/autoload.php");  
include(CORE . "/Gem.class.php");

Gem::preload();  

if (file_exists(ROOT . "/config.php")) {
    include(ROOT . "/config.php");
}

include(APP . "/routes.php");
