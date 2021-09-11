$(document).ready(function(){

    var current_img = null;
    var lightbox_type = null;
    var preload_prev = null;
    var preload_next = null;
    var clicked_nums = [];

    function preloadImage(url){
        var img = new Image();
        img.src = url;
    }

    var img = document.getElementById("popup_image");

    img.addEventListener("load", function(){
        $("#popup_image").show();
        $("#popup_image").fadeOut(0);
        $("#popup_image").fadeIn(500);
        $("#loading_container").hide();
        if (preload_prev != null){
            preloadImage(preload_prev);
            clicked_nums.push(preload_prev);
        }
        if (preload_next != null){
            preloadImage(preload_next);
            clicked_nums.push(preload_next);
        }
        clicked_nums.push(current_img);
        if (getCurrentNum() == 1){
            $("#prev").hide();
        } else if (getCurrentNum() != 1 && lightbox_type == "gallery") {
            $("#prev").show();
        }
    });

    $("#lightbox").hide();
    $("#loading_container").hide();
    $("#next").hide();
    $("#prev").hide();

    function xhrRequest(id){
        $.ajax({
            url: "../tracking/tracking.php",
            data:{
                gallery_view: id
            },
            type: "POST"
        });
    }

    function getCurrentNum(){
        var start = current_img.lastIndexOf("/");
        var end = current_img.indexOf(".jpg");
        var current_num = current_img.slice(start, end);
        return parseInt(current_num.replace("/", ""));
    }

    function getCurrentPath(){
        var end = current_img.lastIndexOf("/");
        var path = current_img.slice(0, end);
        return path;
    }

    $(".gallery_thumb").click(function(e){
        e.preventDefault();
        lightbox_type = "gallery";
        var path = $(this).parent().attr("href");
        current_img = $(this).parent().attr("href");
        xhrRequest(current_img);
        console.log(current_img);
        if (clicked_nums.indexOf(current_img) == -1){
            $("#loading_container").show();
            $("#loading_container").css("opacity", "1");
        }
        if (getCurrentNum() >= 3){
            preload_prev = getCurrentPath() + "/" + (getCurrentNum() - 1) + ".jpg";
            preload_prev2 = getCurrentPath() + "/" + (getCurrentNum() - 2) + ".jpg";
            preload_prev3 = getCurrentPath() + "/" + (getCurrentNum() - 3) + ".jpg";
        }
        setTimeout(function(){
            preloadImage(preload_prev2);
            preloadImage(preload_prev3);
        }, 800);
        preload_next = getCurrentPath() + "/" + (getCurrentNum() + 1) + ".jpg";
        preload_next2 = getCurrentPath() + "/" + (getCurrentNum() + 2) + ".jpg";
        preload_next3 = getCurrentPath() + "/" + (getCurrentNum() + 3) + ".jpg";
        setTimeout(function(){
            preloadImage(preload_next2);
            preloadImage(preload_next3);
        }, 800);
        img.src = ""; //Fixes bug that prevented previously viewed/cached images from loading when thumbnail clicked on iOS.
        $("#popup_image").attr("src", path);
        $("#lightbox").css("opacity", "1");
        $("#popup_image").css("opacity", "1").hide();
        $("#lightbox").show();
        $("#lightbox").fadeOut(0);
        $("#lightbox").fadeIn(500);
        $("#next").show();
        $("#next").css("opacity", "1");
        $("#prev").show();
        $("#prev").css("opacity", "1");
    });

    $("#prev").click(function(){
        $("#next").show();
        if (getCurrentNum() != 1){
            var prev_num = getCurrentNum() - 1;
            var prev_url = getCurrentPath() + "/" + prev_num + ".jpg";
            if (clicked_nums.indexOf(prev_url) == -1){
                $("#loading_container").show();
            }
            preload_prev = getCurrentPath() + "/" + (prev_num - 1) + ".jpg";
            preload_prev2 = getCurrentPath() + "/" + (prev_num - 2) + ".jpg";
            preload_prev3 = getCurrentPath() + "/" + (prev_num - 3) + ".jpg";
            setTimeout(function(){
                preloadImage(preload_prev2);
                preloadImage(preload_prev3);
            }, 800);
            preload_next = null;
            current_img = prev_url;
            xhrRequest(current_img);
            $("#popup_image").fadeOut(200);
            setTimeout(function(){
                $("#popup_image").attr("src", prev_url);
            }, 350);
        }
    });

    $("#next").click(function(){
        var next_num = getCurrentNum() + 1;
        var next_url = getCurrentPath() + "/" + next_num + ".jpg";
        if (clicked_nums.indexOf(next_url) == -1){
            $("#loading_container").show();
        }
        preload_next = getCurrentPath() + "/" + (next_num + 1) + ".jpg";
        preload_next2 = getCurrentPath() + "/" + (next_num + 2) + ".jpg";
        preload_next3 = getCurrentPath() + "/" + (next_num + 3) + ".jpg";
        setTimeout(function(){
            preloadImage(preload_next2);
            preloadImage(preload_next3);
        }, 800);
        preload_prev = null;
        current_img = next_url;
        xhrRequest(current_img);
        $("#popup_image").fadeOut(200);
        setTimeout(function(){
            $("#popup_image").attr("src", next_url);
        }, 250);
    });

var can_key = true;

document.onkeydown = function(e) {
    if (can_key == true){
        can_key = false;
        switch(e.which) {
            case 37: //Left
            $("#prev").trigger("click");
            setTimeout(function(){
                can_key = true;
            }, 700);
            break;
            case 39: //Right
            $("#next").trigger("click");
            setTimeout(function(){
                can_key = true;
            }, 700);
            break;
            case 32: //Space
            $("#lightbox").trigger("click");
            setTimeout(function(){
                can_key = true;
            }, 700);
            break;
            default: return;
        }
    }
};

    $(".page_image").click(function(e){
        e.preventDefault();
        lightbox_type = "single";
        $("#loading_container").show();
        $("#loading_container").css("opacity", "1");
        var id = $(this).attr("id");
        xhrRequest(id);
        img.src = ""; //Fixes bug that prevented previously viewed/cached images from loading when thumbnail clicked on iOS.
        $("#popup_image").attr("src", "/uploads/" + id);
        $("#lightbox").css("opacity", "1");
        $("#popup_image").css("opacity", "1").hide();
        $("#lightbox").show();
        $("#lightbox").fadeOut(0);
        $("#lightbox").fadeIn(500);
        $("#next").hide();
        $("#prev").hide();
    });

    $("#lightbox").click(function(){
        $("#lightbox").fadeOut(500);
        $("#loading_container").hide();
        $("#next").hide();
        $("#prev").hide();
    });
    $("#loading_container").click(function(){
        $("#lightbox").fadeOut(500);
        $("#loading_container").hide();
        $("#next").hide();
        $("#prev").hide();
    });

});