<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use View; 
use DB;
use Auth;
use Input;
use Redirect;

class AcketViewController extends Controller{

    public function index($id){
		$tournament = DB::table('tournaments')->where('id', $id)->first();
		$name = $tournament['name'];
		$tournamentId = $tournament['id'];
		$hostId = $tournament['hostId'];
		$host = DB::table('users')->where('id', $hostId)->first();
		
		$hostName = $host['name'];
		$status = $tournament['status'];
		$statusString = '';
		switch($status){//TODO:find new place for this repeated code
			case 0:
				$statusString = 'Not yet started.';
				break;
			case 1:
				$statusString = 'Complete.';
				break;						
			case 2:
				$statusString = 'Finals in progress.';
				break;			
			case 3:
				$statusString = 'Semi finals in progress.';
				break;
			case 4:
				$statusString = 'Quarter finals in progress.';
				break;
			case 5:
				$statusString = 'Round in progress.';
				break;	
				
			case 6:
				$statusString = 'Unknown status.';
				break;
			case 7:
				$statusString = 'Closed.';
				break;
			case 8:
				$statusString = 'Cancelled.';
				break;
			case 9:
				$statusString = 'Deleted.';
				break;
		}
		$posted_date = $tournament['posted_date'];
		$match_update_date = $tournament['match_update_date'];
		if($match_update_date == $posted_date){
			$match_update_date = "Never";
		}
		$tags = $tournament['tags'];
		$description = $tournament['description'];
		$description_update_date = $tournament['description_update_date'];
		if($description_update_date == $posted_date){
			$description_update_date = null;
		}
		$participantList = $tournament['participantList'];
		$participantNames = explode(',', $participantList);
		$participantStatus = array();
		$brackets = $tournament['brackets'];
		$json = '';
		
		//TODO: Convert JSON to SVG
		//TODO: Save participant status (-2=no show, -1=disqualified, 0=still playing, [+number]=rank at tournament end) to $participantStatus
		//TODO: have a participants table?
		$svg = '<svg height="200" width="400"><rect width="380" height="180" style="fill:rgb(122,122,122)"; /></svg>';
		
		$participantTable = '<table class="table table-striped"><thead><tr><th>Participant</th><th>Status</th></tr><tbody>';
		foreach($participantNames as $pName){
			$participantTable .= '<tr><td>' . $pName . '</td><tr>';
		}
		$participantTable .= '</tbody></table>';

		if(Auth::id() == $hostId){
			
			//description, cancel, close
			
			//select game, select winner, update button
			//DB::table('participants')
			$matchListDropdown = "<select>";
			
			
			$matchListDropdown .= "</select>";
			$userIsHost = true;		
		}
		else{
			$userIsHost = false;
		}			
		$returnView = View::make('acketView')->with([
			"name" => $name,
			"tournamentId" => $tournamentId,
			"hostName" => $hostName,
			"hostId" => $hostId,
			"status" => $status,
			"statusString" => $statusString,
			"posted_date" => $posted_date,
			"match_update_date" => $match_update_date,
			"tags" => $tags,
			"description" => $description,
			"description_update_date" => $description_update_date,
			"participantTable" => $participantTable,
			"svg" => $svg,
			"userIsHost" => $userIsHost
		]);	
			
        return $returnView;
    }

	public function UpdateAcket(){
		$tournamentId = Input::get('tournamentId');
		if($tournamentId !== null && Auth::check()){
			if(Input::get("1") !== null){//update tag/desc
				//TODO: look into this: http://www.easylaravelbook.com/blog/2015/08/26/passing-a-parameter-into-laravel-form-open/
				if(Auth::user()){//TODO: update this to check $hostId = Auth::id()
					$newDescription = Input::get('newDescription');
					$newTags = Input::get('newTags');
					
					
					$updateList = array();
					
					$tournament = DB::table('tournaments')->where('id', $tournamentId)->first();
					if($tournament['description'] != $newDescription){
						$updateList['description'] = $newDescription;
						$updateList['description_update_date'] = date('Y-m-d H:i:s');
					}
					if($tournament['tags'] != $newTags){
						$updateList['tags'] = $newTags;
					}
					if(count($updateList) >= 1){
						DB::table('tournaments')->where('id', $tournamentId)->update($updateList);
					}
				}			
			}
			
			if(Input::get("2") !== null){//close
				//TODO: Confirmation box
				$tournament = DB::table('tournaments')->where('id', $tournamentId)->first();
				DB::table('tournaments')->where('id', $tournamentId)->update(['status'=>7]);
			}
			
			if(Input::get("3") !== null){//cancel
				//TODO: Confirmation box
				$tournament = DB::table('tournaments')->where('id', $tournamentId)->first();
				DB::table('tournaments')->where('id', $tournamentId)->update(['status'=>8]);
			}
			
			if(Input::get("4") !== null){//log match
				//TODO: If it is final match, set status to 1
				
				
			}
			
			return Redirect::to('/acket/' . $tournamentId);
		
		}
	}
	
}
