jQuery(document).ready(function ($) {
	var $form = $('#resp-submit-form');
	var $submitBtn = $('#resp-submit-btn');
	var $fileInput = $('#property_images');
	var $uploadZone = $('#resp-upload-zone');
	var $previewGrid = $('#resp-preview-grid');
	var $progressBar = $('#resp-upload-progress');
	var $progressFill = $('.resp-progress-fill');
	var $progressText = $('.resp-progress-text');
	var $formMessage = $('#resp-form-message');
	var selectedFiles = [];

	// Character count for description.
	$('#property_description').on('input', function () {
		var len = $(this).val().length;
		$('.resp-char-count').text(len + ' / 5000');
	});

	// Drag and drop.
	$uploadZone.on('dragover', function (e) {
		e.preventDefault();
		$(this).addClass('dragover');
	});

	$uploadZone.on('dragleave', function () {
		$(this).removeClass('dragover');
	});

	$uploadZone.on('drop', function (e) {
		e.preventDefault();
		$(this).removeClass('dragover');
		var files = e.originalEvent.dataTransfer.files;
		handleFiles(files);
	});

	$fileInput.on('change', function () {
		handleFiles(this.files);
		$(this).val('');
	});

	function handleFiles(files) {
		var maxSize = respData.maxSize;
		var maxFiles = respData.maxFiles;
		var allowed = ['image/jpeg', 'image/png', 'image/webp'];
		var $error = $('#resp-image-error');
		$error.text('');

		for (var i = 0; i < files.length; i++) {
			if (selectedFiles.length >= maxFiles) {
				$error.text('Maximum ' + maxFiles + ' files allowed.');
				break;
			}

			var file = files[i];
			if (allowed.indexOf(file.type) === -1) {
				$error.text('Invalid file type: ' + file.name + '. Only JPG, PNG, WebP allowed.');
				continue;
			}

			if (file.size > maxSize) {
				$error.text(file.name + ' exceeds 5MB limit.');
				continue;
			}

			var totalSize = file.size;
			for (var j = 0; j < selectedFiles.length; j++) {
				totalSize += selectedFiles[j].size;
			}
			if (totalSize > respData.maxTotal) {
				$error.text('Total file size exceeds 50MB limit.');
				break;
			}

			(function (f) {
				var reader = new FileReader();
				reader.onload = function (e) {
					var idx = selectedFiles.length;
					selectedFiles.push({ file: f, dataUrl: e.target.result });
					var $item = $('<div class="resp-preview-item" data-index="' + idx + '">' +
						'<img src="' + e.target.result + '" alt="Preview">' +
						'<button type="button" class="resp-preview-remove" data-index="' + idx + '">&times;</button>' +
						'</div>');
					$previewGrid.append($item);
				};
				reader.readAsDataURL(f);
			})(file);
		}
	}

	$previewGrid.on('click', '.resp-preview-remove', function () {
		var idx = $(this).data('index');
		selectedFiles.splice(idx, 1);
		refreshPreviews();
	});

	function refreshPreviews() {
		$previewGrid.empty();
		for (var i = 0; i < selectedFiles.length; i++) {
			var $item = $('<div class="resp-preview-item" data-index="' + i + '">' +
				'<img src="' + selectedFiles[i].dataUrl + '" alt="Preview">' +
				'<button type="button" class="resp-preview-remove" data-index="' + i + '">&times;</button>' +
				'</div>');
			$previewGrid.append($item);
		}
	}

	// Client-side validation.
	function validateForm() {
		var valid = true;
		$form.find('.resp-error').text('');
		$form.find('.error').removeClass('error');

		var fields = {
			owner_name: { required: true, label: 'Owner name' },
			owner_telephone: { required: true, label: 'Telephone' },
			owner_email: { required: true, label: 'Email', type: 'email' },
			property_type: { required: true, label: 'Property type' },
			property_location: { required: true, label: 'Location' },
			property_address: { required: true, label: 'Full address' },
			expected_price: { required: true, label: 'Expected price' },
			property_description: { required: true, label: 'Description', minLength: 50 },
			gdpr_consent: { required: true, label: 'Privacy policy consent', type: 'checkbox' }
		};

		$.each(fields, function (name, rules) {
			var $field;
			if (rules.type === 'checkbox') {
				$field = $('#' + name);
			} else {
				$field = $('[name="' + name + '"]');
			}
			var $group = $field.closest('.resp-field-group');
			var $error = $group.find('.resp-error');
			var val = $field.val();

			if (rules.type === 'checkbox') {
				if (!$field.is(':checked')) {
					$field.addClass('error');
					$error.text(rules.label + ' is required.');
					valid = false;
				}
			} else {
				if (rules.required && (!val || !val.trim())) {
					$field.addClass('error');
					$error.text(rules.label + ' is required.');
					valid = false;
				} else if (rules.type === 'email' && val && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
					$field.addClass('error');
					$error.text('Please enter a valid email address.');
					valid = false;
				} else if (rules.minLength && val && val.length < rules.minLength) {
					$field.addClass('error');
					$error.text(rules.label + ' must be at least ' + rules.minLength + ' characters.');
					valid = false;
				}
			}
		});

		return valid;
	}

	// Remove error on input.
	$form.on('input change', 'input, select, textarea', function () {
		$(this).removeClass('error');
		$(this).closest('.resp-field-group').find('.resp-error').text('');
	});

	// AJAX submission.
	$form.on('submit', function (e) {
		e.preventDefault();

		if (!validateForm()) {
			return;
		}

		var formData = new FormData();
		formData.append('action', 'resp_submit_property');
		formData.append('resp_nonce', respData.nonce);
		formData.append('website', '');
		formData.append('owner_name', $('[name="owner_name"]').val());
		formData.append('owner_telephone', $('[name="owner_telephone"]').val());
		formData.append('owner_email', $('[name="owner_email"]').val());
		formData.append('property_type', $('[name="property_type"]').val());
		formData.append('property_location', $('[name="property_location"]').val());
		formData.append('property_address', $('[name="property_address"]').val());
		formData.append('expected_price', $('[name="expected_price"]').val());
		formData.append('land_size', $('[name="land_size"]').val());
		formData.append('land_size_unit', $('[name="land_size_unit"]').val());
		formData.append('building_size', $('[name="building_size"]').val());
		formData.append('building_size_unit', $('[name="building_size_unit"]').val());
		formData.append('bedrooms', $('[name="bedrooms"]').val());
		formData.append('bathrooms', $('[name="bathrooms"]').val());
		formData.append('parking_spaces', $('[name="parking_spaces"]').val());
		formData.append('property_description', $('[name="property_description"]').val());
		formData.append('gdpr_consent', $('#gdpr_consent').is(':checked') ? '1' : '0');

		for (var i = 0; i < selectedFiles.length; i++) {
			formData.append('property_images[]', selectedFiles[i].file);
		}

		$submitBtn.find('.resp-btn-text').hide();
		$submitBtn.find('.resp-btn-loading').show();
		$submitBtn.prop('disabled', true);
		$progressBar.show();

		$.ajax({
			url: respData.ajaxUrl,
			type: 'POST',
			data: formData,
		 processData: false,
			contentType: false,
			xhr: function () {
				var xhr = new XMLHttpRequest();
				xhr.upload.addEventListener('progress', function (e) {
					if (e.lengthComputable) {
						var pct = Math.round((e.loaded / e.total) * 100);
						$progressFill.css('width', pct + '%');
						$progressText.text(pct + '%');
					}
				}, false);
				return xhr;
			},
			success: function (response) {
				$formMessage.show();
				if (response.success) {
					$formMessage.attr('class', 'resp-form-message success').text(response.data.message);
					$form[0].reset();
					selectedFiles = [];
					$previewGrid.empty();
					$('.resp-char-count').text('0 / 5000');
				} else {
					$formMessage.attr('class', 'resp-form-message error').text(response.data.message);
					if (response.data.errors) {
						$.each(response.data.errors, function (field, msg) {
							var $field = $('[name="' + field + '"]');
							if ($field.length) {
								$field.addClass('error');
								$field.closest('.resp-field-group').find('.resp-error').text(msg);
							} else if (field === 'gdpr_consent') {
								$('#gdpr_consent').addClass('error');
								$('#gdpr_consent').closest('.resp-field-group').find('.resp-error').text(msg);
							}
						});
					}
				}
			},
			error: function () {
				$formMessage.show().attr('class', 'resp-form-message error').text('An unexpected error occurred. Please try again.');
			},
			complete: function () {
				$submitBtn.find('.resp-btn-text').show();
				$submitBtn.find('.resp-btn-loading').hide();
				$submitBtn.prop('disabled', false);
				$progressBar.hide();
				$progressFill.css('width', '0%');
				$progressText.text('0%');
			}
		});
	});
});
