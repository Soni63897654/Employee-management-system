@extends('layouts.app')
@section('title', 'Employee Management')
@section('content')
<div class="row g-4">
    <div id="employee_list_col" class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold test">Employees List</h5>
                <button class="btn btn-primary btn-sm px-4" onclick="openForm()">
                    <i class="fa fa-plus me-2"></i>Add Employee
                </button>
            </div>

            <div class="filter-section">
                <div class="row g-2">
                    <div class="col-md-2">
                        <input type="text" id="search_input" class="form-control" placeholder="Search name...">
                    </div>

                    <div class="col-md-3">
                        <select id="dept_filter" class="form-select">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select id="manager_filter" class="form-select">
                            <option value="">All Managers</option>
                            @foreach($managers as $m)
                                <option value="{{ $m->id }}">{{ $m->full_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-5 d-flex gap-1">
                        <div class="date-range-group flex-grow-1">
                            <span>Joined:</span>
                            <input type="date" id="from_date">
                            <span class="mx-1 text-muted">-</span>
                            <input type="date" id="to_date">
                        </div>
                        <button class="btn btn-light border" onclick="clearFilters()">
                            <i class="fa fa-rotate"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-0 position-relative">
                <div id="table_overlay">
                    <div class="spinner-border text-primary spinner-border-sm"></div>
                </div>

                <div class="table-responsive" style="min-height: 400px;">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Employee Name</th>
                                <th>Code</th>
                                <th>Department</th>
                                <th>Manager</th>
                                <th>Joined Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="employee_table_body"></tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-white py-3 border-top" id="pagination_container" style="display: none !important;">
                <div class="d-flex justify-content-center align-items-center gap-3">
                    <button id="prev_btn" class="btn btn-sm btn-outline-secondary px-3">Previous</button>
                    <div class="fw-bold text-muted">
                        <span class="text-primary" id="current_page_text">1</span>
                        of <span id="total_pages_text">1</span>
                    </div>
                    <button id="next_btn" class="btn btn-sm btn-outline-secondary px-3">Next</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Form -->
    <div id="employee_form_col" class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 id="form_title" class="mb-0 fw-bold">Add Employee</h5>
                <button class="btn btn-link text-muted p-0" onclick="closeForm()">
                    <i class="fa fa-times-circle fs-5"></i>
                </button>
            </div>

            <div class="card-body p-4">
                <form id="employeeForm">
                    <input type="hidden" id="employee_id" name="id">

                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" id="full_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Employee Code</label>
                        <input type="text" name="employee_code" id="employee_code" class="form-control" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Department</label>
                            <select name="department_id" id="department_id" class="form-select" required>
                                <option value="">Select Dept</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6">
                            <label class="form-label">Manager</label>
                            <select name="manager_id" id="manager_id" class="form-select" required>
                                <option value="">Select Manager</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Joining Date</label>
                        <input type="date" name="joining_date" id="joining_date" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Address</label>
                        <textarea name="address" id="address" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light border py-2 flex-grow-1" onclick="closeForm()">Cancel</button>
                        <button type="submit" id="submit_btn" class="btn btn-primary py-2 fw-bold flex-grow-1">
                            Save Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    let currentPage = 1;
    function formatDateDMY(dateStr) {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        return d.toLocaleDateString('en-GB'); 
    }
    function openForm() {
        $('#employee_form_col').show();
        $('#employee_list_col').removeClass('col-lg-12').addClass('col-lg-8');
        $('#employee_list_col .btn-primary').hide(); // Hide Add Employee button
        resetForm();
    }
    function closeForm() {
        $('#employee_form_col').hide();
        $('#employee_list_col').removeClass('col-lg-8').addClass('col-lg-12');
         $('#employee_list_col .btn-primary').show();
    }

    function resetForm() {
        $('#employeeForm')[0].reset();
        $('#employee_id').val('');
        $('#manager_id').html('<option value="">Select Manager</option>');
        $('#form_title').text('Add Employee');
        $('#submit_btn').text('Save Employee').removeClass('btn-info').addClass('btn-primary');
    }

    function clearFilters() {
        $('#search_input, #from_date, #to_date').val('');
        $('#dept_filter, #manager_filter').val('');
        fetchEmployees(1);
    }

    function loadManagers(deptId, selectedId = null) {
        if(!deptId) return $('#manager_id').html('<option value="">Select Manager</option>');
        $.get("{{ route('get.managers.by.dept') }}", { department_id: deptId }, function(data) {
            let options = '<option value="">Select Manager</option>';
            $.each(data, function(i, m) {
                options += `<option value="${m.id}" ${selectedId == m.id ? 'selected' : ''}>${m.full_name}</option>`;
            });
            $('#manager_id').html(options);
        });
    }
    function fetchEmployees(page = 1) {
        currentPage = page;
        $('#table_overlay').css('display', 'flex');

        $.ajax({
            url: "{{ route('employees.fetch') }}",
            type: 'GET',
            data: {
                page: page,
                search: $('#search_input').val(),
                department_id: $('#dept_filter').val(),
                manager_id: $('#manager_filter').val(),
                from_date: $('#from_date').val(),
                to_date: $('#to_date').val()
            },
            success: function(res) {
                $('#table_overlay').hide();
                let rows = '';
                let data = res.data || [];
                let totalRecords = res.total || 0;

                if (data.length > 0) {
                    $.each(data, function(i, emp) {
                        rows += `<tr>
                            <td class="ps-4"><a href="javascript:void(0)" onclick="viewProfile(${emp.id})">${emp.full_name}</a></td>
                            <td>${emp.employee_code}</td>
                            <td>${emp.department ? emp.department.name : '-'}</td>
                            <td>${emp.manager ? emp.manager.full_name : '-'}</td>
                            <td>${formatDateDMY(emp.joining_date)}</td>
                            <td class="text-center">
                                <button value="${emp.id}" class="btn btn-sm btn-light border edit_btn"><i class="fa fa-pencil text-primary"></i></button>
                                <button value="${emp.id}" class="btn btn-sm btn-light border delete_btn"><i class="fa fa-trash text-danger"></i></button>
                            </td>
                        </tr>`;
                    });

                    // Show pagination only if total records > 10
                    if (totalRecords > 10) {
                        $('#pagination_container').show();
                        $('#current_page_text').text(res.current_page);
                        $('#total_pages_text').text(res.last_page);
                        $('#prev_btn').prop('disabled', !res.prev_page_url);
                        $('#next_btn').prop('disabled', !res.next_page_url);
                    } else {
                        $('#pagination_container').hide();
                    }

                } else {
                    rows = '<tr><td colspan="6" class="text-center py-5 text-danger">No records found.</td></tr>';
                    $('#pagination_container').hide();
                }

                $('#employee_table_body').html(rows);
            }
        });
    }
    function viewProfile(id) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('employees.show') }}";
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = $('meta[name="csrf-token"]').attr('content');

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;

        form.appendChild(csrfInput);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
    }
    $(document).ready(function() {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        fetchEmployees();

        $('#prev_btn').click(() => { if(currentPage > 1) fetchEmployees(currentPage - 1); });
        $('#next_btn').click(() => fetchEmployees(currentPage + 1));

        let timer;
        $('#search_input').on('keyup', function() {
            clearTimeout(timer);
            timer = setTimeout(() => fetchEmployees(1), 400);
        });

        $('#dept_filter, #manager_filter, #from_date, #to_date').on('change', () => fetchEmployees(1));
        $('#department_id').on('change', function() { loadManagers($(this).val()); });

        $(document).on('click', '.edit_btn', function() {
            let id = $(this).val();
            $.post("{{ route('employees.edit') }}", {id: id}, function(res) {
                if(res.status === 200) {
                    openForm();
                    $('#employee_id').val(res.employee.id);
                    $('#full_name').val(res.employee.full_name);
                    $('#employee_code').val(res.employee.employee_code);
                    $('#email').val(res.employee.email);
                    $('#address').val(res.employee.address);
                    $('#joining_date').val(res.employee.joining_date);
                    $('#department_id').val(res.employee.department_id);
                    loadManagers(res.employee.department_id, res.employee.manager_id);
                    $('#form_title').text('Update Employee');
                    $('#submit_btn').text('Update Details');
                }
            });
        });

        $('#employeeForm').validate({
            submitHandler: function(form) {
                let id  = $('#employee_id').val();
                let url = id 
                    ? "{{ route('employees.update') }}" 
                    : "{{ route('employees.store') }}";

                $('#submit_btn')
                    .prop('disabled', true)
                    .text('Processing...');

                $.ajax({
                    url: url,
                    type: "POST",
                    data: $(form).serialize(),

                    success: function(response) {
                        if (response.status === 200) { 
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            fetchEmployees(); 
                            $('#employeeForm')[0].reset();
                            $('#employee_id').val('');
                            $('#submit_btn').text('Save Employee');
                        }
                    },
                    error: function(xhr) {
                        let response = xhr.responseJSON;
                        if (response && response.errors) {
                            let errorMsg = '';
                            $.each(response.errors, function(key, value) {
                                errorMsg += value[0] + '<br>';
                            });

                            Swal.fire({
                                icon: 'info',
                                title: 'Info',
                                html: errorMsg
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response?.message ?? 'Something went wrong!'
                            });
                        }
                    },

                    complete: function() {
                        $('#submit_btn')
                            .prop('disabled', false)
                            .text('Save Employee');
                    }
                });
            }
        });

        $(document).on('click', '.delete_btn', function() {
            let id = $(this).val();

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: 'You will not be able to recover this employee!',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('employees.destroy') }}",
                        type: "POST",
                        data: { id: id, _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function(response) {
                            if (response.status === 200) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                fetchEmployees(currentPage);
                            }
                        },
                        error: function(xhr) {
                            let response = xhr.responseJSON;
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response?.message ?? 'Failed to delete employee'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endsection