<?php
/**
 * URL Shortener Configuration Template
 */

// Database Configuration
define('DBhost', '__HOST__');        // Your MySQL Host (usually localhost)
define('DBname', '__DB__');          // The database name where the data will be stored
define('DBuser', '__USER__');        // Your MySQL username
define('DBpassword', '__PASS__');    // Your MySQL password 
define('DBprefix', '__PRE__');       // Prefix for your tables

define('DBport', 3306);

// Base Path. Leave AUTO unless installed in a subfolder
define('BASEPATH', 'AUTO');

// Assets and CDN
define('USECDN', true);    
define('CDNASSETS', null);
define('CDNUPLOADS', null);

// Force Domain
define('FORCEURL', true);

// Server Timezone
define('TIMEZONE', 'UTC'); 

// Cache Configuration
define('CACHE', true);  

// Debug mode (0 = off, 1 = on)
define('DEBUG', 0);

// Security Phrases
define('AuthToken', '__KEY__');
define('EncryptionToken', '__ENC__');
define('PublicToken', '__PUB__');
