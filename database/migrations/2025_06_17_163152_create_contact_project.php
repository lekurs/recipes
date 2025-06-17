<?php

use App\Models\Contact;
use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('contact_project', function (Blueprint $table) {
            $table->foreignIdFor(Contact::class)->constrained('contacts');
            $table->foreignIdFor(Project::class)->constrained('projects');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_project');
    }
};
