<?php

namespace Towel\Migration;

use Towel\BaseApp;

class BaseMigration extends BaseApp
{
    protected $messages = array();
    protected $table = 'migrations';

    /**
     * Up method must define all the instructions to do the desired migration.
     *
     * @throws \Exception : if is not implemented
     */
    public function up() {
        throw new \Exception("You have to implement this method in" . get_class($this));
    }

    /**
     * Down method must define all the instruction to revert the migration.
     *
     * @throws \Exception : if is not implemented
     */
    public function down() {
        throw new \Exception("You have to implement this method in" . get_class($this));
    }

    /**
     * Wrapper to call the up method and do the pre an post install task internally
     * This will be called by migrator.
     */
    public function doUp() {
        $out = $this->up();
        $this->setDone();
        return $out;
    }

    /**
     * Wrapper to call the down method and do the pre an post install task internally
     * This will be called by migrator.
     */
    public function doDown() {
        $out = $this->down();
        $this->unsetDone();
        return $out;
    }

    /**
     * Checks if the migration is pending or not.
     *
     * @return bool
     */
    public function isPending() {
        $migration = $this->getMigration();

        if (empty($migration['state'])) {
            return true;
        }

        return false; //haven been executed.
    }

    /**
     * Checks if the migration have been executed or not.
     *
     * @return bool
     */
    public function isDone() {
        return !$this->isPending();
    }

    /**
     * Sets that the migration was done.
     *
     * @return bool
     */
    public function setDone() {
        $migration = $this->getMigration();
        if ($migration['state']) {
            return true; //alredy done
        }
        $this->db()->update($this->table, array('state' => true), array('id' => $migration['id']));
    }

    /**
     * Sets that the migration was reverted.
     *
     * @return bool
     */
    public function unsetDone() {
        $migration = $this->getMigration();
        if (!$migration['state']) {
            return true; //alredy undone
        }
        $this->db()->update($this->table, array('state' => false), array('id' => $migration['id']));
    }

    /**
     * Creates the record in the database of the migration.
     *
     * @throws \Exception
     */
    public function create() {
        $migration = $this->getMessages();
        if (!$migration) {
            $this->db()->insert($this->table, array('migration' => get_class($this), 'state' => false));
        } else {
            throw new \Exception('Migration ' .  get_class($this) . ' already created');
        }
    }

    /**
     * Gets the information in the DB of this migration.
     * @return mixed
     */
    public function getMigration() {
        $query = $this->db()->createQueryBuilder()
          ->select('m.*')
          ->from($this->table, 'm')
          ->where('migration = ?')
          ->setParameter(0, get_class($this));
        $results = $query->execute();
        $migration = $results->fetch();

        if (!$migration) {
            $this->create();
            return true; //Is not in the DB so is new and is pending
        }

        return $migration;
    }

    /**
     * Adds messages in the migration for later use.
     */
    public function addMessage($message) {
        $migrationName = get_class($this);
        $this->messages[] = sprintf("%s:  %s\n", $migrationName, $message);
    }

    /**
     * Gets the messages.
     *
     * @return array
     */
    public function getMessages() {
        return $this->messages;
    }
}
