<?php

class Tracker {
    public function __construct(){
        $this->blocked_ips = [];
        $this->blocked;
        $this->time;
        $this->date;
        $this->settings;
        $this->address;
        $this->ip_lookup;
        $this->ip_lookup_href;
        $this->user_agent;
        $this->os;
        $this->browser;
        $this->userstack_url;
        $this->ipstack_url;
        $this->ipstack_xml;
        $this->country;
        $this->state;
        $this->suburb;
        $this->query_string;
        $this->referer;
        $this->page;
        $this->alert_pages = [];
        $this->requested_url = htmlspecialchars($_SERVER['REQUEST_URI']);
        $this->getSettings();
        $this->getIP();
        $this->getBlockList();
        $this->checkBlock();
    }
    private function getSettings(){
        $this->settings = simplexml_load_file(dirname(__DIR__, 1) . "/site_info.xml");
    }
    private function getTime(){
        $time = new DateTime(null, new DateTimeZone($this->settings->timezone));
        $this->time = $time->format("h:i:s A");
        $this->date = $time->format("D d M Y");
    }
    private function getIP (){
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])){
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = $_SERVER['HTTP_CLIENT_IP'];
        $forward = $_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];
        if (filter_var($client, FILTER_VALIDATE_IP)){
            $ip = $client;
        } elseif(filter_var($forward, FILTER_VALIDATE_IP)){
            $ip = $forward;
        } else {
            $ip = $remote;
        } 
        $this->address = $ip;
        $this->ip_lookup = "<a href='https://whatismyipaddress.com/ip/" . $ip . "' target='_blank'>" . $ip . "</a>";
        $this->ip_lookup_href = "https://whatismyipaddress.com/ip/" . $ip;
    }
    private function getBlockList(){
        $string = $this->settings->no_track;
        $array = explode(",", $string);
        foreach ($array as $entry){
            array_push($this->blocked_ips, trim($entry));
        }
    }
    private function checkBlock(){
        $this->getUserAgentInfo();
        $crawlers = ["AhrefsBot", "GumGum Bot", "SEMrushBot", "YandexBot", "bingbot", "HuaweiWebCatBot", "Googlebot", "Feedfetcher-Google"];
        if (isset($_COOKIE["tracking_block"])){
            $this->blocked = true;
        } elseif (in_array($this->address, $this->blocked_ips)){
            $this->blocked = true;
        } elseif (in_array($this->browser, $crawlers)){
            $this->blocked = true;
        }
        else {
            $this->getIPInfo();
            $this->getOtherInfo();
            $this->getTime();
            $this->blocked = false;
        }
    }
    private function getUserAgentInfo(){
        $this->user_agent = $_SERVER['HTTP_USER_AGENT'];
        $this->userstack_url = "http://api.userstack.com/detect?access_key=".$this->settings->userstack_key . "&ua=" . $this->user_agent . "&output=xml";
        $userstack_xml = simplexml_load_file($this->userstack_url); //LOG
        $this->os = $userstack_xml->os->name;
        $this->browser = $userstack_xml->browser->name;
    }
    private function getIPInfo(){
        $this->ipstack_url = "http://api.ipstack.com/" . $this->address . "?access_key=" . $this->settings->ipstack_key . "&output=xml";
        $this->ipstack_xml = simplexml_load_file($this->ipstack_url); //LOG
        $this->country = $this->ipstack_xml->country_name;
        $this->state = $this->ipstack_xml->region_name;
        $this->suburb = $this->ipstack_xml->city;
    }
    private function getOtherInfo(){
        if (isset($_SERVER['QUERY_STRING'])){
            $this->query_string = stripslashes(htmlspecialchars($_SERVER['QUERY_STRING']));
        } else {
            $this->query_string = "N/A";
        }
        if (isset($_SERVER['HTTP_REFERER'])){
            $this->referer = $_SERVER['HTTP_REFERER'];
        } else {
            $this->referer = "N/A";
        }
        if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['gallery_view']){
            $this->page = htmlspecialchars($_POST['gallery_view']);
            echo $this->page;
        } else {
            $cwd = htmlspecialchars(getcwd());
            $exploded = explode("/", $cwd);
            $this->page = "/" . $exploded[(count($exploded) - 1)] . "/" . htmlspecialchars(basename($_SERVER['PHP_SELF']));
        }
    }
    public function getEmailList(){
        $exploded = explode(",", $this->settings->alert_pages);
        foreach ($exploded as $entry){
            array_push($this->alert_pages, strtolower(trim($entry)));
        }
        $this->visitEmail();
    }
    public function visitEmail(){
        if (in_array($this->page, $this->alert_pages) || in_array("all", $this->alert_pages)){
            mail($this->settings->email, 
            "New visit to " . $this->settings->sitename, 
            "Visit from: " . $this->address . 
            " at " . $this->time . 
            " on " . $this->date . 
            " to " . $this->page . 
            "\n\nMore information: " . 
            "\nOS: " . $this->os . 
            "\nBrowser: " . $this->browser . 
            "\nCountry: " . $this->country . 
            "\nState: " . $this->state . 
            "\nSuburb: " . $this->suburb . 
            "\nQuery string: " . $this->query_string . 
            "\nReferer: " . $this->referer . 
            "\nRequest URL: " . $this->requested_url . 
            "\n\nGet more IP information here: " . $this->ip_lookup_href, 
            "From: alerts@mypage-cms.com");
        }
    }
}

class Writer extends Tracker {
    public function getLastPrefix(){
        if (count(glob($_SERVER["DOCUMENT_ROOT"] . "/tracking/tracking_logs/*")) === 0){
            return 0;
        } else {
            $file_array = scandir($_SERVER["DOCUMENT_ROOT"] . "/tracking/tracking_logs/");
            natsort($file_array);
            $prefix_array = [];
            foreach ($file_array as $file){
                if (strpos($file, "^") != false){
                    $parts = explode("^", $file);
                    $prefix = $parts[0];
                }
                array_push($prefix_array, $prefix);
            }
            return intval(max($prefix_array));
        }
    }
    public function getNewPrefix(){
        $this->new_prefix = $this->getLastPrefix() + 1;
        return $this->new_prefix;
    }
    public function getLastEntryNum(){ //of latest file (top of natsort list)
        if (count(glob($_SERVER["DOCUMENT_ROOT"] . "/tracking/tracking_logs/*")) === 0){
            return 1;
        } else {
            $last_file = scandir($_SERVER["DOCUMENT_ROOT"] . "/tracking/tracking_logs/");
            $file_array = [];
            foreach ($last_file as $file){
                if ($file[0] != "."){
                    array_push($file_array, $file);
                }
            }
            natsort($file_array);
            $reversed = array_reverse($file_array);
            $parts1 = explode("@", $reversed[0]);
            $parts2 = explode("^", $parts1[0]);
            $entry_num = intval($parts2[1]);
            return $entry_num;
        }
    }
    public function getButton($filename, $num){
        $exploded = explode("/tracking_logs/", $filename);
        $button_id = $exploded[1];
        return "<a href='javascript:void(0);' class='tracking_delete' id='" . $button_id . "'>^ Delete ^ </a>";
    }
    public function writeToFile(){
            $this->file = 
            $_SERVER["DOCUMENT_ROOT"] . 
            "/tracking/tracking_logs/" . 
            $this->getLastPrefix() . "^" . 
            $this->getLastEntryNum() . "@" .
            $this->date . 
            ".txt";
            $this->new_file = //Created if file doesn't exist already.
            $_SERVER["DOCUMENT_ROOT"] . 
            "/tracking/tracking_logs/" . 
            ($this->getLastPrefix() + 1) . "^" . 
            "1@" .
            $this->date . 
            ".txt";
            $this->new_file_name = //Created if file DOES exist with entry incremented.
            $_SERVER["DOCUMENT_ROOT"] . 
            "/tracking/tracking_logs/" . 
            $this->getLastPrefix() . "^" . 
            ($this->getLastEntryNum() + 1) . "@" .
            $this->date . 
            ".txt";
        //If the last file DOESN'T have today's date, created a new one with an incremented prefix. If it does, that means it's already been created, so just open it.
            if (!file_exists($this->file)){
                $this->today = fopen($this->new_file, "w");
                fclose($this->today);
                $this->today = fopen($this->new_file, "r+");
                $this->filename = $this->new_file;
                rename($this->filename, $this->new_file);
                $this->button = $this->getButton($this->filename, "1");
            } else {
                $this->today = fopen($this->file, "r+");
                $this->existing_content = file_get_contents($this->file);
                $this->filename = $this->file;
                rename($this->filename, $this->new_file_name);
                $this->button = $this->getButton($this->new_file_name, ($this->getLastEntryNum() + 1));
            }
            $this->data = 
            "<entry_" . $this->getLastEntryNum() . ">\n" . 
            "<table class='tracking_entry' cellspacing='0'>\n" . 
            "<tr>" . "<th>Time</th>" . "<td>" . $this->time . "</td></tr>\n" . 
            "<tr>" . "<th>Date</th>" . "<td>" . $this->date . "</td></tr>\n" .
            "<tr>" . "<th>Page</th>" . "<td>" . $this->page . "</td></tr>\n" . 
            "<tr>" . "<th>IP</th>" . "<td>" . $this->ip_lookup . "</td></tr>\n" . 
            "<tr>" . "<th>OS</th>" . "<td>" . $this->os . "</td></tr>\n" . 
            "<tr>" . "<th>Browser</th>" . "<td>" . $this->browser . "</td></tr>\n" . 
            "<tr>" . "<th>Country</th>" . "<td>" . $this->country . "</td></tr>\n" . 
            "<tr>" . "<th>State</th>" . "<td>" . $this->state . "</td></tr>\n" . 
            "<tr>" . "<th>Suburb</th>" . "<td>" . $this->suburb . "</td></tr>\n" . 
            "<tr>" . "<th>Referer</th>" . "<td>" . $this->referer . "</td></tr>\n" . 
            "<tr>" . "<th>Query</th>" . "<td>" . $this->query_string . "</td></tr>" . 
            "<tr>" . "<th>Requested: </th>" . "<td>" . $this->requested_url . "</td></tr>" . 
            "</table>" . 
            "<entry_" . $this->getLastEntryNum() . ">\n\n";
            fwrite($this->today, $this->data);
            fwrite($this->today, $this->existing_content);
            fclose($this->today);
    }
}

$tracking = new Tracker();
if ($tracking->blocked == false){
    $tracking->getEmailList();
    $writer = new Writer();
    $writer->writeToFile();
}

?>