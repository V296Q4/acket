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
    
	$json = $tournament['brackets'];
    $brackets = json_decode($json, true);
    $num_of_depths = sizeof($brackets);




    $num_of_players = sizeof($participantNames);
    if(($num_of_players & ($num_of_players-1))!=0){
      $pow = (int)(floor(log($num_of_players, 2)));
      $remain = $num_of_players - pow(2, $pow);
      if((2*$remain)>(pow(2, $pow))){
        $svg_height = (pow(2, $pow))/2;
        $svg_height =  50+($svg_height*50+($svg_height-1)*65);
      }else{
        $svg_height = (pow(2, $pow))/2;
        $svg_height =  50+($svg_height*50+($svg_height-1)*20);
      }
    }else{
      $svg_height =  50+(($num_of_players/2)*50+(($num_of_players/2)-1)*20);
    }
    $svg_width = 100 + 200*$num_of_depths;
    $div_height = ($svg_height<500) ? ($svg_height+27):500;

    $cords_array = array();

    $svg = '<div style="width: 900px; height: '.($div_height).'px; overflow:scroll; overflow-y:scroll;"><svg height="'.($svg_height).'" width="'.($svg_width).'"><g>';
    $svg = $svg.'<rect x="0" y="0" width="'.($svg_width).'" height="'.($svg_height).'" style="fill:#efefef;" />';


    if(($num_of_players & ($num_of_players-1))!=0){
      /*
      num of players is not power of 2
      */

      if(sizeof($brackets['depth0'])<=sizeof($brackets['depth1'])){
        //draw second round
        $cords_array[0] = array();
        $cords_array[1] = array();
        $second_round = $brackets['depth1'];
        $numOfSecondRoundGames = sizeof($second_round);
        $x_cord = 200;
        $y_cord = ($svg_height-($numOfSecondRoundGames*50+($numOfSecondRoundGames-1)*20))/2;
        if($y_cord<0) $y_cord = 50;
        foreach($second_round as $game){
          $p0 = $game['p0'];
          $p1 = $game['p1'];
          $svg = $svg.'<rect x="'.$x_cord.'" y="'.$y_cord.'" rx="5" ry="5" width="148" height="24" style="fill:#787878;stroke-width:1;stroke:#efefef;" />';
          $svg = $svg.'<text x="'.($x_cord+2).'" y="'.($y_cord+15).'" fill="white">'.$p0.'</text>';
          $cords_array[1][] = array(
            'pname' => $p0,
            'x' => $x_cord,
            'y' => $y_cord
          );
          $y_cord = $y_cord+25;
          $svg = $svg.'<rect x="'.$x_cord.'" y="'.$y_cord.'" rx="5" ry="5" width="148" height="24" style="fill:#787878;stroke-width:1;stroke:#efefef;" />';
          $svg = $svg.'<text x="'.($x_cord+2).'" y="'.($y_cord+15).'" fill="white">'.$p1.'</text>';
          $cords_array[1][] = array(
            'pname' => $p1,
            'x' => $x_cord,
            'y' => $y_cord
          );
          $y_cord = $y_cord+45;
        }

        //add the first round
        $first_round = $brackets['depth0'];
        $x_cord = 0;
        $temp_counter = 0;
        foreach($first_round as $game){
          $p0 = $game['p0'];
          $p1 = $game['p1'];
          $y_cord = $cords_array[1][$temp_counter]['y']+25;
          $svg = $svg.'<rect x="'.$x_cord.'" y="'.$y_cord.'" rx="5" ry="5" width="148" height="24" style="fill:#787878;stroke-width:1;stroke:#efefef;" />';
          $svg = $svg.'<text x="'.($x_cord+2).'" y="'.($y_cord+15).'" fill="white">'.$p0.'</text>';
          $cords_array[0][] = array(
            'pname' => $p0,
            'x' => $x_cord,
            'y' => $y_cord
          );
          $y_cord = $y_cord+25;
          $svg = $svg.'<rect x="'.$x_cord.'" y="'.$y_cord.'" rx="5" ry="5" width="148" height="24" style="fill:#787878;stroke-width:1;stroke:#efefef;" />';
          $svg = $svg.'<text x="'.($x_cord+2).'" y="'.($y_cord+15).'" fill="white">'.$p1.'</text>';
          $svg = $svg.'<polyline points="'.($x_cord+150).','.($y_cord).' '.($x_cord+175).','.($y_cord).' '.($x_cord+175).','.($y_cord-25).' '.($x_cord+200).','.($y_cord-25).'" style="fill:#efefef;stroke:black;stroke-width:2" />';
          $y_cord = $y_cord+45;
          $cords_array[0][] = array(
            'pname' => $p1,
            'x' => $x_cord,
            'y' => $y_cord
          );
          $temp_counter = $temp_counter+2;
        }

        //add the rest
        for($i=2; $i<$num_of_depths; $i++){
          $x_cord = $i*200;
          $cur_dep = 'depth'.$i;
          $cur_games = $brackets[$cur_dep];
          if($num_of_depths!=($i+1)){ //not the winner
            $cur_games = $brackets[$cur_dep];
            $temp_counter = 0;
            foreach($cur_games as $game){
              $temp_idx = 4*$temp_counter;
              $num1 = $cords_array[$i-1][$temp_idx]['y'];
              $num2 = $cords_array[$i-1][$temp_idx+3]['y']+25;
              $y_cord = ($num1 + ($num2-$num1)/2)-25;
              $p0 = $game['p0'];
              $p1 = $game['p1'];
              $svg = $svg.'<rect x="'.$x_cord.'" y="'.$y_cord.'" rx="5" ry="5" width="148" height="24" style="fill:#787878;stroke-width:1;stroke:#efefef;" />';
              $svg = $svg.'<text x="'.($x_cord+2).'" y="'.($y_cord+15).'" fill="white">'.$p0.'</text>';
              $cords_array[$i][] = array(
                'pname' => $p0,
                'x' => $x_cord,
                'y' => $y_cord
              );
              $y_cord = $y_cord+25;
              $svg = $svg.'<rect x="'.$x_cord.'" y="'.$y_cord.'" rx="5" ry="5" width="148" height="24" style="fill:#787878;stroke-width:1;stroke:#efefef;" />';
              $svg = $svg.'<text x="'.($x_cord+2).'" y="'.($y_cord+15).'" fill="white">'.$p1.'</text>';
              $svg = $svg.'<polyline points="'.($x_cord).','.($y_cord).' '.($x_cord-25).','.($y_cord).' '.($x_cord-25).','.($num1+25).' '.($x_cord-50).','.($num1+25).'" style="fill:#efefef;stroke:black;stroke-width:2" />';
              $svg = $svg.'<polyline points="'.($x_cord).','.($y_cord).' '.($x_cord-25).','.($y_cord).' '.($x_cord-25).','.($num2-25).' '.($x_cord-50).','.($num2-25).'" style="fill:#efefef;stroke:black;stroke-width:2" />';
              $cords_array[$i][] = array(
                'pname' => $p1,
                'x' => $x_cord,
                'y' => $y_cord
              );
              $temp_counter++;
            }
          }else{  //winner
            $winner = $brackets[$cur_dep]['winner'];
            $num1 = $cords_array[$i-1][0]['y'];
            $y_cord = $num1+(25/2);
            $svg = $svg.'<rect x="'.$x_cord.'" y="'.$y_cord.'" rx="5" ry="5" width="148" height="24" style="fill:#787878;stroke-width:1;stroke:#efefef;" />';
            $svg = $svg.'<text x="'.($x_cord+2).'" y="'.($y_cord+15).'" fill="white">'.$winner.'</text>';
            $svg = $svg.'<line x1="'.($x_cord).'" y1="'.($y_cord+12.5).'" x2="'.($x_cord-50).'" y2="'.($y_cord+12.5).'" style="stroke:black; stroke-width:2" />';
            $cords_array[$i][] = array(
              'pname' => $winner,
              'x' => $x_cord,
              'y' => $y_cord
            );
          }
        }
      }else{
        //draw second round
        $cords_array[0] = array();
        $cords_array[1] = array();
        $second_round = $brackets['depth1'];
        $numOfSecondRoundGames = sizeof($second_round);
        $x_cord = 200;
        $y_cord = ($svg_height-($numOfSecondRoundGames*50+($numOfSecondRoundGames-1)*65))/2;
        if($y_cord<0) $y_cord = 0;
        foreach($second_round as $game){
          $p0 = $game['p0'];
          $p1 = $game['p1'];
          $svg = $svg.'<rect x="'.$x_cord.'" y="'.$y_cord.'" rx="5" ry="5" width="148" height="24" style="fill:#787878;stroke-width:1;stroke:#efefef;" />';
          $svg = $svg.'<text x="'.($x_cord+2).'" y="'.($y_cord+15).'" fill="white">'.$p0.'</text>';
          $cords_array[1][] = array(
            'pname' => $p0,
            'x' => $x_cord,
            'y' => $y_cord
          );
          $y_cord = $y_cord+25;
          $svg = $svg.'<rect x="'.$x_cord.'" y="'.$y_cord.'" rx="5" ry="5" width="148" height="24" style="fill:#787878;stroke-width:1;stroke:#efefef;" />';
          $svg = $svg.'<text x="'.($x_cord+2).'" y="'.($y_cord+15).'" fill="white">'.$p1.'</text>';
          $cords_array[1][] = array(
            'pname' => $p1,
            'x' => $x_cord,
            'y' => $y_cord
          );
          $y_cord = $y_cord+90;
        }

        //draw first round
        $first_round = $brackets['depth0'];
        $x_cord = 0;
        $first_round_keys = array_keys($first_round);
        $key_idx = 0;
        $temp_counter = 0;

        while($key_idx<sizeof($first_round)){
          $p0 = $first_round[$first_round_keys[$key_idx]]['p0'];
          $p1 = $first_round[$first_round_keys[$key_idx]]['p1'];
          $p0_r2 = $cords_array[1][$temp_counter]['pname'];
          $p1_r2 = $cords_array[1][$temp_counter+1]['pname'];


          if(preg_match("/(^(game))(([0-9])+)/", $p0_r2) && preg_match("/(^(game))(([0-9])+)/", $p1_r2)){//changed this line
            $y_cord = $cords_array[1][$temp_counter]['y']-25;
            $svg = $svg.'<rect x="'.$x_cord.'" y="'.$y_cord.'" rx="5" ry="5" width="148" height="24" style="fill:#787878;stroke-width:1;stroke:#efefef;" />';
            $svg = $svg.'<text x="'.($x_cord+2).'" y="'.($y_cord+15).'" fill="white">'.$p0.'</text>';
            $cords_array[0][] = array(
              'pname' => $p0,
              'x' => $x_cord,
              'y' => $y_cord
            );
            $y_cord = $y_cord+25;
            $svg = $svg.'<rect x="'.$x_cord.'" y="'.$y_cord.'" rx="5" ry="5" width="148" height="24" style="fill:#787878;stroke-width:1;stroke:#efefef;" />';
            $svg = $svg.'<text x="'.($x_cord+2).'" y="'.($y_cord+15).'" fill="white">'.$p0.'</text>';
            $svg = $svg.'<polyline points="'.($x_cord+150).','.($y_cord).' '.($x_cord+175).','.($y_cord).' '.($x_cord+175).','.($y_cord+25).' '.($x_cord+200).','.($y_cord+25).'" style="fill:#efefef;stroke:black;stroke-width:2" />';
            $cords_array[0][] = array(
              'pname' => $p0,
              'x' => $x_cord,
              'y' => $y_cord
            );
            $y_cord = $y_cord+25;
            $key_idx++;
            $p0 = $first_round[$first_round_keys[$key_idx]]['p0'];
            $p1 = $first_round[$first_round_keys[$key_idx]]['p1'];
            $svg = $svg.'<rect x="'.$x_cord.'" y="'.$y_cord.'" rx="5" ry="5" width="148" height="24" style="fill:#787878;stroke-width:1;stroke:#efefef;" />';
            $svg = $svg.'<text x="'.($x_cord+2).'" y="'.($y_cord+15).'" fill="white">'.$p0.'</text>';
            $cords_array[0][] = array(
              'pname' => $p0,
              'x' => $x_cord,
              'y' => $y_cord
            );
            $y_cord = $y_cord+25;
            $svg = $svg.'<rect x="'.$x_cord.'" y="'.$y_cord.'" rx="5" ry="5" width="148" height="24" style="fill:#787878;stroke-width:1;stroke:#efefef;" />';
            $svg = $svg.'<text x="'.($x_cord+2).'" y="'.($y_cord+15).'" fill="white">'.$p0.'</text>';
            $svg = $svg.'<polyline points="'.($x_cord+150).','.($y_cord).' '.($x_cord+175).','.($y_cord).' '.($x_cord+175).','.($y_cord-25).' '.($x_cord+200).','.($y_cord-25).'" style="fill:#efefef;stroke:black;stroke-width:2" />';
            $cords_array[0][] = array(
              'pname' => $p0,
              'x' => $x_cord,
              'y' => $y_cord
            );
            $temp_counter = $temp_counter+2;
          }else{
            $y_cord = $cords_array[1][$temp_counter]['y']+25;
            $svg = $svg.'<rect x="'.$x_cord.'" y="'.$y_cord.'" rx="5" ry="5" width="148" height="24" style="fill:#787878;stroke-width:1;stroke:#efefef;" />';
            $svg = $svg.'<text x="'.($x_cord+2).'" y="'.($y_cord+15).'" fill="white">'.$p0.'</text>';
            $cords_array[0][] = array(
              'pname' => $p0,
              'x' => $x_cord,
              'y' => $y_cord
            );
            $y_cord = $y_cord+25;
            $svg = $svg.'<rect x="'.$x_cord.'" y="'.$y_cord.'" rx="5" ry="5" width="148" height="24" style="fill:#787878;stroke-width:1;stroke:#efefef;" />';
            $svg = $svg.'<text x="'.($x_cord+2).'" y="'.($y_cord+15).'" fill="white">'.$p1.'</text>';
            $svg = $svg.'<polyline points="'.($x_cord+150).','.($y_cord).' '.($x_cord+175).','.($y_cord).' '.($x_cord+175).','.($y_cord-25).' '.($x_cord+200).','.($y_cord-25).'" style="fill:#efefef;stroke:black;stroke-width:2" />';
            $y_cord = $y_cord+45;
            $cords_array[0][] = array(
              'pname' => $p1,
              'x' => $x_cord,
              'y' => $y_cord
            );
            $temp_counter = $temp_counter+2;
          }
          $key_idx++;
        }


        //draw the rest
        for($i=2; $i<$num_of_depths; $i++){
          $x_cord = $i*200;
          $cur_dep = 'depth'.$i;
          $cur_games = $brackets[$cur_dep];
          if($num_of_depths!=($i+1)){ //not the winner
            $cur_games = $brackets[$cur_dep];
            $temp_counter = 0;
            foreach($cur_games as $game){
              $temp_idx = 4*$temp_counter;
              $num1 = $cords_array[$i-1][$temp_idx]['y'];
              $num2 = $cords_array[$i-1][$temp_idx+3]['y']+25;
              $y_cord = ($num1 + ($num2-$num1)/2)-25;
              $p0 = $game['p0'];
              $p1 = $game['p1'];
              $svg = $svg.'<rect x="'.$x_cord.'" y="'.$y_cord.'" rx="5" ry="5" width="148" height="24" style="fill:#787878;stroke-width:1;stroke:#efefef;" />';
              $svg = $svg.'<text x="'.($x_cord+2).'" y="'.($y_cord+15).'" fill="white">'.$p0.'</text>';
              $cords_array[$i][] = array(
                'pname' => $p0,
                'x' => $x_cord,
                'y' => $y_cord
              );
              $y_cord = $y_cord+25;
              $svg = $svg.'<rect x="'.$x_cord.'" y="'.$y_cord.'" rx="5" ry="5" width="148" height="24" style="fill:#787878;stroke-width:1;stroke:#efefef;" />';
              $svg = $svg.'<text x="'.($x_cord+2).'" y="'.($y_cord+15).'" fill="white">'.$p1.'</text>';
              $svg = $svg.'<polyline points="'.($x_cord).','.($y_cord).' '.($x_cord-25).','.($y_cord).' '.($x_cord-25).','.($num1+25).' '.($x_cord-50).','.($num1+25).'" style="fill:#efefef;stroke:black;stroke-width:2" />';
              $svg = $svg.'<polyline points="'.($x_cord).','.($y_cord).' '.($x_cord-25).','.($y_cord).' '.($x_cord-25).','.($num2-25).' '.($x_cord-50).','.($num2-25).'" style="fill:#efefef;stroke:black;stroke-width:2" />';
              $cords_array[$i][] = array(
                'pname' => $p1,
                'x' => $x_cord,
                'y' => $y_cord
              );
              $temp_counter++;
            }
          }else{  //winner
            $winner = $brackets[$cur_dep]['winner'];
            $num1 = $cords_array[$i-1][0]['y'];
            $y_cord = $num1+(25/2);
            $svg = $svg.'<rect x="'.$x_cord.'" y="'.$y_cord.'" rx="5" ry="5" width="148" height="24" style="fill:#787878;stroke-width:1;stroke:#efefef;" />';
            $svg = $svg.'<text x="'.($x_cord+2).'" y="'.($y_cord+15).'" fill="white">'.$winner.'</text>';
            $svg = $svg.'<line x1="'.($x_cord).'" y1="'.($y_cord+12.5).'" x2="'.($x_cord-50).'" y2="'.($y_cord+12.5).'" style="stroke:black; stroke-width:2" />';
            $cords_array[$i][] = array(
              'pname' => $winner,
              'x' => $x_cord,
              'y' => $y_cord
            );
          }
        }
      }
    }else{
      /*
      num of players is power of 2
      so draw the first round boxes first
      then draw the rest
      */

      //draw first round
      $cords_array[0] = array();
      $second_round = $brackets['depth0'];
      $numOfSecondRoundGames = sizeof($second_round);
      $x_cord = 0;
      $y_cord = ($svg_height-($numOfSecondRoundGames*50+($numOfSecondRoundGames-1)*20))/2;
      if($y_cord<0) $y_cord = 0;
      foreach($second_round as $game){
        $p0 = $game['p0'];
        $p1 = $game['p1'];
        $svg = $svg.'<rect x="'.$x_cord.'" y="'.$y_cord.'" rx="5" ry="5" width="148" height="24" style="fill:#787878;stroke-width:1;stroke:#efefef;" />';
        $svg = $svg.'<text x="'.($x_cord+2).'" y="'.($y_cord+15).'" fill="white">'.$p0.'</text>';
        $cords_array[0][] = array(
          'pname' => $p0,
          'x' => $x_cord,
          'y' => $y_cord
        );
        $y_cord = $y_cord+25;
        $svg = $svg.'<rect x="'.$x_cord.'" y="'.$y_cord.'" rx="5" ry="5" width="148" height="24" style="fill:#787878;stroke-width:1;stroke:#efefef;" />';
        $svg = $svg.'<text x="'.($x_cord+2).'" y="'.($y_cord+15).'" fill="white">'.$p1.'</text>';
        $cords_array[0][] = array(
          'pname' => $p1,
          'x' => $x_cord,
          'y' => $y_cord
        );
        $y_cord = $y_cord+45;
      }
      
      //draw the rest
      for($i=1; $i<$num_of_depths; $i++){
        $x_cord = $i*200;
        $cur_dep = 'depth'.$i;
        $cur_games = $brackets[$cur_dep];
        if($num_of_depths!=($i+1)){ //not the winner
          $cur_games = $brackets[$cur_dep];
          $temp_counter = 0;
          foreach($cur_games as $game){
            $temp_idx = 4*$temp_counter;
            $num1 = $cords_array[$i-1][$temp_idx]['y'];
            $num2 = $cords_array[$i-1][$temp_idx+3]['y']+25;
            $y_cord = ($num1 + ($num2-$num1)/2)-25;
            $p0 = $game['p0'];
            $p1 = $game['p1'];
            $svg = $svg.'<rect x="'.$x_cord.'" y="'.$y_cord.'" rx="5" ry="5" width="148" height="24" style="fill:#787878;stroke-width:1;stroke:#efefef;" />';
            $svg = $svg.'<text x="'.($x_cord+2).'" y="'.($y_cord+15).'" fill="white">'.$p0.'</text>';
            $cords_array[$i][] = array(
              'pname' => $p0,
              'x' => $x_cord,
              'y' => $y_cord
            );
            $y_cord = $y_cord+25;
            $svg = $svg.'<rect x="'.$x_cord.'" y="'.$y_cord.'" rx="5" ry="5" width="148" height="24" style="fill:#787878;stroke-width:1;stroke:#efefef;" />';
            $svg = $svg.'<text x="'.($x_cord+2).'" y="'.($y_cord+15).'" fill="white">'.$p1.'</text>';
            $svg = $svg.'<polyline points="'.($x_cord).','.($y_cord).' '.($x_cord-25).','.($y_cord).' '.($x_cord-25).','.($num1+25).' '.($x_cord-50).','.($num1+25).'" style="fill:#efefef;stroke:black;stroke-width:2" />';
            $svg = $svg.'<polyline points="'.($x_cord).','.($y_cord).' '.($x_cord-25).','.($y_cord).' '.($x_cord-25).','.($num2-25).' '.($x_cord-50).','.($num2-25).'" style="fill:#efefef;stroke:black;stroke-width:2" />';
            $cords_array[$i][] = array(
              'pname' => $p1,
              'x' => $x_cord,
              'y' => $y_cord
            );
            $temp_counter++;
          }
        }else{  //winner
          $winner = $brackets[$cur_dep]['winner'];
          $num1 = $cords_array[$i-1][0]['y'];
          $y_cord = $num1+(25/2);
          $svg = $svg.'<rect x="'.$x_cord.'" y="'.$y_cord.'" rx="5" ry="5" width="148" height="24" style="fill:#787878;stroke-width:1;stroke:#efefef;" />';
          $svg = $svg.'<text x="'.($x_cord+2).'" y="'.($y_cord+15).'" fill="white">'.$winner.'</text>';
          $svg = $svg.'<line x1="'.($x_cord).'" y1="'.($y_cord+12.5).'" x2="'.($x_cord-50).'" y2="'.($y_cord+12.5).'" style="stroke:black; stroke-width:2" />';
          $cords_array[$i][] = array(
            'pname' => $winner,
            'x' => $x_cord,
            'y' => $y_cord
          );
        }
      }
    }

    $svg = $svg.'</g></svg></div>';

    $participantTable = '<table class="table table-striped"><thead><tr><th>Participant</th><th>Status</th></tr><tbody>';
    foreach($participantNames as $pName){
      $participantTable .= '<tr><td>' . $pName . '</td><tr>';
    }
    $participantTable .= '</tbody></table>';

		if(Auth::id() == $hostId){
			
			//select game, select winner, update button
			$activeParticipantsSideA = DB::table('participants')->where('tournamentId', $tournamentId)->where('gameStatus', 0)->where('participantSide', 0)->get();
			$activeParticipantsSideB = DB::table('participants')->where('tournamentId', $tournamentId)->where('gameStatus', 0)->where('participantSide', 1)->get();

			$matchListDropdown = "<select name='gameId'>";
			foreach($activeParticipantsSideA as $apa){
				foreach($activeParticipantsSideB as $apb){
					//$match = $apa['gameId'] . " and " . "";
					//dd($activeParticipantsSideB);
					if($apb['gameId'] == $apa['gameId']){
						$matchListDropdown .= "<option value='" . $apa['gameId'] . "'>" . $apa['name'] . " VS " . $apb['name'] . "</option>";
					}
				}
			}
			$matchListDropdown .= "</select>";
			$winnerListDropdown = '<select name="winnerSide"><option value="0">Player One</option><option value="1">Player Two</option></select>';
			$userIsHost = true;		
		}
		else{
			$matchListDropdown = '';
			$winnerListDropdown = '';
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
			"matchListDropdown" => $matchListDropdown,
			"winnerListDropdown" => $winnerListDropdown,
			"userIsHost" => $userIsHost
		]);	
		
        return $returnView;
    }

	public function UpdateAcket(){
		$tournamentId = Input::get('tournamentId');
		$tournament = DB::table('tournaments')->where('id', $tournamentId)->first();
		if($tournamentId !== null && Auth::check()){
			if(Input::get("1") !== null){//update tag/desc
				//TODO: look into this: http://www.easylaravelbook.com/blog/2015/08/26/passing-a-parameter-into-laravel-form-open/
				if(Auth::user()){//TODO: update this to check $hostId = Auth::id()
					$newDescription = Input::get('newDescription');
					$newTags = Input::get('newTags');
					$updateList = array();
					
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
				
				DB::table('tournaments')->where('id', $tournamentId)->update(['status'=>7]);
			}
			
			if(Input::get("3") !== null){//cancel
				//TODO: Confirmation box
				
				DB::table('tournaments')->where('id', $tournamentId)->update(['status'=>8]);
			}
			
			if(Input::get("4") !== null){//log match
			
				$gameId = Input::get('gameId');
				$winnerSide = Input::get('winnerSide');
				if($winnerSide == 0){
					$loserSide = 1;
				}
				else{
					$loserSide = 0;
				}
			
				$loser = DB::table('participants')->where('tournamentId', $tournament['id'])->where('gameId', $gameId)->where('participantSide', $loserSide)->first();
				$loserUpdate = array();
				$loserUpdate['gameStatus'] = 1;
				$loserUpdate['tournamentStatus'] = 1;
				DB::table('participants')->where('tournamentId', $tournament['id'])->where('participantId', $loser['participantId'])->update($loserUpdate);
				
				$winner = DB::table('participants')->where('tournamentId', $tournament['id'])->where('gameId', $gameId)->where('participantSide', $winnerSide)->first();
				$winnerUpdate = array();
				$winnerUpdate['wins'] = $winner['wins'] + 1;
				$winnerUpdate['depthId'] = $winner['depthId'] + 1;
				$winnerUpdate['gameId'] = floor($winner['gameId'] / 2);
				
				$playerCount = DB::table('participants')->where('tournamentId', $tournament['id'])->count();
				$tournamentStatus = $tournament['status'];
				$tournamentEndDate = '';
				
				
				if($tournament['status'] == 0){
					$tournamentStatus = 5;
				}
				
				$depthCount = DB::table('participants')->where('tournamentId', $tournament['id'])->where('gameStatus', 0)->count();
				$winnerUpdate['gameStatus'] = 1;
				//dd($depthCount);
				if($depthCount <= 2){//winner is last winner of round - round over, reset game statuses
					if($winner['depthId'] == (int)(floor(log($playerCount, 2))) - 1){//won the finals
						//dd((int)(floor(log($playerCount, 2))) - 1);
						//DB::table('tournaments')->where('id', $tournamentId)->update(['status' => 1, 'end_date' => date('Y-m-d H:i:s')]);
						$tournamentStatus = 1;
						$tournamentEndDate = date('Y-m-d H:i:s');
					}
					else if($winner['depthId'] == (int)(floor(log($playerCount, 2))) - 2){//semi finals done, start finals
						$tournamentStatus = 2;
					}
					else if($winner['depthId'] == (int)(floor(log($playerCount, 2))) - 3){//quarter finals done, start semi
						$tournamentStatus = 3;
					}
					else if($winner['depthId'] == (int)(floor(log($playerCount, 2))) - 4){//round of 8 done, start quarters
						$tournamentStatus = 4;
					}
					else{
						$tournamentStatus = 5;
					}
					DB::table('participants')->where('tournamentId', $tournament['id'])->where('tournamentStatus', 0)->update(['gameStatus' => 0]);
					$winnerUpdate['gameStatus'] = 0;
				}					
				
				
				$gameDepth = $winner['depthId'];
				$jsonString = $tournament['brackets'];
				$json = json_decode($jsonString, true);
				//if(array_key_exists('winner', $json['depth']['game']))]){
				//if(array_key_exists('winner', $json['depth' . ($winner['depthId'] + 1)]['game' . floor($winner['gameId'] / 2)])){
				if(!array_key_exists('game' . floor($winner['gameId'] / 2), $json['depth' . ($winner['depthId'] + 1)])){
					$json['depth' . ($winner['depthId'] + 1)]['winner'] = $winner['name'];
				}
				else{
					if($json['depth' . ($winner['depthId'] + 1)]['game' . floor($winner['gameId'] / 2)]['p0'] == ''){//empty position
						$json['depth' . ($winner['depthId'] + 1)]['game' . floor($winner['gameId'] / 2)]['p0'] = $winner['name'];
						$winnerUpdate['participantSide'] = 0;
					}
					else{//take the other side
						$json['depth' . ($winner['depthId'] + 1)]['game' . floor($winner['gameId'] / 2)]['p1'] = $winner['name'];
						$winnerUpdate['participantSide'] = 1;
					}
				}
				
				$brackets = json_encode($json);
				
				DB::table('participants')->where('tournamentId', $tournament['id'])->where('participantId', $winner['participantId'])->update($winnerUpdate);
				
				
				if($tournamentEndDate != ''){
					DB::table('tournaments')->where('id', $tournamentId)->update(['match_update_date' => date('Y-m-d H:i:s'),'brackets' => $brackets, 'gamesPlayed' => $tournament['gamesPlayed'] + 1, 'status' => $tournamentStatus, 'end_date' => $tournamentEndDate]);
				}
				else{
					DB::table('tournaments')->where('id', $tournamentId)->update(['match_update_date' => date('Y-m-d H:i:s'),'brackets' => $brackets, 'gamesPlayed' => $tournament['gamesPlayed'] + 1, 'status' => $tournamentStatus]);
				}
			}

			return Redirect::to('/acket/' . $tournamentId);
		}
	}
	
}