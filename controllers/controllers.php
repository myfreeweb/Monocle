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

    $channels = db\get_user_channels($_SESSION['user_id']);

    $main_channel = ORM::for_table('channels')
      ->where('user_id', $_SESSION['user_id'])
      ->where('type', 'default')
      ->find_one();

    $entries = db\get_entries_for_channel($main_channel->id);

    ob_start();
    render('channel', array(
      'title'       => 'Monocle',
      'meta'        => '',
      'channel'     => $main_channel,
      'channels'    => $channels,
      'entries'     => $entries
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

$app->get('/preview', function() use($app) {
  $params = $app->request()->params();
  $res = $app->response();

  $entry = ORM::for_table('entries')->where('url', $params['url'])->find_one();

  ob_start();
  render('preview-entry', [
    'title' => 'Preview',
    'entry' => $entry
  ]);
  $res->body(ob_get_clean());
});

