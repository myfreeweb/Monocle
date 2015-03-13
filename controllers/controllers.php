<?php

$app->get('/', function ($format = 'html') use ($app) {
  $res = $app->response();

  ob_start();
  render('index', array(
    'title'       => 'Monocle',
    'meta'        => '',
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

