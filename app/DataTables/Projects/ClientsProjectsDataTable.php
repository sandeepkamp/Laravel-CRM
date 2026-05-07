<?php

namespace App\DataTables\Projects;


use App\Models\Projects\ProjectModel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;


class ClientsProjectsDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('project_name', function($row){
                return '<a href="'.route('clients.projects-details', $row->id).'">' . ucwords($row->project_name) . '</a>';
            })
            ->addColumn('project_hours', function($row){
                return $row->project_hours ? intdiv($row->project_hours, 60) . 'h ' . ($row->project_hours % 60) . 'm' : 'NA';
            })
            ->addColumn('start_date', function($row){
                return $row->start_date ? $row->start_date->format('jS M, Y') : 'NA';
            })
            ->addColumn('end_date', function($row){
                return $row->end_date ? $row->end_date->format('jS M, Y') : 'NA';
            })
            ->addColumn('status', function($row){
                $statusDescriptions = [
                    0 => ['text' => 'Not Started', 'color' => 'bg-secondary'],
                    1 => ['text' => 'Initiation Stage', 'color' => 'bg-primary'],
                    2 => ['text' => 'Planning Stage', 'color' => 'bg-info'],
                    3 => ['text' => 'Execution Stage', 'color' => 'bg-warning'],
                    4 => ['text' => 'Monitoring & Controlling Stage', 'color' => 'bg-dark'],
                    5 => ['text' => 'Testing Phase', 'color' => 'bg-danger'],
                    6 => ['text' => 'Closure Stage', 'color' => 'bg-success'],
                    7 => ['text' => 'Completed', 'color' => 'bg-success'],
                    8 => ['text' => 'Handed Over to Client', 'color' => 'bg-success']
                ];
                $status = $statusDescriptions[$row->status] ?? ['text' => 'Unknown Status', 'color' => 'bg-secondary'];
                return '<span class="badge ' . $status['color'] . '">' . $status['text'] . '</span>';
            })
            ->rawColumns(['project_name', 'status'])
            ->setRowId('id')

            // filter columns
            ->filterColumn('project_name', function($query, $keyword) {
                $query->where('projects.project_name', 'like', '%'.$keyword.'%');
            })

            // order columns
            ->orderColumn('created_at', function ($query, $order) {
                $query->orderBy('projects.created_at', $order);
            })
            ->orderColumn('project_name', function ($query, $order) {
                $query->orderBy('projects.project_name', $order);
            });
    }

    public function query(ProjectModel $model): QueryBuilder
    {
        $clientId = Auth::guard('clients')->id();
        return $model->newQuery()->select('projects.*')->where('client_id', $clientId)->groupBy('projects.id');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('projects-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->deferRender()
                    ->dom('<"row"<"col-md-12 mb-4"Br><"col-md-6 mb-4"l><"col-md-6 mb-4"f>><"row"<"col-md-12 mb-4"<"table-responsive"t>>><"row"<"col-md-6 mb-4"i><"col-md-6 mb-4"p>>')
                    ->selectStyleSingle()
                    ->parameters([
                        'drawCallback' => 'function() { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }',
                        'searchDelay' => 500,
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
                                            'columns' => [0,1,2,3]
                                        ]
                                    ],
                                    [
                                        'extend' => 'excelHtml5',
                                        'text' => __('Excel'),
                                        'filename' => $this->filename(),
                                        'title' => '',
                                        'exportOptions' => [
                                            'modifier' => ['selected' => true],
                                            'columns' => [0,1,2,3]
                                        ]
                                    ],
                                    [
                                        'extend' => 'pdfHtml5',
                                        'text' => __('PDF'),
                                        'filename' => $this->filename(),
                                        'title' => '',
                                        'exportOptions' => [
                                            'modifier' => ['selected' => true],
                                            'columns' => [0,1,2,3]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'order' => [3, 'desc']
                    ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('project_name')->searchable(true)->title('Project Name'),
            Column::make('project_hours')->searchable(false)->orderable(false)->title('Project Hours'),
            Column::make('start_date')->searchable(false)->title('Start Date'),
            Column::make('end_date')->searchable(false)->title('End Date'),
            Column::make('status')->searchable(false)->title('Status')
        ];
    }

    protected function filename(): string
    {
        return 'Projects on -'.date('YmdHis').'-'.config('app.name');
    }
}