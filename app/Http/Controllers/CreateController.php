<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use View; 
use DB;
use Auth;
use Input;
use Redirect;
use PDO;

class CreateController extends Controller{

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
		$returnView = View::make('create');
        return $returnView;
    }
	
	public function create(){
		if(Auth::user()){
			$name = Input::get('tournamentName');
			$description = Input::get('description');
			//TODO: Add a type variable for single/double elimination?
			$hostId = Auth::id();
			$startDate = Input::get('startDate');
			$endDate = Input::get('endDate');
			$tags = Input::get('tags');
			$participantList = Input::get('participants');
			$isSeedRandomized = Input::get('seedRandomized');//keep this or no?
			$json = "";
			//TODO:  Create the JSON based on participants list
			
			DB::table('tournaments')->insert(array('name'=>$name, 'description'=>$description, 'hostId'=>$hostId, 'start_date'=>$startDate, 'end_date'=>$endDate, 'participantList'=>$participantList, 'tags'=>$tags, 'brackets'=>$json));
			$id = DB::getPdo()->lastInsertId();
		}
		return Redirect::to("/acket/" . $id);
	}
	
}
