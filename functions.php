<?php
function call($url, $oauthtoken='', $type='GET', $arguments=array(), $encodeData=true, $returnHeaders=false) {
    $type = strtoupper($type);
    if ($type == 'GET') { $url .= "?" . http_build_query($arguments); }
    $curl_request = curl_init($url);
    if ($type == 'POST') { curl_setopt($curl_request, CURLOPT_POST, 1); }
    elseif ($type == 'PUT') { curl_setopt($curl_request, CURLOPT_CUSTOMREQUEST, "PUT"); }
    elseif ($type == 'DELETE') { curl_setopt($curl_request, CURLOPT_CUSTOMREQUEST, "DELETE"); }
    curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($curl_request, CURLOPT_HEADER, $returnHeaders);
    curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);
    if (!empty($oauthtoken)) {
        $token = array("oauth-token: {$oauthtoken}");
        curl_setopt($curl_request, CURLOPT_HTTPHEADER, $token);
    }
    if (!empty($arguments) && $type !== 'GET') {
        if ($encodeData) { $arguments = json_encode($arguments); } //encode the arguments as JSON
        curl_setopt($curl_request, CURLOPT_POSTFIELDS, $arguments);
    }
    $result = curl_exec($curl_request);
    if ($returnHeaders) {
        list($headers, $content) = explode("\r\n\r\n", $result ,2); //set headers from response
        foreach (explode("\r\n",$headers) as $header) { header($header);}
		return trim($content); //return the nonheader data
    }
    curl_close($curl_request);
    $response = json_decode($result); //decode the response from JSON
    return $response;
}