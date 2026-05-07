@extends('app')

@section('title', 'Groups')

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item">List</li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <a href="{{ route('group.create') }}" class="btn btn-outline-primary btn-sm waves-effect float-end">
                    <span class="tf-icons mdi mdi-plus me-1"></span>
                    GROUP
                </a>
            </div>
            <div class="card-body">
                @if(session()->has('success'))
                <div class="alert alert-success alert-dismissible bg-success text-white border-0 fade show">
                    <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
                    <strong><i class="tf-icons mdi mdi-check-circle-outline"></i> Success -</strong>
                    {{ session()->get('success') }}
                </div>
                @endif
                @if(session()->has('error'))
                <div class="alert alert-danger alert-dismissible bg-danger text-white border-0 fade show">
                    <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
                    <strong><i class="tf-icons mdi mdi-alert-outline"></i> Error -</strong>
                    {{ session()->get('error') }}
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table users-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Owner</th>

                                <th>Group Created</th>

                                <th>Members</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groups as $group)
                            <tr>
                                <td>
                                    <a href="{{ route('group.edit', $group->id) }}">{{ $group->name }}</a>
                                </td>

                                <td class="text-center">
                                    @if(($group->owner_user_id == 0) || $group->owner_user_id == null)
                                        <span class="badge rounded-pill bg-danger">Not  Assigned</span>
                                    @else
                                        <span class="badge rounded-pill bg-primary">{{ $group->owner->full_name  }}</span>
                                    @endif
                                </td>
                                <td>{{ date('jS M, Y', strtotime($group->created_at)) }}</td>

                                <td>

                                    <span class="badge rounded-pill bg-label-success">{{ $group->members() }}</span>

                                </td>
                                <td class="text-center">
                                    <form method="POST" action="{{ route('group.delete', $group->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('group.edit', $group->id) }}">
                                                    <i class="mdi mdi-pencil-outline me-1"></i>
                                                    Edit
                                                </a>
                                                <button class="dropdown-item" type="submit" onclick="javascript:if( !confirm('Are you sure delete this group?') ) return false;">
                                                    <i class="mdi mdi-trash-can-outline me-1"></i>
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ url('assets/vendor/libs/DataTables/datatables.min.css') }}">
@endsection

@section('script')
<script src="{{ url('assets/vendor/libs/DataTables/datatables.min.js') }}"></script>
<script type="text/javascript">
    $(function() {
        $('.users-table').DataTable({
            dom: "<'row mb-5'<'col-md-12'B>>" +
                "<'row'<'col-md-6'l><'col-md-6'f>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-6'i><'col-md-6'p>>",
            buttons: [{
                extend: 'copyHtml5',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            }, {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            }, {
                extend: 'csvHtml5',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            }, {
                extend: 'pdfHtml5',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            }],
            language: {
                paginate: {
                    next: '&#8594;',
                    previous: '&#8592;'
                }
            }
        });
    });
</script>
@endsection