<?php

namespace BrainExe\Core\Console;

use BrainExe\Core\Authentication\RegisterTokens;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Command
 */
class SetVersionCommand extends Command {

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this->setName('version:set')
			->setDescription('Create a new composer version and add a git tag')
			->addArgument('version', InputArgument::REQUIRED, 'e.g. 1.0.23');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$version = $input->getArgument('version');

		$composer_json = file_get_contents(ROOT . 'composer.json');
		$composer_json = json_decode($composer_json);
		$composer_json['version'] = $version;

		file_put_contents(ROOT . 'composer.json', json_encode($composer_json, JSON_PRETTY_PRINT));

		$command = 'git add composer.json; git commit -m "set version to %s"; git tag %s';
	}

} 
