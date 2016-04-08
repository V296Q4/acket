<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use View; 
use DB;
use Auth;
use Input;
use Redirect;

class SettingsController extends Controller{

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
		$user = DB::table('users')->where('id', Auth::id())->first();
		$name = $user['name'];
		$description = $user['description'];

		$settingsTable = '';

		$returnView = View::make('settings')->with([
				"name" => $name,
				"description" => $description,
				"settingsTable" => $settingsTable
			]);
			
        return $returnView;
    }

	public function updateSettings(){
		$description = Input::get('description');
		
		DB::table('users')->where('id', Auth::id())->update(['description' => $description]);
		return Redirect::to('/user/' . Auth::id());
	}
	
}
