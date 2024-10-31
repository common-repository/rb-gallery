<?php

class Rb_gallery_admin {

	/**
	 * The ID of this plugin.
	 *
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 */
	private $version;


	private $db;

    private $album_table;

    private $pics_table;

    protected $upload_dir;


    public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        global $wpdb;
        $this->db = $wpdb;
        $this->upload_dir = wp_upload_dir();

        $this->album_table = $this->db->base_prefix . "wp_rbgallery_albums";
        $this->pics_table = $this->db->base_prefix . "wp_rbgallery_pics";

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/rb-gallery-flash.php';

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 */
	public function enqueue_styles() {
        if (isset($_GET['page']) && $_GET['page'] === 'wprb-gallery' || isset($_GET['page']) && $_GET['page'] === 'wprb_gallery_add_album') {
            wp_register_style( 'bootstrap-css', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), '3.3.7', 'all' );
            wp_enqueue_style('bootstrap-css');
        }
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rb-gallery-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 */
	public function enqueue_scripts() {
        if (isset($_GET['page']) && $_GET['page'] === 'wprb-gallery' || isset($_GET['page']) && $_GET['page'] === 'wprb_gallery_add_album') {
            wp_register_script( 'bootstrap-js', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array( 'jquery' ), '3.0.1', true );
            wp_enqueue_script('bootstrap-js');
        }
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-accordion');
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rb-gallery-admin.js', array(), $this->version, false );

	}

	public function wprb_gallery_menu() {

        add_menu_page('Official_RB_Gallery_Plugin', 'RB Gallery', 'manage_options', 'wprb-gallery', array(&$this,'wprb_gallery_options_page') );
        add_submenu_page( 'wprb-gallery', 'RB Gallery', 'RB Gallery', 'manage_options', 'wprb-gallery');
        add_submenu_page( 'wprb-gallery', 'Add Album', 'Add Album', 'manage_options', 'wprb_gallery_add_album' ,array(&$this,'wprb_gallery_add_album'));
        add_submenu_page( 'wprb-gallery', 'How to Use', 'How to Use', 'manage_options', 'wprb_gallery_notes', array(&$this,'wprb_gallery_notes'));
    }

    /*
 * @models
 *
 * */
    //add photo POST
    public function rb_admin_upload_photo()
    {
        $gallery_uploads = $this->upload_dir['basedir'] . '/rb-gallery/gallery-uploads';
        $gallery_url_path = $this->upload_dir['baseurl'] . '/rb-gallery/gallery-uploads';

        $albumid = intval( $_POST['albumid'] );

        if (isset( $_POST['rb_image_upload_nonce'] ) && wp_verify_nonce( $_POST['rb_image_upload_nonce'], 'rb_image_upload' ))
        {

                //do upload
                $tmp_name = $_FILES['photo']['tmp_name'];

if ( $_FILES['photo']['error'] !== 0) {
                if ( $_FILES['photo']['error'] !== UPLOAD_ERR_OK)
                {
                    Rb_gallery_flash::queue_flash_message("Error on File Upload.", "rb-gallery-admin");
                    exit();
                }
								if ( $_FILES['photo']['error'] == UPLOAD_ERR_INI_SIZE )
								{
										Rb_gallery_flash::queue_flash_message("Error File is too big.", "rb-gallery-admin");
										exit();
								}

								if ( $_FILES['photo']['error'] == UPLOAD_ERR_CANT_WRITE )
								{
										Rb_gallery_flash::queue_flash_message("Error File cant write to filesystem.", "rb-gallery-admin");
										exit();
								}
}
                // A non-empty file will pass this test.
                if ( $_FILES['photo']['size'] === 0 )
                {
                    Rb_gallery_flash::queue_flash_message("Error on File Empty.", "rb-gallery-admin");
                    exit();
                }

                //sanitize file name
                $photoname = filter_var($_FILES['photo']['name'], FILTER_SANITIZE_STRING);

                //removes spaces in the name
                $photoname = preg_replace('/\s+/', '_', $photoname);

                $process_file = $gallery_uploads . DIRECTORY_SEPARATOR . $photoname;

                $file_info = pathinfo($process_file);
                $dir_name = $file_info['dirname'];
                $extension = $file_info['extension'];
                $actual_filename = $file_info['filename'];
                // check exstensions
                $mimes = array('jpg', 'png', 'jpeg', 'gif', 'JPG');
                if (!in_array($extension, $mimes)) {
                    var_dump("Extensions error");
                    wp_redirect( sprintf( '%s?page=wprb-gallery&edit_album=%s', admin_url( 'admin.php' ), $albumid ) );
                    exit();
                }

                //limit filename
                $str_limit = 20;
                if ( strlen($actual_filename) > $str_limit) {
                    $actual_filename = substr($actual_filename, 0, $str_limit);
                }

                $new_filename = md5(uniqid(mt_rand())) . '.' . $extension;

                $new_process_file = $gallery_uploads . DIRECTORY_SEPARATOR . $new_filename;
                $gurl = $gallery_url_path . DIRECTORY_SEPARATOR . $new_filename;

//TODO -  make sure to default sort order to 1 and to check if a so already exists and if does increment
                $file_uploaded = $this->save_photo($albumid, $gurl, $new_process_file, $actual_filename, $new_filename);


                $success = move_uploaded_file($tmp_name, $new_process_file);

                //create thumbnail
                $this->create_thumbnail($new_process_file,$new_filename);

                if ($file_uploaded) {
                    Rb_gallery_flash::queue_flash_message("Ok, picture uploaded.", "rb-gallery-admin");
                    wp_redirect( sprintf( '%s?page=wprb-gallery&edit_album=%d', admin_url( 'admin.php' ), $albumid ) );
                    exit();
                } else {
                    //var_dump("Error uploading.");
                }

        }
    }

    public function save_photo($albumid, $galurl, $galdir, $actual_filename, $new_filename)
    {
        $thumbnail = $this->upload_dir["basedir"] . '/rb-gallery/album-thumbs/' . $new_filename;
        $thumbnailurl = $this->upload_dir["baseurl"] . '/rb-gallery/album-thumbs/' . $new_filename;

        $picorder = 1;
        $sort_check = $this->check_pic_sort_conflict($picorder,$albumid);
        if ($sort_check) {
            $picorder = $this->increment_pic_sort_id($albumid);
        }

        $ins = $this->db->insert(
            $this->pics_table,
            array(
                'album_id'          => $albumid,
                'thumbnail_url'     => $thumbnailurl,
                'thumbnail_dir'     => $thumbnail,
                'sorting_order'     => $picorder,
                'created_date'      => current_time( 'mysql' ),
                'url'               => $galurl,
                'dirpath'           => $galdir,
                'pic_name'          => $actual_filename
            ),
            array(
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
            )
        );
        if ($ins) {
            return TRUE;
        }
    }

    /*
     * Thumbnail helper
     * */
    public function create_thumbnail($galdir,$new_filename) {
        // $source_image_path, $thumbnail_image_path, $imageWidth, $imageHeigh
        $imageWidth = 240;
        $imageHeight = 160;

        $thumbnail_image_path = $this->upload_dir["basedir"] . '/rb-gallery/album-thumbs/' . $new_filename;

        list($source_image_width, $source_image_height, $source_image_type) = getimagesize($galdir);
        $source_gd_image = false;
        switch ($source_image_type) {
            case IMAGETYPE_GIF:
                $source_gd_image = imagecreatefromgif($galdir);
                break;
            case IMAGETYPE_JPEG:
                $source_gd_image = imagecreatefromjpeg($galdir);
                break;
            case IMAGETYPE_PNG:
                $source_gd_image = imagecreatefrompng($galdir);
                break;
        }
        if ($source_gd_image === false) {
            return false;
        }
        $source_aspect_ratio = $source_image_width / $source_image_height;
        if ($source_image_width > $source_image_height) {
            $real_height = $imageHeight;
            $real_width = $imageHeight * $source_aspect_ratio;
        } else if ($source_image_height > $source_image_width) {
            $real_height = $imageWidth / $source_aspect_ratio;
            $real_width = $imageWidth;

        } else {

            $real_height = $imageHeight > $imageWidth ? $imageHeight : $imageWidth;
            $real_width = $imageWidth > $imageHeight ? $imageWidth : $imageHeight;
        }


        $thumbnail_gd_image = imagecreatetruecolor($real_width, $real_height);

        if(($source_image_type == 1) || ($source_image_type==3)){
            imagealphablending($thumbnail_gd_image, false);
            imagesavealpha($thumbnail_gd_image, true);
            $transparent = imagecolorallocatealpha($thumbnail_gd_image, 255, 255, 255, 127);
            imagecolortransparent($thumbnail_gd_image, $transparent);
            imagefilledrectangle($thumbnail_gd_image, 0, 0, $real_width, $real_height, $transparent);
        }
        else
        {
            $bg_color = imagecolorallocate($thumbnail_gd_image, 255, 255, 255);
            imagefilledrectangle($thumbnail_gd_image, 0, 0, $real_width, $real_height, $bg_color);
        }
        imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $real_width, $real_height, $source_image_width, $source_image_height);
        switch ($source_image_type)
        {
            case IMAGETYPE_GIF:
                imagepng($thumbnail_gd_image, $thumbnail_image_path, 9 );
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($thumbnail_gd_image, $thumbnail_image_path, 100);
                break;
            case IMAGETYPE_PNG:
                imagepng($thumbnail_gd_image, $thumbnail_image_path, 9 );
                break;
        }
        imagedestroy($source_gd_image);
        imagedestroy($thumbnail_gd_image);
        return TRUE;
    }

    public function wprb_gallery_notes()
    {
        // if( !current_user_can( 'manage_options' ) ) {
        //     wp_die( 'You do not have sufficient permissions to access this page.' );
        // }

        //View
        include dirname( __FILE__ ) . '/partials/rb-gallery-notes-view.php';

    }

    public function wprb_gallery_add_album()
    {
        // if( !current_user_can( 'manage_options' ) ) {
        //     wp_die( 'You do not have sufficient permissions to access this page.' );
        // }

        //Handle Posts
            //Album Add
        Rb_gallery_flash::show_flash_messages();

        //View
        include dirname( __FILE__ ) . '/partials/rb-gallery-add-album-view.php';
    }

    public function wprb_gallery_options_page()
    {
        if (!current_user_can('manage_options')) {

            wp_die('You do not have sufficient permissions to access this page.');

        }

        //Routing START - eventually bring out into another static class
        $view = 'show_all_albums';
        if (isset($_GET['edit_album'])) {
            //call a method to get album data by ID & return the data
            $album_id = intval($_GET['edit_album']);
            $single_album       = $this->get_album_by_id($album_id);
            $get_images_by_id   = $this->get_images_by_id($album_id);
            $view = 'show_album_edit';
        }

        if ($view === 'show_all_albums') {
            $all_albums = $this->get_all_albums();
        }

				if (isset($_GET['delete_album'])) {
					$album_id = intval($_GET['delete_album']);
					$result = $this->_delete_album_by_id($album_id);
					if ($result) {
						Rb_gallery_flash::queue_flash_message("Successfully Deleted an Album <a class='button-secondary' href='".sprintf( '%s?page=wprb-gallery', admin_url( 'admin.php' ))."'>Refresh to see changes</a>", "rb-gallery-admin");
					}
				}
        //Routing END
        Rb_gallery_flash::show_flash_messages();
        include dirname( __FILE__ ) . '/partials/rb-gallery-admin-view.php';
    }

    public function rb_admin_edit_album()
    {
        if (isset($_POST['action']) && $_POST['action'] === 'edit_album') {
            $this->edit_album($_POST['wprbgall_albumname'],$_POST['wprbgall_albumdesc'],$_POST['wprbgall_albumorder'],$_POST['albumid']);
        }
    }

    public function rb_admin_delete_pic()
    {
        $picid = intval($_POST['picid']);
        $albumid = $_POST['albumid'];
        //get pic directory
        $dir_path = $this->db->get_row('SELECT dirpath,pic_name FROM ' . $this->pics_table . ' WHERE pic_id = '. $picid, OBJECT);
        //delete file
        unlink($dir_path->dirpath);

        $deleted = $this->db->delete( $this->pics_table, array( 'pic_id' => $picid ) );
        if ($deleted) {
            Rb_gallery_flash::queue_flash_message("Successfully deleted picture - <i>" . $dir_path->pic_name . "</i>", "rb-gallery-admin");
            wp_redirect( sprintf( '%s?page=wprb-gallery&edit_album=%s', admin_url( 'admin.php' ), $albumid) );
        }

    }

    public function rb_admin_edit_pic()
    {
        $albumid = $_POST['albumid'];
        $picid = intval($_POST['picid']);

        if ($_POST['action'] === 'edit_pic') {

            $picorder = $_POST['pic_sorting'];

            $updated = $this->db->update(
                $this->pics_table,
                array(
                    'img_title' => $_POST['pic_title'],	// string
                    'img_description' => $_POST['pic_desc'],	// integer (number)
                    'sorting_order' => $picorder
                ),
                array( 'pic_id' => $picid ),
                array(
                    '%s',
                    '%s',
                    '%s'
                ),
                array( '%d' )
            );

            if ($updated) {
                Rb_gallery_flash::queue_flash_message("Successfully updated a picture.", "rb-gallery-admin");
                wp_redirect( sprintf( '%s?page=wprb-gallery&edit_album=%s', admin_url( 'admin.php' ), $albumid) );
            }


        }
    }

    /*
     * Picture sort duplication
     * */

    public function check_pic_sort_conflict($sort_id_trying,$albumid)
    {
        // TRUE = there is a duplicate, FALSE = there is NOT a dup its OK
        $a_ord = $this->db->get_results('SELECT sorting_order FROM ' . $this->pics_table . ' WHERE album_id = ' . $albumid, OBJECT);

				$aOrdCheck = '';
				if (is_int((int)$a_ord)) {
					$aOrdCheck = (int)$a_ord;
				} else {
					$aOrdCheck = (int)$a_ord[0]->sorting_order;
				}
        if ($aOrdCheck > 0) {
            //check for duplicate sorts
            $match = array();
            foreach ($a_ord as $ao) {
                if ((int)$sort_id_trying === (int)$ao->sorting_order) {
                    $match[] = $ao->sorting_order;
                }
            }

            if (count($match) > 0) {
                //there is a duplicate, find highest number and add one
                return TRUE;
            } else {
                return FALSE;
            }
        }

    }

    public function increment_pic_sort_id($albumid)
    {
        // gets max sort id and increments it by one
        $a_ord = $this->db->get_row('SELECT MAX(sorting_order) as max_sort FROM ' . $this->pics_table . ' WHERE album_id = ' . $albumid, OBJECT);
        return $a_ord->max_sort + 1;
    }

    public function edit_album($albumname,$albumdesc,$albumorder,$albumid)
    {
        $updated = $this->db->update(
            $this->album_table,
            array(
                'album_name' => $albumname,	// string
                'description' => $albumdesc,	// integer (number)
                'album_order' => $albumorder
            ),
            array( 'album_id' => $albumid ),
            array(
                '%s',
                '%s',
                '%d'
            ),
            array( '%d' )
        );

        if ($updated) {
            Rb_gallery_flash::queue_flash_message("Successfully updated an album.", "rb-gallery-admin");
            wp_redirect( sprintf( '%s?page=wprb-gallery&edit_album=%s', admin_url( 'admin.php' ), $albumid) );
        } else {
            //good place to log error?
            wp_redirect( sprintf( '%s?page=wprb-gallery&edit_album=%s', admin_url( 'admin.php' ), $albumid) );
        }
    }

    public function get_album_by_id($album_id)
    {
        $album = $this->db->get_row('SELECT * FROM ' . $this->album_table . ' WHERE album_id = '. $album_id, OBJECT);
        return $album;
    }

		public function _delete_album_by_id($album_id)
		{
			$images = $this->get_images_by_id($album_id);
			if (count($images) > 0) {
				$ids = array();
				$thumbdirs = array();
				$bigdirs = array();
				foreach($images as $image) {
					$ids[] = $image->pic_id;
					$thumbdirs[] = $image->thumbnail_dir;
					$bigdirs[] = $image->dirpath;
				}
				foreach($bigdirs as $bigdir) {
					if (file_exists($bigdir)) {
						unlink($bigdir);
					}
				}
				foreach($thumbdirs as $thumbdir) {
					if (file_exists($thumbdir)) {
						unlink($thumbdir);
					}
				}
				foreach($ids as $id) {
					$this->db->delete( $this->pics_table, array( 'pic_id' => $id ) );
				}
			}
				// done deleting images, now album
			//first delete images in album
			// then delete album
			$res = $this->db->delete( $this->album_table, array( 'album_id' => $album_id ) );
			return $res;

		}

    public function get_images_by_id($album_id) {
        $images = $this->db->get_results('SELECT * FROM ' . $this->pics_table . ' WHERE album_id = '. $album_id, OBJECT);
        return $images;
    }

    public function get_all_albums()
    {
        $albums = $this->db->get_results('SELECT * FROM ' . $this->album_table, OBJECT);
        if (count($albums) > 0) {
            return $albums;
        }
    }

    public function rb_admin_add_album() {
        if (isset($_POST['action']) && $_POST['action'] === 'add_album') {
            $add_res = $this->add_album($_POST['wprbgall_albumname'],$_POST['wprbgall_albumdesc'],$_POST['wprbgall_albumorder']);
            if ($add_res) {
                Rb_gallery_flash::queue_flash_message("Successfully added an album. See albums on the RB Gallery link.", "rb-gellery-admin");
                wp_redirect( sprintf( '%s?page=wprb_gallery_add_album', admin_url( 'admin.php' ) ) );
            }
        }
    }

    public function add_album($albumname,$albumdesc,$albumorder) {
        //check sort, if same than increment
        $sort_check = $this->check_album_sort_conflict($albumorder);
        if ($sort_check) {
            $albumorder = $this->increment_sort_id();
        }

        $rez = $this->db->insert($this->album_table, array("album_name" => sanitize_text_field($albumname), "album_date" => date('Y-m-d'), "description" => sanitize_text_field($albumdesc), "album_order" => sanitize_text_field($albumorder) ));

        return $rez;

    }

    public function check_album_sort_conflict($sort_id_trying)
    {
        // TRUE = there is a duplicate, FALSE = there is NOT a dup its OK
        $a_ord = $this->db->get_results('SELECT album_order FROM ' . $this->album_table, OBJECT);

        if (count($a_ord) > 0) {
            //check for duplicate sorts
            $match = array();
            foreach ($a_ord as $ao) {
                if ($sort_id_trying === $ao->album_order) {
                    $match[] = $ao->album_order;
                }
            }
            if (count($match) > 0) {
                //there is a duplicate, find highest number and add one
                return TRUE;
            } else {
                return FALSE;
            }
        }

    }

    /*
     * returns int
     * */

    public function increment_sort_id()
    {
        // gets max sort id and increments it by one
        $a_ord = $this->db->get_row('SELECT MAX(album_order) as max_sort FROM ' . $this->album_table, OBJECT);
        return $a_ord->max_sort + 1;
    }


}

?>
