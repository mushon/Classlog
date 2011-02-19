<?php
//Skip the image attachment page, go directly to the file.
$attachment = wp_get_attachment_image_src($post->ID, 'full');
wp_redirect($attachment[0]);
?>