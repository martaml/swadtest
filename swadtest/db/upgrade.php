<?php

/**
 * This file keeps track of upgrades to the swadtest module
 *
 * Sometimes, changes between versions involve alterations to database
 * structures and other major things that may break installations. The upgrade
 * function in this file will attempt to perform all the necessary actions to
 * upgrade your older installation to the current version. If there's something
 * it cannot do itself, it will tell you what you need to do.  The commands in
 * here will all be database-neutral, using the functions defined in DLL libraries.
 *
 * @package    mod_swadtest
 * @copyright  2014 Marta Muñoz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute swadtest upgrade from the given old version
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_swadtest_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes
    
    if ($oldversion < 2014112904) {

        // Define table swadtest to be created.
        $table = new xmldb_table('swadtest');

        // Adding fields to table swadtest.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('intro', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('introformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('swaduser', XMLDB_TYPE_CHAR, '127', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('swadpass', XMLDB_TYPE_CHAR, '256', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('swadcourse', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table swadtest.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table swadtest.
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));

        // Conditionally launch create table for swadtest.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Swadtest savepoint reached.
        upgrade_mod_savepoint(true, 2014112904, 'swadtest');
    }


    // 12/11/2014
    /*if ($oldversion < 2014111201) {
    
	$table = new xmldb_table('swadtest');
	$dbman->create_table($table);

        // Define field timecreated to be added to swadtest
        $table = new xmldb_table('swadtest');
        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0',
            'introformat');

        // Add field timecreated
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field timemodified to be added to swadtest
        $table = new xmldb_table('swadtest');
        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0',
            'timecreated');

        // Add field timemodified
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define index course (not unique) to be added to swadtest
        $table = new xmldb_table('swadtest');
        $index = new xmldb_index('courseindex', XMLDB_INDEX_NOTUNIQUE, array('course'));

        // Add index to course field
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        
        // Another save point reached
        upgrade_mod_savepoint(true, 2014111201, 'swadtest');
    }*/

    // Final return of upgrade result (true, all went good) to Moodle.
    return true;
}




