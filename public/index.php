<?php
/**
 * URL Shortener
 */

if (version_compare(phpversion(), '7.4', '<')) {
    die('PHP 7.4 is required to run this software. Please upgrade your PHP version.');
}

include(dirname(dirname(__FILE__)) . "/app/core.php");

Gem::Bootstrap();