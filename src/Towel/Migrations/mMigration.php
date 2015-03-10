<?php

namespace Towel\Migrations;

class mMigration extends \Towel\Migration\BaseMigration {

    public function up() {
        if (!$this->db()->getSchemaManager()->tablesExist('migrations')) {

            $this->db()->executeQuery("CREATE TABLE migrations(
              id BIGINT AUTO_INCREMENT,
              migration VARCHAR(255) NOT NULL,
              state BOOLEAN DEFAULT FALSE,
              PRIMARY KEY(id)
            )");
        }
    }
}