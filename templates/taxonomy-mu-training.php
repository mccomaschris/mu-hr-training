<?php
/**
 * Default template for displaying Training listings
 *
 * @package MU HR Training
 */

require WP_PLUGIN_DIR . '/mu-hr-train/vendor/autoload.php';
use Carbon\Carbon;

get_header();

require get_template_directory() . '/template-parts/hero/no-hero.php';
?>
<div class="w-full xl:max-w-screen-xl px-6 xl:px-0 xl:mx-auto pt-4 lg:pt-12 pb-16">
	<div class="flex flex-wrap mx-0 lg:-mx-6 px-0">
		<div  class="w-full lg:w-3/4 lg:px-6">

			<header class="page-header">
				<?php
				the_archive_title( '<h1 class="entry-title font-sans uppercase font-semibold text-gray-700 mb-4 text-3xl lg:text-4xl">', '</h1>' );
				?>
			</header><!-- .page-header -->
			<?php
			echo 'Time: ' . date('g:ia');
			if ( have_posts() ) :
				while ( have_posts() ) {
					the_post();

					$registrations = get_posts(
						array(
							'numberposts' => -1,
							'post_type'   => 'mu-registrations',
							'meta_key'    => 'muhr_registration_training_session',
							'meta_value'  => get_the_ID(),
						)
					);

					wp_reset_postdata();

					$seats_total = get_field( 'mu_training_training_seats', get_the_ID() );

					$seats_left = intval( $seats_total ) - intval( count( $registrations ) );

					?>
					<div id="course<?php echo esc_attr( get_the_ID() ); ?>" class="flex flex-col border-gray-100 border border-t border-b rounded my-6">
						<div class="border-b border-gray-100 flex flex-row items-start py-4 px-4 lg:px-6">
							<div class="flex-col flex w-12 lg:w-16 mx-auto">
								<div class="bg-green text-white text-xl font-bold uppercase py-1 rounded-t text-center"><?php echo esc_attr( Carbon::parse( get_field( 'mu_training_start_time', get_the_ID() ) )->format( 'M' ) ); ?></div>
								<div class="bg-gray-100 text-sm lg:text-xl font-bold uppercase py-1 rounded-b text-center"><?php echo esc_attr( Carbon::parse( get_field( 'mu_training_start_time', get_the_ID() ) )->format( 'j' ) ); ?></div>
							</div>
							<div class="ml-4 lg:ml-6 flex-1">
								<div class="">
									<?php
									if ( $seats_left > 0 ) {
										?>
										<a href="<?php echo esc_url( home_url() ); ?>/training/registration/?courseid=<?php echo esc_attr( get_the_ID() ); ?>" class="font-semibold"><?php the_title(); ?></a>
									<?php } else { ?>
										<span class="font-semibold"><?php the_title(); ?></span>
									<?php } ?>
									<div class="text-sm"><span class="font-semibold">Location:</span> <?php echo esc_attr( get_field( 'mu_training_training_location', get_the_ID() ) ); ?></div>
									<div class="text-sm"><?php echo esc_attr( Carbon::parse( get_field( 'mu_training_start_time', get_the_ID() ) )->format( 'F j, g:ia' ) ); ?> - <?php echo esc_attr( Carbon::parse( get_field( 'mu_training_end_time', get_the_ID() ) )->format( 'g:ia' ) ); ?> · <span class="font-semibold"><?php echo esc_attr( $seats_left ); ?></span> spots remaining</div> <span class="hidden">Seats taken: <?php echo intval( count( $registrations ) ); ?></span>
									<div class="text-sm"><span class="font-semibold">Instructor:</span> <?php echo esc_attr( get_field( 'mu_training_instructor', get_the_ID() )['instructor_name'] ); ?> (<a href="<?php echo esc_url( home_url() ); ?>/training/registered-list/?courseid=<?php echo esc_attr( get_the_ID() ); ?>">Instructor Access</a>)</div>
									<?php
									if ( get_field( 'mu_training_course_description', get_the_ID() ) ) {
										?>
										<div class="my-4"><?php echo wp_kses_post( get_field( 'mu_training_course_description', get_the_ID() ) ); ?></div>
									<?php } else { ?>
										<div class="my-4"><?php echo wp_kses_post( term_description() ); ?></div>
									<?php } ?>
								</div>
								<div class="mt-6">
									<?php
									if ( $seats_left > 0 ) {
										?>
										<a href="<?php echo esc_url( home_url() ); ?>/training/registration/?courseid=<?php echo esc_attr( get_the_ID() ); ?>" class="btn btn-green">Register</a>
									<?php } else { ?>
										<div class="mt-6 btn bg-gray-300 text-gray-500 cursor-not-allowed">Course Full</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
			endif;
			?>
		</div>
		<div class="w-full lg:w-1/4 lg:px-6 mt-6 lg:mt-0">
			<?php dynamic_sidebar( 'sidebar-1' ); ?>
		</div>
	</div>
</div>

<!-- Footer -->
<?php get_footer(); ?>
