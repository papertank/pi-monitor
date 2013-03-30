<?php

class Monitor_Create_Tables {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reports', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('location')->unique();
			$table->string('port')->default('80');
			$table->string('type')->default('up');
			$table->string('frequency')->default('often');
			$table->text('html')->nullable();
			$table->boolean('active')->default(0);
			$table->boolean('online')->default(1);
			$table->timestamp('checked_at')->nullable();
			$table->timestamp('online_at')->nullable();

			$table->timestamps();
		});

		Schema::create('logs', function($table)
		{
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('report_id')->unsigned();
			$table->string('type');
			$table->text('message')->nullable();
			$table->text('extra')->nullable();

			$table->timestamp('created_at')->nullable();
		});

	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('reports');
		Schema::drop('log');
	}

}