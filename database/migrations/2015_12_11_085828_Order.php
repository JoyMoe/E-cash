***REMOVED***

use Illuminate\Database\Migrations\Migration;

class Order extends Migration
***REMOVED***
    ***REMOVED****
     * Run the migrations.
     *
     * @return void
     ***REMOVED***
    public function up()
    ***REMOVED***
        Schema::create('orders', function ($table) ***REMOVED***
            $table->increments('id'***REMOVED***
            $table->integer('merchandiser_id')->unsigned(***REMOVED***
            $table->string('trade_no')->unique(***REMOVED***
            $table->string('subject'***REMOVED***
            $table->float('amount'***REMOVED***
            $table->text('description')->nullable(***REMOVED***
            $table->string('returnUrl'***REMOVED***
            $table->string('notifyUrl'***REMOVED***
            $table->enum('gateway', array('alipay', 'paypal', 'unionpay', 'wechat'))->nullable(***REMOVED***
            $table->string('transaction_id')->nullable(***REMOVED***
            $table->float('received')->default(0***REMOVED***
            $table->enum('status', array('pending', 'processing', 'done', 'refunded', 'cancelled'))->default('pending'***REMOVED***
            $table->softDeletes(***REMOVED***
            $table->timestamps(***REMOVED***

            $table->foreign('merchandiser_id')->references('id')->on('merchandisers'***REMOVED***
        ***REMOVED******REMOVED***
    ***REMOVED***

    ***REMOVED****
     * Reverse the migrations.
     *
     * @return void
     ***REMOVED***
    public function down()
    ***REMOVED***
        Schema::dropIfExists('orders'***REMOVED***
    ***REMOVED***
***REMOVED***
