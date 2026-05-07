<?php



namespace App\Providers;



use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Schema;

use App\Models\Sales\CurrencyModel;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Constants\Enum;



class AppServiceProvider extends ServiceProvider

{

    /**

     * Register any application services.

     */

    public function register(): void

    {

        //

    }



    /**

     * Bootstrap any application services.

     */

    public function boot(): void

    {

        Schema::defaultStringLength(200);
        Paginator::useBootstrap();
        View::composer('*', function ($view) {
            $view->with('priorities', Enum::PRIORITIES);
        });
        View::share('deadline', config('constants.deadline'));
        require_once app_path('Helpers/DepartmentHelpers.php');
        require_once app_path('Helpers/CommonHelpers.php');
        require_once app_path('Helpers/FileHelpers.php');
        require_once app_path('Helpers/ApiHelpers.php');
        require_once app_path('Helpers/MetaHelper.php');
    }

}

