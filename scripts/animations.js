$(document).ready(function(){

function preloadImage(url){
    var img = new Image();
    img.src = url;
}
if (backgrounds_array[0] == null){
    preloadImage("/themes/" + site_theme + "/backgrounds/" + backgrounds_array[0]);
}

$(".background").fadeOut(0);

$(window).scroll(function(){
    var scrollPosSubheading = 1 - $(window).scrollTop() / 215;
    var scrollPosBackground = 1 + $(window).scrollTop() * 0.0005;
    $(".subheading").css({opacity: scrollPosSubheading});
    $(".background").css("transform", "scale(" + scrollPosBackground + ")");
});

$(".blog_contact_text").css("opacity", "0");
$("#footer_box").css("opacity", "0");
$(".text_div").css("opacity", "0");
setTimeout(function(){
    $(".text_div").animate({opacity: 1}, 1000);
    $(".blog_contact_text").animate({opacity: 1}, 1000);
}, 2000);
setTimeout(function(){
    $("#footer_box").animate({opacity: 1}, 1000);
}, 2800);

if (location.pathname.substring(1) == "pages/index.php" || location.pathname.substring(1) == "pages/" || location.pathname.substring(1) == ""){
    $(".subheading").css("opacity", "0");
    $(".header").css("opacity", "0");
    $(".nav_container").css("opacity", "0");
    setTimeout(function(){
        $(".subheading").animate({opacity: 1}, 1000);
    }, 3500);
    setTimeout(function(){
        $(".header").animate({opacity: 1}, 1000);
    }, 4000);
    setTimeout(function(){
        $(".nav_container").animate({opacity: 1}, 1000);
    }, 4500);
}
if (backgrounds_array[0] == null){
    setTimeout(function(){
        $(".background").css("background-image", "url(../themes/" + site_theme + "/backgrounds/" + backgrounds_array[0]);
        $(".background").fadeIn(1000);
    }, 2300);
}

function shuffle(array) {
  var currentIndex = array.length, temporaryValue, randomIndex;
  while (0 !== currentIndex){
    randomIndex = Math.floor(Math.random() * currentIndex);
    currentIndex -= 1;
    temporaryValue = array[currentIndex];
    array[currentIndex] = array[randomIndex];
    array[randomIndex] = temporaryValue;
  }
  return array;
}

var last_image;
var slideshowInt;

function startSlideshow(){
    setTimeout(function(){
        for (i = 1; i < backgrounds_array.length; i++){
            preloadImage("/themes/" + site_theme + "/backgrounds/" + backgrounds_array[i]);
        }
    }, 3000);
    slideshowInt = setInterval(function(){
        shuffle(backgrounds_array);
        if (last_image != backgrounds_array[0]){
            preloadImage("../themes/" + site_theme + "/backgrounds/" + backgrounds_array[0]);
            $(".background").fadeOut(1000);
            setTimeout(function(){
                $(".background").css("background-image", "url(../themes/" + site_theme + "/backgrounds/" + backgrounds_array[0]);
                $(".background").finish().dequeue().fadeIn(1000);
            }, 1900);
            last_image = backgrounds_array[0];
        } else {
            preloadImage("../themes/" + site_theme + "/backgrounds/" + backgrounds_array[1]);
            $(".background").fadeOut(1000);
            setTimeout(function(){
                $(".background").css("background-image", "url(../themes/" + site_theme + "/backgrounds/" + backgrounds_array[1]);
                $(".background").finish().dequeue().fadeIn(1800);
            }, 1900);
            last_image = backgrounds_array[1];
        }
    }, 6000);
}

if (backgrounds_array.length > 1){
    startSlideshow();
    setInterval(function(){
        clearInterval(slideshowInt);
        $(".background").finish().dequeue();
    }, 60000);
} else if (backgrounds_array.length == 1) {
    $(".background").css("background-image", "url(../themes/" + site_theme + "/backgrounds/" + backgrounds_array[0]);
    setTimeout(function(){
        $(".background").fadeIn(1000);
    }, 3000);
}

});