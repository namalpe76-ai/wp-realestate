(function($) {
    'use strict';

    $(document).ready(function() {

        // Select all checkbox
        $('#ree-select-all').on('change', function() {
            var checked = $(this).prop('checked');
            $('.ree-enquiries-table tbody input[type="checkbox"]').prop('checked', checked);
        });

        // Quick status update
        $('.ree-quick-status').on('change', function() {
            var $select = $(this);
            var id = $select.data('id');
            var status = $select.val();

            $.ajax({
                url: ree_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'ree_update_status',
                    nonce: ree_admin.nonce,
                    id: id,
                    status: status
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showNotice('success', ree_admin.i18n.status_updated);
                    } else {
                        showNotice('error', response.data ? response.data.message : ree_admin.i18n.error);
                    }
                },
                error: function() {
                    showNotice('error', ree_admin.i18n.error);
                }
            });
        });

        // Delete single enquiry
        $(document).on('click', '.ree-delete-btn', function(e) {
            e.preventDefault();
            if (!confirm(ree_admin.i18n.confirm_delete)) {
                return;
            }

            var id = $(this).data('id');
            var $row = $('tr[data-id="' + id + '"]');

            $.ajax({
                url: ree_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'ree_delete_enquiry',
                    nonce: ree_admin.nonce,
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $row.fadeOut(300, function() {
                            $(this).remove();
                        });
                        showNotice('success', ree_admin.i18n.enquiry_deleted);
                    } else {
                        showNotice('error', response.data ? response.data.message : ree_admin.i18n.error);
                    }
                },
                error: function() {
                    showNotice('error', ree_admin.i18n.error);
                }
            });
        });

        // Bulk actions
        $('.ree-bulk-apply, .ree-bulk-apply-bottom').on('click', function() {
            var bulkAction;
            var $form = $('#ree-bulk-form');

            if ($(this).hasClass('ree-bulk-apply')) {
                bulkAction = $form.find('.ree-bulk-action-select').first().val();
            } else {
                bulkAction = $('#ree-bulk-action-bottom').val();
            }

            if (!bulkAction) {
                return;
            }

            var selectedIds = [];
            $form.find('.ree-enquiries-table tbody input[type="checkbox"]:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                alert(ree_admin.i18n.no_selected);
                return;
            }

            if (bulkAction === 'delete' && !confirm(ree_admin.i18n.confirm_bulk)) {
                return;
            }

            var completed = 0;
            var total = selectedIds.length;

            if (bulkAction === 'delete') {
                selectedIds.forEach(function(id) {
                    $.ajax({
                        url: ree_admin.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'ree_delete_enquiry',
                            nonce: ree_admin.nonce,
                            id: id
                        },
                        dataType: 'json',
                        success: function() {
                            $('tr[data-id="' + id + '"]').fadeOut(300, function() {
                                $(this).remove();
                            });
                        },
                        complete: function() {
                            completed++;
                            if (completed === total) {
                                showNotice('success', ree_admin.i18n.enquiry_deleted);
                            }
                        }
                    });
                });
            } else {
                selectedIds.forEach(function(id) {
                    $.ajax({
                        url: ree_admin.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'ree_update_status',
                            nonce: ree_admin.nonce,
                            id: id,
                            status: bulkAction
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                var $select = $('tr[data-id="' + id + '"] .ree-quick-status');
                                $select.val(bulkAction);
                            }
                        },
                        complete: function() {
                            completed++;
                            if (completed === total) {
                                showNotice('success', ree_admin.i18n.status_updated);
                            }
                        }
                    });
                });
            }
        });

        // Export CSV
        $('.ree-export-btn').on('click', function(e) {
            e.preventDefault();
            window.location.href = ree_admin.ajax_url + '?action=ree_export_csv&nonce=' + ree_admin.nonce;
        });

        function showNotice(type, message) {
            var noticeClass = type === 'success' ? 'notice-success' : 'notice-error';
            var $notice = $('<div class="notice ' + noticeClass + ' is-dismissible ree-admin-notice"><p>' + message + '</p></div>');
            $('.ree-admin-wrap h1').first().after($notice);

            setTimeout(function() {
                $notice.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 4000);
        }
    });

})(jQuery);
