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
		$posted_date = $tournament['posted_date'];
		$updated_date = $tournament['updated_date'];
		$tags = $tournament['tags'];
		$description = $tournament['description'];
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

		$returnView = View::make('acketView')->with([
				"name" => $name,
				"tournamentId" => $tournamentId,
				"hostName" => $hostName,
				"hostId" => $hostId,
				"status" => $status,
				"posted_date" => $posted_date,
				"updated_date" => $updated_date,
				"tags" => $tags,
				"description" => $description,
				"participantTable" => $participantTable,
				"svg" => $svg
			]);
			
        return $returnView;
    }

	
}
