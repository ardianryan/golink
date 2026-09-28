<?php
namespace Helpers;

use Core\View;
use Core\Response;
use Core\Request;
use Core\Helper;
use Core\Http;
use Core\DB;

final class Events {

    private $type = null;
    private $userid = null;
    private $planid = null;

    /**
     * Set event type
     *
     * @version 7.4.3
     * @return void
     */
    public static function for($type){
        $instance = new self();
        $instance->type = $type;
        return $instance;
    }

    /**
     * Set User id
     *
     * @version 7.4.3
     * @param [type] $id
     * @return void
     */
    public function user($id){
        $this->userid = $id;
        return $this;
    }

    /**
     * Set Plan Id
     *
     * @version 7.4.3
     * @param [type] $id
     * @return void
     */
    public function plan($id){
        $this->planid = $id;
        return $this;
    }
    /**
     * Log Event
     *
     * @version 7.4.3
     * @param [type] $data
     * @return void
     */
    public function log($data){

        if(!config('userlogging')) return false;

        $notification = DB::appevents()->create();

        $notification->type = $this->type;
        $notification->data = $data;
        $notification->userid = $this->userid;
        $notification->planid = $this->planid;
        $notification->created_at = Helper::dtime();
        $notification->expires_at = null;
        $notification->save();
    }
}