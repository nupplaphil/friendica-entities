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

use Asika\SimpleConsole\CommandArgsException;
use Dice\Dice;

class Console extends \Asika\SimpleConsole\Console
{
	// Disables the default help handling
	protected $helpOptions = [];
	protected $customHelpOptions = ['h', 'help', '?'];

	/** @var Dice */
	protected $dice;

	protected function getHelp()
	{
		return <<<HELP
Usage: vendor/bin/friendica-entities [--version] <command> [-h|--help|-?] [-v]

Commands:
	entities               Check/Create entities

Options:
	-h|--help|-? Show help information
	-v           Show more debug information.
HELP;

	}
	protected $subConsoles = [
		'entities'               => Console\Entities::class,
	];

	/**
	 * constructor.
	 *
	 * @param Dice $dice The DI library
	 * @param array $argv
	 */
	public function __construct(Dice $dice, array $argv = null)
	{
		parent::__construct($argv);

		$this->dice = $dice;
	}

	protected function doExecute()
	{
		if ($this->getOption('v')) {
			$this->out('Executable: ' . $this->executable);
			$this->out('Arguments: ' . var_export($this->args, true));
			$this->out('Options: ' . var_export($this->options, true));
		}

		$subHelp = false;
		$command = null;

		if ($this->getOption('version')) {
			$this->out('Friendica Entities Console version ');

			return 0;
		} elseif ((count($this->options) === 0 || $this->getOption($this->customHelpOptions) === true || $this->getOption($this->customHelpOptions) === 1) && count($this->args) === 0
		) {
		} elseif (count($this->args) >= 2 && $this->getArgument(0) == 'help') {
			$command = $this->getArgument(1);
			$subHelp = true;
			array_shift($this->args);
			array_shift($this->args);
		} elseif (count($this->args) >= 1) {
			$command = $this->getArgument(0);
			array_shift($this->args);
		}

		if (is_null($command)) {
			$this->out($this->getHelp());
			return 0;
		}

		$console = $this->getSubConsole($command);

		if ($subHelp) {
			$console->setOption($this->customHelpOptions, true);
		}

		return $console->execute();
	}

	private function getSubConsole($command)
	{
		if ($this->getOption('v')) {
			$this->out('Command: ' . $command);
		}

		if (!isset($this->subConsoles[$command])) {
			throw new CommandArgsException('Command ' . $command . ' doesn\'t exist');
		}

		$subargs = $this->args;
		array_unshift($subargs, $this->executable);

		$className = $this->subConsoles[$command];

		/** @var Console $subconsole */
		$subconsole = $this->dice->create($className, [$subargs]);

		foreach ($this->options as $name => $value) {
			$subconsole->setOption($name, $value);
		}

		return $subconsole;
	}

}
