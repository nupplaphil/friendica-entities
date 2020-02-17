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

namespace Entities\Console;

use Asika\SimpleConsole\Console;
use Entities\Printer\FriendicaPhpPrinter;
use Entities\Util;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use RuntimeException;

/**
 * Checks/Creates Friendica entities
 */
class Entities extends Console
{
	const DEFAULT_BASE_DIR = 'src/Domain/Entities';

	protected $helpOptions = ['h', 'help', '?'];

	protected function getHelp()
	{
		return <<<HELP
console entities - Check/Create Friendica entitites
Usage
	bin/console entities <dbstructure> [-h|--help|-?] [-v]

Description
	Checks/Creates Friendica entities

Options
    -b           The basedir
    -h|--help|-? Show help information
    -v           Show more debug information.
HELP;
	}

	protected function doExecute()
	{
		if ($this->getOption('v')) {
			$this->out('Class: ' . __CLASS__);
			$this->out('Arguments: ' . var_export($this->args, true));
			$this->out('Options: ' . var_export($this->options, true));
		}

		if (count($this->args) == 0) {
			$this->out($this->getHelp());
			return 0;
		}

		$dbstructureFile = __DIR__ . '/../../' . $this->getArgument(0);
		if (!file_exists($dbstructureFile)) {
			throw new RuntimeException('Invalid db structure.');
		}

		$baseDir = __DIR__ . '/../../' . $this->getOption('b', self::DEFAULT_BASE_DIR);
		if (!file_exists($baseDir)) {
			throw new RuntimeException('Invalid base path.');
		}

		$dbstructure = include $dbstructureFile;

		foreach ($dbstructure as $name => $table) {
			$className = Util::getClassName($name);
			$dirPath   = Util::getDirs($name, '/');
			$nsPath    = Util::getDirs($name, '\\');

			$fullDir = $baseDir . '/' . $dirPath . '/';
			if (!file_exists($fullDir)) {
				mkdir($fullDir, 0777, true);
			}

			$fullName  = $fullDir . $className . '.php';
			$classPath = 'Friendica\Domain\Entity\\' . ($nsPath ? $nsPath . '\\' : '') . $className;

			$oldClass = null;
			if (class_exists($classPath)) {
				$oldClass = ClassType::from($classPath);
				$exists   = true;
				$fields   = $oldClass->getProperties();
				$oldClass->setComment(null);
			} else {
				$generator = new ClassType($className);
				$exists    = false;
				$fields    = [];
				$generator->setExtends('Friendica\BaseEntity');
			}

			$generator->addComment(sprintf('Entity class for table %s', $name))
			          ->addComment('');

			if ($generator->hasMethod('toArray')) {
				$generator->removeMethod('toArray');
			}

			$returnArray = $generator->addMethod('toArray')
			                         ->setPublic()
			                         ->addComment('{@inheritDoc}')
			                         ->addBody('return [');

			$file = new PhpFile();
			$file->addComment(<<<LIC
@copyright Copyright (C) 2020, Friendica

@license GNU APGL version 3 or any later version

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/>.

Used to check/generate entities for the Friendica codebase
LIC
			);
			$file->setStrictTypes();

			$namespace = $file->addNamespace('Friendica\Domain\Entity' . ($nsPath ? '\\' . $nsPath : ''));
			$namespace->addUse('\Friendica\BaseEntity');

			foreach ($table as $key => $value) {
				switch ($key) {
					case 'comment':
						$generator->addComment($value);
						break;
					case 'fields':
						foreach ($value as $field => $attributes) {
							$fieldExist = false;
							foreach ($fields as $currField) {
								if ($currField->getName() === Util::camelCase($field)) {
									$returnArray->addBody(sprintf("\t'%s' => \$this->%s,", $field, $currField->getName()));
									$fieldExist = true;
									break;
								}
							}

							if ($fieldExist) {
								continue;
							}

							$isBoolean   = false;
							$property    = $generator->addProperty(Util::camelCase($field))->setPrivate();
							$getter      = $generator->addMethod(Util::camelCase('get_' . $field))
							                         ->setPublic()
							                         ->addBody(sprintf('return $this->%s;', $property->getName()));
							$setter      = $generator->addMethod(Util::camelCase('set_' . $field))
							                         ->setPublic()
							                         ->addBody(sprintf('$this->%s = $%s;', $property->getName(), $property->getName()));
							$setterParam = $setter->addParameter($property->getName());
							$returnArray->addBody(sprintf("\t'%s' => \$this->%s,", $field, $property->getName()));
							foreach ($attributes as $name => $attribute) {
								switch ($name) {
									case 'type':
										$property->addComment(sprintf('@var %s', Util::getDbType($attribute)));
										$getter->addComment(sprintf('@return %s', Util::getDbType($attribute)));
										$setter->addComment(sprintf('@param %s $%s', Util::getDbType($attribute), $property->getName()));
										$setterParam->setType(Util::getDbType($attribute));
										if ($attribute === 'boolean') {
											$isBoolean = true;
											$newGetter = $getter->cloneWithName(Util::camelCase('is_' . $field));
											$generator->setMethods([$newGetter]);
											$generator->removeMethod($getter->getName());
											$getter = $newGetter;
											unset($newGetter);
											$property->setValue((bool)$property->getValue());
										}
										break;
									case 'comment':
										$property->addComment($attribute);
										break;
									case 'primary':
										if ($attribute) {
											$generator->removeMethod($setter->getName());
										}
										break;
									case 'relation':
										foreach ($attribute as $relTable => $relField) {
											$nsRel = Util::getDirs($relTable, '\\');
											$generator->addMethod(Util::camelCase('get_' . $relTable))
											          ->addComment(sprintf('Get %s', Util::getClassName($relTable)))
											          ->addComment('')
											          ->addComment(sprintf('@return %s', ($nsRel ? $nsRel . '\\' : '') . Util::getClassName($relTable)))
											          ->addBody('//@todo use closure')
											          ->addBody(sprintf('throw new NotImplementedException(\'lazy loading for %s is not implemented yet\');', Util::camelCase($relField)));
											$namespace->addUse('Friendica\Network\HTTPException\NotImplementedException');
											if (!empty($nsRel)) {
												$namespace->addUse('Friendica\Domain\Entity\\' . $nsRel);
											}
										}
										break;
									case 'default':
										$property->setValue($isBoolean ? (bool)$attribute : $attribute);
								}
							}
						}
						break;
				}
			}

			$returnArray->addBody('];');

			$namespace->add($generator);

			file_put_contents($fullName, (new FriendicaPhpPrinter())->printFile($file), FILE_USE_INCLUDE_PATH);
		}

		return 0;
	}
}
