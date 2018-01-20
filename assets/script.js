$(document).ready(function(){
    var url = "broadband_usage.php";
    
    loadData();
    
    $("#refresh").click(function(e){
        $(this).addClass("working").addClass("fa-spin");                        
        loadData();
    });
    
    $("#verbose_desc").click(function() {
        $("#desc1").toggle();
        $("#desc2").toggle();
    });
    
    function loadData() {
        $.ajax(url, {
        
            beforeSend: function(){
                            $("#error").hide();
                        },
            success : function(data){
                        used = parseFloat(data.used);
                        remain = parseFloat(data.remain);
                        allotted = parseFloat(data.allotted);
                        remainPerc = ((remain/allotted)*100);
                        
                        isDataLow = remainPerc < 15;
                        lowDataIcon = isDataLow ? "<i class='fa fa-warning fa-fw red'></i> ":"<i class='fa fa-cloud-download fa-fw'></i> ";
                        lowDataClass = isDataLow ? "lowData":"normalData";
                        
                        $(".donut").addClass(lowDataClass);
                        
                        if (remainPerc < 50) {
                            $(".donut").addClass("inverted");
                            $(".donut .slice.one").css("transform", "rotate(270deg)");                            
                        }
                        $(".donut .slice.two").css("transform", "rotate("+Math.round(360*remainPerc/100)+"deg)");    
                        
                        
                        
                        
                        $(".donut .chart-center span").html(Math.round(remainPerc)+"%");

                        if( remain < 0 && used > allotted) {
	                        $("#desc1").html(lowDataIcon+"<strong>"+prettifyData(Math.abs(remain))+"</strong> used after <strong>"+prettifyData(allotted)+"</strong>");
	                        $("#desc2").html(lowDataIcon+"<strong>"+prettifyData(used)+"</strong> used in total");                        
                        } else {
	                        $("#desc1").html(lowDataIcon+"<strong>"+prettifyData(remain)+"</strong> remaining of <strong>"+prettifyData(allotted)+"</strong>");
	                        $("#desc2").html(lowDataIcon+"<strong>"+prettifyData(used)+"</strong> used of <strong>"+prettifyData(allotted)+"</strong>");                        
                        }

                        
                        
                        $("#package_name").html(data.packageName);
                        $("#package_expiry").html(data.packageExpiry);
                        $("#package_last_renewal").html(data.packageLastRenewal);
                        $("#login").html(data.login);
                        $("#time_taken").html(round(data.timeTaken,1)+"s");

                        $("#loading").fadeOut(500, function(){
                            $("#stats").fadeIn(500);
                        });                               
                        
                    },
            error   : function() {
                        $("#loading").hide();
                        $("#error").fadeIn(500);
                        
                    },
            complete:function(){$("#refresh").removeClass("working").removeClass("fa-spin");},
            timeout : 10000,
            dataType: "json"
            
            
            });
    }
    
});

round = function(number, precision) {
    var factor = Math.pow(10, precision);
    var tempNumber = number * factor;
    var roundedTempNumber = Math.round(tempNumber);
    return roundedTempNumber / factor;
};

prettifyData = function(mb) {
    if (Math.abs(mb) > 1024) {
        return round( mb / 1024, 1) + "GB";
    }
    return mb + "MB";
};