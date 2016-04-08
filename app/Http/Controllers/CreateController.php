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
			$hostId = Auth::id();
			$tags = Input::get('tags');
			$participantInput = Input::get('participants');
			$json = "";

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


			//TODO:  Create the JSON based on participants list
			$players = preg_split("/[\s,]+/", $participantList);
			shuffle($players);
			$playersSafe = $players;
			$num_of_players = sizeof($players);

			$insertArray = array();
			
			if($num_of_players<=1){ //no games can be done
				return Redirect::to("/create/");
			}
			elseif(($num_of_players & ($num_of_players-1))==0){  //num_of_players is power of two
				$cur_depth = 0;
				$cur_game = 0;
				$num_of_players = sizeof($players);
				$counter = 0;
				$temp_dep = 'depth'.$cur_depth;
				$json_array[$temp_dep] = array();
				while($counter<$num_of_players){
				  $pow = (int)(floor(log($num_of_players, 2)));
				  $temp_game = 'game'.$cur_game;
				  $p0 = $players[$counter];
				  //
				  $participant = array();
				  $participant['participantId'] = $counter;
				  $participant['name'] = $players[$counter];
				  $participant['gameStatus'] = 0;
				  $participant['depthId'] = 0;
				  $participant['gameId'] = floor($counter / 2);
				  $participant['participantSide'] = 0;
				  $insertArray[] = $participant;
				  //
				  $counter++;
				  $p1 = $players[$counter];
				  //
				  $participant = array();
				  $participant['participantId'] = $counter;
				  $participant['name'] = $players[$counter];
				  $participant['gameStatus'] = 0;
				  $participant['depthId'] = 0;
				  $participant['gameId'] = floor($counter / 2);
				  $participant['participantSide'] = 1;
				  $insertArray[] = $participant;				  
				  //
				  
				  
				  $json_array[$temp_dep][$temp_game] = array(
					'p0' => $p0,
					'p1' => $p1
				  );
				  $counter++;
				  $cur_game++;
				}
				$cur_depth++;
				$num_of_players = $num_of_players/2;

				while($cur_depth<$pow){
					$temp_dep = 'depth'.$cur_depth;
					$json_array[$temp_dep] = array();
					$counter = 0;
					$cur_game = 0;
					while($counter<$num_of_players){
						$temp_game = 'game'.$cur_game;
						$p0 = '';//'game'.$counter;
						$counter++;
						$p1 = '';//'game'.$counter;
						$json_array[$temp_dep][$temp_game] = array(
							'p0' => $p0,
							'p1' => $p1
							);
						$counter++;
						$cur_game++;
					}
					$cur_depth++;
					$num_of_players = $num_of_players/2;

					$temp_dep = 'depth'.$cur_depth;
					$json_array[$temp_dep] = array(
						'winner' => ''//changed line
						);
				}
			}
			else{  //num_of_players is not power of two
				$pow = (int)(floor(log($num_of_players, 2)));
				$remain = $num_of_players - pow(2, $pow);
				$first_round_players = array();
				for($i = 0; $i < (2 * $remain); $i++){
				  $cur = array_pop($players);
				  array_push($first_round_players, $cur);
				}
				for($i = 0; $i < $remain; $i++){
				  //$temp = "game".$i;
				  $temp = "";
				  array_splice($players, (1+2*$i), 0,  $temp);
				}

				$json_array = array(
				  'depth0' => array()
				);

				$cur_game = 0;
				while(!empty($first_round_players)){
				  $temp = 'game'.$cur_game;
				  $p0 = array_pop($first_round_players);
						//
						$participantId = array_search($p0, $playersSafe);
						//if($p0 != "" && $participantId != null){
							$participant = array();
							$participant['participantId'] = $participantId;
							$participant['name'] = $p0;
							$participant['gameStatus'] = 0;
							$participant['depthId'] = 0;
							$participant['gameId'] = $cur_game;//floor($participant['participantId'] / 2);
							$participant['participantSide'] = 0;
							$insertArray[] = $participant;
						//}
						//
				  $p1 = array_pop($first_round_players);
						//
						$participantId = array_search($p1, $playersSafe);
						//if($p1 != "" && $participantId != null){
							$participant = array();
							$participant['participantId'] = $participantId;
							$participant['name'] = $p1;
							$participant['gameStatus'] = 0;
							$participant['depthId'] = 0;
							$participant['gameId'] = $cur_game;//floor($participant['participantId'] / 2);
							$participant['participantSide'] = 1;
							$insertArray[] = $participant;
						//}
						//
				  $json_array['depth0'][$temp] = array(
					'p0' => $p0,
					'p1' => $p1
				  );
				  $cur_game++;
				}

				$cur_depth = 1;
				$cur_game = 0;
				$num_of_players = sizeof($players);
				$counter = 0;
				$temp_dep = 'depth'.$cur_depth;
				$json_array[$temp_dep] = array();
				while($counter<$num_of_players){//handling depth1 (second depth)
						$temp_game = 'game'.$cur_game;
						$p0 = $players[$counter];
						//dd($players);
						//
						
						if($p0 != "" && in_array($p0, $playersSafe)){
							//$hasP1 = true;
							$participantId = array_search($p0, $playersSafe);
							$participant = array();
							$participant['participantId'] = $participantId;
							$participant['name'] = $playersSafe[$participantId];
							$participant['gameStatus'] = 1;
							$participant['depthId'] = 1;
							$participant['gameId'] = $cur_game;//floor($participantId / 2);
							$participant['participantSide'] = 0;
							$insertArray[] = $participant;
						}
						//
						$counter++;
						$p1 = $players[$counter];
						//
						
						if($p1 != "" && in_array($p1, $playersSafe)){
							//$hasP2 = true;
							$participant = array();
							$participantId = array_search($p1, $playersSafe);
							$participant['participantId'] = $participantId;
							$participant['name'] = $playersSafe[$participantId];
							$participant['gameStatus'] = 1;
							$participant['depthId'] = 1;
							$participant['gameId'] = $cur_game;//floor($participantId / 2);
							$participant['participantSide'] = 1;
							$insertArray[] = $participant;
						}
						//
						
						
					$json_array[$temp_dep][$temp_game] = array(
						'p0' => $p0,
						'p1' => $p1
					);
					$counter++;
					$cur_game++;
				}
				
				$cur_depth++;//Handling depth2 (second depth) and onward now:
				$num_of_players = $num_of_players/2;

				while($cur_depth<=$pow){
				  $temp_dep = 'depth'.$cur_depth;
				  $json_array[$temp_dep] = array();
				  $counter = 0;
				  $cur_game = 0;
				  while($counter<$num_of_players){
					$temp_game = 'game'.$cur_game;
					$p0 = '';//'game'.$counter;
					$counter++;
					$p1 = '';//'game'.$counter;
					$json_array[$temp_dep][$temp_game] = array(
					  'p0' => $p0,
					  'p1' => $p1
					);
					$counter++;
					$cur_game++;
				  }
				  $cur_depth++;
				  $num_of_players = $num_of_players/2;
				}

				$temp_dep = 'depth'.$cur_depth;
				$json_array[$temp_dep] = array(
				  'winner' => ''//changed line
				);
			}

			$json = json_encode($json_array);

			DB::table('tournaments')->insert(array('name'=>$name, 'description'=>$description, 'hostId'=>$hostId, 'participantList'=>$participantList, 'tags'=>$tags, 'brackets'=>$json));
			$acketId = DB::getPdo()->lastInsertId();
			
			foreach($insertArray as &$participant){
				$participant['tournamentId'] = $acketId;
			}
			unset($participant);

			DB::table('participants')->insert($insertArray);
		
			return Redirect::to("/acket/" . $acketId);
		}
	}
	
	public function removeEmptyStrings($str){
		return (strlen($str) >= 1); 
	}
	
}
