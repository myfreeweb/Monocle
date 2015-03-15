<?php
declare(ticks=1);

chdir('..');

$mode = 'run';
if(array_key_exists(1, $argv) && $argv[1] == 'once')
  $mode = 'once';

if($mode == 'run') {
  if(function_exists('pcntl_signal')) {
    pcntl_signal(SIGINT, function($sig){
      global $pcntl_continue;
      $pcntl_continue = FALSE;
    });
  }
}
$pcntl_continue = TRUE;

define('PDO_SUPPORT_DELAYED', TRUE);

require 'vendor/autoload.php';

if(count($argv) < 2) {
  echo "Usage example: php manager.php worker\n";
  exit(0);
}

if($mode == 'once') {
  DeferredTask::run_once();
} else {
  DeferredTask::run();
}

