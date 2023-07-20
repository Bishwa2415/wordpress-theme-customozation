<?php


<!---------- Custom Blog post With Load More Buttton Shortcode ----------->




function job_shortcode($atts){
	ob_start();
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	$query = new WP_Query( array(
    	'post_type' => 'vacancy',
    	'posts_per_page' => 3,
    	'order' => 'ASC',
    	'orderby' => 'title',
    	'paged' => $paged,
	));
	if ( $query->have_posts() ) {
    	?>
    	<div class="container my-custom-container">
        	<div class="row">
            	<?php
            	while ($query->have_posts()) {
                	$query->the_post();
                	ob_start(); // Start output buffering
                	?>
                	<article>
                    	<h2><?php the_title(); ?></h2>
                    	<div><?php the_post_thumbnail(); ?></div>
                    	<p><?php the_excerpt();?></p>
                	</article>
                	<?php
                	$article_html = ob_get_clean(); // Get the buffered output
                	echo $article_html; // Output the article element
            	}
            	wp_reset_postdata();
            	?>
        	</div> <!-- Close row -->
    	</div> <!-- Close container -->
    	<div class="load-more-container">
        	<button class="load-more-button" data-page="<?php echo $paged + 1; ?>">Load More</button>
    	</div>
    	<script>
    	document.addEventListener('DOMContentLoaded', function() {
        	var loadMoreButton = document.querySelector('.load-more-button');
        	var container = document.querySelector('.load-more-container');
        	if (loadMoreButton) {
            	loadMoreButton.addEventListener('click', function() {
                	var button = this;
                	button.disabled = true;
                	button.innerHTML = 'Loading...';
                	var xhr = new XMLHttpRequest();
                	xhr.open('GET', '<?php echo admin_url('admin-ajax.php'); ?>?action=load_more_jobs&page=' + button.dataset.page);
                	xhr.onreadystatechange = function() {
                    	if (xhr.readyState === XMLHttpRequest.DONE) {
                        	if (xhr.status === 200) {
                            	var response = xhr.responseText;
                            	if (response.trim()) {
                                	var parser = new DOMParser();
                                	var doc = parser.parseFromString(response, 'text/html');
                                	var articles = doc.querySelectorAll('article');
                                	if (articles.length > 0) {
                                    	var row = document.querySelector('.row');
                                    	articles.forEach(function(article) {
                                        	row.appendChild(article);
                                    	});
                                    	button.dataset.page = parseInt(button.dataset.page) + 1;
                                    	button.disabled = false;
                                    	button.innerHTML = 'Load More';
                                	} else {
                                    	container.parentNode.removeChild(container);
                                	}
                            	} else {
                                	container.parentNode.removeChild(container);
                            	}
                        	} else {
                            	alert('Error: ' + xhr.status);
                        	}
                    	}
                	};
                	xhr.send();
            	});
        	}
    	});
    	</script>
    	<?php
	}
	$myvariable = ob_get_clean();
	return $myvariable;
}

add_shortcode( 'job', 'job_shortcode' );

function load_more_jobs() {
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$query = new WP_Query( array(
    	'post_type' => 'vacancy',
    	'posts_per_page' => 3,
    	'order' => 'ASC',
    	'orderby' => 'title',
    	'paged' => $page,
	));
	if ( $query->have_posts() ) {
    	ob_start(); // Start output buffering
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
    	$articles_html = ob_get_clean(); // Get the buffered output
    	echo '<div class="row">' . $articles_html . '</div>'; // Output the row with article elements
    	wp_reset_postdata();
	}
	wp_die();
}

add_action( 'wp_ajax_load_more_jobs', 'load_more_jobs' );
add_action( 'wp_ajax_nopriv_load_more_jobs', 'load_more_jobs' );
