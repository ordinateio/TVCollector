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
        $tvId = $tv->get('id');
        $tvName = $tv->get('name');
        $tvcollector[$tvName] = $resource->getTVValue($tvId);
      }

      $resource->setProperties($tvcollector, 'tvc');
      $resource->save();

    }
    break;


  case 'OnLoadWebDocument':
    $properties = $modx->resource->get('properties');
    $modx->toPlaceholders(array(
      'tvc' => $properties['tvc']
    ));
    break;

}
