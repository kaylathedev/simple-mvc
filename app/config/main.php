<?php
// This file is loaded immediately by the application.


// This call allows the page "/" to be the same as "/posts"
Config::route('/', '/nav/home');


// Creates a new logger, more specifically, a file logger.
// A Logger can write errors, warnings, or debug information.
$logger = new FileLogger();

// Sets the filename the logger should write to.
$logger->setFilename(APP . DS . 'tmp' . DS . 'main.log');


// Creates a new database.
// PDO is a wrapper for many database. For example, MySQL, or SQL Server.
$db = new PDODatabase();

// "Attaches" the logger to the database, so the database can log stuff.
$db->setLogger($logger);

// The next lines set the database connection info.
// The connection string defines where the database is.
// Read more on http://us3.php.net/manual/en/ref.pdo-mysql.connection.php
$db->setConnectionString('mysql:host=localhost;dbname=example');
$db->setUsername('root');
$db->setPassword('');


// "Config::set" will save variables to every script.
// Useful if you need to access a database, without creating a new one.
Config::set('auto_add_root', true); // TODO: Find use.
Config::set('db', $db);
Config::set('logger', $logger);


// A PHP function. It sets the default timezone.
date_default_timezone_set('UTC');

