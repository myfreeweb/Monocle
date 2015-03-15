<?php

function buildRedirectURI() {
  return 'http' . (Config::$ssl ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . '/auth/callback';
}

function clientID() {
  return 'https://monocle.p3k.io';
}

function build_url($parsed_url) { 
  $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : ''; 
  $host     = isset($parsed_url['host']) ? $parsed_url['host'] : ''; 
  $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : ''; 
  $user     = isset($parsed_url['user']) ? $parsed_url['user'] : ''; 
  $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : ''; 
  $pass     = ($user || $pass) ? "$pass@" : ''; 
  $path     = isset($parsed_url['path']) ? $parsed_url['path'] : ''; 
  $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : ''; 
  $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : ''; 
  return "$scheme$user$pass$host$port$path$query$fragment"; 
} 

// Input: Any URL or string like "aaronparecki.com"
// Output: Normlized URL (default to http if no scheme, force "/" path)
//         or return false if not a valid URL (has query string params, etc)
function normalizeMeURL($url) {
  $me = parse_url($url);

  if(array_key_exists('path', $me) && $me['path'] == '')
    return false;

  // parse_url returns just "path" for naked domains
  if(count($me) == 1 && array_key_exists('path', $me)) {
    $me['host'] = $me['path'];
    unset($me['path']);
  }

  if(!array_key_exists('scheme', $me))
    $me['scheme'] = 'http';

  if(!array_key_exists('path', $me))
    $me['path'] = '/';

  // Invalid scheme
  if(!in_array($me['scheme'], array('http','https')))
    return false;

  // Invalid path
  if($me['path'] != '/')
    return false;

  // query and fragment not allowed
  if(array_key_exists('query', $me) || array_key_exists('fragment', $me))
    return false;

  return build_url($me);
}

function require_login(&$app) {
  if($user=current_user())
    return $user;

  $app->redirect('/');
  return false;
}

function require_login_json(&$app) {
  if($user=current_user())
    return $user;

  json_response($app, array('error'=>'not_logged_in'));
  return false;
}

function current_user() {
  if(!is_logged_in()) {
    return false;
  } else {
    return ORM::for_table('users')->find_one($_SESSION['user_id']);
  }
}

function is_logged_in() {
  return array_key_exists('user_id', $_SESSION);
}

function user_id() {
  if(is_logged_in()) {
    return $_SESSION['user_id'];
  } else {
    return false;
  }
}

$app->get('/auth/start', function() use($app) {
  $req = $app->request();

  $params = $req->params();
  
  // the "me" parameter is user input, and may be in a couple of different forms:
  // aaronparecki.com http://aaronparecki.com http://aaronparecki.com/
  // Normlize the value now (move this into a function in IndieAuth\Client later)
  if(!array_key_exists('me', $params) || !($me = normalizeMeURL($params['me']))) {
    $html = render('auth_error', array(
      'title' => 'Sign In',
      'error' => 'Invalid "me" Parameter',
      'errorDescription' => 'The URL you entered, "<strong>' . $params['me'] . '</strong>" is not valid.'
    ));
    $app->response()->body($html);
    return;
  }

  $authorizationEndpoint = IndieAuth\Client::discoverAuthorizationEndpoint($me);
  $tokenEndpoint = IndieAuth\Client::discoverTokenEndpoint($me);
  $micropubEndpoint = IndieAuth\Client::discoverMicropubEndpoint($me);

  // Generate a "state" parameter for the request
  $state = IndieAuth\Client::generateStateParameter();
  $_SESSION['auth_state'] = $state;

  if($tokenEndpoint && $micropubEndpoint && $authorizationEndpoint) {
    // If the user specified all three, build an authorization URL for their auth endpoint

    $scope = 'post';
    $authorizationURL = IndieAuth\Client::buildAuthorizationURL($authorizationEndpoint, $me, buildRedirectURI(), clientID(), $state, $scope);

  } elseif($authorizationEndpoint) {
    // If the user specified only an authorization endpoint, use that

    $authorizationURL = IndieAuth\Client::buildAuthorizationURL($authorizationEndpoint, $me, buildRedirectURI(), clientID(), $state);

  } else {
    // Otherwise, fall back to indieauth.com but tell them what's happening first
    $authorizationURL = IndieAuth\Client::buildAuthorizationURL(Config::$defaultAuthorizationEndpoint, $me, buildRedirectURI(), clientID(), $state);

  }

  // If the user has already signed in before and has a micropub access token, skip 
  // the debugging screens and redirect immediately to the auth endpoint.
  // This will still generate a new access token when they finish logging in.
  $user = ORM::for_table('users')->where('url', $me)->find_one();
  if($user && $user->micropub_access_token && !array_key_exists('restart', $params)) {

    $user->authorization_endpoint = $authorizationEndpoint;
    $user->micropub_endpoint = $micropubEndpoint;
    $user->save();

    $app->redirect($authorizationURL, 301);

  } elseif($tokenEndpoint && $micropubEndpoint && $authorizationEndpoint) {
    // If all three endpoints are found, redirect immediately.
    // Normally happens with brand new users, but could also happen the first time 
    // someone adds a micropub endpoint.

    if(!$user) {
      $user = ORM::for_table('users')->create();
      $user->url = $me;
      $user->date_created = date('Y-m-d H:i:s');
    }

    $user->authorization_endpoint = $authorizationEndpoint;
    $user->micropub_endpoint = $micropubEndpoint;
    $user->save();

    $app->redirect($authorizationURL, 301);

  } else {
    // Either only the authorization endpoint was found, or none were found.
    // Create the user anyway, but show them a message explaning what is happening.
    // They will be able to log in and use it anyway, but won't be able to post.

    if(!$user) {
      $user = ORM::for_table('users')->create();
      $user->date_created = date('Y-m-d H:i:s');
      $user->url = $me;
    }
    $user->authorization_endpoint = $authorizationEndpoint;
    $user->save();

    $html = render('auth_start', array(
      'title' => 'Sign In',
      'me' => $me,
      'authorizing' => $me,
      'meParts' => parse_url($me),
      'authorizationEndpoint' => $authorizationEndpoint,
      'authorizationURL' => $authorizationURL
    ));
    $app->response()->body($html);
  }
});

$app->get('/auth/callback', function() use($app) {
  $req = $app->request();
  $params = $req->params();

  // Double check there is a "me" parameter
  // Should only fail for really hacked up requests
  if(!array_key_exists('me', $params) || !($me = normalizeMeURL($params['me']))) {
    $html = render('auth_error', array(
      'title' => 'Auth Callback',
      'error' => 'Invalid "me" Parameter',
      'errorDescription' => 'The ID you entered, <strong>' . $params['me'] . '</strong> is not valid.'
    ));
    $app->response()->body($html);
    return;
  }

  // If there is no state in the session, start the login again
  if(!array_key_exists('auth_state', $_SESSION)) {
    $app->redirect('/auth/start?me='.urlencode($params['me']));
    return;
  }

  if(!array_key_exists('code', $params) || trim($params['code']) == '') {
    $html = render('auth_error', array(
      'title' => 'Auth Callback',
      'error' => 'Missing authorization code',
      'errorDescription' => 'No authorization code was provided in the request.'
    ));
    $app->response()->body($html);
    return;
  }

  // Verify the state came back and matches what we set in the session
  // Should only fail for malicious attempts, ok to show a not as nice error message
  if(!array_key_exists('state', $params)) {
    $html = render('auth_error', array(
      'title' => 'Auth Callback',
      'error' => 'Missing state parameter',
      'errorDescription' => 'No state parameter was provided in the request. This shouldn\'t happen. It is possible this is a malicious authorization attempt.'
    ));
    $app->response()->body($html);
    return;
  }

  if($params['state'] != $_SESSION['auth_state']) {
    $html = render('auth_error', array(
      'title' => 'Auth Callback',
      'error' => 'Invalid state',
      'errorDescription' => 'The state parameter provided did not match the state provided at the start of authorization. This is most likely caused by a malicious authorization attempt.'
    ));
    $app->response()->body($html);
    return;
  }

  // Now the basic sanity checks have passed. Time to start providing more helpful messages when there is an error.
  // An authorization code is in the query string, and we want to exchange that for an access token at the token endpoint.

  // Discover the endpoints
  $authorizationEndpoint = IndieAuth\Client::discoverAuthorizationEndpoint($me);
  $micropubEndpoint = IndieAuth\Client::discoverMicropubEndpoint($me);
  $tokenEndpoint = IndieAuth\Client::discoverTokenEndpoint($me);

  if($tokenEndpoint) {
    $token = IndieAuth\Client::getAccessToken($tokenEndpoint, $params['code'], $params['me'], buildRedirectURI(), clientID(), $params['state'], true);
  } elseif($authorizationEndpoint) {
    $token = IndieAuth\Client::verifyIndieAuthCode($authorizationEndpoint, $params['code'], $params['me'], buildRedirectURI(), clientID(), $params['state'], true);
  } else {
    $token = IndieAuth\Client::verifyIndieAuthCode(Config::$defaultAuthorizationEndpoint, $params['code'], $params['me'], buildRedirectURI(), clientID(), $params['state'], true);
  }

  $redirectToDashboardImmediately = false;

  // If a valid access token was returned, store the token info in the session and they are signed in
  if(k($token['auth'], array('me'))) {
    $_SESSION['auth'] = $token['auth'];
    $_SESSION['me'] = $params['me'];
    $redirectToDashboardImmediately = true;

    $user = ORM::for_table('users')->where('url', $me)->find_one();
    if(!$user) {
      // New user! Store the user in the database
      $user = ORM::for_table('users')->create();
      $user->url = $me;
      $user->date_created = date('Y-m-d H:i:s');
    }
    $user->subscriptions_url = '';
    $user->authorization_endpoint = $authorizationEndpoint;

    if(k($token['auth'], array('access_token','scope'))) {
      $user->micropub_endpoint = $micropubEndpoint;
      $user->micropub_access_token = $token['auth']['access_token'];
      $user->micropub_scope = $token['auth']['scope'];
    }

    $user->token_response = $token['response'];
    $user->last_login = date('Y-m-d H:i:s');
    $user->save();
    $_SESSION['user_id'] = $user->id();

    // Make sure their default feed exists
    $channel = ORM::for_table('channels')->where('user_id', $_SESSION['user_id'])->where('type','default')->find_one();
    if(!$channel) {
      $channel = db\new_channel($_SESSION['user_id']);
      $channel->save();
    }
       
  }

  unset($_SESSION['auth_state']);

  if($redirectToDashboardImmediately) {
    $app->redirect('/', 301);
  } else {
    $html = render('auth_callback', array(
      'title' => 'Sign In',
      'me' => $me,
      'authorizing' => $me,
      'meParts' => parse_url($me),
      'tokenEndpoint' => $tokenEndpoint,
      'auth' => $token['auth'],
      'response' => $token['response'],
      'curl_error' => (array_key_exists('error', $token) ? $token['error'] : false)
    ));
    $app->response()->body($html);
  }
});

$app->get('/signout', function() use($app) {
  unset($_SESSION['auth']);
  unset($_SESSION['me']);
  unset($_SESSION['auth_state']);
  unset($_SESSION['user_id']);
  $app->redirect('/', 301);
});
