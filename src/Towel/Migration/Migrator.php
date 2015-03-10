<?php

namespace Towel\Migration;

use Towel\BaseApp;

class Migrator extends BaseApp
{
    public function pending() {

    }

    public function status() {
        $out = array();
        $migrations = $this->getMigrations();

        foreach ($migrations as $migration) {
            $out[] = sprintf("%s    %s\n", get_class($migration), ($migration->isPending()) ? 'Pending':'Done');
        }

        return $out;
    }

    public function migrate($migration = null) {
        $out = array();
        $migrations = $this->getMigrations();

        foreach ($migrations as $migration) {
            if ($migration->isPending()) {
                $migration->doUp();
            }

            $out = array_merge($out, $migration->getMessages());
        }

        return $out;
    }

    public function revert($migration) {

    }

    public function getMigrations() {
        $apps = \Towel\Towel::getApps();
        $migrations = array();
        foreach ($apps as $app) {
            $migrationDir = $app['path'] . '/Migrations';
            $migrationFiles = glob($migrationDir.'/m*.php');
            foreach ($migrationFiles as $migrationFile) {
                $migrationFile = basename($migrationFile);
                $migrationClass = str_replace('.php', '', $migrationFile);

                if ($app['name'] == 'Towel') {
                    $migrationClass = '\\' . $app['name'] . '\Migrations\\' . $migrationClass;
                } else {
                    $migrationClass = '\Application\\' . $app['name'] . '\Migrations\\' . $migrationClass;
                }

                $migration = new $migrationClass();
                $migrations[$migrationClass] = $migration;
            }
        }
        return $migrations;
    }

}
