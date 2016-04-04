<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use View; 
use DB;

class BrowseController extends Controller{

    public function index(){
		
		$activeTournaments = DB::table('tournaments')->orderBy('updated_date', 'desc')->take(20)->get();
		$table = '<table class="table table-striped"><thead><tr><th>Acket Name</th><th>Host</th><th>Description</th><th>Participants</th></tr></thead><tbody>';
		foreach($activeTournaments as $tournament){
			DB::table('users')->where('id', $tournament["hostId"])->first();
			$host = DB::table('users')->where('id', $tournament["hostId"])->first();
			$hostName = $host["name"];
			$participantList = $tournament['participantList'];
			$participantCount = substr_count($participantList, ",") + 1;
			$table .= '<tr><td><a href="/acket/' . $tournament["id"] . '">' . $tournament["name"] . '</a></td><td><a href="/user/' . $tournament["hostId"] . '">' .  $hostName . '</a></td><td>' . $tournament["description"] . '</td><td>' 
				 . $participantCount . '</td></tr>';
		}
		$table .= '</tbody></table>';
		
		$returnView = View::make('browse')->with([
			'table'=>$table
			]);
			
        return $returnView;
    }
}
