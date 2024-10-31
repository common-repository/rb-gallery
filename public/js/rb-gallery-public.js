jQuery(document).ready(function($){

    var Rb_gallery_front_end = (function ($) {

        var albumDataGlobal;
        var globAlbumData = [];

        var init = function () {

            // eventlisteners();

            $.ajax({
                type: 'post',
                url: ajax_object.ajax_url,
                data: {
                    'action' : 'the_ajax_hook'
                },
                success: function(response) {
                  globAlbumData.push(JSON.parse(response));
                  processView(globAlbumData[0]);
                  eventlisteners();

                    //var album_data = JSON.parse(response);
                    //albumDataGlobal = album_data;
                }
            });
        };

        var processView = function(album_data) {
          var allalbumsF = [];

          // Can change title tag
          var html_h_tag_base = 'h3';
          var html_h_tag = ['<' + html_h_tag_base + '>','</' + html_h_tag_base + '>'];

          $.each(album_data, function (index, album) {
          //  albumMeta.push({'id':album[0].id, 'name': album[0].album_name});
              var top = '<div class="wprb_album_single" >';
              var title = html_h_tag[0] + album[0].album_name + html_h_tag[1];

              var images = [];

              $.each(album[1], function (idx, pic) {
                if (Number(pic.sorting_order) === 1) {
                // TODO  - if sort order is lowest, only add this to array so only shows one pic on admin end
                  var html = '<a class="getrbgall" data-id="' + pic.id + '" href="javascript:;"><img id="getrbgall" type="image" src=" ' + pic.thumbnail_url + ' " /></a>';
                  images.push(html);
                }
                // todo - else show default pic
              });
              var endhtml = '</div>';
              allalbumsF.push(top + title + images.join("") + endhtml );
          });
          $(".wprb_album_group").html(allalbumsF);
        };

        var eventlisteners = function() {
            $(document.body).on('click','.getrbgall',function (e) {
                var album_id = $(this).data('id');
                if (album_id) {
                    trigger_album_gallery(album_id);
                }
                e.preventDefault();
            });
        };

        var trigger_album_gallery = function (album_id) {
            var display_html_line = [];
            $.each(globAlbumData[0], function (index, album) {
                if (Number(album[0].id) === Number(album_id)) {
                    $.each(album[1], function (idx,pic) {
                        var live_album = '<a href="' + pic.url + '" data-rbgallery-group="thumb" class="rbgallery-thumbs" title=""><img src="' + pic.thumbnail_url + '" /></a>';
                        display_html_line.push(live_album);
                    });
                }
            });


            var js =  ' <script>' +
                '     jQuery(document).ready(function($) {' +
                '         $(".rbgallery").rbgallery();' +
                '         $(".rbgallery-thumbs").rbgallery({' +
                '             prevEffect : "none",' +
                '             nextEffect : "none",' +
                '             closeBtn  : true,' +
                '             arrows    : true,' +
                '             nextClick : true,' +
                '             helpers : {' +
                '                 thumbs : {' +
                '                     width  : 50,' +
                '                     height : 50' +
                '                 }' +
                '             }' +
                '         }).eq(0).trigger("click");' +
                '     }); ' +
                ' </script>';

            $(".show-rbgallery-div").html(display_html_line + js);
        };

        return {
            init:init
        }

    })(jQuery);

    Rb_gallery_front_end.init();

});
