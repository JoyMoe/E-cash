***REMOVED***

use Illuminate\Database\Migrations\Migration;

class Merchandiser extends Migration
***REMOVED***
    ***REMOVED****
     * Run the migrations.
     *
     * @return void
     ***REMOVED***
    public function up()
    ***REMOVED***
        Schema::create('merchandisers', function ($table) ***REMOVED***
            $table->increments('id'***REMOVED***
            $table->integer('user_id')->unsigned(***REMOVED***
            $table->string('name'***REMOVED***
            $table->string('domain'***REMOVED***
            $table->text('pubkey')->nullable(***REMOVED***
            $table->enum('status', array('frozen', 'testing', 'alive'))->default('testing'***REMOVED***
            $table->timestamps(***REMOVED***

            $table->foreign('user_id')->references('id')->on('users'***REMOVED***
        ***REMOVED******REMOVED***
    ***REMOVED***

    ***REMOVED****
     * Reverse the migrations.
     *
     * @return void
     ***REMOVED***
    public function down()
    ***REMOVED***
        Schema::dropIfExists('merchandisers'***REMOVED***
    ***REMOVED***
***REMOVED***
