<?php

namespace App\Http\Controllers;

use App\Map;
use App\Container;
use App\ItemOfInterest;
use Illuminate\Support\Facades\Cache;

class MainController extends Controller
{
    public function home()
    {
        $maps = Cache::remember('maps', 120, function () {
            return Map::where('is_public', '=', 1)->get();
        });

        $itemsOfInterest = Cache::remember('items-of-interest', 120, function () {
            return ItemOfInterest::with('item')->get()->sortBy(function ($ioi) {
                return $ioi->item->name;
            })->groupBy('interest_type');
        });

        $revision = Cache::remember('revision', 2, function () {
            return substr(`git rev-parse HEAD`, 0, 7);
        });

        $thumbs = Cache::tags(['container'])->remember('thumbs', 0, function () {
            return json_encode(
                Container::where('is_public', '=', 1)->get()->keyBy('id')
            );
        });

        return view('main', compact('maps', 'itemsOfInterest', 'revision', 'thumbs'));
    }

    public function live()
    {
        return view('live');
    }
}
