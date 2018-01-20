<?php

include('inc/simple_html_dom.php');

header("Access-Control-Allow-Origin: *");

if(isset($_REQUEST["mock"])) {
    sleep(1);
    if(isset($_REQUEST["low"]))
        echo '{"used":"7563.9","remain":"7440.60","allotted":"61440","usedTime":"24:54","remainTime":"2135:5","allottedTime":"2160: 00","packageExpiry":"16-12-2016","packageLastRenewal":"17-09-2016","packageName":"799_2_6Mbps_60GB_1Mbps_3Months","login":"john_doe","mock":true,"timeTaken":9.843657}';
    else
        echo '{"used":"7563.9","remain":"53876.0","allotted":"61440","usedTime":"24:54","remainTime":"2135:5","allottedTime":"2160: 00","packageExpiry":"16-12-2016","packageLastRenewal":"17-09-2016","packageName":"799_2_6Mbps_60GB_1Mbps_3Months","login":"john_doe","mock":true,"timeTaken":4.657}';
} else {
    
    $startTime = microtime(true);
    $useragent = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3';
    $cookie = 'cookie.txt';
    $data = [];
    $loginUrl = 'http://myaccount.gazonindia.com/loginpage.aspx';
    $detailsUrl = 'http://myaccount.gazonindia.com/Masters/usagedetail.aspx';
    $credentials = array(
        'txtUserName' => 'mujtaba_aab16',
        'txtlogPassword' => 'XXXXXX',
        'btnSubmit' => 'Submit'
    );
    $hiddenFields = array("__EVENTTARGET", "__EVENTARGUMENT", "__VIEWSTATE", "__VIEWSTATEGENERATOR", "__EVENTVALIDATION");
    $elementDataMap = array(
        "span[id=ctl00_ContentPlaceHolder1_lblUsedTotal]"       => "used",
        "span[id=ctl00_ContentPlaceHolder1_lblRemainTotal]"     => "remain",
        "span[id=ctl00_ContentPlaceHolder1_lblAllotedTotal]"    => "allotted",
        "span[id=ctl00_ContentPlaceHolder1_lblUsedTime]"        => "usedTime",
        "span[id=ctl00_ContentPlaceHolder1_lblRemailTime]"      => "remainTime",
        "span[id=ctl00_ContentPlaceHolder1_lblallotedTime]"     => "allottedTime",
        "span[id=ctl00_ContentPlaceHolder1_lblPkgExp]"          => "packageExpiry",
        "span[id=ctl00_ContentPlaceHolder1_lblRenewdt]"         => "packageLastRenewal",
        "span[id=ctl00_ContentPlaceHolder1_lblPkg]"             => "packageName",
        "span[id=ctl00_ContentPlaceHolder1_lblLoginId]"         => "login"
    );
    $data["mock"] = false;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //curl_setopt($ch, CURLOPT_PROXY, 'http://brproxy.persistent.co.in:8080');
    //curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'mujtaba_ahmad@persistent.co.in:@gallardo91B');
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_COOKIESESSION, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    
    
    // GET
    curl_setopt($ch, CURLOPT_URL, $loginUrl);    // Setting URL to GET
    curl_setopt($ch, CURLOPT_POST, false);   // Setting method as GET
    $result = curl_exec($ch);    
    
    $dom = str_get_html($result);
    
    foreach($hiddenFields as $field) {
        $hiddenInputs = $dom->find('input[name='.$field.']');
        foreach($hiddenInputs as $e) {        
            $credentials[$field] = $e->getAttribute("value");
            break;
        }
    }
    
    // POST
    curl_setopt($ch, CURLOPT_URL, $loginUrl);    // Setting URL to POST to
    curl_setopt($ch, CURLOPT_POST, true);   // Setting method as POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, $credentials);  // Setting POST fields as array
    curl_exec($ch);
    
    // GET
    curl_setopt($ch, CURLOPT_URL, $detailsUrl);    // Setting URL to GET
    curl_setopt($ch, CURLOPT_POST, false);   // Setting method as GET
    $result = curl_exec($ch);
    
    $dom = str_get_html($result);
    
    foreach($elementDataMap as $key => $val) {
        foreach($dom->find($key) as $e) {
            $data[$val] = $e->innertext;
            break;
        }
    }
    
    $data["timestamp"] = time();
    $data["timeTaken"] = microtime(true) - $startTime;

    echo json_encode($data);
}

?>