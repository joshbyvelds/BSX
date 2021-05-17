$(function(){
    const trading_fee = 9.95;
    const CANtoUSD = 0.8139;
    const USDtoCAN = 1.1936;
    var gl_total_day = 0;
    var gl_total_ave = 0;
    var gl_total_p_cdn = 0;
    var gl_total_p_usd = 0;

    $CP = $("input.current_price");

    $CP.each(function(){
        updateCurrentPrice($(this).attr("data-id"), $(this).val());
        updateGLTotals();
    });

    $CP.on('change', function(){        
        updateCurrentPrice($(this).attr("data-id"), $(this).val());
        updateGLTotals();
    });

    function updateCurrentPrice(id, current){
        let day = parseFloat($("[data-opening=" + id + "]").html().replace("$", ""));
        let average = parseFloat($("[data-average=" + id + "]").html().replace("$", ""));
        let shares = $("[data-shares=" + id + "]").html();
        let currency = parseInt($("[data-currency=" + id + "]").html());

        $("[data-day-gl=" + id + "]").html("$" + (parseFloat(current) - day).toFixed(2));
        $("[data-ave-gl=" + id + "]").html("$" + (parseFloat(current) - average).toFixed(2));
        
        if(currency === 1){
            $("[data-p-gl=" + id + "]").html("$" + (((parseFloat(current) - average) * shares) - trading_fee).toFixed(2) + "/" + ((((parseFloat(current) - average) * shares) - trading_fee) * CANtoUSD).toFixed(2));
        }else{
            $("[data-p-gl=" + id + "]").html("$" + ((((parseFloat(current) - average) * shares) - trading_fee) * USDtoCAN ).toFixed(2) + "/" + (((parseFloat(current) - average) * shares) - trading_fee).toFixed(2));
        }
    }

    function updateGLTotals(){
        $(".gl_day").each(function(){
            gl_total_day += parseFloat($(this).html().replace("$", ""));
        });

        $(".gl_ave").each(function(){
            gl_total_ave += parseFloat($(this).html().replace("$", ""));
        });

        $(".gl_p").each(function(){
            var amounts = $(this).html().replace("$", "").split("/");
            console.log(amounts);
            gl_total_p_cdn += parseFloat(amounts[0].replace("$", ""));
            gl_total_p_usd += parseFloat(amounts[1].replace("$", ""));
        });

        $("#total_gl_day").html("$" + gl_total_day.toFixed(2));
        $("#total_gl_ave").html("$" + gl_total_ave.toFixed(2));
        $("#total_gl_p").html("$" + gl_total_p_cdn.toFixed(2) + "/$" + gl_total_p_usd.toFixed(2));
    }
});