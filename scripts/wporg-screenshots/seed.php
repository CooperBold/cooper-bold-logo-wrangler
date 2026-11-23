<?php
require_once ABSPATH . 'wp-admin/includes/media.php'; require_once ABSPATH . 'wp-admin/includes/file.php'; require_once ABSPATH . 'wp-admin/includes/image.php';
$dir = WP_PLUGIN_DIR . '/cooper-bold-logo-wrangler-wp-plugin/uncommitted/demo-logos/'; // written by make-demo-logos.py
$order = array( 'quillmark', 'voltan', 'orbitry', 'summit-trail', 'pebblewise', 'fernhollow', 'zentro', 'brindle-oak' );
$names = array( 'Quillmark', 'Voltan', 'Orbitry', 'Summit Trail', 'Pebblewise', 'Fernhollow', 'Zentro', 'Brindle & Oak' );
$logos = array();
foreach ( $order as $i => $slug ) {
	$tmp = wp_tempnam( $slug . '.png' ); copy( $dir . $slug . '.png', $tmp );
	$id = media_handle_sideload( array( 'name' => 'demo-' . $slug . '.png', 'tmp_name' => $tmp ), 0, $names[ $i ] );
	if ( is_wp_error( $id ) ) { echo 'ERR ', $id->get_error_message(), "\n"; continue; }
	update_post_meta( $id, '_wp_attachment_image_alt', $names[ $i ] );
	$logos[] = array( 'id' => $id, 'url' => wp_get_attachment_url( $id ), 'alt' => $names[ $i ], 'link' => '' );
}
$cid = wp_insert_post( array( 'post_type' => 'coopbolo_collection', 'post_status' => 'publish', 'post_title' => 'Our partners', 'post_name' => 'our-partners' ) );
update_post_meta( $cid, '_coopbolo_logos', wp_slash( $logos ) );
update_post_meta( $cid, '_coopbolo_settings', array( 'baseSize' => 56, 'gap' => 40, 'densityAware' => true, 'densityFactor' => 0.5, 'cropToContent' => true, 'alignBy' => 'visual-center-y', 'layout' => 'strip' ) );
$page = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Trusted by teams everywhere', 'post_content' => '<!-- wp:paragraph --><p>Mixed logo files, one balanced row.</p><!-- /wp:paragraph --><!-- wp:cooper-bold/logo-wrangler {"collectionId":' . $cid . '} /-->' ) );
echo "collection=$cid page=$page logos=", count( $logos ), "\n";
