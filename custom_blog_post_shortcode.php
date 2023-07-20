<?php


<!----------------- Custom Blog post With Shortcode ------------------ -->

function job_shortcode($atts){
    ob_start();
    $query = new WP_Query( array(
   	 'post_type' => 'vacancy',
   	 'posts_per_page' => 3,
   	 'order' => 'ASC',
   	 'orderby' => 'title',   	 
    ));
    if ( $query->have_posts() ) {
   	 ?>
   	 <div class="container">
   		 <div class="row">
   			 <?php
   			 while ($query->have_posts()) {
   				 $query->the_post();
   			 ?>
   			 <article>
   				 <h2><?php the_title(); ?></h2>
   				 <div><?php the_post_thumbnail(); ?></div>
   				 <p><?php the_excerpt();?></p>
   			 </article>
   			 <?php
   			 }
   			 wp_reset_postdata();
   			 ?>
   		 </div>
   	 </div>
   	 <?php
   	 $myvariable = ob_get_clean();
   	 return $myvariable;
    }
}

add_shortcode( 'job', 'job_shortcode' );
