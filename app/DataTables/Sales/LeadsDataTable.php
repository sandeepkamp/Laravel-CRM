<?php

namespace App\DataTables\Sales;


use App\Models\Sales\LeadsModel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;


class LeadsDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function ($row) {
                return '
                    <form method="POST" action="' . route('lead.delete', $row->id) . '">
                        ' . csrf_field() . '
                        <input type="hidden" name="_method" value="DELETE">
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="mdi mdi-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="' . route('lead.edit', $row->id) . '">
                                    <i class="mdi mdi-pencil-outline me-1"></i> Edit
                                </a>
                                <button class="dropdown-item" type="submit" onclick="return confirm(\'Are you sure delete this lead?\')">
                                    <i class="mdi mdi-trash-can-outline me-1"></i> Delete
                                </button>
                            </div>
                        </div>
                    </form>';
            })
    
            ->addColumn('name', function ($row) {
                $orgName = $row->organisation_detail->name ?? 'N/A';
                return '<a href="' . route('lead.show', $row->id) . '">' . $orgName . '</a>';
            })
    
            ->addColumn('next_activity', function ($row) {
                if ($row->next_activity) {
                    $timeFrom = $row->next_activity->time_from;
                    $date = $timeFrom->format('d.m.Y');
                    $time = $timeFrom->format('g:i A');
                    $today = now()->format('d.m.Y');
                    $yesterday = now()->subDay()->format('d.m.Y');
                    $tomorrow = now()->addDay()->format('d.m.Y');
                    $weekAhead = now()->addWeek()->format('d.m.Y');
                    $weekAgo = now()->subWeek()->format('d.m.Y');
                    $dayDiff = now()->diffInDays($timeFrom, false);
    
                    if ($date === $today) {
                        $label = 'Today' . ($time !== '12:00 AM' ? " at $time" : '');
                        $color = 'text-success';
                    } elseif ($date === $yesterday) {
                        $label = 'Yesterday' . ($time !== '12:00 AM' ? " at $time" : '');
                        $color = 'text-danger';
                    } elseif ($date === $tomorrow) {
                        $label = 'Tomorrow' . ($time !== '12:00 AM' ? " at $time" : '');
                        $color = 'text-muted';
                    } elseif ($dayDiff > 1 && $date <= $weekAhead) {
                        $label = $timeFrom->format('D, d.m.Y') . ($time !== '12:00 AM' ? " at $time" : '');
                        $color = 'text-muted';
                    } elseif ($dayDiff < 0 && $date >= $weekAgo) {
                        $label = $timeFrom->format('D, d.m.Y') . ($time !== '12:00 AM' ? " at $time" : '');
                        $color = 'text-danger';
                    } elseif ($dayDiff > 7) {
                        $label = $timeFrom->format('D, d.m.Y') . ($time !== '12:00 AM' ? " at $time" : '');
                        $color = 'text-muted';
                    } elseif ($dayDiff < -7) {
                        $label = $timeFrom->format('D, d.m.Y') . ($time !== '12:00 AM' ? " at $time" : '');
                        $color = 'text-danger';
                    } else {
                        return '<i class="mdi mdi-minus-circle-outline text-warning"></i> 
                                <span class="text-warning">No Activity</span>';
                    }
    
                    $icons = [1 => 'mdi-phone',2 => 'mdi-account-group',3 => 'mdi-checkbox-marked-circle-plus-outline',
                    4 => 'mdi-flag-triangle',5 => 'mdi-email-plus-outline',];
                    $icon = $icons[$row->next_activity->type] ?? 'mdi-calendar';
    
                    return '<i class="mdi ' . $icon . ' me-2 ' . $color . '"></i> 
                            <span class="' . $color . '">' . $label . '</span>';
                }
    
                return '<i class="mdi mdi-minus-circle-outline text-warning"></i> 
                        <span class="text-warning">No Activity</span>';
            })
    
            ->addColumn('labels', function ($row) {
                $output = [];
    
                if ($row->labels_data) {
                    $labels = explode(',', $row->labels_data);
                    foreach ($labels as $label) {
                        [$name, $color] = explode('|', $label);
                        $output[] = '<span class="badge badge-pill" style="background:' . $color . ';">' . $name . '</span>';
                    }
                }
    
                return implode('<br>', $output);
            })
    
            ->addColumn('owner', fn($row) => $row->owner_data?->first_name . ' ' . $row->owner_data?->last_name)
    
            ->addColumn('created_at', fn($row) => $row->created_at->format('jS M, Y'))
    
            ->rawColumns(['action', 'labels', 'next_activity', 'name'])
            ->setRowId('id')
    
            // Filters
            ->filter(function ($query) {
                if (auth()->user()->role != 1) {
                    $query->where('leads.owner', auth()->id());
                }
    
                if (request()->has('search') && $keyword = request('search')['value']) {
                    $query->where(function ($q) use ($keyword) {
                        $q->whereRaw("CONCAT(leads.first_name, ' ', COALESCE(leads.last_name, '')) LIKE ?", ["%{$keyword}%"])
                            ->orWhereHas('owner_data', function ($q2) use ($keyword) {
                                $q2->whereRaw("CONCAT(users.first_name, ' ', COALESCE(users.last_name, '')) LIKE ?", ["%{$keyword}%"]);
                            })
                            ->orWhereHas('organisation_detail', function ($q3) use ($keyword) {
                                $q3->where('name', 'LIKE', "%{$keyword}%");
                            });
                    });
                }
            })
    
            ->filterColumn('labels', function ($query, $keyword) {
                $query->whereRaw("lead_label_list.name LIKE ?", ["%{$keyword}%"]);
            })
    
            // Today always on top for sorting then past dates and future dates 08.05.2025
            ->orderColumn('next_activity', function ($query, $order) {
                $query->orderByRaw("
                    CASE 
                        WHEN DATE(next_activity.next_activity_time) = CURDATE() THEN 0
                        WHEN DATE(next_activity.next_activity_time) < CURDATE() THEN 1
                        WHEN DATE(next_activity.next_activity_time) > CURDATE() THEN 2
                        WHEN next_activity.next_activity_time IS NULL THEN 3
                    END ASC,  -- always ASC to keep today at top
                    next_activity.next_activity_time $order
                ");
            })

            ->orderColumn('created_at', function ($query, $order) {
                $query->orderBy('leads.created_at', $order);
            })
    
            ->orderColumn('name', function ($query, $order) {
                $query->leftJoin('organisations', 'leads.organisation', '=', 'organisations.id')
                      ->orderBy('organisations.name', $order)
                      ->select('leads.*');
            });
    }
    // New Code addition for sorting (Today always on top)8.05.2025 //
    public function query(LeadsModel $model): QueryBuilder
    { 
        $querys = $model->newQuery()->from('leads')
            ->where(function ($query) {
                if (auth()->user()->role != 1) {
                $query->where('leads.visible_to', auth()->user()->id)->orWhereNull('leads.visible_to');
                }
            })
            ->leftJoin('lead_labels', 'lead_labels.lead_id', '=', 'leads.id')
            ->leftJoin('lead_label_list', 'lead_label_list.id', '=', 'lead_labels.lead_label')
            ->leftJoinSub(
                \DB::table('leads_activity')->selectRaw('lead_id, MIN(time_from) as next_activity_time')
                    ->where('done', 0)->groupBy('lead_id'),'next_activity',
                'next_activity.lead_id','=','leads.id'
            )
            ->select(['leads.id','leads.first_name','leads.last_name',
                'leads.email','leads.phone','leads.title','leads.organisation',
                'leads.website', 'leads.value','leads.currency',
                'leads.close_date','leads.owner','leads.visible_to',
                'leads.converted','leads.created_at','leads.updated_at',
                \DB::raw('next_activity.next_activity_time'),
                \DB::raw("GROUP_CONCAT(CONCAT(lead_label_list.name, '|', lead_label_list.bg_color) SEPARATOR ',') as labels_data")
            ])
            ->where('leads.converted', 0)
            ->groupBy(['leads.id','leads.first_name','leads.last_name',
                'leads.email','leads.phone','leads.title','leads.organisation',
                'leads.website','leads.value','leads.currency','leads.close_date',
                'leads.owner','leads.visible_to','leads.converted','leads.created_at',
                'leads.updated_at','next_activity.next_activity_time'
            ]);
            if (request()->input('length') == -1) {
                $querys->getQuery()->offset(0)->limit(PHP_INT_MAX); // Bypass pagination
            }
            if (!request()->has('order')) {
                $querys->orderByRaw("
                    CASE 
                        WHEN DATE(next_activity.next_activity_time) = CURDATE() THEN 0
                        WHEN DATE(next_activity.next_activity_time) < CURDATE() THEN 1
                        WHEN DATE(next_activity.next_activity_time) > CURDATE() THEN 2
                        WHEN next_activity.next_activity_time IS NULL THEN 3
                    END,
                    next_activity.next_activity_time ASC
                ");
            }
            return $querys;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('leads-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('<"row"<"col-md-12 mb-4"Br><"col-md-6 mb-4"l><"col-md-6 mb-4"f>><"row"<"col-md-12 mb-4"<"table-responsive"t>>><"row"<"col-md-6 mb-4"i><"col-md-6 mb-4"p>>')
                    ->selectStyleSingle()
                    ->parameters([
                        'drawCallback' => 'function() { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }',
                        'searchDelay' => 500,
                        'initComplete' => 'function(settings, json) {
                            $("#leads-table_filter input").attr("placeholder", "Search by Name/Owner");
                        }',
                        'buttons' =>  [
                            [
                                'extend' => 'collection',
                                'text' => __('Export'),
                                'buttons'   => [
                                    [
                                        'extend' => 'csvHtml5',
                                        'text' => __('CSV'),
                                        'filename' => $this->filename(),
                                        'exportOptions' => [
                                            'modifier' => ['selected' => true],
                                            'columns' => [0,1,2,3,4]
                                        ]
                                    ],
                                    [
                                        'extend' => 'excelHtml5',
                                        'text' => __('Excel'),
                                        'filename' => $this->filename(),
                                        'title' => '',
                                        'exportOptions' => [
                                            'modifier' => ['selected' => true],
                                            'columns' => [0,1,2,3,4]
                                        ]
                                    ],
                                    [
                                        'extend' => 'pdfHtml5',
                                        'text' => __('PDF'),
                                        'filename' => $this->filename(),
                                        'title' => '',
                                        'exportOptions' => [
                                            'modifier' => ['selected' => true],
                                            'columns' => [0,1,2,3,4]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'lengthMenu' => [
                            [30, 90, 270, -1], // Values
                            [30, 90, 270, 'All'] // Labels
                        ],
                        'order' => []
                    ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('name')->title('Name')->orderable(true)
            ->searchable(true), 
            Column::computed('next_activity')->title('Next Activity')
            ->searchable(false)->addClass('text-center')->orderable(true),
            Column::make('labels')->title('Labels')->orderable(false),
            Column::make('created_at')->orderable(true)->title('Leads Created'),
            Column::computed('owner')->searchable(true)->title('Owner'),
            Column::computed('action')->searchable(false)->orderable(false)
            ->exportable(false)->printable(false)->addClass('text-center')
            ->title('Action'),
        ];
    }

    protected function filename(): string
    {
        return 'Leads by '.auth()->user()?->first_name.'-'.date('YmdHis').'-'.config('app.name');
    }
}