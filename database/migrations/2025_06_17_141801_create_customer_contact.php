<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('customer_contact', function (Blueprint $table) {
            $table->foreignId('customer_id');
            $table->foreignId('contact_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_contact');
    }
};
