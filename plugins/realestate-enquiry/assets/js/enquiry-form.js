(function($) {
    'use strict';

    $(document).ready(function() {

        // Auto-fill property_id and property_name from URL parameters
        var urlParams = new URLSearchParams(window.location.search);
        var propertyId = urlParams.get('property_id');
        var propertyName = urlParams.get('property_name');

        if (propertyId) {
            $('#ree-property-id').val(propertyId);
        }
        if (propertyName) {
            $('#ree-property-name').val(propertyName);
        }

        // Set minimum date to today for viewing date picker
        var today = new Date().toISOString().split('T')[0];
        $('#ree-viewing-date').attr('min', today);

        // Phone number formatting - allow only valid characters
        $('#ree-telephone').on('input', function() {
            var val = $(this).val();
            var cleaned = val.replace(/[^\d+\-\(\)\s]/g, '');
            if (val !== cleaned) {
                $(this).val(cleaned);
            }
        });

        // Real-time field validation
        $('#ree-enquiry-form').on('blur change', 'input[required], select[required], textarea[required]', function() {
            validateField($(this));
        });

        // Clear error on focus
        $('#ree-enquiry-form').on('focus', 'input, select, textarea', function() {
            var $field = $(this);
            $field.removeClass('ree-field-error');
            $field.siblings('.ree-error-msg').removeClass('visible').text('');
        });

        // Form submission
        $('#ree-enquiry-form').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);
            var $submitBtn = $form.find('.ree-submit-btn');
            var isValid = true;

            // Validate all required fields
            $form.find('[required]').each(function() {
                if (!validateField($(this))) {
                    isValid = false;
                }
            });

            if (!isValid) {
                var firstError = $form.find('.ree-field-error').first();
                if (firstError.length) {
                    $('html, body').animate({
                        scrollTop: firstError.offset().top - 50
                    }, 300);
                }
                return;
            }

            // Check honeypot
            if ($('#ree-website').val().length > 0) {
                showSuccess();
                return;
            }

            // Show loading state
            $submitBtn.prop('disabled', true);
            $submitBtn.find('.ree-btn-text').hide();
            $submitBtn.find('.ree-btn-loading').show();

            $.ajax({
                url: ree_form.ajax_url,
                type: 'POST',
                data: $form.serialize() + '&action=ree_submit_enquiry&ree_nonce=' + ree_form.nonce,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showSuccess(response.data.message);
                        $form[0].reset();
                        $form.find('.ree-field-error').removeClass('ree-field-error');
                        $form.find('.ree-error-msg').removeClass('visible').text('');
                    } else {
                        if (response.data && response.data.errors) {
                            var errors = response.data.errors;
                            for (var field in errors) {
                                var $field = $form.find('[name="' + field + '"]');
                                $field.addClass('ree-field-error');
                                $field.siblings('.ree-error-msg').text(errors[field]).addClass('visible');
                            }
                            var firstErr = $form.find('.ree-field-error').first();
                            if (firstErr.length) {
                                $('html, body').animate({
                                    scrollTop: firstErr.offset().top - 50
                                }, 300);
                            }
                        } else {
                            showError(response.data ? response.data.message : ree_form.i18n.error_msg);
                        }
                    }
                },
                error: function() {
                    showError(ree_form.i18n.error_msg);
                },
                complete: function() {
                    $submitBtn.prop('disabled', false);
                    $submitBtn.find('.ree-btn-text').show();
                    $submitBtn.find('.ree-btn-loading').hide();
                }
            });
        });

        function validateField($field) {
            var value = $.trim($field.val());
            var name = $field.attr('name');
            var $errorEl = $field.siblings('.ree-error-msg');
            var isValid = true;
            var message = '';

            $field.removeClass('ree-field-error');
            $errorEl.removeClass('visible').text('');

            if ($field.prop('required') && value.length === 0) {
                isValid = false;
                message = ree_form.i18n.required;
            } else if (name === 'email' && value.length > 0) {
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                    message = ree_form.i18n.invalid_email;
                }
            } else if (name === 'telephone' && value.length > 0) {
                var phoneRegex = /^[\+]?[\d\s\-\(\)]{7,30}$/;
                if (!phoneRegex.test(value)) {
                    isValid = false;
                    message = ree_form.i18n.invalid_phone;
                }
            }

            if (!isValid) {
                $field.addClass('ree-field-error');
                $errorEl.text(message).addClass('visible');
            }

            return isValid;
        }

        function showSuccess(message) {
            message = message || ree_form.i18n.success_msg;
            var $messages = $('#ree-form-messages');
            $messages.html('<div class="ree-success-msg">' + escapeHtml(message) + '</div>');
            $('html, body').animate({
                scrollTop: $messages.offset().top - 50
            }, 300);

            setTimeout(function() {
                $messages.fadeOut(300, function() {
                    $(this).html('').show();
                });
            }, 8000);
        }

        function showError(message) {
            var $messages = $('#ree-form-messages');
            $messages.html('<div class="ree-error-global">' + escapeHtml(message) + '</div>');
            $('html, body').animate({
                scrollTop: $messages.offset().top - 50
            }, 300);
        }

        function escapeHtml(str) {
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(str));
            return div.innerHTML;
        }
    });

})(jQuery);
