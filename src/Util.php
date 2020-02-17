<?php
/**
 * @copyright Copyright (C) 2020, Friendica
 *
 * @license GNU APGL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * Used to check/generate entities for the Friendica codebase
 *
 */
declare(strict_types=1);

namespace Entities;

use Nette\PhpGenerator\Type;
use ReflectionFunction;
use ReflectionMethod;

class Util
{
	// replaces digits with their names
	public static function digitToText(string $name)
	{
		$name = str_replace('0', 'zero_', $name);
		$name = str_replace('1', 'one_', $name);
		$name = str_replace('2', 'two_', $name);
		$name = str_replace('3', 'three_', $name);
		$name = str_replace('4', 'four_', $name);
		$name = str_replace('5', 'five_', $name);
		$name = str_replace('6', 'six_', $name);
		$name = str_replace('7', 'seven_', $name);
		$name = str_replace('8', 'eight_', $name);
		$name = str_replace('9', 'nine_', $name);
		return $name;
	}

	// Replaces underlines ("_") with camelCase notation (for variables)
	public static function camelCase($str)
	{
		$i   = ["-", "_"];
		$str = self::digitToText($str);
		$str = preg_replace('/([a-z])([A-Z])/', "\\1 \\2", $str);
		$str = preg_replace('@[^a-zA-Z0-9\-_ ]+@', '', $str);
		$str = str_replace($i, ' ', $str);
		$str = str_replace(' ', '', ucwords(strtolower($str)));
		$str = strtolower(substr($str, 0, 1)) . substr($str, 1);
		return $str;
	}

	// Like camelcase, but with Uppercasing the first letter (for classes)
	public static function toClassName($str)
	{
		$str = self::camelCase($str);
		return ucfirst($str);
	}

	// Custom mapping of db-types to PHP types
	public static function getDbType(string $type)
	{
		switch ($type) {
			case 'int unsigned':
			case 'longblob':
			case 'mediumint unsigned':
			case 'int':
				return Type::INT;
			case 'datetime':
				// @todo Replace with "real" datetime
				return Type::STRING;
			case 'boolean':
				return Type::BOOL;
			default:
				return Type::STRING;
		}
	}

	// returns the class name based on a given table name
	public static function getClassName(string $str)
	{
		$names = preg_split('/[-]+/', $str);
		return self::toClassName($names[count($names) - 1]);
	}

	// returns a directory sequence based on a given table name
	public static function getDirs(string $str, string $del = '/')
	{
		$names = preg_split('/[-]+/', $str);
		$dirs  = '';
		for ($i = 0; $i < count($names) - 1; $i++) {
			$dirs .= self::toClassName($names[$i]) . $del;
		}
		return substr($dirs, 0, (strlen($dirs) - strlen($del)));
	}

	public static function get_function($method, $class = null)
	{

		if (!empty($class)) $func = new ReflectionMethod($class, $method);
		else $func = new ReflectionFunction($method);

		$f          = $func->getFileName();
		$start_line = $func->getStartLine() - 1;
		$end_line   = $func->getEndLine();
		$length     = $end_line - $start_line;

		$source = file($f);
		$source = implode('', array_slice($source, 0, count($source)));
		$source = preg_split("/" . PHP_EOL . "/", $source);

		$body = '';
		for ($i = $start_line; $i < $end_line; $i++)
			$body .= "{$source[$i]}\n";

		return $body;
	}
}
