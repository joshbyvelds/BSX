$(function(){
    const trading_fee = 9.95;
    const CANtoUSD = 0.805;
    const USDtoCAN = 1.2325;
    var gl_total_day = 0;
    var gl_total_ave = 0;

    $CP = $("input.current_price");
    $WCP = $("input.wizard_current_price");

    $CP.each(function(){
        updateCurrentPrice($(this).attr("data-id"), $(this).val());
        updateGLTotals();
    });

    $CP.on('change', function(){        
        updateCurrentPrice($(this).attr("data-id"), $(this).val());
        updateGLTotals();
    });

    $WCP.on('change', function(){        
        updateWizardCurrentPrice($(this).attr("data-id"), $(this).val());
    });

    function updateCurrentPrice(id, current){
        let day = parseFloat($("[data-opening=" + id + "]").html().replace("$", ""));
        let average = parseFloat($("[data-average=" + id + "]").html().replace("$", ""));
        let shares = $("[data-shares=" + id + "]").html();
        let currency = parseInt($("[data-currency=" + id + "]").html());
        let buys = $("[data-p-gl=" + id + "]").attr("data-buys");

        console.log(buys);

        $("[data-day-gl=" + id + "]").html("$" + (parseFloat(current) - day).toFixed(2));
        $("[data-ave-gl=" + id + "]").html("$" + (parseFloat(current) - average).toFixed(2));
        
        if(currency === 1){
            $("[data-p-gl=" + id + "]").html("$" + (((parseFloat(current) - average) * shares) - trading_fee - (trading_fee * buys)).toFixed(2) + "/" + ((((parseFloat(current) - average) * shares) - trading_fee - (trading_fee * buys)) * CANtoUSD).toFixed(2));
        }else{
            $("[data-p-gl=" + id + "]").html("$" + ((((parseFloat(current) - average) * shares) - trading_fee - (trading_fee * buys)) * USDtoCAN ).toFixed(2) + "/" + (((parseFloat(current) - average) * shares - (trading_fee * buys)) - trading_fee).toFixed(2));
        }

        $("[data-pp=" + id + "]").html("$" + (average + ((trading_fee + (trading_fee * buys)) / shares)).toFixed(2));

        //173.88
        $.post("/update", {id:id,price:current});
    }

    function updateGLTotals(){
        var gl_total_p_cdn = 0;
        var gl_total_p_usd = 0;
        var pp_total = 0;

        $(".gl_day:not(.no_total)").each(function(){
            gl_total_day += parseFloat($(this).html().replace("$", ""));
        });

        $(".gl_ave:not(.no_total)").each(function(){
            gl_total_ave += parseFloat($(this).html().replace("$", ""));
        });

        $(".gl_p:not(.no_total)").each(function(){
            var amounts = $(this).html().replace("$", "").split("/");
            console.log(amounts);
            gl_total_p_cdn += parseFloat(amounts[0].replace("$", ""));
            gl_total_p_usd += parseFloat(amounts[1].replace("$", ""));
        });

        $(".pp:not(.no_total)").each(function(){
            pp_total += parseFloat($(this).html().replace("$", ""));
        });


        $("#total_gl_day").html("$" + gl_total_day.toFixed(2));
        $("#total_gl_ave").html("$" + gl_total_ave.toFixed(2));
        $("#total_gl_p").html("$" + gl_total_p_cdn.toFixed(2) + "/$" + gl_total_p_usd.toFixed(2));
        $("#total_pp").html("$" + pp_total.toFixed(2));
    }

    function calculateStock(){
        
    }

    function updateWizardCurrentPrice(id, current){
        $.post("/wizards/updateplay", {id:id,price:current});
    }

});