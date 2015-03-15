<?php

$app->get('/', function ($format = 'html') use ($app) {
  $res = $app->response();

  if(!is_logged_in()) {

    ob_start();
    render('index-public', array(
      'title'       => 'Monocle',
      'meta'        => ''
    ));
    $html = ob_get_clean();
    $res->body($html);

  } else {

    $channels = ORM::for_table('channels')
      ->where('user_id', $_SESSION['user_id'])
      ->where_not_equal('type', 'default')
      ->find_many();

    $main_channel = ORM::for_table('channels')
      ->where('user_id', $_SESSION['user_id'])
      ->where('type', 'default')
      ->find_one();

    ob_start();
    render('index', array(
      'title'       => 'Monocle',
      'meta'        => '',
      'channel'     => $main_channel,
      'channels'    => $channels
    ));
    $html = ob_get_clean();
    $res->body($html);
  }
});

$app->get('/docs/?', function($format = 'html') use ($app) {

  $res = $app->response();

  ob_start();
  render('docs', array(
    'title'       => 'Docs',
    'meta'        => '',
  ));
  $html = ob_get_clean();
  $res->body($html);

});

