<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use View; 
use DB;
use Input;

class BrowseController extends Controller{

    public function index(){
		
		if(Input::get('u') !== null){
			$host = DB::table('users')->where('name', Input::get('u'))->first();
			if($host != null){
				
				$activeTournaments = DB::table('tournaments')->where('hostId', $host['id'])->orderBy('match_update_date', 'desc')->take(100)->get();
				$title = "Browsing Ackets for Host: " . Input::get('u');
			}
			else{
				$title = "Host '" . Input::get('u') . "' does not exist.";
				$activeTournaments = null;
			}
		}
		else{
			$title = "Browsing Ackets";
			//$activeTournaments = DB::table('tournaments')->orderBy('posted_date', 'desc')->take(100)->get();
			$activeTournaments = DB::table('tournaments')->take(100)->get();
		}
		
		if($activeTournaments !== null){
			$table = '<table id="acketTable" class="table table-striped"><thead><tr><th>Acket Name</th><th>Host</th><th>Description</th><th>Posted Date</th><th>Status</th></tr></thead><tbody>';
			foreach($activeTournaments as $tournament){
				//DB::table('users')->where('id', $tournament["hostId"])->first();
				$posted_date = $tournament['posted_date'];
				$host = DB::table('users')->where('id', $tournament["hostId"])->first();
				$hostName = $host["name"];
				if(strlen($tournament['name']) > 30){
					$acketName = substr($tournament['name'],0, 30) . '...';
				}
				else{
					$acketName = $tournament['name'];
				}
				if(strlen($tournament['description']) > 50){
					$acketDescription = substr($tournament['description'], 0, 50) . '...';
				}
				else{
					$acketDescription = $tournament['description'];
				}
				$acketStatus = $tournament['status'];

				$participantList = $tournament['participantList'];
				$participantCount = substr_count($participantList, ",") + 1;
				
				$acketStatus = $tournament['status'];
				$statusString = '';
				switch($acketStatus){
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
						
					case 8:
						$statusString = 'Cancelled.';
						break;
					case 9:
						$statusString = 'Deleted.';
						break;
							
				}
				
				$table .= '<tr><td class="col-md-2"><a href="/acket/' . $tournament["id"] . '">' . $acketName . '</a></td><td class="col-md-1"><a href="/user/' . $tournament["hostId"] . '">' .  $hostName . '</a></td><td class="col-md-3">' . $acketDescription . '</td><td>' . $posted_date . '</td><td>'. $statusString.'</td></tr>';
			}
			$table .= '</tbody></table>';
		}
		else{
			$table = '';
		}
		$returnView = View::make('browse')->with([
			'title'=>$title,
			'table'=>$table
			]);
			
        return $returnView;
    }
}
