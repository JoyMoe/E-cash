***REMOVED***

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class User extends Migration
***REMOVED***
    ***REMOVED****
     * Run the migrations.
     *
     * @return void
     ***REMOVED***
    public function up()
    ***REMOVED***
        Schema::create('users', function (Blueprint $table) ***REMOVED***
            $table->increments('id'***REMOVED***
            $table->string('name'***REMOVED***
            $table->string('email'***REMOVED***
            $table->string('password'***REMOVED***
            $table->rememberToken(***REMOVED***
            $table->enum('status', array('frozen', 'pending', 'alive'))->default('pending'***REMOVED***
            $table->timestamps(***REMOVED***
        ***REMOVED******REMOVED***
    ***REMOVED***

    ***REMOVED****
     * Reverse the migrations.
     *
     * @return void
     ***REMOVED***
    public function down()
    ***REMOVED***
        Schema::dropIfExists('users'***REMOVED***
    ***REMOVED***
***REMOVED***
