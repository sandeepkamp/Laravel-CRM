
// ----------------------------datatable—------------------------
function initDataTable(selector, ajaxUrl, columnKeys, extraOptions = {}) {
    if ($.fn.DataTable.isDataTable(selector)) {
        $(selector).DataTable().clear().destroy();
    }
    
    const columns = columnKeys.map(key => {
        const parts = key.split('.');
        const originalKey = parts[0];
        const flags = parts.slice(1).join('.').match(/(sf|of)/g) || [];

        return {
            data: originalKey,
            name: originalKey,
            searchable: !flags.includes('sf'),
            orderable: !flags.includes('of')
        };
    });
    console.log(columns);

    let columnDefs = [];
    columnKeys.forEach((col, index) => {
        columnDefs.push({
            targets: index,
            responsivePriority: index === 0 ? 1 : 10000
        });
    });

    const defaultOptions = {
        processing: true,
        serverSide: true,
        ordering: true,
        ajax: ajaxUrl,
        columns: columns,
        lengthMenu: [[5, 10, 50, 100], [5, 10, 50, 100]],
        pageLength: 5,
        responsive: true,
        columnDefs: columnDefs,
        order: []
    };

    return $(selector).DataTable({
        ...defaultOptions,
        ...extraOptions
    });
}

// ------------------------------------update status------------------------------------------
(function($) {
    // -----edit status (generic)-----
    $(document).on('click', '.edit-status', function () {
        var parent = $(this).closest('.status-wrapper');
        var id = parent.data('id');
        var currentStatus = parent.data('current-status');
        var module = parent.data('module');
        console.log(id, currentStatus, module);

        var options = `<option value="">Select Status</option>`;
        var statuses = window.STATUS_ENUMS[module];

        $.each(statuses, function (key, value) {
            options += `<option value="${key}">${value.label}</option>`;
        });

        var html = `
            <div style="display: inline;">
                <select class="form-control new-status">
                    ${options}
                </select>
                <button class="btn btn-sm btn-success btn-xs save-status" style="width: 50%;">
                    <span class="mdi mdi-check-bold"></span>
                </button>
                <button class="btn btn-sm btn-danger btn-xs cancel-status" style="margin-left:-3px;width: 49%;">
                    <span class="mdi mdi-close-thick"></span>
                </button>
            </div>
        `;
        parent.html(html);
        parent.find('.new-status').val(currentStatus);
    });

    // -----save status (generic)-----
    $(document).on('click', '.save-status', function () {
        var parent = $(this).closest('.status-wrapper');
        var id = parent.data('id');
        var module = parent.data('module');
        var updateUrl = parent.data('url');
        var tableClass = parent.data('table-class');
        var newStatus = parent.find('.new-status').val();

        $.ajax({
            url: updateUrl,
            type: 'PATCH',
            data: {
                status: newStatus,
                _token: window.CSRF_TOKEN
            },
            success: function (response) {
                $('.' + tableClass).DataTable().ajax.reload(null, false);

                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                });
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON.message || 'Failed to update status',
                });
            }
        });
    });

    // -----cancel edit status (generic)-----
    $(document).on('click', '.cancel-status', function () {
        var parent = $(this).closest('.status-wrapper');
        var currentStatus = parent.data('current-status');
        var module = parent.data('module');

        var statuses = window.STATUS_ENUMS[module];
        var label = statuses[currentStatus] ? statuses[currentStatus].label : '-';

        parent.html(`
            <span class='status-text'>${label}</span>
            <a href='javascript:void(0);' class='edit-status' style='margin-left:8px;'>
                <span class='mdi mdi-circle-edit-outline'></span>
            </a>
        `);
    });
})(jQuery);

