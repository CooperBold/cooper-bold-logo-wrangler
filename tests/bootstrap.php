<?php
/**
 * Minimal WordPress stubs for COOPBOLO_Renderer unit tests.
 *
 * @package CooperBoldLogoWrangler
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

if ( ! defined( 'COOPBOLO_PATH' ) ) {
	define( 'COOPBOLO_PATH', dirname( __DIR__ ) . '/' );
}

if ( ! defined( 'COOPBOLO_URL' ) ) {
	define( 'COOPBOLO_URL', 'https://example.com/wp-content/plugins/cooper-bold-logo-wrangler/' );
}

if ( ! function_exists( 'get_current_screen' ) ) {
	/**
	 * @return object|null
	 */
	function get_current_screen() {
		return $GLOBALS['cb_test_current_screen'] ?? null;
	}
}

if ( ! function_exists( 'esc_url' ) ) {
	/**
	 * @param string $url Raw URL.
	 */
	function esc_url( $url ): string {
		return (string) $url;
	}
}

if ( ! function_exists( 'esc_attr' ) ) {
	/**
	 * @param string $text Text.
	 */
	function esc_attr( $text ): string {
		return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'esc_attr__' ) ) {
	/**
	 * @param string $text Text.
	 */
	function esc_attr__( $text, $domain = 'default' ): string {
		return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'esc_html__' ) ) {
	/**
	 * @param string $text Text.
	 */
	function esc_html__( $text, $domain = 'default' ): string {
		return htmlspecialchars( (string) $text, ENT_NOQUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'add_action' ) ) {
	/**
	 * @param string   $hook Hook name.
	 * @param callable $callback Callback.
	 */
	function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ): void {
		unset( $hook, $callback, $priority, $accepted_args );
	}
}

if ( ! function_exists( 'add_filter' ) ) {
	/**
	 * @param string   $hook Hook name.
	 * @param callable $callback Callback.
	 */
	function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ): void {
		unset( $hook, $callback, $priority, $accepted_args );
	}
}

if ( ! function_exists( 'esc_url_raw' ) ) {
	/**
	 * @param string $url Raw URL.
	 */
	function esc_url_raw( $url ): string {
		$url = trim( (string) $url );
		if ( '' === $url || preg_match( '#^\s*javascript:#i', $url ) ) {
			return '';
		}
		if ( preg_match( '#^(https?://|/)#i', $url ) ) {
			return $url;
		}
		return '';
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	/**
	 * @param string $str Raw string.
	 */
	function sanitize_text_field( $str ): string {
		return trim( wp_strip_all_tags( (string) $str ) );
	}
}

if ( ! function_exists( 'wp_strip_all_tags' ) ) {
	/**
	 * @param string $string Raw string.
	 */
	function wp_strip_all_tags( $string ): string {
		return strip_tags( (string) $string );
	}
}

if ( ! function_exists( 'sanitize_hex_color' ) ) {
	/**
	 * @param string $color Raw color.
	 */
	function sanitize_hex_color( $color ) {
		if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', (string) $color ) ) {
			return $color;
		}
		return '';
	}
}

if ( ! function_exists( 'wp_parse_args' ) ) {
	/**
	 * @param array<string, mixed> $args     Args.
	 * @param array<string, mixed> $defaults Defaults.
	 * @return array<string, mixed>
	 */
	function wp_parse_args( $args, $defaults = array() ): array {
		if ( is_object( $args ) ) {
			$parsed = get_object_vars( $args );
		} else {
			$parsed = (array) $args;
		}
		return array_merge( $defaults, $parsed );
	}
}

if ( ! function_exists( 'wp_parse_url' ) ) {
	/**
	 * @param string $url        URL.
	 * @param int    $component  Component.
	 * @return mixed
	 */
	function wp_parse_url( $url, $component = -1 ) {
		return parse_url( (string) $url, $component );
	}
}

if ( ! function_exists( '__' ) ) {
	/**
	 * @param string $text Text.
	 */
	function __( $text, $domain = 'default' ): string {
		return (string) $text;
	}
}

if ( ! function_exists( 'esc_html_e' ) ) {
	/**
	 * @param string $text Text.
	 */
	function esc_html_e( $text, $domain = 'default' ): void {
		echo esc_html( (string) $text );
	}
}

if ( ! function_exists( 'esc_html' ) ) {
	/**
	 * @param string $text Text.
	 */
	function esc_html( $text ): string {
		return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'absint' ) ) {
	/**
	 * @param mixed $maybeint Value.
	 */
	function absint( $maybeint ): int {
		return abs( (int) $maybeint );
	}
}

if ( ! class_exists( 'WP_Post' ) ) {
	/**
	 * Minimal WP_Post stub for unit tests.
	 */
	class WP_Post {
		/** @var object */
		public $filter;

		/**
		 * @param object $data Post data.
		 */
		public function __construct( $data ) {
			foreach ( get_object_vars( $data ) as $key => $value ) {
				$this->$key = $value;
			}
		}
	}
}

if ( ! function_exists( 'get_post' ) ) {
	/**
	 * @param int $post_id Post ID.
	 */
	function get_post( $post_id ) {
		$id = (int) $post_id;
		return $GLOBALS['cb_test_posts'][ $id ] ?? null;
	}
}

if ( ! function_exists( 'get_post_meta' ) ) {
	/**
	 * @param int    $post_id Post ID.
	 * @param string $key Meta key.
	 * @param bool   $single Single value.
	 * @return mixed
	 */
	function get_post_meta( $post_id, $key, $single = false ) {
		$id = (int) $post_id;
		if ( ! isset( $GLOBALS['cb_test_post_meta'][ $id ][ $key ] ) ) {
			return $single ? '' : array();
		}
		$value = $GLOBALS['cb_test_post_meta'][ $id ][ $key ];
		return $single ? $value : array( $value );
	}
}

if ( ! function_exists( 'metadata_exists' ) ) {
	/**
	 * @param string $meta_type Type of object metadata.
	 * @param int    $object_id Object ID.
	 * @param string $meta_key  Meta key.
	 */
	function metadata_exists( $meta_type, $object_id, $meta_key ): bool {
		unset( $meta_type );
		$id = (int) $object_id;
		return isset( $GLOBALS['cb_test_post_meta'][ $id ][ $meta_key ] );
	}
}

if ( ! function_exists( 'delete_post_meta' ) ) {
	/**
	 * @param int    $post_id Post ID.
	 * @param string $key     Meta key.
	 */
	function delete_post_meta( $post_id, $key, $value = '' ): bool {
		unset( $value );
		$id = (int) $post_id;
		unset( $GLOBALS['cb_test_post_meta'][ $id ][ $key ] );
		return true;
	}
}

if ( ! function_exists( 'wp_verify_nonce' ) ) {
	/**
	 * @param string $nonce  Nonce.
	 * @param string $action Action.
	 */
	function wp_verify_nonce( $nonce, $action = '' ): bool {
		return 'valid' === $nonce && '' !== $action;
	}
}

if ( ! function_exists( 'current_user_can' ) ) {
	/**
	 * @param string $capability Capability.
	 */
	function current_user_can( $capability, ...$args ): bool {
		unset( $capability, $args );
		return true;
	}
}

if ( ! function_exists( 'get_posts' ) ) {
	/**
	 * @param array<string, mixed> $args Query args.
	 * @return array<int, WP_Post>
	 */
	function get_posts( $args = array() ): array {
		if ( isset( $args['name'] ) ) {
			$slug = (string) $args['name'];
			if ( isset( $GLOBALS['cb_test_posts_by_slug'][ $slug ] ) ) {
				return array( $GLOBALS['cb_test_posts_by_slug'][ $slug ] );
			}
			return array();
		}
		// Simulates a pre_get_posts filter rewriting the query.
		if ( isset( $GLOBALS['cb_test_get_posts_override'] ) ) {
			return $GLOBALS['cb_test_get_posts_override'];
		}
		$posts = array_values( $GLOBALS['cb_test_posts'] ?? array() );
		if ( isset( $args['post_type'] ) ) {
			$posts = array_values(
				array_filter(
					$posts,
					static function ( $post ) use ( $args ) {
						return ! isset( $post->post_type ) || $post->post_type === $args['post_type'];
					}
				)
			);
		}
		if ( 'ids' === ( $args['fields'] ?? '' ) ) {
			return array_map(
				static function ( $post ) {
					return (int) $post->ID;
				},
				$posts
			);
		}
		return $posts;
	}
}

if ( ! function_exists( 'sanitize_title' ) ) {
	/**
	 * @param string $title Raw title.
	 */
	function sanitize_title( $title ): string {
		$title = strtolower( trim( (string) $title ) );
		return preg_replace( '/[^a-z0-9-]+/', '-', $title ) ?? '';
	}
}

if ( ! function_exists( 'sanitize_key' ) ) {
	/**
	 * @param string $key Raw key.
	 */
	function sanitize_key( $key ): string {
		return strtolower( preg_replace( '/[^a-z0-9_-]/', '', (string) $key ) ?? '' );
	}
}

if ( ! function_exists( 'absint' ) ) {
	/**
	 * @param mixed $value Value.
	 */
	function absint( $value ): int {
		return abs( (int) $value );
	}
}

if ( ! function_exists( 'sanitize_html_class' ) ) {
	/**
	 * @param string $class CSS class name.
	 */
	function sanitize_html_class( $class, $fallback = '' ): string {
		$class = preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $class ) ?? '';
		return '' !== $class ? $class : (string) $fallback;
	}
}

if ( ! function_exists( 'wp_json_encode' ) ) {
	/**
	 * @param mixed $data Data to encode.
	 * @return string|false
	 */
	function wp_json_encode( $data, $options = 0, $depth = 512 ) {
		return json_encode( $data, $options, $depth );
	}
}

if ( ! function_exists( 'is_admin' ) ) {
	/**
	 * @return bool
	 */
	function is_admin(): bool {
		return false;
	}
}

if ( ! function_exists( 'wp_script_is' ) ) {
	/**
	 * @param string $handle Script handle.
	 */
	function wp_script_is( $handle, $status = 'enqueued' ): bool {
		if ( 'registered' === $status ) {
			return isset( wp_scripts()->registered[ $handle ] );
		}
		return false;
	}
}

if ( ! function_exists( 'wp_style_is' ) ) {
	/**
	 * @param string $handle Style handle.
	 */
	function wp_style_is( $handle, $status = 'enqueued' ): bool {
		unset( $handle, $status );
		return false;
	}
}

if ( ! function_exists( 'wp_enqueue_script' ) ) {
	/**
	 * @param string $handle Script handle.
	 */
	function wp_enqueue_script( $handle, $src = '', $deps = array(), $ver = false, $args = false ): void {
		unset( $handle, $src, $deps, $ver, $args );
	}
}

if ( ! function_exists( 'wp_dequeue_script' ) ) {
	/**
	 * @param string $handle Script handle.
	 */
	function wp_dequeue_script( $handle ): void {
		$GLOBALS['cb_test_dequeued'][] = $handle;
	}
}

if ( ! function_exists( 'wp_enqueue_style' ) ) {
	/**
	 * @param string $handle Style handle.
	 */
	function wp_enqueue_style( $handle, $src = '', $deps = array(), $ver = false, $media = 'all' ): void {
		unset( $handle, $src, $deps, $ver, $media );
	}
}

if ( ! function_exists( 'shortcode_parse_atts' ) ) {
	/**
	 * @param string $text Shortcode attribute string.
	 * @return array<string, string>
	 */
	function shortcode_parse_atts( $text ): array {
		$atts = array();
		$text = trim( (string) $text );
		if ( '' === $text ) {
			return $atts;
		}
		if ( preg_match_all( '/([\w-]+)\s*=\s*"([^"]*)"/', $text, $matches, PREG_SET_ORDER ) ) {
			foreach ( $matches as $match ) {
				$atts[ $match[1] ] = $match[2];
			}
		}
		return $atts;
	}
}

if ( ! function_exists( 'wp_scripts' ) ) {
	/**
	 * @return object
	 */
	function wp_scripts() {
		if ( ! isset( $GLOBALS['cb_test_wp_scripts'] ) ) {
			$GLOBALS['cb_test_wp_scripts'] = (object) array(
				'registered' => array(),
			);
		}
		return $GLOBALS['cb_test_wp_scripts'];
	}
}

if ( ! function_exists( 'map_deep' ) ) {
	/**
	 * @param mixed    $value  Value to map.
	 * @param callable $callback Callback.
	 * @return mixed
	 */
	function map_deep( $value, $callback ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $index => $item ) {
				$value[ $index ] = map_deep( $item, $callback );
			}
			return $value;
		}
		return is_callable( $callback ) ? $callback( $value ) : $value;
	}
}

if ( ! function_exists( 'wp_slash' ) ) {
	/**
	 * @param mixed $value Value to slash.
	 * @return mixed
	 */
	function wp_slash( $value ) {
		return map_deep( $value, static function ( $item ) {
			return is_string( $item ) ? addslashes( $item ) : $item;
		} );
	}
}

if ( ! function_exists( 'wp_unslash' ) ) {
	/**
	 * @param mixed $value Value to unslash.
	 * @return mixed
	 */
	function wp_unslash( $value ) {
		return map_deep( $value, static function ( $item ) {
			return is_string( $item ) ? stripslashes( $item ) : $item;
		} );
	}
}

if ( ! function_exists( 'update_post_meta' ) ) {
	/**
	 * Unslashes the value like core's update_metadata().
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     Meta key.
	 * @param mixed  $value   Meta value.
	 */
	function update_post_meta( $post_id, $key, $value ): bool {
		$GLOBALS['cb_test_post_meta'][ (int) $post_id ][ $key ] = wp_unslash( $value );
		return true;
	}
}

if ( ! function_exists( 'get_option' ) ) {
	/**
	 * @param string $name    Option name.
	 * @param mixed  $default Default value.
	 * @return mixed
	 */
	function get_option( $name, $default = false ) {
		return $GLOBALS['cb_test_options'][ $name ] ?? $default;
	}
}

if ( ! function_exists( 'update_option' ) ) {
	/**
	 * @param string $name  Option name.
	 * @param mixed  $value Option value.
	 */
	function update_option( $name, $value ): bool {
		$GLOBALS['cb_test_options'][ $name ] = $value;
		return true;
	}
}

if ( ! function_exists( 'get_post_stati' ) ) {
	/**
	 * @return array<string, string>
	 */
	function get_post_stati(): array {
		$stati = array( 'publish', 'future', 'draft', 'pending', 'private', 'trash', 'auto-draft', 'inherit' );
		return array_combine( $stati, $stati );
	}
}

if ( ! function_exists( 'set_post_type' ) ) {
	/**
	 * @param int    $post_id   Post ID.
	 * @param string $post_type New post type.
	 */
	function set_post_type( $post_id, $post_type ): bool {
		$id = (int) $post_id;
		if ( ! empty( $GLOBALS['cb_test_set_post_type_fails'] ) || ! isset( $GLOBALS['cb_test_posts'][ $id ] ) ) {
			return false;
		}
		$GLOBALS['cb_test_posts'][ $id ]->post_type = $post_type;
		return true;
	}
}

if ( ! function_exists( 'get_post_type' ) ) {
	/**
	 * @param int $post_id Post ID.
	 * @return string|false
	 */
	function get_post_type( $post_id ) {
		return $GLOBALS['cb_test_posts'][ (int) $post_id ]->post_type ?? false;
	}
}

if ( ! class_exists( '_WP_Dependency' ) ) {
	/**
	 * Minimal _WP_Dependency stub for script registry tests.
	 */
	class _WP_Dependency {
		/** @var string */
		public $handle;
		/** @var string|false */
		public $src;
		/** @var array<int, string> */
		public $deps;

		/**
		 * @param string       $handle Handle.
		 * @param string|false $src    Source URL.
		 * @param array        $deps   Dependencies.
		 */
		public function __construct( $handle = '', $src = false, $deps = array() ) {
			$this->handle = $handle;
			$this->src    = $src;
			$this->deps   = $deps;
		}
	}
}

require_once COOPBOLO_PATH . 'includes/class-coopbolo-assets.php';
require_once COOPBOLO_PATH . 'includes/class-coopbolo-renderer.php';
require_once COOPBOLO_PATH . 'includes/class-coopbolo-collections.php';
require_once COOPBOLO_PATH . 'includes/class-coopbolo-admin-branding.php';
