<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use View; 
use DB;

class HomeController extends Controller{

    public function index(){
		//TODO: This is too expensive to do every time.  Look into storing results in JSON and updating it whenver necessary.  
		$completedAcketsCount = DB::table('tournaments')->where('status', 1)->count();
		$inProgressAcketsCount = DB::table('tournaments')->whereBetween('status', array(2,5))->count();
		$futureAcketsCount = DB::table('tournaments')->where('status', 0)->count();
		$totalGamesPlayedCount = DB::table('tournaments')->sum('gamesPlayed');
		$hostCount = DB::table('users')->count();
		
		$returnView = View::make('home')->with([
			'completedAcketsCount'=>$completedAcketsCount,
			'inProgressAcketsCount' => $inProgressAcketsCount,
			'futureAcketsCount' => $futureAcketsCount,
			'totalGamesPlayedCount' => $totalGamesPlayedCount,
			'hostCount' => $hostCount
			]);
			
        return $returnView;
    }
}
