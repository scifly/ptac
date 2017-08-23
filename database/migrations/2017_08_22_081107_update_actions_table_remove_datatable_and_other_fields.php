<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateActionsTableRemoveDatatableAndOtherFields extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('actions', function (Blueprint $table) {
            $table->dropColumn('datatable');
            $table->dropColumn('parsley');
            $table->dropColumn('select2');
            $table->dropColumn('chart');
            $table->dropColumn('map');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('actions', function (Blueprint $table) {
            $table->boolean('datatable')->nullable()->after('js')->comment('是否使用datatable')->change();
            $table->boolean('parsley')->nullable()->after('datatable')->comment('是否使用parsley')->change();
            $table->boolean('select2')->nullable()->after('parsley')->comment('是否使用select2')->change();
            $table->boolean('chart')->nullable()->after('select2')->comment('是否使用chart');
            $table->boolean('map')->nullable()->after('chart')->comment('是否使用map');
        });
    }
}
