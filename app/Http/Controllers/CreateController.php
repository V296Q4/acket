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

  /*
  public function __construct(){
    $this->middleware('auth');
  }
  */

  public function index(){
    $returnView = View::make('create');
    return $returnView;
  }
	
  public function create(){
    //if(Auth::user()){
      $name = Input::get('tournamentName');
      $description = Input::get('description');
      //TODO: Add a type variable for single/double elimination?
      //ANSWER: NO
      $hostId = Auth::id();
      $startDate = Input::get('startDate');
      $endDate = Input::get('endDate');
      $tags = Input::get('tags');
      $participantList = Input::get('participants');
      $isSeedRandomized = Input::get('seedRandomized');//Answer: can do either way
      $json = "";





      //TODO:  Create the JSON based on participants list
      $players = preg_split("/[\s,]+/", $participantList);
      shuffle($players);
      $num_of_players = sizeof($players);

      if($num_of_players<=1){ //no games can be done
        return Redirect::to("/create/");
      }elseif(($num_of_players & ($num_of_players-1))==0){  //num_of_players is power of two
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
          $counter++;
          $p1 = $players[$counter];
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
            $p0 = 'game'.$counter;
            $counter++;
            $p1 = 'game'.$counter;
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
            'winner' => 'game0'
          );
        }
      }else{  //num_of_players is not power of two
        $pow = (int)(floor(log($num_of_players, 2)));
        $remain = $num_of_players - pow(2, $pow);
        $first_round_players = array();
        for($i=0; $i<(2*$remain); $i++){
          $cur = array_pop($players);
          array_push($first_round_players, $cur);
        }
        for($i=0; $i<$remain; $i++){
          $temp = "game".$i;
          array_splice($players, (1+2*$i), 0,  $temp);
        }

        $json_array = array(
          'depth0' => array()
        );

        $cur_game = 0;
        while(!empty($first_round_players)){
          $temp = 'game'.$cur_game;
          $p0 = array_pop($first_round_players);
          $p1 = array_pop($first_round_players);
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
        while($counter<$num_of_players){
          $temp_game = 'game'.$cur_game;
          $p0 = $players[$counter];
          $counter++;
          $p1 = $players[$counter];
          $json_array[$temp_dep][$temp_game] = array(
            'p0' => $p0,
            'p1' => $p1
          );
          $counter++;
          $cur_game++;
        }
        $cur_depth++;
        $num_of_players = $num_of_players/2;

        while($cur_depth<=$pow){
          $temp_dep = 'depth'.$cur_depth;
          $json_array[$temp_dep] = array();
          $counter = 0;
          $cur_game = 0;
          while($counter<$num_of_players){
            $temp_game = 'game'.$cur_game;
            $p0 = 'game'.$counter;
            $counter++;
            $p1 = 'game'.$counter;
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
          'winner' => 'game0'
        );
      }

      $json = json_encode($json_array);





      DB::table('tournaments')->insert(array('name'=>$name, 'description'=>$description, 'hostId'=>$hostId, 'start_date'=>$startDate, 'end_date'=>$endDate, 'participantList'=>$participantList, 'tags'=>$tags, 'brackets'=>$json));
      $id = DB::getPdo()->lastInsertId();
      return Redirect::to("/acket/" . $id);
    //}
  }
}
