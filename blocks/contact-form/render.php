<?php
/**
 * Contact form block render callback.
 *
 * @package McCullough_Digital
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$defaults = [
    'buttonText'     => "Send my project details",
    'successMessage' => "Thanks! We'll be in touch within one business day.",
    'showOptIn'      => true,
];

$attributes      = wp_parse_args( $attributes, $defaults );
$button_text     = trim( (string) $attributes['buttonText'] );
$success_message = trim( (string) $attributes['successMessage'] );
$show_opt_in     = (bool) $attributes['showOptIn'];

if ( '' === $button_text ) {
    $button_text = $defaults['buttonText'];
}

if ( '' === $success_message ) {
    $success_message = $defaults['successMessage'];
}

wp_enqueue_script( 'mccullough-digital-contact-form-view-script' );

static $mcd_contact_form_localized = false;

if ( ! $mcd_contact_form_localized ) {
    wp_localize_script(
        'mccullough-digital-contact-form-view-script',
        'mcdContactFormSettings',
        array(
            'endpoint'       => esc_url_raw( rest_url( 'mcd/v1/contact' ) ),
            'nonce'          => wp_create_nonce( 'mcd_contact_form_submit' ),
            'loadingText'    => __( 'Sending...', 'mccullough-digital' ),
            'errorMessage'   => __( 'Something went wrong. Please email hello@mccullough.digital.', 'mccullough-digital' ),
            'successMessage' => __( "Thanks! We'll be in touch shortly.", 'mccullough-digital' ),
            'successEvent'   => 'mcd_contact_success',
            'errorEvent'     => 'mcd_contact_error',
        )
    );

    $mcd_contact_form_localized = true;
}

$form_id = function_exists( 'wp_unique_id' ) ? wp_unique_id( 'mcd-contact-form-' ) : uniqid( 'mcd-contact-form-' );

$wrapper_attributes = get_block_wrapper_attributes(
    [
        'class'                => 'mcd-contact-form',
        'data-success-message' => $success_message,
        'data-button-text'     => $button_text,
        'data-show-opt-in'     => $show_opt_in ? '1' : '0',
    ]
);

?>
<form <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> id="<?php echo esc_attr( $form_id ); ?>" novalidate>
    <nav class="mcd-contact-form__progress" aria-label="<?php esc_attr_e( 'Quote request sections', 'mccullough-digital' ); ?>">
        <ol class="mcd-contact-form__progress-list">
            <li class="mcd-contact-form__progress-step" data-section-target="contact">
                <span class="mcd-contact-form__progress-index">1</span>
                <span class="mcd-contact-form__progress-label"><?php esc_html_e( 'Contact', 'mccullough-digital' ); ?></span>
            </li>
            <li class="mcd-contact-form__progress-divider" aria-hidden="true"></li>
            <li class="mcd-contact-form__progress-step" data-section-target="project">
                <span class="mcd-contact-form__progress-index">2</span>
                <span class="mcd-contact-form__progress-label"><?php esc_html_e( 'Project Fit', 'mccullough-digital' ); ?></span>
            </li>
            <li class="mcd-contact-form__progress-divider" aria-hidden="true"></li>
            <li class="mcd-contact-form__progress-step" data-section-target="timeline">
                <span class="mcd-contact-form__progress-index">3</span>
                <span class="mcd-contact-form__progress-label"><?php esc_html_e( 'Budget & Timing', 'mccullough-digital' ); ?></span>
            </li>
        </ol>
    </nav>

    <div class="mcd-contact-form__sections">
        <section class="mcd-contact-form__section" data-section="contact">
            <header class="mcd-contact-form__section-header">
                <h3 class="mcd-contact-form__section-title">
                    <span class="mcd-contact-form__section-index">1</span>
                    <?php esc_html_e( 'Contact details', 'mccullough-digital' ); ?>
                </h3>
                <p class="mcd-contact-form__section-note"><?php esc_html_e( 'Who should receive the estimate and follow-up?', 'mccullough-digital' ); ?></p>
            </header>

            <div class="mcd-contact-form__grid">
                <div class="mcd-contact-form__field" data-field="name">
                    <label for="<?php echo esc_attr( $form_id ); ?>-name" class="mcd-contact-form__label">
                        <?php esc_html_e( 'Name', 'mccullough-digital' ); ?>
                        <span class="mcd-contact-form__required" aria-hidden="true">*</span>
                    </label>
                    <input type="text" id="<?php echo esc_attr( $form_id ); ?>-name" name="name" aria-describedby="<?php echo esc_attr( $form_id ); ?>-name-error" aria-required="true" required autocomplete="name" />
                    <p class="mcd-contact-form__error" id="<?php echo esc_attr( $form_id ); ?>-name-error" aria-live="polite"></p>
                </div>

                <div class="mcd-contact-form__field" data-field="email">
                    <label for="<?php echo esc_attr( $form_id ); ?>-email" class="mcd-contact-form__label">
                        <?php esc_html_e( 'Email', 'mccullough-digital' ); ?>
                        <span class="mcd-contact-form__required" aria-hidden="true">*</span>
                    </label>
                    <input type="email" id="<?php echo esc_attr( $form_id ); ?>-email" name="email" aria-describedby="<?php echo esc_attr( $form_id ); ?>-email-error" aria-required="true" required autocomplete="email" />
                    <p class="mcd-contact-form__error" id="<?php echo esc_attr( $form_id ); ?>-email-error" aria-live="polite"></p>
                </div>

                <div class="mcd-contact-form__field" data-field="business">
                    <label for="<?php echo esc_attr( $form_id ); ?>-business" class="mcd-contact-form__label">
                        <?php esc_html_e( 'Business or organization', 'mccullough-digital' ); ?>
                    </label>
                    <input type="text" id="<?php echo esc_attr( $form_id ); ?>-business" name="business" autocomplete="organization" />
                </div>

                <div class="mcd-contact-form__field" data-field="website">
                    <label for="<?php echo esc_attr( $form_id ); ?>-website" class="mcd-contact-form__label">
                        <?php esc_html_e( 'Current website', 'mccullough-digital' ); ?>
                    </label>
                    <input type="url" id="<?php echo esc_attr( $form_id ); ?>-website" name="website" autocomplete="url" />
                </div>
            </div>
        </section>

        <section class="mcd-contact-form__section" data-section="project">
            <header class="mcd-contact-form__section-header">
                <h3 class="mcd-contact-form__section-title">
                    <span class="mcd-contact-form__section-index">2</span>
                    <?php esc_html_e( 'Project essentials', 'mccullough-digital' ); ?>
                </h3>
            </header>

            <div class="mcd-contact-form__grid">
                <div class="mcd-contact-form__field mcd-contact-form__field--full" data-field="goals">
                    <label for="<?php echo esc_attr( $form_id ); ?>-goals" class="mcd-contact-form__label">
                        <?php esc_html_e( 'What should we launch or improve?', 'mccullough-digital' ); ?>
                        <span class="mcd-contact-form__required" aria-hidden="true">*</span>
                    </label>
                    <textarea id="<?php echo esc_attr( $form_id ); ?>-goals" name="goals" aria-describedby="<?php echo esc_attr( $form_id ); ?>-goals-error" aria-required="true" required rows="4"></textarea>
                    <p class="mcd-contact-form__error" id="<?php echo esc_attr( $form_id ); ?>-goals-error" aria-live="polite"></p>
                </div>

                <fieldset class="mcd-contact-form__field mcd-contact-form__field--full mcd-contact-form__choices" aria-labelledby="<?php echo esc_attr( $form_id ); ?>-services-label">
                    <legend id="<?php echo esc_attr( $form_id ); ?>-services-label" class="mcd-contact-form__label"><?php esc_html_e( "Services you're interested in", 'mccullough-digital' ); ?></legend>
                    <div class="mcd-contact-form__choice-wrap">
                        <label class="mcd-contact-form__choice"><input type="checkbox" name="services[]" value="website-brand" /><?php esc_html_e( 'Website build or refresh', 'mccullough-digital' ); ?></label>
                        <label class="mcd-contact-form__choice"><input type="checkbox" name="services[]" value="content-seo" /><?php esc_html_e( 'Content &amp; SEO', 'mccullough-digital' ); ?></label>
                        <label class="mcd-contact-form__choice"><input type="checkbox" name="services[]" value="paid-ads" /><?php esc_html_e( 'Paid ads &amp; funnels', 'mccullough-digital' ); ?></label>
                        <label class="mcd-contact-form__choice"><input type="checkbox" name="services[]" value="automation-analytics" /><?php esc_html_e( 'Automation &amp; analytics', 'mccullough-digital' ); ?></label>
                        <label class="mcd-contact-form__choice"><input type="checkbox" name="services[]" value="support-care" /><?php esc_html_e( 'Care plan &amp; support', 'mccullough-digital' ); ?></label>
                    </div>
                </fieldset>
            </div>
        </section>

        <section class="mcd-contact-form__section" data-section="timeline">
            <header class="mcd-contact-form__section-header">
                <h3 class="mcd-contact-form__section-title">
                    <span class="mcd-contact-form__section-index">3</span>
                    <?php esc_html_e( 'Budget & timing', 'mccullough-digital' ); ?>
                </h3>
            </header>

            <div class="mcd-contact-form__grid">
                <div class="mcd-contact-form__field">
                    <label for="<?php echo esc_attr( $form_id ); ?>-budget" class="mcd-contact-form__label">
                        <?php esc_html_e( 'Monthly marketing budget', 'mccullough-digital' ); ?>
                    </label>
                    <select id="<?php echo esc_attr( $form_id ); ?>-budget" name="budget">
                        <option value="" selected><?php esc_html_e( 'Choose a range', 'mccullough-digital' ); ?></option>
                        <option value="under-1k"><?php esc_html_e( 'Under $1k', 'mccullough-digital' ); ?></option>
                        <option value="1k-3k"><?php esc_html_e( '$1k - $3k', 'mccullough-digital' ); ?></option>
                        <option value="3k-5k"><?php esc_html_e( '$3k - $5k', 'mccullough-digital' ); ?></option>
                        <option value="5k-plus"><?php esc_html_e( '$5k+', 'mccullough-digital' ); ?></option>
                    </select>
                </div>

                <div class="mcd-contact-form__field">
                    <label for="<?php echo esc_attr( $form_id ); ?>-timeline" class="mcd-contact-form__label">
                        <?php esc_html_e( 'Ideal launch window', 'mccullough-digital' ); ?>
                    </label>
                    <select id="<?php echo esc_attr( $form_id ); ?>-timeline" name="timeline">
                        <option value="" selected><?php esc_html_e( "I'm flexible", 'mccullough-digital' ); ?></option>
                        <option value="asap"><?php esc_html_e( 'ASAP (next 30 days)', 'mccullough-digital' ); ?></option>
                        <option value="quarter"><?php esc_html_e( 'Next 2-3 months', 'mccullough-digital' ); ?></option>
                        <option value="half-year"><?php esc_html_e( 'Within 6 months', 'mccullough-digital' ); ?></option>
                    </select>
                </div>

                <div class="mcd-contact-form__field mcd-contact-form__field--full">
                    <label for="<?php echo esc_attr( $form_id ); ?>-referral" class="mcd-contact-form__label">
                        <?php esc_html_e( 'How did you hear about us?', 'mccullough-digital' ); ?>
                    </label>
                    <input type="text" id="<?php echo esc_attr( $form_id ); ?>-referral" name="referral" />
                </div>
            </div>
        </section>
    </div>

    <?php if ( $show_opt_in ) : ?>
    <div class="mcd-contact-form__field mcd-contact-form__optin">
        <label class="mcd-contact-form__choice mcd-contact-form__choice--checkbox">
            <input type="checkbox" name="opt_in" value="1" />
            <?php esc_html_e( 'Optional — send me McCullough Digital playbooks and growth tips once a month. Skipping this does not impact my project estimate.', 'mccullough-digital' ); ?>
        </label>
    </div>
    <?php endif; ?>

    <div class="mcd-contact-form__actions">
        <button type="submit" class="mcd-contact-form__submit mcd-btn mcd-btn--primary">
            <span class="mcd-contact-form__submit-text"><?php echo esc_html( $button_text ); ?></span>
            <span class="mcd-contact-form__spinner" aria-hidden="true"></span>
        </button>
        <p class="mcd-contact-form__disclaimer"><?php esc_html_e( "No spam, no obligations. We'll confirm next steps and any resources you should prep.", 'mccullough-digital' ); ?></p>
    </div>

    <div class="mcd-contact-form__notice" role="status" aria-live="polite"></div>
</form>
