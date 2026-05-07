<?php

namespace App\DataTables\Projects;


use App\Models\Projects\ProjectBugsModel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;


class ClientsProjectBugsDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('name', function($row){
                return '<a href="'.route('clients.project-bug-detail', [$row->project_id, $row->id]).'">'.ucwords($row->bug_name).'</a>';
            })
            ->addColumn('created_at', function($row){
                return $row->created_at->format('jS M, Y');
            })
            ->addColumn('project', function($row){
                return $row->project()->count() > 0 ? '<a href="'.route('clients.projects-details', $row->project_id).'">'.ucwords($row->project()->first()->project_name).'</a>' : '';
            })
            ->addColumn('milestone', function($row){
                return $row->milestone()->count() > 0 ? '<a href="'.route('clients.project-milestone-detail', [$row->project_id, $row->milestone_id]).'">'.$row->milestone()->first()->milestone_name.'</a>' : '';
            })
            ->addColumn('assigns', function($row){
                if($row->assigns()->count() > 0){
                    
                    $assigns = $row->assigns()->get();
                    $firstAssign = $assigns->first();
                    $remainingAssigns = $assigns->slice(1);

                    $tooltipContent = $remainingAssigns->map(fn($assign) => e($assign->first_name . ' ' . $assign->last_name))->implode('<br>'); 
                    $tooltip = $tooltipContent 
                        ? ' <i class="mdi mdi-dots-horizontal-circle text-primary ms-1" style="cursor:pointer;" 
                                data-bs-toggle="tooltip" 
                                data-bs-placement="right" 
                                data-bs-html="true" 
                                title="'.$tooltipContent.'"></i>'
                        : '';
                    $padding = $tooltip ? 'style="padding: 0px 6px;"' : '';
                    return '<span class="badge rounded-pill bg-label-primary role-'.$firstAssign->role.'" ' . $padding . '>'
                        . e($firstAssign->first_name . ' ' . $firstAssign->last_name) .
                        $tooltip .
                       '</span>';
                }else{
                    return '';
                }
            })
            ->addColumn('status', function($row){
                if($row->status()->count() > 0){
                    return $row->status()->get()->map(function($status){
                        return '<span class="badge rounded-pill mb-2 '.$status->slug.'" style="color:'.$status->font_color.';background-color:'.$status->bg_color.';cursor:pointer;" data-bs-toggle="tooltip" data-bs-placement="top" title="'.$status->description.'">'.$status->status_name.'</span>';
                    })->implode('<br>');
                }else{
                    return '';
                }
            })
            ->rawColumns(['name', 'assigns', 'status', 'project', 'milestone'])
            ->setRowId('id')

            // filter columns
            ->filterColumn('name', function($query, $keyword) {
                $query->where('project_bug.bug_name', 'like', '%'.$keyword.'%');
            })
            ->filterColumn('assigns', function($query, $keyword){
                $query->where('users.first_name', 'like', '%'.$keyword.'%')->orWhere('users.last_name', 'like', '%'.$keyword.'%');
            })
            ->filterColumn('project', function($query, $keyword){
                $query->where('projects.project_name', 'like', '%'.$keyword.'%');
            })
            ->filterColumn('milestone', function($query, $keyword){
                $query->where('milestones.milestone_name', 'like', '%'.$keyword.'%');
            })

            // order columns
            ->orderColumn('created_at', function ($query, $order) {
                $query->orderBy('project_bug.created_at', $order);
            })
            ->orderColumn('name', function ($query, $order) {
                $query->orderBy('project_bug.task_name', $order);
            })
            ->orderColumn('project', function ($query, $order) {
                $query->orderBy('projects.project_name', $order);
            })
            ->orderColumn('milestone', function ($query, $order) {
                $query->orderBy('milestones.milestone_name', $order);
            });
    }

    public function query(ProjectBugsModel $model): QueryBuilder
    {
        return $model->newQuery()
                ->leftJoin('bug_assigns', 'bug_assigns.bug_id', '=', 'project_bug.id')
                ->leftJoin('projects', 'projects.id', '=', 'project_bug.project_id')
                ->leftJoin('users', 'users.id', '=', 'bug_assigns.user_id')
                ->leftJoin('milestones', 'milestones.id', '=', 'project_bug.milestone_id')
                ->leftJoin('bug_status', 'bug_status.id', '=', 'project_bug.status')
                ->where('project_bug.project_id', $this->project->id)
                ->select('project_bug.*')
                ->groupBy('project_bug.id');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('tasks-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('<"row"<"col-md-12 mb-4"Br><"col-md-6 mb-4"l><"col-md-6 mb-4"f>><"row"<"col-md-12 mb-4"<"table-responsive"t>>><"row"<"col-md-6 mb-4"i><"col-md-6 mb-4"p>>')
                    ->selectStyleSingle()
                    ->deferRender()
                    ->parameters([
                        'drawCallback' => 'function() { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }',
                        'searchDelay' => 1000,
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
                                            'columns' => [0,1,2,3,4,5]
                                        ]
                                    ],
                                    [
                                        'extend' => 'excelHtml5',
                                        'text' => __('Excel'),
                                        'filename' => $this->filename(),
                                        'title' => '',
                                        'exportOptions' => [
                                            'modifier' => ['selected' => true],
                                            'columns' => [0,1,2,3,4,5]
                                        ]
                                    ],
                                    [
                                        'extend' => 'pdfHtml5',
                                        'text' => __('PDF'),
                                        'filename' => $this->filename(),
                                        'title' => '',
                                        'exportOptions' => [
                                            'modifier' => ['selected' => true],
                                            'columns' => [0,1,2,3,4,5]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'order' => [5, 'desc']
                    ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('name')
                ->title('Bug Name'),
            Column::make('assigns')
                ->addClass('text-center')
                ->orderable(false)
                ->title('Assigns'),
            Column::make('project')
                ->title('Project'),
            Column::make('milestone')
                ->title('Milestone'),
            Column::make('status')
                ->title('Status')
                ->orderable(false),
            Column::make('created_at')
                ->title('Bug Created'),
        ];
    }

    protected function filename(): string
    {
        return 'Bugs on -'.date('YmdHis').'-'.config('app.name');
    }
}