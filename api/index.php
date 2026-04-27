<?php

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Vercel usa X-Forwarded-Proto, no $_SERVER['HTTPS']
$proto = $_SERVER['HTTP_X_FORWARDED_PROTO'] 
      ?? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http');

$url = $proto . '://' . $host;

putenv('APP_URL=' . $url);
putenv('ASSET_URL=' . $url);
$_ENV['APP_URL'] = $url;
$_ENV['ASSET_URL'] = $url;
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Vercel usa X-Forwarded-Proto, no $_SERVER['HTTPS']
$proto = $_SERVER['HTTP_X_FORWARDED_PROTO'] 
      ?? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http');

$url = $proto . '://' . $host;

putenv('APP_URL=' . $url);
putenv('ASSET_URL=' . $url);
$_ENV['APP_URL'] = $url;
$_ENV['ASSET_URL'] = $url;


require (__DIR__.'/../public/index.php');