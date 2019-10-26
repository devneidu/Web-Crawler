<?php
require 'App\Crawler.php';
use App\Crawler;

$crawler = new Crawler('http://127.0.0.1:5502');
$crawler->crawlContents('');

