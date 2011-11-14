<?php

require_once('lib.php');

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

// Set the asked_year variable
$asked_year = getAskedYear();

// Set the displayed_year variable
if ($asked_year)
{
	$displayed_year = $asked_year;	
}
else
{
	$displayed_year = intval(date('Y'));
}

// Get the movies
$movies = Movie::getBestMovies(intval($config->get('number_of_movies')), $asked_year);
$movies_table = buildMoviesTable($movies);

// Get the template
$template = file_get_contents($config->get('template_file'));

// Replace the template variables
$variables = array(
	'%YEAR%'	=> $displayed_year,
	'%MOVIES%'	=> $movies_table
);
$html = strtr($template, $variables);

// Send back the final HTML code
echo $html;
