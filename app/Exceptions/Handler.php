***REMOVED***

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
***REMOVED***
    ***REMOVED****
     * A list of the exception types that should not be reported.
     *
     * @var array
     ***REMOVED***
    protected $dontReport = [
        HttpException::class,
    ];

    ***REMOVED****
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     ***REMOVED***
    public function report(Exception $e)
    ***REMOVED***
        return parent::report($e***REMOVED***
    ***REMOVED***

    ***REMOVED****
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     ***REMOVED***
    public function render($request, Exception $e)
    ***REMOVED***
        return parent::render($request, $e***REMOVED***
    ***REMOVED***
***REMOVED***
