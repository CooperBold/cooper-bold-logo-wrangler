<?php
/**
 * Guards WordPress.org review constraints on the plugin bootstrap.
 *
 * @package CooperBoldLogoWrangler
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class COOPBOLO_Bootstrap_Test extends TestCase {

	public function test_plugin_never_rewrites_active_plugins(): void {
		$root = dirname( __DIR__ );
		$this->assertFileDoesNotExist( $root . '/balanced-logos.php' );
		$files = array_merge( array( $root . '/cooper-bold-logo-wrangler.php' ), glob( $root . '/includes/*.php' ) );
		foreach ( $files as $file ) {
			$src = file_get_contents( $file );
			$this->assertNotFalse( $src );
			$this->assertStringNotContainsString( 'active_plugins', $src, basename( $file ) );
			$this->assertStringNotContainsString( 'upgrader_process_complete', $src, basename( $file ) );
		}
	}

	public function test_bootstrap_registers_activation_and_deactivation_hooks(): void {
		$src = file_get_contents( dirname( __DIR__ ) . '/cooper-bold-logo-wrangler.php' );
		$this->assertNotFalse( $src );
		$this->assertStringContainsString( 'register_activation_hook( COOPBOLO_FILE, \'coopbolo_activate\' )', $src );
		$this->assertStringContainsString( 'register_deactivation_hook( COOPBOLO_FILE, \'coopbolo_deactivate\' )', $src );
	}

	/**
	 * The plugin was renamed from Logo Soup / Balanced Logos. Those names (generic, or
	 * the third-party project's) must not come back as shortcodes, blocks, or markup.
	 */
	public function test_old_product_names_are_not_registered_or_emitted(): void {
		$root  = dirname( __DIR__ );
		$files = array_merge(
			array( $root . '/cooper-bold-logo-wrangler.php' ),
			glob( $root . '/includes/*.php' ),
			array( $root . '/src/view.js', $root . '/src/view.scss', $root . '/src/block/index.js', $root . '/src/block/block.json' )
		);
		$pattern = '/\[logo_soup|\[balanced_logos|\'logo_soup\'|\'balanced_logos\'|cooper-bold-logo-soup\'|cb-balanced-logos|cooper-bold\/balanced-logos|cooper-bold\/logo-soup/';
		$found   = array();
		foreach ( $files as $file ) {
			$src = file_get_contents( $file );
			$this->assertNotFalse( $src );
			if ( preg_match( $pattern, $src, $m ) ) {
				$found[ basename( $file ) ] = $m[0];
			}
		}
		$this->assertSame( array(), $found );
	}

	public function test_global_functions_and_classes_use_plugin_prefix(): void {
		$root  = dirname( __DIR__ );
		$files = array_merge(
			array( $root . '/cooper-bold-logo-wrangler.php' ),
			glob( $root . '/includes/*.php' )
		);
		$unprefixed = array();
		foreach ( $files as $file ) {
			$src = file_get_contents( $file );
			$this->assertNotFalse( $src );
			preg_match_all( '/^\s*function\s+(\w+)/m', $src, $functions );
			preg_match_all( '/^\s*(?:final\s+|abstract\s+)?(?:class|interface|trait)\s+(\w+)/m', $src, $classes );
			foreach ( $functions[1] as $name ) {
				if ( 0 !== strpos( $name, 'coopbolo_' ) ) {
					$unprefixed[] = basename( $file ) . ': ' . $name . '()';
				}
			}
			foreach ( $classes[1] as $name ) {
				if ( 0 !== strpos( $name, 'COOPBOLO_' ) ) {
					$unprefixed[] = basename( $file ) . ': class ' . $name;
				}
			}
			// WP.org reads the prefix up to the first underscore, so CB_LW_ counted as "cb".
			preg_match_all( "/define\(\s*'(\w+)'/", $src, $defines );
			foreach ( $defines[1] as $name ) {
				if ( 0 !== strpos( $name, 'COOPBOLO_' ) ) {
					$unprefixed[] = basename( $file ) . ': define ' . $name;
				}
			}
		}
		$this->assertSame( array(), $unprefixed );
	}

	public function test_deactivate_clears_schema_version_so_reactivation_migrates_again(): void {
		$src = file_get_contents( dirname( __DIR__ ) . '/cooper-bold-logo-wrangler.php' );
		$this->assertNotFalse( $src );
		$this->assertStringContainsString( 'delete_option( COOPBOLO_Collections::SCHEMA_OPTION )', $src );
	}

	/**
	 * The plugin declares PHP 7.4 support; WP.org activates it on 7.4 and fatals on PHP 8-only calls.
	 */
	public function test_plugin_code_avoids_php8_only_functions(): void {
		$root  = dirname( __DIR__ );
		$files = array_merge(
			array( $root . '/cooper-bold-logo-wrangler.php' ),
			glob( $root . '/includes/*.php' )
		);
		$found = array();
		foreach ( $files as $file ) {
			$src = file_get_contents( $file );
			$this->assertNotFalse( $src );
			if ( preg_match_all( '/(?<![\w>:$])(str_contains|str_starts_with|str_ends_with|array_is_list|get_debug_type|fdiv)\s*\(/', $src, $m ) ) {
				$found[ basename( $file ) ] = array_unique( $m[1] );
			}
		}
		$this->assertSame( array(), $found );
	}

	public function test_post_type_meta_and_option_names_use_plugin_prefix(): void {
		$this->assertStringStartsWith( 'coopbolo_', COOPBOLO_Collections::POST_TYPE );
		$this->assertLessThanOrEqual( 20, strlen( COOPBOLO_Collections::POST_TYPE ) );
		$this->assertStringStartsWith( '_coopbolo_', COOPBOLO_Collections::META_LOGOS );
		$this->assertStringStartsWith( '_coopbolo_', COOPBOLO_Collections::META_SETTINGS );
		$this->assertStringStartsWith( 'coopbolo_', COOPBOLO_Collections::SCHEMA_OPTION );
	}

	public function test_readme_points_to_public_plugin_source(): void {
		$readme = file_get_contents( dirname( __DIR__ ) . '/readme.txt' );
		$this->assertNotFalse( $readme );
		$this->assertStringContainsString( 'https://github.com/CooperBold/cooper-bold-logo-wrangler', $readme );
		$this->assertStringContainsString( 'src/view.js', $readme );
		$this->assertStringContainsString( 'src/admin/collection-preview.js', $readme );
	}
}
