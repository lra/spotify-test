<?php

class Check
{
	const MAX_TEXT_LENGTH = 255;

	static public function name($string)
	{
		global $errors;

		if (!is_string($string))
		{
			$errors[] = '"'.strval($string).'" is not a string';
			return false;
		}

		if (strlen($string) > self::MAX_TEXT_LENGTH)
		{
			$errors[] = '"'.strval($string).'" is too long';
			return false;
		}

		if (empty($string))
		{
			$errors[] = '"'.strval($string).'" is empty';
			return false;
		}

		return true;
	}

	static public function id($string)
	{
		global $errors;

		if (!is_int($string))
		{
			$errors[] = '"'.strval($string).'" is not an integer';
			return false;
		}

		if ($string === 0)
		{
			$errors[] = '"'.strval($string).'" equals zero';
			return false;
		}

		return true;
	}
	
	static public function rank($value)
	{
		return self::id($value);
	}
	
	static public function year($value)
	{
		return self::id($value);
	}

	static public function votes($value)
	{
		return self::id($value);
	}
	
	static public function rating($string)
	{
		global $errors;

		if (!is_float($string))
		{
			$errors[] = '"'.strval($string).'" is not a float';
			return false;
		}

		if ($string < 0)
		{
			$errors[] = '"'.strval($string).'" is lower than 0';
			return false;
		}

		if ($string > 10)
		{
			$errors[] = '"'.strval($string).'" is higher than 10';
			return false;
		}

		return true;
	}
}
