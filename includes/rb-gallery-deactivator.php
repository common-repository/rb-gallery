<?php

class Rb_gallery_deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 */
	public static function deactivate() {

        global $wpdb;


        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->base_prefix}wp_rbgallery_albums");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->base_prefix}wp_rbgallery_pics");

        // delete gallery directories
        $rb_gallery = get_home_path()  . 'wp-content/uploads/rb-gallery/';

        if (file_exists($rb_gallery)) {
            self::remove_dirs($rb_gallery);
        }

	}

	public static function remove_dirs($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") self::remove_dirs($dir."/".$object); else unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

}

?>