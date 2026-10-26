<?php 
namespace Core;

use Core\Helper;
use Core\Request;

class Localization {	
    /**
     * Current Locale
     *
     * @version 1.0
     */
    private static $locale = null;
    /**
     * Default File
     *
     * @version 1.0
     */
    private static $defaultFile = 'app';
    /**
     * Locale Name
     *
     * @version 1.0
     */
    private static $name = null;
    /**
     * Locale Code
     *
     * @version 1.0
     */
    private static $code = null;
    /**
     * Locale Region
     *
     * @version 6.0
     */
    private static $region = "en_US";
    /**
     * RTL
     *
     * @version 6.0
     */
    private static $rtl = null;
    /**
     * Locale Author
     *
     * @version 1.0
     */
    private static $author = null;
    /**
     * Language List
     *
     * @version 1.0
     */
    private static $list;

    /**
     * Set App Locale
     *
     * @version 1.0
     * @param [type] $name
     * @return void
     */
    static function setLocale($locale){
        self::$locale = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$locale);
    }
    /**
     * Set Default File
     *
     * @version 1.0
     * @param [type] $name
     * @return void
     */
    static function setFile($name){
        self::$defaultFile = preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$name);
    }
    /**
     * Return current Locale
     *
     * @version 1.0
     * @return void
     */
    static function locale(){    
        if(file_exists(LOCALE."/".self::$locale."/".self::$defaultFile.".php")){
            return self::$locale;
        }
        return appConfig('app.language');
    }
    /**
     * Bootstrap
     *
     * @version 1.0
     */
    static function bootstrap(){
        
        if(is_null(self::$locale)){
            self::$locale = appConfig('app.language');
        }    

        if(file_exists(LOCALE."/".self::$locale."/".self::$defaultFile.".php")){
            $lang = include(LOCALE."/".self::$locale."/".self::$defaultFile.".php");
            self::$name = $lang['name'] ?? 'Unknown';
            self::$code = $lang['code'] ?? 'na';
            self::$region = $lang['region'] ?? 'na';
            self::$author = $lang['author'] ?? 'na';
            self::$rtl = $lang['rtl'] ?? false;
            self::$list = $lang['data'] ?? [];

            setlocale(LC_ALL, self::$code, self::$region);
        }
    }
    /**
     * Return variable
     *
     * @version 6.0
     * @param [type] $name
     * @return void
     */
    public static function get($name){
        return self::$$name;
    }
    /**
     * Update language
     *
     * @version 1.0
     * @return void
     */
    static function update(){
        if(file_exists(LOCALE."/".self::$locale."/".self::$defaultFile.".php")){
            $lang = include(LOCALE."/".self::$locale."/".self::$defaultFile.".php");
            self::$name = $lang['name'] ?? 'Unknown';
            self::$code = $lang['code'] ?? 'na';
            self::$region = $lang['region'] ?? 'na';
            self::$author = $lang['author'] ?? 'na';
            self::$rtl = $lang['rtl'] ?? false;
            self::$list = $lang['data'] ?? [];
            setlocale(LC_ALL, self::$code, self::$region);
        }
    }
    /**
     * Get Locale String
     *
     * @version 1.0
     * @param string $string
     * @param mixed $count
     * @param array $variables
     * @return string Translated string
     */
    static function translate($string, $count = null, $variables = []){    
        if(isset(self::$list[$string]) && !empty(self::$list[$string])){
            $e = self::$list[$string];
            if(!is_null($count) && is_numeric($count)) {
                [$single, $multiple] = explode("|", $e);
                $e = ($count == 1) ? $single : $multiple;
            }
            $string = $e;
        }

        if($variables){
            foreach($variables as $key => $var){
                if(!$var || empty($var)) continue;
                $var = str_replace('$', '&#x24;', $var);
                $string = preg_replace('#{\t?\s?\t?'.$key.'\t?\s?\t?}#', $var, $string);
            }
        }    

        if(!is_null($count) && is_numeric($count)) {
            [$single, $multiple] = explode("|", $string);
            $string = ($count == 1) ? $single : $multiple;
        }
        
        return $string;
    }
    /**
     * Get List of language files
     *
     * @version 1.0
     * @param $limit Number of language files
     * @return array list
     */
    static function list($limit = null){
        $list = [];
        $count = 0;
        foreach (new \RecursiveDirectoryIterator(LOCALE) as $path){
            if($limit && $count > $limit) break;
            
            if(!$path->isDir() || in_array($path->getFilename(), ['.','..'])) continue;
            
            $list[] = ['code' => $path->getFilename(), 'path' => $path->getPathname()];
            $count++;
        }
        return $list;
    }
    /**
     * List Array
     *
     * @version 7.0
     * @param [type] $limit
     * @return void
     */
    static function listArray($limit = null){
        $list = ['en'];
        $count = 0;
        foreach (new \RecursiveDirectoryIterator(LOCALE) as $path){
            if($limit && $count > $limit) break;
            
            if(!$path->isDir() || in_array($path->getFilename(), ['.','..'])) continue;
            
            $list[] = $path->getFilename();
            $count++;
        }
        return $list;
    }
    /**
     * Get List of languages with data
     *
     * @version 1.0
     * @return void
     */
    static function listInfo(){
        $list = [];
        foreach(self::list() as $data){
            if(!file_exists(LOCALE.'/'.$data['code'].'/app.php')) continue;
            $array = include(LOCALE.'/'.$data['code'].'/app.php');
            $list[] = $data + ['name' => $array['name'], 'author' => $array['author']];
        }

        return $list;
    }
    /**
     * Get Language String 
     *
     * @version 1.0
     * @param [type] $string
     * @return void
     */
    static function lang($string){
        if(strpos($string, '.')){
            $data = explode('.', $string);
            $code = $data[0];
            $file = $data[1];
        } else {
            $code = $string;
            $file = 'app';
        }
        if(!file_exists(LOCALE.'/'.$code.'/'.$file.'.php')) return false;
        return include(LOCALE.'/'.$code.'/'.$file.'.php');
    }
    /**
     * Does lang exists
     *
     * @version 6.2
     * @param [type] $lang
     * @return void
     */
    static function exists($lang){
        return file_exists(LOCALE.'/'.$lang.'/app.php');
    }

    /**
     * Generate Localized URL
     *
     * @version 1.0
     * @return void
     */
    static function localUrl($lang){
        $currentPath = trim(request()->path(), '/');
        
        if(in_array(explode('/', $currentPath)[0], \Core\Localization::listArray())) {
            $currentPath = substr($currentPath, strlen(explode('/', $currentPath)[0]) + 1);
        }
        
        return !empty($currentPath) ? url($lang['code'].'/'.$currentPath) : url($lang['code']);
    }
}