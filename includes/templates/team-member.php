<?php 
if ( ! empty( $tmpl_data ) ) {
	$is_js_tmpl = false;
	// Setup some defaults - just in case. If you're certain in your input you can ignore this
	$defaults = array(
		'position'	=> '',
		'short_bio'	=> '',
		'name'		=> '',
		'image'		=> '',
	);

	$data = wp_parse_args( $tmpl_data, $defaults );
	if ( $data['image'] ) {
		$data['image'] = '<img alt="' . esc_attr( $data['name'] ) . '" src="' . esc_url( $data['image'] ) . '" />';
	} else {
		$data['image'] = '<img alt="' . esc_attr( $data['name'] ) . '" src="' . esc_url( get_bloginfo( 'template_directory' ) . '/images/faceless-team-member.jpg' ) . '" />';
	}

	if ( $data['position'] ) {
		$data['position'] = '<div class="t-position">' . $data['position'] . '</div>';
	}

	$data['short_bio'] = wpautop( $data['short_bio'] );
} else {
	$is_js_tmpl = true;

	// These are our placeholders for the JS template
	$data = array(
		'position'	=> '<# if ( data.position ) { #><div class="t-position">{{ data.position }}</div><# } #>',
		'short_bio'	=> '{{data.short_bio}}',
		'name'		=> '{{data.name}}',
		'image' => '<# if ( data.image ) { #><img alt="{{data.name}}" src="{{data.image}}" /><# } else { #><img src="' . esc_url( get_bloginfo( 'template_directory' ) . '/images/faceless-team-member.jpg' ) . '" alt="" /><# } #>',
	);
} ?>
<?php if ( $is_js_tmpl ) : ?>
	<script type="text/html" id="tmpl-team-member">
<?php endif; ?>
	<div class="cell-3">
		<div class="team-box">
			<div class="team-img">
				<?php echo $data['image']; ?>
				<h3><?php echo $data['name']; ?></h3>
			</div>
			<div class="team-details">
				<h3 class="gry-bg"><?php echo $data['name']; ?></h3>
				<?php echo $data['position']; ?>
				<?php echo $data['short_bio']; ?>
			</div>
		</div>
	</div>
<?php if ( $is_js_tmpl ) : ?>
	</script>
<?php endif; ?>