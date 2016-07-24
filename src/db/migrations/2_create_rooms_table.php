<?php

class CreateRoomsTable extends DatabaseBase {
    public function up() {
        if ($this->schema->hasTable('rooms')) {
            return;
        }
        
         $this->schema->create('rooms', function($table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('password')->nullable();
            $table->integer('owner_id')->unsigned();
            $table->foreign('owner_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    public function down() {
        $this->schema->dropIfExists('rooms');
    }
}