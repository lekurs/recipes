<?php

use App\Models\Customer;
use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('customer_project', function (Blueprint $table) {
            $table->foreignIdFor(Customer::class)->constrained('customers');
            $table->foreignIdFor(Project::class)->constrained('projects');
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_project');
    }
};
