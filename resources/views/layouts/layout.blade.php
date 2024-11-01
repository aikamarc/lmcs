<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Less or More CS2</title>

        <meta name="csrf-token" content="{{ csrf_token() }}" />

        {{--  PUSH CSS  --}}
        <link href="{{ asset('css/style.css') }}" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" rel="stylesheet" type="text/css">

        {{--  PUSH JS  --}}
        <script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
        <script src="{{ asset('js/script.js') }}"></script>
    </head>
    <body>
        <div class="header">
            <img src="{{ asset('media/logo.png') }}">
            <div class="score">
                <div>
                    SCORE
                    <span id="score">0</span>
                </div>
                <div>
                    PERSONNAL BEST
                    <span id="best_score">
                        @if(Session::has('user'))
                            {{ Session::get('user')->personnal_best }}
                        @else
                            0
                        @endif
                    </span>
                </div>
            </div>
            <div class="steam">

                @if(Session::has('user'))
                    <div class="steam-card" id="steam-card">
                        <div class="rank" id="rank">
                            @if(Session::get('user')->rank_id > 1)
                                <img src="{{ asset('media/rank/'.Session::get('user')->rank_id.'.png') }}">
                            @endif
                        </div>
                        <img src="{{ Session::get('user')->avatar }}">
                    </div>
                @else
                    <a href="{{ route('auth') }}" class="login-steam">
                        <i class="fa-brands fa-steam"></i>
                        LOGIN
                    </a>
                @endif
            </div>
        </div>
