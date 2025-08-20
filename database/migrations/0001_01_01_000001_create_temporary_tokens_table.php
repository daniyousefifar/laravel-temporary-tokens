<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Get the table name from the config file.
     *
     * @return string
     */
    private function getTableName(): string
    {
        return config('temporary-tokens.table_name', 'temporary_tokens');
    }

    public function up()
    {
        Schema::create($this->getTableName(), function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type')->nullable();
            $table->string('token')->index();
            $table->integer('usage_count')->default(0);
            $table->integer('max_usage_limit')->default(1);
            $table->json('metadata')->nullable();
            $table->nullableUuidMorphs('tokenable');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['type', 'token']);
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->getTableName());
    }
};
