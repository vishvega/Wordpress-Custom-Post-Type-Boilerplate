<?php
/*
 * Template Name: CPT By Vi Single Template
 * Template Post Type: post, page, Butchers
 */
   
 get_header();  ?>

 <h1 style="margin:15rem 0">his is the Single Page</h1>

 <?php 
 global $post;

    // retrieve the global notice for the current post
    $cpt_vi_bt_nickname_saved = esc_attr( get_post_meta( $post->ID, '_cpt_vi_bt_nickname', true ) );
    
    $cpt_vi_bt_nickname_saved   = esc_attr( get_post_meta( $post->ID, 'cpt_vi_metabox_val1'  ) );
            $cpt_vi_bt_weapon_saved     = esc_attr( get_post_meta( $post->ID, 'cpt_vi_metabox_val2'  ) );

    $global_notice = get_post_meta( $post->ID );
    // $global_notice = get_post_meta( $post->ID, '_cpt_vi_metabox_val1' );
 echo "<pre>";
        print_r($global_notice);
        echo "</pre>";


 ?>

 <?php get_footer() ?>