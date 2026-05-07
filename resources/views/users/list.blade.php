@extends('app')



@section('title', 'Users')



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

                <a href="{{ route('user.create') }}" class="btn btn-outline-primary btn-sm waves-effect float-end">

                    <span class="tf-icons mdi mdi-plus me-1"></span>

                    USER

                </a>

            </div>

            <div class="card-body">

                @if(session()->has('success'))

                <div class="alert alert-success alert-dismissible bg-success text-white border-0 fade show">

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

                    <strong><i class="tf-icons mdi mdi-check-circle-outline"></i> Success -</strong>

                    {{ session()->get('success') }}

                </div>

                @endif

                @if(session()->has('error'))

                <div class="alert alert-danger alert-dismissible bg-danger text-white border-0 fade show">

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

                    <strong><i class="tf-icons mdi mdi-alert-outline"></i> Error -</strong>

                    {{ session()->get('error') }}

                </div>

                @endif

                <div class="table-responsive">

                    <table class="table users-table">

                        <thead>

                            <tr>

                                <th>Name</th>

                                <th>Email</th>

                                <th class="text-center">Department</th>

                                <th>Date Registered</th>

                                <th>Statistics</th>

                                <th>Status</th>

                                <th>Actions</th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($users as $user)

                            <tr>

                                <td>

                                    <a href="{{ route('user.edit', $user->id) }}">{{ $user->full_name }}</a>

                                </td>

                                <td>

                                    <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>

                                </td>

                                <td class="text-center">

                                    @php
                                        $roles = roleLabels();
                                        $roleClass = $roles[$user->role]['class'] ?? 'badge rounded-pill bg-dark';
                                    @endphp
                                    <span class="{{ $roleClass }}">{{ $user->role_details->name }}</span>

                                </td>

                                <td>{{ date('jS M, Y', strtotime($user->date_registered)) }}</td>

                                <td>

                                    @if($user->analytics_data['leads'] == 0 && $user->analytics_data['deals'] == 0 && $user->analytics_data['projects'] == 0)

                                    {{ 'No data available' }}

                                    @else

                                    <div class="mb-2">

                                        <span class="badge badge-center rounded-pill bg-label-primary">{{ $user->analytics_data['leads'] }}</span>

                                        <span>Leads</span>

                                    </div>

                                    <div class="mb-2">

                                        <span class="badge badge-center rounded-pill bg-label-primary">{{ $user->analytics_data['deals'] }}</span>

                                        <span>deals</span>

                                    </div>

                                    <div class="mb-2">

                                        <span class="badge badge-center rounded-pill bg-label-primary">{{ $user->analytics_data['projects'] }}</span>

                                        <span>Projects</span>

                                    </div>

                                    @endif

                                </td>

                                <td>

                                    @if($user->status == 1)

                                    <span class="badge rounded-pill bg-label-success">Active</span>

                                    @elseif($user->status == 2)

                                    <span class="badge rounded-pill bg-label-warning">Inactive</span>

                                    @elseif($user->status == 3)

                                    <span class="badge rounded-pill bg-label-danger">Block</span>

                                    @endif

                                </td>

                                <td class="text-center">

                                    <form method="POST" action="{{ route('user.delete', $user->id) }}">

                                        @csrf

                                        @method('DELETE')

                                        <div class="dropdown">

                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">

                                                <i class="mdi mdi-dots-vertical"></i>

                                            </button>

                                            <div class="dropdown-menu">

                                                <a class="dropdown-item" href="{{ route('user.edit', $user->id) }}">

                                                    <i class="mdi mdi-pencil-outline me-1"></i>

                                                    Edit

                                                </a>

                                                <button class="dropdown-item" type="submit" onclick="javascript:if( !confirm('Are you sure delete this user?') ) return false;">

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

    $(function(){

        $('.users-table').DataTable({

            dom:"<'row mb-5'<'col-md-12'B>>" + 

                "<'row'<'col-md-6'l><'col-md-6'f>>" +

                "<'row'<'col-md-12'tr>>" +

                "<'row'<'col-md-6'i><'col-md-6'p>>",

            buttons: [

                {

                    extend: 'copyHtml5',

                    exportOptions: {

                        columns: [0,1,2,3,4]

                    }

                },{

                    extend: 'excelHtml5',

                    exportOptions: {

                        columns: [0,1,2,3,4]

                    }

                },{

                    extend: 'csvHtml5',

                    exportOptions: {

                        columns: [0,1,2,3,4]

                    }

                },{

                    extend: 'pdfHtml5',

                    exportOptions: {

                        columns: [0,1,2,3,4]

                    }

                }

            ],

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