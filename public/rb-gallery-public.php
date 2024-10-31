<?php


class Rb_gallery_public {

	/**
	 * The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 */
	private $version;

    public $post;

    protected $db;

    private $album_table;

    private $pics_table;

	/**
	 * Initialize the class and set its properties.
	 *
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

        global $post;
        $this->post = $post;

        global $wpdb;
        $this->db = $wpdb;

        $this->album_table = $this->db->base_prefix . "wp_rbgallery_albums";
        $this->pics_table = $this->db->base_prefix . "wp_rbgallery_pics";

        add_shortcode( 'wprb_gallery', array(&$this, 'wprb_gallery_shortcode') );
        add_action( 'wp_ajax_the_ajax_hook', array(&$this, 'display_all_album_data') );
        //Public AJAX hook
        add_action( 'wp_ajax_nopriv_the_ajax_hook', array(&$this, 'display_all_album_data') );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 */
	public function enqueue_styles() {

        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rb-gallery-album.css', array(), $this->version, 'all' );
        wp_enqueue_style( $this->plugin_name . '-thumbs', plugin_dir_url( __FILE__ ) . 'css/rb-gallery-thumbs.css', array(), $this->version, 'all' );
        wp_enqueue_style( $this->plugin_name . '-base', plugin_dir_url( __FILE__ ) . 'css/rb-gallery-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 */
	public function enqueue_scripts() {

        wp_enqueue_script( 'ajax-script', plugin_dir_url( __FILE__ ) . 'js/rb-gallery-public.js', array('jquery') );
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rb-gallery-album.js', array(), $this->version, false );
        wp_enqueue_script( $this->plugin_name . '-thumb', plugin_dir_url( __FILE__ ) . 'js/rb-gallery-thumbs.js', array(), $this->version, false );
        wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

	}

	public function register_shortcodes() {
		add_shortcode( 'rbgall_short', array( $this, 'wprb_gallery_shortcode' ) );
	}

    public function unique_multidim_array($array, $key) {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }

    public function display_all_album_data() {

	    if ($_POST['action'] === 'the_ajax_hook') {

        $results = $this->db->get_results('select ra.album_id,ra.album_name,ra.album_date,ra.description,ra.album_order,rp.pic_id,rp.img_title,rp.img_description,rp.thumbnail_url,rp.thumbnail_dir,rp.sorting_order,rp.url,rp.dirpath,rp.pic_name
        FROM '.$this->album_table.' ra
        INNER JOIN '.$this->pics_table.' rp
        ON ra.album_id = rp.album_id
        ORDER BY sorting_order', ARRAY_A);

            $albums = array();
            foreach($results as $data)
            {
                $albums[] = array(
                    "id" => $data['album_id'],
                    "album_name" => $data['album_name'],
                    "album_date" => $data['album_date'],
                    "description" => $data['description'],
                    "album_order" => $data['album_order']
                );

            }

            $albums = $this->unique_multidim_array($albums, "id");

            $pics = array();
            foreach($results as $pic)
            {
                $pics[] = array(
                    "id" => $pic['album_id'],
                    "picid" => $pic['pic_id'],
                    "img_title" => $pic['img_title'],
                    "img_description" => $pic['img_description'],
                    "thumbnail_url" => $pic['thumbnail_url'],
                    "sorting_order" => $pic['sorting_order'],
                    "url" => $pic['url'],
                    "dirpath" => $pic['dirpath'],
                    "pic_name" => $pic['pic_name']
                );
            }

            $final = array();
            foreach ($albums as $album) {
                $falbum = array(
                    "id" => $album['id'],
                    "album_name" => $album['album_name'],
                    "album_date" => $album['album_date'],
                    "description" => $album['description'],
                    "album_order" => $album['album_order']
                );
                $fpics = array();
                foreach ($pics as $pic) {
                    if ((int)$album['id'] === (int)$pic['id']) {
                        $fpics[] = $pic;
                    }
                }
                $final[] = array($falbum,$fpics);

            }
            echo json_encode($final);
        }

        wp_die();
    }

	public function wprb_gallery_shortcode( $atts )
    {

//        $this->post
//        extract( shortcode_atts( array(
//            'album_display' => 'hor',
//            'album_size' => 'med',
//            'line_h' => '200px'
//        ), $atts ) );

        //get album data
        //$album_data = $this->get_album_data();

        ob_start();
        include dirname( __FILE__ ) . '/partials/rb-gallery-public-view.php';
        $content = ob_get_clean();
        return $content;

    }

    /*
     * Models
     *
     * */
}

?>
