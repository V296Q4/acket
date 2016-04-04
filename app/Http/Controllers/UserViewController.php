<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use View; 
use DB;
use Auth;
use Input;
use Redirect;

class UserViewController extends Controller{

    public function index($id){
		$user = DB::table('users')->where('id', $id)->first();
		if($user != null){
			$name = $user['name'];
			$description = $user['description'];
			$acketListTable = '';

			$created_at = $user["created_at"];
			$updated_at = $user["updated_at"];
			
			$acketCount = DB::table('tournaments')->where("hostId", $id)->count();
			if($acketCount == 1){
				$acketCountString = $acketCount . " Acket Hosted:";
			}
			else if($acketCount == 0){
				$acketCountString = "No Ackets Hosted.";
			}
			else{
				$acketCountString = $acketCount . " Ackets Hosted:";
			}
			
			$ownedTournaments = DB::table('tournaments')->where("hostId", $id)->orderBy('updated_date', 'desc')->take(30)->get();
			$acketListTable = '<table class="table table-striped"><thead><tr><th>Acket Name</th><th>Description</th><th>Status</th><th>Participants</th></tr></thead><tbody>';
			foreach($ownedTournaments as $tournament){
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
				$acketListTable .= '<tr><td><a href="/acket/' . $tournament["id"] . '">' . $tournament["name"] . '</a></td><td>' . $tournament["description"] . '</td><td>' . $statusString . '</td><td>' . $participantCount . '</td></tr>';
			}
			$acketListTable .= '</tbody></table>';
					$returnView = View::make('userView')->with([
				"name" => $name,
				"created_at" => $created_at,
				"updated_at" => $updated_at,
				"acketCountString" => $acketCountString,
				"description" => $description,
				"table" => $acketListTable
			]);
		}
		else{
			$returnView = View::make('userView')->with([
				"name" => 'User not found.',
				"created_at" => '',
				"updated_at" => '',
				"acketCountString" => '',
				"description" => 'User not found.',
				"table" => '',
				"invalidUserId" => true
			]);
		}
		

			
        return $returnView;
    }

	
}
