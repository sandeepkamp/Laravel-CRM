<div class="card mb-4">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2 mb-1">{{ $title }}</h5>
            @if ($search)
                <div class=" text-end pt-0 my-n5">
                    <input type="search" id="search{{ $tableId }}" class="form-control form-control-sm" placeholder="Search {{ $title }}">
                </div>
            @endif
            @if ($button && $route && $buttonName)
                <a href="{{ $route }}" class="btn btn-outline-primary btn-sm waves-effect">
                    <span class="tf-icons mdi mdi-plus me-1"></span> {{ $buttonName }}
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive sm-device">
            <table class="display table table-striped table-bordered {{ $tableClass }}" id="{{ $tableId }}">
                <thead>
                    {{ $thead }}
                </thead>
                <tbody>
                    <!-- AJAX Data Will Be Loaded Here -->
                </tbody>
            </table>
        </div>
    </div>
</div>
