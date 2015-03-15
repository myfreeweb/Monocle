<?php
namespace db;
use \ORM;

function random_hash() {
  $len = 32;
  $alpha_numeric = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  return substr(str_shuffle(str_repeat($alpha_numeric, $len)), 0, $len);
}

function new_feed() {
  $feed = ORM::for_table('feeds')->create();
  $feed->hash = random_hash();
  $feed->date_created = date('Y-m-d H:i:s');
  $feed->date_updated = date('Y-m-d H:i:s');
  return $feed;
}

function new_channel($user_id, $name='Home', $type='default') {
  $channel = ORM::for_table('channels')->create();
  $channel->user_id = $user_id;
  $channel->name = $name;
  $channel->type = $type;
  $channel->date_created = date('Y-m-d H:i:s');
  $channel->date_updated = date('Y-m-d H:i:s');
  return $channel;
}

function get_channel($user_id, $channel_id) {
  return ORM::for_table('channels')
    ->where('id', $channel_id)
    ->where('user_id', $user_id)
    ->find_one();
}

function add_source($channel_id, $feed_id, $filter=false) {
  // Check if the source already exists
  $source = ORM::for_table('channel_sources')->where('channel_id', $channel_id)->where('feed_id', $feed_id)->find_one();
  if($source) {
    $source->filter = $filter;
    $source->date_updated = date('Y-m-d H:i:s');
  } else {
    $source = ORM::for_table('channel_sources')->create();
    $source->channel_id = $channel_id;
    $source->feed_id = $feed_id;
    $source->filter = $filter;
    $source->date_created = date('Y-m-d H:i:s');
  }
  $source->save();
  return $source;
}
