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
			$name = substr($name, 0, 100);
			$description = Input::get('description');
			$description = substr($description, 0, 1000);
			//TODO: Add a type variable for single/double elimination?
			$hostId = Auth::id();
			//$startDate = Input::get('startDate');
			//$endDate = Input::get('endDate');
			$tags = Input::get('tags');
			$participantInput = Input::get('participants');
			$isSeedRandomized = Input::get('seedRandomized');//keep this or no?
			$json = "";
			//TODO:  Create the JSON based on participants list
			
			//Participant List Handling:
			$participantsArray = explode(',', $participantInput);
			$participantsArray = array_slice($participantsArray, 0, 128);
			
			foreach($participantsArray as &$participant){
				$participant = trim($participant, " \t\n\r\0");
				$participant = substr($participant, 0, 64);
			}
			unset($participant);
			$participantsArray = array_filter($participantsArray, function($str){
				return (strlen($str) >= 1); 
			});
			$participantsArray = array_unique($participantsArray);
			
			$participantList = "";
			foreach($participantsArray as $participant){
				$participantList .= $participant . ',';
			}
			
			$participantList = substr($participantList, 0, strlen($participantList)-1);
			
			DB::table('tournaments')->insert(array('name'=>$name, 'description'=>$description, 'hostId'=>$hostId, 'participantList'=>$participantList, 'tags'=>$tags, 'brackets'=>$json));
			$acketId = DB::getPdo()->lastInsertId();
			
			$insertArray = array();
			$participantId = 0;
			foreach($participantsArray as $participant){
				$insert['tournamentId'] = $acketId;
				$insert['participantId'] = $participantId;
				$insert['name'] = $participant;
				$participantId++;
				$insertArray[] = $insert;
			}
			
			DB::table('participants')->insert($insertArray);
			
		}
		return Redirect::to("/acket/" . $acketId);
	}
	
	public function removeEmptyStrings($str){
		return (strlen($str) >= 1); 
	}
	
}
