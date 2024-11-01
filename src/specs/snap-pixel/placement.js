import {
	deactivatePlugin,
	activatePlugin,
	visitAdminPage,
} from '@wordpress/e2e-test-utils';

describe( 'Snap Pixel Placement', () => {
	const TIMEOUT = 100000;
	const PIXEL_ID = 'd35c17f9-c6e5-46cc-9ba9-b116dd77b7b3';
	const product = {
		id: '9',
		slug: 'product',
		category: 'Uncategorized',
		currency: 'USD',
		price: 123,
	};
	const snaptr = jest.fn();
	beforeAll( async () => {
		// Set up mocks.
		jest.setTimeout( TIMEOUT );
		await page.exposeFunction( 'snaptr', snaptr );

		// Set up the pixel.
		await deactivatePlugin( 'snap-pixel-for-woocommerce' );
		await activatePlugin( 'snap-pixel-for-woocommerce' );
		await visitAdminPage( 'admin.php', 'page=snap-pixel' );
		await expect( page ).toFill(
			'input[name="snap_pixel_code[pixel_id]"]',
			PIXEL_ID
		);
		await expect( page ).toClick( '#submit' );
	}, TIMEOUT );
	beforeEach( snaptr.mockClear );

	it( 'Works for page views.', async () => {
		await page.goto( 'http://localhost:8889' );
		expect( snaptr ).toHaveBeenNthCalledWith( 1, 'init', '', {
			'user_email': 'wordpress@example.com'
		} );
		expect( snaptr ).toHaveBeenNthCalledWith( 2, 'track', 'PAGE_VIEW' );
	} );

	it( 'Works for searches.', async () => {
		const searchString = 'something';
		await page.goto( `http://localhost:8889/?s=${ searchString }` );
		expect( snaptr ).toHaveBeenNthCalledWith( 1, 'init', PIXEL_ID, {
			'user_email': 'wordpress@example.com'
		} );
		expect( snaptr ).toHaveBeenNthCalledWith( 2, 'track', 'PAGE_VIEW' );
		expect( snaptr ).toHaveBeenNthCalledWith( 3, 'track', 'SEARCH', {
			search_string: searchString,
		} );
	} );

	it( 'Works for product views.', async () => {
		await page.goto( `http://localhost:8889/?product=${ product.slug }` );
		expect( snaptr ).toHaveBeenNthCalledWith( 1, 'init', PIXEL_ID, {
			'user_email': 'wordpress@example.com'
		} );
		expect( snaptr ).toHaveBeenNthCalledWith( 2, 'track', 'PAGE_VIEW' );
		expect( snaptr ).toHaveBeenNthCalledWith( 3, 'track', 'VIEW_CONTENT' );
	} );

	it( 'Works for the entire "add to cart", checkout, and purchase flow.', async () => {
		// Go to product page.
		await page.goto( `http://localhost:8889/?product=${ product.slug }` );
		expect( snaptr ).toHaveBeenNthCalledWith( 1, 'init', PIXEL_ID, {
			'user_email': 'wordpress@example.com'
		} );
		expect( snaptr ).toHaveBeenNthCalledWith( 2, 'track', 'PAGE_VIEW' );
		expect( snaptr ).toHaveBeenNthCalledWith( 3, 'track', 'VIEW_CONTENT' );

		// Add to cart.
		await page.focus( '.single_add_to_cart_button' );
		await page.waitFor( 1000 );
		await Promise.all( [
			page.waitForNavigation(),
			expect( page ).toClick( '.single_add_to_cart_button' ),
		] );
		await page.waitFor( 5000 );
		expect( snaptr ).toHaveBeenNthCalledWith( 4, 'init', PIXEL_ID, {
			'user_email': 'wordpress@example.com'
		} );
		expect( snaptr ).toHaveBeenNthCalledWith( 5, 'track', 'PAGE_VIEW' );
		expect( snaptr ).toHaveBeenNthCalledWith( 6, 'track', 'VIEW_CONTENT' );
		expect( snaptr ).toHaveBeenNthCalledWith( 7, 'track', 'ADD_CART', {
			item_ids: [ product.id ],
			item_category: product.category,
			currency: product.currency,
			price: product.price,
		} );

		// View cart.
		await Promise.all( [
			page.waitForNavigation(),
			expect( page ).toClick( '.wc-forward' ),
		] );
		expect( snaptr ).toHaveBeenNthCalledWith( 8, 'init', PIXEL_ID, {
			'user_email': 'wordpress@example.com'
		} );
		expect( snaptr ).toHaveBeenNthCalledWith( 9, 'track', 'PAGE_VIEW' );

		// Start checkout.
		await page.focus( '.wc-forward' );
		await page.waitFor( 1000 );
		await Promise.all( [
			page.waitForNavigation(),
			expect( page ).toClick( '.wc-forward' ),
		] );
		expect( snaptr ).toHaveBeenNthCalledWith( 10, 'init', PIXEL_ID, {
			'user_email': 'wordpress@example.com'
		} );
		expect( snaptr ).toHaveBeenNthCalledWith( 11, 'track', 'PAGE_VIEW' );
		const price = await page.$eval(
			'.woocommerce-Price-amount',
			( element ) =>
				parseInt( element.textContent.slice( 1 ).replace( ',', '' ) )
		);
		expect( snaptr ).toHaveBeenNthCalledWith(
			12,
			'track',
			'START_CHECKOUT',
			{
				item_ids: expect.arrayContaining( [ product.id ] ),
				currency: product.currency,
				price,
				number_items: expect.stringMatching( /^\d+$/ ),
			}
		);

		// Complete check out.
		await page.focus( '#billing_first_name' );
		await page.waitFor( 1000 );
		await expect( page ).toFill( '#billing_first_name', 'John' );
		await expect( page ).toFill( '#billing_last_name', 'Smith' );
		await page.focus( '#billing_address_1' );
		await page.waitFor( 1000 );
		await expect( page ).toFill( '#billing_address_1', '123' );
		await expect( page ).toFill( '#billing_city', 'Los Angeles' );
		await page.focus( '#billing_postcode' );
		await page.waitFor( 1000 );
		await expect( page ).toFill( '#billing_postcode', '12345' );
		await expect( page ).toFill( '#billing_phone', '1231231234' );
		await page.focus( '#place_order' );
		await page.waitFor( 1000 );
		await Promise.all( [
			page.waitForNavigation(),
			await expect( page ).toClick( '#place_order' ),
		] );
		expect( snaptr ).toHaveBeenNthCalledWith( 13, 'init', PIXEL_ID, {
			'user_email': 'wordpress@example.com'
		} );
		expect( snaptr ).toHaveBeenNthCalledWith( 14, 'track', 'PAGE_VIEW' );
		expect( snaptr ).toHaveBeenNthCalledWith( 15, 'track', 'ADD_BILLING' );
		expect( snaptr ).toHaveBeenNthCalledWith( 16, 'track', 'PURCHASE', {
			item_ids: expect.arrayContaining( [ product.id ] ),
			currency: product.currency,
			price: price,
			number_items: expect.stringMatching( /^\d+$/ ),
			transaction_id: expect.stringMatching( /^\d+$/ ),
		} );
	} );
} );
