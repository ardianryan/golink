<?php
namespace API;

use Core\Helper;
use Core\Request;
use Core\Response;
use Core\DB;
use Core\Auth;
use Models\User;

class Splash {

    /**
     * Check if is admin
     *
     * @version 6.0
     */
    public function __construct(){
        $user = Auth::ApiUser();

        if(!$user->keyCan('splash')) return die(Response::factory(['error' => true, 'message' => 'You do not have access to this endpoint with this API key.'])->json());

        if(!$user->has('splash')){
            die(Response::factory(['error' => 1, 'message' => 'You do not have permission to access this endpoint.'], 403)->json());
        }        
    }
    /**
     * List all plans
     *
     * @version 6.0
     * @param \Core\Request $request
     * @return void
     */
    public function get(Request $request){

        $splashes = [];

        $query = DB::splash()->where('userid', Auth::ApiUser()->id);

        $page = (int) currentpage();

        $limit = 15;

        if( $request->limit && \is_numeric($request->limit) ){                    
            $limit = (int) $request->limit;
        } 

        $total = $query->count();

        $results = $query->limit($limit)->offset(($page-1)*$limit)->findMany();
        
        if(($total % $limit)<>0) {
            $max = floor($total/$limit)+1;
        } else {
            $max = floor($total/$limit);
        }  
    
        foreach($results as $splash){

            $splashes[] = [
                "id" => $splash->id,
                "name" => $splash->name,
                "date" => $splash->date,
            ];
        }

        return Response::factory(['error' => 0, 'data' => ['result' => $total, 'perpage' => $limit, 'currentpage' => $page, 'nextpage' => $max == 0 || $page == $max ? null : $page+1, 'maxpage' => $max, 'splash' => $splashes]])->json();

    }
}