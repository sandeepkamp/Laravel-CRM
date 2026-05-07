<?php

namespace App\DataTables\Projects;

use App\Models\Projects\ProjectTasksModel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;

class ClientsTasksDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addColumn('name', function($row){
            return '<a href="'.route('clients.project-task-detail', [$row->project_id, $row->id]).'">'.ucfirst($row->task_name).'</a>';
        })
        ->addColumn('project', function($row){
            return $row->project()->count() > 0 ? '<a href="'.route('clients.projects-details', $row->project_id).'">'.$row->project()->first()->project_name.'</a>' : '';
        })
        ->addColumn('assigns', function($row){
            if($row->assigns()->count() > 0){
                $assigns = $row->assigns()->get();
                $firstAssign = $assigns->first();
                $remainingAssigns = $assigns->slice(1);
        
                $tooltipContent = $remainingAssigns->map(fn($assign) => e($assign->first_name . ' ' . $assign->last_name))
                                                   ->implode('<br>'); 
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
        ->addColumn('status', function ($row) {
            if ($row->taskStatus) { 
                return '<span class="badge rounded-pill mb-2 to-do" 
                            style="color:' . $row->taskStatus->font_color . ';background-color:' . $row->taskStatus->bg_color . ';">
                            ' . $row->taskStatus->status_name . '
                        </span>';
            }
            return 'N/A';
        })
        ->addColumn('created_at', fn($row) => $row->created_at->format('jS M, Y'))
        ->rawColumns(['name', 'project', 'assigns', 'status'])
        ->setRowId('id')
        ->filterColumn('name', function($query, $keyword) {
            $query->where('project_tasks.task_name', 'like', '%'.$keyword.'%');
        });
    }

    public function query(ProjectTasksModel $model): QueryBuilder
    {
        return $model->newQuery()
                ->with('taskStatus') 
                ->with('assigns')
                ->where('project_tasks.project_id', $this->project->id)
                ->select('project_tasks.*');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('tasks-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(2, 'desc');
    }

    public function getColumns(): array
    {
        return [
            ['data' => 'name', 'title' => 'Task Name'],
            ['data' => 'project', 'title' => 'Project'],
            ['data' => 'assigns', 'title' => 'Assigns'],
            ['data' => 'status', 'title' => 'Status'],
            ['data' => 'created_at', 'title' => 'Task Created']
        ];
    }

    protected function filename(): string
    {
        return 'Tasks_' . date('YmdHis');
    }
}
