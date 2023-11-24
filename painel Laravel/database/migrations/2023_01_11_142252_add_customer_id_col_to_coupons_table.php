<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerIdColToCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('coupons', function (Blueprint $table) {
            $table->string('created_by', 50)->default('admin')->nullable();
            $table->string('customer_id')->default(json_encode(['all']))->nullable();
            $table->string('slug', 255)->nullable();
            if (!Schema::hasColumn('coupons', 'restaurant_id')) {
                $table->foreignId('restaurant_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            if (Schema::hasColumn('coupons', 'restaurant_id')) {
                $table->dropColumn('restaurant_id');
            }
            if (Schema::hasColumn('coupons', 'customer_id')) {
                $table->dropColumn('customer_id');
            }
            if (Schema::hasColumn('coupons', 'slug')) {
                $table->dropColumn('slug');
            }
            if (Schema::hasColumn('coupons', 'created_by')) {
                $table->dropColumn('created_by');
            }
        });
    }
}
