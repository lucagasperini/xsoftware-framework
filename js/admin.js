function confirm_box(text) {
        if(text) {
                return confirm(text);
        } else {
                return confirm("Are you sure?");
        }
} 
 
var media_uploader = null;
var image_data;

function wp_media_gallery_url(id_input, id_image)
{
        media_uploader = wp.media({
                title:    "Insert Media",
                button:   {
                text: "Upload Image"
                },
                multiple: false
        });

        media_uploader.on( 'select', function() {

                image_data = media_uploader.state().get( 'selection' ).first().toJSON();
                document.getElementById(id_input).value = image_data['url'];
                document.getElementById(id_image).src = image_data['url'];
                for ( var image_property in image_data ) {
                        console.log( image_property + ': ' + image_data[ image_property ] );
                }

        });
        media_uploader.open();
        
        
}

function dump(obj) {
    var out = '';
    for (var i in obj) {
        out += i + ": " + obj[i] + "\n";
    }

    alert(out);
}
