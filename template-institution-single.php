<?php
/**
 * Template Name: Institution | Single
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

require_once( get_template_directory() . '/classes/EqarApi.class.php' );

$context         = Timber::get_context();
$eqarApi         = new EqarApi();
$context['post'] = new TimberPost();
$context['pages'] = array(
        'country' =>            get_field('country_page'),
        'agency' =>             get_field('agency_page'),
        'report' =>             get_field('report_page'),
        'institution' =>        get_field('institution_page') ?: $context['post']->link ,
    );

// Check if the agency is set.
if ( isset($context['request']->get['id']) ) {

    $id = strtoupper($context['request']->get['id']);

    // Get the agency ID from the GET variable (can be DEQARINST or ETER ID)
    if (preg_match('/^[0-9]+$/', $id)) {
        $institution    = $eqarApi->getInstitution( $id );
    } elseif (preg_match('/^DEQARINST([0-9]+)$/', $id, $matches)) {
        $institution    = $eqarApi->getInstitution( $matches[1] );
    } elseif (preg_match('/^[A-Z][A-Z][0-9]{4}$/', $id, $matches)) {
        $institution    = $eqarApi->getInstitutionByEterId( $matches[0] );
    } else {
        Site::do404(400, "Malformed DEQARINST or ETER ID provided.");
    }

    if ( isset($institution) && !empty($institution) && $institution != false ) {

        $context['institution'] = $institution;

        $parameters = array();
        foreach($context['search']['facets'] as &$facet) {
            // prepare facets by which reports can be filtered
            if ( !empty($facet['report_field']) ) {
                // prepare empty field array
                $facet['field'] = array();
                // re-organise value labels
                if ( is_array($facet['value_labels']) ) {
                    $newArray = array();
                    foreach($facet['value_labels'] as $label) {
                        $newArray[$label['value']] = $label['label'];
                    }
                    $facet['value_labels'] = $newArray;
                }
                // pass through to template those facets that were set
                if ( isset($context['request']->get[$facet['tag']]) ) {
                    $facet['select'] = $context['request']->get[$facet['tag']];
                }
            }
        }

        make_facet_field(array_merge($institution->reports, $institution->programmes), $context['search']['facets']);

        foreach($institution->hierarchical_relationships->includes as $rel) {
            if (isset($rel->valid_to) and $rel->valid_to != null) {
                if (empty($rel->relationship_type)) { $rel->relationship_type = '[undef]'; }
                $institution->historical_relationships[] = array(
                    'relationship_type' => ( $rel->relationship_type == 'consortium' ? 'consortium with' : 'included '.$rel->relationship_type ),
                    'institution' => $rel->institution,
                    'valid_from' => $rel->valid_from,
                    'valid_to' => $rel->valid_to,
                );
            }
        }
        foreach($institution->hierarchical_relationships->part_of as $rel) {
            if (isset($rel->valid_to) and $rel->valid_to != null) {
                if (empty($rel->relationship_type)) { $rel->relationship_type = '[undef]'; }
                $institution->historical_relationships[] = array(
                    'relationship_type' => ( $rel->relationship_type == 'consortium' ? 'consortium with' : 'was '.$rel->relationship_type.' of' ),
                    'institution' => $rel->institution,
                    'valid_from' => $rel->valid_from,
                    'valid_to' => $rel->valid_to,
                );
            }
        }

    } else {
        Site::do404();
    }

} else {
    Site::do404(400, "DEQAR institution ID or ETER ID must be provided.");
}

// Render the twig template.
Timber::render('institution-single.twig', $context);
