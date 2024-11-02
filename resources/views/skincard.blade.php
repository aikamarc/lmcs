<div class="skin-card" data-skin="{{ $steam_id }}" onclick="selectSkin('{{ $side }}')">
    <div class="data-skin-img @if(isset($skin['rarity_name'])) itemCard_{{ str_replace(' ', '', $skin['rarity_name']) }} @endif">
        @if(isset($skin['cs2_screenshot_id']))
            <img src="https://s.csfloat.com/m/{{ $skin['cs2_screenshot_id'] }}/playside.png?v=2">
        @elseif(isset($skin['icon_url']))
            <img src="https://community.cloudflare.steamstatic.com/economy/image/{{ $skin['icon_url'] }}">
        @else
            NULL
        @endif
    </div>
    <div class="data-skin-container">
        <div class="market_hash_name">
            <div>{{ $skin['market_hash_name'] }}</div>
            @if(isset($skin['paint_seed']))
                <div>{{ $skin['paint_seed'] }}</div>
            @endif
        </div>
        <div class="float_value">
            @if(isset($skin['float_value']))
                {{ $skin['float_value'] }}
            @endif
        </div>
    </div>
</div>
