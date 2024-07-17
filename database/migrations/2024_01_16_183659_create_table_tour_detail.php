<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tour_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('district_id')->nullable();
            $table->string('city')->nullable();
            $table->bigInteger('registration_number')->unique();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->string('name')->nullable();
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->integer('mobile_country_code')->nullable();
            $table->bigInteger('mobile_no')->nullable();
            $table->string('email')->nullable();
            $table->integer('emergency_country_code')->nullable();
            $table->bigInteger('emergency_contact_no')->nullable();
            $table->enum('mode_of_travel',['2 Wheeler','4 Wheeler','Bus','Bicycle','Commercial Vehicle'])->nullable();
            $table->string('vehicle_no')->nullable();
            $table->enum('accommodation',['Hotel','HomeStay','Other'])->nullable();
            $table->string('name_Of_accommodation')->nullable();
            $table->integer('id_proof')->nullable();
            $table->string('id_number')->nullable();
            $table->string('Proof_image_1_url')->nullable()->default(null);
            $table->string('Proof_image_1_file_name')->nullable()->default(null);
            $table->string('Proof_image_2_url')->nullable()->default(null);
            $table->string('Proof_image_2_file_name')->nullable()->default(null);
            $table->enum('mode_of_tour_generate',['Web','Mobile'])->nullable()->default(null);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_details');
    }
};
