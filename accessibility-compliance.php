<?php

/**
 * Plugin Name:       Accessibility Compliance
 * Plugin URI:        https://www.achecks.org/accessibility-compliance-wordpress-plugin/
 * Description:       This companion plugin to the ACHECKS.org service helps manage and report on your website’s accessibility compliance using the most popular accessibility checkers such as AChecker and Lighthouse for web or Tingtun for PDFs.
 * Version:           0.0.3
 * Requires at least: 5.2
 * Requires PHP:      5.6
 * Author:            ACHECKS
 * Author URI:        https://www.achecks.org
 * License:           Apache License 2.0
 * License URI:       https://www.apache.org/licenses/LICENSE-2.0
 * Text Domain:       accessibility-compliance
 */

defined('ABSPATH') or die();

class ACACHECKS_AccessibilityComplianceByACHECKS
{
	function __construct()
	{
		define( 'ACACHECKS_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'ACACHECKS_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

		if ( is_admin() )
		{
			add_action( 'admin_enqueue_scripts', array( $this, 'acachecks_admin_enqueue_scripts' ) );
			add_action( 'admin_menu', array( $this, 'acachecks_admin_menu' ) );
			add_action( 'admin_head', array( $this, 'acachecks_inline_styles' ) );
			add_action( 'admin_init', array( $this, 'acachecks_build_settings_fields' ) );
		}
	}

	public static function acachecks_admin_enqueue_scripts()
	{
		wp_enqueue_style( 'acachecks-material-css', ACACHECKS_PLUGIN_URL . '/assets/material-components-web.min.css' );
		wp_enqueue_style( 'acachecks-material-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons' );
		wp_enqueue_script( 'acachecks-material-js', ACACHECKS_PLUGIN_URL . '/assets/material-components-web.min.js' );
	}

	public function acachecks_admin_menu()
	{
		global $submenu;
		add_menu_page( 'Accessibility Compliance by ACHECKS', 'Accessibility Compliance', 'manage_options', 'acachecks', array( $this, 'acachecks' ), 'dashicons-universal-access-alt' );
		add_submenu_page( 'acachecks', 'Settings', 'Settings', 'manage_options', 'acachecks_settings', array( $this, 'acachecks_settings' ) );
		$submenu['acachecks'][0][0] = 'Dashboard';
	}

	public function acachecks_inline_styles()
	{
	?>
<style type="text/css" media="screen">
	:root
	{
		--mdc-theme-primary: #37474F;
		--mdc-theme-secondary: #9DFF20;
	}
	svg.achecks-logo
	{
		max-height: 40px;
		float: right;
		margin: 5px;
		color-scheme: only light;
	}
	.content
	{
		display: none;
	}
	.content--active
	{
		display: block;
		margin-top: 20px;
	}
	.mdc-dialog
	{
		z-index: 999999;
	}
	.mdc-dialog__surface
	{
		height: 100%;
	}
	.material-icons.small
	{
		font-size: 14px;
		vertical-align: middle;
	}
	.material-icons.grey
	{
		color: #616161;
	}
	.material-icons.green
	{
		color: #1B5E20;
	}
	.material-icons.amber
	{
		color: #A86200;
	}
	.material-icons.red
	{
		color: #B71C1C;
	}
	.width-400
	{
		max-width: 400px;
		overflow: hidden;
	}
	.hidden
	{
		display: none !important;
	}
	.sticky-action
	{
		position: sticky;
		right: 0;
		background-color: white;
	}
	.body-content
	{
		max-width: 650px;
		margin: auto;
	}
	body .mdc-typography--headline5
	{
		text-transform: uppercase;
		font-weight: 700;
	}
	body .mdc-typography--headline4
	{
		font-weight: 900;
	}
	.mdc-card
	{
		padding: 24px;
	}
	.full-width
	{
		width: 100%;
	}
	.text-align-center
	{
		text-align: center;
	}
	.flex-bottom
	{
		flex: auto;
	}
	.flex-bottom > div
	{
		place-self: flex-end;
	}
	iframe
	{
		width: 100%;
		min-height: 200px;
	}
	.mdc-button--raised:not(:disabled)
	{
		background-color: #9DFF20;
		color: black;
		min-width: 20vw;
	}
	.price .currency
	{
		font-weight: 800;
		color: #37474F;
		font-size: 18px;
		margin-right: 5px;
		float: left;
		margin-top: 3px;
	}
	.price .amount
	{
		font-weight: 900;
		font-size: 42px;
	}
	.price .cadence
	{
		font-weight: 500px;
		color: #37474F;
		font-size: 14px;
		margin-left: 10px;
		display: inline-block;
		width: 50px;
	}
	.checkmarks
	{
		list-style-type: none;
	}
	.checkmarks > li:before
	{
		content: "✓ ";
		font-weight: 800;
		background-color: var(--mdc-theme-secondary);
		color-scheme: light only;
		margin-right: 10px;
		padding: 2px 0 0 2px;
		border-radius: 100%;
	}
</style>
	<?php
	}

	public function acachecks()
	{
		$page = isset( $_GET['p'] ) ? intval( $_GET['p'] ) : 0;
		$response = wp_remote_get( 'https://achecks.org/api/v1/domain/' . parse_url( home_url(), PHP_URL_HOST ) . '/' .  $page, array(
			'headers' => array(
				'referer'        => home_url(),
				'Connection-Key' => get_option( 'acachecks_connection_key_setting' )
			)
		) );
		$code = wp_remote_retrieve_response_code( $response );
?>
<a href="https://www.achecks.org" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" class="achecks-logo" viewBox="0 0 600 160"><title>ACHECKS Accessibility Checker</title><defs><style>.white { fill: white; font-family: Noto Sans; font-size: 97px; }.black { fill: #212121; font-family: Noto Sans; font-size: 97px; letter-spacing: 7px; }@media (prefers-color-scheme: light) {.black { fill: #212121; }.white { fill: white; }}@media (prefers-color-scheme: dark) {.black { fill: white; }.white { fill: #212121 !important; }}</style></defs><rect fill="none" x="20" y="20" width="570" height="140" rx="20"></rect><rect class="black" x="30" y="30" width="120" height="120" rx="12"></rect><text class="white" x="60" y="125" width="120">A</text><text class="black" x="165" y="125" width="120">CHECKS</text></svg></a>
<h1 class="mdc-typography--headline6">Accessibility Compliance by ACHECKS</h1>
<div id="main-tabs" class="mdc-tab-bar" role="tablist">
	<div class="mdc-tab-scroller">
		<div class="mdc-tab-scroller__scroll-area">
			<div class="mdc-tab-scroller__scroll-content">
				<button class="mdc-tab mdc-tab--active" role="tab" aria-selected="true" tabindex="0"><span class="mdc-tab__content"><span class="mdc-tab__text-label"><?php echo esc_html( $code === 404 ? 'Plans' : ( $code === 403 ? 'Error' : 'Domain' ) ); ?></span></span><span class="mdc-tab-indicator mdc-tab-indicator--active"><span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span></span><span class="mdc-tab__ripple"></span></button>
					<button class="mdc-tab mdc-tab" role="tab" aria-selected="true" tabindex="0"><span class="mdc-tab__content"><span class="mdc-tab__text-label">Support</span></span><span class="mdc-tab-indicator mdc-tab-indicator"><span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span></span><span class="mdc-tab__ripple"></span></button>
					<button class="mdc-tab mdc-tab" role="tab" aria-selected="true" tabindex="0"><span class="mdc-tab__content"><span class="mdc-tab__text-label">About</span></span><span class="mdc-tab-indicator mdc-tab-indicator"><span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span></span><span class="mdc-tab__ripple"></span></button>
				</div>
			</div>
		</div>
	</div>
	<div class="content content--active main-tabs-content">
	<?php if ( $code === 404 ): ?>
		<div class="body-content">
			<p class="mdc-typography--body1">For any level of compliance checks that you require, we have you covered. You can choose from either the AChecker plan, which provides for <abbr title="Web Content Accessibility Guidelines">WCAG</abbr> 2.0 AA based on the markup of your website. Or you can opt for one of our Lighthouse plans which cover <abbr title="Web Content Accessibility Guidelines">WCAG</abbr> 2.1 AA.</p>
			<p class="mdc-typography--body1">Lighthouse reports are generated based on a rendering of your webpages on an actual browser. All of our plans are fully automated running in the background, with no additional code required on your websites.</p>
			<p class="mdc-typography--body1">Our service also checks your website for any linked PDFs that you host and runs it against the Tingtun accessibility checker. ACHECKS is the only automated accessibility scanner to cover both web pages and PDFs.</p>
			<h2 class="mdc-typography--headline5">Select a plan to get started:</h2>
		</div>
		<div class="mdc-layout-grid__inner">
			<div class="mdc-card mdc-layout-grid__cell mdc-layout-grid__cell--span-4">
				<h1 class="mdc-typography--headline4">AChecker Basic</h1>
				<p><abbr title="Web Content Accessibility Guidelines">WCAG</abbr> 2.0 AA Accessibility</p>
				<h2 class="price"><sup class=currency></sup><span class="amount">99</span><span class="cadence">per month</span></h2>
				<div class="mdc-card__actions full-width">
					<div class="mdc-card__action-buttons full-width">
						<a href="https://achecks.org/signup#achecker" class="mdc-button mdc-button--raised mdc-card__action mdc-card__action--button full-width">
							<div class="mdc-button__ripple"></div>
							<span class="mdc-button__label">Subscribe</span>
						</a>
					</div>
				</div>
				<p>This includes:</p>
				<ul class="checkmarks">
					<li>Web Reports for a Domain</li>
					<li>PDF Reports for a Domain</li>
				</ul>
			</div>
			<div class="mdc-card mdc-layout-grid__cell mdc-layout-grid__cell--span-4">
				<h1 class="mdc-typography--headline4">Lighthouse Single</h1>
				<p><abbr title="Web Content Accessibility Guidelines">WCAG</abbr> 2.1 AA Accessibility</p>
				<h2 class="price"><sup class=currency></sup><span class="amount">199</span><span class="cadence">per month</span></h2>
				<div class="mdc-card__actions full-width">
					<div class="mdc-card__action-buttons full-width">
						<a href="https://achecks.org/signup#lh_single" class="mdc-button mdc-button--raised mdc-card__action mdc-card__action--button full-width">
							<div class="mdc-button__ripple"></div>
							<span class="mdc-button__label">Subscribe</span>
						</a>
					</div>
				</div>
				<p>This includes:</p>
				<ul class="checkmarks">
					<li>AChecker Features</li>
					<li>Lighthouse Mobile OR Desktop</li>
				</ul>
			</div>
			<div class="mdc-card mdc-layout-grid__cell mdc-layout-grid__cell--span-4">
				<h1 class="mdc-typography--headline4">Lighthouse Full</h1>
				<p><abbr title="Web Content Accessibility Guidelines">WCAG</abbr> 2.1 AA Accessibility</p>
				<h2 class="price"><sup class=currency></sup><span class="amount">249</span><span class="cadence">per month</span></h2>
				<div class="mdc-card__actions full-width">
					<div class="mdc-card__action-buttons full-width">
						<a href="https://achecks.org/signup#lh_full" class="mdc-button mdc-button--raised mdc-card__action mdc-card__action--button full-width">
							<div class="mdc-button__ripple"></div>
							<span class="mdc-button__label">Subscribe</span>
						</a>
					</div>
				</div>
				<p>This includes:</p>
				<ul class="checkmarks">
					<li>AChecker Features</li>
					<li>Lighthouse Mobile AND Desktop</li>
				</ul>
			</div>
		</div>
	<?php elseif ( $code === 403 ): ?>
		Connection error, check that your connection key in settings matches the expected value for the ACHECKS domain. Contact support if you need help with a misconfigured website.
	<?php else:
		$obj = json_decode( $response['body'] );
		global $pagenow, $plugin_page;
		$this_page = add_query_arg( 'page', $plugin_page, admin_url( $pagenow ) );
		$prev_link = add_query_arg( 'p', $page - 1, $this_page );
		$next_link = add_query_arg( 'p', $page + 1, $this_page );
		if ( $obj->domain->connection_key )
		{
			update_option( 'acachecks_connection_key_setting', $obj->domain->connection_key );
		}
	?>
<div class="mdc-layout-grid__inner"><div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12 mdc-elevation--z1 mdc-data-table"><div class="mdc-data-table__table-container">
	<table class="mdc-data-table__table" aria-label="Web Pages">
		<thead>
			<tr class="mdc-data-table__header-row">
				<th class="mdc-data-table__header-cell width-400" role="columnheader" scope="col">Path</th>
				<?php if ( in_array( 'achecker', $obj->domain->categories ) || in_array( 'tingtun', $obj->domain->categories ) ): ?>
					<th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col">Assessed</th>
					<th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col">Errors</th>
				<?php endif; ?>
				<?php if ( in_array( 'lh_accessibility', $obj->domain->categories ) ): ?>
					<th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col">Accessibility</th>
				<?php endif; ?>
				<?php if ( in_array( 'lh_best-practices', $obj->domain->categories ) ): ?>
					<th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col">Practices</th>
				<?php endif; ?>
				<?php if ( in_array( 'lh_seo', $obj->domain->categories ) ): ?>
					<th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col"><abbr title="Search Engine Optimization">SEO</abbr></th>
				<?php endif; ?>
				<?php if ( in_array( 'lh_performance', $obj->domain->categories ) ): ?>
					<th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col">Speed</th>
				<?php endif; ?>
				<th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col">Status</th>
				<th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col">Content</th>
				<th class="mdc-data-table__header-cell" role="columnheader" scope="col">Title</th>
				<th class="mdc-data-table__header-cell sticky-action" role="columnheader" scope="col">Actions</th>
			</tr>
		</thead>
		<tbody class="mdc-data-table__content">
		<?php foreach ( $obj->urls as $i => $u ): ?>
			<tr class="mdc-data-table__row">
				<td class="mdc-data-table__cell width-400" scope="row"><a target="_blank" href="<?php echo esc_url( $u->url ); ?>" data-tooltip-id="tt-url-<?php echo absint( $i ); ?>"><em aria-label="Link opens in new tab" class="material-icons small">open_in_new</em> <?php echo esc_html( $u->path ? $u->path : '/' ); ?></a></td>
				<?php if ( in_array( 'achecker', $obj->domain->categories ) || in_array( 'tingtun', $obj->domain->categories ) ): ?>
				<?php $rag = ( $u->status !== 200 ? 'grey' : ( ( ( $u->format === 'PDF' && $u->tingtun_errors === 0 ) || ( $u->format === 'HTML' && $u->achecker_errors === 0 ) ) ? 'green' : ( ( ( $u->format === 'PDF' && $u->tingtun_errors < $obj->domain->threshold_okay ) || ( $u->format === 'HTML' && $u->achecker_errors < $obj->domain->threshold_okay ) ) ? 'amber' : 'red' ) ) ); ?>
					<td class="mdc-data-table__cell mdc-data-table__cell--numeric" scope="row"><em aria-label="<?php echo esc_html( $rag === 'green' ? 'Pass' : ( $rag === 'amber' ? 'Okay' : ( $rag === 'red' ? 'Poor' : 'Not applicable' ) ) ); ?>" class="material-icons small middle <?php echo esc_html( $u->achecker_errors === 0 || $u->achecker_errors > 0 || $u->tingtun_errors === 0 || $u->tingtun_errors > 0 ? $rag : 'hidden' ); ?>"><?php echo esc_html( $rag === 'green' ? 'check_circle' : ( $rag === 'amber' ? 'warning' : ( $rag === 'red' ? 'stop_circle' : 'disabled_by_default' ) ) ); ?></em></td>
					<td class="mdc-data-table__cell mdc-data-table__cell--numeric" scope="row"><?php echo absint( $u->format === 'HTML' ? $u->achecker_errors : $u->tingtun_errors ); ?></td>
				<?php endif; ?>
				<?php if ( in_array( 'lh_accessibility', $obj->domain->categories ) ): ?>
					<td class="mdc-data-table__cell mdc-data-table__cell--numeric" scope="row">
					<?php if ( $u->status !== 200 ): ?>
						<em aria-label="Not applicable" class="material-icons small middle grey">disabled_by_default</em>
					<?php else: ?>
						<?php if ( $obj->domain->lh_mobile && isset( $u->lighthouse_mobile_accessibility ) ): ?>
							<p><em aria-label="Mobile" class="material-icons small <?php echo esc_html( $u->lighthouse_mobile_accessibility > 89 ? 'green' : ( $u->lighthouse_mobile_accessibility > 49 ? 'amber' : 'red' ) ); ?>">smartphone</em> <?php echo esc_html( $u->lighthouse_mobile_accessibility ); ?></p>
						<?php endif; ?>
						<?php if ( $obj->domain->lh_desktop && isset( $u->lighthouse_desktop_accessibility ) ): ?>
							<p><em aria-label="Desktop" class="material-icons small <?php echo esc_html( $u->lighthouse_desktop_accessibility > 89 ? 'green' : ( $u->lighthouse_desktop_accessibility > 49 ? 'amber' : 'red' ) ); ?>">computer</em> <?php echo esc_html( $u->lighthouse_desktop_accessibility ); ?></p>
						<?php endif; ?>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ( in_array( 'lh_best-practices', $obj->domain->categories ) ): ?>
					<td class="mdc-data-table__cell mdc-data-table__cell--numeric" scope="row">
					<?php if ( $u->status !== 200 ): ?>
						<em aria-label="Not applicable" class="material-icons small middle grey">disabled_by_default</em>
					<?php else: ?>
						<?php if ( $obj->domain->lh_mobile && isset( $u->lighthouse_mobile_best_practice ) ): ?>
							<p><em aria-label="Mobile" class="material-icons small <?php echo esc_html( $u->lighthouse_mobile_best_practice > 89 ? 'green' : ( $u->lighthouse_mobile_best_practice > 49 ? 'amber' : 'red' ) ); ?>">smartphone</em> <?php echo esc_html( $u->lighthouse_mobile_best_practice ); ?></p>
						<?php endif; ?>
						<?php if ( $obj->domain->lh_desktop && isset( $u->lighthouse_desktop_best_practice ) ): ?>
							<p><em aria-label="Desktop" class="material-icons small <?php echo esc_html( $u->lighthouse_desktop_best_practice > 89 ? 'green' : ( $u->lighthouse_desktop_best_practice > 49 ? 'amber' : 'red' ) ); ?>">computer</em> <?php echo esc_html( $u->lighthouse_desktop_best_practice ); ?></p>
						<?php endif; ?>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ( in_array( 'lh_seo', $obj->domain->categories ) ): ?>
					<td class="mdc-data-table__cell mdc-data-table__cell--numeric" scope="row">
					<?php if ( $u->status !== 200 ): ?>
						<em aria-label="Not applicable" class="material-icons small middle grey">disabled_by_default</em>
					<?php else: ?>
						<?php if ( $obj->domain->lh_mobile && isset( $u->lighthouse_mobile_seo ) ): ?>
							<p><em aria-label="Mobile" class="material-icons small <?php echo esc_html( $u->lighthouse_mobile_seo > 89 ? 'green' : ( $u->lighthouse_mobile_seo > 49 ? 'amber' : 'red' ) ); ?>">smartphone</em> <?php echo esc_html( $u->lighthouse_mobile_seo ); ?></p>
						<?php endif; ?>
						<?php if ( $obj->domain->lh_desktop && isset( $u->lighthouse_desktop_seo ) ): ?>
							<p><em aria-label="Desktop" class="material-icons small <?php echo esc_html( $u->lighthouse_desktop_seo > 89 ? 'green' : ( $u->lighthouse_desktop_seo > 49 ? 'amber' : 'red' ) ); ?>">computer</em> <?php echo esc_html( $u->lighthouse_desktop_seo ); ?></p>
						<?php endif; ?>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ( in_array( 'lh_performance', $obj->domain->categories ) ): ?>
					<td class="mdc-data-table__cell mdc-data-table__cell--numeric" scope="row">
					<?php if ( $u->status !== 200 ): ?>
						<em aria-label="Not applicable" class="material-icons small middle grey">disabled_by_default</em>
					<?php else: ?>
						<?php if ( $obj->domain->lh_mobile && isset( $u->lighthouse_mobile_performance ) ): ?>
							<p><em aria-label="Mobile" class="material-icons small <?php echo esc_html( $u->lighthouse_mobile_performance > 89 ? 'green' : ( $u->lighthouse_mobile_performance > 49 ? 'amber' : 'red' ) ); ?>">smartphone</em> <?php echo esc_html( $u->lighthouse_mobile_performance ); ?></p>
						<?php endif; ?>
						<?php if ( $obj->domain->lh_desktop && isset( $u->lighthouse_desktop_performance ) ): ?>
							<p><em aria-label="Desktop" class="material-icons small <?php echo esc_html( $u->lighthouse_desktop_performance > 89 ? 'green' : ( $u->lighthouse_desktop_performance > 49 ? 'amber' : 'red' ) ); ?>">computer</em> <?php echo esc_html( $u->lighthouse_desktop_performance ); ?></p>
						<?php endif; ?>
					<?php endif; ?>
				<?php endif; ?>
				<td class="mdc-data-table__cell mdc-data-table__cell--numeric" scope="row">
					<em aria-label="<?php echo esc_html( $u->status === 200 ? 'Accessed successfully' : 'Access unsuccessful' ); ?>" class="material-icons small <?php echo esc_html( $u->status === 200 ? 'green' : 'amber' ); ?>"><?php echo esc_html( $u->status === 200 ? 'check_circle_outline' : 'error' ); ?></em> <?php echo absint( $u->status ); ?>
				<td class="mdc-data-table__cell mdc-data-table__cell--numeric" scope="row">
				<?php if ( $u->format === 'HTML' ): ?>
					<em aria-label="Web page" class="material-icons">html</em>
				<?php else: ?>
					<em aria-label="PDF" class="material-icons">picture_as_pdf</em>
				<?php endif; ?>
				</td>
				<td class="mdc-data-table__cell" scope="row"><?php echo esc_html( $u->title ); ?></td>
				<td class="mdc-data-table__cell sticky-action" scope="row"><a class="mdc-icon-button material-icons" data-tooltip-id="tt-report-<?php echo absint( $i ); ?>" data-hide-tooltip-from-screenreader="true" data-url="<?php echo esc_url( $u->url ); ?>" data-path="<?php echo esc_url( $u->path ); ?>" data-referer="<?php echo esc_html( $u->referer ); ?>" data-format="<?php echo esc_url( $u->format) ; ?>" data-errors="<?php echo absint( $u->format === 'HTML' ? $u->achecker_errors : $u->tingtun_errors ); ?>" data-lighthouse="<?php echo absint( intval( $u->lighthouse_mobile_accessibility ) + intval( $u->lighthouse_desktop_accessibility ) + intval( $u->lighthouse_mobile_best_practice ) + intval( $u->lighthouse_desktop_best_practice ) + intval( $u->lighthouse_mobile_seo ) + intval( $u->lighthouse_desktop_seo ) + intval( $u->lighthouse_mobile_performance ) + intval( $u->lighthouse_desktop_performance ) ); ?>" data-updated="<?php echo esc_html( $u->updated ); ?>" onclick="return viewReport(this);" target="_blank" href="https://achecks.org/url/<?php echo esc_url( $u->id ); ?>">accessibility_new</a></td>
			</tr>
			<?php if ( $i === 19 ) { break; } ?>
		<?php endforeach; ?>
		</tbody>
	</table>
</div></div></div>
<div class="mdc-dialog mdc-dialog--fullscreen mdc-dialog--scrollable mdc-dialog-scroll-divider-footer">
	<div class="mdc-dialog__container">
		<div tabindex="0" aria-hidden="true" class="mdc-dom-focus-sentinel"></div>
		<div class="mdc-dialog__surface" role="dialog" aria-modal="true">
			<div class="mdc-dialog__header">
				<h1 class="mdc-dialog__title">Report</h1><button class="mdc-icon-button material-icons mdc-dialog__close" data-mdc-dialog-action="close">close</button>
			</div>
			<div class="mdc-dialog__content">
				<div id="report-tabs" class="mdc-tab-bar" role="tablist"><div class="mdc-tab-scroller"><div class="mdc-tab-scroller__scroll-area"><div class="mdc-tab-scroller__scroll-content"><button id="report-tab-summary" class="mdc-tab mdc-tab--active" role="tab" aria-selected="true" tabindex="0"><span class="mdc-tab__content"><span class="mdc-tab__text-label">Summary</span></span><span class="mdc-tab-indicator mdc-tab-indicator--active"><span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span></span><span class="mdc-tab__ripple"></span></button><button id="report-tab-tingtun" class="mdc-tab mdc-tab" role="tab" aria-selected="true" tabindex="0"><span class="mdc-tab__content"><span class="mdc-tab__text-label">Tingtun</span></span><span class="mdc-tab-indicator mdc-tab-indicator"><span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span></span><span class="mdc-tab__ripple"></span></button><button id="report-tab-achecker" class="mdc-tab mdc-tab" role="tab" aria-selected="true" tabindex="0"><span class="mdc-tab__content"><span class="mdc-tab__text-label">AChecker</span></span><span class="mdc-tab-indicator mdc-tab-indicator"><span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span></span><span class="mdc-tab__ripple"></span></button><button id="report-tab-lighthouse" class="mdc-tab mdc-tab" role="tab" aria-selected="true" tabindex="0"><span class="mdc-tab__content"><span class="mdc-tab__text-label">Lighthouse</span></span><span class="mdc-tab-indicator mdc-tab-indicator"><span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span></span><span class="mdc-tab__ripple"></span></button></div></div></div></div><div class="content content--active report-tabs-content">
				<table class="mdc-data-table__table" aria-label="Report Summary">
					<tbody class="mdc-data-table__content">
						<tr class="mdc-data-table__row">
							<td class="mdc-data-table__cell mdc-data-table__cell" scope="row"><strong>Status</strong></td>
							<td class="mdc-data-table__cell mdc-data-table__cell--numeric" scope="row" id="report-status"></td>
						</tr>
						<tr class="mdc-data-table__row">
							<td class="mdc-data-table__cell mdc-data-table__cell" scope="row"><strong>Errors</strong></td>
							<td class="mdc-data-table__cell mdc-data-table__cell--numeric" scope="row" id="report-errors"></td>
						</tr>
						<tr class="mdc-data-table__row">
							<td class="mdc-data-table__cell mdc-data-table__cell" scope="row"><strong>Link</strong></td>
							<td class="mdc-data-table__cell mdc-data-table__cell--numeric" scope="row" id="report-link"></td>
						</tr>
						<tr class="mdc-data-table__row">
							<td class="mdc-data-table__cell mdc-data-table__cell" scope="row"><strong>Title</strong></td>
							<td class="mdc-data-table__cell mdc-data-table__cell--numeric" scope="row" id="report-title"></td>
						</tr>
						<tr class="mdc-data-table__row">
							<td class="mdc-data-table__cell mdc-data-table__cell" scope="row"><strong>Referer</strong></td>
							<td class="mdc-data-table__cell mdc-data-table__cell--numeric" scope="row" id="report-referer"></td>
						</tr>
						<tr class="mdc-data-table__row">
							<td class="mdc-data-table__cell mdc-data-table__cell" scope="row"><strong>Updated</strong></td>
							<td class="mdc-data-table__cell mdc-data-table__cell--numeric" scope="row" id="report-updated"></td>
						</tr>
					</tbody>
				</table>
</div><div class="content report-tabs-content report-tab-tingtun"></div><div class="content report-tabs-content mdc-layout-grid"><div class="report-tab-achecker mdc-layout-grid__inner"></div></div><div class="content report-tabs-content report-tab-lighthouse">
				<div id="lh-tabs" class="mdc-tab-bar" role="tablist"><div class="mdc-tab-scroller"><div class="mdc-tab-scroller__scroll-area"><div class="mdc-tab-scroller__scroll-content"><button id="lh-tab-mobile" class="mdc-tab mdc-tab--active" role="tab" aria-selected="true" tabindex="0"><span class="mdc-tab__content"><span class="mdc-tab__text-label">Mobile</span></span><span class="mdc-tab-indicator mdc-tab-indicator--active"><span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span></span><span class="mdc-tab__ripple"></span></button><button id="lh-tab-desktop" class="mdc-tab mdc-tab" role="tab" aria-selected="true" tabindex="0"><span class="mdc-tab__content"><span class="mdc-tab__text-label">Desktop</span></span><span class="mdc-tab-indicator mdc-tab-indicator"><span class="mdc-tab-indicator__content mdc-tab-indicator__content--underline"></span></span><span class="mdc-tab__ripple"></span></button></div></div></div></div><div class="content content--active lh-tabs-content lh-tab-mobile"><iframe frameborder="0" width="100%"></iframe></div><div class="content lh-tabs-content lh-tab-desktop"><iframe frameborder="0" width="100%"></iframe></div></div>
			</div>
			<div class="mdc-dialog__actions"><button type="button" class="mdc-button mdc-dialog__button mdc-ripple-upgraded" data-mdc-dialog-action="ok"><div class="mdc-button__ripple"></div><span class="mdc-button__label">OK</span></button></div></div><div tabindex="0" aria-hidden="true" class="mdc-dom-focus-sentinel"></div>
	</div>
	<div class="mdc-dialog__scrim"></div>
</div>
<?php if ( count( $obj->urls ) > 20 || $page > 0 ): ?>
	<div class="mdc-data-table__pagination">
		<div class="mdc-data-table__pagination-trailing">
			<div class="mdc-data-table__pagination-navigation">
			<?php if ( $page > 0 ): ?>
				<a class="mdc-icon-button material-icons mdc-data-table__pagination-button" data-prev-page="true" href="<?php echo esc_url( $prev_link ); ?>"><div class="mdc-button__icon">chevron_left</div></a>
			<?php endif; ?>
			<?php if ( count( $obj->urls ) > 20 ): ?>
				<a class="mdc-icon-button material-icons mdc-data-table__pagination-button" data-next-page="true" href="<?php echo esc_url( $next_link ); ?>"><div class="mdc-button__icon">chevron_right</div></a>
			<?php endif; ?>
			</div>
		</div>
	</div>
	<?php endif;
		add_action( 'admin_footer', function() use ($obj)
		{
			foreach ( $obj->urls as $i => $u )
			{ ?>
				<div id="tt-url-<?php echo absint( $i ); ?>" class="mdc-tooltip" role="tooltip" aria-hidden="true"><div class="mdc-tooltip__surface mdc-tooltip__surface-animation"><?php echo esc_url( $u->url ); ?></div></div>
				<div id="tt-report-<?php echo absint( $i ); ?>" class="mdc-tooltip" role="tooltip" aria-hidden="true"><div class="mdc-tooltip__surface mdc-tooltip__surface-animation">Report</div></div>
				<?php if ( $i === 19 ) { break; }
			}
		} );
	endif; ?>
	</div>
	<div class="content main-tabs-content"><div class="body-content"><h1 class="mdc-typography--headline6">Frequently Asked Questions</h2><p class="mdc-typography--body1"><strong>Q:</strong> Can I setup ACHECKS on a local development environment, or websites/webpages that are not public?</p><p class="mdc-typography--body1"><strong>A:</strong> No, ACHECKS runs scans from external servers that require public access to your website.</p><hr><p class="mdc-typography--body1"><strong>Q:</strong> How long after I sign up will I get my first results?</p><p class="mdc-typography--body1"><strong>A:</strong> It may take up to an hour to start the initial scan of your website. How long it takes to complete depends on the size of your website. We cap it to 1000 pages however we can extend this upon request and depending on how many total pages are required to be indexed.</p><hr><p class="mdc-typography--body1"><strong>Q:</strong> What if I am running my website behind a firewall that restricts automated checks?</p><p class="mdc-typography--body1"><strong>A:</strong> You will need to whitelist our originating IP addresses in your firewall service. Registered users will find a link to an updated list in the <a href="https://achecks.org/domains#hdrbtn">ACHECKS user menu</a>.</p><hr><p class="mdc-typography--body1"><strong>Q:</strong> Can I cancel my service at any time?</p><p class="mdc-typography--body1"><strong>A:</strong> Yes, ACHECKS is a prepaid subscription service that automatically renews at the end of the period. You can cancel your service at any time before the renewal date to avoid charges in the subsequent period. You will continue to have access to your reports after cancellation until the subscription expires.</p><hr><p class="mdc-typography--body1"><strong>Q:</strong> I am having some issues that are not addressed here, where can I get some help?</p><p class="mdc-typography--body1"><strong>A:</strong> Sorry to hear, our team will be happy to look into it if you send us a message through <a href="https://www.achecks.org/support/" target="_blank">our support page</a>. Our support staff work in the 9-5 Eastern Time, Monday to Friday and will connect with you to resolve the issue at the earliest convenience.</p></div></div>
	<div class="content main-tabs-content"><div class="body-content"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1269.3 322.42" aria-hidden="true"><defs><style>.cls-1{fill:#fff;}.cls-2{fill:#27aae1;opacity:.2;}</style></defs><path class="cls-2" d="M136.61,67.91c9.9-6.73,15.09-18.7,24.57-26.02,6.32-4.88,14.15-7.36,21.93-9.16,18.66-4.31,41.57-3.67,52.84,11.82,10.82,14.86,5.38,35.98-3.67,51.97-9.06,15.99-21.54,30.83-24.53,48.97-6.05,36.71,28.73,70.09,25.53,107.16-.42,4.81-1.51,9.64-4,13.78-3.82,6.37-10.5,10.45-17.24,13.57-32.59,15.07-72.83,12.17-102.92-7.42-30.09-19.59-49.03-55.22-48.44-91.12,.27-16.37,4.28-32.98,.53-48.92-4.82-20.52-24.78-44.12,3.15-55.03,22.93-8.96,51.21,4.71,72.26-9.59Z"/><path class="cls-2" d="M449.68,78.63l-70.85-1.83c-7.07-.18-14.41-.32-20.88,2.54-12.53,5.53-18.01,20.53-18.74,34.21-.73,13.68,1.93,27.52-.31,41.03-2.35,14.21-9.91,26.91-15.48,40.19s-9.15,28.66-3.4,41.86c4.7,10.79,15.03,18.31,26.11,22.29,11.07,3.98,22.98,4.88,34.72,5.75,21.39,1.58,42.79,3.15,64.18,4.73,16.82,1.24,34.09,2.43,50.23-2.45,5.78-1.75,11.5-4.39,15.52-8.91,4.28-4.82,6.2-11.27,7.41-17.6,5.09-26.75-.54-54.7-11.51-79.62-10.97-24.92-27.03-47.23-43.61-68.83-11.29-14.71-24.54-30.21-42.81-33.35-21.88-3.76-42.02,11.46-59.01,25.75"/><path class="cls-2" d="M599.34,84.02c-17.17,13.07-22.75,36.04-27.21,57.16-2.2,10.44-4.41,21.17-2.68,31.7,2.95,17.86,16.59,31.87,30.33,43.65,18.44,15.81,38.29,29.91,58.79,42.94,8.74,5.56,18.22,11.11,28.58,11.02,16.19-.13,29.51-14.7,33.14-30.48,3.63-15.78-.36-32.29-5.59-47.61s-11.77-30.45-13.41-46.56c-1.02-10-.13-20.25-2.69-29.97-11.05-42.04-65.49-57.53-99.25-31.84Z"/><path class="cls-2" d="M896.98,65.71c-23.01,1.88-46.59-15.16-67.93-6.36-14.5,5.98-22.26,22.58-22.64,38.26s5.13,30.82,10.31,45.62c5.18,14.8,10.17,30.23,8.69,45.85-1.37,14.43-8.24,28.21-7.56,42.68,.22,4.68,1.3,9.45,4.06,13.23,4.2,5.77,11.48,8.29,18.38,10.11,25.73,6.79,52.81,8.39,79.15,4.68,6.96-.98,14.08-2.41,19.92-6.33,11.4-7.65,15.07-22.56,16.84-36.18,5.56-42.71,1.23-86.67-12.55-127.47-2.65-7.84-5.74-15.73-11.12-22.01-9.43-11-24.84-15.48-39.31-14.7-14.47,.78-28.28,6.16-41.5,12.1"/><path class="cls-2" d="M1136.99,37.38c-17.29,2.53-33.23,13.12-42.25,28.09-5.22,8.66-8.17,18.55-13.63,27.06-13.95,21.75-42.98,32.72-51,57.29-4.99,15.29-.1,32.49,9.31,45.53s22.82,22.59,36.42,31.17c25.07,15.81,51.68,29.2,79.33,39.89,18.91,7.32,42.37,12.69,57.8-.47,7.98-6.8,11.89-17.37,13.03-27.8,3.96-36.5-22.07-71.31-18.08-107.81,1.46-13.34,6.92-26.51,4.77-39.76-5.12-31.57-45.21-57.65-75.69-53.2Z"/><path d="M293.22,184.43c-4.58-10.3-5.4-21.05-3.81-32.11,.87-6.03,2.67-11.76,4.87-17.39,.84-2.15,1.17-2.34,3.46-2.17,.81,.06,1.63,.14,2.44,.16,10.36,.23,20.72,.43,31.08,.71,1.27,.03,2.14-.33,3.01-1.2,6.18-6.18,12.36-12.35,18.6-18.45,6.83-6.68,13.95-13.04,21.5-18.9,2.83-2.2,5.48-4.63,8.31-6.83,5.54-4.31,11.05-8.68,17.7-11.23,.53-.2,1.06-.44,1.61-.57,1.13-.26,2,.15,2.2,1.33,.23,1.36,.36,2.74,.37,4.12,.05,3.59,0,7.17,.02,10.76,.03,6.94,.03,13.88,.13,20.82,.06,4.57,.26,9.14,.33,11.75,0,11.19-.11,20.41,.04,29.64,.11,6.93,.65,13.86,.79,20.8,.11,5.54,.03,11.1-.18,16.64-.2,5.24,.15,10.43,.58,15.64,.45,5.44,.76,10.91,.91,16.37,.14,5.05-.04,10.11-.01,15.17,.01,2.44-1.03,3.39-3.34,2.48-2.34-.93-4.71-1.97-6.75-3.4-6.88-4.83-13.72-9.72-20.38-14.84-11.13-8.55-22.11-17.29-33.14-25.97-1.47-1.16-2.88-2.41-4.25-3.69-.82-.77-1.65-1.11-2.82-1.13-7.66-.14-15.32-.38-22.98-.6-4.57-.13-9.13-.37-13.7-.43-1.62-.02-2.91-.36-3.7-1.82"/><path d="M399.42,231.36c-.12-2.2-.22-3.95-.3-5.71-.38-8.56-.89-17.12-1.1-25.69-.3-12.08-.39-24.17-.56-36.25-.15-10.7-.52-21.39-.38-32.09,.15-11.84,.81-23.66,1.17-35.5,.1-3.18-.1-6.36-.2-9.54-.02-.53-.19-1.06-.34-1.8-.58,.34-1.01,.55-1.39,.81-5.24,3.63-10.21,7.59-14.76,12.07-3.43,3.37-6.97,6.65-10.52,9.9-4.33,3.97-8.72,7.88-13.1,11.79-2.25,2.01-4.51,4.01-6.8,5.98-3.72,3.19-7.28,6.59-11.24,9.44-3.89,2.8-4.79,6.35-4.41,10.8,.69,8.05,1.01,16.13,1.71,24.18,.4,4.63,1.23,9.22,1.87,13.82,.2,1.42,.86,2.56,1.88,3.61,6.06,6.23,13.1,11.29,19.92,16.6,3.67,2.86,7.39,5.65,11.16,8.39,4.82,3.5,9.69,6.93,14.57,10.35,3.67,2.58,7.38,5.1,11.07,7.65,.44,.31,.9,.6,1.77,1.18Z"/><path d="M328.93,139.99c-.69-.05-1.16-.1-1.63-.13-3.74-.18-7.48-.34-11.22-.53-4.88-.24-9.76-.54-14.65-.71-2.05-.07-2.11,.06-2.81,1.98-.45,1.22-.84,2.47-1.2,3.72-2.36,8.38-2.55,16.93-1.39,25.47,.65,4.82,1.6,9.64,3.63,14.17,.55,1.23,1.22,1.73,2.55,1.74,9.13,.06,18.25,.19,27.37,.27,1.29,.01,2.58-.11,4.21-.18-3.61-15.12-5.24-30.21-4.86-45.8Z"/><path d="M452.1,106.05c1.27-1.01,2.39-1.93,3.53-2.82,.78-.61,1.53-.25,2.28,.13,2.87,1.45,5.4,3.35,7.68,5.61,5.38,5.33,8.97,11.78,11.6,18.81,3.05,8.17,4.62,16.66,5.23,25.33,.35,5.04,.78,10.11,.62,15.15-.15,5.12-.91,10.22-1.53,15.31-.3,2.42-.97,4.8-1.33,7.21-.57,3.75-1.95,7.22-3.33,10.72-1.71,4.33-3.44,8.62-5.91,12.6-2.53,4.07-5.7,7.54-9.29,10.66-.17,.15-.42,.22-.81,.4-1.39-1.41-2.8-2.84-4.37-4.44,.58-.89,1.12-1.78,1.71-2.62,2.78-3.93,5.63-7.81,8.36-11.77,3.14-4.57,4.95-9.74,6.25-15.07,1.42-5.86,2.86-11.74,3.79-17.69,.63-4,.73-8.15,.45-12.19-.54-7.97-1.4-15.93-3.87-23.61-2.74-8.53-6.06-16.77-12.17-23.5-2.34-2.58-5.1-4.78-7.67-7.15-.34-.31-.7-.61-1.23-1.08Z"/><path d="M441.8,205.46c-1.6-1.71-2.92-3.13-4.47-4.78,.79-1.07,1.6-2.25,2.51-3.36,5.51-6.7,9.45-14.24,12.12-22.47,.92-2.82,1.35-5.67,1.27-8.66-.14-5.22-.1-10.46-1.2-15.59-2.3-10.7-7.61-19.69-15.68-27.05-.6-.55-1.21-1.1-1.8-1.66-.11-.11-.17-.27-.41-.68,.63-.5,1.28-1.05,1.96-1.57,.64-.49,1.32-.94,2.09-1.49,4.22,2.17,7.77,5.08,10.57,8.88,4.11,5.59,7.09,11.71,8.5,18.54,1.65,8.04,2.49,16.17,.96,24.3-2.1,11.21-6.63,21.46-13.02,30.88-1,1.47-2.08,2.88-3.39,4.69Z"/><path d="M416.02,135.66c1.4-1.61,2.6-2.99,3.86-4.45,2.35,1.16,4.44,2.48,5.96,4.37,2.51,3.1,4.91,6.3,7.09,9.63,1.61,2.47,2.37,5.33,2.76,8.29,.77,5.89,.73,11.69-1.15,17.41-1.88,5.73-3.61,11.52-6.3,16.95-.52,1.06-1.13,2.08-1.92,3.52-1.48-1.54-2.73-2.68-3.74-4-.32-.41-.15-1.4,.09-2,.89-2.28,1.97-4.48,2.86-6.75,2.57-6.59,4.15-13.39,4.07-20.51-.04-3.86-.65-7.63-2.14-11.23-1.82-4.39-4.76-7.75-9.06-9.85-.71-.35-1.37-.79-2.39-1.38Z"/><path class="cls-1" d="M399.42,231.36c-.87-.58-1.33-.87-1.77-1.18-3.69-2.55-7.4-5.07-11.07-7.65-4.87-3.42-9.75-6.85-14.57-10.35-3.76-2.73-7.48-5.53-11.16-8.39-6.83-5.31-13.86-10.37-19.92-16.6-1.02-1.05-1.68-2.19-1.88-3.61-.64-4.61-1.47-9.2-1.87-13.82-.7-8.05-1.02-16.13-1.71-24.18-.38-4.45,.52-8,4.41-10.8,3.96-2.85,7.53-6.25,11.24-9.44,2.29-1.97,4.55-3.97,6.8-5.98,4.38-3.92,8.77-7.82,13.1-11.79,3.55-3.25,7.08-6.53,10.52-9.9,4.56-4.48,9.52-8.44,14.76-12.07,.38-.27,.81-.47,1.39-.81,.14,.74,.32,1.27,.34,1.8,.09,3.18,.29,6.37,.2,9.54-.35,11.83-1.01,23.66-1.17,35.5-.14,10.69,.23,21.39,.38,32.09,.17,12.08,.26,24.17,.56,36.25,.21,8.57,.72,17.13,1.1,25.69,.08,1.76,.18,3.51,.3,5.71Z"/><path class="cls-1" d="M328.93,139.99c-.38,15.59,1.25,30.68,4.86,45.8-1.63,.07-2.92,.19-4.21,.18-9.13-.08-18.25-.21-27.37-.27-1.33,0-2-.5-2.55-1.74-2.03-4.52-2.98-9.34-3.63-14.17-1.15-8.54-.97-17.08,1.39-25.47,.35-1.25,.75-2.5,1.2-3.72,.7-1.92,.76-2.05,2.81-1.98,4.89,.17,9.77,.47,14.65,.71,3.74,.18,7.48,.35,11.22,.53,.47,.02,.94,.07,1.63,.13Z"/><path d="M631.58,187.67c-8.77-.24-16.05-1.04-23.23-2.72-8.36-1.96-16.04-5.51-23.3-9.97-7.72-4.75-15.34-9.67-21.74-16.19-2.8-2.85-5.42-5.87-8.1-8.83-1.04-1.15-1.23-2.48-.37-3.83,.93-1.44,1.81-2.93,2.89-4.24,3.58-4.35,7.08-8.77,10.91-12.89,3.79-4.08,8.28-7.43,13.19-10.07,6.54-3.51,13.18-6.84,20.29-9.1,9.35-2.98,18.93-4.17,28.73-3.53,13.9,.92,27.24,4.16,39.9,9.93,6.92,3.16,13.24,7.4,19.32,11.97,3.07,2.3,6.09,4.67,9.2,6.92,6.19,4.46,11.6,9.78,16.8,15.31,1.1,1.17,1.16,1.59,0,2.66-2.81,2.6-5.52,5.37-8.62,7.58-10.25,7.31-21.04,13.71-32.65,18.67-6.86,2.93-13.9,5.43-21.23,6.53-7.72,1.16-15.52,2.29-21.98,1.8Zm-69.84-39.74c3.14,2.29,6.21,4.56,9.31,6.8,2.25,1.62,4.58,3.12,6.79,4.8,6.64,5.02,13.21,10.13,20.88,13.54,1.79,.79,3.62,1.49,5.38,2.35,3.91,1.91,8.08,3.05,12.27,4.08,1.63,.4,3.31,.56,5.47,.91-.57-.61-.75-.87-.99-1.04-5.34-3.78-8.64-9.03-10.77-15.1-2.99-8.53-3.38-17.25-1.61-26.07,1.74-8.66,5.24-16.45,11.62-22.77,.15-.14,.17-.4,.3-.73-3.18,.38-6.26,.64-9.3,1.12-10.53,1.66-20.27,5.48-29.34,11.03-2.22,1.36-4.4,2.82-6.43,4.44-5.65,4.49-9.14,10.76-13.58,16.64Zm146.71,3.45c-2.65-3.73-21.82-18.91-28.9-22.48-8.61-4.34-17.09-9.05-26.97-11.7,.46,.8,.67,1.13,.84,1.47,1.49,2.98,3.34,5.84,4.4,8.97,3.69,10.9,3.28,21.85-.46,32.66-2.82,8.15-7.38,15.06-14.93,19.67-.19,.12-.3,.35-.59,.7,8.32-.93,16.05-3.41,23.76-5.98,3.4-1.13,6.76-2.36,10.11-3.64,1.89-.72,3.78-1.51,5.54-2.5,3.48-1.95,6.89-4.01,10.28-6.1,4.3-2.65,8.56-5.36,12.8-8.1,1.43-.92,2.76-1.99,4.13-2.98Zm-68.1-6.99c0-2.04-.57-3.86-1.47-5.66-.99-1.98-2.57-2.93-4.64-3.09-2.14-.17-3.63,1.09-4.44,2.88-.99,2.22-1.72,4.56-2.46,6.88-1.34,4.2-.4,8.04,1.97,11.64,1.86,2.83,4.03,3.07,6.37,.68,3.65-3.73,3.82-8.65,4.67-13.33Z"/><path class="cls-1" d="M561.74,147.93c4.44-5.88,7.93-12.16,13.58-16.64,2.04-1.62,4.21-3.08,6.43-4.44,9.07-5.55,18.81-9.37,29.34-11.03,3.04-.48,6.12-.74,9.3-1.12-.13,.33-.16,.59-.3,.73-6.38,6.31-9.89,14.1-11.62,22.77-1.77,8.82-1.38,17.54,1.61,26.07,2.12,6.06,5.43,11.31,10.77,15.1,.24,.17,.42,.43,.99,1.04-2.16-.35-3.84-.51-5.47-.91-4.2-1.02-8.36-2.17-12.27-4.08-1.76-.85-3.59-1.55-5.38-2.35-7.67-3.41-14.25-8.52-20.88-13.54-2.21-1.67-4.54-3.17-6.79-4.8-3.1-2.23-6.17-4.5-9.31-6.8Z"/><path class="cls-1" d="M708.45,151.39c-1.37,1-2.7,2.06-4.13,2.98-4.24,2.73-8.51,5.44-12.8,8.1-3.39,2.09-6.81,4.15-10.28,6.1-1.76,.99-3.65,1.78-5.54,2.5-3.34,1.28-6.71,2.51-10.11,3.64-7.7,2.57-15.44,5.05-23.76,5.98,.29-.35,.4-.58,.59-.7,7.55-4.61,12.11-11.52,14.93-19.67,3.74-10.81,4.15-21.77,.46-32.66-1.06-3.13-2.91-5.99-4.4-8.97-.17-.35-.38-.68-.84-1.47,9.89,2.65,18.37,7.35,26.97,11.7,7.08,3.57,26.25,18.76,28.9,22.48Z"/><path class="cls-1" d="M640.35,144.4c-.85,4.68-1.02,9.6-4.67,13.33-2.34,2.39-4.51,2.14-6.37-.68-2.36-3.6-3.31-7.44-1.97-11.64,.74-2.32,1.46-4.66,2.46-6.88,.8-1.79,2.29-3.05,4.44-2.88,2.07,.16,3.65,1.11,4.64,3.09,.9,1.8,1.47,3.62,1.47,5.66Z"/><path d="M929.68,122.12c.05,12.83-3.79,26.83-10.55,40.18-5.2,10.28-12.61,18.85-20.29,27.26-2.02,2.21-4.15,4.32-6.25,6.46-5.63,5.78-8.4,12.87-9.42,20.81-.61,4.69-1.07,9.42-1.88,14.07-.59,3.36-1.49,6.7-2.53,9.95-1.63,5.07-5.91,8.85-11.12,9.97-4.79,1.03-9.64,1.47-14.55,.82-8.18-1.09-13.43-5.84-16.48-13.23-1.81-4.4-2.81-9.03-3.25-13.78-.35-3.74-.74-7.47-1.17-11.2-.52-4.49-1.74-8.8-3.5-12.96-1.35-3.19-3.25-6.02-5.9-8.32-1.72-1.5-3.26-3.23-5.05-4.63-6.42-5.01-9.93-11.8-11.93-19.47-.59-2.27-.98-4.65-1.02-6.99-.15-7.46,3.56-12.84,10.13-16.22,1.59-.82,2.68-2.35,3.02-4.11,.96-4.97,1.96-9.93,3.1-14.86,2.01-8.75,3.92-17.53,7.31-25.89,2.03-5,4.39-9.84,7.37-14.36,9.34-14.17,22.62-21.11,39.46-21.77,11.08-.44,21.22,2.54,30.45,8.36,7.19,4.54,12.91,10.6,17,18.24,4.96,9.26,6.73,19.17,7.05,31.67Zm-7.3,2.84c-.7-5.51-1.44-11.08-2.1-16.66-.67-5.68-2.69-10.88-5.35-15.88-2.13-4-5.08-7.39-7.98-10.79-2.59-3.05-5.63-5.76-9.69-6.76-6.32-1.57-12.58-3.11-19.21-3.37-5.15-.21-9.77,.66-14.51,2.36-5.64,2.03-10.97,4.56-16,7.82-4.22,2.74-7.37,6.42-9.77,10.76-1.58,2.85-2.97,5.81-4.3,8.79-.76,1.68-1.35,3.43-1.75,5.23-1.4,6.31-2.63,12.66-4.03,18.98-1.11,5.01-2.55,9.96-3.53,14.99-.91,4.68-2.93,8.32-7.47,10.2-.23,.1-.59,.29-.8,.43-1.89,1.38-3.59,2.98-3.78,5.45-.17,2.17-.21,4.39,.06,6.55,.6,4.91,1.8,9.74,5,13.61,4.31,5.21,8.91,10.19,13.51,15.15,2.21,2.39,4.08,4.95,4.84,8.09,1.09,4.51,1.98,9.07,2.85,13.62,1,5.2,1.82,10.44,2.84,15.63,1.12,5.7,4.34,10.12,8.83,13.68,1.2,.96,2.35,1.91,3.89,2.43,4.14,1.4,8.01,.74,11.86-1.02,3.21-1.47,5.67-3.65,6.61-7.1,.98-3.61,1.84-7.27,2.45-10.96,.59-3.53,.7-7.14,1.19-10.69,.39-2.82,1.26-5.59,1.53-8.42,.45-4.65,2.6-8.33,5.75-11.58,2.44-2.52,4.81-5.1,7.26-7.61,2.96-3.04,6.04-5.95,8.93-9.06,2.54-2.74,5.12-5.51,7.25-8.56,2.8-4,5.35-8.2,7.68-12.5,2.32-4.28,4.81-8.54,5.43-13.53,.25-2.02,.67-4.03,.92-6.05,.55-4.36,1.04-8.73,1.57-13.24Z"/><path d="M947.19,134.3c-.22-10.12-.66-20.95-3.66-31.52-5.33-18.76-16.48-33.17-32.59-43.8-6.29-4.16-13.38-5.94-21.01-4.83-1.73,.25-2.65-.27-2.88-1.49-.24-1.26,.35-2.37,1.79-2.79,1.32-.39,2.71-.67,4.08-.72,6.14-.21,12.21,.32,18.19,1.88,4.42,1.16,7.97,3.65,11.31,6.58,2.08,1.83,4.15,3.68,6.23,5.53,.26,.23,.64,.65,.86,.93,3.15,4.13,6.86,7.83,9.45,12.39,.74,1.32,1.57,2.58,2.47,3.8,2.9,3.88,4.84,8.22,5.98,12.9,1.48,6.1,2.62,12.3,4.32,18.33,1.79,6.34,1.85,12.74,1.53,19.19-.25,5.13-.68,10.26-1.1,15.38-.13,1.61-.29,3.29-2.35,3.91-1.29,.39-2.6-.36-2.88-1.68-.17-.8-.14-1.65-.12-2.47,.1-3.59,.24-7.18,.39-11.52Z"/><path class="cls-1" d="M922.38,124.96c-.53,4.51-1.02,8.88-1.57,13.24-.25,2.02-.67,4.03-.92,6.05-.62,4.99-3.11,9.25-5.43,13.53-2.33,4.29-4.88,8.49-7.68,12.5-2.13,3.05-4.71,5.82-7.25,8.56-2.88,3.11-5.97,6.02-8.93,9.06-2.45,2.51-4.82,5.09-7.26,7.61-3.15,3.25-5.3,6.93-5.75,11.58-.27,2.83-1.14,5.59-1.53,8.42-.49,3.55-.6,7.16-1.19,10.69-.61,3.69-1.47,7.35-2.45,10.96-.94,3.45-3.4,5.63-6.61,7.1-3.85,1.76-7.72,2.43-11.86,1.02-1.54-.52-2.69-1.48-3.89-2.43-4.49-3.56-7.71-7.98-8.83-13.68-1.02-5.2-1.84-10.43-2.84-15.63-.87-4.56-1.76-9.12-2.85-13.62-.76-3.15-2.63-5.71-4.84-8.09-4.6-4.96-9.19-9.94-13.51-15.15-3.2-3.87-4.4-8.7-5-13.61-.26-2.15-.23-4.38-.06-6.55,.19-2.47,1.89-4.07,3.78-5.45,.2-.15,.57-.34,.8-.43,4.53-1.88,6.55-5.52,7.47-10.2,.98-5.04,2.42-9.98,3.53-14.99,1.4-6.31,2.63-12.66,4.03-18.98,.4-1.8,.99-3.55,1.75-5.23,1.34-2.97,2.73-5.93,4.3-8.79,2.4-4.34,5.55-8.02,9.77-10.76,5.03-3.25,10.35-5.79,16-7.82,4.74-1.71,9.36-2.57,14.51-2.36,6.63,.26,12.89,1.81,19.21,3.37,4.06,1.01,7.1,3.72,9.69,6.76,2.89,3.4,5.85,6.79,7.98,10.79,2.66,5.01,4.68,10.2,5.35,15.88,.66,5.58,1.4,11.15,2.1,16.66Zm-13.47-6.23c.02-5.09-1.19-10.33-3.09-15.4-1.72-4.58-4.47-8.47-8.29-11.62-7.96-6.56-17.11-8.81-27.28-7.43-5.59,.76-10.91,3.03-15.32,6.56-6.49,5.21-10.95,11.82-12.93,20.01-.63,2.62-1.24,5.25-1.67,7.9-.52,3.22-.99,6.47-.26,9.73,.21,.95,.23,2.18,1.49,2.2,1.2,.02,1.15-1.24,1.3-2.05,1.61-8.39,4.53-16.31,8.12-24.05,2.2-4.73,5.34-8.37,9.62-11.41,5.61-3.98,11.76-4.92,18.32-4.81,2.54,.04,4.66,1.37,6.97,2.13,6.66,2.19,11.1,7,13.93,12.98,2.7,5.69,3.83,11.93,3.03,18.36-.7,5.69-1.36,11.37-4.61,16.32-1.74,2.66-3.4,5.39-5.39,7.86-2.45,3.03-5.63,5.01-9.45,6.21-4.84,1.52-9.75,1.82-14.73,1.86-7.4,.07-14.46,1.74-21.31,4.4-.57,.22-1.46,.69-1.96,1.06-4.57,3.32-7.81,7.49-8.38,13.3-.51,5.27,.1,10.13,5.01,13.38,.27,.18,.67,.53,.9,.77,2.17,2.35,5.14,3.11,8.04,3.99,1.41,.43,2.78,.14,3.99-.67,1.13-.77,1.21-2.25,.15-3.1-.73-.59-1.66-.94-2.48-1.44-2.65-1.6-5.36-3.1-7.9-4.86-2.24-1.56-3.34-3.98-2.88-6.64,.91-5.24,3.25-9.6,8.69-11.47,1.31-.45,2.65-.88,4.01-1.06,2.83-.38,5.68-.6,8.52-.85,5.36-.47,10.77-.65,16.08-1.45,7.52-1.13,13.82-4.76,18.78-10.58,4.48-5.26,7.22-11.41,9.19-17.96,1.16-3.85,1.99-7.75,1.79-12.18Z"/><path d="M908.9,118.72c.2,4.43-.63,8.33-1.79,12.18-1.97,6.55-4.71,12.7-9.19,17.96-4.96,5.83-11.26,9.46-18.78,10.58-5.31,.8-10.72,.97-16.08,1.45-2.84,.25-5.69,.47-8.52,.85-1.36,.18-2.7,.61-4.01,1.06-5.44,1.87-7.78,6.23-8.69,11.47-.46,2.65,.64,5.08,2.88,6.64,2.53,1.76,5.25,3.26,7.9,4.86,.82,.5,1.75,.85,2.48,1.44,1.06,.86,.98,2.34-.15,3.1-1.21,.82-2.58,1.1-3.99,.67-2.9-.89-5.88-1.64-8.04-3.99-.22-.24-.62-.59-.9-.77-4.91-3.25-5.52-8.11-5.01-13.38,.56-5.81,3.8-9.97,8.38-13.3,.5-.36,1.38-.83,1.96-1.06,6.85-2.66,13.92-4.33,21.31-4.4,4.98-.05,9.89-.34,14.73-1.86,3.82-1.2,7.01-3.18,9.45-6.21,1.99-2.47,3.65-5.2,5.39-7.86,3.25-4.95,3.91-10.63,4.61-16.32,.8-6.44-.34-12.68-3.03-18.36-2.84-5.98-7.28-10.79-13.93-12.98-2.31-.76-4.43-2.09-6.97-2.13-6.56-.11-12.71,.83-18.32,4.81-4.28,3.04-7.42,6.67-9.62,11.41-3.59,7.73-6.52,15.66-8.12,24.05-.16,.81-.11,2.07-1.3,2.05-1.26-.02-1.27-1.25-1.49-2.2-.74-3.27-.27-6.51,.26-9.73,.43-2.65,1.04-5.29,1.67-7.9,1.98-8.19,6.44-14.8,12.93-20.01,4.4-3.53,9.72-5.8,15.32-6.56,10.17-1.39,19.32,.87,27.28,7.43,3.82,3.15,6.58,7.04,8.29,11.62,1.9,5.07,3.12,10.31,3.09,15.4Z"/><path d="M1104.82,268.81c-4.65,0-9.3,.11-13.94-.04-2.84-.09-5.67-.55-8.5-.92-2.29-.3-3.69-1.43-4.47-3.82-1.38-4.25-3.28-8.33-4.85-12.53-2.22-5.96-4.52-11.9-6.45-17.96-1.75-5.51-3.09-11.16-4.54-16.77-1.88-7.26-3.74-14.53-5.5-21.82-.82-3.41-1.35-6.88-2.08-10.31-1.06-5.03-2.31-10.02-3.25-15.07-1.83-9.86-3.81-19.73-3.58-29.84,.13-5.82,1.28-11.5,2.9-17.08,2.49-8.54,6.36-16.44,11.23-23.83,4.9-7.45,11.28-13.55,18.32-18.92,7.21-5.51,14.86-10.42,23.39-13.65,8.33-3.16,16.88-5.6,25.82-6.39,9.81-.86,19.18,.52,28,5.16,5.81,3.05,11.39,6.37,15.98,11.16,2.38,2.49,4.5,5.18,6.1,8.24,3.02,5.79,6.19,11.53,7,18.17,.52,4.29,1.12,8.58,1.46,12.89,.35,4.39,.28,8.81,.65,13.2,.36,4.38,.87,8.76,1.61,13.1,.65,3.76,2.8,6.95,4.77,10.14,2.45,3.95,5.12,7.76,7.63,11.68,4.93,7.7,3.19,15.84-3.83,21.52-1.07,.86-2.34,1.48-3.56,2.14-3.89,2.14-6.08,5.35-6.2,9.84-.04,1.38,.04,2.77,.09,4.16,.28,7.75,.6,15.49,.83,23.24,.08,2.61-.04,5.2-1.4,7.61-.85,1.5-1.99,2.56-3.51,3.27-4.04,1.87-8.29,2.76-12.75,3.08-6.59,.47-13.15,1.22-19.72,1.95-3.32,.37-5.62,3.1-6,6.69-.44,4.2,.23,8.31,.93,12.41,.39,2.28,.06,2.5-2.13,3.49-5.24,2.38-10.93,2.47-16.4,3.67-7.94,1.74-16.03,1.89-24.08,2.14Zm-50.21-122.45c.06,.01,.13,.03,.19,.04,0,2.12-.16,4.26,.04,6.36,.21,2.18,.71,4.34,1.17,6.49,.62,2.95,1.39,5.87,1.96,8.83,1.63,8.49,3,17.03,4.82,25.48,2.65,12.27,6.37,24.26,10.1,36.25,.05,.16,.12,.3,.18,.45,2.65,7.02,5.31,14.03,7.96,21.05,.89,2.36,1.83,4.72,2.61,7.12,.66,2.05,1.96,3.02,4.09,3.1,4.64,.17,9.28,.58,13.92,.57,5.79-.01,11.58-.64,17.36-.47,6.47,.19,12.66-1.02,18.84-2.53,1.92-.47,2.04-.55,2.06-2.44,.03-3.67,.05-7.34-.16-11-.27-4.75,2.43-8.8,7.1-9.75,7.75-1.57,15.54-2.92,23.31-4.4,3.12-.59,6.26-1.12,9.31-1.96,3.05-.84,3.77-1.96,3.78-5.17,.02-4.81,.09-9.63-.14-14.43-.29-5.91,.16-11.74,1.04-17.56,.29-1.9,.84-3.68,2.22-5.07,2.41-2.43,4.62-5.14,7.36-7.11,3.74-2.69,5.32-8.82,2.18-13.02-.82-1.1-1.41-2.38-2.1-3.58-2.64-4.59-5.21-9.22-7.95-13.75-1.43-2.36-2.29-4.84-2.71-7.53-.9-5.67-1.24-11.35-.79-17.08,.04-.56,.01-1.15-.09-1.7-.71-4.01-1.39-8.03-2.18-12.02-1.09-5.52-2.13-11.05-3.45-16.51-1.81-7.47-6.08-13.48-11.91-18.35-4.08-3.41-8.41-6.49-13.78-7.62-1.28-.27-2.54-.59-3.8-.94-4.37-1.21-8.89-1.6-13.34-1.43-4.61,.18-9.25,.93-13.77,1.94-4.19,.93-8.32,2.33-12.33,3.89-5.93,2.3-11.69,5-17.11,8.39-2,1.25-4.27,2.2-5.99,3.76-4.77,4.33-9.66,8.59-13.87,13.44-7.72,8.89-12.04,19.53-14.01,31.06-.97,5.69-1.43,11.48-2.11,17.22Z"/><path d="M1125.1,287.6c-.34-.29-.75-.49-.73-.64,.04-.31,.3-.6,.48-.89,.27,.22,.62,.41,.77,.69,.07,.13-.27,.47-.52,.85Z"/><path class="cls-1" d="M1054.61,146.36c.69-5.74,1.14-11.53,2.11-17.22,1.97-11.54,6.29-22.18,14.01-31.06,4.21-4.85,9.1-9.12,13.87-13.44,1.72-1.56,3.99-2.51,5.99-3.76,5.41-3.39,11.18-6.09,17.11-8.39,4.01-1.56,8.14-2.95,12.33-3.89,4.52-1.01,9.16-1.76,13.77-1.94,4.45-.17,8.97,.23,13.34,1.43,1.26,.35,2.52,.67,3.8,.94,5.38,1.13,9.7,4.22,13.78,7.62,5.83,4.87,10.1,10.89,11.91,18.35,1.33,5.46,2.36,11,3.45,16.51,.79,4,1.47,8.01,2.18,12.02,.1,.56,.13,1.14,.09,1.7-.45,5.73-.11,11.42,.79,17.08,.43,2.69,1.28,5.17,2.71,7.53,2.74,4.53,5.31,9.16,7.95,13.75,.69,1.2,1.28,2.48,2.1,3.58,3.14,4.2,1.56,10.33-2.18,13.02-2.74,1.97-4.95,4.69-7.36,7.11-1.38,1.39-1.93,3.17-2.22,5.07-.88,5.82-1.32,11.66-1.04,17.56,.23,4.8,.15,9.62,.14,14.43-.01,3.2-.73,4.32-3.78,5.17-3.05,.84-6.2,1.37-9.31,1.96-7.77,1.48-15.56,2.83-23.31,4.4-4.66,.95-7.37,5-7.1,9.75,.21,3.66,.18,7.33,.16,11-.01,1.89-.13,1.97-2.06,2.44-6.19,1.5-12.38,2.72-18.84,2.53-5.78-.17-11.57,.46-17.36,.47-4.64,.01-9.28-.39-13.92-.57-2.13-.08-3.43-1.05-4.09-3.1-.78-2.4-1.71-4.75-2.61-7.12-2.65-7.02-5.3-14.03-7.96-21.05-.06-.15-.13-.3-.18-.45-3.73-11.98-7.45-23.97-10.1-36.25-1.83-8.45-3.19-16.99-4.82-25.48-.57-2.96-1.34-5.88-1.96-8.83-.45-2.15-.96-4.31-1.17-6.49-.2-2.1-.04-4.24-.04-6.36-.06-.01-.13-.03-.19-.04Zm62.76-55.49c-.49-.63-.82-1.08-1.18-1.49-1.92-2.16-4.04-4-6.86-4.94-5.66-1.88-10.64,0-14.8,3.48-4.64,3.88-7.3,9.14-7.58,15.38-.03,.71-.11,1.41-.17,2.27-1.14-.2-2.09-.35-3.04-.53-5.14-.94-9.19,.96-12.14,5.06-3.03,4.21-2.97,9.8-.16,14.04,.66,1,1.33,1.98,2.04,3.04-.66,.48-1.14,.77-1.56,1.14-3.71,3.27-6.28,8.64-4.29,14.34,.98,2.79,2.78,4.74,5.59,5.75,2.8,1,5.61,1.18,8.35-.08,3.01-1.38,5.95-2.93,9.25-4.57,3.98,4.89,9.85,6.51,16.14,7.35,6.92,.92,12-2.68,17.09-6.58,.96,1.13,1.84,2.19,2.75,3.22,1.27,1.43,2.78,2.48,4.64,3.04,5.97,1.79,11.28-.16,14.55-5.46,.88-1.43,1.65-2.94,2.66-4.77,.49,.65,.78,1.15,1.17,1.55,4.34,4.47,11.89,4.92,16.74,1.03,2.8-2.25,4.85-5.1,6.15-8.4,.55-1.39,.8-3.04,.68-4.53-.34-4.33-2.89-7.32-6.42-9.57-.73-.47-1.52-.84-2.27-1.24,.12-.53,.16-.86,.27-1.16,1.58-4.14,1.56-8.28,.11-12.45-1.56-4.51-4.81-8.12-9.86-8.12-1.92,0-3.84,0-5.84,0,0-.26,0-.66,0-1.06,.02-4.32-1.78-7.79-5.25-10.24-8.69-6.15-19.48-4-25.02,2.72-.5,.61-1.1,1.13-1.76,1.79Z"/><path d="M1117.37,90.87c.66-.66,1.26-1.19,1.76-1.79,5.54-6.71,16.34-8.87,25.02-2.72,3.47,2.46,5.27,5.92,5.25,10.24,0,.4,0,.8,0,1.06,2,0,3.92,0,5.84,0,5.05,0,8.29,3.61,9.86,8.12,1.45,4.17,1.47,8.31-.11,12.45-.11,.3-.15,.62-.27,1.16,.75,.41,1.54,.78,2.27,1.24,3.53,2.26,6.08,5.24,6.42,9.57,.12,1.49-.14,3.14-.68,4.53-1.3,3.31-3.34,6.15-6.15,8.4-4.85,3.89-12.4,3.44-16.74-1.03-.39-.4-.69-.9-1.17-1.55-1.02,1.83-1.78,3.34-2.66,4.77-3.27,5.3-8.58,7.26-14.55,5.46-1.87-.56-3.37-1.61-4.64-3.04-.92-1.03-1.79-2.1-2.75-3.22-5.09,3.9-10.18,7.5-17.09,6.58-6.3-.84-12.16-2.46-16.14-7.35-3.3,1.64-6.24,3.18-9.25,4.57-2.73,1.26-5.55,1.08-8.35,.08-2.81-1.01-4.62-2.95-5.59-5.75-1.99-5.69,.59-11.07,4.29-14.34,.42-.37,.9-.67,1.56-1.14-.71-1.06-1.38-2.04-2.04-3.04-2.81-4.24-2.87-9.83,.16-14.04,2.95-4.09,7-6,12.14-5.06,.95,.17,1.9,.33,3.04,.53,.07-.86,.14-1.56,.17-2.27,.28-6.24,2.94-11.5,7.58-15.38,4.16-3.48,9.14-5.36,14.8-3.48,2.82,.94,4.95,2.78,6.86,4.94,.37,.41,.69,.87,1.18,1.49Zm-3.55,12.87c-.22-.1-.43-.2-.65-.3-.06-.53-.15-1.07-.16-1.6-.03-1.47-.07-2.94,0-4.41,.06-1.39-.4-2.57-1.25-3.61-.93-1.13-1.88-2.26-2.89-3.32-1.87-1.96-4.04-2.24-6.47-1.08-4.26,2.03-7.02,5.51-9.2,9.52-1.83,3.37-2.36,6.94-.99,10.63,.89,2.39-1.08,3.51-2.9,2.93-1.55-.49-3.08-1.07-4.58-1.69-4-1.63-6.44-.79-8.7,2.84-1.28,2.06-1.52,4.12-.47,6.26,.64,1.31,1.44,2.54,2.24,3.76,.54,.81,1.28,1.49,1.8,2.32,.82,1.3,.72,2.17-.32,3.32-.49,.54-1.11,.96-1.66,1.44-1.17,1.02-2.44,1.94-3.48,3.07-1.9,2.06-2.31,6.02-1,8.27,1.08,1.87,4.02,2.62,7.04,2,2.89-.59,5.08-2.11,6.75-4.48,.14-.2,.27-.41,.42-.61,1.81-2.5,3.22-2.61,5.69-.66,2.36,1.87,4.76,3.72,7.24,5.42,2.58,1.77,5.5,2.4,8.62,2.04,4.56-.53,8.36-2.61,11.69-5.67,1.17-1.07,2.06-2.37,2.21-3.98,.28-2.98-.1-5.79-1.8-8.43-1.94-3-4.51-5.35-7.04-7.78-.43-.41-.81-.88-1.21-1.32,2.19,0,4.34-.02,6.13,.77,3.5,1.56,6.18,4.24,8.02,7.64,1.03,1.91,1.4,4.01,1.08,6.17-.26,1.77-.51,3.57-1.01,5.28-.35,1.21,0,1.92,.77,2.81,2.09,2.44,4.77,3.27,7.84,3.31,3.28,.04,5.71-1.38,7.04-4.33,1.07-2.36,1.88-4.85,2.71-7.31,.51-1.5,.84-3.08,2.09-4.65,.94,1.45,1.73,2.65,2.51,3.85,1.45,2.26,2.71,4.68,4.4,6.74,2.22,2.71,6.35,2.72,8.98,.44,2.09-1.82,3.38-4.16,4.63-6.55,1.31-2.52,.56-5.7-1.7-7.41-2.41-1.82-5.16-2.72-8.12-3.15-1.23-.18-2.6-.19-3.69-1.67,1.01-1,1.92-2,2.93-2.88,1.7-1.46,2.69-3.31,3-5.46,.85-5.96-3.83-9.86-9.09-8.26-2.95,.9-5.81,2.11-8.69,3.22-.7,.27-1.34,.69-2.03-.3,.12-.35,.21-.82,.43-1.22,.94-1.72,1.91-3.42,2.89-5.12,3.05-5.29-.47-11.68-6.16-12.01-.81-.05-1.61-.23-2.41-.39-1.72-.34-3.39-.26-5.06,.35-3.67,1.34-6.93,3.34-9.49,6.28-1.81,2.08-3.36,4.39-5.04,6.59-.61,.8-1.25,1.58-1.88,2.36Z"/><path class="cls-1" d="M1113.82,103.74c.63-.79,1.27-1.56,1.88-2.36,1.68-2.2,3.23-4.51,5.04-6.59,2.56-2.95,5.82-4.95,9.49-6.28,1.67-.61,3.34-.69,5.06-.35,.8,.16,1.6,.35,2.41,.39,5.69,.33,9.2,6.73,6.16,12.01-.98,1.7-1.95,3.4-2.89,5.12-.22,.4-.31,.88-.43,1.22,.69,1,1.33,.58,2.03,.3,2.88-1.11,5.74-2.32,8.69-3.22,5.26-1.6,9.94,2.29,9.09,8.26-.31,2.15-1.31,4-3,5.46-1.02,.88-1.93,1.88-2.93,2.88,1.09,1.47,2.46,1.49,3.69,1.67,2.96,.43,5.7,1.33,8.12,3.15,2.26,1.7,3.01,4.88,1.7,7.41-1.24,2.39-2.54,4.74-4.63,6.55-2.62,2.28-6.75,2.27-8.98-.44-1.69-2.06-2.95-4.48-4.4-6.74-.78-1.21-1.57-2.4-2.51-3.85-1.25,1.58-1.58,3.15-2.09,4.65-.84,2.46-1.65,4.95-2.71,7.31-1.33,2.95-3.76,4.37-7.04,4.33-3.08-.04-5.75-.87-7.84-3.31-.76-.89-1.12-1.6-.77-2.81,.49-1.72,.75-3.51,1.01-5.28,.31-2.16-.05-4.26-1.08-6.17-1.84-3.4-4.52-6.08-8.02-7.64-1.79-.8-3.94-.78-6.13-.77,.4,.44,.78,.9,1.21,1.32,2.52,2.43,5.1,4.79,7.04,7.78,1.71,2.64,2.08,5.45,1.8,8.43-.15,1.62-1.04,2.91-2.21,3.98-3.32,3.05-7.13,5.14-11.69,5.67-3.12,.36-6.05-.27-8.62-2.04-2.49-1.7-4.88-3.55-7.24-5.42-2.46-1.95-3.88-1.84-5.69,.66-.14,.2-.28,.4-.42,.61-1.66,2.37-3.86,3.89-6.75,4.48-3.02,.61-5.96-.14-7.04-2-1.31-2.26-.91-6.22,1-8.27,1.05-1.13,2.31-2.06,3.48-3.07,.55-.48,1.17-.9,1.66-1.44,1.04-1.15,1.14-2.02,.32-3.32-.52-.82-1.26-1.5-1.8-2.32-.8-1.22-1.6-2.45-2.24-3.76-1.05-2.14-.81-4.2,.47-6.26,2.26-3.63,4.7-4.47,8.7-2.84,1.51,.61,3.03,1.19,4.58,1.69,1.82,.58,3.79-.54,2.9-2.93-1.37-3.68-.84-7.26,.99-10.63,2.18-4,4.94-7.49,9.2-9.52,2.43-1.16,4.6-.88,6.47,1.08,1.01,1.06,1.96,2.18,2.89,3.32,.85,1.04,1.31,2.22,1.25,3.61-.06,1.47-.02,2.94,0,4.41,.01,.53,.11,1.07,.16,1.6,.22,.1,.43,.2,.65,.3Z"/><path d="M157.99,123.82c1-.87,1.65-1.46,2.33-2.02,4.22-3.48,10.25-4.14,14.67-1.65,2.19,1.23,3.7,2.98,4.08,5.49,1.01,6.61,1.96,13.23,2.93,19.85,.11,.78,.22,1.56,.4,2.78,.78-.82,1.27-1.31,1.72-1.83,3.77-4.36,8.69-5.93,14.25-5.72,4.08,.15,7.37,3.3,8.11,7.58,1.21,7.04,1.68,14.13,.7,21.22-1.31,9.45-2.96,18.86-4.29,28.31-1.53,10.83-2.88,21.68-4.32,32.52-.12,.88-.26,1.76-.42,2.84-2.61,.18-5.11,.51-7.62,.51-26.04-.07-52.07-.19-78.11-.31-.49,0-.98-.04-1.47-.03-1.84,.04-2.86-.98-3.36-2.63-.54-1.8-.88-3.68-1.61-5.39-2.11-4.95-4.29-9.87-6.59-14.74-1.39-2.95-2.98-5.81-4.57-8.65-6.75-12.04-13.32-24.15-18.32-37.05-2.62-6.76-4.24-13.69-4.93-20.89-.58-6.03,3.56-11.9,7.9-13.77,4.09-1.76,11.55-1.42,14.83,3.96,.32,.53,.78,.97,1.42,1.75,.14-.62,.26-.9,.26-1.18,.1-6.04,.18-12.08,.28-18.12,.16-10.22,1.18-20.36,2.67-30.47,.99-6.7,1.83-13.42,2.78-20.13,.33-2.34,.71-4.69,1.26-6.98,.38-1.57,.95-3.13,1.68-4.58,3.83-7.66,13.41-6.2,16.6-1.19,2.66,4.17,4.72,8.59,5.39,13.54,.29,2.1,.48,4.22,.54,6.33,.27,9.39,.47,18.77,.7,28.16,.03,1.3,.13,2.59,.22,4.39,.7-.76,1.14-1.16,1.47-1.63,2.6-3.65,6.04-5.89,10.55-6.39,1.86-.21,3.72-.53,5.58-.68,2.75-.23,4.9,.94,6.6,3.05,1.62,2.01,2.77,4.29,3.23,6.8,.8,4.41,1.38,8.86,2.03,13.3,.16,1.09,.27,2.19,.44,3.66Zm34.88,102.53c.13-1.2,.27-2.23,.34-3.28,.16-2.36,.11-4.75,.47-7.08,1.02-6.61,2.14-13.21,3.34-19.79,1.39-7.62,3.17-15.19,4.29-22.85,1.03-7.04,.89-14.13-1.1-21.1-.85-2.96-2.73-3.78-5.86-3.03-1.5,.36-2.92,1.04-4.4,1.49-2.88,.88-4.75,2.73-5.67,5.59-.27,.85-.55,1.73-1.02,2.48-.89,1.46-2.46,1.41-3.28-.1-.38-.7-.63-1.51-.79-2.3-.86-4.24-1.6-8.5-2.52-12.72-1.11-5.1-2.25-10.19-4.61-14.92-.76-1.52-1.77-2.2-3.39-2.31-2.66-.18-4.47,1.3-6.08,3.08-2.51,2.78-4.01,6.12-5.03,9.69-.18,.62-.31,1.32-.69,1.81-.36,.47-1.05,1-1.53,.96-.46-.05-1.08-.74-1.24-1.27-.42-1.4-.72-2.85-.91-4.3-.55-4.37-.94-8.76-1.55-13.12-.72-5.16-1.24-10.36-2.67-15.42-.68-2.4-2.06-3.8-4.44-4.33-4.37-.97-9.28,1.11-11.63,4.95-1.84,3.01-3.31,6.18-4.08,9.65-.32,1.43-.71,2.87-1.29,4.2-.28,.64-1.01,1.39-1.65,1.51-1.02,.19-1.52-.75-1.77-1.67-.17-.63-.36-1.26-.41-1.91-.54-5.93-1.26-11.85-1.54-17.8-.43-9.05-.5-18.12-.83-27.17-.17-4.58-.88-9.11-2.48-13.43-.59-1.59-1.3-3.21-2.32-4.54-1.56-2.04-3.87-1.88-5.28,.26-.94,1.42-1.7,2.97-2.4,4.53-1.42,3.16-1.94,6.55-2.53,9.93-1.57,8.87-2.41,17.83-2.38,26.8,.02,8.43-.56,16.79-.96,25.19-.32,6.75,.02,13.54,.15,20.3,.04,2.28,.35,4.56,.48,6.84,.05,.89-.37,1.6-1.28,1.87-.92,.27-1.57-.18-2.06-.93-.54-.81-1.04-1.66-1.63-2.44-3.66-4.85-8.31-8.74-12.6-12.98-.82-.81-1.84-1.61-3.24-1.43-2.42,.31-4.53,2.01-4.88,4.42-.29,2-.48,4.09-.22,6.08,.36,2.73,1.09,5.43,1.81,8.1,1.83,6.71,4.48,13.12,7.4,19.41,4.02,8.66,7.88,17.38,12.86,25.57,3.65,5.99,7.17,12.07,9.4,18.79,.69,2.08,1.66,4.07,2.46,6.12,.55,1.41,1.34,2.22,3.04,2.24,3.26,.04,6.52,.39,9.78,.48,4.4,.12,8.81,.14,13.22,.16,7.91,.02,15.83,.05,23.74,0,8.56-.06,17.13-.2,25.69-.3,1.86-.02,3.73,0,5.77,0Z"/><path class="cls-1" d="M192.87,226.36c-2.04,0-3.9-.02-5.77,0-8.56,.1-17.13,.25-25.69,.3-7.91,.05-15.83,.03-23.74,0-4.41-.01-8.81-.04-13.22-.16-3.26-.09-6.52-.44-9.78-.48-1.7-.02-2.49-.83-3.04-2.24-.8-2.05-1.77-4.04-2.46-6.12-2.23-6.72-5.75-12.8-9.4-18.79-4.99-8.18-8.85-16.91-12.86-25.57-2.92-6.3-5.57-12.7-7.4-19.41-.73-2.67-1.46-5.37-1.81-8.1-.26-1.99-.08-4.08,.22-6.08,.35-2.41,2.46-4.11,4.88-4.42,1.4-.18,2.42,.62,3.24,1.43,4.29,4.23,8.94,8.13,12.6,12.98,.59,.78,1.09,1.62,1.63,2.44,.49,.74,1.14,1.2,2.06,.93,.9-.27,1.33-.98,1.28-1.87-.13-2.28-.43-4.56-.48-6.84-.13-6.77-.46-13.55-.15-20.3,.39-8.39,.98-16.76,.96-25.19-.03-8.97,.81-17.93,2.38-26.8,.6-3.38,1.12-6.77,2.53-9.93,.7-1.56,1.46-3.11,2.4-4.53,1.41-2.14,3.72-2.3,5.28-.26,1.02,1.33,1.73,2.95,2.32,4.54,1.6,4.32,2.32,8.84,2.48,13.43,.33,9.06,.4,18.12,.83,27.17,.28,5.94,1,11.87,1.54,17.8,.06,.64,.25,1.28,.41,1.91,.25,.92,.75,1.86,1.77,1.67,.63-.12,1.37-.87,1.65-1.51,.58-1.34,.97-2.77,1.29-4.2,.78-3.47,2.24-6.64,4.08-9.65,2.35-3.84,7.26-5.92,11.63-4.95,2.39,.53,3.76,1.93,4.44,4.33,1.43,5.05,1.95,10.25,2.67,15.42,.61,4.36,1,8.75,1.55,13.12,.18,1.45,.49,2.9,.91,4.3,.16,.53,.77,1.23,1.24,1.27,.49,.05,1.17-.49,1.53-.96,.38-.49,.51-1.18,.69-1.81,1.02-3.57,2.53-6.91,5.03-9.69,1.61-1.78,3.42-3.27,6.08-3.08,1.62,.11,2.63,.78,3.39,2.31,2.36,4.73,3.5,9.82,4.61,14.92,.92,4.22,1.66,8.48,2.52,12.72,.16,.79,.41,1.6,.79,2.3,.82,1.5,2.39,1.55,3.28,.1,.46-.75,.74-1.63,1.02-2.48,.92-2.86,2.79-4.71,5.67-5.59,1.48-.45,2.9-1.13,4.4-1.49,3.13-.75,5.01,.07,5.86,3.03,1.99,6.97,2.13,14.06,1.1,21.1-1.12,7.66-2.9,15.22-4.29,22.85-1.2,6.58-2.32,13.18-3.34,19.79-.36,2.33-.31,4.72-.47,7.08-.07,1.04-.21,2.08-.34,3.28Z"/></svg><h1 class="mdc-typography--headline4">About ACHECKS</h2><p class="mdc-typography--body1">ACHECKS exists to help make the world a more accessible place. Our developers strive to help organizations maintain accessibility compliance across their websites through an easy-to-use dashboard and detailed reporting capabilities.</p><p class="mdc-typography--body1">To stay up-to-date with the latest news and updates about ACHECKS, <a href="https://achecks.org/subscribe" target="_blank">subscribe to the email list</a>.</p><p class="mdc-typography--body1">If you need support with achieving accessibility compliance with your websites, connect with with our professional services team at <a href="https://www.achecks.org/support/" target="_blank">our support page</a>.</p></div>
</div>
	<?php add_action( 'admin_footer', function() { ?>
		<script>
			var c = document.querySelectorAll('.currency');
			fetch('https://achecks.org/currency', {
				headers: { 'Accept': 'application/json' }
			})
			.then((response) => {
				return response.json();
			})
			.then((result) => {
				for (var i = 0; i < c.length; i++) {
					c[i].textContent = result.symbol;
				}
			});
			[].forEach.call(document.querySelectorAll('.mdc-tooltip'), function(tt) {
				var tt = new mdc.tooltip.MDCTooltip(tt);
				tt.setTooltipPosition({xPos: 1, yPos: 1});
				tt.setHideDelay(0);
			});
			var tabBar = new mdc.tabBar.MDCTabBar(document.getElementById('main-tabs'));
			var contentMainEls = document.querySelectorAll('.main-tabs-content');
			tabBar.listen('MDCTabBar:activated', function(event) {
				document.querySelector('.main-tabs-content.content--active').classList.remove('content--active');
				contentMainEls[event.detail.index].classList.add('content--active');
			});
		</script>
	<?php } );
	if ( $code === 200 ) { add_action( 'admin_footer', function() { ?>
		<script>
			var reportTabBar = new mdc.tabBar.MDCTabBar(document.getElementById('report-tabs'));
			var contentReportEls = document.querySelectorAll('.report-tabs-content');
			reportTabBar.listen('MDCTabBar:activated', function(event) {
				document.querySelector('.report-tabs-content.content--active').classList.remove('content--active');
				contentReportEls[event.detail.index].classList.add('content--active');
				resizeLighthouseReports();
			});
			var reportLHBar = new mdc.tabBar.MDCTabBar(document.getElementById('lh-tabs'));
			var contentLHEls = document.querySelectorAll('.lh-tabs-content');
			reportLHBar.listen('MDCTabBar:activated', function(event) {
				document.querySelector('.lh-tabs-content.content--active').classList.remove('content--active');
				contentLHEls[event.detail.index].classList.add('content--active');
				resizeLighthouseReports();
			});
			function resizeLighthouseReports() {
				document.querySelector('.mdc-dialog .lh-tab-mobile iframe').height = document.querySelector('.mdc-dialog').offsetHeight - 310;
				document.querySelector('.mdc-dialog .lh-tab-desktop iframe').height = document.querySelector('.mdc-dialog').offsetHeight - 310;
			}
			window.onresize = resizeLighthouseReports;
			const dialog = new mdc.dialog.MDCDialog(document.querySelector('.mdc-dialog'));
			function viewReport(btn) {
				document.getElementById('lh-tab-mobile').click();
				document.getElementById('report-tab-summary').click();
				document.getElementById('report-tab-achecker').style.display = btn.dataset.format !== 'PDF' && btn.dataset.errors > 0 ? 'flex' : 'none';
				document.getElementById('report-tab-tingtun').style.display = btn.dataset.format === 'PDF' && btn.dataset.errors > 0 ? 'flex' : 'none';
				document.querySelector('.mdc-dialog .report-tab-tingtun').textContent = '';
				document.querySelector('.mdc-dialog .report-tab-achecker').textContent = '';
				document.getElementById('report-tab-lighthouse').style.display = btn.dataset.lighthouse > 0 ? 'flex' : 'none';
				document.querySelector('.mdc-dialog .lh-tab-mobile iframe').srcdoc = '';
				document.querySelector('.mdc-dialog .lh-tab-desktop iframe').srcdoc = '';
				document.querySelector('.mdc-dialog .mdc-dialog__title').textContent = btn.parentElement.parentElement.firstElementChild.textContent.replace('open_in_new ', 'Report (') + ')';
				document.getElementById('report-status').innerHTML = btn.parentElement.previousElementSibling.previousElementSibling.previousElementSibling.innerHTML;
				document.getElementById('report-errors').innerHTML = btn.parentElement.parentElement.firstElementChild.nextElementSibling.innerHTML + ' ' + btn.parentElement.parentElement.firstElementChild.nextElementSibling.nextElementSibling.innerHTML;
				document.getElementById('report-link').innerHTML = '<a href="' + btn.dataset.url + '" target="_blank"><em aria-label="Link opens in new tab" class="material-icons small">open_in_new</em> ' + btn.dataset.url + '</a>';
				document.getElementById('report-title').textContent = btn.parentElement.previousElementSibling.textContent;
				document.getElementById('report-referer').innerHTML = '<a href="' + (btn.dataset.referer ? btn.dataset.referer : '/') + '" target="_blank"><em aria-label="Link opens in new tab" class="material-icons small">open_in_new</em> ' + (btn.dataset.referer ? btn.dataset.referer : '/') + '</a>';
				document.getElementById('report-updated').textContent = btn.dataset.updated;
				dialog.open()
				fetch('https://achecks.org/api/v1/reports/<?php echo esc_html( parse_url( home_url(), PHP_URL_HOST ) ); ?>' + btn.dataset.path, {
					headers: {
						'Referer': '<?php echo esc_url( home_url() ); ?>',
						'Connection-Key': '<?php echo esc_html( get_option( 'acachecks_connection_key_setting' ) ); ?>',
						'Accept': 'application/json'
					}
				})
				.then(function(response) {
					return response.json();
				})
				.then((result) => {
					if (btn.dataset.format === 'PDF') {
						document.querySelector('.mdc-dialog .report-tab-tingtun').textContent = result['tingtun'];
					} else {
						var html = '';
						if ('achecker' in result) {
							result['achecker']['errors'].forEach(function(e) {
								html += '<div class="mdc-card mdc-layout-grid__cell"><h2 class="mdc-typography--headline6">' + e.msg.replaceAll('&lt;code&gt;', '&lt;').replaceAll('&lt;/code&gt;', '&gt;')+ '</h2><ul><li>Line number: ' + e.line + '</li><li>Column number: ' + e.column + '</li></ul><code>' + e.src + '</code><hr><p>' + e.fix.replaceAll('&lt;code&gt;', '<strong>&lt;').replaceAll('&lt;/code&gt;', '&gt;</strong>') + '</p><div class="mdc-card__actions flex-bottom"><div class="mdc-card__action-buttons"><a href="' + e.url + '" target="_blank" class="mdc-button mdc-card__action mdc-card__action--button"><div class="mdc-button__ripple"></div><span class="mdc-button__label"><em aria-label="Link opens in new tab" class="material-icons small amber">open_in_new</em> Explanation</span></a></div></div></div>';
							});
							document.querySelector('.mdc-dialog .report-tab-achecker').innerHTML = html ? html : '<h2 class="mdc-typography--headline6 mdc-layout-grid__cell mdc-layout-grid__cell--span-12 text-align-center">Congratulations! No known problems.</h2>';
						}
						if ('lighthouse_mobile' in result) {
							document.querySelector('.mdc-dialog .lh-tab-mobile iframe').srcdoc = result['lighthouse_mobile'];
						}
						document.getElementById('lh-tab-mobile').style.display = 'lighthouse_mobile' in result ? 'flex' : 'none';
						if (!('lighthouse_mobile' in result)) {
							document.getElementById('lh-tab-desktop').click();
						}
						if ('lighthouse_desktop' in result) {
							document.querySelector('.mdc-dialog .lh-tab-desktop iframe').srcdoc = result['lighthouse_desktop'];
						}
						document.getElementById('lh-tab-desktop').style.display = 'lighthouse_desktop' in result ? 'flex' : 'none';
					}
				})
				.catch(function(err) {
					console.log(err);
				});
				return false;
			}
			function getStringBetween(s, start, end) {
				pos = s.indexOf(start) + start.length;
				return s.substring(pos, s.indexOf(end, pos));
			}
		</script>
	<?php }); }
	}

	public function acachecks_settings()
	{
		if ( !current_user_can( 'manage_options' ) ) {
			return;
		}
	?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
			<?php
				settings_fields( 'acachecks_general_settings' );
				do_settings_sections( 'acachecks_general_settings' );
				submit_button( 'Save Settings' );
			?>
		</form>
	</div>
	<?php
	}

	public function acachecks_build_settings_fields()
	{
		add_settings_section(
			'acachecks_general_section',
			'Connection Management',
			array( $this, 'acachecks_display_connection_management' ),
			'acachecks_general_settings'
		);
		unset( $args );
		$args = array (
			'type'             => 'input',
			'subtype'          => 'text',
			'id'               => 'acachecks_connection_key_setting',
			'name'             => 'acachecks_connection_key_setting',
			'value_type'       => 'normal',
			'wp_data'          => 'option'
		);
		add_settings_field(
			'acachecks_connection_key_setting',
			'Connection Key',
			array( $this, 'acachecks_render_settings_field' ),
			'acachecks_general_settings',
			'acachecks_general_section',
			$args
		);
		register_setting(
			'acachecks_general_settings',
			'acachecks_connection_key_setting'
		);
	}

	public function acachecks_display_connection_management()
	{ ?>
		<p>The connection key allows this website to communicate securely with ACHECKS.</p>
	<?php }

	public function acachecks_render_settings_field($args)
	{ ?>
		<label><small>The connection key is retrieved when you subscribe to a plan.</small><br><input type="text" name="acachecks_connection_key_setting" id="acachecks_connection_key_setting" placeholder="xxxx-xxxx-xxxx-xxxx" value="<?php echo esc_html( get_option( $args['name'] ) ); ?>"></label>
	<?php }
}

if ( class_exists( 'ACACHECKS_AccessibilityComplianceByACHECKS' ) )
{
	new ACACHECKS_AccessibilityComplianceByACHECKS();
}
