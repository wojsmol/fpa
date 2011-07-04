<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" >

<?php
/**
 **  @package Forum Post Assistant
 **  @version 1.2.0
 **  @release playGround
 **  @date 24/06/2011
 **  @author RussW
 **/


    /** SET THE FPA DEFAULTS *****************************************************************/
    define ( '_FPA_DEV', 1 );   // developer-mode
    //define ( '_FPA_DIAG', 1 );  // diagnostic-mode
    $protected = '0';



    /** TIMER-POPS ***************************************************************************/
    // mt_get: returns the current microtime
    function mt_get(){
        global $mt_time;
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    // mt_start: starts the microtime counter
    function mt_start(){
        global $mt_time; $mt_time = mt_get();
    }

    // mt_end: calculates the elapsed time
    function mt_end($len=3){
        global $mt_time;
        $time_end = mt_get();
        return round($time_end - $mt_time, $len);
    }

    // start the timer-pop
    mt_start();


    // build the initial arrays used throughout fpa
    $fpa['ARRNAME'] = 'Forum Post Assistant';
    $fpa['diagLOG'] = 'fpaDiag.log';
    $instance['ARRNAME'] = 'Application Instance';
    $system['ARRNAME'] = 'Systems Environment';
    $phpenv['ARRNAME'] = 'PHP Environment';
    $phpextensions['ARRNAME'] = 'PHP Extensions';
    $apachemodules['ARRNAME'] = 'Apache Modules';
    $database['ARRNAME'] = 'dataBase Instance';
    $modecheck['ARRNAME'] = 'Permissions Checks';
    // folders to be tested for permissions
    $folders['ARRNAME'] = 'Selected Folders';
    $folders[] = 'images/';
    $folders[] = 'components/';
    $folders[] = 'modules/';
    $folders[] = 'plugins/';
    $folders[] = 'mambots/';
    $folders[] = 'language/';
    $folders[] = 'templates/';
    $folders[] = 'cache/';
    $folders[] = 'logs/';
    $folders[] = 'tmp/';
    $folders[] = 'administrator/components/';
    $folders[] = 'administrator/modules/';
    $folders[] = 'administrator/language/';
    $folders[] = 'administrator/templates/';
    $folders[] = 'test/';


    // build the developer-mode function to display the raw arrays
    function showDev( &$section ) {

        // this can only have inline styling because it is outputed before the html styling
        if ( defined( '_FPA_DEV' ) ) {
            echo '<div style="width:750px;margin: 0px auto;margin-bottom:10px;font-family:arial;font-size:10px;color:#808080;">';
            echo '<div style="text-shadow: 1px 1px 1px #F5F5F5;font-weight:bold;color:#4D8000;text-transform:uppercase;padding-bottom:2px;">';
            echo '<span style="color: #808080;font-weight:normal;text-transform:lowercase;">[developer-mode information]</span><br />';
            echo $section['ARRNAME'] .' Array :';
            echo '</div>';
            echo '<div style="-moz-box-shadow: inset -3px -3px 3px #CAE897;-webkit-box-shadow: inset -3px -3px 3px #CAE897;box-shadow: inset -3px -3px 3px #CAE897;padding:5px;background-color:#E2F4C4; border:1px solid #4D8000;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">';
                print_r ( $section );
            echo '<p><em>elapse runtime: <strong>'. mt_end() .'</strong> seconds</em></p>';
            echo '</div>';
            echo '</div>';
        } // end if _FPA_DEV defined

     } // end developer-mode function
?>





<?php
    /** DETERMINE SOME SETTINGS BEFORE FPA MIGHT PLAY WITH THEM ******************************/
    $phpenv['phpERRORDISPLAY'] = ini_get('display_errors');
    $phpenv['phpERRORREPORT'] = ini_get('error_reporting');
    $phpenv['phpERRLOGFILE'] = ini_get( 'error_log' );
    $system['sysSHORTOS'] = strtoupper( substr( PHP_OS, 0, 3 ) ); // WIN, DAR, LIN, SOL
    $system['sysSERVSOFTWARE'] = strtoupper( substr( $_SERVER['SERVER_SOFTWARE'], 0, 3 ) ); // APA = Apache, MIC = MS IIS

    /** DETERMINE IF THERE IS A KNOWN ERROR ALREADY *******************************************
     ** here we try and determine if there is an existing php error log file, if there is we
     ** then look to see how old it is, if it's less than one day old, lets see if what the last
     ** error this and try and auto-enter that as the problem description
     *****************************************************************************************/

    /** is there an existing php error-log file? *********************************************/
    if ( file_exists( $phpenv['phpERRLOGFILE'] ) ) {

        // when was the file last modified?
        $phpenv['phpLASTERRDATE'] = date ("dS F Y H:i:s.", filemtime( $phpenv['phpERRLOGFILE'] ));

        // determine the number of seconds for one day
        $age = 1 * 60*60*24;
        // get the modified time in seconds
        $file_time = filemtime( $phpenv['phpERRLOGFILE'] );
        // get the current time in seconds
        $now_time = time();

            // if the file was modified less than one day ago, grab the last error entry
            if ( $file_time - $now_time < $age ) {
                $phpenv['phpLASTERR'] = array_pop( file( $phpenv['phpERRLOGFILE'] ) );
            }
    }
?>





<?php
    /** SEEING A WHITE SCREEN WHILST RUNNING FPA? OR SOMEONE HELPING YOU SENT YOU HERE? *******
     ** uncomment _FPA_DIAG above and re-run FPA
     **
     ** display_errors, enables php errors to be displayed on the screen
     ** error_reporting, sets the level of errors to report, "-1" is all errors
     ** log_errors, enables errors to be logged to a file, fpa_error.log in the "/" folder
     *****************************************************************************************/

    if ( defined( '_FPA_DEV' ) OR defined( '_FPA_DIAG' ) ) {

        // these can only have inline styling because it is outputed before the html styling
        echo '<div style="text-align:center; margin:0px auto; margin-bottom: 5px; width:750px;">';

        if ( defined( '_FPA_DEV' ) AND defined( '_FPA_DIAG' ) ) {
            $divwidth = '350px';
        } else {
            $divwidth = '740px';
        }
            // display developer-mode notice
            if ( defined( '_FPA_DEV' ) ) {
                ini_set('display_errors','Off'); // default-display

                echo '<div style="text-shadow: 1px 1px 1px #FFF;float:right; text-align:center; width:'. $divwidth .'; background-color:#CAFFD8; border:1px solid #4D8000; color:#404040; font-size:10px; font-family:arial; padding:5px;-moz-box-shadow: 3px 3px 3px #C0C0c0;-webkit-box-shadow: 3px 3px 3px #C0C0c0;box-shadow: 3px 3px 3px #C0C0c0;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">';
                echo '<strong style="color:#4D8000;">DEVELOPER MODE is enabled</strong><br />';
                echo 'This means that a variety of diagnostic information will be displayed on-screen to assist with fpa troubleshooting.';
                echo '</div>';
            } // end developer-mode display


            // display diagnostic-mode notice
            if ( defined( '_FPA_DIAG' ) ) {
                ini_set('display_errors','On');
                error_reporting( -1 );
                ini_set('error_log', $fpa['diagLOG'] );

                echo '<div style="text-shadow: 1px 1px 1px #FFF;float:left; text-align:center; width:'. $divwidth .'; background-color:#CAFFD8; border:1px solid #4D8000; color:#404040; font-size:10px; font-family:arial; padding:5px;-moz-box-shadow: 3px 3px 3px #C0C0c0;-webkit-box-shadow: 3px 3px 3px #C0C0c0;box-shadow: 3px 3px 3px #C0C0c0;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">';
                echo '<strong style="color:#4D8000;">DIAGNOSTIC MODE is enabled</strong><br />';
                echo 'This means that all php errors will be displayed on-screen and logged out to a file named '. $fpa['diagLOG'] .'.';
                echo '</div>';


                if ( file_exists( $fpa['diagLOG'] ) ) {
                    echo '<br style="clear:both;" /><div style="margin-top:10px;text-align:left;text-shadow: 1px 1px 1px #FFF; width:740px; background-color:#FFFFCC; border:1px solid #800000; color:#404040; font-size:10px; font-family:arial; padding:5px;-moz-box-shadow: 3px 3px 3px #C0C0c0;-webkit-box-shadow: 3px 3px 3px #C0C0c0;box-shadow: 3px 3px 3px #C0C0c0;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">';
                    echo '<strong style="color:#800000;">LAST DIAGNOSTIC MODE ERROR</strong> in '. $fpa['diagLOG'] .'<br />';

                    $fpa['fpaLASTERR'] = @array_pop( file( $fpa['diagLOG'] ) );
                    echo $fpa['fpaLASTERR'];
                    echo '</div>';
                } else {
                    echo '<br style="clear:both;" /><div style="margin-top:10px;text-align:left;text-shadow: 1px 1px 1px #FFF; width:740px; background-color:#FFFFCC; border:1px solid #800000; color:#404040; font-size:10px; font-family:arial; padding:5px;-moz-box-shadow: 3px 3px 3px #C0C0c0;-webkit-box-shadow: 3px 3px 3px #C0C0c0;box-shadow: 3px 3px 3px #C0C0c0;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">';
                    echo '<strong style="color:#800000;">LAST DIAGNOSTIC MODE ERROR</strong> in '. $fpa['diagLOG'] .'<br />';
                    echo 'No FPA errors to report at the moment';
                    echo '</div>';
                }

            } // end diagnostic-mode display


        echo '<br style="clear:both;" />';
        echo '</div>';

    } else { // end developer- or diag -mode display
      ini_set('display_errors','Off'); // default-display
    }


    // This is a test variable to check that diagnostic mode works, uncomment to cause an Undefined Variable notice
    // this will display an error if Developer-mode or diagnostic-mode are enabled, otherwise you shouldn't see an error message
    //echo $ExpectedDiagDevModeErrorVariable;


    /** SET THE JOOMLA! PARENT FLAG AND CONSTANTS ********************************************/
    define ( '_VALID_MOS', 1 ); // for J!1.0
    define ( '_JEXEC', 1 );     // for J!1.5, J!1.6, J!1.7
    define ( 'JPATH_BASE', dirname( __FILE__ ) );


    /** DEFINE LANGUAGE STRINGS **************************************************************/
    define ( '_RES', 'Forum Post Assistant' );
    define ( '_RES_VERSION', '1.2.0' );
    define ( '_RES_RELEASE', 'Alpha' ); // can be Alpha, Beta, RC, Final
    define ( '_RES_BRANCH', 'playGround' ); // can be playGround, currentDevelopment, masterPublic
    define ( '_RES_FPALINK', 'https://github.com/ForumPostAssistant/FPA/archives/masterPublic' ); // where to get the latest 'Final release'
    define ( '_RES_FPALATEST', 'Get the latest release of the ' );

    /** php options and messages *************************************************************/
    define ( '_PHP_DISERR', 'Display PHP Errors Enabled' );
    define ( '_PHP_ERRREP', 'PHP Error Reporting Enabled' );
    define ( '_PHP_LOGERR', 'PHP Errors Being Logged To File' );

    /** user instructions and data entry fields **********************************************/
    define ( '_FPA_INSTRUCTIONS', 'Instructions' );
    define ( '_FPA_INS_1', 'Complete your problem description <i>(optional)</i>' );
    define ( '_FPA_INS_2', 'Add <i>single line</i> error message of log entry <i>(optional)</i>' );
    define ( '_FPA_INS_3', 'Explain what actions have already been taken to resolve this issue <i>(optional)</i>' );
    define ( '_FPA_INS_4', 'Select additional level(s) of detail to generate in post.' );
    define ( '_FPA_INS_5', 'Click "<em>Generate Forum Post</em>" button.' );
    define ( '_FPA_POST_NOTE', 'Leave ALL fields blank/empty to simply post diagnostic information.' );
    define ( '_FPA_PROB_DESC', 'Problem Description' );
    define ( '_FPA_LOG_MSG', 'Log/Error Message' );
    define ( '_FPA_ACTION', 'Actions Taken To Resolve' );

    /** common screen and post output strings ************************************************/
    define ( '_FPA_Y', 'Yes' );
    define ( '_FPA_N', 'No' );
    define ( '_FPA_M', 'Maybe' );
    define ( '_FPA_U', 'Unknown' );
    define ( '_FPA_E', 'Exists' );
    define ( '_FPA_DNE', 'Does Not Exist' );
    define ( '_FPA_F', 'Found' );
    define ( '_FPA_NF', 'Not Found' );
    define ( '_FPA_NA', 'N/A' );
    define ( '_FPA_HIDDEN', 'protected' );
    define ( '_FPA_MODE', 'Mode' );
    define ( '_FPA_WRITABLE', 'Writable' );
    define ( '_FPA_FOLDER', 'Folder' );
    define ( '_FPA_FILE', 'File' );
    define ( '_FPA_OWNER', 'Owner' );
    define ( '_FPA_GROUP', 'Group' );
    define ( '_FPA_VER', 'Version' );
    // instance test strings
    // system test strings
    // php test strings
    define ( '_PHP_VERLOW', 'PHP version too low' );
    // web-server test strings
    // mysql test strings
    // permissions test strings
    /** END LANGUAGE STRINGS *****************************************************************/
?>





<?php
    /** DETERMINE INSTANCE STATUS & VERSIONING ************************************************
     ** here we check for known files to determine if an instance even exists, then we look for
     ** the version and configuration files. some differ between between versions, so we have a
     ** bit of jiggling to do.
     ** to try and avoid "white-screens" fpa no-longer "includes" these files, but merely tries
     ** to open and read them, although this is slower, it improves the reliability of fpa.
     *****************************************************************************************/

    /** is an instance present? **************************************************************/
    // this is a two-fold sanity check, we look two pairs of known folders, only one pair need exist
    // this caters for the potential of missing folders, but is not exhaustive or too time consuming
    if ( ( file_exists( 'components/' ) AND file_exists( 'modules/' ) ) OR ( file_exists( 'administrator/components/' ) AND file_exists( 'administrator/modules/' ) ) ) {
        $instance['instanceFOUND'] = _FPA_Y;
    } else {
        $instance['instanceFOUND'] = _FPA_N;
    }


    /** what version is the instance? ********************************************************/
    if ( file_exists( 'includes/version.php' ) AND file_exists( 'mambots/' ) ) {
    // J1.0 includes/version.php & mambots folder
        $instance['cmsVFILE'] = 'includes/version.php';

    } elseif ( file_exists( 'libraries/joomla/version.php' ) AND file_exists( 'xmlrpc/' ) ) {
    // J1.5 libraries/joomla/version.php & xmlrpc folder
        $instance['cmsVFILE'] = 'libraries/joomla/version.php';

    } elseif ( file_exists( 'libraries/joomla/version.php' ) AND file_exists( 'joomla.xml' ) ) {
    // J1.6 libraries/joomla/version.php & joomla.xml files
        $instance['cmsVFILE'] = 'libraries/joomla/version.php';

    } elseif ( file_exists( 'includes/version.php' ) AND file_exists( 'libraries/joomla/platform.php' ) ) {
    // J1.7 includes/version.php & libraries/joomla/platform.php files
        $instance['cmsVFILE'] = 'includes/version.php';

    } else {
    // fpa could find the required files to determine version(s)
        $instance['cmsVFILE'] = _FPA_N;
    }


    /** what version is the framework? (J!1.7 & above) ***************************************/
    if ( file_exists( 'libraries/joomla/platform.php' ) ) {
    // J1.7 libraries/joomla/platform.php
        $instance['platformVFILE'] = 'libraries/joomla/platform.php';
    } else {
        $instance['platformVFILE'] = _FPA_N;
    }


    // read the cms version file into $cmsVContent (all versions)
    if ( $instance['cmsVFILE'] != _FPA_N ) {
        $cmsVContent = file_get_contents( $instance['cmsVFILE'] );

        // find the basic cms information
        preg_match ( '#\$PRODUCT.*=\s[\'|\"](.*)[\'|\"];#', $cmsVContent, $cmsPRODUCT );
        preg_match ( '#\$RELEASE.*=\s[\'|\"](.*)[\'|\"];#', $cmsVContent, $cmsRELEASE );
        preg_match ( '#\$(DEV_LEVEL.*|MAINTENANCE.*)=\s[\'|\"](.*)[\'|\"];#', $cmsVContent, $cmsDEVLEVEL );
        preg_match ( '#\$(DEV_STATUS.*|STATUS.*)=\s[\'|\"](.*)[\'|\"];#', $cmsVContent, $cmsDEVSTATUS );
        preg_match ( '#\$(CODENAME.*|CODE_NAME.*)=\s[\'|\"](.*)[\'|\"];#', $cmsVContent, $cmsCODENAME );
        preg_match ( '#\$(RELDATE.*|RELEASE_DATE.*)=\s[\'|\"](.*)[\'|\"];#', $cmsVContent, $cmsRELDATE );

            $instance['cmsPRODUCT'] = $cmsPRODUCT[1];
            $instance['cmsRELEASE'] = $cmsRELEASE[1];
            $instance['cmsDEVLEVEL'] = $cmsDEVLEVEL[2];
            $instance['cmsDEVSTATUS'] = $cmsDEVSTATUS[2];
            $instance['cmsCODENAME'] = $cmsCODENAME[2];
            $instance['cmsRELDATE'] = $cmsRELDATE[2];
    }


    // read the platform version file into $platformVContent (J!1.7 & above only)
    if ( $instance['platformVFILE'] != _FPA_N ) {
        $platformVContent = file_get_contents( $instance['platformVFILE'] );

        // find the basic platform information
        preg_match ( '#PRODUCT.*=\s[\'|\"](.*)[\'|\"];#', $platformVContent, $platformPRODUCT );
        preg_match ( '#RELEASE.*=\s[\'|\"](.*)[\'|\"];#', $platformVContent, $platformRELEASE );
        preg_match ( '#MAINTENANCE.*=\s[\'|\"](.*)[\'|\"];#', $platformVContent, $platformDEVLEVEL );
        preg_match ( '#STATUS.*=\s[\'|\"](.*)[\'|\"];#', $platformVContent, $platformDEVSTATUS );
        preg_match ( '#CODE_NAME.*=\s[\'|\"](.*)[\'|\"];#', $platformVContent, $platformCODENAME );
        preg_match ( '#RELEASE_DATE.*=\s[\'|\"](.*)[\'|\"];#', $platformVContent, $platformRELDATE );

            $instance['platformPRODUCT'] = $platformPRODUCT[1];
            $instance['platformRELEASE'] = $platformRELEASE[1];
            $instance['platformDEVLEVEL'] = $platformDEVLEVEL[1];
            $instance['platformDEVSTATUS'] = $platformDEVSTATUS[1];
            $instance['platformCODENAME'] = $platformCODENAME[1];
            $instance['platformRELDATE'] = $platformRELDATE[1];
    }


    /** is Joomla! installed/configured? *****************************************************/
    if ( file_exists( 'configuration.php' ) ) {
    // find the configuration.php file (all versions)
        $instance['instanceCONFIGURED'] = _FPA_Y;

        // determine it's ownership and mode
        if ( is_writable( 'configuration.php' ) ) {
		  $instance['configWRITABLE']	= _FPA_Y;
        } else {
		  $instance['configWRITABLE']	= _FPA_N;
        }

        $instance['configMODE'] = substr( sprintf('%o', fileperms( 'configuration.php' ) ),-3, 3 );

        if ( $system['sysSHORTOS'] != 'WIN' ) { // gets the UiD and converts to 'name' on non Windows systems
            $instance['configOWNER'] = posix_getpwuid(fileowner('configuration.php'));
            $instance['configGROUP'] = posix_getgrgid(filegroup('configuration.php'));
        } else { // only get the UiD for Windows, not 'name'
            $instance['configOWNER']['name'] = fileowner( 'configuration.php' );
            $instance['configGROUP']['name'] = filegroup( 'configuration.php' );
        }


        /** if present, is the configuration file valid? *****************************************/
        $cmsCContent = file_get_contents( 'configuration.php' );

            if ( preg_match ( '#(\$mosConfig_)#', $cmsCContent ) ) {
                $instance['configVALIDFOR'] = '1.0';
            } elseif ( preg_match ( '#(var)#', $cmsCContent ) ) {
                $instance['configVALIDFOR'] = '1.5';
            } elseif ( preg_match ( '#(public)#', $cmsCContent ) AND $instance['platformVFILE'] == _FPA_N ) {
                $instance['configVALIDFOR'] = '1.6';
            } elseif ( preg_match ( '#(public)#', $cmsCContent ) AND $instance['platformVFILE'] != _FPA_N ) {
                $instance['configVALIDFOR'] = '1.7 and above';
            } else {
                $instance['configVALIDFOR'] = _FPA_U;
            }


                // fpa found a configuration.php but couldn't determine the version, is it valid?
                if ( $instance['configVALIDFOR'] == _FPA_U ) {

                    if ( filesize( 'configuration.php' ) < 512 ) {
                        $instance['configSIZEVALID'] = _FPA_N;
                    }

                }

    // check if the configuration.php version matches the discovered version
    if ( $instance['configVALIDFOR'] != _FPA_U AND $instance['cmsVFILE'] != _FPA_N ) {

        if ( version_compare( $instance['cmsRELEASE'], substr( $instance['configVALIDFOR'],0,3 ), '==' ) ) {
            $instance['instanceCFGVERMATCH'] = _FPA_Y;
        } else {
            $instance['instanceCFGVERMATCH'] = _FPA_N;
        }


    // set defaults for the configuration's validity and a sanity score of zero
    $instance['configSANE'] = _FPA_N;
    $instance['configSANITYSCORE'] = 0;

        // !TODO add white-space etc checks
        // do some configuration.php sanity/validity checks
        if ( filesize( 'configuration.php' ) > 512 ) {
            $instance['cfgSANITY']['configSIZEVALID'] = _FPA_Y;
        }


        // !TODO FINISH  white-space etc checks
        $instance['cfgSANITY']['configNOTDIST'] = _FPA_Y; // is not the distribution example
        $instance['cfgSANITY']['configNOWSPACE'] = _FPA_Y; // no white-space
        $instance['cfgSANITY']['configOPTAG'] = _FPA_Y; // has php open tag
        $instance['cfgSANITY']['configCLTAG'] = _FPA_Y; // has php close tag
        $instance['cfgSANITY']['configJCONFIG'] = _FPA_Y; // has php close tag

        // run through the sanity checks, if sane ( =Yes ) increment the score by 1 (should total 6)
        foreach ( $instance['cfgSANITY'] as $i => $sanityCHECK ) {

            if ( $instance['cfgSANITY'][$i] == _FPA_Y ) {
                $instance['configSANITYSCORE'] = $instance['configSANITYSCORE'] +1;
            }

        }

        // if the configuration file is sane, set it as valid
        if ( $instance['configSANITYSCORE'] == '6' ) {
            $instance['configSANE'] = _FPA_Y; // configuration appears valid?
        }

    } else {
        $instance['instanceCFGVERMATCH'] = _FPA_U;
    }


    // common configuration variables for J!1.5 and above only
    if ( $instance['configVALIDFOR'] != _FPA_U ) {

        // common configuration variable across all versions
        preg_match ( '#\$(mosConfig_offline.*|offline.*)=\s[\'|\"](.*)[\'|\"];#', $cmsCContent, $configOFFLINE );
        preg_match ( '#\$(mosConfig_sef.*|sef.*)=\s[\'|\"](.*)[\'|\"];#', $cmsCContent, $configSEF );
        preg_match ( '#\$(mosConfig_gzip.*|gzip.*)=\s[\'|\"](.*)[\'|\"];#', $cmsCContent, $configGZIP );
        preg_match ( '#\$(mosConfig_caching.*|caching.*)=\s[\'|\"](.*)[\'|\"];#', $cmsCContent, $configCACHING );
        preg_match ( '#\$(mosConfig_error_reporting.*|error_reporting.*)=\s[\'|\"](.*)[\'|\"];#', $cmsCContent, $configERRORREP );
        preg_match ( '#\$(mosConfig_debug.*|debug.*)=\s[\'|\"](.*)[\'|\"];#', $cmsCContent, $configSITEDEBUG );
        preg_match ( '#dbtype.*=\s[\'|\"](.*)[\'|\"];#', $cmsCContent, $configDBTYPE );

            // J!1.0 assumed 'mysql' with no variable, so we'll just add it
            if (!array_key_exists('1', $configDBTYPE)) {
                $configDBTYPE[1] = 'mysql';
            }

        preg_match ( '#\$(mosConfig_host.*|host.*)=\s[\'|\"](.*)[\'|\"];#', $cmsCContent, $configDBHOST );
        preg_match ( '#\$(mosConfig_db.*|db\s.*)=\s[\'|\"](.*)[\'|\"];#', $cmsCContent, $configDBNAME );
        preg_match ( '#\$(mosConfig_dbprefix.*|dbprefix.*)=\s[\'|\"](.*)[\'|\"];#', $cmsCContent, $configDBPREF );
        preg_match ( '#\$(mosConfig_user.*|user.*)=\s[\'|\"](.*)[\'|\"];#', $cmsCContent, $configDBUSER );
        preg_match ( '#\$(mosConfig_password.*|password.*)=\s[\'|\"](.*)[\'|\"];#', $cmsCContent, $configDBPASS );

            $instance['configOFFLINE'] = $configOFFLINE[2];
            $instance['configSEF'] = $configSEF[2];
            $instance['configGZIP'] = $configGZIP[2];
            $instance['configCACHING'] = $configCACHING[2];
            $instance['configERRORREP'] = $configERRORREP[2];
            $instance['configSITEDEBUG'] = $configSITEDEBUG[2];
            $instance['configDBTYPE'] = $configDBTYPE[1];
            $instance['configDBHOST'] = $configDBHOST[2];
            $instance['configDBNAME'] = $configDBNAME[2];
            $instance['configDBPREF'] = $configDBPREF[2];
            $instance['configDBUSER'] = $configDBUSER[2];
            $instance['configDBPASS'] = $configDBPASS[2];
            // force all the configuration settings that are either depreciated or unused by the lowest support release (ie: J!1.0)
            $instance['configLANGDEBUG'] = _FPA_NA;
            $instance['configSEFSUFFIX'] = _FPA_NA;
            $instance['configSEFRWRITE'] = _FPA_NA;
            $instance['configFTP'] = _FPA_NA;
            $instance['configSSL'] = _FPA_NA;
            $instance['configACCESS'] = _FPA_NA;
            $instance['configUNICODE'] = _FPA_NA;
            // these forced settings will be over-written later by the variable supported release
    }


        // common configuration variables for J!1.5 and above only
        if ( $instance['configVALIDFOR'] != '1.0' AND $instance['configVALIDFOR'] != _FPA_U ) {

            preg_match ( '#sef_rewrite.*=\s[\'|\"](.*)[\'|\"];#', $cmsCContent, $configSEFREWRITE );
            preg_match ( '#sef_suffix.*=\s[\'|\"](.*)[\'|\"];#', $cmsCContent, $configSEFSUFFIX );
            preg_match ( '#debug_lang.*=\s[\'|\"](.*)[\'|\"];#', $cmsCContent, $configLANGDEBUG );
            preg_match ( '#ftp_enable.*=\s[\'|\"](.*)[\'|\"];#', $cmsCContent, $configFTP );
            preg_match ( '#force_ssl.*=\s[\'|\"](.*)[\'|\"];#', $cmsCContent, $configSSL );

                $instance['configSEFRWRITE'] = $configSEFREWRITE[1];
                $instance['configSEFSUFFIX'] = $configSEFSUFFIX[1];
                $instance['configLANGDEBUG'] = $configLANGDEBUG[1];
                $instance['configFTP'] = $configFTP[1];
                $instance['configSSL'] = $configSSL[1];
        }


        // common configuration variables for J!1.6 and above only
        if ( $instance['configVALIDFOR'] != '1.0' AND $instance['configVALIDFOR'] != '1.5' AND $instance['configVALIDFOR'] != _FPA_U ) {

            preg_match ( '#access.*=\s[\'|\"](.*)[\'|\"];#', $cmsCContent, $configACCESS );
            preg_match ( '#unicodeslugs.*=\s[\'|\"](.*)[\'|\"];#', $cmsCContent, $configUNICODE );

                $instance['configACCESS'] = $configACCESS[1];
                $instance['configUNICODE'] = $configUNICODE[1];
        }

        // check if all the DB credentials are complete
        if ( @$instance['configDBTYPE'] AND $instance['configDBHOST'] AND $instance['configDBNAME'] AND $instance['configDBPREF'] AND $instance['configDBUSER'] AND $instance['configDBPASS'] ) {
            $instance['configDBCREDOK'] = _FPA_Y;
        } else {
            $instance['configDBCREDOK'] = _FPA_N;
        }


        // looking for htaccess (Apache and some others) or web.config (IIS)
        if ( $system['sysSERVSOFTWARE'] != 'MIC' ) {

            // htaccess files
            if ( file_exists( '.htaccess' ) ) {
                $instance['configSITEHTWC'] = _FPA_Y;
            } else {
                $instance['configSITEHTWC'] = _FPA_N;
            }

            if ( file_exists( 'administrator/.htaccess' ) ) {
                $instance['configADMINHTWC'] = _FPA_Y;
            } else {
                $instance['configADMINHTWC'] = _FPA_N;
            }

        } else {

            // web.config file
            if ( file_exists( 'web.config' ) ) {
                $instance['configSITEHTWC'] = _FPA_Y;
                $instance['configADMINHTWC'] = _FPA_NA;
            } else {
                $instance['configSITEHTWC'] = _FPA_N;
                $instance['configADMINHTWC'] = _FPA_NA;
            }

        }


    } else { // no configuration.php found
        $instance['instanceCONFIGURED'] = _FPA_N;
        $instance['configVALIDFOR'] = _FPA_U;
    }
?>





<?php
    /** DETERMINE SYSTEM ENVIRONMENT & SETTINGS ***********************************************
     ** here we try to determine the hosting enviroment and configuration
     ** to try and avoid "white-screens" fpa tries to check for function availability before
     ** using any function, but this does mean it has grown in size quite a bit and unfortunately
     ** gets a little messy in places.
     *****************************************************************************************/

    /** what server and os is the host? ******************************************************/
    $system['sysPHPVERSION'] = phpversion();
    $system['sysPLATFORM'] = php_uname('v');
    $system['sysPLATNAME'] = php_uname('n');
    $system['sysPLATTECH'] = php_uname('m');
    $system['sysSERVNAME'] = $_SERVER['SERVER_NAME'];
    $system['sysSERVIP'] = gethostbyname($_SERVER['SERVER_NAME']);
    $system['sysSERVSIG'] = $_SERVER['SERVER_SOFTWARE'];
    $system['sysENCODING'] = $_SERVER["HTTP_ACCEPT_ENCODING"];
    $system['sysCURRUSER'] = get_current_user(); // current process user
    $system['sysSERVIP'] = gethostbyname($_SERVER['SERVER_NAME']);
    //!TESTME for WIN IIS7?
    //$system['sysSERVIP'] =  $_SERVER['LOCAL_ADDR'];
	if ( $system['sysSHORTOS'] != 'WIN' ) {
    // !BUGID #1 $_ENV USER doesn't work on lightspeed server? maybe tie it down to apache only
	    $system['sysEXECUSER'] = $_ENV['USER']; // user that executed this script
        $system['sysDOCROOT'] = $_SERVER['DOCUMENT_ROOT'];
	} else {
        $localpath = getenv( 'SCRIPT_NAME' );
        $absolutepath = str_replace( '\\', '/', realpath( basename( getenv( 'SCRIPT_NAME' ) ) ) );
            $system['sysDOCROOT'] = substr( $absolutepath, 0, strpos( $absolutepath, $localpath ) );
            $system['sysEXECUSER'] = $system['sysCURRUSER']; // Windows work-around for not using EXEC User
	}


        // looking for the Apache "suExec" Utility
	    if ( function_exists( 'exec' ) AND $system['sysSHORTOS'] != 'WIN' ) { // find the owner of the current process running this script
            $system['sysWEBOWNER'] = exec("whoami");
        } elseif ( function_exists( 'passthru' ) AND $system['sysSHORTOS'] != 'WIN' ) {
            $system['sysWEBOWNER'] = passthru("whoami");
        } else {
            $system['sysWEBOWNER'] = _FPA_NA;
        }


        // find the system temp directory
        if ( version_compare( PHP_VERSION, '5.2.1', '>=' ) ) {
            $system['sysSYSTMPDIR'] = sys_get_temp_dir();

            // is the system /tmp writable to this user?
            if ( is_writable( sys_get_temp_dir() ) ) {
	   	      $system['sysTMPDIRWRITABLE'] = _FPA_Y;
            } else {
		      $system['sysTMPDIRWRITABLE'] = _FPA_N;
            }

        }
?>





<?php
    /** DETERMINE PHP ENVIRONMENT & SETTINGS ***********************************************
     ** here we try to determine the php enviroment and configuration
     ** to try and avoid "white-screens" fpa tries to check for function availability before
     ** using any function, but this does mean it has grown in size quite a bit and unfortunately
     ** gets a little messy in places.
     *****************************************************************************************/

    /** general system related settings? *****************************************************/
    // find the current php.ini file
    if ( version_compare( PHP_VERSION, '5.2.4', '>=' ) ) {
        $phpenv['phpINIFILE'] = php_ini_loaded_file();
    } else {
        $phpenv['phpINIFILE'] = _FPA_U;
    }


    // find the other loaded php.ini file(s)
    if (version_compare(PHP_VERSION, '4.3.0', '>=')) {
        $phpenv['phpINIOTHER'] = php_ini_scanned_files();
    } else {
        $phpenv['phpINIOTHER'] = _FPA_U;
    }

    $phpenv['phpREGGLOBAL'] = ini_get( 'register_globals' );
    $phpenv['phpMAGICQUOTES'] = ini_get( 'magic_quotes_gpc' );
    $phpenv['phpSAFEMODE'] = ini_get( 'safe_mode' );
    $phpenv['phpOPENBASE'] = ini_get( 'open_basedir' );
    $phpenv['phpMAGICQUOTES'] = ini_get( 'magic_quotes_gpc' );
    $phpenv['phpSESSIONPATH'] = session_save_path();

    // if open_basedir is in effect, don't bother doing session_save.path test, will error if path not in open_basedir
    if ( isset( $phpenv['phpOPENBASE'] ) ) {
        // is the session_save.path writable to this user?
        if ( is_writable( session_save_path() ) ) {
            $phpenv['phpSESSIONPATHWRITABLE'] = _FPA_Y;
        } else {
            $phpenv['phpSESSIONPATHWRITABLE'] = _FPA_N;
        }
    } else {
        $phpenv['phpSESSIONPATHWRITABLE'] = _FPA_U;
    }

    // input and upload related settings
    $phpenv['phpUPLOADS'] = ini_get( 'file_uploads' );
    $phpenv['phpMAXUPSIZE'] = ini_get( 'upload_max_filesize' );
    $phpenv['phpMAXPOSTSIZE'] = ini_get( 'post_max_size' );
    $phpenv['phpMAXIMPUTTIME'] = ini_get( 'max_input_time' );
    $phpenv['phpMAXEXECTIME'] = ini_get( 'max_execution_time' );
    $phpenv['phpMEMLIMIT'] = ini_get( 'memory_limit' );


    /** API and ownership related settings ***************************************************/
    $phpenv['phpAPI'] = php_sapi_name();

        // looking for php to be installed as a CGI or CGI/Fast
        if (substr($phpenv['phpAPI'], 0, 3) == 'cgi') {
            $phpenv['phpCGI'] = _FPA_Y;

            // looking for the Apache "suExec" utility
            if ( ( $system['sysCURRUSER'] === $system['sysWEBOWNER'] ) AND ( substr($phpenv['phpAPI'], 0, 3) == 'cgi' ) ) {
                $phpenv['phpAPACHESUEXEC'] = _FPA_Y;
                $phpenv['phpOWNERPROB'] = _FPA_N;
            } else {
                $phpenv['phpAPACHESUEXEC'] = _FPA_N;
                $phpenv['phpOWNERPROB'] = _FPA_M;
            }

            // looking for the "phpsuExec" utility
            if ( ( $system['sysCURRUSER'] === $system['sysEXECUSER'] ) AND ( substr($phpenv['phpAPI'], 0, 3) == 'cgi' ) ) {
                $phpenv['phpPHPSUEXEC'] = _FPA_Y;
                $phpenv['phpOWNERPROB'] = _FPA_N;
            } else {
                $phpenv['phpPHPSUEXEC'] = _FPA_N;
                $phpenv['phpOWNERPROB'] = _FPA_M;
            }

        } else {
            $phpenv['phpCGI'] = _FPA_N;
            $phpenv['phpAPACHESUEXEC'] = _FPA_N;
            $phpenv['phpPHPSUEXEC'] = _FPA_N;
            $phpenv['phpOWNERPROB'] = _FPA_M;
        }




        /** WARNING WILL ROBINSON! ****************************************************************
         ** THIS IS A TEST FEATURE AND AS SUCH NOT GUARANTEED TO BE 100% ACCURATE
         ** try and cater for custom "su" environments, like cluster, grid and cloud computing.
         ** this would include weird ownership combinations that allow group access to non-owner files
         ** (like GoDaddy and a couple of grid and cloud providers I know of)
         *****************************************************************************************/
        if ( ( $instance['instanceCONFIGURED'] == _FPA_Y ) AND ( $system['sysCURRUSER'] != $instance['configOWNER']['name'] ) AND ( $instance['configWRITABLE'] == _FPA_Y ) AND ( ( substr( $instance['configMODE'],0 ,1 ) < '6' ) OR ( substr( $instance['configMODE'],1 ,1 ) < '6' ) OR ( substr( $instance['configMODE'],2 ,1 ) <= '6' ) ) ) {
            $phpenv['phpCUSTOMSU'] = _FPA_M;
            $phpenv['phpOWNERPROB'] = _FPA_N;
        } else {
            $phpenv['phpCUSTOMSU'] = _FPA_N;
            $phpenv['phpOWNERPROB'] = _FPA_M;
        }
        /*****************************************************************************************/
        /** THIS IS A TEST FEATURE AND AS SUCH NOT GUARANTEED TO BE 100% ACCURATE ****************/
        /*****************************************************************************************/




    // get all the Apache loaded extensions and versions
    foreach ( get_loaded_extensions() as $i => $ext ) {
       $phpextensions[$ext] = phpversion($ext);
    }

    $phpextensions['Zend Engine'] = zend_version();
    //!TODO find out if this shows IONCUBE and SUHOSIN
?>





<?php
    /** DETERMINE APACHE ENVIRONMENT & SETTINGS ***********************************************
     ** here we try to determine the php enviroment and configuration
     ** to try and avoid "white-screens" fpa tries to check for function availability before
     ** using any function, but this does mean it has grown in size quite a bit and unfortunately
     ** gets a little messy in places.
     *****************************************************************************************/

    /** general apache loaded modules? *******************************************************/
	if ( function_exists( 'apache_get_version' ) ) {
        foreach ( apache_get_modules() as $i => $modules ) {
           $apachemodules[$i] = ( $modules );  // show the version of loaded extensions
        }
        // include the Apache version
        $apachemodules[] = apache_get_version();
    }
    // !TODO see if there are IIS specific functions/modules
?>





<?php
    /** COMPLETE MODE (PERMISSIONS) CHECKS ON KNOWN FOLDERS ***********************************
     ** test the mode and writability of known folders from the $folders array
     ** to try and avoid "white-screens" fpa tries to check for function availability before
     ** using any function, but this does mean it has grown in size quite a bit and unfortunately
     ** gets a little messy in places.
     *****************************************************************************************/

    /** build the mode-set details for each folder *******************************************/
    if ( $instance['instanceFOUND'] == _FPA_Y ) {

        foreach ( $folders as $i => $show ) {

            if ( $show != 'Selected Folders' ) { // ignore the ARRNAME

                if ( file_exists( $show ) ) {
                    $modecheck[$show]['mode'] = substr( sprintf('%o', fileperms( $show ) ),-3, 3 );

                    if ( is_writable( $show ) ) {
                        $modecheck[$show]['writable'] = _FPA_Y;
                    } else {
                        $modecheck[$show]['writable'] = _FPA_N;
                    }

                    if ( $system['sysSHORTOS'] != 'WIN' ) {
                        $modecheck[$show]['owner'] = posix_getpwuid( fileowner( $show ) );
                        $modecheck[$show]['group'] = posix_getgrgid( filegroup( $show ) );
                    } else {
                        $modecheck[$show]['owner'] = fileowner( $show );
                        $modecheck[$show]['group'] = filegroup( $show );
                    }

                } else {
                    $modecheck[$show]['mode'] = '---';
                    $modecheck[$show]['writable'] = '-';
                    $modecheck[$show]['owner']['name'] = '-';
                    $modecheck[$show]['group']['name'] = _FPA_DNE;
                }
            }
        }


        // here we take the folders array and unset folders that aren't relevant to a specific release
        function filter_folders( $folders, $instance ) {
        GLOBAL $folders;

            unset ( $folders['ARRNAME'] );

            if ( $instance['cmsRELEASE'] != '1.0' ) { // ignore the folders for J!1.0
                unset ( $folders[4] );
            } elseif ( $instance['cmsRELEASE'] == '1.0' ) { // ignore folders for J1.5 and above
                unset ( $folders[12] );
                unset ( $folders[3] );
                unset ( $folders[8] );
                unset ( $folders[9] );
                unset ( $folders[12] );
            }

        }

        // new filtered list of folders to check permissions on, based on the installed release
        // !FIXME need to fix warning in array_filter
        @array_filter( $folders, filter_folders( $folders, $instance ) );
    }
?>





<?php
    /** DETERMINE THE MYSQL VERSION AND IF WE CAN CONNECT *************************************
     ** here we try and find out more about MySQL and if we have an installed instance, see if
     ** talk to it and access the database.
     *****************************************************************************************/
    // !TODO database test-cases
    if ( $instance['instanceCONFIGURED'] == _FPA_Y AND $instance['configDBCREDOK'] == _FPA_Y ) {
        $database['dbDOCHECKS'] = _FPA_Y;

        if ( $instance['configDBHOST'] == 'localhost' OR $instance['configDBHOST'] == '127.0.0.1' ) {
            $database['dbLOCAL'] = _FPA_Y;
        } else {
            $database['dbLOCAL'] = _FPA_N;
        }

// !TODO DB PING
/**
            // See if the PHP Functions are available to test for connectivity
            if ( @$opSystem != 'WIN') {

              if ( function_exists('exec') ) {
                $hostPing = 1;
                exec("ping -c 2 ". $cfgARRAY['dbhost'][1] ." 2>&1", $output, $retval);
              } else if ( function_exists('passthru') ) {
                $hostPing = 1;
                passthru("ping -c 2 ". $cfgARRAY['dbhost'][1] ." >/dev/null 2>$0", $retval);
              } else if ( function_exists('system') ) {
                $hostPing = 1;
                system("ping -c 2 ". $cfgARRAY['dbhost'][1] ." >/dev/null 2>$0", $retval);
              } else {
                $hostPing = 0;
                echo '<br /><span class="isOk">ping not attempted, PHP restriciton</span>';
              }

            } else { // Windows Machines IIS Users, by default have no access to the shell, so the above errors
              @$hostPing = 0;
              echo '<br /><span class="isOk">ping not attempted, host restriciton</span>';
            }

            if ( @$hostPing != 0 ) {

              if ( @$retval != 0 ) {
                echo '<br />- '. PINGHOST .' <span class="isNo">'. FAIL .'</span>';
              } else {
                echo '<br />- '. PINGHOST .' <span class="isYes">'. SUCCESS.'</span>';
              }

            }
**/
        // !TODO DB MySQLI not supported by PHP4
        if ( version_compare( PHP_VERSION, '5.0.0', '>=' ) ) {
            $database['dbPHPSUPPORTSMYSQLI'] = _FPA_Y;
        } else {
            $database['dbPHPSUPPORTSMYSQLI'] = _FPA_N;
        }

// !TODO MYSQL COLLATION
/**
             $rs = $conn->query( "SHOW VARIABLES LIKE 'collation_database'" );
            while ( $row = mysqli_fetch_row( $rs ) ) {
              echo '&nbsp;'. $row[1];
            }

            $rs1 = $conn->query( "SHOW VARIABLES LIKE 'character_set_database'" );
            while ( $row = mysqli_fetch_array( $rs1 ) ) {
              echo '&nbsp; ( '.$row[1] .' )';
            }
 **/


        // try and establish if we can talk to the dBase server with a ping, then try and connect and ping with mysql_ping
        if ( $instance['configDBTYPE'] == 'mysql' ) {

            $dBconn = @mysql_connect( $instance['configDBHOST'], $instance['configDBUSER'], $instance['configDBPASS'] );
            $database['dbERROR'] = mysql_errno() .':'. mysql_error();

            if ( $dBconn ) {
                mysql_select_db( $instance['configDBNAME'], $dBconn );
                $database['dbERROR'] = mysql_errno() .':'. mysql_error();

                $database['dbHOSTSERV'] = mysql_get_server_info( $dBconn ); // SQL server version
                $database['dbHOSTINFO'] = mysql_get_host_info( $dBconn ); // connection type to dB
                $database['dbHOSTPROTO'] = mysql_get_proto_info( $dBconn ); // server protocol type
                $database['dbHOSTCLIENT'] = mysql_get_client_info(); // client library version
                $database['dbHOSTDEFCHSET'] = mysql_client_encoding( $dBconn ); // this is the hosts default character-set
                $database['dbHOSTSTATS'] = explode("  ", mysql_stat( $dBconn ) ); // latest statistics

                // find the database collation
                $coResult = mysql_query( "SHOW VARIABLES LIKE 'collation_database'" );
                while ( $row = mysql_fetch_row( $coResult ) ) {
                    $database['dbCOLLATION'] =  $row[1];
                }

                // find the database character-set
                $csResult = mysql_query( "SHOW VARIABLES LIKE 'character_set_database'" );
                while ( $row = mysql_fetch_array( $csResult ) ) {
                    $database['dbCHARSET'] =  $row[1];
                }

                mysql_close( $dBconn );

            } else {
                $database['dbERROR'] = mysql_errno() .':'. mysql_error();
            } // end mysql if $dBconn is good

        } elseif ( $instance['configDBTYPE'] == 'mysqli' AND $database['dbPHPSUPPORTSMYSQLI'] == _FPA_Y ) { // mysqli

            $dBconn = @new mysqli( $instance['configDBHOST'], $instance['configDBUSER'], $instance['configDBPASS'], $instance['configDBNAME'] );
            $database['dbERROR'] = mysqli_connect_errno( $dBconn ) .':'. mysqli_connect_error( $dBconn );



            if ( $dBconn ) {
                $database['dbHOSTSERV'] = @mysqli_get_server_info( $dBconn ); // SQL server version
                $database['dbHOSTINFO'] = @mysqli_get_host_info( $dBconn ); // connection type to dB
                $database['dbHOSTPROTO'] = @mysqli_get_proto_info( $dBconn ); // server protocol type
                $database['dbHOSTCLIENT'] = @mysqli_get_client_info(); // client library version
                $database['dbHOSTDEFCHSET'] = @mysqli_client_encoding( $dBconn ); // this is the hosts default character-set
                $database['dbHOSTSTATS'] = explode("  ", @mysqli_stat( $dBconn ) ); // latest statistics

                // find the database collation
                $coResult = @$dBconn->query( "SHOW VARIABLES LIKE 'collation_database'" );
                while ( $row = @mysqli_fetch_row( $coResult ) ) {
                    $database['dbCOLLATION'] =  $row[1];
                }

                // find the database character-set
                $csResult = @$dBconn->query( "SHOW VARIABLES LIKE 'character_set_database'" );
                while ( $row = @mysqli_fetch_array( $csResult ) ) {
                    $database['dbCHARSET'] =  $row[1];
                }

            } else {
               // $database['dbERROR'] = mysqli_connect_errno( $dBconn ) .':'. mysqli_connect_error( $dBconn );
            } // end mysqli if $dBconn is good

        } else {
                $database['dbHOSTSERV'] = _FPA_U; // SQL server version
                $database['dbHOSTINFO'] = _FPA_U; // connection type to dB
                $database['dbHOSTPROTO'] = _FPA_U; // server protocol type
                $database['dbHOSTCLIENT'] = _FPA_U; // client library version
                $database['dbHOSTDEFCHSET'] = _FPA_U; // this is the hosts default character-set
                $database['dbHOSTSTATS'] = _FPA_U; // latest statistics
                $database['dbCOLLATION'] =  _FPA_U;
                $database['dbCHARSET'] =  _FPA_U;
        } // end of dataBase connection routines


            if ( isset( $dBconn ) AND $database['dbERROR'] == '0:' ) {
                $database['dbERROR'] = _FPA_N;
            } elseif ( $database['dbLOCAL'] == _FPA_N AND substr($database['dbERROR'], 0, 4) == '2005' ) { // 2005 = can't access host
                // if this is a remote host, it might be firewalled or disabled from external or non-internal network access
                $database['dbERROR'] = $database['dbERROR'] .' ( might not be an error, this remote SQL server might be firewalled by mistake )';
            }




    // if no configuration or if configured but dBase credentials aren't valid
    } else {
        $database['dbDOCHECKS'] = _FPA_N;
        $database['dbLOCAL'] = _FPA_U;
    }

    /** find a MySQL instance? ***************************************************************/
?>













        <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <title><?php echo _RES .' : v'. _RES_VERSION .' ('. _RES_RELEASE .')';?></title>


        <style type="text/css" media="screen">
            html, body, div, p, span {
                font-size: 10px;
                font-family: tahoma, arial;
                color: #404040;
            }

            .dev-mode-information {
                margin: 0 auto;
                margin-top:10px;
                margin-bottom:10px;
                padding: 5px;
                width:750px;
                background-color:#CAFFD8;
                border:1px solid #4D8000;
                /** CSS3 **/
                /* text-shadow: 1px 1px 1px #FFF; */
                box-shadow: 3px 3px 3px #C0C0C0;
                -moz-box-shadow: 3px 3px 3px #C0C0C0;
                -webkit-box-shadow: 3px 3px 3px #C0C0C0;
                border-radius: 5px;
                -moz-border-radius: 5px;
                -webkit-border-radius: 5px;
            }

            .dev-mode-title {
                color: #4D8000;
                font-weight: bold;
                /** CSS3 **/
                text-shadow: 1px 1px 1px #FFF;
            }

            .header-information {
                margin: 0px auto;
                margin-top:10px;
                margin-bottom:10px;
                padding: 5px;
                width:750px;
                background-color:#E0FFFF;
                border:1px solid #42AEC2;
                /** CSS3 **/
                /* text-shadow: 1px 1px 1px #FFF; */
                box-shadow: 3px 3px 3px #C0C0C0;
                -moz-box-shadow: 3px 3px 3px #C0C0C0;
                -webkit-box-shadow: 3px 3px 3px #C0C0C0;
                border-radius: 5px;
                -moz-border-radius: 5px;
                -webkit-border-radius: 5px;
            }

            .header-title {
                color: #404040;
                text-align:center;
                font-size: 14px;
                font-weight: bold;
                padding: 1px;
                text-transform: uppercase;
                margin-left: 1px;
                margin-right: 1px;
                margin-top: 2px;
                margin-bottom: 2px;
                /** CSS3 **/
                text-shadow: 1px 1px 1px #FFF;
            }

            .header-column-title {
                color: #404040;
                font-weight: normal;
                padding: 1px;
                /** CSS3 **/
                text-shadow: 1px 1px 1px #FFF;
            }

            .section-information {
                margin: 0px auto;
                margin-top:10px;
                margin-bottom:10px;
                padding: 5px;
                width:750px;
                background-color:#E0FFFF;
                border:1px solid #42AEC2;
                /** CSS3 **/
                box-shadow: 3px 3px 3px #C0C0C0;
                -moz-box-shadow: 3px 3px 3px #C0C0C0;
                -webkit-box-shadow: 3px 3px 3px #C0C0C0;
                border-radius: 5px;
                -moz-border-radius: 5px;
                -webkit-border-radius: 5px;
            }

            .half-section-container {
                margin: 0px auto;
/*
                margin-top:10px;
                margin-bottom:10px;
*/
                padding: 5px;
                width:750px;

/*
                background-color:#E0FFFF;
                border:1px solid #42AEC2;
*/
            }

            .half-section-information-left {
                float:left;
/*
                margin: 0px auto;
*/
                margin-top:10px;
                margin-bottom:10px;
                padding: 5px;
                width:355px;
                background-color:#E0FFFF;
                border:1px solid #42AEC2;
                /** CSS3 **/
                box-shadow: 3px 3px 3px #C0C0C0;
                -moz-box-shadow: 3px 3px 3px #C0C0C0;
                -webkit-box-shadow: 3px 3px 3px #C0C0C0;
                border-radius: 5px;
                -moz-border-radius: 5px;
                -webkit-border-radius: 5px;
            }

            .half-section-information-right {
                float:right;
/*
                margin: 0px auto;
*/
                margin-top:10px;
                margin-bottom:10px;
                padding: 5px;
                width:355px;
                background-color:#E0FFFF;
                border:1px solid #42AEC2;
                /** CSS3 **/
                box-shadow: 3px 3px 3px #C0C0C0;
                -moz-box-shadow: 3px 3px 3px #C0C0C0;
                -webkit-box-shadow: 3px 3px 3px #C0C0C0;
                border-radius: 5px;
                -moz-border-radius: 5px;
                -webkit-border-radius: 5px;
            }

            .section-title {
                color: #404040;
                font-size: 12px;
                font-weight: bold;
                padding: 1px;
                text-transform: uppercase;
                margin-left: 1px;
                margin-right: 1px;
                margin-top: 2px;
                margin-bottom: 2px;
                /** CSS3 **/
                text-shadow: 1px 1px 1px #F5F5F5;
            }

            .mini-content-container {
                font-size: 9px !important;
                float:left;
                width: 83px;
                height: 75px;
                margin: 2px;
/*                border: 1px solid red; */
            }

            .mini-content-title {
                font-size: 8px;
                font-weight: bold;
                text-align:center;
                margin: 0px auto;
                margin-bottom: 4px !important;
/*                padding: 1px;*/
/*                background-color: #FFF; */
/*                border-top: 1px dotted #C0C0C0; */
                border-bottom: 1px solid #C0C0C0;
                text-transform: uppercase;
                /** CSS3 **/
                text-shadow: 1px 1px 1px #FFF;
/*
                text-shadow: none !important;
                border-top-left-radius: 5px;
                -moz-border-radius-topleft: 5px;
                -webkit-border-top-left: 5px;
                border-top-right-radius: 5px;
                -moz-border-radius-topright: 5px;
                -webkit-border-top-right: 5px;
*/
            }

            .mini-content-box {
                font-size: 10px !important;
                text-align:center;
                margin: 0px auto;
                padding: 4px;
/*
                border-left: 1px solid #808080;
                border-right: 1px solid #808080;
*/
                border: 1px solid #808080;
                height: 45px;
                background-color: #FFFFF0;
/*                border: 1px solid blue; */
                /** CSS3 **/
                border-radius: 5px;
                -moz-border-radius: 5px;
                -webkit-border-radius: 5px;
            }

            .mini-content-box-small {
                font-size: 9px !important;
                text-align:center;
                margin: 0px auto;
/*                padding: 4px; */
                padding-left: 2px;
                text-align: left;

/*
                border-left: 1px solid #808080;
                border-right: 1px solid #808080;
*/
/*
                border: 1px solid #808080;
                height: 45px;
                background-color: #FFFFF0;
*/
/*                border: 1px solid blue; */
                /** CSS3 **/
/*
                border-radius: 5px;
                -moz-border-radius: 5px;
                -webkit-border-radius: 5px;
*/
            }

            .column-title-container {
                background-color: #42AEC2;
                /** CSS3 **/
                border-top-left-radius: 5px;
                -moz-border-radius-topleft: 5px;
                -webkit-border-top-left: 5px;
                border-top-right-radius: 5px;
                -moz-border-radius-topright: 5px;
                -webkit-border-top-right: 5px;
            }

            .column-title {
                color: #E0FFFF;
                font-weight: bold;
                padding: 1px;
                text-transform: uppercase;
                margin-left: 1px;
                margin-right: 1px;
                margin-top: 2px;
                margin-bottom: 2px;
                /** CSS3 **/
                text-shadow: 1px 1px 1px #808080;
            }

            .row-content-container {
                border-bottom: 1px dotted #C0C0C0;
                width: 99%;
                margin: 0px auto;
/*
                padding-top:1px;
                padding-bottom: 1px;
*/
                clear:both;
            }

/*
            .content-box {
                text-align:center;
                float: left;
                border: 1px solid #C0C0C0;
                width: 20%;
                height: 50px;
                margin: 3px;
                padding:4px;

                border-radius: 5px;
                -moz-border-radius: 5px;
                -webkit-border-radius: 5px;
            }
*/
            .nothing-to-display {
                text-align: center;
                font-size: 11px;
            }

            .column-content {
                margin-left: 1px;
                margin-right: 1px;
            }

            .normal {
                color: #404040;
            }

            .ok {
                color: #008000;
            }

            .warn {
                background-color: #FFE4B5;
                color: #800000;
                border: 1px solid #FFA500;
                /** CSS3 **/
                border-radius: 5px;
                -moz-border-radius: 5px;
                -webkit-border-radius: 5px;
            }

            .warn-text {
                color: #FFA500;
            }

            .alert {
                background-color: #FFFF00;
                color: #800000;
                border: 1px solid #800000;
                /** CSS3 **/
                border-radius: %px;
                -moz-border-radius: 5px;
                -webkit-border-radius: 5px;
            }

            .alert-text {
                color: #800000;
            }
        </style>

        </head>
    <body>

<?php
    /** display the fpa heading ***************************************************************/
    echo '<div class="header-information">';
    echo '<div class="header-title" style="">'. _RES .'</div>';
    echo '<div class="header-column-title" style="text-align:center;">'. _FPA_VER .': v'. _RES_VERSION .'-'. _RES_RELEASE .' ('. _RES_BRANCH .')</div>';
    echo '<div style="clear:both;"></div>';
    echo '</div>';
?>



<?php
    // build a full-width div to hold two 'half-width' section, side-by-side
    echo '<div class="half-section-container" style="">'; // start half-section container

        /** display the instance information *************************************************/
        echo '<div class="half-section-information-left">'; // start left content block

        echo '<div class="section-title" style="text-align:center;">'. $instance['ARRNAME'] .' Discovery</div>';
        echo '<div class="" style="width:99%;margin: 0px auto;clear:both;margin-bottom:10px;">';
        // this is the column heading area, if any


        /** mini-content, shown in all cases *************************************************/
        echo '<div class="mini-content-container">';
        echo '<div class="mini-content-box">';
        echo '<div class="mini-content-title">CMS Found</div>';

            if ( $instance['instanceFOUND'] == _FPA_Y ) {
                echo '<strong>'. $instance['cmsRELEASE'] .'.'. $instance['cmsDEVLEVEL'] .'</strong><br />';

                if ( strtolower( $instance['cmsDEVSTATUS'] ) == 'stable' ) {
                    $statusClass = 'ok';
                } elseif ( strtolower( substr( $instance['cmsDEVSTATUS'],0, 4 ) ) == 'alph' OR strtolower( substr( $instance['cmsDEVSTATUS'],0, 4 ) ) == 'beta' ) {
                    $statusClass = 'alert';
                } elseif ( strtolower( substr( $instance['cmsDEVSTATUS'],0, 2 ) ) == 'rc' ) {
                    $statusClass = 'warn';
                }

                echo '<div class="'. $statusClass .'" style="width:50px;margin: 0px auto;margin-top:1px;">'. $instance['cmsDEVSTATUS'] .'</div>';
                //echo $instance['cmsCODENAME'];

            } else {
                echo '<div class="warn" style="width:50px;margin: 0px auto;">'. $instance['instanceFOUND'] .'</div>';
            }

        echo '</div>';
        echo '</div>';




        // caters for the platform separation

            echo '<div class="mini-content-container">';
            echo '<div class="mini-content-box">';
            echo '<div class="mini-content-title">Platform</div>';

        if ( $instance['platformVFILE'] != _FPA_N ) {
            echo '<strong>'. $instance['platformRELEASE'] .'.'. $instance['platformDEVLEVEL'] .'</strong><br />';

                if ( strtolower( $instance['platformDEVSTATUS'] ) == 'stable' ) {
                    $statusClass = 'ok';
                } elseif ( strtolower( substr( $instance['platformDEVSTATUS'],0, 4 ) ) == 'alph' OR strtolower( substr( $instance['platformDEVSTATUS'],0, 4 ) ) == 'beta' ) {
                    $statusClass = 'alert';
                } elseif ( strtolower( substr( $instance['platformDEVSTATUS'],0, 2 ) ) == 'rc' ) {
                    $statusClass = 'warn';
                }
                    echo '<div class="'. $statusClass .'" style="width:50px;margin: 0px auto;">'. $instance['platformDEVSTATUS'] .'</div>';
                    //echo $instance['platformCODENAME'];

        } elseif ( $instance['platformVFILE'] == _FPA_N AND $instance['cmsVFILE'] == _FPA_N) {
            echo '<div class="warn" style="width:50px;margin: 0px auto;">'. _FPA_N .'</div>';
        } else {
            echo _FPA_NA;
        }
            echo '</div>';
            echo '</div>';



        echo '<div class="mini-content-container">';
        echo '<div class="mini-content-box">';
        echo '<div class="mini-content-title">Config Exists</div>';

            if ( $instance['instanceCONFIGURED'] == _FPA_N ) {
                $configuredClass = 'warn';
            } else {
                $configuredClass = 'ok';
            }

        echo '<div class="'. $configuredClass .'" style="width:50px;margin: 0px auto;">'. $instance['instanceCONFIGURED'] .'</div>';
        echo '</div>';
        echo '</div>';



        echo '<div class="mini-content-container">';
        echo '<div class="mini-content-box">';
        echo '<div class="mini-content-title">Config Version</div>';
        echo $instance['configVALIDFOR'];

            if ( $instance['instanceCFGVERMATCH'] == _FPA_Y ) {
                echo '<div class="ok" style="width:99%;margin: 0px auto;">matches cms</div>';
            } else {
                echo '<div class="warn" style="width:99%;margin: 0px auto;">cms mis-match</div>';
            }

        echo '</div>';
        echo '</div>';


        /** mini-content, only shown if instance found and configured ************************/
        if ( $instance['instanceCONFIGURED'] != _FPA_N AND $instance['instanceFOUND'] != _FPA_N ) {

            // force new line of mini-content-boxes
            echo '<div style="clear:both;"></div>';



            echo '<div class="mini-content-container">';
            echo '<div class="mini-content-box">';
            echo '<div class="mini-content-title">Config Valid</div>';

                if ( $instance['configSANE'] == _FPA_Y AND @$instance['configSIZEVALID'] != _FPA_N ) {
                    $saneClass = 'ok';
                    $configVALID = _FPA_Y;
                } else {
                    $saneClass = 'warn';
                    $configVALID = _FPA_N;
                }

            echo '<div class="'. $saneClass .'" style="width:50px;margin: 0px auto;">'. $configVALID .'</div>';
            echo '</div>';
            echo '</div>';



            echo '<div class="mini-content-container">';
            echo '<div class="mini-content-box">';
            echo '<div class="mini-content-title">Config Mode</div>';

                // looking for --7 or -7- or -77 (default folder permissions are usually 755)
                if ( substr( $instance['configMODE'],0 ,1 ) == '7' OR substr( $instance['configMODE'],1 ,1 ) == '7' OR substr( $instance['configMODE'],2 ,1 ) == '7' ) {
                    $modeClass = 'alert';
                } elseif ( $instance['configMODE'] <= '644' ) {
                    $modeClass = 'ok';
                } elseif ( substr( $instance['configMODE'],1 ,1 ) >= '5' OR substr( $instance['configMODE'],2 ,1 ) >= '5' ) {
                    $modeClass = 'warn';
                } elseif ( $instance['configMODE'] == _FPA_N ) {
                    $modeClass = 'warn-text';
                } else {
                    $modeClass = 'normal';
                }

            echo '<div class="'. $modeClass .'" style="width:50px;margin: 0px auto;">'. $instance['configMODE'] .'</div>';

                // is the file writable?
                if ( ( $instance['configWRITABLE'] == _FPA_Y ) AND ( substr( $instance['configMODE'],0 ,1 ) == '7' OR substr( $instance['configMODE'],1 ,1 ) == '7' OR substr( $instance['configMODE'],2 ,1 ) == '7' ) ) {
                    $writeClass = 'alert';
                    $canWrite = 'Writable';
                } elseif ( ( $instance['configWRITABLE'] == _FPA_Y ) AND ( substr( $instance['configMODE'],0 ,1 ) <= '6' ) ) {
                    $writeClass = 'ok';
                    $canWrite = 'Writable';
                } elseif ( ( $instance['configWRITABLE'] != _FPA_Y ) ) {
                    $writeClass = 'warn';
                    $canWrite = 'Read Only';
                }

            echo '<div class="'. $writeClass .'" style="width:50px;margin: 0px auto;margin-top:1px;">'. $canWrite .'</div>';
            echo '</div>';
            echo '</div>';



            echo '<div class="mini-content-container">';
            echo '<div class="mini-content-box">';
            echo '<div class="mini-content-title">Config Owner</div>';
            echo $instance['configOWNER']['name'];
            echo '</div>';
            echo '</div>';



            echo '<div class="mini-content-container">';
            echo '<div class="mini-content-box">';
            echo '<div class="mini-content-title">Config Group</div>';
            echo $instance['configGROUP']['name'];
            echo '</div>';
            echo '</div>';

        } // end if no instance or configuration found dont display

        echo '</div>';



        // only do mode/permissions checks if an instance was found in the intial checks
        if ( $instance['instanceFOUND'] != _FPA_Y ) {
            // this is the content area
            echo '<div class="row-content-container nothing-to-display" style="">';
            echo '<div class="warn" style=" margin-top:10px;margin-bottom:10px;">';
            echo 'Instance not found, no '. $instance['ARRNAME'] .' tests performed';

                if ( $instance['instanceCONFIGURED'] == _FPA_Y ) {
                    echo '<br />but there is a configuration.php file.';
                }

            echo '</div>';
            echo '</div>';
        }

        echo '</div>';
        // end content left block





        /** display the system information *************************************************/
        echo '<div class="half-section-information-right">'; // start right content block

        echo '<div class="section-title" style="text-align:center;">'. $instance['ARRNAME'] .' Configuration</div>';
        echo '<div class="" style="width:99%;margin: 0px auto;clear:both;margin-bottom:10px;">';
        // this is the column heading area, if any

//        echo '</div>';

            // only do mode/permissions checks if an instance was found in the intial checks
            if ( $instance['instanceCONFIGURED'] == _FPA_Y AND $instance['configVALIDFOR'] != _FPA_U ) {
            // this is the content area


        echo '<div class="mini-content-container">';
        echo '<div class="mini-content-box">';
        echo '<div class="mini-content-title">Site Offline</div>';
        echo $instance['configOFFLINE'];
        echo '</div>';
        echo '</div>';


        echo '<div class="mini-content-container">';
        echo '<div class="mini-content-box">';
        echo '<div class="mini-content-title" style="margin-bottom:0px!important;">SEF URL\'s</div>';
        echo '<div class="mini-content-box-small">';
        echo '<div style="font-size:9px;width:99%;border-bottom: 1px dotted #c0c0c0;">Enabled:<div style="float:right;font-size:9px;">'. $instance['configSEF'] .'</div></div>';
        echo '<div style="font-size:9px;width:99%;border-bottom: 1px dotted #c0c0c0;">Suffix:<div style="float:right;font-size:9px;">'. $instance['configSEFSUFFIX'] .'</div></div>';

            if ( $system['sysSERVSOFTWARE'] != 'MIC' AND $instance['configSEFRWRITE'] == '1' AND $instance['configSITEHTWC'] != '1' ) {
                $sefColor = 'ff0000';
            } else {
                $sefColor = '404040';
            }

        echo '<div style="font-size:9px;width:99%;border-bottom: 1px dotted #c0c0c0;color:#'. $sefColor .';">ReWrite:<div style="float:right;color:#'. $sefColor .';font-size:9px;">'. $instance['configSEFRWRITE'] .'</div></div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';


        echo '<div class="mini-content-container">';
        echo '<div class="mini-content-box">';
        echo '<div class="mini-content-title" style="margin-bottom:0px!important;">Compression</div>';
        echo '<div class="mini-content-box-small">';
        echo '<div style="font-size:9px;width:99%;border-bottom: 1px dotted #c0c0c0;">GZip:<div style="float:right;font-size:9px;">'. $instance['configGZIP'] .'</div></div>';
        echo '<div style="font-size:9px;width:99%;border-bottom: 1px dotted #c0c0c0;">Cache:<div style="float:right;font-size:9px;">'. $instance['configCACHING'] .'</div></div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';


        echo '<div class="mini-content-container">';
        echo '<div class="mini-content-box">';
        echo '<div class="mini-content-title" style="margin-bottom:0px!important;">Debugging</div>';
        echo '<div class="mini-content-box-small">';
        echo '<div style="font-size:9px;width:99%;border-bottom: 1px dotted #c0c0c0;">Error Rep:<div style="float:right;font-size:9px;">'. $instance['configERRORREP'] .'</div></div>';
        echo '<div style="font-size:9px;width:99%;border-bottom: 1px dotted #c0c0c0;">Site Debug:<div style="float:right;font-size:9px;">'. $instance['configSITEDEBUG'] .'</div></div>';
        echo '<div style="font-size:9px;width:99%;border-bottom: 1px dotted #c0c0c0;">Lang Debug:<div style="float:right;font-size:9px;">'. $instance['configLANGDEBUG'] .'</div></div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';


        echo '<div class="mini-content-container">';
        echo '<div class="mini-content-box">';
        echo '<div class="mini-content-title" style="margin-bottom:0px!important;">dataBase</div>';
        echo '<div class="mini-content-box-small">';
        echo '<div style="font-size:9px;width:99%;border-bottom: 1px dotted #c0c0c0;">Type:<div style="float:right;font-size:9px;">'. $instance['configDBTYPE'] .'</div></div>';
        echo '<div style="font-size:9px;width:99%;border-bottom: 1px dotted #c0c0c0;">Version:<div style="float:right;font-size:9px;">'. $instance['configDBTYPE'] .'</div></div>';
        echo '<div style="font-size:9px;width:99%;border-bottom: 1px dotted #c0c0c0;">Colation:<div style="float:right;font-size:9px;">'. $instance['configDBTYPE'] .'</div></div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';


        echo '<div class="mini-content-container">';
        echo '<div class="mini-content-box">';
        echo '<div class="mini-content-title">DB Credentials</div>';
            if ( $instance['configDBCREDOK'] == _FPA_Y ) {
                echo '<div class="ok" style="width:99%;margin: 0px auto;font-size:9px;">appears<br />complete</div>';
            } else {
                echo '<div class="warn" style="width:99%;margin: 0px auto;font-size:9px;">appears<br />in-complete</div>';
            }
        echo '</div>';
        echo '</div>';



        echo '<div class="mini-content-container">';
        echo '<div class="mini-content-box">';
        echo '<div class="mini-content-title" style="margin-bottom:0px!important;">Security</div>';
        echo '<div class="mini-content-box-small">';
        echo '<div style="font-size:9px;width:99%;border-bottom: 1px dotted #c0c0c0;">SSL Enabled:<div style="float:right;font-size:9px;">'. $instance['configSSL'] .'</div></div>';
        echo '<div style="font-size:9px;width:99%;border-bottom: 1px dotted #c0c0c0;">Def\' Access:<div style="float:right;font-size:9px;">'. $instance['configACCESS'] .'</div></div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';





        echo '<div class="mini-content-container">';
        echo '<div class="mini-content-box">';
        echo '<div class="mini-content-title" style="margin-bottom:0px!important;">Features</div>';
        echo '<div class="mini-content-box-small">';
        echo '<div style="font-size:9px;width:99%;border-bottom: 1px dotted #c0c0c0;">FTP Enabled:<div style="float:right;font-size:9px;">'. $instance['configFTP'] .'</div></div>';
        echo '<div style="font-size:9px;width:99%;border-bottom: 1px dotted #c0c0c0;">Unicode Slug:<div style="float:right;font-size:9px;">'. $instance['configUNICODE'] .'</div></div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';




        echo '<br style="clear:both;" />';
        echo '<div class="mini-content-box-small" style="">';
        echo '<div style="font-size:9px;width:95%;border-bottom: 1px dotted #c0c0c0;font-weight:bold;">dataBase Host:<div style="float:right;font-size:9px;font-weight:normal;">';

            if ( $protected == '0' ) {

                if ( $instance['configDBHOST'] ) {
                    echo $instance['configDBHOST'];
                } else {
                    echo '<span class="alert">&nbsp;'. _FPA_DNE .'&nbsp;</span>';
                }

            } else {
                echo '<span class="warn-text">[**&nbsp;'. _FPA_HIDDEN .'&nbsp;**]</span>';
            }
        echo '</div></div>';
        echo '</div>';



            } else { // an instance wasn't found in the initial checks, so no folders to check

        echo '<div class="mini-content-container">';
        echo '<div class="mini-content-box">';
        echo '<div class="mini-content-title">Config Version</div>';
            echo '<div class="warn" style="width:50px;margin: 0px auto;">'. _FPA_U .'</div>';
        echo '</div>';
        echo '</div>';

        echo '<div class="mini-content-container">';
        echo '<div class="mini-content-box">';
        echo '<div class="mini-content-title">Config Valid</div>';
            if ( $instance['configSIZEVALID'] == _FPA_N ) {
                echo '<div class="warn" style="width:99%;margin: 0px auto;">could be empty</div>';
            }
        echo '</div>';
        echo '</div>';


                echo '<div class="row-content-container nothing-to-display" style="">';
                echo '<div class="warn" style=" margin-top:10px;margin-bottom:10px;">configuration not found or appears not to be valid<br />no '. $instance['ARRNAME'] .' checks performed</div>';
                echo '</div>';
            }

        echo '</div>';
        echo '</div>';
        // end content right block

    echo '<div style="clear:both;"></div>';

    showDev( $instance );
    showDev( $system );

    echo '</div>'; // end half-section container
?>




<?php
    showDev( $phpenv );
    showDev( $phpextensions );
	if ( function_exists( 'apache_get_version' ) ) { // don't show if not Apache
        showDev( $apachemodules );
    }
?>





<?php
    /** display the mode-set details for each selected folder ********************************/
    echo '<div class="section-information">';
    echo '<div class="section-title" style="text-align:center;">'. $modecheck['ARRNAME'] .'</div>';

    echo '<div class="column-title-container" style="width:99%;margin: 0px auto;clear:both;display:block;">';
    // this is the column heading area, if any

    echo '<div class="column-title" style="width:7%;float:left;text-align:center;">'. _FPA_MODE .'</div>';
    echo '<div class="column-title" style="width:8%;float:left;left;text-align:center;">'. _FPA_WRITABLE .'</div>';
    echo '<div class="column-title" style="width:58%;float:left;">'. _FPA_FOLDER .'</div>';
    echo '<div class="column-title" style="width:12%;float:right;text-align:center;">'. _FPA_GROUP .'</div>';
    echo '<div class="column-title" style="width:12%;float:right;text-align:center;">'. _FPA_OWNER .'</div>';
    echo '<div style="clear:both;"></div>';
    echo '</div>';

    // only do mode/permissions checks if an instance was found in the intial checks
    if ( $instance['instanceFOUND'] == _FPA_Y ) {
    // this is the content area

        foreach ( $folders as $i => $show ) {

            // looking for --7 or -7- or -77 (default folder permissions are usually 755)
            if ( substr( $modecheck[$show]['mode'],1 ,1 ) == '7' OR substr( $modecheck[$show]['mode'],2 ,1 ) == '7' ) {
                $modeClass = 'alert';
                $alertClass = 'alert-text';
                $userClass = 'normal';
                $groupClass = 'normal';
            } elseif ( $modecheck[$show]['mode'] == '755' ) {
                $modeClass = 'ok';
                $alertClass = 'normal';
                $userClass = 'normal';
                $groupClass = 'normal';
            } else if ( substr( $modecheck[$show]['mode'],0 ,1 ) <= '5' AND $modecheck[$show]['mode'] != '---' ) {
                $modeClass = 'warn';
                $alertClass = 'warn-text';
                $userClass = 'normal';
                $groupClass = 'normal';
            } else if ( $modecheck[$show]['group']['name'] == _FPA_N ) {
                $modeClass = 'warn-text';
                $alertClass = 'warn-text';
                $userClass = 'warn-text';
                $groupClass = 'warn-text';
            } else {
                $modeClass = 'normal';
                $alertClass = 'normal';
                $userClass = 'normal';
                $groupClass = 'normal';
            }

            // is the folder writable?
            if ( ( $modecheck[$show]['writable'] != _FPA_Y ) ) {
                $writeClass = 'warn-text';
            } elseif ( ( $modecheck[$show]['writable'] == _FPA_Y ) AND ( substr( $modecheck[$show]['mode'],0 ,1 ) == '7' ) ) {
                $writeClass = 'normal';
            } elseif ( $modecheck[$show]['writable'] == _FPA_N ) {
                $writeClass = 'ok';
            }

            // is the 'executing' owner the same as the folder owner? and is the users groupID the same as the folders groupID?
            if ( ( $modecheck[$show]['owner']['name'] != $system['sysEXECUSER'] ) AND ( $modecheck[$show]['group']['name'] != _FPA_DNE ) ) {
                $userClass = 'warn-text';
                $groupClass = 'normal';
            } elseif ( isset( $modecheck[$show]['group']['gid'] ) AND isset( $modecheck[$show]['owner']['gid'] ) ) {

                if ( $modecheck[$show]['group']['gid'] != $modecheck[$show]['owner']['gid'] ) {
                    $userClass = 'normal';
                    $groupClass = 'warn-text';
                }

            } elseif ( $modecheck[$show]['group']['name'] == _FPA_DNE ) {
                $modeClass = 'warn-text';
                $alertClass = 'warn-text';
                $writeClass = 'warn-text';
                $userClass = 'warn-text';
                $groupClass = 'warn-text';
            }

            // produce the output
            echo '<div style="border-bottom:1px dotted #C0C0C0;width:99%;margin: 0px auto;padding-top:1px;padding-bottom:1px;clear:both;">';

            echo '<div class="column-content '. $modeClass .'" style="float:left;width:7%;text-align:center;">';
            echo $modecheck[$show]['mode'];  // display the mode
            echo '</div>';

            echo '<div class="column-content '. $writeClass .'" style="width:8%;float:left;text-align:center;">';
            echo $modecheck[$show]['writable'];  // display if writable
            echo '</div>';

            echo '<div class="column-content '. $alertClass .'" style="width:58%;float:left;padding-left:5px;">';
            echo $show;  // display the folder name
            echo '</div>';

            echo '<div class="column-content '. $groupClass .'" style="float:right;width:12%;text-align:center;">';
            echo $modecheck[$show]['group']['name'];  // display the group
            echo '</div>';

            echo '<div class="column-content '. $userClass .'"" style="float:right;width:12%;text-align:center;">';
            echo $modecheck[$show]['owner']['name'];  // display the owner
            echo '</div>';

            echo '<div style="clear:both;"></div>';
            echo '</div>';
        }

    } else { // an instance wasn't found in the initial checks, so no folders to check
        echo '<div style="text-align:center;border-bottom:1px dotted #C0C0C0;width:99%;margin: 0px auto;padding-top:1px;padding-bottom:1px;clear:both;font-size: 11px;">';
        echo '<div class="warn" style=" margin-top:10px;margin-bottom:10px;">Instance not found, no '. $modecheck['ARRNAME'] .' performed</div>';
        echo '</div>';
    }

    echo '</div>';
    echo '<div style="clear:both;"></div>';

    // !TODO fix missing heading properly rather than this messy work-around
    $folders['ARRNAME'] = 'Selected Folders';
    showDev( $folders );
    showDev( $modecheck );





        showDev( $database );
?>



<?php
    showDev( $fpa );

    if ( defined( '_FPA_DEV' ) ) {

        echo '<div class="dev-mode-information">';
        echo '<span class="dev-mode-title">FPA Memory Statistics : </span> (requires PHP4.3.2 & PHP5.2.0)<br />';
        echo '<div style="margin-left: 10px;">';

            function convert($size) {
                $unit=array('b','kb','mb','gb','tb','pb');
                return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
            }

                if( function_exists('memory_get_usage') ) {
                    echo 'Currently Allocated Memory: '. convert( memory_get_usage() ) .'<br />'; // currently allocated memory
                } else {
                    echo _PHP_VERLOW .', memory_get_usage '. _FPA_DNE .'<br />';
                }


                if( function_exists('memory_get_peak_usage') ) {
                    echo 'Total Peak Memory: '. convert( memory_get_peak_usage(true) ); // total peak memory usage
                } else {
                    echo _PHP_VERLOW .', memory_get_peak_usage '. _FPA_DNE .'<br />';
                }

        echo '<p style="font-weight:bold;"><em>total runtime : '. mt_end() .' seconds</em></p>';
        echo '</div>';
        echo '</div>';
    }

        echo '<div class="dev-mode-information dev-mode-title" style="text-align:center;color:#4D8000!important;">';
        echo '<a style="color:#4D8000!important;" href="'. _RES_FPALINK .'" target="_github">'. _RES_FPALATEST .' '. _RES .'</a>';
        echo '</div>';
?>

    </body>
</html>