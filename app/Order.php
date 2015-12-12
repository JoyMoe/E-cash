***REMOVED***

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
***REMOVED***
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function merchandiser()
    ***REMOVED***
        return $this->belongsTo('App\Merchandiser'***REMOVED***
    ***REMOVED***
***REMOVED***
