<?php
require __DIR__ . '/../database_base.php';

class CreateUsersTable extends DatabaseBase {
    public function up() {
        if ($this->schema->hasTable('users')) {
            return;
        }
        
         $this->schema->create('users', function($table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('name');
            $table->string('password');
            $table->timestamps();
        });
    }

    public function down() {
        $this->schema->dropIfExists('users');
    }
}