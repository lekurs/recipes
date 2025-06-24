<?php

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->text('comment')->nullable();
            $table->text('question')->nullable();
            $table->dateTime('responded_at')->nullable();
            $table->foreignIdFor(Recipe::class)->constrained('recipes');
            $table->foreignId('user_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('answers');
    }
};
