<?php

namespace App\DataTables\Projects;

use App\Models\Projects\ProjectTasksModel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;
use App\Models\Projects\ProjectMilestoneModel;

class ProjectMilestonesDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
                ->addColumn('milestone_name', function($row){
                    return '<a href="'.route('project.milestones.detail', [$row->project_id, $row->id]).'">'.ucwords($row->milestone_name).'</a>';
                })
                ->addColumn('project_name', function($row){
                    return '<a href="'.route('project.details', [$row->project_id]).'">'.ucwords($row->projectModel->project_name).'</a>';
                })
                // ->addColumn('description', function($row){
                //     $description = $row->description;
                //     if (strlen($description) > 50) {
                //         return '<span>' . substr($description, 0, 15) . '...</span>
                //                 <a href="javascript:void(0)" class="text-primary read-more" data-description="' . htmlspecialchars($description) . '" title="Read More">Read More</a>';
                //     } else {
                //         return $description;
                //     }
                // })
                ->addColumn('action', function($row){
                    return '<div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="mdi mdi-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="'.route('project.milestone.edit', [$row->project_id, $row->id]).'">
                                    <i class="mdi mdi-pencil-outline me-1"></i> Edit
                                </a>
                                <form id="delete-form-'.$row->id.'" method="POST" action="'.route('project.milestones.delete', [$row->project_id, $row->id]).'" style="display:inline;">
                                    '.csrf_field().'
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="button" class="dropdown-item text-danger" onclick="confirmDelete('.$row->id.')">
                                        <i class="mdi mdi-trash-can-outline me-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>';
                })
                ->addColumn('created_at', fn($row) => $row->created_at->format('jS M, Y'))
                ->rawColumns(['milestone_name', 'project_name', 'action'])
                ->setRowId('id')
                
                ->filterColumn('milestone_name', function($query, $keyword){
                    $query->where('milestones.milestone_name', 'like', '%'.$keyword.'%');
                });
            }

    public function query(ProjectMilestoneModel $model): QueryBuilder
    {
        return $model->newQuery()->where('milestones.project_id', $this->project->id)
            ->leftJoin('projects', 'projects.id', '=', 'milestones.project_id')
            ->select('milestones.*')
            ->groupBy('milestones.id')
            ->orderBy('milestones.id', 'desc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('project-milestones-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->orderBy(2, 'desc');
    }

    public function getColumns(): array
    {
        return [
            ['data' => 'milestone_name', 'title' => 'Milestone'],
            ['data' => 'project_name', 'title' => 'Project'],
            // ['data' => 'description', 'title' => 'Description'],
            ['data' => 'created_at', 'title' => 'Created', 'orderable' => false, 'searchable' => false],
            ['data' => 'action', 'title' => 'action', 'orderable' => false, 'searchable' => false]
        ];
    }

    protected function filename(): string
    {
        return 'Milestones_' . date('YmdHis');
    }
}
