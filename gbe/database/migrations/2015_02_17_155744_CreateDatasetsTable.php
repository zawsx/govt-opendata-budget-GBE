<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDatasetsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('datasets', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('name')->nullable();
            $table->integer('year');
            $table->string('type');
            $table->integer('organization');
            $table->foreign('organization')->references('id')->on('organizations');
            $table->integer('chart');
            $table->foreign('chart')->references('id')->on('account_charts');
            $table->text('description')->nullable();
            $table->text('properties')->nullable();
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
		Schema::drop('datasets');
	}

}
