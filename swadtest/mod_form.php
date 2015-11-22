<?php

/**
 * The main swadtest configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_swadtest
 * @copyright  2014 Marta Muñoz López
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form
 */
 
class mod_swadtest_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {

        global $CFG;

        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('swadtestname', 'swadtest'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'swadtestname', 'swadtest');

        // Adding the standard "intro" and "introformat" fields
        $this->add_intro_editor();

        //-------------------------------------------------------------------------------
        // Adding the rest of swadtest settings, spreeading all them into this fieldset
        // or adding more fieldsets ('header' elements) if needed for better logic
        
        $mform->addElement('header', 'swad', get_string('swad', 'swadtest'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('swad', PARAM_TEXT);
        } else {
            $mform->setType('swad', PARAM_CLEAN);
        }
        
        // Swad user for SOAP connection
        $mform->addElement('text', 'swaduser', get_string('swaduser', 'swadtest'), array('size'=>'32'));
        $mform->addRule('swaduser', null, 'required', null, 'client');
        $mform->setType('swaduser',PARAM_TEXT);
        $mform->addHelpButton('swaduser', 'swaduser', 'swadtest');
        
        // Swad password for SOAP connection
        $mform->addElement('passwordunmask', 'swadpass', get_string('swadpass', 'swadtest'), array('size'=>'32'));
        $mform->addRule('swadpass', null, 'required', null, 'client');
        $mform->setType('swadpass',PARAM_TEXT);
        $mform->addHelpButton('swadpass', 'swadpass', 'swadtest');
        
        // Swad course code
        $mform->addElement('text', 'swadcourse', get_string('swadcoursecode', 'swadtest'), array('size'=>'32'));
        $mform->addRule('swadcourse', 'Need a number', 'numeric', null, 'client');
        $mform->addRule('swadcourse', null, 'required', null, 'client');
        $mform->setType('swadcourse',PARAM_INT);
        $mform->addHelpButton('swadcourse', 'swadcoursecode', 'swadtest');
        
        
        
        //-------------------------------------------------------------------------------
        // add standard elements, common to all modules
        $this->standard_coursemodule_elements();
        //-------------------------------------------------------------------------------
        // add standard buttons, common to all modules
        $this->add_action_buttons();       
    }
  
     function data_preprocessing(&$default_values){
        global $DB;
        if (!empty($this->_instance) && ($options = $DB->get_records_menu('choice_options',array('choiceid'=>$this->_instance), 'id', 'id,text'))
               && ($options2 = $DB->get_records_menu('choice_options', array('choiceid'=>$this->_instance), 'id', 'id,maxanswers')) ) {
            $choiceids=array_keys($options);
            $options=array_values($options);
            $options2=array_values($options2);

            foreach (array_keys($options) as $key){
                $default_values['option['.$key.']'] = $options[$key];
                $default_values['limit['.$key.']'] = $options2[$key];
                $default_values['optionid['.$key.']'] = $choiceids[$key];
            }

        }
        if (empty($default_values['timeopen'])) {
            $default_values['timerestrict'] = 0;
        } else {
            $default_values['timerestrict'] = 1;
        }

    }

    function get_data() {
        $data = parent::get_data();
        if (!$data) {
            return false;
        }
        // Set up completion section even if checkbox is not ticked
        if (!empty($data->completionunlocked)) {
            if (empty($data->completionsubmit)) {
                $data->completionsubmit = 0;
            }
        }
        return $data;
    }

    function add_completion_rules() {
        $mform =& $this->_form;

        $mform->addElement('checkbox', 'completionsubmit', '', get_string('completionsubmit', 'choice'));
        return array('completionsubmit');
    }

    function completion_rule_enabled($data) {
        return !empty($data['completionsubmit']);
    }
}
