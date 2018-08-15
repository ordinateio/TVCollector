<?php
/**
 * TVCollector
 * Clear data from all resources.
 *
 * @package TVCollector
 * @author Callisto
 * @source https://github.com/callisto2410/TVCollector
 *
 */
$resources = $modx->getCollection('modResource');
$modx->lexicon->load('tvcollector:default');
$counter = 0;

$modx->log(modX::LOG_LEVEL_INFO, $modx->lexicon('tvcollector.data_cleaning'));
set_time_limit(0);

foreach ( $resources as $resource ) {

  $resource->setProperties(array(), 'tvc', false);
  if ( $resource->save() !== false ) {
    $modx->log(modX::LOG_LEVEL_INFO,
      $modx->lexicon('tvcollector.resource_successfully_updated', array(
        'id' => $resource->id
      ))
    );
    $counter++;
  } else {
    $modx->log(modX::LOG_LEVEL_WARN,
      $modx->lexicon('tvcollector.resource_could_not_be_saved', array(
        'id' => $resource->id
      ))
    );
  }
  sleep(1);

}

sleep(1);
$modx->log(modX::LOG_LEVEL_INFO,
  $modx->lexicon('tvcollector.processed_resources_from', array(
    'counter'   => $counter,
    'resources' => count($resources),
  ))
);
sleep(1);
$modx->log(modX::LOG_LEVEL_INFO, 'COMPLETED');
