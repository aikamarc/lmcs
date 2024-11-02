// VARIABLES

let score = 0;
let pb = 0;

let skin_left  = "";
let skin_right = "";

$(document).ready(function() {
    loadSkin('l');
    loadSkin('r');
});

function loadSkin(side) {
    $.ajax({
        url: '/getSkin',
        type: 'POST',
        headers: {  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')  },
        data : {
            side,
        }
    })
    .done(function(response) {
        $(`#preview_skin_${side}`).html(response);
    });
}

let isRevealing = false;

function selectSkin(side) {

    if(isRevealing == false) {
        isRevealing = true;

        let skinL = $('#preview_skin_l').find('.skin-card').attr('data-skin');
        let skinR = $('#preview_skin_r').find('.skin-card').attr('data-skin');

        $.ajax({
            url: '/selectSkin',
            type: 'POST',
            headers: {  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')  },
            data:
            {
                side,
                skinL,
                skinR,
            },
        })
        .done(function(response) {
            // Animation reveal prix
            let float_l = 0.00;
            let target_l = response.skinL;
            let increment_l = (target_l - float_l) / 100; // Moins d'étapes pour une animation plus rapide
            $('#preview_skin_l').find('.data-skin-img').append(`<div id="counter_reveal_l" class="counter-reveal" style="font-size: 1em;">${float_l.toFixed(2)} $</div>`);
            let interval_l = setInterval(() => {
                float_l += increment_l;
                let scale = 1 + (float_l / target_l) * 0.5; // Ajuste le facteur d'échelle
                if (float_l >= target_l) {
                    float_l = target_l;
                    clearInterval(interval_l);
                }
                $('#counter_reveal_l').html(float_l.toFixed(2) + " $").css('font-size', `${scale}em`); // Applique le grossissement
            }, 10);

            let float_r = 0.00;
            let target_r = response.skinR;
            let increment_r = (target_r - float_r) / 100;
            $('#preview_skin_r').find('.data-skin-img').append(`<div id="counter_reveal_r" class="counter-reveal" style="font-size: 1em;">${float_r.toFixed(2)} $</div>`);
            let interval_r = setInterval(() => {
                float_r += increment_r;
                let scale = 1 + (float_r / target_r) * 0.5;
                if (float_r >= target_r) {
                    float_r = target_r;
                    clearInterval(interval_r);
                }
                $('#counter_reveal_r').html(float_r.toFixed(2) + " $").css('font-size', `${scale}em`);
            }, 10);

            // Result
            setTimeout(() => {
                if(response.result == "W") { score++;   }
                else                       {
                    if(pb > score) {
                        pb = score;
                        $('#best_score').html(pb);

                        $.ajax({
                            url: '/updatePb',
                            type: 'POST',
                            headers: {  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')  },
                            data: {
                                pb,
                            },
                        })
                        .done(function(response) {
                            $("#steam-card").load(" #steam-card > *");
                        });
                    }
                    score = 0;
                }

                $('#score').html(score);

                startNewGame();
            }, 1500);
        });
    }
}

function startNewGame() {
    $('#preview_skin_l').css('opacity', '0%');
    $('#preview_skin_r').css('opacity', '0%');

    setTimeout(() => {
        loadSkin('l');
        loadSkin('r');
        $('#counter_reveal_l').remove();
        $('#counter_reveal_r').remove();
    }, 500);

    setTimeout(() => {
        $('#preview_skin_l').css('opacity', '100%');
        $('#preview_skin_r').css('opacity', '100%');
        isRevealing = false;
    }, 1000);
}
