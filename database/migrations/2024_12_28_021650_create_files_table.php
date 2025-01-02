<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('type_id');
            $table->string('product_name');
            $table->string('contact_person');
            $table->string('vendor');
            $table->string('material_position')->nullable();
            $table->text('material_description')->nullable();
            $table->string('website')->nullable();
            $table->string('image')->nullable();

            $table->unsignedBigInteger('folder_id');
            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('cascade');

            $table->unsignedBigInteger('material_type_id')->nullable();
            $table->foreign('material_type_id')->references('id')->on('material_types')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
