<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

class SearchController extends Controller{

    public function __construct(){
        $this->middleware('auth');
    }

    public function Search(){
		
		$returnView = View::make('home')->with([
			'tournamentCount'=>1
			]);
			
        return returnView;
    }
}
