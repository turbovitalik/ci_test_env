<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['socket_type'] = ''; //`tcp` or `unix`
$config['host'] = '';
$config['password'] = '';
$config['port'] = '';
$config['timeout'] = 0;
$config['db'] = REDIS_DB;
$config['db_default'] = 0;
