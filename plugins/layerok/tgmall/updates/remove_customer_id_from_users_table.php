<?php

namespace Layerok\TgMall\Updates;

use October\Rain\Database\Schema\Blueprint;
use Schema;
use October\Rain\Database\Updates\Migration;

class RemoveCustomerIdFromUsersTable extends Migration
{
    public function up()
    {
        Schema::table('layerok_tgmall_users', function (Blueprint $table) {
            $table->dropColumn('customer_id');
        });

    }

    public function down()
    {
        Schema::table('layerok_tgmall_users', function (Blueprint $table) {
            $table->string('customer_id');
        });
    }
}
