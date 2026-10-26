<?php
namespace Traits;

use Core\Request;
use Core\DB;
use Core\Helper;
use User\Overlay;

trait Teams {
    /**
     * Permissions
     *
     * @version 7.5     
     * @return void
     */
	public static function permissions(){
        $list = [
            'links' => [
                'available' => true,
                'name' => e("Links"),
                'permissions' => [
                    'create' => e("Create Links"),
                    'edit' => e("Edit Links"),
                    'delete' => e("Delete Links"),
                ]
            ],
            'qr' => [
                'available' => (user()->has("qr") !== false),
                'name' => e("QR Codes"),
                'permissions' => [
                    'create' => e("Create QR"),
                    'edit' => e("Edit QR"),
                    'delete' => e("Delete QR"),
                ]
            ],
            'bio' => [
                'available' => (user()->has("bio") !== false),
                'name' => e("Bio Pages"),
                'permissions' => [
                    'create' => e("Create Bio"),
                    'edit' => e("Edit Bio"),
                    'delete' => e("Delete Bio"),
                ]
            ],
            'splash' => [
                'available' => (user()->has("splash") !== false),
                'name' => e("Custom Splash"),
                'permissions' => [
                    'create' => e("Create Splash"),
                    'edit' => e("Edit Splash"),
                    'delete' => e("Delete Splash"),
                ]
            ],
            'overlay' => [
                'available' => (user()->has("overlay") !== false),
                'name' => e("CTA Overlay"),
                'permissions' => [
                    'create' => e("Create Overlay"),
                    'edit' => e("Edit Overlay"),
                    'delete' => e("Delete Overlay"),
                ]
            ],
            'pixels' => [
                'available' => (user()->has("pixels") !== false),
                'name' => e("Tracking Pixels"),
                'permissions' => [
                    'create' => e("Create Pixels"),
                    'edit' => e("Edit Pixels"),
                    'delete' => e("Delete Pixels"),
                ]
            ],
            'domain' => [
                'available' => (user()->has("domain") !== false),
                'name' => e("Branded Domain"),
                'permissions' => [
                    'create' => e("Add Custom Domain"),
                    'delete' => e("Delete Custom Domain"),
                ]
            ],
            'bundle' => [
                'available' => (user()->has("bundle") !== false),
                'name' => e("Campaigns").'/'.e('Channels'),
                'permissions' => [
                    'create' => e("Create Campaigns").'/'.e('Channels'),
                    'edit' => e("Edit Campaigns").'/'.e('Channels'),
                    'delete' => e("Delete Campaigns").'/'.e('Channels'),
                ]
            ],
            'api' => [
                'available' => (user()->has("api") !== false),
                'name' => e('Developer API'),
                'permissions' => [
                    'create' => e("Developer API"),
                ]
            ],
            'export' => [
                'available' => (user()->has("export") !== false),
                'name' => e('Export'),
                'permissions' => [
                    'create' => e("Export Data"),
                ]
            ]
        ];

        if($extended = \Core\Plugin::dispatch('teampermissions.extend')){
			foreach($extended as $fn){
				$list = array_merge($list, $fn);
			}
		}

		return $list;
    }   
}