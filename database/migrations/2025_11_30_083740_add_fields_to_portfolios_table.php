<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {

            if (!Schema::hasColumn('portfolios', 'client')) {
                $table->string('client')->nullable();
            }

            if (!Schema::hasColumn('portfolios', 'date')) {
                $table->string('date')->nullable();
            }

            if (!Schema::hasColumn('portfolios', 'duration')) {
                $table->string('duration')->nullable();
            }

            if (!Schema::hasColumn('portfolios', 'challenge')) {
                $table->text('challenge')->nullable();
            }

            if (!Schema::hasColumn('portfolios', 'solution')) {
                $table->text('solution')->nullable();
            }

            if (!Schema::hasColumn('portfolios', 'results')) {
                $table->json('results')->nullable();
            }

            if (!Schema::hasColumn('portfolios', 'technologies')) {
                $table->json('technologies')->nullable();
            }

            if (!Schema::hasColumn('portfolios', 'images')) {
                $table->json('images')->nullable();
            }

            if (!Schema::hasColumn('portfolios', 'link')) {
                $table->string('link')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropColumn([
                'client',
                'date',
                'duration',
                'challenge',
                'solution',
                'results',
                'technologies',
                'images',
                'link',
            ]);
        });
    }
};
