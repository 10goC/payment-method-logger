<div class="wrap">
    <?php do_action( 'admin_notices' ) ?>
    <h1 class="wp-heading-inline"><?php _e('Payment Method Logger', 'payment-method-logger') ?></h1>
    <h2><?php _e('Fallback settings', 'payment-method-logger') ?></h2>
    <form method="post" action="<?php echo admin_url( 'admin.php' ) ?>">
        <table class="form-table">
            <tr>
                <th>
                    <label for="webhook-url"><?php _e('Webhook URL', 'payment-method-logger') ?></label>
                </th>
                <td>
                    <input name="webhook-url" type="text" id="webhook-url" value="<?php echo get_option('webhook-url') ?>" class="regular-text">
                </td>
            </tr>
        </table>
        <input type="hidden" name="action" value="save-logger-fallback" />
        <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('payment-method-logger') ?>">
        <button type="submit" class="button button-primary"><?php _e('Save changes', 'payment-method-logger') ?></button>
    </form>
</div>