<?php
if (! defined('ABSPATH')) {
	exit;
}

global $product;

// Fallback if not a product
if (! is_a($product, 'WC_Product')) {
	echo '<p>Error: No product found.</p>';
	return;
}

$product_id = $product->get_id();
$product_title = $product->get_name();
$product_price = $product->get_price_html();
$short_description = $product->get_short_description();

// Images
$main_image_id = $product->get_image_id();
$main_image_src = $main_image_id ? wp_get_attachment_image_url($main_image_id, 'woocommerce_single') : wc_placeholder_img_src();
$main_image_thumb = $main_image_id ? wp_get_attachment_image_url($main_image_id, 'woocommerce_gallery_thumbnail') : wc_placeholder_img_src();

$gallery_image_ids = $product->get_gallery_image_ids();
?>
<div class="sh-product-container">
	<nav class="sh-breadcrumb">
		<?php
		// Simple breadcrumb logic (can be expanded later if needed)
		echo woocommerce_breadcrumb();
		?>
	</nav>

	<h1 class="sh-product-title"><?php echo esc_html($product_title); ?></h1>

	<div class="sh-product-layout">
		<!-- Left Column: Images -->
		<div class="sh-product-gallery">
			<div class="sh-main-image-wrapper">
				<img id="sh-main-image" src="<?php echo esc_url($main_image_src); ?>" alt="<?php echo esc_attr($product_title); ?>">
				<div class="sh-brand-logo">Hanes</div>
			</div>
			<div class="sh-gallery-thumbnails" id="sh-gallery-thumbnails">
				<?php if ($main_image_id) : ?>
					<div class="sh-thumbnail sh-active" data-img="<?php echo esc_url($main_image_src); ?>">
						<img src="<?php echo esc_url($main_image_thumb); ?>" alt="Main Thumbnail">
					</div>
				<?php endif; ?>

				<?php
				if ($gallery_image_ids) {
					foreach ($gallery_image_ids as $attachment_id) {
						$full_src = wp_get_attachment_image_url($attachment_id, 'woocommerce_single');
						$thumb_src = wp_get_attachment_image_url($attachment_id, 'woocommerce_gallery_thumbnail');
						if ($full_src && $thumb_src) {
							echo '<div class="sh-thumbnail" data-img="' . esc_url($full_src) . '">';
							echo '<img src="' . esc_url($thumb_src) . '" alt="Gallery Thumbnail">';
							echo '</div>';
						}
					}
				}
				?>
			</div>
		</div>

		<!-- Right Column: Details -->
		<div class="sh-product-details">
			<div class="sh-short-description">
				<?php echo wp_kses_post(wpautop($short_description)); ?>
			</div>

			<div class="sh-product-price" id="sh-product-price">
				<?php echo wp_kses_post($product_price); ?>
			</div>

			<?php if ($product->is_type('variable')) : ?>
				<div class="sh-global-attributes">
					<?php
					$attributes = $product->get_variation_attributes();
					$size_attribute = '';
					$size_options = array();

					foreach ($attributes as $attribute_name => $options) :
						// Identify if this is a size attribute
						$is_size = (strpos(strtolower($attribute_name), 'size') !== false);

						if ($is_size) {
							$size_attribute = $attribute_name;
							$size_options = $options;
							continue; // Handle size later as rows
						}

						// Otherwise, render it globally (e.g., Color)
						$is_color = (strpos(strtolower($attribute_name), 'color') !== false);
					?>
						<div class="sh-variation-row" data-attribute="<?php echo esc_attr($attribute_name); ?>">
							<div class="sh-variation-label"><?php echo wc_attribute_label($attribute_name); ?></div>

							<?php if ($is_color) : ?>
								<div class="sh-color-swatches sh-variation-selector" data-attribute="<?php echo esc_attr($attribute_name); ?>">
									<?php foreach ($options as $option) : ?>
										<?php
										$term = get_term_by('slug', $option, $attribute_name);
										$name = $term ? $term->name : $option;

										// Fetch color or image from term meta
										$bg_style = 'background-color: #ccc;'; // default fallback
										if ($term) {
											$color = get_term_meta($term->term_id, 'product_attribute_color', true);
											if (empty($color)) $color = get_term_meta($term->term_id, 'color', true);
											if (empty($color)) $color = get_term_meta($term->term_id, '_color', true);

											$image_id = get_term_meta($term->term_id, 'product_attribute_image', true);
											if (empty($image_id)) $image_id = get_term_meta($term->term_id, 'image', true);

											if (! empty($image_id)) {
												$image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
												if ($image_url) {
													$bg_style = 'background-image: url(' . esc_url($image_url) . '); background-size: cover; background-position: center;';
												}
											} elseif (! empty($color)) {
												$bg_style = 'background-color: ' . esc_attr($color) . ';';
											}
										}
										?>
										<div class="sh-swatch" data-value="<?php echo esc_attr($option); ?>" data-name="<?php echo esc_attr($name); ?>" title="<?php echo esc_attr($name); ?>">
											<span class="sh-swatch-inner" style="<?php echo esc_attr($bg_style); ?> display:block; width:100%; height:100%;"></span>
										</div>
									<?php endforeach; ?>
								</div>
								<div class="sh-selected-color-name sh-selected-name-<?php echo esc_attr($attribute_name); ?>">Select a color</div>
							<?php else : ?>
								<div class="sh-size-selector-wrapper" style="margin-bottom:0;">
									<select class="sh-size-dropdown sh-variation-selector" data-attribute="<?php echo esc_attr($attribute_name); ?>">
										<option value="">Choose an option</option>
										<?php foreach ($options as $option) : ?>
											<?php
											$term = get_term_by('slug', $option, $attribute_name);
											$name = $term ? $term->name : $option;
											?>
											<option value="<?php echo esc_attr($option); ?>"><?php echo esc_html($name); ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>

				<?php if ($size_attribute) : ?>
					<div class="sh-size-rows-container">
						<div class="sh-variation-label" style="margin-bottom: 8px; font-weight: 600; font-size: 13px; color: #999; text-transform: capitalize;">
							<?php echo wc_attribute_label($size_attribute); ?>
						</div>

						<div class="sh-size-qty-row">
							<div class="sh-size-selector-wrapper">
								<select class="sh-size-dropdown sh-variation-selector sh-row-size" data-attribute="<?php echo esc_attr($size_attribute); ?>">
									<option value="">Choose an option</option>
									<?php foreach ($size_options as $option) : ?>
										<?php
										$term = get_term_by('slug', $option, $size_attribute);
										$name = $term ? $term->name : $option;
										?>
										<option value="<?php echo esc_attr($option); ?>"><?php echo esc_html($name); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<input type="number" class="sh-qty-input sh-row-qty" value="1" min="1">
							<span class="sh-remove-row" style="visibility:hidden;">
								<svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round">
									<circle cx="12" cy="12" r="10"></circle>
									<line x1="8" y1="12" x2="16" y2="12"></line>
								</svg>
							</span>
						</div>
					</div>

					<a href="#" class="sh-add-another-size">
						<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round">
							<circle cx="12" cy="12" r="10"></circle>
							<line x1="12" y1="8" x2="12" y2="16"></line>
							<line x1="8" y1="12" x2="16" y2="12"></line>
						</svg>
						Add Another Size
					</a>
				<?php else : ?>
					<!-- Variable product, but no size attribute found -->
					<div class="sh-qty-wrapper" style="margin-top:20px; margin-bottom:20px;">
						<input type="number" class="sh-qty-input sh-row-qty" value="1" min="1">
					</div>
				<?php endif; ?>

			<?php else : ?>
				<!-- Simple Product Qty -->
				<div class="sh-qty-wrapper" style="margin-top:20px; margin-bottom:20px;">
					<input type="number" class="sh-qty-input sh-row-qty" value="1" min="1">
				</div>
			<?php endif; ?>

			<button type="button" class="sh-add-to-quote-btn">
				<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round" class="sh-btn-icon">
					<line x1="5" y1="12" x2="19" y2="12"></line>
					<polyline points="12 5 19 12 12 19"></polyline>
				</svg>
				ADD TO QUOTE
			</button>
		</div>
	</div>
</div>