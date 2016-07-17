<?php

class AddUsersColumns extends DatabaseBase {
    public function up() {
        if ($this->schema->hasTable('users') == false) {
            return;
        }
        
        $this->schema->table('users', function($table) {
            $table->string('avatar');
            $table->string('location');
            $table->string('website');
            $table->string('about');
        });
    }

    public function down() {
        $this->schema->table('users', function($table) {
            $table->dropColumn('avatar');
            $table->dropColumn('location');
            $table->dropColumn('website');
            $table->dropColumn('about');
        });
    }
}