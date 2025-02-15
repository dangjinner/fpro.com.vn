<?php

if (! function_exists('setting')) {
    /**
     * Get / set the specified setting value.
     *
     * If an array is passed, we'll assume you want to set settings.
     *
     * @param string|array $key
     * @param mixed $default
     * @return mixed|\Modules\Setting\Repository
     */
    function setting($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('setting');
        }
        
        if (is_array($key)) {
            return app('setting')->set($key);
        }
        
        try {
            return app('setting')->get($key, $default);
        } catch (PDOException $e) {
            return $default;
        }

    }
}

if (! function_exists('is_serialized')) {
	function is_serialized($value, &$result = null)
	{
		// Bit of a give away this one
		if (!is_string($value))
		{
			return false;
		}

		// Serialized false, return true. unserialize() returns false on an
		// invalid string or it could return false if the string is serialized
		// false, eliminate that possibility.
		if ($value === 'b:0;')
		{
			$result = false;
			return true;
		}

		$length	= strlen($value);
		$end	= '';

		switch ($value[0])
		{
			case 's':
				if ($value[$length - 2] !== '"')
				{
					return false;
				}
			case 'b':
			case 'i':
			case 'd':
				// This looks odd but it is quicker than isset()ing
				$end .= ';';
			case 'a':
			case 'O':
				$end .= '}';

				if ($value[1] !== ':')
				{
					return false;
				}

				switch ($value[2])
				{
					case 0:
					case 1:
					case 2:
					case 3:
					case 4:
					case 5:
					case 6:
					case 7:
					case 8:
					case 9:
					break;

					default:
						return false;
				}
			case 'N':
				$end .= ';';

				if ($value[$length - 1] !== $end[0])
				{
					return false;
				}
			break;

			default:
				return false;
		}

		if (($result = @unserialize($value)) === false)
		{
			$result = null;
			return false;
		}
		return true;
	}
}
