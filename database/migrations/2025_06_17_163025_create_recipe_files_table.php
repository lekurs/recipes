<?php

use App\Models\Recipe;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('recipe_files', function (Blueprint $table) {
            $table->id();
            $table->text('filename');
            $table->string('original_name');
            $table->string('size');
            $table->string('mime_type');
            $table->foreignIdFor(Recipe::class)->constrained('recipes');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recipe_files');
    }
};
