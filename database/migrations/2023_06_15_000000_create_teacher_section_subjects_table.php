<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('teacher_section_subjects', function (Blueprint $table) {
			$table->id();
			$table->foreignId("teacher_id")->constrained("teachers")->cascadeOnDelete()->cascadeOnUpdate();
			$table->foreignId("section_id")->nullable()->constrained("sections")->cascadeOnDelete()->cascadeOnUpdate();
			$table->foreignId("subject_id")->nullable()->constrained("subjects")->cascadeOnDelete()->cascadeOnUpdate();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('teacher_section_subjects');
	}
};
