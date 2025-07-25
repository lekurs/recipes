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
            $table->id();
            $table->string('access_token', 64)->unique()->nullable(); // Token d'accès unique
            $table->timestamp('expires_at')->nullable(); // Date d'expiration
            $table->boolean('is_active')->default(true); // Statut actif/inactif
            $table->timestamps(); // created_at et updated_at

            // Index pour les performances
            $table->index(['access_token', 'expires_at']);
            $table->index(['project_id', 'is_active']);

            $table->foreignIdFor(Contact::class)->constrained('contacts');
            $table->foreignIdFor(Project::class)->constrained('projects');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_project');
    }
};
