<?php

namespace App\Http\Controllers;

use App\Models\DListeSkinCS2;
use App\Models\Users;
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

        return view('index', $view_params);
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

    public function getSkin(Request $request)
    {
        while(!isset($data['data'][0]['item']))
        {
            $randomSkin = DListeSkinCS2::inRandomOrder()->first();

            $url = "https://csfloat.com/api/v1/listings";
            $params = [
                'type' => 'buy_now',
                'market_hash_name' => $randomSkin->market_hash_name,
            ];
            $response = Http::withHeaders(['Authorization' => env('CSFLOAT_KEY')])->get($url, $params);

            if($response->status() == 429) {
                dd('TO_MANY_REQUEST');
            }

            $data = json_decode($response->body(), true);
        }

        $item = $data['data'][0]['item'];

        $randomSkin->price = number_format($data['data'][0]['price'] / 100, 2, '.', '');
        $randomSkin->save();

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

                if($user->personnal_best >= 90)  { $user->rank_id = 19;  }
                if($user->personnal_best >= 85)  { $user->rank_id = 18;  }
                if($user->personnal_best >= 80)  { $user->rank_id = 17;  }
                if($user->personnal_best >= 75)  { $user->rank_id = 16;  }
                if($user->personnal_best >= 70)  { $user->rank_id = 15;  }
                if($user->personnal_best >= 65)  { $user->rank_id = 14;  }
                if($user->personnal_best >= 60)  { $user->rank_id = 13;  }
                if($user->personnal_best >= 55)  { $user->rank_id = 12;  }
                if($user->personnal_best >= 50)  { $user->rank_id = 11;  }
                if($user->personnal_best >= 45)  { $user->rank_id = 10;  }
                if($user->personnal_best >= 40)  { $user->rank_id = 9;   }
                if($user->personnal_best >= 35)  { $user->rank_id = 8;   }
                if($user->personnal_best >= 30)  { $user->rank_id = 7;   }
                if($user->personnal_best >= 25)  { $user->rank_id = 6;   }
                if($user->personnal_best >= 4)   { $user->rank_id = 5;   }
                if($user->personnal_best >= 3)   { $user->rank_id = 4;   }
                if($user->personnal_best >= 2)   { $user->rank_id = 3;   }
                if($user->personnal_best >= 1)   { $user->rank_id = 2;   }

                Session::put('user', $user);

                $user->save();
            }
        }

        return response()->json(['result' => 'OK']);
    }
}
