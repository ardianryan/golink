<?php 
namespace Models;

use Gem;
use Core\Model;
use Core\Helper;

class Url extends Model {

	/**	
	 * Table Name
	 */
	public static $_table = DBprefix.'url';

    /**
     * Return only Links
     *
     * @version 6.0
     * @return void
     */
    public static function recent(){
        return parent::whereRaw("(alias != '' OR alias IS NOT NULL OR custom != '' OR custom IS NOT NULL)")->where('archived', 0)->whereNull('qrid')->whereNull('profileid')->whereRaw('(expiry IS NULL OR expiry > DATE(CURDATE()))');
    }
    /**
     * Archived Links
     *
     * @version 6.0
     * @return void
     */
    public static function archived(){
        return parent::whereRaw("(alias != '' OR alias IS NOT NULL OR custom != '' OR custom IS NOT NULL)")->where('archived', 1)->whereNull('qrid')->whereNull('profileid')->whereRaw('(expiry IS NULL OR expiry > DATE(CURDATE()))');
    } 

    /**
     * Expired
     *
     * @version 6.0
     * @return void
     */
    public static function expired(){
        return parent::whereRaw("(alias != '' OR alias IS NOT NULL OR custom != '' OR custom IS NOT NULL)")->where('archived', 0)->whereNull('qrid')->whereNull('profileid')->whereRaw('expiry < DATE(CURDATE())');
    }
    /**
     * Get Channels
     *
     * @version 6.4
     * @return void
     */
    public function channels(){
        if($channels = \Core\DB::tochannels()->join(DBprefix.'channels', [DBprefix.'tochannels.channelid' , '=', DBprefix.'channels.id'])->where(DBprefix.'tochannels.itemid', $this->id)->where('type', 'links')->findMany()) return $channels;

        return false;
    }
}