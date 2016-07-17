<?php

class AddUsersColumns extends DatabaseBase {
    public function up() {
        if ($this->schema->hasTable('users') == false) {
            return;
        }
        
        $this->schema->table('users', function($table) {
            $table->text('avatar')->nullable();
            $table->string('location')->nullable();
            $table->string('website')->nullable();
            $table->string('about')->nullable();
        });
    }

    public function down() {
        if ($this->schema->hasTable('users') == false) {
            return;
        }
        
        $this->schema->table('users', function($table) {
            $table->dropColumn('avatar');
            $table->dropColumn('location');
            $table->dropColumn('website');
            $table->dropColumn('about');
        });
    }
}