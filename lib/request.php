<?php
namespace request;

function get_url($url) {
  $ch = curl_init($url);
  set_user_agent($ch);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  return curl_exec($ch);
}

function set_user_agent(&$ch) {
  // Unfortunately I've seen a bunch of websites return different content when the user agent is set to something like curl or other server-side libraries, so we have to pretend to be a browser to successfully get the real HTML
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) p3k/Monocle/0.1.0 AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.57 Safari/537.36');
}
