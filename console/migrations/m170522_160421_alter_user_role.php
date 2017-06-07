<?php

use yii\db\Migration;

class m170522_160421_alter_user_role extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'role', $this->integer()->notNull()->defaultValue(1));
    }

    public function down()
    {
        $this->dropColumn('user','role');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
