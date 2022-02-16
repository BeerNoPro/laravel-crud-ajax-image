@extends('layouts.app')

@section('content')

    <!-- Modal add employee-->
    <div class="modal fade" id="AddEmployeeModal" tabindex="-1" 
        aria-labelledby="titleModalLabel" aria-hidden="true"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="titleModalLabel">Add employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="AddEmployeeForm" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <ul class="alert alert-warning d-none" id="save_error_list"></ul>
                        <input type="hidden" name="employee-id">
                        <div class="form-group mb-3">
                            <label for="">Name</label>
                            <input type="text" name="name" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label for="">Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label for="">Image</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn-save btn btn-primary">Save</button>
                        <button type="submit" class="btn-update btn btn-success d-none">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End modal add employee-->

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            Laravel Ajax Image CRUD - Employee Data
                            <a href="#" class="btn btn-primary btn-sm float-end btn-add" 
                                data-bs-toggle="modal"
                                data-bs-target="#AddEmployeeModal"
                            >
                                Add Employee
                            </a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Image</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center">
                    {{-- {{ $employee->links() }} --}}
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $(document).ready(function () {
            var ipt_name = $('#AddEmployeeForm').find('input[name="name"]');
            var ipt_phone = $('#AddEmployeeForm').find('input[name="phone"]');
            var employee_id = $('#AddEmployeeForm').find('input[name="employee-id"]');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Reset form add employee
            $(document).on('click', '.btn-add', function () {
                $(ipt_name).val('');
                $(ipt_phone).val('');
                $('#titleModalLabel').html('Add employee');
                $('.btn-save').removeClass('d-none');
                $('.btn-update').addClass('d-none');
            });

            // function fetch employee
            fetchEmployee()
            function fetchEmployee() { 
                $.ajax({
                    type: "GET",
                    url: "/fetch-employee",
                    dataType: "json",
                    success: function (response) {
                        console.log(response);
                        $('tbody').html('');
                        $.each(response.employee.data, function (key, item) { 
                            // console.log(key, item);
                            $('tbody').append(`
                                <tr>
                                    <td>${item.id}</td>
                                    <td>${item.name}</td>
                                    <td>${item.phone}</td>
                                    <td>
                                        <img src="uploads/employee/${item.image}" width="50px" height="50px" alt="">
                                    </td>
                                    <td>
                                        <button value="${item.id}" class="edit-btn btn btn-warning btn-sm">
                                            Edit
                                        </button>
                                    </td>
                                    <td>
                                        <button value="${item.id}" class="del-btn btn btn-danger btn-sm">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            `);
                        });
                        console.log(response.employee.links);
                    }
                });
            }

            // Delete employee
            $(document).on('click', '.del-btn', function () {
                var emp_id = $(this).val();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: "/delete-employee/" + emp_id,
                            dataType: "json",
                            success: function (response) {
                                if (response.status == 200) {
                                    Swal.fire(
                                        'Deleted!',
                                        'Your file has been deleted.',
                                        'success'
                                    )
                                    fetchEmployee()
                                }
                            }
                        });
                    }
                })
            });

            // Add employee
            $(document).on('click', '.btn-save', function (e) {
                e.preventDefault();
                let formData = new FormData($('#AddEmployeeForm')[0]);
                $.ajax({
                    type: "POST",
                    url: "/employee",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        // console.log(response);
                        if (response.status == 400) {
                            $('#save_error_list').removeClass('d-none');
                            $.each(response.errors, function (indexInArray, valueOfElement) { 
                                $('#save_error_list').append('<li>' + valueOfElement + '</li>');
                            });
                        } else if (response.status == 200) {
                            $('#save_error_list').addClass('d-none');
                            $('#AddEmployeeModal').modal('hide');
                            $('#AddEmployeeModal').find('input').val('');
                            fetchEmployee();
                            alertify.set('notifier','position', 'top-right');
                            alertify.success(response.message);
                        }
                    }
                });
            });

            // Edit employee
            $(document).on('click', '.edit-btn', function () {
                const emp_id = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "/edit-employee/" + emp_id,
                    dataType: "json",
                    success: function (response) {
                        // console.log(response);
                        if (response.status == 200) {
                            $(ipt_name).val(response.employee.name);
                            $(ipt_phone).val(response.employee.phone);
                            $(employee_id).val(response.employee.id);
                            $('#titleModalLabel').html('Edit employee');
                            $('.btn-save').addClass('d-none');
                            $('.btn-update').removeClass('d-none');
                            $('#AddEmployeeModal').modal('show');
                        }
                    }
                });
            });

            // Update employee
            $(document).on('click', '.btn-update', function (e) {
                e.preventDefault();
                let emp_id = $('input[name="employee-id"]').val();
                let formData = new FormData($('#AddEmployeeForm')[0]);
                $.ajax({
                    type: "POST",
                    url: "/update-employee/" + emp_id,
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        console.log(response);
                        if (response.status == 400) {
                            $('#save_error_list').removeClass('d-none');
                            $.each(response.errors, function (indexInArray, valueOfElement) { 
                                $('#save_error_list').append('<li>' + valueOfElement + '</li>');
                            });
                        } else if (response.status == 200) {
                            $('#save_error_list').addClass('d-none');
                            $('#AddEmployeeModal').modal('hide');
                            $('#AddEmployeeModal').find('input').val('');
                            fetchEmployee();
                            alertify.set('notifier','position', 'top-right');
                            alertify.success(response.message);
                        }
                    }
                });
            });
        });
    </script>
@endsection
