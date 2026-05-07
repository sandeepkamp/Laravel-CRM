<?php

namespace App\DataTables;


use App\Models\Projects\ClientModel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;


class ClientDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function($row){
                return '<form method="POST" action="'.route('client.delete', $row->id).'">
                            '.csrf_field().'
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="mdi mdi-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="'.route('client.edit', $row->id).'">
                                        <i class="mdi mdi-pencil-outline me-1"></i>
                                        Edit
                                    </a>
                                    <button class="dropdown-item" type="submit" onclick="javascript:if( !confirm(\'Are you sure delete this client?\') ) return false;">
                                        <i class="mdi mdi-trash-can-outline me-1"></i>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </form>';
            })
            ->addColumn('name', function($row){
                return '<a href="'.route('client.details', $row->id).'">'.$row->full_name.'</a>';
            })
            ->addColumn('status', function($row){
                switch ($row->status) {
                    case 1:
                        return '<span class="badge bg-success">Active</span>';
                        break;
                    case 2:
                        return '<span class="badge bg-warning">Inactive</span>';
                        break;
                    case 3:
                        return '<span class="badge bg-danger">Blocked</span>';
                        break;
                    
                    default:
                        // code...
                        break;
                }
            })
            ->addColumn('created_at', function($row){
                return $row->created_at->format('jS M, Y');
            })
            ->rawColumns(['action', 'name', 'status'])
            ->setRowId('id')            

            // order columns
            ->orderColumn('created_at', function ($query, $order) {
                $query->orderBy('created_at', $order);
            })
            ->orderColumn('name', function ($query, $order) {
                $query->orderBy('first_name', $order)->orderBy('last_name', $order);
            });
    }

    public function query(ClientModel $model): QueryBuilder
    {
        return $model->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('clients-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
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
                                            'columns' => [0,1]
                                        ]
                                    ],
                                    [
                                        'extend' => 'excelHtml5',
                                        'text' => __('Excel'),
                                        'filename' => $this->filename(),
                                        'title' => '',
                                        'exportOptions' => [
                                            'modifier' => ['selected' => true],
                                            'columns' => [0,1]
                                        ]
                                    ],
                                    [
                                        'extend' => 'pdfHtml5',
                                        'text' => __('PDF'),
                                        'filename' => $this->filename(),
                                        'title' => '',
                                        'exportOptions' => [
                                            'modifier' => ['selected' => true],
                                            'columns' => [0,1]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'order' => [0, 'desc']
                    ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('name')->title('Client Name'),
            Column::make('created_at')->title('Client Created'),
            Column::make('status')
                ->addClass('text-center')
                ->title('Status'),
            Column::computed('action')
                ->searchable(false)
                ->orderable(false)
                ->exportable(false)
                ->printable(false)
                ->addClass('text-center')
                ->title('Action'),
        ];
    }

    protected function filename(): string
    {
        return 'Leads by '.auth()->user()->first_name.'-'.date('YmdHis').'-'.config('app.name');
    }
}