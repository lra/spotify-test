#!/usr/bin/env php -f
<?php

// Load the library and the configuration
require_once('lib.php');
require_once('Parser.php');

// Instanciate a config object
$config = new Config(CONFIG_FILE);

// Instanciate a MySQL object
$db = new mysqli(
    $config->get('mysql_host'),
    $config->get('mysql_username'),
    $config->get('mysql_password'),
    $config->get('mysql_database'),
    $config->get('mysql_port')
);

// Get the list of the top movies at IMDB
$parser = new Parser($config->get('imdb_top_page_url'), $config->get('number_of_movies'));

// Get the web page content to parse
$parser->parse();
//var_dump($parser->getParsedMovies());
// For each movie found, create a Movie instance
foreach ($parser->getParsedMovies() as $raw_movie)
{
    $movie = new Movie($raw_movie['id']);
    $movie->setRank($raw_movie['rank']);
    $movie->setRating($raw_movie['rating']);
    $movie->setTitle($raw_movie['title']);
    $movie->setReleaseYear($raw_movie['release_year']);
    $movie->setNumberOfVotes($raw_movie['number_of_votes']);
    $movie->save();
}
