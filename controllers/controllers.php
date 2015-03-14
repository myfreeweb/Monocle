<?php

$app->get('/', function ($format = 'html') use ($app) {
  $res = $app->response();

  $channels = ORM::for_table('channels')
    ->where('user_id', $_SESSION['user_id'])
    ->where_not_equal('type', 'default')
    ->find_many();

  ob_start();
  render('index', array(
    'title'       => 'Monocle',
    'meta'        => '',
    'channels'    => $channels,
    'entries'     => []
  ));
  $html = ob_get_clean();
  $res->body($html);
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

