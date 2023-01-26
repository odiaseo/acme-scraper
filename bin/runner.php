#!/usr/bin/env php
<?php
require __DIR__.'/../vendor/autoload.php';

use App\Command\ScraperCommand;
use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new ScraperCommand());
$application->run();
