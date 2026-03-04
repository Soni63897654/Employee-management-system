<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manager Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); }
        .form-control.is-invalid, .form-select.is-invalid { 
            border-color: #dc3545 !important; 
            background-image: none !important; 
        }
        /* Pagination Styling */
        .pagination-info { font-weight: 600; color: #6c757d; min-width: 80px; text-align: center; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">Employee Management</a>
    </div>
</nav>

<div class="container-fluid py-4">
    <div class="row">
        <div id="manager_list_col" class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Managers List</h5>
                    <button class="btn btn-primary btn-sm" id="add_btn_toggle" style="display:none;" onclick="openForm()">+ Add Manager</button>
                </div>

                <div class="card-body border-bottom">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" id="search_input" class="form-control form-control-sm" placeholder="Search by Name...">
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="manager_table_body"></tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white py-3">
                    <div id="pagination_links" class="d-flex justify-content-center align-items-center gap-3"></div>
                </div>
            </div>
        </div>

        <div id="manager_form_col" class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 id="form_title" class="mb-0">Add Manager</h5>
                    <button class="btn btn-light btn-sm border" onclick="closeForm()"><i class="fa fa-times"></i></button>
                </div>
                <div class="card-body">
                    <form id="managerForm">
                        <input type="hidden" id="manager_id" name="id">
                        <div class="mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" id="full_name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department <span class="text-danger">*</span></label>
                            <select name="department_id" id="department_id" class="form-select">
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex gap-2 pt-2">
                            <button type="button" class="btn btn-light border flex-grow-1" onclick="closeForm()">Close</button>
                            <button type="submit" id="submit_btn" class="btn btn-primary flex-grow-1">Save Manager</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function(){
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    fetchManagers();
    function fetchManagers(page = 1){
        $.get("{{ route('managers.fetch') }}", {
            search: $('#search_input').val(),
            page: page
        }, function(res){
            let html = '';
            let meta = res.managers;
            let managerData = meta.data;
            
            if(managerData && managerData.length > 0){
                $.each(managerData, function(k, m){
                    html += `<tr>
                        <td>${m.full_name}</td>
                        <td>${m.email}</td>
                        <td>${m.department ? m.department.name : '-'}</td>
                        <td>
                            <button value="${m.id}" class="btn btn-sm btn-light border edit_btn"><i class="fa fa-edit text-primary"></i></button>
                            <button value="${m.id}" class="btn btn-sm btn-light border delete_btn"><i class="fa fa-trash text-danger"></i></button>
                        </td>
                    </tr>`;
                });
            } else {
                html = '<tr><td colspan="4" class="text-center text-muted py-4">No managers found</td></tr>';
            }
            $('#manager_table_body').html(html);
            renderPagination(meta);
        });
    }

    function renderPagination(meta) {
        if(meta.last_page <= 1) {
            $('#pagination_links').html('');
            return;
        }

        let prevDisabled = (meta.current_page === 1) ? 'disabled' : '';
        let nextDisabled = (meta.current_page === meta.last_page) ? 'disabled' : '';

        let paginationHtml = `
            <button class="btn btn-sm btn-outline-primary px-3 prev_btn" ${prevDisabled} data-page="${meta.current_page - 1}">
                <i class="fa fa-chevron-left me-1"></i> Previous
            </button>
            <div class="pagination-info">
                ${meta.current_page} <span class="text-muted mx-1">of</span> ${meta.last_page}
            </div>
            <button class="btn btn-sm btn-outline-primary px-3 next_btn" ${nextDisabled} data-page="${meta.current_page + 1}">
                Next <i class="fa fa-chevron-right ms-1"></i>
            </button>
        `;
        $('#pagination_links').html(paginationHtml);
    }

    $(document).on('click', '.prev_btn, .next_btn', function(){
        let page = $(this).data('page');
        if(page) fetchManagers(page);
    });


    $('#search_input').on('keyup', function(){ fetchManagers(1); });

    window.openForm = function(){
        $('#manager_list_col').removeClass('col-lg-12').addClass('col-lg-8');
        $('#manager_form_col').fadeIn().removeClass('d-none');
        $('#add_btn_toggle').hide();
        resetForm();
    }

    window.closeForm = function(){
        $('#manager_form_col').hide().addClass('d-none');
        $('#manager_list_col').removeClass('col-lg-8').addClass('col-lg-12');
        $('#add_btn_toggle').show();
    }

    $(document).on('click', '.edit_btn', function(){
        let id = $(this).val();
        openForm();
        $.post("{{ route('managers.edit') }}", {id: id}, function(res){
            if(res.status == 200){
                $('#manager_id').val(res.manager.id);
                $('#full_name').val(res.manager.full_name);
                $('#email').val(res.manager.email);
                $('#department_id').val(res.manager.department_id);
                $('#form_title').text('Edit Manager');
                $('#submit_btn').text('Update Manager');
            }
        });
    });

    $(document).on('click', '.delete_btn', function(){
        let id = $(this).val();
        Swal.fire({
            title: 'Delete this manager?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete'
        }).then((result) => {
            if(result.isConfirmed){
                $.post("{{ route('managers.destroy') }}", {id: id}, function(res){
                    if(res.status == 200){
                        Swal.fire('Deleted!', res.message, 'success');
                        fetchManagers();
                    }
                });
            }
        });
    });

    $('#managerForm').validate({
        rules: {
            full_name: 'required',
            email: { required: true, email: true },
            department_id: 'required'
        },
        errorElement: 'div',
        errorClass: 'text-danger small',
        highlight: function(element) { $(element).addClass('is-invalid'); },
        unhighlight: function(element) { $(element).removeClass('is-invalid'); },
        submitHandler: function(form){
        let id = $('#manager_id').val();
        let url = id ? "{{ route('managers.update') }}" : "{{ route('managers.store') }}";
        $('#submit_btn').prop('disabled', true).text('Processing...');
        $('.is-invalid').removeClass('is-invalid');
        $('.text-danger.small').remove();
        $.post(url, $(form).serialize())
        .done(function(res){
            if(res.status == 200){
                Swal.fire('Success', res.message, 'success');
                fetchManagers();
                closeForm(); 
            } 
            else if(res.status == 400 && res.errors){
                // Display field validation errors (like email already exists)
                $.each(res.errors, function(field, messages){
                    let input = $('[name="'+field+'"]');
                    input.addClass('is-invalid');
                    input.after('<div class="text-danger small">'+messages[0]+'</div>');
                });
            } 
            else {
                Swal.fire('Error', res.message, 'error');
            }
        })
        .always(function(){
            let btnText = $('#manager_id').val() ? 'Update Manager' : 'Save Manager';
            $('#submit_btn').prop('disabled', false).text(btnText);
        });
    }
        });

    window.resetForm = function(){
        $('#managerForm')[0].reset();
        $('#manager_id').val('');
        $('#form_title').text('Add Manager');
        $('#submit_btn').text('Save Manager').addClass('btn-primary').removeClass('btn-info');
        $('.is-invalid').removeClass('is-invalid');
        $('.text-danger.small').remove();
    }
});
</script>
</body>
</html>