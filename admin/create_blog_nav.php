<?php

function createBlogNavFile(){
    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $subdir . "/blog_nav.php")){
        fopen($_SERVER['DOCUMENT_ROOT'] . $subdir . "/blog_nav.php", "w");
    }
    $nav_file = fopen($_SERVER['DOCUMENT_ROOT'] . $subdir . "/blog_nav.php", "r+");
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . $subdir . "/blog_nav.php", "");
    fwrite($nav_file, "<div class='blog_posts'><br>");
    $sortArray1 = json_decode(file_get_contents("sorted_blog.txt"));
    $unpublished = json_decode(file_get_contents("unpublished_posts.txt"));
    $sortArray = [];
    foreach ($sortArray1 as $post){
        if (!in_array($post, $unpublished)){
            array_push($sortArray, $post);
        }
    }
    for ($i = 0; $i <= count($sortArray); $i++){
        if (count($sortArray) === 0){
            fwrite($nav_file, "<p style='text-align:center';>No posts yet.</p>");
        }
        $title = ucfirst(str_replace(".php", "", $sortArray[$i]));
        if ($i <= (count($sortArray) - 2)){
            $content = "<a href='" . "../posts/" . $sortArray[$i]. "' class='blog_link'>" . $title . "</a>" . 
            "<p class='excerpt'>" . getExcerpt($sortArray[$i]) . "</p>";
            fwrite($nav_file, $content);
        } elseif ($i <= (count($sortArray) - 1)){
            $content = "<a href='" . "../posts/" . $sortArray[$i] . "' class='blog_link'>" . $title . "</a>"  . 
            "<p class='excerpt'>" . getExcerpt($sortArray[$i]) . "</p>";
            fwrite($nav_file, $content);
        }
        }

    fwrite($nav_file, "</div>");
    fclose($nav_file);
}

function getExcerpt($filename){
    $file = file_get_contents(dirname(__DIR__, 1) . "/posts_text/" . str_replace(".php", ".txt", $filename));
    $explode = explode("--- Place your excerpt below this line ---", $file);
    return trim($explode[1]);
}

?>