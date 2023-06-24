<?php
defined('ABSPATH') || die("Nice Try"); 

add_action( 'show_user_profile', 'rudr_profile_fields' );
add_action( 'edit_user_profile', 'rudr_profile_fields' );

function rudr_profile_fields( $user ) {

	// let's get custom field values
	$city = get_user_meta( $user->ID, 'city', true );
	// what about making a default value?
	$drinks = ( $drinks = get_user_meta( $user->ID, 'drinks', true ) ) ? $drinks : 'wine';

	?>
		<h3>Additional Information</h3>
		<table class="form-table">
	 		<tr>
				<th><label for="city">City</label></th>
		 		<td>
					<input type="text" name="city" id="city" value="<?php echo esc_attr( $city ) ?>" class="regular-text" />
				</td>
			</tr>
			<tr>
				<th>Drinks</th>
		 		<td>
					<ul>
			 			<li>
							<label>
								<input type="radio" value="wine" name="drinks"<?php checked( $drinks, 'wine' ) ?> /> Wine
							</label>
						</li>
						<li>
							<label>
								<input type="radio" value="coffee" name="drinks"<?php checked( $drinks, 'coffee' ) ?> /> Coffee
							</label>
						</li>
						<li>
							<label>
								<input type="radio" value="water" name="drinks"<?php checked( $drinks, 'water' ) ?> /> Water
							</label>
						</li>
			 		</ul>
				</td>
			</tr>
		</table>
	<?php
}


add_action( 'personal_options_update', 'rudr_save_profile_fields' );
add_action( 'edit_user_profile_update', 'rudr_save_profile_fields' );
 
function rudr_save_profile_fields( $user_id ) {
	
	if( ! isset( $_POST[ '_wpnonce' ] ) || ! wp_verify_nonce( $_POST[ '_wpnonce' ], 'update-user_' . $user_id ) ) {
		return;
	}
	
	if( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}
 
	// update_user_meta( $user_id, 'city', sanitize_text_field( $_POST[ 'city' ] ) );
	// update_user_meta( $user_id, 'drinks', sanitize_text_field( $_POST[ 'drinks' ] ) );
 
    
$drinks = in_array( $_POST[ 'drinks' ], array( 'wine', 'coffee', 'water' ) ) ? $_POST[ 'drinks' ] : 'wine';
update_user_meta( $user_id, 'drinks', $drinks );
}