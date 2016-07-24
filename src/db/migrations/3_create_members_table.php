<?php

class CreateMembersTable extends DatabaseBase {
    public function up() {
        if ($this->schema->hasTable('members')) {
            return;
        }
        
         $this->schema->create('members', function($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('room_id')->unsigned();
            $table->foreign('room_id')->references('id')->on('rooms');
            $table->timestamps();
        });
    }

    public function down() {
        $this->schema->dropIfExists('members');
    }
}