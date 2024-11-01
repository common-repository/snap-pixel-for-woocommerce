import {
	deactivatePlugin,
	activatePlugin,
	visitAdminPage,
} from '@wordpress/e2e-test-utils';

describe( 'Snap Pixel Settings', () => {
	beforeAll( async () => {
		jest.setTimeout( 100000 );
		await deactivatePlugin( 'snap-pixel-for-woocommerce' );
		await activatePlugin( 'snap-pixel-for-woocommerce' );
	} );

	it( 'Initialize to all on by default.', async () => {
		// Check general settings.
		await visitAdminPage( 'admin.php', 'page=snap-pixel' );
		await expect( page ).toMatchElement(
			'#snap_pixel_code_homepage:checked'
		);
		await expect( page ).toMatchElement(
			'#snap_pixel_code_pages:checked'
		);
		await expect( page ).toMatchElement(
			'#snap_pixel_code_posts:checked'
		);
		await expect( page ).toMatchElement(
			'#snap_pixel_code_search:checked'
		);
		await expect( page ).toMatchElement(
			'#snap_pixel_code_categories:checked'
		);
		await expect( page ).toMatchElement(
			'#snap_pixel_code_tags:checked'
		);
		await expect( page ).toMatchElement(
			'#snap_pixel_places_woocommerce_cart:checked'
		);
		await expect( page ).toMatchElement(
			'#snap_pixel_places_woocommerce_checkout:checked'
		);
		await expect( page ).toMatchElement(
			'#snap_pixel_places_woocommerce_paymentinfo:checked'
		);
		await expect( page ).toMatchElement(
			'#snap_pixel_places_woocommerce_addtocart:checked'
		);
	} );
} );
