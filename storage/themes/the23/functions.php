<?php
use Core\Plugin;

Gem::post('/admin/themes/settings/update', 'themeSettings@update')->name('admin.themes.update')->middleware('Auth@admin');

Plugin::register('admin.theme.getoptions', [themeSettings::class, 'settings']);

Plugin::register('homemenu', [themeSettings::class, 'menu']);

if(isset(config('theme_config')->siteimage) && config('theme_config')->siteimage){
    \Core\View::set('image', uploads(config('theme_config')->siteimage));
}