***REMOVED***

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
***REMOVED***
    ***REMOVED****
     * The event listener mappings for the application.
     *
     * @var array
     ***REMOVED***
    protected $listen = [
        'App\Events\SomeEvent' => [
            'App\Listeners\EventListener',
        ],
    ];
***REMOVED***
