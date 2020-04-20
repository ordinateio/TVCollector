<?php
/**
 * TVCollector
 * Clear data for all resources.
 *
 * @package TVCollector
 * @author Callisto
 * @source https://github.com/callisto2410/TVCollector
 */
set_time_limit(0);

$resources = $modx->getCollection('modResource');
$modx->lexicon->load('tvcollector:default');
$counter = 0;
$usleep = 50000;


$modx->log(modX::LOG_LEVEL_INFO, $modx->lexicon('tvcollector.data_cleaning'));

foreach ($resources as $resource) {
    $resource->setProperties(array(), 'tvc', false);

    $ok = $resource->save();
    if (!$ok) {
        $modx->log(modX::LOG_LEVEL_WARN,
            $modx->lexicon('tvcollector.resource_could_not_be_saved', array(
                'id' => $resource->id
            ))
        );
        continue;
    }

    $modx->log(modX::LOG_LEVEL_INFO,
        $modx->lexicon('tvcollector.resource_successfully_updated', array(
            'id' => $resource->id
        ))
    );

    $counter++;
    usleep($usleep);
}


$modx->log(modX::LOG_LEVEL_INFO,
    $modx->lexicon('tvcollector.processed_resources_from', array(
        'counter' => $counter,
        'resources' => count($resources),
    ))
);

$modx->log(modX::LOG_LEVEL_INFO, 'COMPLETED');
