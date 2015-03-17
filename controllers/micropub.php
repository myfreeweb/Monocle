<?php

$app->post('/micropub/like', function() use($app){
  if($user=require_login_json($app)) {
    $params = $app->request()->params();

    $mp_params = [
      'like-of' => $params['url']
    ];

    $r = micropub_post($user->micropub_endpoint, $mp_params, $user->micropub_access_token);
    $response = $r['response'];

    if($response && preg_match('/Location: (.+)/', $response, $match)) {
      $location = $match[1];
      $user->micropub_success = 1;
      $user->save();
    } else {
      $location = false;
    }

    json_response($app, [
      'location' => $location,
      'status' => $r['status'],
      'response' => trim($response),
      'error' => $r['error']
    ]);
  }
});

$app->post('/micropub/repost', function() use($app){
  if($user=require_login_json($app)) {
    $params = $app->request()->params();

    $mp_params = [
      'repost-of' => $params['url']
    ];

    $r = micropub_post($user->micropub_endpoint, $mp_params, $user->micropub_access_token);
    $response = $r['response'];

    if($response && preg_match('/Location: (.+)/', $response, $match)) {
      $location = $match[1];
      $user->micropub_success = 1;
      $user->save();
    } else {
      $location = false;
    }

    json_response($app, [
      'location' => $location,
      'status' => $r['status'],
      'response' => trim($response),
      'error' => $r['error']
    ]);
  }
});


/////

$app->post('/micropub/post', function() use($app){
  if($user=require_login($app)) {
    $params = $app->request()->params();

    // Now send to the micropub endpoint
    $post_id = $params['post_id'];
    unset($params['post_id']);
    $r = micropub_post($user->micropub_endpoint, $params, $user->micropub_access_token);
    $response = $r['response'];

    // Check the response and look for a "Location" header containing the URL
    if($response && preg_match('/Location: (.+)/', $response, $match)) {
      $location = $match[1];
      $user->micropub_success = 1;
    } else {
      $location = false;
    }

    $user->save();

    $app->response()['Content-Type'] = 'application/json';
    $app->response()->body(json_encode(array(
      'response' => htmlspecialchars(trim($response)),
      'location' => $location,
      'error' => $r['error'],
      'curlinfo' => $r['curlinfo'],
      'post_id' => $post_id
    )));
  }
});

