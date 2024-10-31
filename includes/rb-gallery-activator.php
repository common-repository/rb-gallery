<?php


class Rb_gallery_activator {

	/**
	 * Short Description. (use period)
	 *
	 */

	public static function activate() {

        global $wpdb;

        $sql = "CREATE TABLE {$wpdb->base_prefix}wp_rbgallery_albums (
		  album_id int(10) unsigned NOT NULL AUTO_INCREMENT,
		  album_name varchar(100) DEFAULT NULL,
		  album_date date DEFAULT NULL,
		  description text,
		  album_order int(10) DEFAULT NULL,
		  PRIMARY KEY (album_id)
		  );";

        require_once( get_home_path() . '/wp-admin/includes/upgrade.php' );

        dbDelta( $sql );

        $sql = "CREATE TABLE {$wpdb->base_prefix}wp_rbgallery_pics (
		  pic_id int(10) unsigned NOT NULL AUTO_INCREMENT,
		  album_id int(10) unsigned NOT NULL,
		  title text,
		  description text,
		  thumbnail_url text DEFAULT NULL,
			thumbnail_dir text DEFAULT NULL,
		  sorting_order int(20) DEFAULT NULL,
			img_title text DEFAULT NULL,
			img_description text DEFAULT NULL,
		  created_date date DEFAULT NULL,
		  url varchar(250) DEFAULT NULL,
		  video int(10) DEFAULT NULL,
			dirpath text DEFAULT NULL,
		  tags text,
		  pic_name text DEFAULT NULL,
		  PRIMARY KEY (pic_id)
		  );";
        require_once( get_home_path() . '/wp-admin/includes/upgrade.php' );
        dbDelta( $sql );


        // make gallery directories
        $rb_gallery = get_home_path()  . 'wp-content/uploads/rb-gallery/';
        $album_thumbs = get_home_path()  . 'wp-content/uploads/rb-gallery/album-thumbs';
        //$dontremove = get_home_path() .  'wp-content/uploads/rb-gallery/dontremove';
        $gallery_uploads = get_home_path()  . 'wp-content/uploads/rb-gallery/gallery-uploads';

        if (!is_dir($rb_gallery)) {
            mkdir($rb_gallery);
        }

        if (!is_dir($album_thumbs)) {
            mkdir($album_thumbs);
        }

//        if (!is_dir($dontremove)) {
//            mkdir($dontremove);
//        }

        if (!is_dir($gallery_uploads)) {
            mkdir($gallery_uploads);
        }

        // move file to new location
//        $old_loc = plugin_dir_path(__FILE__) .'admin/images/dontremove.jpg';
//        $new_loc = get_home_path() .  'wp-content/uploads/rb-gallery/dontremove/dontremove.jpg';
//        rename($old_loc , $new_loc);

	}

}
