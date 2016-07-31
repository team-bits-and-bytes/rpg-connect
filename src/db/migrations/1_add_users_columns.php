<?php

class AddUsersColumns extends DatabaseBase {
    public function up() {
        if ($this->schema->hasTable('users') == false) {
            return;
        }
        
        if ($this->schema->hasColumn('users', 'username') &&
            $this->schema->hasColumn('users', 'avatar') &&
            $this->schema->hasColumn('users', 'location') &&
            $this->schema->hasColumn('users', 'website') &&
            $this->schema->hasColumn('users', 'about')) {
                return;
            }
        
        $this->schema->table('users', function($table) {
            $table->string('username')->unique();
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
            $table->dropColumn('username');
            $table->dropColumn('avatar');
            $table->dropColumn('location');
            $table->dropColumn('website');
            $table->dropColumn('about');
        });
    }
}