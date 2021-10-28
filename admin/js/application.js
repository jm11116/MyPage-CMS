$(document).ready(function(){

//Preload loading spinner:
var img = new Image();
img.src = "assets/loading.gif";

//Prevent jQuery caching AJAX calls:
$.ajaxSetup ({
    cache: false
});

var base_font_size = "1em";
var bigger_font_size = "1.3em";

class Query {
    constructor(){
        this.url = window.location.href;
        this.keyvaluesarray = this.url.split(/\?|\=|\&/g);
    }
    present(){
        if (this.url.indexOf("?") == "-1"){
            return false;
        } else {
            return true;
        }
    }
    parts(part){
        if (this.present() == true){
            this.array = this.url.split("?");
            if (part == "url"){
                return this.array[0];
            } else if (part == "query"){
                return this.array[1];
            }
        }
    }
    append(key, value){
        this.existing = "?" + this.parts("query") + "&";
        this.combined = this.existing + key + "=" + value;
        if (this.present() == true){
            window.history.replaceState("", "", this.combined);
        } else {
            window.history.replaceState("", "", "?" + key + "=" + value);
        }
    }
    replace(key, value){
        window.history.replaceState("", "", "?" + key + "=" + value);
    }
    getvaluefromkey(key){
        if (this.keyvaluesarray.includes(key) == true){
            this.valuepos = this.keyvaluesarray.indexOf(key) + 1;
            return this.keyvaluesarray[this.valuepos];
        } else {
            return false;
        }
    }
    getkeyfromvalue(value){
        if (this.keyvaluesarray.includes(value) == true){
            this.valuepos = this.keyvaluesarray.indexOf(value) - 1;
            return this.keyvaluesarray[this.valuepos];
        } else {
            return false;
        } 
    }
    loadfromquery(){
        if (this.present() == true && this.getvaluefromkey("section") != false) {
            this.pagetoload = this.getvaluefromkey("section");
            console.log(this.pagetoload);
            $("#main").load(this.pagetoload);
            $(".section_link").css("text-decoration", "none");
            this.id = "#" + this.pagetoload.replace(".php", "_link");
            $(".section_link").css("font-size", base_font_size);
            $(this.id).css("text-decoration", "underline");
            $(this.id).css("font-size", bigger_font_size);
        } else {
            $("#main").load("home.php");
            this.replace("section", "home.php");
            $("#home_link").css("text-decoration", "underline");
            $("#home_link").css("font-size", bigger_font_size);
        }
    }
}
query = new Query;
query.loadfromquery(); //Load section from query string or make a new one:

$(".section_link").click(function(){
    var id = $(this).attr("id");
    var link = id.replace("_link", ".php");
    query.replace("section", link)
    $(".section_link").css("text-decoration", "none");
    $(this).css("text-decoration", "underline");
    var base_font_size = "1em";
    var bigger_font_size = "1.3em";
    $(".section_link").css("font-size", base_font_size);
    $(this).css("font-size", bigger_font_size);
    $.ajax({
        beforeSend: function(){
            $("#main").html("<div id='loading'></div>");
        },
        url: link,
        type: "POST",
        error: function(xhr){
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
        },
        success: function(data){
            $("#main").html(data);
        }
    });
});

});