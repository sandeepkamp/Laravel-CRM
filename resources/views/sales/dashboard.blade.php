@extends('app')

@section('title', 'Dasboard')

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('sales_dashboard') }}">Sales Dashboard</a>
    </li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12 col-lg-4 mb-5">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Welcome, {{ auth()->user()->first_name }}</h4>
                <p class="pb-0">Your total sales in this month</p>
                <h4 class="text-primary mb-1">{{$sales_count[0]->sales_count}}</h4>
                <a href="/sales/leads" class="btn btn-sm btn-primary">View Sales</a>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-lg-8 mb-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Statistics</h5>
                </div>
                <!--<p class="mt-3"><span class="fw-medium">Total 48.5% growth</span> this month</p>-->
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <div class="d-flex align-items-center">
                            <a href="/sales/leads?user_id={{Auth::user()->id}}">
                            <div class="avatar">
                                <div class="avatar-initial bg-primary rounded shadow">
                                    <i class="tf-icons mdi mdi-crosshairs-gps mdi-24px"></i>
                                </div>
                            </div>
                            </a>
                            <div class="ms-3">
                                <div class="small mb-1">Sales</div>
                                <h5 class="mb-0">{{$sales_count[0]->sales_count}}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="d-flex align-items-center">
                            <a href="">
                            <div class="avatar">
                                <div class="avatar-initial bg-success rounded shadow">
                                    <i class="tf-icons mdi mdi-currency-gbp mdi-24px"></i>
                                </div>
                            </div>
                            </a>
                            <div class="ms-3">
                                <div class="small mb-1">Prospects</div>
                                <h5 class="mb-0">{{$deals_count_prospects[0]->deals_count}}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="d-flex align-items-center">
                            <a href="/sales/deals?user_id={{Auth::user()->id}}">
                            <div class="avatar">
                                <div class="avatar-initial bg-warning rounded shadow">
                                    <i class="tf-icons mdi mdi-currency-gbp mdi-24px"></i>
                                </div>
                            </div>
                            </a>
                            <div class="ms-3">
                                <div class="small mb-1">Deals</div>
                                <h5 class="mb-0">{{ $deals_count[0]->deals_count}}</h5>
                            </div>
                        </div>
                    </div>
                    @if(Auth::user()->role == 1)
                    <div class="col-md-3 col-6">
                        <div class="d-flex align-items-center">
                            <div class="avatar">
                                <div class="avatar-initial bg-info rounded shadow">
                                    <i class="mdi mdi-currency-usd mdi-24px"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="small mb-1">Users</div>
                                <h5 class="mb-0">{{$users_count[0]->users_count}}</h5>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="col-md-3 col-6">
                        <div class="d-flex align-items-center">
                            <div class="avatar">
                                <div class="avatar-initial bg-info rounded shadow">
                                    <i class="mdi mdi-cart-variant mdi-24px"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="small mb-1">Income</div>
                                <h5 class="mb-0">{{ $global_currency->symbol.$product_revenue[0]->total_with_tax}}</h5>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card mb-5">
            <div class="card-body">
                <div id="convert_rate"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-5">
            <div class="card-body">
                <div class="form-group">
                    <select class="form-control w-50 mb-3" id="deals">
                        @for($i=(date('Y')-10);$i<=date('Y');$i++)
                            <option @if($i == date('Y')) selected @endif>{{$i}}</option>
                        @endfor

                    </select>
                </div>
                <div id="leads_convertion"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-5">
            <div class="card-body">
                @if(Auth::user()->role == 1)
                <div class="form-group">
                    <select class="form-control w-50 mb-3" id="pie_status">
                        <option value="">All</option>
                        @foreach($sales_person as $row)
                            <option value="{{$row->id}}">{{$row->first_name}} {{$row->last_name}}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div id="deals_chart"></div>
            </div>
        </div>
    </div>
    @if(Auth::user()->role == 1)
    <div class="col-md-6">
        <div class="card mb-5">
            <div class="card-body">
                <div id="leads_compare">
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('style')
<style type="text/css">
    .highcharts-container {
        font-family: var(--bs-body-font-family);
    }
    .highcharts-title,
    .highcharts-subtitle,
    .highcharts-yaxis .highcharts-axis-title,
    .highcharts-caption,
    .highcharts-label,
    .highcharts-label text {
        font-family: var(--bs-body-font-family);
        font-weight: normal !important;
    }
</style>
@endsection

@section('script')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script type="text/javascript">
    var linechart = Highcharts.chart('convert_rate', {
        chart:{
            type: 'line'
        },
        credits: {
            enabled: false
        },
        title: {
            text: 'Lead, Deals & Project Chart',
            align: 'left',

        },
   
        yAxis: {
            title: {
                text: ''
            }
        },

        xAxis: {
            type: 'datetime',
            categories : ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            // tickInterval   : 24 * 3600 * 1000
        },

        legend: {
            layout: 'horizontal',
            align: 'center',
            verticalAlign: 'bottom'
        },

        series: [{
            name: 'Total Leads',
            data: [43934, 48656, 65165, 81827, 112143, 142383,
                171533, 165174, 155157, 161454, 154610]
        }, {
            name: 'Leads Converted to Deals',
            data: [24916, 37941, 29742, 29851, 32490, 30282,
                38121, 36885, 33726, 34243, 31050]
        }, {
            name: 'Total Deals',
            data: [11744, 30000, 16005, 19771, 20185, 24377,
                32147, 30912, 29243, 29213, 25663]
        }, {
            name: 'Deals Converted to Project',
            data: [21908, 5548, 8105, 11248, 8989, 11816, 18274,
                17300, 13053, 11906, 10073]
        },
        {
            name: 'Project',
            data: [21908, 5548, 8105, 11248, 8989, 11816, 18274,
                17300, 13053, 11906, 10073]
        }

        ],

        responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                },
                chartOptions: {
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom'
                    }
                }
            }]
        }

    });
    
    var barchart = Highcharts.chart('leads_convertion', {
        credits: {
            enabled: false
        },
        chart: {
            type: 'column'
        },
        title: {
            text: 'Lead vs Deal vs Projects',
            align: 'left'
        },
        xAxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            crosshair: true,
            accessibility: {
                description: 'Countries'
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Counts'
            }
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [
            {
                name: 'Leads',
                data: [406292, 260000, 107000, 68300, 27500, 14500]
            },
            {
                name: 'Deals',
                data: [51086, 136000, 5500, 141000, 107180, 77000]
            },
            {
                name: 'Projects',
                data: [51086, 136000, 5500, 141000, 107180, 77000]
            }
        ]
    });
    
    var piechart = Highcharts.chart('deals_chart', {
        credits: {
            enabled: false
        },
        chart: {
            type: 'pie',
            options3d: {
                enabled: true,
                alpha: 45,
                beta: 0
            }
        },
        title: {
            text: 'Deals Stages Chart',
            align: 'left'
        },
        subtitle: {
            text: 'Total Deals: 10,000',
            align: 'left'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Percent',
            data: [
          
            ]
        }]
    });

    // Retrieved from https://www.ssb.no/jord-skog-jakt-og-fiskeri/jakt
    @if(Auth::user()->role == 1)
    var areachart = Highcharts.chart('leads_compare', {
        credits: {
            enabled: false
        },
        chart: {
            type: 'areaspline'
        },
        title: {
            text: 'Total Sales Comparison by Sales Person',
            align: 'left'
        },
        legend: {
            layout: 'vertical',
            align: 'left',
            verticalAlign: 'top',
            x: 120,
            y: 70,
            floating: true,
            borderWidth: 1,
            backgroundColor:
                Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF'
        },
        xAxis: {
            categories: [],
            crosshair: true,
            accessibility: {
                description: 'Countries'
            }
        },
        yAxis: {
            title: {
                text: 'Amount'
            }
        },
        plotOptions: {
           
        },
        series: []
    });
    @endif
    function barchartgraph(year) {
       $.ajax({
            url:"{{route('dashboard.bar')}}",
            method:'GET',
            data:{
                'year':year
            },
            success:function(res){
                barchart.series[0].setData(res.lead_array);
                barchart.series[1].setData(res.deals_array);
                barchart.series[2].setData(res.project_array);
                barchart.redraw();
            }
         })
    }

    function piechartgraph(user) {
       $.ajax({
            url:"{{route('dashboard.pie')}}",
            method:'GET',
            data:{
               'user':user
            },
            success:function(res){
                piechart.series[0].setData(res.deal_stage_array);
                piechart.setTitle(null, { text: 'Total Deals'});
                piechart.redraw();

            }
         })
    }

    function linechartgraph(){

        $.ajax({
            url:"{{route('dashboard.line')}}",
            method:'GET',
            data:{
               
            },
            success:function(res){
                linechart.series[0].setData(res.total_lead_array);
                linechart.series[1].setData(res.lead_array);
                linechart.series[2].setData(res.total_deal_array);
                linechart.series[3].setData(res.deals_array);
                linechart.series[4].setData(res.project_array);
                linechart.redraw();

            }
         })
    }

    function areachartgraph(){

         $.ajax({
            url:"{{route('dashboard.area')}}",
            method:'GET',
            data:{
               
            },
            success:function(res){
                 for (var i = 0; i < areachart.series.length; i++){
                    areachart.series[i].remove(true);
                 }
                 areachart.xAxis[0].setCategories(res.x_axis);
                $(res.final_array).each(function(index,value){
                    areachart.addSeries({                        
                        name: value.name,
                        data: value.data
                    }, false);
                });

                areachart.redraw();
            }
         })
    }

    $('#deals').change(function(){
        barchartgraph($('#deals').val());
    })
    $('#pie_status').change(function(){
       piechartgraph($('#pie_status').val());
    })
    $(document).ready(function(){
       barchartgraph($('#deals').val());
    })
    $(document).ready(function(){
       piechartgraph($('#pie_status').val());
    })
    $(document).ready(function(){
       linechartgraph();
    })
    $(document).ready(function(){
       areachartgraph();
    })

</script>
@endsection