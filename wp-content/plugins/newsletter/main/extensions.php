<?php
@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = Newsletter::instance();
$extensions = $module->getTnpExtensions();

$controls->data = get_option('newsletter_main');

if (isset($_POST['email']) && check_admin_referer('subscribe')) {
    $body = array();
    $body['ne'] = $_POST['email'];
    $body['nr'] = 'extensions';
    $body['nl'] = array('3', '4', '1');
    $body['optin'] = 'single';

    wp_remote_post('http://www.thenewsletterplugin.com/?na=ajaxsub', array('body' => $body));

    update_option('newsletter_subscribed', time(), false);

    $id = (int) $_POST['id'];
    wp_redirect(wp_nonce_url(admin_url('admin.php'), 'save') . '&page=newsletter_main_extensions&act=install&id=' . $id);
    die();
}

$subscribed = get_option('newsletter_subscribed', false);
$has_license = !empty(Newsletter::instance()->options['contract_key']);
if ($has_license) {
    if (!class_exists('NewsletterExtensions')) {
        $controls->warnings = 'Please, install our Addons manager to manage all extensions from this panel. 
            <a href="https://www.thenewsletterplugin.com/documentation/how-to-install-the-addons-manager" target="_blank">Get it here</a>.';
    }
}
?>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('Extensions', 'newsletter') ?></h2>

        <p><?php _e('Extend your Newsletter experience with our addons', 'newsletter') ?>.</p>

    </div>

    <div id="tnp-body">

        <?php if (is_array($extensions)) { ?>

             <!-- Extensions -->
            <?php foreach ($extensions AS $e) { ?>

                <?php
                $e->activate_url = wp_nonce_url(admin_url('admin.php') . '?page=newsletter_extensions_index&act=activate&id=' . $e->id, 'save');
                if ($subscribed) {
                    $e->install_url = wp_nonce_url(admin_url('admin.php') . '?page=newsletter_main_extensions&act=install&id=' . $e->id, 'save');
                } else {
                    $e->install_url = 'javascript:newsletter_subscribe(' . $e->id . ')';
                }
                ?>
               
                <?php if ($e->type == "extension" || $e->type == "premium") { ?>
                    <?php if ($e->free) { ?>
                    <div class="tnp-extension-free-box <?php echo $e->slug ?>">
                    <?php } else { ?>
                    <div class="tnp-extension-premium-box <?php echo $e->slug ?>">
                    <?php } ?>
                        <?php if ($e->free) { ?>
                        <img class="tnp-extensions-free-badge" src="<?php echo plugins_url('newsletter')?>/images/extension-free.png">
                        <?php } ?>
                        <div class="tnp-extensions-image"><img src="<?php echo $e->image ?>" alt="" /></div>
                        <h3><?php echo $e->title ?></h3>
                        <p><?php echo $e->description ?></p>
                        <div class="tnp-extension-premium-action">
                            <?php if (is_plugin_active($e->wp_slug)) { ?>
                                <span><i class="fa fa-check" aria-hidden="true"></i> <?php _e('Plugin active', 'newsletter') ?></span>
                            <?php } elseif (file_exists(WP_PLUGIN_DIR . "/" . $e->wp_slug)) { ?>
                                <a href="<?php echo $e->activate_url ?>" class="tnp-extension-activate">
                                    <i class="fa fa-power-off" aria-hidden="true"></i> <?php _e('Activate', 'newsletter') ?>
                                </a>
                            <?php } elseif ($e->downloadable) { ?>
                                <a href="<?php echo $e->install_url ?>" class="tnp-extension-install">
                                    <i class="fa fa-download" aria-hidden="true"></i> Install Now
                                </a>
                            <?php } else { ?>
                                <a href="https://www.thenewsletterplugin.com/premium?utm_source=plugin&utm_medium=link&utm_campaign=extpanel" class="tnp-extension-buy" target="_blank">
                                    <i class="fa fa-shopping-cart" aria-hidden="true"></i> Buy Now
                                </a>
                            <?php } ?>
                            <!--    
                            <?php if ($e->url) { ?>
                                <br><br>
                                <a href="<?php echo $e->url ?>" class="tnp-extension-details" target="_blank">
                                    View details
                                </a>
                            <?php } ?>
                            -->
                        </div>
                        
                    </div>
                <?php } ?>
            <?php } ?>

            <!-- Integrations -->
            <?php foreach ($extensions AS $e) { ?>

                <?php
                $e->activate_url = wp_nonce_url(admin_url('admin.php') . '?page=newsletter_extensions_index&act=activate&id=' . $e->id, 'save');
                if ($subscribed) {
                    $e->install_url = wp_nonce_url(admin_url('admin.php') . '?page=newsletter_main_extensions&act=install&id=' . $e->id, 'save');
                } else {
                    $e->install_url = 'javascript:newsletter_subscribe(' . $e->id . ')';
                }
                ?>
                <?php if ($e->type == "integration") { ?>
                    <?php if ($e->free) { ?>
                    <div class="tnp-extension-free-box <?php echo $e->slug ?>">
                    <?php } else { ?>
                    <div class="tnp-integration-box <?php echo $e->slug ?>">
                    <?php } ?>
                        <?php if ($e->free) { ?>
                        <img class="tnp-extensions-free-badge" src="<?php echo plugins_url('newsletter')?>/images/extension-free.png">
                        <?php } ?>
                        <div class="tnp-extensions-image"><img src="<?php echo $e->image ?>"></div>
                        <h3><?php echo $e->title ?></h3>
                        <p><?php echo $e->description ?></p>
                        <div class="tnp-extension-free-action">
                            <?php if (is_plugin_active($e->wp_slug)) { ?>
                                <span><i class="fa fa-check" aria-hidden="true"></i> <?php _e('Plugin active', 'newsletter') ?></span>
                            <?php } elseif (file_exists(WP_PLUGIN_DIR . "/" . $e->wp_slug)) { ?>
                                <a href="<?php echo $e->activate_url ?>" class="tnp-extension-activate">
                                    <i class="fa fa-power-off" aria-hidden="true"></i> <?php _e('Activate', 'newsletter') ?>
                                </a>
                           <?php } elseif ($e->downloadable) { ?>
                                <a href="<?php echo $e->install_url ?>" class="tnp-extension-install">
                                    <i class="fa fa-download" aria-hidden="true"></i> Install Now
                                </a>
                            <?php } else { ?>
                                <a href="https://www.thenewsletterplugin.com/premium?utm_source=plugin&utm_medium=link&utm_campaign=extpanel" class="tnp-extension-buy" target="_blank">
                                    <i class="fa fa-shopping-cart" aria-hidden="true"></i> Buy Now
                                </a>
                            <?php } ?>
                            <!--  
                            <?php if ($e->url) { ?>
                            <br><br>
                            <div class="tnp-integration-details">
                                <a href="<?php echo $e->url ?>" class="tnp-extension-details" target="_blank">
                                    View details
                                </a>
                            </div>
                            <?php } ?>
                            -->
                        </div>
                    </div>
                <?php } ?>

            <?php } ?>

                        <!-- Delivery -->
            <?php foreach ($extensions AS $e) { ?>

                <?php
                $e->activate_url = wp_nonce_url(admin_url('admin.php') . '?page=newsletter_extensions_index&act=activate&id=' . $e->id, 'save');
                if ($subscribed) {
                    $e->install_url = wp_nonce_url(admin_url('admin.php') . '?page=newsletter_main_extensions&act=install&id=' . $e->id, 'save');
                } else {
                    $e->install_url = 'javascript:newsletter_subscribe(' . $e->id . ')';
                }
                ?>

                <?php if ($e->type == "delivery") { ?>
                    <?php if ($e->free) { ?>
                    <div class="tnp-extension-free-box <?php echo $e->slug ?>">
                    <?php } else { ?>
                    <div class="tnp-integration-box <?php echo $e->slug ?>">
                    <?php } ?>
                        <?php if ($e->free) { ?>
                        <img class="tnp-extensions-free-badge" src="<?php echo plugins_url('newsletter')?>/images/extension-free.png">
                        <?php } ?>
                        <div class="tnp-extensions-image"><img src="<?php echo $e->image ?>" alt="" /></div>
                        <h3><?php echo $e->title ?></h3>
                        <p><?php echo $e->description ?></p>
                        <div class="tnp-integration-action">
                            <?php if (is_plugin_active($e->wp_slug)) { ?>
                                <span><i class="fa fa-check" aria-hidden="true"></i> <?php _e('Plugin active', 'newsletter') ?></span>
                            <?php } elseif (file_exists(WP_PLUGIN_DIR . "/" . $e->wp_slug)) { ?>
                                <a href="<?php echo $e->activate_url ?>" class="tnp-extension-activate">
                                    <i class="fa fa-power-off" aria-hidden="true"></i> <?php _e('Activate', 'newsletter') ?>
                                </a>
                            <?php } elseif ($e->downloadable) { ?>
                                <a href="<?php echo $e->install_url ?>" class="tnp-extension-install">
                                    <i class="fa fa-download" aria-hidden="true"></i> Install Now
                                </a>
                            <?php } else { ?>
                                <a href="https://www.thenewsletterplugin.com/premium?utm_source=plugin&utm_medium=link&utm_campaign=extpanel" class="tnp-extension-buy" target="_blank">
                                    <i class="fa fa-shopping-cart" aria-hidden="true"></i> Buy Now
                                </a>
                            <?php } ?>
                            <!--      
                            <?php if ($e->url) { ?>
                            <br><br>    
                            <div class="tnp-integration-details">
                                <a href="<?php echo $e->url ?>" class="tnp-extension-details" target="_blank">
                                    View details
                                </a>
                            </div>
                            <?php } ?>
                            -->
                        </div>
                    </div>
                <?php } ?>

            <?php } ?>


        <?php } else { ?>

            <p style="color: white;">No extensions available.</p>

        <?php } ?>


        <p class="clear"></p>

    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>

<script>
    function newsletter_subscribe(id) {
        document.getElementById("tnp-extension-id").value = id;
        jQuery("#tnp-subscribe-overlay").fadeIn(500);
    }
</script>

<div id="tnp-subscribe-overlay">
    <div id="tnp-subscribe-modal">
        <div>
            <img src="https://cdn.thenewsletterplugin.com/newsletters-img/tnp-logo-colore-text-white@2x.png">
        </div>
        <div id="tnp-subscribe-title">
            Subscribe our newsletter to get this extension<br>
            and be informed about updates and best practices.</div>
        <form method="post" action="?page=newsletter_main_extensions&noheader=true">
            <?php wp_nonce_field('subscribe'); ?>
            <input type="hidden" value="id" name="id" id="tnp-extension-id">
            <div id="tnp-subscribe-email-wrapper"><input type="email" id="tnp-subscribe-email" name="email" value="<?php echo esc_attr(get_option('admin_email')) ?>"></div>
            <div id="tnp-subscribe-submit-wrapper"><input type="submit" id="tnp-subscribe-submit" value="<?php esc_attr_e('Subscribe', 'newsletter') ?>"></div>
        </form>
        <div class="tnp-subscribe-no-thanks">
            <a href="javascript:void(jQuery('#tnp-subscribe-overlay').hide())">No thanks, I don't want to install the free extension</a>
        </div>
    </div>
</div>
