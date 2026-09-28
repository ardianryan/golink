<?php
namespace Admin;

use Core\DB;
use Core\View;
use Core\Request;
use Core\Auth;
use Core\Response;
use Core\Helper;
use Core\Email;
use Helpers\Emails;
use Models\User;

class Verifications {	
    /**
     * Get verified page 
     *
     * @version 7.0
     * @return void
     */
    public function index(){
        if(!user()->hasRolePermission('settings.view')) {
            return Helper::redirect()->to(route('admin'))->with('danger', e('You do not have permission to view verifications.'));
        }

        View::set('title', e('Verifications'));

        $verifications = [];

        foreach(DB::verification()->orderByDesc('created_at')->paginate(15) as $verification){
            if(!$verification->user = User::where('id', $verification->userid)->first()) {
                $verification->delete();
                continue;
            }
            $verifications[] = $verification;
        }

        return View::with('admin.verifications.index', compact('verifications'))->extend('admin.layouts.main');
    }
    /**
     * Verify
     *
     * @version 6.5
     * @param integer $id
     * @return void
     */
    public function view(int $id){

        if(!$verification = DB::verification()->first($id)){
            return back()->with('danger', e('Verification request not found.'));
        }

        View::set('title', e('Verify User'));

        if(!$user = User::first($verification->userid)) return back()->with('danger', e('User does not exist.'));
                
        $user->address = json_decode($user->address);

        return View::with('admin.verifications.view', compact('verification', 'user'))->extend('admin.layouts.main');
    }
    /**
     * Process Verification
     *
     * @version 6.5
     * @param \Core\Request $request
     * @param integer $id
     * @return void
     */
    public function process(Request $request, int $id){
        
        if(!$verification = DB::verification()->first($id)){
            return back()->with('danger', e('Verification request not found.'));
        }

        if(!$user = User::first($verification->userid)) return back()->with('danger', e('User does not exist.'));
        
        $mailer = Emails::setup();

        $mailer->from([config('email'), config('sitename')])
               ->template(View::$path.'/email.php');

        if($request->action == "1"){
            $user->verified = 0;
            $user->save();

            $verification->status = 1;
            $verification->save();

            if($request->deletefile){
                // Check old directory first (backward compatibility), then new directory
                $oldPath = appConfig('app.storage')['files']['path'].'/'.$verification->file;
                $newPath = STORAGE.'/verifications/'.$verification->file;
                
                if(file_exists($oldPath)){
                    \Helpers\App::delete($oldPath);
                } elseif(file_exists($newPath)){
                    \Helpers\App::delete($newPath);
                }
            }

            $mailer->to($user->email)
                    ->send([
                        'subject' => e('Your verification request has been rejected'),
                        'message' => function($template, $data) {
                            if(config('logo')){
                                $title = '<img align="center" alt="'.config('title').'" border="0" class="center autowidth" src="'.uploads(config('logo')).'" style="text-decoration: none; -ms-interpolation-mode: bicubic; border: 0; height: auto; width: 100%; max-width: 166px; display: block;" title="'.config('title').'" width="166"/>';
                            } else {
                                $title = '<h3>'.config('title').'</h3>';
                            }
                            return Email::parse($template, ['content' => e('Thank you for taking the time to verify your account however at this moment we were not able to verify you. You can submit another document and request a new verification.'), 'brand' => $title]);
                        }
                    ]);

            return back()->with('success', e('Verification request has been rejected.'));
        }

        if($request->action == "2"){
            $user->verified = 1;
            $user->save();

            $verification->status = 2;
            $verification->save();

            if($request->deletefile){
                // Check old directory first (backward compatibility), then new directory
                $oldPath = appConfig('app.storage')['files']['path'].'/'.$verification->file;
                $newPath = STORAGE.'/verifications/'.$verification->file;
                
                if(file_exists($oldPath)){
                    \Helpers\App::delete($oldPath);
                } elseif(file_exists($newPath)){
                    \Helpers\App::delete($newPath);
                }
            }

            $mailer->to($user->email)
            ->send([
                'subject' => e('Your verification request has been approved!'),
                'message' => function($template, $data) {
                    if(config('logo')){
                        $title = '<img align="center" alt="'.config('title').'" border="0" class="center autowidth" src="'.uploads(config('logo')).'" style="text-decoration: none; -ms-interpolation-mode: bicubic; border: 0; height: auto; width: 100%; max-width: 166px; display: block;" title="'.config('title').'" width="166"/>';
                    } else {
                        $title = '<h3>'.config('title').'</h3>';
                    }
                    return Email::parse($template, ['content' => e('Thank you for taking the time to verify your account. Your verification request was approved!'), 'brand' => $title]);
                }
            ]);

            return back()->with('success', e('Verification request has been approved!'));
        }
    }
}