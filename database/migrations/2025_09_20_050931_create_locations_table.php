<?php
// database/migrations/2025_01_01_000000_create_posisi_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('location', function (Blueprint $table) {
            $table->id();
            $table->string('gpsid')->index();
            $table->string('vname')->nullable();
            $table->string('status')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->decimal('speed', 8, 2)->nullable();
            $table->decimal('direct', 8, 2)->nullable();
            $table->decimal('mileage', 12, 2)->nullable();
            $table->dateTime('datetime_utc')->index();
            $table->timestamps();

            $table->unique(['gpsid','datetime_utc']); // hindari duplikat
        });
    }
    public function down(): void {
        Schema::dropIfExists('location');
    }
};

