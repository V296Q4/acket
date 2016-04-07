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
    $json = $tournament['brackets'];
    $brackets = json_decode($json, true);
    $num_of_depths = sizeof($brackets);





    //TODO: Convert JSON to SVG
    //TODO: Save participant status (-2=no show, -1=disqualified, 0=still playing, [+number]=rank at tournament end) to $participantStatus
    //TODO: have a participants table? ANSWER: NO
    $cords_array = array();

    $svg = '<div width="400" height="200" style="overflow:auto;"><svg height="500" width="1600"><g>';
    $svg = $svg.'<rect x="0" y="0" width="1600" height="550" style="fill:#efefef;" />';

    /*
    box width = 148 + 2;
    box height = 23 + 2;
    initial horizontal gap = 50;
    initial vertical gap = 20;
    */


    $num_of_players = sizeof($participantNames);
    if(($num_of_players & ($num_of_players-1))!=0){
      /*
      num of players is not power of 2,
      so draw the second round boxes first
      then draw first round boxes and then draw the rest
      */

      //draw second round
      $cords_array[0] = array();
      $cords_array[1] = array();
      $second_round = $brackets['depth1'];
      $numOfSecondRoundGames = sizeof($second_round);
      $x_cord = 200;
      $y_cord = (550-($numOfSecondRoundGames*50+($numOfSecondRoundGames-1)*20))/2;
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
      $y_cord = (550-($numOfSecondRoundGames*50+($numOfSecondRoundGames-1)*20))/2;
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
