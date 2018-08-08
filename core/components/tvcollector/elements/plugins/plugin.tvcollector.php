<?php
/**
 * TVCollector
 *
 * @package TVCollector
 * @author Callisto
 */
switch ( $modx->event->name ) {

  case 'OnDocFormSave':
    $template = $resource->get('template');
    $template = $modx->getObject('modTemplate', $template);

    $tvs = $template->getTemplateVarList();
    if ( $tvs !== false ) {

      $tvcollector = array();
      foreach ( $tvs['collection'] as $tv ) {
        $tvId = $tv->get('id');
        $tvName = $tv->get('name');
        $tvcollector[$tvName] = $resource->getTVValue($tvId);
      }

      $resource->setProperties($tvcollector, 'tvc');
      $resource->save();

    }
    break;


  case 'OnLoadWebDocument':
    $tvcollector = $modx->resource->get('properties');
    $modx->toPlaceholders(array(
      'tvc' => $tvcollector['tvc']
    ));
    break;

}
