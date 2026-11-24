<?php
/**
 * Plugin bootstrap: block registration, shortcodes, and render delegation.
 *
 * @package CooperBoldLogoWrangler
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once COOPBOLO_PATH . 'includes/class-coopbolo-assets.php';
require_once COOPBOLO_PATH . 'includes/class-coopbolo-renderer.php';
require_once COOPBOLO_PATH . 'includes/class-coopbolo-collections.php';
require_once COOPBOLO_PATH . 'includes/class-coopbolo-admin-branding.php';

/**
 * Singleton that wires assets, collections, branding, block, and shortcode output.
 */
final class COOPBOLO_Plugin {

	private static ?COOPBOLO_Plugin $instance = null;
	private COOPBOLO_Renderer $renderer;

	/**
	 * @return COOPBOLO_Plugin Plugin singleton.
	 */
	public static function instance(): COOPBOLO_Plugin {
		return self::$instance ?? ( self::$instance = new self() );
	}

	private function __construct() {
		new COOPBOLO_Assets();
		new COOPBOLO_Collections();
		new COOPBOLO_Admin_Branding();
		$this->renderer = new COOPBOLO_Renderer();
		add_action( 'init', array( $this, 'register_block' ) );
		add_shortcode( 'logo_wrangler', array( $this, 'render_shortcode' ) );
	}

	/**
	 * Register the Gutenberg block from build/ or src/ block.json.
	 */
	public function register_block(): void {
		$candidates = array(
			COOPBOLO_PATH . 'build/block',
			COOPBOLO_PATH . 'src/block',
		);
		$dir = '';
		foreach ( $candidates as $candidate ) {
			if ( file_exists( $candidate . '/block.json' ) ) {
				$dir = $candidate;
				break;
			}
		}
		if ( '' === $dir ) {
			return;
		}
		register_block_type( $dir, array( 'render_callback' => array( $this, 'render_block' ) ) );
	}

	/**
	 * Server-side render callback for cooper-bold/logo-wrangler.
	 *
	 * @param array<string, mixed> $attributes Block attributes.
	 * @param string               $content    Inner block content (unused).
	 * @param WP_Block             $block      Block instance.
	 * @return string Rendered HTML.
	 */
	public function render_block( array $attributes, string $content, WP_Block $block ): string {
		$attributes = $this->prepare_block_attributes( $attributes );
		return $this->renderer->render(
			$attributes,
			get_block_wrapper_attributes(
				array( 'class' => 'cb-lw cb-lw-wrapper' )
			)
		);
	}

	/**
	 * When a block uses a collection, only pass the collection reference so
	 * settings are loaded fresh from the collection post (not block defaults).
	 *
	 * @param array<string, mixed> $attributes Block attributes.
	 * @return array<string, mixed>
	 */
	private function prepare_block_attributes( array $attributes ): array {
		$collection_id = isset( $attributes['collectionId'] ) ? absint( $attributes['collectionId'] ) : 0;
		if ( $collection_id > 0 ) {
			$prepared = array( 'collectionId' => $collection_id );
			if ( ! empty( $attributes['className'] ) ) {
				$prepared['className'] = (string) $attributes['className'];
			}
			if ( ! empty( $attributes['layout'] ) ) {
				$prepared['layout'] = (string) $attributes['layout'];
			}
			if ( ! empty( $attributes['wrapper'] ) ) {
				$prepared['wrapper'] = (string) $attributes['wrapper'];
			}
			return $prepared;
		}
		return $attributes;
	}

	/**
	 * Render `[logo_wrangler]` and legacy shortcode aliases.
	 *
	 * @param array<string, string>|string $atts    Shortcode attributes.
	 * @param string|null                  $content Enclosed content (unused).
	 * @param string                       $tag     Shortcode tag name.
	 * @return string Rendered HTML.
	 */
	public function render_shortcode( $atts, ?string $content = null, string $tag = 'logo_wrangler' ): string {
		$a = shortcode_atts(
			array(
				'collection'         => '',
				'id'                 => '',
				'logos'              => '',
				'base_size'          => '',
				'scale_factor'       => '',
				'contrast_threshold' => '',
				'density_aware'      => '',
				'density_factor'     => '',
				'crop_to_content'    => '',
				'background_color'   => '',
				'align_by'           => '',
				'gap'                => '',
				'layout'             => '',
				'wrapper'            => '',
				'class'              => '',
			),
			$atts,
			$tag
		);

		$attributes = array();

		$collection_id = absint( $a['id'] );
		if ( $collection_id > 0 ) {
			$attributes['collectionId'] = $collection_id;
		} elseif ( '' !== trim( (string) $a['collection'] ) ) {
			$attributes['collection'] = sanitize_title( (string) $a['collection'] );
		}

		$parsed_logos = $this->parse_logos( (string) $a['logos'] );
		if ( ! empty( $parsed_logos ) ) {
			$attributes['logos'] = $parsed_logos;
		}

		$scalar_map = array(
			'base_size'          => 'baseSize',
			'scale_factor'       => 'scaleFactor',
			'contrast_threshold' => 'contrastThreshold',
			'density_factor'     => 'densityFactor',
			'background_color'   => 'backgroundColor',
			'align_by'           => 'alignBy',
			'gap'                => 'gap',
			'class'              => 'className',
		);

		foreach ( $scalar_map as $shortcode_key => $attr_key ) {
			if ( '' !== $a[ $shortcode_key ] ) {
				$attributes[ $attr_key ] = $a[ $shortcode_key ];
			}
		}

		if ( '' !== $a['density_aware'] ) {
			$attributes['densityAware'] = filter_var( $a['density_aware'], FILTER_VALIDATE_BOOLEAN );
		}
		if ( '' !== $a['crop_to_content'] ) {
			$attributes['cropToContent'] = filter_var( $a['crop_to_content'], FILTER_VALIDATE_BOOLEAN );
		}
		if ( '' !== $a['layout'] ) {
			$attributes['layout'] = sanitize_key( (string) $a['layout'] );
		}
		if ( '' !== $a['wrapper'] ) {
			$attributes['wrapper'] = sanitize_key( (string) $a['wrapper'] );
		}

		return $this->renderer->render( $attributes );
	}

	/**
	 * Parse logos from shortcode attribute (comma list, JSON, or base64 JSON).
	 *
	 * @param string $value Raw logos attribute.
	 * @return array<int, array<string, mixed>> Sanitized logo rows.
	 */
	private function parse_logos( string $value ): array {
		$value = trim( $value );
		if ( '' === $value ) {
			return array();
		}
		if ( 0 === stripos( $value, 'base64:' ) ) {
			$decoded = json_decode(
				base64_decode( substr( $value, 7 ), true ) ?: '',
				true
			);
			return is_array( $decoded ) ? $this->renderer->sanitize_logos( $decoded ) : array();
		}
		if ( '[' === $value[0] ) {
			$decoded = json_decode( html_entity_decode( $value, ENT_QUOTES ), true );
			return is_array( $decoded ) ? $this->renderer->sanitize_logos( $decoded ) : array();
		}
		$logos = array();
		$id = 1;
		foreach ( array_map( 'trim', explode( ',', $value ) ) as $chunk ) {
			if ( '' === $chunk ) {
				continue;
			}
			$p = array_map( 'trim', explode( '|', $chunk, 3 ) );
			$url = esc_url_raw( $p[0] );
			if ( '' === $url ) {
				continue;
			}
			$row = array( 'id' => $id++, 'url' => $url, 'alt' => isset( $p[1] ) ? sanitize_text_field( $p[1] ) : '' );
			if ( isset( $p[2] ) ) {
				$link = esc_url_raw( $p[2] );
				if ( '' !== $link && 0 !== stripos( $link, 'javascript:' ) ) {
					$row['link'] = $link;
				}
			}
			$logos[] = $row;
		}
		return $logos;
	}
}
