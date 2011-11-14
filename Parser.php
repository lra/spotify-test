<?php

class Parser
{
	private $url;
	private $parsed_movies;
	private $number_of_movies;
	
	function __construct($url, $nb)
	{
		$this->url = $url;
		$this->number_of_movies = $nb;
	}
	
	public function parse()
	{
		libxml_use_internal_errors(TRUE);
		$dom = new DOMDocument;
		$dom->loadHTMLFile($this->url);
		libxml_clear_errors();
		$xp = new DOMXpath($dom);
		$query = '//div[@id="main"]';
		$query = '//table/tr';
		$elements = $xp->query($query);

		$i = 0;
		$movies = array();
		foreach ($elements as $element)
		{
			if ($i > $this->number_of_movies)
			{
				break;
			}
			
			if ($i > 0)
			{
				$nodes = $element->childNodes;
				$j = 0;
				$movie = array();
				foreach ($nodes as $node)
				{
					switch($j)
					{
						// Parse the rank of the movie
						case 0:
							list($movie['rank']) = sscanf($node->nodeValue, '%u.');
							break;

						// Parse the rating of the movie
						case 1:
							list($movie['rating']) = sscanf($node->nodeValue, '%f');
							break;

						// Parse the name and the year of the movie
						case 2:
							// Get the URL of the movie
							$href = $node->firstChild->firstChild->attributes->getNamedItem('href')->nodeValue;
							// Deduct the IMDB id of the movie
							list($movie['id']) = sscanf($href, '/title/tt%u/');
							
							// Then get the title and the release year
							preg_match('/(.+) \((\d{4})\)/', $node->nodeValue, $matches);
							$movie['title'] = $matches[1];
							list($movie['release_year']) = sscanf($matches[2], '%u');
							break;

						// Parse the votes of the movie
						case 3:
							$nb_without_commas = str_replace(',', '', $node->nodeValue);
							list($movie['number_of_votes']) = sscanf($nb_without_commas, '%u');
							break;
					}
					$j++;
				}
				$movies[] = $movie;
			}
			$i++;
		}

		$this->parsed_movies = $movies;
	}
	
	public function getParsedMovies()
	{
		return $this->parsed_movies;
	}
}