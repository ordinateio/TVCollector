<?php
/**
 * TVCollector
 * Update data for all resources.
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
  $template = $modx->getObject('modTemplate', $resource->get('template'));
  $tvs = $template->getTemplateVarList();

  if ( $tvs !== false ) {

    $tvcollector = array();
    foreach ( $tvs['collection'] as $tv ) {
      $tvId = $tv->get('id');
      $tvName = $tv->get('name');
      $tvcollector[$tvName] = $resource->getTVValue($tvId);
    }

    $resource->setProperties($tvcollector, 'tvc', false);
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
}


$modx->log(modX::LOG_LEVEL_INFO, sprintf('%s %s %s %s.',
  $modx->lexicon('tvcollector.processed'),
  $counter,
  $modx->lexicon('tvcollector.resources_out_of'),
  count($resources)
));
$modx->log(modX::LOG_LEVEL_INFO, 'COMPLETED');
