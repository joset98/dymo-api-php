<?php

require_once "../config.php";
require_once "../exceptions.php";

function get_prayer_times($data) {
    if (empty($data["lat"]) || empty($data["lon"])) throw new BadRequestError("You must provide a latitude and longitude.");

    $params = [
        "lat" => $data["lat"],
        "lon" => $data["lon"]
    ];

    $url = BASE_URL . "/v1/public/islam/prayertimes?" . http_build_query($params);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) throw new APIError(curl_error($ch));

    curl_close($ch);
    if ($httpCode !== 200) throw new APIError("API request failed with status code: $httpCode");
    return json_decode($response, true);
}

function satinizer($data) {
    if (!isset($data["input"])) throw new BadRequestError("You must specify at least the input.");
    
    $input_value = $data["input"];
    $url = BASE_URL . "/v1/public/inputSatinizer?input=" . urlencode($input_value);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        throw new APIError(curl_error($ch));
    }

    curl_close($ch);

    if ($httpCode !== 200) {
        throw new APIError("API request failed with status code: $httpCode");
    }

    return json_decode($response, true);
}

function is_valid_pwd($data) {
    if (empty($data["password"])) throw new BadRequestError("You must specify at least the password.");

    $params = [
        "password" => urlencode($data["password"])
    ];

    if (!empty($data["email"])) {
        if (!preg_match("/^[a-zA-Z0-9._\-+]+@?[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/", $data["email"])) throw new BadRequestError("If you provide an email address it must be valid.");
        $params["email"] = urlencode($data["email"]);
    }

    if (!empty($data["bannedWords"])) {
        $banned_words = $data["bannedWords"];
        if (is_string($banned_words)) {
            $banned_words = explode(",", trim($banned_words, "[]"));
            $banned_words = array_map('trim', $banned_words);
        }
        if (!is_array($banned_words) || count($banned_words) > 10) throw new BadRequestError("If you provide a list of banned words; the list may not exceed 10 words and must be of array type.");
        if (count($banned_words) !== count(array_unique($banned_words))) throw new BadRequestError("If you provide a list of banned words; all elements must be non-repeated strings.");
        $params["bannedWords"] = implode(",", array_map('urlencode', $banned_words));
    }

    if (isset($data["min"])) {
        $min_length = $data["min"];
        if (!is_int($min_length) || $min_length < 8 || $min_length > 32) throw new BadRequestError("If you provide a minimum it must be valid.");
        $params["min"] = $min_length;
    }

    if (isset($data["max"])) {
        $max_length = $data["max"];
        if (!is_int($max_length) || $max_length < 32 || $max_length > 100) throw new BadRequestError("If you provide a maximum it must be valid.");
        $params["max"] = $max_length;
    }

    $url = BASE_URL . "/v1/public/validPwd?" . http_build_query($params);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) throw new APIError(curl_error($ch));

    curl_close($ch);
    if ($httpCode !== 200) throw new APIError("API request failed with status code: $httpCode");
    return json_decode($response, true);
}

function new_url_encrypt($url) {
    if (empty($url) || !(strpos($url, "https://") === 0 || strpos($url, "http://") === 0)) throw new BadRequestError("You must provide a valid url.");

    $url = BASE_URL . "/v1/public/url-encrypt?url=" . urlencode($url);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) throw new APIError(curl_error($ch));

    curl_close($ch);
    if ($httpCode !== 200) throw new APIError("API request failed with status code: $httpCode");
    return json_decode($response, true);
}

?>