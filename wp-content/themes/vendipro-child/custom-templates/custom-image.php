<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
global $thumbnail_post;
var_dump($thumbnail_post);
?>


<div class="cat-thumb">
    <?php if ( ! empty( $thumbnail_post->post_title ) || ! empty( $thumbnail_post->post_excerpt ) ) : ?>
        <div class="cat-thumb-overlay">
            <?php echo ( ! empty( $thumbnail_post->post_title ) ? '<h3>' . $thumbnail_post->post_title . '</h3>' : '' ); ?>
            <?php echo ( ! empty( $thumbnail_post->post_excerpt ) ? '<i>' . $thumbnail_post->post_excerpt . '</i>' : '' ); ?>
        </div>
    <?php endif; ?>
    <img src="<?php echo $image ?>" alt="<?php echo get_post_meta( $thumbnail_post->ID, '_wp_attachment_image_alt', true ); ?>" />
</div>