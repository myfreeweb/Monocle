<?php
namespace db;
use \ORM;

function random_hash() {
  $len = 32;
  $alpha_numeric = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  return substr(str_shuffle(str_repeat($alpha_numeric, $len)), 0, $len);
}

function set_updated(&$record) {
  $record->date_updated = date('Y-m-d H:i:s');
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

function get_feed($feed_id) {
  return ORM::for_table('feeds')
    ->where('id', $feed_id)
    ->find_one();
}

function get_source($channel_id, $feed_id) {
  return ORM::for_table('channel_sources')
    ->where('channel_id', $channel_id)
    ->where('feed_id', $feed_id)
    ->find_one();
}

function get_feed_for_user($user_id, $feed_id) {
  return ORM::for_table('channel_sources')
    ->select('feeds.*')
    ->select('channel_sources.display_name')
    ->select('channel_sources.filter')
    ->join('feeds', ['channel_sources.feed_id','=','feeds.id'])
    ->join('channels', ['channel_sources.channel_id','=','channels.id'])
    ->where('channel_sources.feed_id', $feed_id)
    ->where('channels.user_id', $user_id)
    ->find_one();
}

function get_user_channels($user_id) {
  return ORM::for_table('channels')
    ->where('user_id', $user_id)
    ->order_by_asc('type', '=', 'default')
    ->find_many();
}

function feed_display_name(&$record) {
  if($record['display_name'])
    return $record['display_name'];
  return friendly_url($record['feed_url']);
}

function get_entries_for_channel($channel_id) {
  return ORM::for_table('entries')
    ->join('channel_entries', ['channel_entries.entry_id','=','entries.id'])
    ->where('channel_entries.channel_id', $channel_id)
    ->order_by_desc('entries.date_published')
    ->limit(30)
    ->find_many();
}

function add_source($channel_id, $feed_id, $filter=false) {
  // Check if the source already exists
  $source = ORM::for_table('channel_sources')
    ->where('channel_id', $channel_id)
    ->where('feed_id', $feed_id)
    ->find_one();

  if($filter) {
    // split on commas and spaces
    $filter = preg_split('/[ ,]+/', $filter);
    $filter = join(',',$filter);
  }

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

function get_feeds_for_channel($channel_id) {
  $feeds = ORM::for_table('channel_sources')
    ->join('feeds', ['channel_sources.feed_id','=','feeds.id'])
    ->where('channel_id', $channel_id)
    ->order_by_desc('channel_sources.date_created')
    ->find_many();
  return $feeds;
}
