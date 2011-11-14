<?php

require_once('Check.php');
require_once('lib.php');

class Movie
{
	private $id;
	private $rank;
	private $rating;
	private $title;
	private $release_year;
	private $number_of_votes;
	private $modified;
	
	function __construct($id)
	{
		global $db;

		if (!Check::id($id))
		{
			throw new Exception('Bad Movie ID');
		}

		$this->setId($id);

		$result = $db->query('SELECT rank, rating, title, release_year, number_of_votes FROM Movies WHERE id = '.$id.';');

		if ($result && $result->num_rows === 1)
		{
			$row = $result->fetch_assoc();
			$this->setRank(intval($row['rank']));
			$this->setRating(floatval($row['rating']));
			$this->setTitle(strval($row['title']));
			$this->setReleaseYear(intval($row['release_year']));
			$this->setNumberOfVotes(intval($row['number_of_votes']));
			$this->modified = false;
		}
	}
	
	public function save()
	{
		global $db;

		if ($this->modified)
		{
			debug('Saving the movie '.$this->id.' into the db...');
			$query = '
				UPDATE Movies
				SET rank = '.$this->rank.',
					rating = '.$this->rating.',
					title = \''.addslashes($this->title).'\',
					release_year = '.$this->release_year.',
					number_of_votes = '.$this->number_of_votes.'
				WHERE id = '.$this->id.'
			;';
		
			$result = $db->query($query);

			if (!$result)
			{
				throw new Exception($db->error);
			}

			// If no entry has been updated, create it
			if ($db->affected_rows === 0)
			{
				$query = '
					INSERT INTO Movies
					(id, rank, rating, title, release_year, number_of_votes)
					VALUES
					('.$this->id.', '.$this->rank.', '.$this->rating.', \''.addslashes($this->title).'\', '.$this->release_year.', '.$this->number_of_votes.');
				';
				if (!$db->query($query))
				{
					throw new Exception($db->error);
				}
			}
			
			$this->modified = false;
		}
		else
		{
			debug('Not saving the movie '.$this->id.' into the db, it has not been modified');
		}
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function setId($value)
	{
		if (!Check::id($value))
		{
			throw new Exception('Bad ID');
		}

		if ($this->id != $value)
		{
			$this->id = $value;		
			$this->modified = true;
		}
	}
	
	public function getRank()
	{
		return $this->rank;
	}

	public function setRank($value)
	{
		
		if (!Check::rank($value))
		{
			throw new Exception('Bad rank');
		}
		
		if ($this->rank != $value)
		{
			$this->rank = $value;		
			$this->modified = true;
		}
	}
	
	public function getRating()
	{
		return $this->rating;
	}

	public function setRating($value)
	{
		if (!Check::rating($value))
		{
			throw new Exception('Bad rating');
		}
		
		if ($this->rating != $value)
		{
			$this->rating = $value;		
			$this->modified = true;
		}
	}
	
	public function getTitle()
	{
		return $this->title;
	}

	public function setTitle($value)
	{
		if (!Check::name($value))
		{
			throw new Exception('Bad title');
		}
		
		if ($this->title != $value)
		{
			$this->title = $value;		
			$this->modified = true;
		}
	}
	
	public function getReleaseYear()
	{
		return $this->release_year;
	}

	public function setReleaseYear($value)
	{
		if (!Check::year($value))
		{
			throw new Exception('Bad year');
		}
		
		if ($this->release_year != $value)
		{
			$this->release_year = $value;		
			$this->modified = true;
		}
	}
	
	public function getNumberOfVotes()
	{
		return $this->number_of_votes;
	}
	
	public function setNumberOfVotes($value)
	{
		if (!Check::votes($value))
		{
			throw new Exception('Bad number of votes');
		}
		
		if ($this->number_of_votes != $value)
		{
			$this->number_of_votes = $value;		
			$this->modified = true;
		}
	}
	
	// Returns an array of the best movies of a specific year
	// Or of all times if no year has been specified
	public static function getBestMovies($number, $year = null)
	{
		if (!is_int($number) || $number <= 0)
		{
			throw new Exception('Bad number of movies specified');
		}

		if ($year && !Check::year($year))
		{
			throw new Exception('Bad year specified');
		}
		
		if (self::isCached($year))
		{
			$movies = self::getCache($year);
		}
		else
		{
			// Get movies from DB
			$movies = self::getBestMoviesFromDb($number, $year);
			self::setCache($movies, $year);
		}
		
		return $movies;
	}
	
	private static function getBestMoviesFromDb($number, $year = null)
	{
		global $db;
		
		$movies = array();
		
		if (!is_int($number) || $number <= 0)
		{
			throw new Exception('Bad number of movies specified');
		}
		
		if ($year && !Check::year($year))
		{
			throw new Exception('Bad year specified');
		}
		
		$query = '
			SELECT id
			FROM Movies
		';
		if ($year)
		{
			$query .= '
				WHERE release_year = '.$year.'
			';
		}
		$query .= '
			ORDER BY rank ASC
			LIMIT '.$number.'
		';
		
		$result = $db->query($query);

		if (!$result)
		{
			throw new Exception($db->error);
		}
		
		while ($row = $result->fetch_assoc())
		{
			$movies[] = new Movie(intval($row['id']));
		}

		$result->free();
		
		return $movies;
	}
	
	private static function getCache($year = null)
	{
		if ($year && !Check::year($year))
		{
			throw new Exception('Bad year specified');
		}
		else
		{
			$year = 'all';
		}
		
		$data = file_get_contents(CACHE_FOLDER.'/'.$year);
		$movies = unserialize($data);

		return $movies;
	}
	
	private function setCache($movies, $year = null)
	{
		if ($year && !Check::year($year))
		{
			throw new Exception('Bad year specified');
		}
		else
		{
			$year = 'all';
		}
		
		$filepath = 
		$data = serialize($movies);
		file_put_contents(CACHE_FOLDER.'/'.$year, $data);
	}
	
	private function isCached($year = null)
	{
		if ($year && !Check::year($year))
		{
			throw new Exception('Bad year specified');
		}
		else
		{
			$year = 'all';
		}
		
		return file_exists(CACHE_FOLDER.'/'.$year);
	}
}
