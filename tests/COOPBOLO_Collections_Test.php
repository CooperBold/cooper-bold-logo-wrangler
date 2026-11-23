<?php
/**
 * Unit tests for logo collection resolution.
 *
 * @package CooperBoldLogoWrangler
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * @covers COOPBOLO_Collections
 * @covers COOPBOLO_Renderer::resolve_attributes
 */
final class COOPBOLO_Collections_Test extends TestCase {

	/** @var COOPBOLO_Renderer */
	private $renderer;

	protected function setUp(): void {
		parent::setUp();
		$this->renderer = new COOPBOLO_Renderer();
		$GLOBALS['cb_test_posts']         = array();
		$GLOBALS['cb_test_post_meta']     = array();
		$GLOBALS['cb_test_posts_by_slug'] = array();
	}

	protected function tearDown(): void {
		unset(
			$GLOBALS['cb_test_posts'],
			$GLOBALS['cb_test_post_meta'],
			$GLOBALS['cb_test_posts_by_slug'],
			$GLOBALS['cb_test_options'],
			$GLOBALS['cb_test_get_posts_override'],
			$GLOBALS['cb_test_set_post_type_fails']
		);
		parent::tearDown();
	}

	public function test_build_attributes_from_post_returns_merged_settings(): void {
		$post = $this->make_post(
			42,
			'homepage-partners',
			array(
				COOPBOLO_Collections::META_LOGOS => array(
					array(
						'url' => 'https://example.com/logo.png',
						'alt' => 'Partner',
					),
				),
				COOPBOLO_Collections::META_SETTINGS => array(
					'baseSize' => 40,
					'gap'      => 32,
				),
			)
		);

		$attrs = COOPBOLO_Collections::build_attributes_from_post( $post );

		$this->assertNotNull( $attrs );
		$this->assertSame( 42, $attrs['collectionId'] );
		$this->assertCount( 1, $attrs['logos'] );
		$this->assertSame( 40, $attrs['baseSize'] );
		$this->assertSame( 32, $attrs['gap'] );
	}

	public function test_get_attributes_by_id_and_slug(): void {
		$post = $this->make_post(
			7,
			'enterprise-logos',
			array(
				COOPBOLO_Collections::META_LOGOS => array(
					array( 'url' => 'https://example.com/a.svg', 'alt' => 'A' ),
				),
			)
		);
		$GLOBALS['cb_test_posts'][7] = $post;

		$by_id = COOPBOLO_Collections::get_attributes( 7 );
		$this->assertNotNull( $by_id );
		$this->assertSame( 'A', $by_id['logos'][0]['alt'] );

		$GLOBALS['cb_test_posts_by_slug']['enterprise-logos'] = $post;
		$by_slug = COOPBOLO_Collections::get_attributes( 'enterprise-logos' );
		$this->assertNotNull( $by_slug );
		$this->assertSame( 'https://example.com/a.svg', $by_slug['logos'][0]['url'] );
	}

	public function test_get_attributes_returns_null_for_missing_collection(): void {
		$this->assertNull( COOPBOLO_Collections::get_attributes( 999 ) );
		$this->assertNull( COOPBOLO_Collections::get_attributes( 'missing-slug' ) );
	}

	public function test_resolve_attributes_uses_collection_logos_and_overrides_gap(): void {
		$post = $this->make_post(
			3,
			'partners',
			array(
				COOPBOLO_Collections::META_LOGOS => array(
					array( 'url' => 'https://example.com/logo.png', 'alt' => 'Logo' ),
				),
				COOPBOLO_Collections::META_SETTINGS => array(
					'gap' => 28,
				),
			)
		);
		$GLOBALS['cb_test_posts'][3] = $post;

		$resolved = $this->renderer->resolve_attributes(
			array(
				'collectionId' => 3,
				'gap'          => 48,
				'logos'        => array(
					array( 'url' => 'https://ignored.test/x.png', 'alt' => 'Ignored' ),
				),
			)
		);

		$this->assertSame( 'https://example.com/logo.png', $resolved['logos'][0]['url'] );
		$this->assertSame( 48, $resolved['gap'] );
	}

	public function test_resolve_attributes_by_slug(): void {
		$post = $this->make_post(
			5,
			'homepage-partners',
			array(
				COOPBOLO_Collections::META_LOGOS => array(
					array( 'url' => 'https://example.com/p.png', 'alt' => 'P' ),
				),
			)
		);
		$GLOBALS['cb_test_posts_by_slug']['homepage-partners'] = $post;

		$resolved = $this->renderer->resolve_attributes(
			array( 'collection' => 'homepage-partners' )
		);

		$this->assertCount( 1, $resolved['logos'] );
		$this->assertSame( 'P', $resolved['logos'][0]['alt'] );
	}

	public function test_get_shortcode_snippet_prefers_slug(): void {
		$post = $this->make_post( 11, 'homepage-partners', array() );
		$this->assertSame(
			'[logo_wrangler collection="homepage-partners"]',
			COOPBOLO_Collections::get_shortcode_snippet( $post )
		);
	}

	public function test_reads_1_3_x_meta_keys_until_saved(): void {
		$post = $this->make_post(
			15,
			'legacy-partners',
			array(
				'_cb_lw_logos' => array(
					array( 'url' => 'https://example.com/legacy.svg', 'alt' => 'Legacy' ),
				),
				'_cb_lw_settings' => array(
					'gap' => 36,
				),
			)
		);

		$attrs = COOPBOLO_Collections::build_attributes_from_post( $post );

		$this->assertNotNull( $attrs );
		$this->assertSame( 'Legacy', $attrs['logos'][0]['alt'] );
		$this->assertSame( 36, $attrs['gap'] );
	}

	public function test_empty_primary_logos_does_not_resurrect_legacy(): void {
		$post = $this->make_post(
			16,
			'cleared-partners',
			array(
				COOPBOLO_Collections::META_LOGOS => array(),
				'_cb_lw_logos'    => array(
					array( 'url' => 'https://example.com/old.svg', 'alt' => 'Old' ),
				),
			)
		);

		$logos = COOPBOLO_Collections::get_logos_for_post( (int) $post->ID );

		$this->assertSame( array(), $logos );
	}

	public function test_sanitize_settings_clamps_values(): void {
		$settings = COOPBOLO_Collections::sanitize_settings(
			array(
				'baseSize'    => 999,
				'scaleFactor' => -1,
				'gap'         => 200,
			)
		);

		$this->assertSame( 256, $settings['baseSize'] );
		$this->assertEquals( 0.0, $settings['scaleFactor'] );
		$this->assertSame( 96, $settings['gap'] );
	}

	public function test_preview_meta_box_outputs_mount_root(): void {
		$reflection = new ReflectionClass( COOPBOLO_Collections::class );
		$method     = $reflection->getMethod( 'render_preview_meta_box' );
		$instance   = $reflection->newInstanceWithoutConstructor();
		$post       = $this->make_post( 1, 'preview-test', array() );

		ob_start();
		$method->invoke( $instance, $post );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'id="cb-lw-preview-root"', $html );
		$this->assertStringContainsString( 'cb-lw-preview-wrap', $html );
	}

	public function test_migrate_moves_legacy_post_type_and_meta(): void {
		$logos = array(
			array(
				'url' => 'https://example.com/logo.png',
				'alt' => 'Smith \\ Sons',
			),
		);
		$this->register_post( 7, COOPBOLO_Collections::LEGACY_POST_TYPE );
		$this->register_post( 8, 'page' );
		$GLOBALS['cb_test_post_meta'][7] = array(
			'_cb_lw_logos'    => $logos,
			'_cb_lw_settings' => array( 'gap' => 32 ),
		);

		$result = COOPBOLO_Collections::migrate_legacy_data();

		$this->assertSame( array( 'moved' => 1, 'failed' => 0 ), $result );
		$this->assertSame( COOPBOLO_Collections::POST_TYPE, $GLOBALS['cb_test_posts'][7]->post_type );
		$this->assertSame( 'page', $GLOBALS['cb_test_posts'][8]->post_type );
		$this->assertSame( $logos, $GLOBALS['cb_test_post_meta'][7][ COOPBOLO_Collections::META_LOGOS ] );
		$this->assertSame( array( 'gap' => 32 ), $GLOBALS['cb_test_post_meta'][7][ COOPBOLO_Collections::META_SETTINGS ] );
		// Old keys stay so a rollback to 1.3.4 only has to change the post type back.
		$this->assertSame( $logos, $GLOBALS['cb_test_post_meta'][7]['_cb_lw_logos'] );
		$this->assertSame( array( 'gap' => 32 ), $GLOBALS['cb_test_post_meta'][7]['_cb_lw_settings'] );
	}

	public function test_migrate_keeps_existing_new_meta(): void {
		$old = array( array( 'url' => 'https://example.com/old.png' ) );
		$this->register_post( 7, COOPBOLO_Collections::LEGACY_POST_TYPE );
		$GLOBALS['cb_test_post_meta'][7] = array(
			'_cb_lw_logos'                    => $old,
			COOPBOLO_Collections::META_LOGOS => array(),
		);

		COOPBOLO_Collections::migrate_legacy_data();

		$this->assertSame( array(), $GLOBALS['cb_test_post_meta'][7][ COOPBOLO_Collections::META_LOGOS ] );
		$this->assertSame( $old, $GLOBALS['cb_test_post_meta'][7]['_cb_lw_logos'] );
	}

	public function test_migrate_ignores_posts_a_query_filter_slipped_in(): void {
		$this->register_post( 7, COOPBOLO_Collections::LEGACY_POST_TYPE );
		$this->register_post( 8, 'post' );
		$GLOBALS['cb_test_get_posts_override'] = array( 7, 8 );

		$result = COOPBOLO_Collections::migrate_legacy_data();

		$this->assertSame( array( 'moved' => 1, 'failed' => 0 ), $result );
		$this->assertSame( 'post', $GLOBALS['cb_test_posts'][8]->post_type );
	}

	public function test_migrate_skips_meta_that_is_not_an_array(): void {
		$this->register_post( 7, COOPBOLO_Collections::LEGACY_POST_TYPE );
		$GLOBALS['cb_test_post_meta'][7] = array( '_cb_lw_logos' => false );

		COOPBOLO_Collections::migrate_legacy_data();

		$this->assertArrayNotHasKey( COOPBOLO_Collections::META_LOGOS, $GLOBALS['cb_test_post_meta'][7] );
		$this->assertArrayHasKey( '_cb_lw_logos', $GLOBALS['cb_test_post_meta'][7] );
	}

	public function test_failed_move_leaves_schema_version_unset_for_retry(): void {
		$instance = ( new ReflectionClass( COOPBOLO_Collections::class ) )->newInstanceWithoutConstructor();
		$this->register_post( 7, COOPBOLO_Collections::LEGACY_POST_TYPE );
		$GLOBALS['cb_test_set_post_type_fails'] = true;

		$instance->maybe_migrate_legacy_data();

		$this->assertArrayNotHasKey( COOPBOLO_Collections::SCHEMA_OPTION, $GLOBALS['cb_test_options'] ?? array() );
	}

	public function test_maybe_migrate_runs_once_per_schema_version(): void {
		$instance = ( new ReflectionClass( COOPBOLO_Collections::class ) )->newInstanceWithoutConstructor();
		$this->register_post( 7, COOPBOLO_Collections::LEGACY_POST_TYPE );

		$instance->maybe_migrate_legacy_data();

		$this->assertSame( COOPBOLO_Collections::POST_TYPE, $GLOBALS['cb_test_posts'][7]->post_type );
		$this->assertSame( COOPBOLO_Collections::SCHEMA_VERSION, $GLOBALS['cb_test_options'][ COOPBOLO_Collections::SCHEMA_OPTION ] );

		$this->register_post( 9, COOPBOLO_Collections::LEGACY_POST_TYPE );
		$instance->maybe_migrate_legacy_data();

		$this->assertSame( COOPBOLO_Collections::LEGACY_POST_TYPE, $GLOBALS['cb_test_posts'][9]->post_type );
	}

	public function test_migrate_copies_1_2_x_meta_when_no_newer_keys(): void {
		$logos = array( array( 'url' => 'https://example.com/soup.png', 'alt' => 'Soup era' ) );
		$this->register_post( 7, COOPBOLO_Collections::LEGACY_POST_TYPE );
		$GLOBALS['cb_test_post_meta'][7] = array(
			'_cb_logo_soup_logos'    => $logos,
			'_cb_logo_soup_settings' => array( 'gap' => 24 ),
		);

		$result = COOPBOLO_Collections::migrate_legacy_data();

		$this->assertSame( array( 'moved' => 1, 'failed' => 0 ), $result );
		$this->assertSame( $logos, $GLOBALS['cb_test_post_meta'][7][ COOPBOLO_Collections::META_LOGOS ] );
		$this->assertSame( array( 'gap' => 24 ), $GLOBALS['cb_test_post_meta'][7][ COOPBOLO_Collections::META_SETTINGS ] );
	}

	public function test_migrate_prefers_1_3_x_meta_over_1_2_x(): void {
		$newer = array( array( 'url' => 'https://example.com/lw.png' ) );
		$this->register_post( 7, COOPBOLO_Collections::LEGACY_POST_TYPE );
		$GLOBALS['cb_test_post_meta'][7] = array(
			'_cb_lw_logos'        => $newer,
			'_cb_logo_soup_logos' => array( array( 'url' => 'https://example.com/soup.png' ) ),
		);

		COOPBOLO_Collections::migrate_legacy_data();

		$this->assertSame( $newer, $GLOBALS['cb_test_post_meta'][7][ COOPBOLO_Collections::META_LOGOS ] );
	}

	public function test_1_2_x_meta_renders_after_post_type_already_moved(): void {
		// A site that went straight from 1.2.x to 1.4.0-1.5.1: post type moved, meta never copied.
		$post = $this->make_post(
			17,
			'soup-partners',
			array(
				'_cb_logo_soup_logos'    => array(
					array( 'url' => 'https://example.com/a.svg', 'alt' => 'A' ),
					array( 'url' => 'https://example.com/b.svg', 'alt' => 'B' ),
				),
				'_cb_logo_soup_settings' => array( 'gap' => 40 ),
			)
		);

		$attrs = COOPBOLO_Collections::build_attributes_from_post( $post );

		$this->assertNotNull( $attrs );
		$this->assertCount( 2, $attrs['logos'] );
		$this->assertSame( 40, $attrs['gap'] );
	}

	public function test_migrate_skips_non_array_1_3_x_meta_for_1_2_x(): void {
		$logos = array( array( 'url' => 'https://example.com/soup.png' ) );
		$this->register_post( 7, COOPBOLO_Collections::LEGACY_POST_TYPE );
		$GLOBALS['cb_test_post_meta'][7] = array(
			'_cb_lw_logos'        => false,
			'_cb_logo_soup_logos' => $logos,
		);

		COOPBOLO_Collections::migrate_legacy_data();

		$this->assertSame( $logos, $GLOBALS['cb_test_post_meta'][7][ COOPBOLO_Collections::META_LOGOS ] );
	}

	public function test_save_drops_1_2_x_meta_so_an_empty_save_stays_empty(): void {
		$post = $this->make_post(
			18,
			'saved-partners',
			array(
				'_cb_logo_soup_logos'    => array( array( 'url' => 'https://example.com/old.svg' ) ),
				'_cb_logo_soup_settings' => array( 'gap' => 40 ),
			)
		);
		$instance = ( new ReflectionClass( COOPBOLO_Collections::class ) )->newInstanceWithoutConstructor();
		$_POST    = array( 'coopbolo_collection_nonce' => 'valid' );

		try {
			$instance->save_post( 18, $post );
		} finally {
			$_POST = array();
		}

		$this->assertArrayNotHasKey( '_cb_logo_soup_logos', $GLOBALS['cb_test_post_meta'][18] );
		$this->assertArrayNotHasKey( '_cb_logo_soup_settings', $GLOBALS['cb_test_post_meta'][18] );
		$this->assertSame( array(), COOPBOLO_Collections::get_logos_for_post( 18 ) );
	}

	public function test_unmigrated_cb_lw_meta_still_renders(): void {
		$GLOBALS['cb_test_post_meta'][5] = array(
			'_cb_lw_logos' => array( array( 'url' => 'https://example.com/logo.png' ) ),
		);

		$this->assertCount( 1, COOPBOLO_Collections::get_logos_for_post( 5 ) );
	}

	/**
	 * @param int    $id        Post ID.
	 * @param string $post_type Post type.
	 */
	private function register_post( int $id, string $post_type ): void {
		$GLOBALS['cb_test_posts'][ $id ] = new WP_Post(
			(object) array(
				'ID'          => $id,
				'post_type'   => $post_type,
				'post_status' => 'publish',
				'post_name'   => 'post-' . $id,
			)
		);
	}

	/**
	 * @param int                  $id Post ID.
	 * @param string               $slug Post slug.
	 * @param array<string, mixed> $meta Meta values.
	 */
	private function make_post( int $id, string $slug, array $meta ): WP_Post {
		$post = new WP_Post(
			(object) array(
				'ID'          => $id,
				'post_type'   => COOPBOLO_Collections::POST_TYPE,
				'post_status' => 'publish',
				'post_title'  => 'Test Collection',
				'post_name'   => $slug,
			)
		);

		foreach ( $meta as $key => $value ) {
			$GLOBALS['cb_test_post_meta'][ $id ][ $key ] = $value;
		}

		return $post;
	}
}
