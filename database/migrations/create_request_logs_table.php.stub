<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('request_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable()->unique();
            $table->string('correlation_id')->nullable(); // This is not unique if subsequent (queued) requests are logged
            $table->string('client_request_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('team_id')->nullable(); // Can be changed to the following if your application uses teams: $table->foreignId('team_id')->nullable()->constrained();
            $table->ipAddress('ip')->nullable();
            $table->string('session')->nullable();
            $table->json('middleware')->nullable();
            $table->unsignedSmallInteger('status')->nullable();
            $table->string('method')->nullable();
            $table->string('route')->nullable(); // Not all routes needs to have a name
            $table->text('path')->nullable();
            $table->json('headers')->nullable();
            $table->json('payload')->nullable();
            $table->json('response_headers')->nullable();
            $table->json('response_body')->nullable();
            $table->unsignedMediumInteger('duration')->nullable(); // ms
            $table->float('memory')->nullable(); // MB
            $table->timestamps();

            $table->index('uuid');
            $table->index('correlation_id');
            $table->index('client_request_id');
            $table->index('created_at'); // Used for pruning
            $table->index(['user_id', 'created_at']);
            $table->index(['team_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('request_logs');
    }
};
