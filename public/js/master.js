$(function(){
    const trading_fee = 9.95;
    const CANtoUSD = 0.8139;
    const USDtoCAN = 1.1936;

    $CP = $("input.current_price");

    $CP.each(function(){
        updateCurrentPrice($(this).attr("data-id"), $(this).val());
    });

    $CP.on('change', function(){        
        updateCurrentPrice($(this).attr("data-id"), $(this).val());
    });

    function updateCurrentPrice(id, current){
        let day = parseFloat($("[data-opening=" + id + "]").html().replace("$", ""));
        let average = parseFloat($("[data-average=" + id + "]").html().replace("$", ""));
        let shares = $("[data-shares=" + id + "]").html();
        let currency = parseInt($("[data-currency=" + id + "]").html());

        $("[data-day-gl=" + id + "]").html((parseFloat(current) - day).toFixed(2));
        $("[data-ave-gl=" + id + "]").html((parseFloat(current) - average).toFixed(2));
        
        if(currency === 1){
            $("[data-p-gl=" + id + "]").html((((parseFloat(current) - average) * shares) - trading_fee).toFixed(2) + "/" + ((((parseFloat(current) - average) * shares) - trading_fee) * CANtoUSD).toFixed(2));
        }else{
            $("[data-p-gl=" + id + "]").html(((((parseFloat(current) - average) * shares) - trading_fee) * USDtoCAN ).toFixed(2) + "/" + (((parseFloat(current) - average) * shares) - trading_fee).toFixed(2));
        }
    }
});