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
$modx->lexicon->load('tvcollector:default');
$resources = $modx->getCollection('modResource');
$counter = 0;


foreach ( $resources as $resource ) {
  $resource->setProperties(array(), 'tvc', false);
  if ( $resource->save() !== false ) {
    $modx->log(modX::LOG_LEVEL_INFO, sprintf('%s %s: OK.',
      $modx->lexicon('tvcollector.resource'),
      $resource->id
    ));
    $counter++;
  } else {
    $modx->log(modX::LOG_LEVEL_ERROR, sprintf('%s %s: %s.',
      $modx->lexicon('tvcollector.resource'),
      $resource->id,
      $modx->lexicon('tvcollector.could_not_save')
    ));
  }

}


$modx->log(modX::LOG_LEVEL_INFO, sprintf('%s %s %s %s.',
  $modx->lexicon('tvcollector.processed'),
  $counter,
  $modx->lexicon('tvcollector.resources_out_of'),
  count($resources)
));
$modx->log(modX::LOG_LEVEL_INFO, 'COMPLETED');
