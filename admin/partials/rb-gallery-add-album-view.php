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
                            <strong><?php esc_attr_e( 'Add Album', 'rb-gallery' ); ?></strong>
                        </h2>

                        <div class="inside">

                            <div class="postbox">

                                <div class="inside">

                                    <form name="wprbgall_albumname_form" id="wprbgall_albumname_form" method="post" action="<?php echo sprintf( '%s', admin_url( 'admin-post.php' ) ); ?>">

                                        <input type="hidden" name="action" value="add_album" >

                                        <table class="form-table">
                                            <tr>
                                                <td><label for="wprbgall_albumname">Album Name</label></td>
                                                <td><input name="wprbgall_albumname" id="rbgall_albumname" type="text" value="" class="regular-text" /></td>
                                            </tr>
                                            <tr>
                                                <td><label for="wprbgall_albumdesc">Description</label></td>
                                                <td><input name="wprbgall_albumdesc" id="rbgall_albumdesc" type="text" value="" class="regular-text" /></td>
                                            </tr>
                                            <tr>
                                                <td><label for="wprbgall_albumorder">Album Order</label></td>
                                                <td><input name="wprbgall_albumorder" id="rbgall_albumorder" type="text" value="" class="regular-text" /></td>
                                            </tr>
                                        </table>
                                        <p>
                                            <input class="button-primary" type="submit" id="album_add_btn" name="rbgall_albumname_submit" value="Add" />
                                        </p>
                                    </form>

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
                                esc_attr_e( 'See our other plugins at devteamseven.com', 'rb-gallery' );
                                ?>
  
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

        //    simple validation on add album
        $( "#wprbgall_albumname_form" ).submit(function( event ) {
            if ($('#rbgall_albumname').val() === "" || $('#rbgall_albumdesc').val() === "" || $('#rbgall_albumorder').val() === "") {
                $('#js_messages').html("<div class='alert alert-danger' role='alert'>Please fill out all fields before adding Album</div>");
                return false;
            } else {
                $('#js_messages').html("");
                return true;
            }
            event.preventDefault();
        });

    });

</script>




