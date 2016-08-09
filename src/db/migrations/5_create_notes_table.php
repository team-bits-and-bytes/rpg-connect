<?php

class CreateNotesTable extends DatabaseBase {
    public function up() {
        if ($this->schema->hasTable('notes')) {
            return;
        }
        
         $this->schema->create('notes', function($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->text('body')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        $this->schema->dropIfExists('notes');
    }
}