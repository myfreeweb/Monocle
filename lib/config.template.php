<?php
class Config {
  public static $base_url = 'https://example.com';
  public static $hostname = 'example.com';
  public static $ssl = false;

  public static $dbHost = '127.0.0.1';
  public static $dbName = 'reader';
  public static $dbUsername = 'reader';
  public static $dbPassword = '';

  public static $beanstalkServer = '127.0.0.1';
  public static $beanstalkPort = 11300;

  public static $defaultAuthorizationEndpoint = 'https://indieauth.com/auth';
}

