<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use app\models\User;

/*
 * m200211_000003_create_table_system
 */
class m230104_000001_create_table_users extends Migration
{
    public function safeUp(){
        $this->createTable(User::tableName(), [
            'id'            => $this->primaryKey(),
        ], $this->tableOptions);

//        $this->addForeignKey('fk_pontos_user', Pontos::tableName(), 'avaliador_id', User::tableName(), 'id', $this->cascade, $this->restrict);

//        $this->addForeignKey('fk_pontos_base', Pontos::tableName(), 'base_id', Bases::tableName(), 'id', $this->cascade, $this->restrict);

//        $this->addForeignKey('fk_pontos_equipe', Pontos::tableName(), 'equipe_id', Equipe::tableName(), 'id', $this->cascade, $this->restrict);
    }

    public function down(){
        $this->dropTable(User::tableName());
    }
}
