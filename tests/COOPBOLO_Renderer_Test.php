<?php
/**
 * Unit tests for COOPBOLO_Renderer sanitization.
 *
 * @package CooperBoldLogoWrangler
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * @covers COOPBOLO_Renderer
 */
final class COOPBOLO_Renderer_Test extends TestCase {

	/** @var COOPBOLO_Renderer */
	private $renderer;

	protected function setUp(): void {
		parent::setUp();
		$this->renderer = new COOPBOLO_Renderer();
	}

	public function test_sanitize_logos_accepts_valid_https_urls(): void {
		$raw = array(
			array(
				'url' => 'https://example.com/logo.png',
				'alt' => 'Example',
			),
		);

		$logos = $this->renderer->sanitize_logos( $raw );

		$this->assertCount( 1, $logos );
		$this->assertSame( 'https://example.com/logo.png', $logos[0]['url'] );
		$this->assertSame( 'Example', $logos[0]['alt'] );
	}

	public function test_sanitize_logos_skips_empty_and_invalid_urls(): void {
		$raw = array(
			array( 'url' => '' ),
			array( 'url' => 'not-a-url' ),
			'not-an-array',
			array( 'url' => 'https://valid.test/a.svg' ),
		);

		$logos = $this->renderer->sanitize_logos( $raw );

		$this->assertCount( 1, $logos );
		$this->assertSame( 'https://valid.test/a.svg', $logos[0]['url'] );
	}

	public function test_sanitize_logos_rejects_javascript_links(): void {
		$raw = array(
			array(
				'url'  => 'https://example.com/logo.png',
				'link' => 'javascript:alert(1)',
			),
		);

		$logos = $this->renderer->sanitize_logos( $raw );

		$this->assertCount( 1, $logos );
		$this->assertSame( '', $logos[0]['link'] );
	}

	public function test_sanitize_logos_accepts_safe_https_links(): void {
		$raw = array(
			array(
				'url'  => 'https://example.com/logo.png',
				'link' => 'https://example.com',
			),
		);

		$logos = $this->renderer->sanitize_logos( $raw );

		$this->assertSame( 'https://example.com', $logos[0]['link'] );
	}

	public function test_sanitize_attributes_color_hex_named_and_rgb(): void {
		$hex = $this->renderer->sanitize_attributes(
			array(
				'logos'             => array(),
				'backgroundColor'   => '#ff00aa',
			)
		);
		$this->assertSame( '#ff00aa', $hex['backgroundColor'] );

		$named = $this->renderer->sanitize_attributes(
			array(
				'logos'             => array(),
				'backgroundColor'   => 'cornflowerblue',
			)
		);
		$this->assertSame( 'cornflowerblue', $named['backgroundColor'] );

		$rgb = $this->renderer->sanitize_attributes(
			array(
				'logos'             => array(),
				'backgroundColor'   => 'rgb(10, 20, 30)',
			)
		);
		$this->assertSame( 'rgb(10, 20, 30)', $rgb['backgroundColor'] );
	}

	public function test_sanitize_attributes_rejects_invalid_colors(): void {
		$result = $this->renderer->sanitize_attributes(
			array(
				'logos'             => array(),
				'backgroundColor'   => 'not-a-color',
			)
		);

		$this->assertSame( '', $result['backgroundColor'] );
	}

	public function test_density_factor_zeroed_when_density_aware_false(): void {
		$result = $this->renderer->sanitize_attributes(
			array(
				'logos'         => array(),
				'densityAware'  => false,
				'densityFactor' => 0.9,
			)
		);

		$this->assertFalse( $result['densityAware'] );
		$this->assertSame( 0, $result['densityFactor'] );
	}

	public function test_density_factor_clamped_when_density_aware_true(): void {
		$result = $this->renderer->sanitize_attributes(
			array(
				'logos'         => array(),
				'densityAware'  => true,
				'densityFactor' => 2.5,
			)
		);

		$this->assertTrue( $result['densityAware'] );
		$this->assertEquals( 1.0, $result['densityFactor'] );
	}

	public function test_numeric_attributes_clamped_to_ranges(): void {
		$result = $this->renderer->sanitize_attributes(
			array(
				'logos'               => array(),
				'baseSize'            => 999,
				'scaleFactor'         => -1,
				'contrastThreshold'   => 500,
				'gap'                 => 200,
			)
		);

		$this->assertSame( 256, $result['baseSize'] );
		$this->assertEquals( 0.0, $result['scaleFactor'] );
		$this->assertSame( 255, $result['contrastThreshold'] );
		$this->assertSame( 96, $result['gap'] );
	}

	public function test_gap_with_css_units_falls_back_to_default(): void {
		$result = $this->renderer->sanitize_attributes(
			array(
				'logos' => array(),
				'gap'   => '2rem',
			)
		);

		$this->assertSame( 28, $result['gap'] );
	}

	public function test_align_by_invalid_value_uses_default(): void {
		$result = $this->renderer->sanitize_attributes(
			array(
				'logos'   => array(),
				'alignBy' => 'invalid-align',
			)
		);

		$this->assertSame( 'visual-center-y', $result['alignBy'] );
	}

	public function test_resolve_alt_from_filename_when_alt_empty(): void {
		$logos = $this->renderer->sanitize_logos(
			array(
				array(
					'url' => 'https://example.com/acme-corp.svg',
					'alt' => '',
				),
			)
		);

		$this->assertSame( 'Acme Corp', $logos[0]['alt'] );
	}

	public function test_sanitize_logos_accepts_unlimited_entries(): void {
		$raw = array();
		for ( $i = 0; $i < 60; $i++ ) {
			$raw[] = array( 'url' => 'https://example.com/' . $i . '.png' );
		}

		$logos = $this->renderer->sanitize_logos( $raw );

		$this->assertCount( 60, $logos );
	}

	public function test_render_outputs_wrapper_and_inner_classes(): void {
		$html = $this->renderer->render(
			array(
				'logos' => array(
					array(
						'url' => 'https://example.com/logo.png',
						'alt' => 'Example',
					),
				),
			)
		);

		$this->assertStringContainsString( 'cb-lw-wrapper', $html );
		$this->assertStringContainsString( 'cb-lw-inner', $html );
		$this->assertStringContainsString( 'data-cb-lw="', $html );
		$this->assertMatchesRegularExpression(
			'/<div[^>]*cb-lw-wrapper[^>]*><div[^>]*cb-lw-inner[^>]*data-cb-lw=/',
			$html
		);
	}

	public function test_render_strip_placeholder_matches_hydrated_dom_structure(): void {
		$html = $this->renderer->render(
			array(
				'logos' => array(
					array(
						'url' => 'https://example.com/a.png',
						'alt' => 'Alpha',
					),
				),
				'gap' => 32,
			)
		);

		$this->assertMatchesRegularExpression(
			'/<div[^>]*cb-lw-inner[^>]*>[\s\S]*<div[^>]*text-align:center[^>]*>[\s\S]*<span[^>]*padding:16px[^>]*>[\s\S]*<img[^>]*alt="Alpha"/',
			$html
		);

		$linked = $this->renderer->render(
			array(
				'logos' => array(
					array(
						'url'  => 'https://example.com/b.png',
						'alt'  => 'Beta',
						'link' => 'https://beta.test',
					),
				),
			)
		);

		$this->assertMatchesRegularExpression(
			'/<span[^>]*>[\s\S]*<a href="https:\/\/beta\.test"[^>]*>[\s\S]*<img[^>]*alt="Beta"/',
			$linked
		);
	}

	public function test_render_carousel_full_wrapper_outputs_splide_markup(): void {
		$html = $this->renderer->render(
			array(
				'layout' => 'carousel',
				'logos'  => array(
					array(
						'url' => 'https://example.com/a.png',
						'alt' => 'A',
					),
					array(
						'url' => 'https://example.com/b.png',
						'alt' => 'B',
					),
				),
			)
		);

		$this->assertStringContainsString( 'cb-lw-carousel', $html );
		$this->assertStringContainsString( 'splide__slide', $html );
		$this->assertStringContainsString( 'logo-slider-slide', $html );
		$this->assertStringContainsString( 'data-cb-lw-ref=', $html );
		$this->assertStringContainsString( 'splide__track', $html );
		$this->assertStringContainsString( 'splide__list', $html );
	}

	public function test_render_carousel_slides_wrapper_outputs_fragments(): void {
		$html = $this->renderer->render(
			array(
				'layout'  => 'carousel',
				'wrapper' => 'slides',
				'logos'   => array(
					array(
						'url' => 'https://example.com/a.png',
						'alt' => 'A',
					),
				),
			)
		);

		$this->assertStringContainsString( 'cb-lw-carousel-host', $html );
		$this->assertStringContainsString( 'splide__slide', $html );
		$this->assertStringNotContainsString( 'splide__track', $html );
	}

	public function test_sanitize_layout_rejects_invalid_values(): void {
		$result = $this->renderer->sanitize_attributes(
			array(
				'logos'  => array(),
				'layout' => 'marquee',
			)
		);

		$this->assertSame( 'strip', $result['layout'] );
	}
}
