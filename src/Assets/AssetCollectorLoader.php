<?php

namespace Matze\Core\Assets;

use Matze\Core\Assets\Rules\MergableFilesDefinition;
use Matze\Core\Assets\Rules\MergableProcessor;
use Matze\Core\Assets\Rules\Processor;
use Matze\Core\Traits\ServiceContainerTrait;

/**
 * @Service(public=false)
 */
class AssetCollectorLoader {
	use ServiceContainerTrait;

	public function loadProcessors() {
		$final_processors = [];

		$file_content = file_get_contents(ROOT . AssetCollector::ASSETS_DIR . '/assets.json');
		$processors = json_decode($file_content, true);

		foreach ($processors as $id => $definition) {
			$processor_id = 'Assets.' . $id;
			/** @var Processor $service */
			$service = clone $this->getService($processor_id);

			foreach ($definition as $file) {
				/** @var MergableProcessor $service */
				$file_definition = new MergableFilesDefinition($file['name'], $file['files']);
				$service->addDefinition($file_definition);
			}

			$final_processors[] = $service;
		}

		return $final_processors;
	}
} 