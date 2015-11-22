<?php

/**
 * Prints a particular instance of swadtest
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_swadtest
 * @copyright  2014 Marta Muñoz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG, $USER;
 
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/format.php');
require_once($CFG->dirroot . '/question/format/gift/format.php');
//require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // swadtest instance ID - it should be named as the first character of the module

if ($id) {
    $cm         = get_coursemodule_from_id('swadtest', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $swadtest  = $DB->get_record('swadtest', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $swadtest  = $DB->get_record('swadtest', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $swadtest->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('swadtest', $swadtest->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);

add_to_log($course->id, 'swadtest', 'view', "view.php?id={$cm->id}", $swadtest->name, $cm->id);

/// Print the page header
$PAGE->set_url('/mod/swadtest/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($swadtest->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);


// Output starts here
echo $OUTPUT->header();

if ($swadtest->intro) { // Conditions to show the intro can change to look for own settings or whatever
    echo $OUTPUT->box(format_module_intro('swadtest', $swadtest, $cm->id), 'generalbox mod_introbox', 'swadtestintro');
}

//-------------------------------------------------INICIO FUNCIONES SOAP-------------------------------------------------
    // ENCRIPTACIÓN DE CONTRASEÑA DE SWAD
    $pass = hash('sha512', $swadtest->swadpass, true);    
    $pass = base64url_encode($pass);
    $appKey = "martamod";
    $content = '';

    $parameters = array(
        'userID' => $swadtest->swaduser,
        'userPassword' => $pass,
        'appKey' => $appKey
    );
    
    $client = new SoapClient("http://swad.ugr.es/ws/swad.wsdl");
   
    $login = $client->__call( "loginByUserPasswordKey", array($parameters));
    
    $parameters = array(
        'wsKey' => $login->wsKey
    );
    
    $coursesinfo = $client->__call( "getCourses", array($parameters));
    
    for ($j=0 ; $j<sizeof($coursesinfo->coursesArray->item); $j++) {
        if ($coursesinfo->coursesArray->item[$j]->courseCode == $swadtest->swadcourse) {
            $courseinfo = $coursesinfo->coursesArray->item[$j]; 
        }
    }
    
    $parameters = array(
        'wsKey' => $login->wsKey,
        'courseCode' => $swadtest->swadcourse
    );

    $testsConfi = $client->__call("getTestConfig", array($parameters));

    if ($testsConfi->pluggable==1) {
        if ($testsConfi->numQuestions>1) {
        
            echo $OUTPUT->heading($courseinfo->courseFullName);

            echo $OUTPUT->box_start();
            echo $OUTPUT->container_start();
            echo get_string('num_questions', 'swadtest')." ".$testsConfi->numQuestions;
            echo $OUTPUT->container_end(); 
            
            $parameters = array(
                'wsKey' => $login->wsKey,
                'courseCode' => $swadtest->swadcourse,
                'beginTime' => 0
            );
            $tests = $client->__call("getTests", array($parameters));
            //var_dump($tests);
            for ($j=0 ; $j< sizeof($tests->questionsArray->item); $j++) {
                $answers = array();
                $corrects = array();
                $feedbacks = array();
                $content .= "\r\n".replace_special_char ($tests->questionsArray->item[$j]->stem)." { ";
                for ($h=0 ; $h<sizeof($tests->answersArray->item); $h++) {
                    if ($tests->answersArray->item[$h]->questionCode == $tests->questionsArray->item[$j]->questionCode) {
                        switch ($tests->questionsArray->item[$j]->answerType) {
                            case 'TF':
                                $content .= " ".replace_special_char ($tests->answersArray->item[$h]->answerText);
                                if ( strlen($tests->answersArray->item[$h]->answerFeedback)>1 ) {
                                    $content .= "# ".replace_special_char ($tests->answersArray->item[$h]->answerFeedback);
                                }
                                break;
                            case 'int':
                                $content .= " #".replace_special_char ($tests->answersArray->item[$h]->answerText);
                                if ( strlen($tests->answersArray->item[$h]->answerFeedback)>1 ) {
                                    $content .= "# ".replace_special_char ($tests->answersArray->item[$h]->answerFeedback);
                                }
                                break;
                            case 'float':
                                if ($tests->answersArray->item[$h]->answerIndex==0) {
                                    $content .= " #".replace_special_char ($tests->answersArray->item[$h]->answerText);
                                } else if ($tests->answersArray->item[$h]->answerIndex==1) {
                                    $content .= "..".replace_special_char ($tests->answersArray->item[$h]->answerText);
                                }
                                if ( strlen($tests->answersArray->item[$h]->answerFeedback)>1 ) {
                                    $content .= "# ".replace_special_char ($tests->answersArray->item[$h]->answerFeedback);
                                }
                                break;   
                            case 'text':
                                $content .= "\r\n =".replace_special_char ($tests->answersArray->item[$h]->answerText);
                                if ( strlen($tests->answersArray->item[$h]->answerFeedback)>1 ) {
                                    $content .= "# ".replace_special_char ($tests->answersArray->item[$h]->answerFeedback);
                                }
                                break;
                            case 'uniqueChoice': // HASTA DIEZ RESPUESTAS POSIBLES
                                if ($tests->answersArray->item[$h]->correct==0) {
                                    $content .= "\r\n   ~".replace_special_char ($tests->answersArray->item[$h]->answerText);
                                    if ( strlen($tests->answersArray->item[$h]->answerFeedback)>1 ) {
                                        $content .= "# ".replace_special_char ($tests->answersArray->item[$h]->answerFeedback);
                                    }
                                } else if ($tests->answersArray->item[$h]->correct==1) {
                                    $content .= "\r\n   =".replace_special_char ($tests->answersArray->item[$h]->answerText);
                                    if ( strlen($tests->answersArray->item[$h]->answerFeedback)>1 ) {
                                        $content .= "# ".replace_special_char ($tests->answersArray->item[$h]->answerFeedback);
                                    }
                                }
                                break;
                            case 'multipleChoice':
                                array_push ($answers, $tests->answersArray->item[$h]->answerText);
                                array_push ($corrects, $tests->answersArray->item[$h]->correct);
                                array_push ($feedbacks, $tests->answersArray->item[$h]->answerFeedback);
                                break;
                            default:
                                echo "\nTipo de pregunta no clasificado\n";
                                break;
                            }  
                       }    
                   }
                   if ( $tests->questionsArray->item[$j]->answerType=='multipleChoice' ) {
                       $cont = array_count_values($corrects);
                       $correct = 100.0/$cont['1'];
                       $incorrect = 100.0/$cont['0'];
                       for ($n=0; $n<sizeof($corrects); $n++) {
                           if ($corrects[$n]==0) {
                               $content .= "\r\n   ~%-".$incorrect."%".replace_special_char ($answers[$n]);
                           } else if ($corrects[$n]==1) {
                               $content .= "\r\n   ~%".$correct."%".replace_special_char ($answers[$n]);
                           }
                           if ( (sizeof($feedbacks[$n])>1) && ($feedbacks[$n]!=null) )
                               $content .= "#".replace_special_char ($feedbacks[$n]);
                       }
                       $answers = array();
                       $corrects = array();
                   }
                   $content .= " } \r\n";
               }
               
               $filename = $courseinfo->courseShortName."".date(DATE_ATOM).".txt";
               $fs = get_file_storage();
               
               // CONTEXTO DEL USUARIO PARA QUE SEAN ARCHIVOS PRIVADOS DEL USUARIO
               $contextuser = context_user::instance($USER->id);

               // Prepare file record object
               $fileinfo = array(
                   'contextid' => $contextuser->id, // ID of context
                   'component' => 'user',     // usually = table name
                   'filearea' => 'private',     // usually = table name
                   'itemid' => 0,               // usually = ID of row in table
                   'filepath' => '/',           // any path beginning and ending in /
                   'filename' => $filename); // any filename 
                
               // Create file containing text 'hello world'
               $fs->create_file_from_string($fileinfo, $content);            
  
               $url = $CFG->wwwroot.'/pluginfile.php/'.$contextuser->id.'/user/private/'.$filename;
            
               echo $OUTPUT->container_start();
               echo html_writer::link($url, get_string( 'download_file', 'swadtest'));
               echo $OUTPUT->container_end();
               echo $OUTPUT->box_end();
           }
        }
//-------------------------------------------------FIN FUNCIONES SOAP-------------------------------------------------

// Finish the page
echo $OUTPUT->footer();

?>