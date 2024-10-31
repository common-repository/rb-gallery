<h2><?php esc_attr_e( 'RB Image Collection Gallery', 'rb-gallery' ); ?></h2>

<div class="wrap">

    <div id="icon-options-general" class="icon32"></div>

    <div id="poststuff">

        <div id="post-body" class="metabox-holder columns-2">

            <!-- main content -->
            <div id="post-body-content">

                <div class="meta-box-sortables ui-sortable">

                    <div class="postbox">

                        <div class="handlediv" title="Click to toggle"><br></div>
                        <!-- Toggle -->

                        <h2>
                            <strong><?php esc_attr_e( 'How to Use', 'rb-gallery' ); ?></strong>
                        </h2>

                        <div class="inside">

                            <div class="postbox">

                                <div class="inside">

                                    <p>
                                        To use your gallery, copy and paste this shortcode into your post or page:
                                    </p>
                                    <p>
                                        [wprb_gallery]
                                    </p>
                                    <ul>
                                       <li>Add Gallery</li>
                                       <li>See all Galleries</li>
                                       <li>Edit Gallery</li>
                                       <li>Add Images</li>
                                    </ul>

                                    <div id="js_messages"></div>

                                </div> <!-- .inside -->

                            </div> <!-- .postbox FIRST BOX END -->

                        </div>
                        <!-- .inside -->

                    </div>
                    <!-- .postbox -->

                </div>
                <!-- .meta-box-sortables .ui-sortable -->


            </div>
            <!-- post-body-content -->

            <!-- sidebar -->
            <div id="postbox-container-1" class="postbox-container">

                <div class="meta-box-sortables">

                    <div class="postbox">

                        <div class="handlediv" title="Click to toggle"><br></div>
                        <!-- Toggle -->

                        <h2 class="hndle"><span><?php esc_attr_e(
                                    'RB Image Collection Gallery', 'rb_gallery'
                                ); ?></span></h2>

                        <div class="inside">
                            <p><?php
                                esc_attr_e( 'To use your gallery, copy and paste this shortcode into your post or page:', 'rb-gallery' );
                                echo "<br /><br />";
                                esc_attr_e('[wprb_gallery]', 'rb-gallery');
                                echo "<br /><br />";
                                esc_attr_e('If you want to donate to help development with this plugin, please follow the button below.', 'rb-gallery');
                                ?>
                            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                                <input type="hidden" name="cmd" value="_s-xclick">
                                <input type="hidden" name="hosted_button_id" value="QG9MZEU9QLSQQ">
                                <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                                <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                            </form>
                            </p>
                        </div>
                        <!-- .inside -->

                    </div>
                    <!-- .postbox -->

                </div>
                <!-- .meta-box-sortables -->

            </div>
            <!-- #postbox-container-1 .postbox-container -->

        </div>
        <!-- #post-body .metabox-holder .columns-2 -->

        <br class="clear">
    </div>
    <!-- #poststuff -->

</div> <!-- .wrap -->


<script>
    jQuery( document ).ready(function($) {


    });

</script>




