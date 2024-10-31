<h2><?php esc_attr_e( 'RB Image Collection Gallery', 'rb-gallery' ); ?></h2>

<div class="wrap">

    <div id="icon-options-general" class="icon32"></div>
    <h1><?php //esc_attr_e( 'Heading', 'rb-gallery' ); ?></h1>

    <div id="poststuff">

        <div id="post-body" class="metabox-holder columns-2">

            <!-- main content -->
            <div id="post-body-content">


<!--                This div can be switched out on album edit-->
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">

                        <h2>
                            <a href="<?php echo sprintf('%s?page=wprb-gallery', admin_url('admin.php')); ?>"><?php esc_attr_e('Show All Albums', 'rb-gallery'); ?></a>
                            <?php echo (isset($_GET['edit_album']) ? '> Edit Album'  : '') ?>
                        </h2>

                        <!-- show_all_albums -->
                        <?php if ($view === 'show_all_albums'): ?>
                            <hr />
                        <div class="inside">
<!--                            <p>List albums here, if edit one of them, switch out this DIV with album detail</p>-->
                            <?php if (isset($all_albums)) {
                                foreach ($all_albums as $all_album): ?>
                                    <div class="album_item">
                                        <h3><i>Title:</i> <?php echo $all_album->album_name; ?></h3>
                                        <p><i>Description:</i> <?php echo $all_album->description; ?></p>
                                        <p><i>Date Created:</i> <?php echo $all_album->album_date; ?></p>
                                        <p><i>Order:</i> <?php echo $all_album->album_order; ?></p>
                                        <p>
                                            <a href="<?php echo sprintf('%s?page=wprb-gallery&edit_album=%s', admin_url('admin.php'), $all_album->album_id); ?>">Edit
                                                Album</a>
                                        </p>
                                        <p>
                                          <a href="<?php echo sprintf('%s?page=wprb-gallery&delete_album=%s', admin_url('admin.php'), $all_album->album_id); ?>">Delete Album</a>
                                        </p>
                                    </div>
                                <?php endforeach;
                            }
                            ?>
                        </div>
                        <?php endif; ?>

                        <!-- ************** show_album_edit ************** -->
                        <?php if ($view === 'show_album_edit'): ?>
                            <div class="inside">

                                <div class="album_item">

                                    <h2>
                                        <strong><?php esc_attr_e( 'Album: ' . $single_album->album_name, 'rb-gallery' ); ?></strong>
                                    </h2>

                                    <form name="wprbgall_albumname_form" id="wprbgall_albumname_form" method="post" action="<?php echo sprintf( '%s', admin_url( 'admin-post.php' ) ); ?>">

                                        <input type="hidden" name="action" value="edit_album" >
                                        <input type="hidden" name="albumid" value="<?php echo $_GET['edit_album']; ?>" >

                                        <table class="form-table">
                                            <tr>
                                                <td><label for="wprbgall_albumname">Title</label></td>
                                                <td><input name="wprbgall_albumname" id="rbgall_albumname" type="text" value="<?php echo $single_album->album_name; ?>" class="regular-text" /></td>
                                            </tr>
                                            <tr>
                                                <td><label for="wprbgall_albumdesc">Description</label></td>
                                                <td><input name="wprbgall_albumdesc" id="rbgall_albumdesc" type="text" value="<?php echo $single_album->description; ?>" class="regular-text" /></td>
                                            </tr>
                                            <tr>
                                                <td><label for="wprbgall_albumorder">Sort Order</label></td>
                                                <td><input name="wprbgall_albumorder" id="rbgall_albumorder" type="text" value="<?php if (isset($single_album->album_order)) { echo $single_album->album_order; } ?>" class="regular-text" /></td>
                                            </tr>
                                        </table>
                                        <p>
                                            <input class="button-primary" type="submit" id="album_add_btn" name="rbgall_albumname_submit" value="Edit" />
                                        </p>
                                    </form>

                                </div>

                                <div class="album_item">

                                    <h2>
                                        <strong><?php esc_attr_e( 'Album Pictures: ', 'rb-gallery' ); ?></strong>
                                    </h2>

                                    <p>Show all images in album; add image; delete image</p>
                                    <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#add_picture_modal">Add Picture</button>
                                    <br /><br />
<!--                                    [Image] (title,description,sorting,delete, pick album cover radio)-->

                                    <div>
                                        Images
                                        <?php foreach ($get_images_by_id as $get_img) : ?>
                                            <hr />
                                            <?php
                                            // Fix Images Sizes in Admin Gallery ** Fix
                                            $width = '25%';
                                            list($source_image_width, $source_image_height, $source_image_type) = getimagesize($get_img->url);
                                             if ($source_image_width > 0 && $source_image_width < 500) {
                                                 $width = '30%';
                                             }
                                            if ($source_image_width > 500 && $source_image_width < 1000) {
                                                $width = '40%';
                                            }
                                            if ($source_image_width > 1000) {
                                                $width = '50%';
                                            }
                                        ?>


                                            <img src="<?php echo $get_img->url; ?>" width="<?php echo $width; ?>"/>
                                            <p>Picture name: <?php echo $get_img->pic_name; ?></p>
                                            <p>Created: <?php echo $get_img->created_date; ?></p>

                                            <form method="post" action="<?php echo sprintf('%s', admin_url('admin-post.php')); ?>" >
                                                <input type="hidden" name="action" value="edit_pic" >
                                                <input type="hidden" name="picid" value="<?php echo $get_img->pic_id; ?>" >
                                                <input type="hidden" name="albumid" value="<?php echo $_GET['edit_album']; ?>">
                                                <div class="form-group">
                                                    <label for="pic_title">Title</label><br />
                                                    <input type="text" name="pic_title" value="<?php echo (isset($get_img->img_title)) ? $get_img->img_title : ''; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="pic_desc">Description</label><br />
                                                    <input type="text" name="pic_desc" value="<?php echo (isset($get_img->img_description)) ? $get_img->img_description : ''; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="pic_sorting">Sorting Order</label><br />
                                                    <input type="text" name="pic_sorting" value="<?php echo (isset($get_img->sorting_order)) ? $get_img->sorting_order : ''; ?>">
                                                </div>
                                                <input class="btn btn-default" type="submit" value="Update Picture">
                                            </form>
                                            <br />
<!--                                            pic_id-->

                                            <form method="post" action="<?php echo sprintf( '%s', admin_url( 'admin-post.php' ) ); ?>">
                                                <input type="hidden" name="action" value="delete_pic" >
                                                <input type="hidden" name="picid" value="<?php echo $get_img->pic_id; ?>" >
                                                <input type="hidden" name="albumid" value="<?php echo $_GET['edit_album']; ?>">
                                                <input class="btn btn-default" type="submit" value="Delete">
                                            </form>

                                            <hr />

                                        <?php endforeach; ?>
                                    </div>

                                </div>

                            </div>

                            <!-- [Add pic] Modal Start -->

                            <div class="modal fade" id="add_picture_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="myModalLabel">Add A Picture to Album</h4>
                                        </div>
                                        <div class="modal-body">

                                            <form action="<?php echo sprintf('%s', admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data">
                                                <input type="hidden" name="action" value="upload_photo">
                                                <input type="hidden" name="albumid" value="<?php echo $_GET['edit_album']; ?>">
                                                <?php wp_nonce_field('rb_image_upload', 'rb_image_upload_nonce'); ?>
                                                <div class="form-group">
                                                    <input type="file" name="photo" size="25"/>
                                                </div>
                                                <div class="form-group">
                                                    <input type="submit" class="btn btn-default" name="submit" value="Upload"/>
                                                </div>
                                            </form>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- [Add pic] Modal End -->

                        <?php endif; ?>


                    </div>
                </div>

            </div>
            <!-- post-body-content -->

            <!-- sidebar -->
            <div id="postbox-container-1" class="postbox-container">

                <div class="meta-box-sortables">

                    <div class="postbox">

                        <div class="handlediv" title="Click to toggle"><br></div>
                        <!-- Toggle -->

                        <h2 class="hndle"><span><strong><?php esc_attr_e(
                                    'RB Image Collection Gallery', 'rb_gallery'
                                ); ?></span></strong></h2>

                        <div class="inside">
                            <p>
                            <?php
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
