<?php

namespace App\Http\Controllers;

use App\Models\DListeSkinCS2;
use App\Models\Users;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    public function index()
    {
        $view_params = [];

        if(Session::has('user'))
        {
            $user = Users::find(Session::get('user')->id);
            Session::put('user', $user);
        }

        $view_params['countSkins'] = DListeSkinCS2::where('price', '!=', null)->count();

        return view('index', $view_params);
    }

    public function getSkin(Request $request)
    {
        $randomSkin = DListeSkinCS2::where('price', '!=', null)->inRandomOrder()->first();

        $item = [
            'rarity_name'       => $randomSkin->rarity_name,
            'cs2_screenshot_id' => $randomSkin->cs2_screenshot_id,
            'icon_url'          => $randomSkin->icon_url,
            'market_hash_name'  => $randomSkin->market_hash_name,
            'paint_seed'        => $randomSkin->paint_seed,
            'float_value'       => $randomSkin->float_value,
        ];

        $view_params = [];
        $view_params['skin'] = $item;
        $view_params['steam_id'] = $randomSkin->steam_id;
        $view_params['side'] = $request->side;

        return view('skincard', $view_params);
    }

    public function selectSkin(Request $request)
    {
        $skinL = DListeSkinCS2::where('steam_id', $request->skinL)->first()->price;
        $skinR = DListeSkinCS2::where('steam_id', $request->skinR)->first()->price;

        if($skinL == $skinR) {
            if($request->side == "l") { return response()->json(['result' => 'W',  'side' => $request->side, 'skinL' => $skinL, 'skinR' => $skinR ]); }
            else                      { return response()->json(['result' => 'W',  'side' => $request->side, 'skinL' => $skinL, 'skinR' => $skinR ]); }
        }
        elseif($skinL > $skinR) {
            if($request->side == "l") { return response()->json(['result' => 'W',  'side' => $request->side, 'skinL' => $skinL, 'skinR' => $skinR ]); }
            else                      { return response()->json(['result' => 'L',  'side' => $request->side, 'skinL' => $skinL, 'skinR' => $skinR ]); }
        }
        else {
            if($request->side == "r") { return response()->json(['result' => 'W',  'side' => $request->side, 'skinL' => $skinL, 'skinR' => $skinR ]); }
            else                      { return response()->json(['result' => 'L',  'side' => $request->side, 'skinL' => $skinL, 'skinR' => $skinR ]); }
        }
    }

    public function updatePb(Request $request)
    {
        if(Session::has('user'))
        {
            $user = Users::where('steamId', Session::get('user')->steamId)->first();
            if($request->pb > $user->personnal_best) {
                $user->personnal_best = $request->pb;

                if($user->personnal_best >= 100)     { $user->rank_id = 19;  }
                elseif($user->personnal_best >= 75)  { $user->rank_id = 18;  }
                elseif($user->personnal_best >= 60)  { $user->rank_id = 17;  }
                elseif($user->personnal_best >= 50)  { $user->rank_id = 16;  }
                elseif($user->personnal_best >= 40)  { $user->rank_id = 15;  }
                elseif($user->personnal_best >= 35)  { $user->rank_id = 14;  }
                elseif($user->personnal_best >= 30)  { $user->rank_id = 13;  }
                elseif($user->personnal_best >= 25)  { $user->rank_id = 12;  }
                elseif($user->personnal_best >= 20)  { $user->rank_id = 11;  }
                elseif($user->personnal_best >= 15)  { $user->rank_id = 10;  }
                elseif($user->personnal_best >= 12)  { $user->rank_id = 9;   }
                elseif($user->personnal_best >= 9)   { $user->rank_id = 8;   }
                elseif($user->personnal_best >= 7)   { $user->rank_id = 7;   }
                elseif($user->personnal_best >= 5)   { $user->rank_id = 6;   }
                elseif($user->personnal_best >= 4)   { $user->rank_id = 5;   }
                elseif($user->personnal_best >= 3)   { $user->rank_id = 4;   }
                elseif($user->personnal_best >= 2)   { $user->rank_id = 3;   }
                elseif($user->personnal_best >= 1)   { $user->rank_id = 2;   }

                $user->save();

                Session::put('user', $user);
            }
        }

        return response()->json(['result' => 'OK']);
    }

    public function loadSkin($token)
    {
        if($token != "MARCPASSOK") { dd('403'); }
        $file = file_get_contents(asset('cs2.json'));
        $data  = json_decode($file, true);

        foreach($data as $key => $value) {

            $checkItem = DListeSkinCS2::where('steam_id', $value)->first();

            if(!isset($checkItem))
            {
                $item = new DListeSkinCS2();
                $item->market_hash_name = $key;
                $item->steam_id = $value;
                $item->save();

                echo($item->market_hash_name . "<br>");
            }
        }

        dd('OK DONE');
    }

    public function loadPrice($token)
    {
        if($token != "MARCPASSOK") { dd('403'); }

        while(1 == 1)
        {
            $randomSkin = DListeSkinCS2::where('price', null)->inRandomOrder()->first();

            $url = "https://csfloat.com/api/v1/listings";
            $params = [
                'type' => 'buy_now',
                'market_hash_name' => $randomSkin->market_hash_name,
                'min_price' => 3,
            ];
            $response = Http::withHeaders(['Authorization' => env('CSFLOAT_KEY')])->get($url, $params);

            if($response->status() == 429) { dd('TO_MANY_REQUEST'); }

            $data = json_decode($response->body(), true);

            if(isset($data['data'][0]['price'])) {
                $randomSkin->price = number_format($data['data'][0]['price'] / 100, 2, '.', '');
                $randomSkin->rarity_name       = $data['data'][0]['item']['rarity_name'];
                $randomSkin->icon_url          = $data['data'][0]['item']['icon_url'];
                if(isset($data['data'][0]['item']['paint_seed']))        { $randomSkin->paint_seed        = $data['data'][0]['item']['paint_seed'];        }
                if(isset($data['data'][0]['item']['float_value']))       { $randomSkin->float_value       = $data['data'][0]['item']['float_value'];       }
                if(isset($data['data'][0]['item']['cs2_screenshot_id'])) { $randomSkin->cs2_screenshot_id = $data['data'][0]['item']['cs2_screenshot_id']; }
                $randomSkin->last_update = Carbon::now();
                $randomSkin->save();

                echo($randomSkin->market_hash_name . "<br>");
            }
        }
    }

}
