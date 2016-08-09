<?php

class AddFavouriteToMembers extends DatabaseBase {
    public function up() {
        if ($this->schema->hasTable('members') == false) {
            return;
        }
        
        if ($this->schema->hasColumn('members', 'favourite')) {
            return;
        }
        
        $this->schema->table('members', function($table) {
            $table->boolean('favourite')->default(false);
        });
    }

    public function down() {
        if ($this->schema->hasTable('members') == false) {
            return;
        }
        
        $this->schema->table('members', function($table) {
            $table->dropColumn('favourite');
        });
    }
}