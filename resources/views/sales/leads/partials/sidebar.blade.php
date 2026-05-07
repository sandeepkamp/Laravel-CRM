<div class="card mb-5">
    <div class="card-body">
        <h5 class="mb-5">{{ $lead->first_name }} {{ $lead->last_name }}</h5>
        <div class="divider text-start">
            <div class="divider-text">
                <h6 class="m-0">DETAILS</h6>
            </div>
        </div>
        <div class="d-block">
            <a href="#" data-bs-toggle="modal" data-bs-target="#modalToggle" data-action="labels">
                <div class="row mb-3">
                    <div class="col-1">
                        <i class="tf-icons mdi mdi-tag-outline"></i>
                    </div>
                    <div class="col-11">
                        @foreach ($lead->labels as $label)
                        <span class="badge badge-sm mb-1" style="background: {{ $label->label->bg_color }}; color: {{ $label->label->font_color }}">{{ $label->label->name }}</span>
                        @endforeach
                        <span class="float-end">
                            <i class="mdi mdi-pencil"></i>
                        </span>
                    </div>
                </div>
            </a>
            <!--<a href="#" data-bs-toggle="modal" data-bs-target="#modalToggle" data-action="value">-->
            <!--    <div class="row mb-3">-->
            <!--        <div class="col-1">-->
            <!--            <i class="tf-icons mdi mdi-cash-multiple"></i>-->
            <!--        </div>-->
            <!--        <div class="col-11">-->
            <!--            @if($lead->value)-->
            <!--            <span aria-label="{{ 'Lead Value' }}">{{ $global_currency->symbol.number_format($lead->value, 2) }}</span>-->
            <!--            @endif-->
            <!--            <span class="float-end">-->
            <!--                <i class="mdi mdi-pencil"></i>-->
            <!--            </span>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</a>-->
            <a href="#" data-bs-toggle="modal" data-bs-target="#modalToggle" data-action="close_date">
                <div class="row mb-3">
                    <div class="col-1">
                        <i class="tf-icons mdi mdi-flag-checkered"></i>
                    </div>
                    <div class="col-11">
                        @if($lead->close_date)
                        <span aria-label="{{ 'Lead Close Date' }}">{{ $lead->close_date ? date('jS M, Y', strtotime($lead->close_date)) : '' }}</span>
                        @endif
                        <span class="float-end">
                            <i class="mdi mdi-pencil"></i>
                        </span>
                    </div>
                </div>
            </a>
            <!-- <a href="#" data-bs-toggle="modal" data-bs-target="#modalToggle" data-action="owner"> -->
                <div class="row mb-5">
                    <div class="col-1">
                        <i class="tf-icons mdi mdi-account-circle"></i>
                    </div>
                    <div class="col-11">
                        <span aria-label="{{ 'Lead Owner' }}">{{ $lead->owner_data?->first_name.' '.$lead->owner_data?->last_name }}</span>
                    </div>
                </div>
            <!-- </a> -->
        </div>
        <div class="divider text-start">
            <div class="divider-text">
                <h6 class="m-0">PERSON</h6>
            </div>
        </div>
        <div class="d-block">
            <div class="row mb-3">
                <div class="col-1">
                    <i class="tf-icons mdi mdi-account-outline"></i>
                </div>
                <div class="col-11">{{ $lead->full_name }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-1">
                    <i class="tf-icons mdi mdi-email-outline"></i>
                </div>
                <div class="col-11">
                    <a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-1">
                    <i class="tf-icons mdi mdi-phone"></i>
                </div>
                <div class="col-11">
                    <a href="tel:{{ $lead->phone }}">{{ $lead->phone }}</a>
                </div>
            </div>
        </div>
        <div class="divider text-start">
            <div class="divider-text">
                <h6 class="m-0">ORGANISATION</h6>
            </div>
        </div>
        <div class="d-block">
            <div class="row mb-3">
                <div class="col-1">
                    <i class="tf-icons mdi mdi-domain"></i>
                </div>
                <div class="col-11">{{ $lead->organisation_detail->name }}</div>
            </div>
            @if($lead->website)
            <div class="row mb-3">
                <div class="col-1">
                    <i class="tf-icons mdi mdi-web"></i>
                </div>
                <div class="col-11">
                    <a href="{{ $lead->website }}" target="_blank">{{ $lead->website }}</a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- CONVERT TO lead  -->
    <div class="card-footer text-end">
        <button type="button" class="btn btn-outline-primary btn-sm waves-effect" data-bs-toggle="modal" data-bs-target="#transferOwnerModal">
            Transfer Ownership
        </button>
    </div>
    <div class="card-footer text-end">
        <button type="button" id="modal_btn" class="btn btn-outline-primary btn-sm waves-effect" data-bs-toggle="modal" data-bs-target="#largeModal">CONVERT TO Deal</button>
        <!-- <a href="{{ route('lead.convert', $lead->id) }}" class="btn btn-outline-primary btn-sm waves-effect">CONVERT TO lead</a> -->
        <a href="{{ route('lead.edit', $lead->id) }}" class="btn btn-outline-primary btn-sm waves-effect">EDIT</a>
    </div>
</div>