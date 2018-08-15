<?php
/**
 * TVCollector
 * Save and output additional fields.
 *
 * @package TVCollector
 * @author Callisto
 * @source https://github.com/callisto2410/TVCollector
 *
 */
switch ( $modx->event->name ) {

  case 'OnDocFormSave':
    $template = $modx->getObject('modTemplate', $resource->get('template'));
    $tvs = $template->getTemplateVarList();

    if ( $tvs !== false ) {
      $tvcollector = array();
      foreach ( $tvs['collection'] as $tv ) {
        $tvcollector[$tv->get('id')] = $resource->getTVValue($tv->get('name'));
      }

      $resource->setProperties($tvcollector, 'tvc');
      $resource->save();
    }
    break;


  case 'OnLoadWebDocument':
    $tvcollector = $modx->resource->get('properties');
    $tvcollector = is_array($tvcollector) && array_key_exists('tvc', $tvcollector) ? $tvcollector['tvc']  : '';
    $modx->toPlaceholders(array(
      'tvc' => $tvcollector
    ));
    break;

}
