<?php

$app->get('/subscriptions/?', function($format = 'html') use ($app) {
  if($user=require_login($app)) {
    $res = $app->response();

    ob_start();
    render('subscriptions', array(
      'title'       => 'Subscriptions',
      'meta'        => ''
    ));
    $html = ob_get_clean();
    $res->body($html);
  }
});

$app->post('/subscriptions/discover', function($format='json') use($app) {
  if($user=require_login_json($app)) {

    $feeds = array(
      array('url' => 'http://pk.dev/', 'display_url' => friendly_url('http://pk.dev/'), 'type' => 'microformats2'),
      array('url' => 'http://pk.dev/articles.atom', 'display_url' => friendly_url('http://pk.dev/articles.atom'), 'type' => 'atom')
    );

    json_response($app, array(
      'feeds' => $feeds
    ));
  }
});

$app->post('/subscriptions/add', function($format='json') use($app) {
  if($user=require_login_json($app)) {

    

    json_response($app, array(
      'result' => 'ok'
    ));
  }
});
