<?php
include( 'core_API.php' );
include( $g_summary_jpgraph_function );

#centers the chart
$center = 0.26;

#position of the legend
$poshorizontal = 0.03;
$posvertical = 0.09;

if ($g_customize_attributes) {
			# to be deleted when moving to manage_project_page.php	
			$t_project_id = '0000000';

			# custom attributes insertion
			insert_attributes( 'resolution', $t_project_id, 'global' );
			insert_attributes( 'resolution', $t_project_id, 'str' ) ;
}
create_bug_enum_summary_pct($s_resolution_enum_string, 'resolution');
graph_bug_enum_summary_pct($s_by_resolution_pct);
?>