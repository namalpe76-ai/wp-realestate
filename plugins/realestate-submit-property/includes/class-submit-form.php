<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RESP_Submit_Form {

	public function __construct() {
		add_shortcode( 'submit_property_form', array( $this, 'render_form' ) );
	}

	public function render_form() {
		ob_start();
		?>
		<div class="resp-form-wrapper" id="resp-submit-form-wrapper">
			<form id="resp-submit-form" class="resp-submit-form" enctype="multipart/form-data" novalidate>
				<input type="hidden" name="action" value="resp_submit_property">
				<?php wp_nonce_field( 'resp_submit_nonce', 'resp_nonce' ); ?>
				<input type="text" name="website" class="resp-honeypot" tabindex="-1" autocomplete="off">

				<div class="resp-form-header">
					<h2><?php esc_html_e( 'Submit Your Property', 'realestate-submit-property' ); ?></h2>
					<p><?php esc_html_e( 'Fill in the details below to submit your property for listing.', 'realestate-submit-property' ); ?></p>
				</div>

				<div class="resp-form-section" data-section="owner">
					<h3 class="resp-section-title"><?php esc_html_e( 'Owner Information', 'realestate-submit-property' ); ?></h3>
					<div class="resp-form-grid">
						<div class="resp-field-group">
							<label for="owner_name"><?php esc_html_e( 'Owner Name', 'realestate-submit-property' ); ?> <span class="required">*</span></label>
							<input type="text" id="owner_name" name="owner_name" required maxlength="100" placeholder="<?php esc_attr_e( 'Full Name', 'realestate-submit-property' ); ?>">
							<span class="resp-error"></span>
						</div>
						<div class="resp-field-group">
							<label for="owner_telephone"><?php esc_html_e( 'Telephone', 'realestate-submit-property' ); ?> <span class="required">*</span></label>
							<input type="tel" id="owner_telephone" name="owner_telephone" required maxlength="20" placeholder="<?php esc_attr_e( 'Phone Number', 'realestate-submit-property' ); ?>">
							<span class="resp-error"></span>
						</div>
						<div class="resp-field-group">
							<label for="owner_email"><?php esc_html_e( 'Email', 'realestate-submit-property' ); ?> <span class="required">*</span></label>
							<input type="email" id="owner_email" name="owner_email" required maxlength="100" placeholder="<?php esc_attr_e( 'Email Address', 'realestate-submit-property' ); ?>">
							<span class="resp-error"></span>
						</div>
					</div>
				</div>

				<div class="resp-form-section" data-section="property">
					<h3 class="resp-section-title"><?php esc_html_e( 'Property Details', 'realestate-submit-property' ); ?></h3>
					<div class="resp-form-grid">
						<div class="resp-field-group">
							<label for="property_type"><?php esc_html_e( 'Property Type', 'realestate-submit-property' ); ?> <span class="required">*</span></label>
							<select id="property_type" name="property_type" required>
								<option value=""><?php esc_html_e( 'Select Property Type', 'realestate-submit-property' ); ?></option>
								<option value="house"><?php esc_html_e( 'House', 'realestate-submit-property' ); ?></option>
								<option value="apartment"><?php esc_html_e( 'Apartment', 'realestate-submit-property' ); ?></option>
								<option value="land"><?php esc_html_e( 'Land', 'realestate-submit-property' ); ?></option>
								<option value="commercial"><?php esc_html_e( 'Commercial Property', 'realestate-submit-property' ); ?></option>
								<option value="office"><?php esc_html_e( 'Office', 'realestate-submit-property' ); ?></option>
								<option value="shop"><?php esc_html_e( 'Shop', 'realestate-submit-property' ); ?></option>
								<option value="warehouse"><?php esc_html_e( 'Warehouse', 'realestate-submit-property' ); ?></option>
								<option value="villa"><?php esc_html_e( 'Villa', 'realestate-submit-property' ); ?></option>
							</select>
							<span class="resp-error"></span>
						</div>
						<div class="resp-field-group">
							<label for="property_location"><?php esc_html_e( 'Location', 'realestate-submit-property' ); ?> <span class="required">*</span></label>
							<input type="text" id="property_location" name="property_location" required maxlength="200" placeholder="<?php esc_attr_e( 'City / Area', 'realestate-submit-property' ); ?>">
							<span class="resp-error"></span>
						</div>
						<div class="resp-field-group resp-field-full">
							<label for="property_address"><?php esc_html_e( 'Full Address', 'realestate-submit-property' ); ?> <span class="required">*</span></label>
							<textarea id="property_address" name="property_address" required rows="3" maxlength="500" placeholder="<?php esc_attr_e( 'Complete Property Address', 'realestate-submit-property' ); ?>"></textarea>
							<span class="resp-error"></span>
						</div>
						<div class="resp-field-group">
							<label for="expected_price"><?php esc_html_e( 'Expected Price', 'realestate-submit-property' ); ?> <span class="required">*</span></label>
							<div class="resp-input-with-prefix">
								<span class="resp-prefix">LKR</span>
								<input type="number" id="expected_price" name="expected_price" required min="0" step="1" placeholder="<?php esc_attr_e( 'Price', 'realestate-submit-property' ); ?>">
							</div>
							<span class="resp-error"></span>
						</div>
						<div class="resp-field-group">
							<label><?php esc_html_e( 'Land Size', 'realestate-submit-property' ); ?></label>
							<div class="resp-dual-input">
								<input type="number" name="land_size" min="0" step="0.01" placeholder="<?php esc_attr_e( 'Size', 'realestate-submit-property' ); ?>">
								<select name="land_size_unit">
									<option value="perches"><?php esc_html_e( 'Perches', 'realestate-submit-property' ); ?></option>
									<option value="acres"><?php esc_html_e( 'Acres', 'realestate-submit-property' ); ?></option>
									<option value="sqft"><?php esc_html_e( 'sqft', 'realestate-submit-property' ); ?></option>
									<option value="sqm"><?php esc_html_e( 'sqm', 'realestate-submit-property' ); ?></option>
								</select>
							</div>
						</div>
						<div class="resp-field-group">
							<label><?php esc_html_e( 'Building Size', 'realestate-submit-property' ); ?></label>
							<div class="resp-dual-input">
								<input type="number" name="building_size" min="0" step="0.01" placeholder="<?php esc_attr_e( 'Size', 'realestate-submit-property' ); ?>">
								<select name="building_size_unit">
									<option value="sqft"><?php esc_html_e( 'sqft', 'realestate-submit-property' ); ?></option>
									<option value="sqm"><?php esc_html_e( 'sqm', 'realestate-submit-property' ); ?></option>
								</select>
							</div>
						</div>
						<div class="resp-field-group">
							<label for="bedrooms"><?php esc_html_e( 'Bedrooms', 'realestate-submit-property' ); ?></label>
							<input type="number" id="bedrooms" name="bedrooms" min="0" max="20" value="0">
						</div>
						<div class="resp-field-group">
							<label for="bathrooms"><?php esc_html_e( 'Bathrooms', 'realestate-submit-property' ); ?></label>
							<input type="number" id="bathrooms" name="bathrooms" min="0" max="10" value="0">
						</div>
						<div class="resp-field-group">
							<label for="parking_spaces"><?php esc_html_e( 'Parking Spaces', 'realestate-submit-property' ); ?></label>
							<input type="number" id="parking_spaces" name="parking_spaces" min="0" max="10" value="0">
						</div>
						<div class="resp-field-group resp-field-full">
							<label for="property_description"><?php esc_html_e( 'Description', 'realestate-submit-property' ); ?> <span class="required">*</span></label>
							<textarea id="property_description" name="property_description" required rows="5" maxlength="5000" minlength="50" placeholder="<?php esc_attr_e( 'Describe the property in detail (minimum 50 characters)', 'realestate-submit-property' ); ?>"></textarea>
							<span class="resp-char-count">0 / 5000</span>
							<span class="resp-error"></span>
						</div>
					</div>
				</div>

				<div class="resp-form-section" data-section="images">
					<h3 class="resp-section-title"><?php esc_html_e( 'Property Images', 'realestate-submit-property' ); ?></h3>
					<div class="resp-upload-zone" id="resp-upload-zone">
						<input type="file" id="property_images" name="property_images[]" multiple accept=".jpg,.jpeg,.png,.webp" class="resp-file-input">
						<div class="resp-upload-content">
							<span class="resp-upload-icon">&#128247;</span>
							<p class="resp-upload-text"><?php esc_html_e( 'Drag & drop images here or click to browse', 'realestate-submit-property' ); ?></p>
							<p class="resp-upload-hint"><?php esc_html_e( 'Max 10 files. JPG, PNG, WebP only. 5MB per file, 50MB total.', 'realestate-submit-property' ); ?></p>
						</div>
					</div>
					<div class="resp-upload-progress" id="resp-upload-progress" style="display:none;">
						<div class="resp-progress-bar"><div class="resp-progress-fill"></div></div>
						<span class="resp-progress-text">0%</span>
					</div>
					<div class="resp-preview-grid" id="resp-preview-grid"></div>
					<span class="resp-error resp-image-error" id="resp-image-error"></span>
				</div>

				<div class="resp-form-section" data-section="consent">
					<div class="resp-field-group resp-field-full">
						<label class="resp-checkbox-label">
							<input type="checkbox" id="gdpr_consent" name="gdpr_consent" required>
							<span><?php printf( esc_html__( 'I agree to the %sPrivacy Policy%s and consent to my data being processed for property listing purposes.', 'realestate-submit-property' ), '<a href="' . esc_url( get_privacy_policy_url() ) . '" target="_blank">', '</a>' ); ?></span>
						</label>
						<span class="resp-error"></span>
					</div>
				</div>

				<div class="resp-form-actions">
					<button type="submit" class="resp-submit-btn" id="resp-submit-btn">
						<span class="resp-btn-text"><?php esc_html_e( 'Submit Property', 'realestate-submit-property' ); ?></span>
						<span class="resp-btn-loading" style="display:none;"><?php esc_html_e( 'Submitting...', 'realestate-submit-property' ); ?></span>
					</button>
				</div>

				<div class="resp-form-message" id="resp-form-message" style="display:none;"></div>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}
}
