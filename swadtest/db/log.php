<?php

/**
 * Definition of log events
 *
 * NOTE: this is an example how to insert log event during installation/update.
 * It is not really essential to know about it, but these logs were created as example
 * in the previous 1.9 NEWMODULE.
 *
 * @package    mod_swadtest
 * @copyright  2011 Your Name <your@email.adress>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $DB;

$logs = array(
    array('module'=>'swadtest', 'action'=>'add', 'mtable'=>'swadtest', 'field'=>'name'),
    array('module'=>'swadtest', 'action'=>'update', 'mtable'=>'swadtest', 'field'=>'name'),
    array('module'=>'swadtest', 'action'=>'view', 'mtable'=>'swadtest', 'field'=>'name'),
    array('module'=>'swadtest', 'action'=>'view all', 'mtable'=>'swadtest', 'field'=>'name')
);
