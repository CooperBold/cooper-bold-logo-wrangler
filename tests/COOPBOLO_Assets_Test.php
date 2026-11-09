<?php
/**
 * Unit tests for COOPBOLO_Assets conditional Splide enqueue.
 *
 * @package CooperBoldLogoWrangler
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * @covers COOPBOLO_Assets
 */
final class COOPBOLO_Assets_Test extends TestCase {

	protected function tearDown(): void {
		parent::tearDown();
		unset( $GLOBALS['cb_test_wp_scripts'], $GLOBALS['cb_test_dequeued'] );
		$ref  = new ReflectionClass( COOPBOLO_Assets::class );
		$prop = $ref->getProperty( 'needs_standalone_splide' );
		$prop->setValue( null, false );
	}

	public function test_content_detects_carousel_shortcode(): void {
		$content = '[logo_wrangler layout="carousel" logos="https://example.com/a.svg|A"]';
		$this->assertTrue( COOPBOLO_Assets::content_needs_standalone_splide( $content ) );
	}

	public function test_content_skips_carousel_slides_wrapper(): void {
		$content = '[logo_wrangler layout="carousel" wrapper="slides" logos="https://example.com/a.svg|A"]';
		$this->assertFalse( COOPBOLO_Assets::content_needs_standalone_splide( $content ) );
	}

	public function test_content_skips_strip_layout(): void {
		$content = '[logo_wrangler layout="strip" logos="https://example.com/a.svg|A"]';
		$this->assertFalse( COOPBOLO_Assets::content_needs_standalone_splide( $content ) );
	}

	public function test_enqueue_frontend_sets_standalone_flag(): void {
		$this->assertFalse( COOPBOLO_Assets::needs_standalone_splide() );
		COOPBOLO_Assets::enqueue_frontend( true );
		$this->assertTrue( COOPBOLO_Assets::needs_standalone_splide() );
	}

	public function test_enqueue_frontend_without_carousel_keeps_flag_false(): void {
		COOPBOLO_Assets::enqueue_frontend( false );
		$this->assertFalse( COOPBOLO_Assets::needs_standalone_splide() );
	}

	public function test_raw_content_detects_shortcode_in_bricks_meta(): void {
		$bricks_json = '{"name":"shortcode","settings":{"shortcode":"[logo_wrangler collection=\\"test-slide\\" layout=\\"carousel\\"]"}}';
		$this->assertTrue( COOPBOLO_Assets::content_needs_standalone_splide( $bricks_json ) );
	}

	public function test_content_detects_collection_carousel_shortcode(): void {
		$content = '[logo_wrangler collection="test-slide" layout="carousel"]';
		$this->assertTrue( COOPBOLO_Assets::content_needs_standalone_splide( $content ) );
	}

	private function register_script( string $handle, string $src ): void {
		wp_scripts()->registered[ $handle ] = new _WP_Dependency( $handle, $src );
	}

	private function external_autoscroll_handle(): string {
		$method = new ReflectionMethod( COOPBOLO_Assets::class, 'external_autoscroll_handle' );
		$method->setAccessible( true );
		return (string) $method->invoke( null );
	}

	public function test_own_autoscroll_handle_is_not_an_external_copy(): void {
		$this->register_script( COOPBOLO_Assets::SPLIDE_AUTOSCROLL_SCRIPT_HANDLE, 'https://example.com/wp-content/plugins/cooper-bold-logo-wrangler/lib/splide/js/splide-extension-auto-scroll.min.js' );

		$this->assertSame( '', $this->external_autoscroll_handle() );
	}

	public function test_theme_autoscroll_under_another_handle_is_found(): void {
		$this->register_script( COOPBOLO_Assets::SPLIDE_AUTOSCROLL_SCRIPT_HANDLE, 'https://example.com/wp-content/plugins/cooper-bold-logo-wrangler/lib/splide/js/splide-extension-auto-scroll.min.js' );
		$this->register_script( 'theme-autoscroll', 'https://cdn.example.com/splide-extension-auto-scroll.min.js' );

		$this->assertSame( 'theme-autoscroll', $this->external_autoscroll_handle() );
	}

	public function test_registered_core_autoscroll_handle_wins_when_it_has_a_src(): void {
		$this->register_script( 'theme-autoscroll', 'https://cdn.example.com/splide-extension-auto-scroll.min.js' );
		$this->register_script( 'splide-extension-auto-scroll', 'https://cdn.example.com/other.js' );

		$this->assertSame( 'splide-extension-auto-scroll', $this->external_autoscroll_handle() );
	}

	public function test_view_script_depends_on_theme_autoscroll_not_a_second_copy(): void {
		$this->register_script( COOPBOLO_Assets::SPLIDE_SCRIPT_HANDLE, 'https://example.com/splide.min.js' );
		$this->register_script( COOPBOLO_Assets::SPLIDE_AUTOSCROLL_SCRIPT_HANDLE, 'https://example.com/lib/splide/js/splide-extension-auto-scroll.min.js' );
		$this->register_script( 'theme-autoscroll', 'https://cdn.example.com/splide-extension-auto-scroll.min.js' );
		$this->register_script( COOPBOLO_Assets::VIEW_SCRIPT_HANDLE, 'https://example.com/build/view.js' );

		$method = new ReflectionMethod( COOPBOLO_Assets::class, 'ensure_view_script_depends_on_splide' );
		$method->setAccessible( true );
		$method->invoke( null );

		$deps = wp_scripts()->registered[ COOPBOLO_Assets::VIEW_SCRIPT_HANDLE ]->deps;
		$this->assertContains( 'theme-autoscroll', $deps );
		$this->assertNotContains( COOPBOLO_Assets::SPLIDE_AUTOSCROLL_SCRIPT_HANDLE, $deps );
	}

	public function test_view_script_depends_on_own_autoscroll_when_no_theme_copy(): void {
		$this->register_script( COOPBOLO_Assets::SPLIDE_SCRIPT_HANDLE, 'https://example.com/splide.min.js' );
		$this->register_script( COOPBOLO_Assets::SPLIDE_AUTOSCROLL_SCRIPT_HANDLE, 'https://example.com/lib/splide/js/splide-extension-auto-scroll.min.js' );
		$this->register_script( COOPBOLO_Assets::VIEW_SCRIPT_HANDLE, 'https://example.com/build/view.js' );

		$method = new ReflectionMethod( COOPBOLO_Assets::class, 'ensure_view_script_depends_on_splide' );
		$method->setAccessible( true );
		$method->invoke( null );

		$this->assertContains( COOPBOLO_Assets::SPLIDE_AUTOSCROLL_SCRIPT_HANDLE, wp_scripts()->registered[ COOPBOLO_Assets::VIEW_SCRIPT_HANDLE ]->deps );
	}

	public function test_footer_reconcile_drops_bundled_autoscroll_when_theme_enqueued_its_own_late(): void {
		$this->register_script( COOPBOLO_Assets::SPLIDE_AUTOSCROLL_SCRIPT_HANDLE, 'https://example.com/lib/splide/js/splide-extension-auto-scroll.min.js' );
		$this->register_script( 'theme-autoscroll', 'https://cdn.example.com/splide-extension-auto-scroll.min.js' );
		$this->register_script( COOPBOLO_Assets::VIEW_SCRIPT_HANDLE, 'https://example.com/build/view.js' );
		wp_scripts()->registered[ COOPBOLO_Assets::VIEW_SCRIPT_HANDLE ]->deps = array( 'wp-element', COOPBOLO_Assets::SPLIDE_AUTOSCROLL_SCRIPT_HANDLE );

		( new COOPBOLO_Assets() )->reconcile_autoscroll();

		$this->assertSame( array( COOPBOLO_Assets::SPLIDE_AUTOSCROLL_SCRIPT_HANDLE ), $GLOBALS['cb_test_dequeued'] );
		$this->assertSame( array( 'wp-element', 'theme-autoscroll' ), wp_scripts()->registered[ COOPBOLO_Assets::VIEW_SCRIPT_HANDLE ]->deps );
	}

	public function test_footer_reconcile_leaves_bundled_autoscroll_alone_without_a_theme_copy(): void {
		$this->register_script( COOPBOLO_Assets::SPLIDE_AUTOSCROLL_SCRIPT_HANDLE, 'https://example.com/lib/splide/js/splide-extension-auto-scroll.min.js' );
		$this->register_script( COOPBOLO_Assets::VIEW_SCRIPT_HANDLE, 'https://example.com/build/view.js' );
		wp_scripts()->registered[ COOPBOLO_Assets::VIEW_SCRIPT_HANDLE ]->deps = array( COOPBOLO_Assets::SPLIDE_AUTOSCROLL_SCRIPT_HANDLE );

		( new COOPBOLO_Assets() )->reconcile_autoscroll();

		$this->assertArrayNotHasKey( 'cb_test_dequeued', $GLOBALS );
		$this->assertSame( array( COOPBOLO_Assets::SPLIDE_AUTOSCROLL_SCRIPT_HANDLE ), wp_scripts()->registered[ COOPBOLO_Assets::VIEW_SCRIPT_HANDLE ]->deps );
	}

	/**
	 * WordPress loads every handle the built view script declares. A React handle here
	 * puts ~57 KB of React on each page that shows a logo strip.
	 */
	public function test_frontend_view_script_does_not_depend_on_react(): void {
		$asset_file = dirname( __DIR__ ) . '/build/view.asset.php';
		$this->assertFileExists( $asset_file );
		$asset = include $asset_file;
		$this->assertSame(
			array(),
			array_values( array_intersect( $asset['dependencies'], array( 'react', 'react-dom', 'react-jsx-runtime', 'wp-element' ) ) )
		);
	}
}
