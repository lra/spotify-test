<?php
// The name of the configuration file
define('CONFIG_FILE',	'config.ini');
define('CACHE_FOLDER',	'cache');

// Debug mode
define('DEBUG', true);

require_once('Movie.php');
require_once('Check.php');

// Configuration class used to store and retrieve the configuration variables
class Config
{
	private $config_array;
	
	function __construct($config_file)
	{
		$this->config_array = parse_ini_file($config_file);
	}
	
	public function get($name)
	{
		$value = null;
		
		if (isset($this->config_array[$name]))
		{
			$value = $this->config_array[$name];
		}
		
		return $value;
	}
}

function debug($message)
{
	if (DEBUG)
	{
		echo "$message\n";
	}
}

function getAskedYear()
{
	$year = null;

	if (isset($_GET['year']))
	{
		if (Check::year(intval($_GET['year'])))
		{
			$year = intval($_GET['year']);
		}
	}

	return $year;
}

function buildMoviesTable($movies)
{
	$html = '<table>';
	$html .= '<tr>';
	$html .= '<th>Rank</th>';
	$html .= '<th>Rating</th>';
	$html .= '<th>Title</th>';
	$html .= '<th>Votes</th>';
	$html .= '</tr>';

	if (count($movies) > 0)
	{
		foreach($movies as $movie)
		{
			$html .= '<tr>';
			$html .= '<td>'.$movie->getRank().'</td>';
			$html .= '<td>'.$movie->getRating().'</td>';
			$html .= '<td>'.$movie->getTitle().'</td>';
			$html .= '<td>'.$movie->getNumberOfVotes().'</td>';
			$html .= '</tr>';
		}
	}
	else
	{
		$html .= '<tr>';
		$html .= '<td colspan="4">No movie for this year =(</td>';
		$html .= '</tr>';
	}
	
	$html .= '</table>';
	
	return $html;
}
