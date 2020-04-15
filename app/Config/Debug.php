<?php
/*
  |--------------------------------------------------------------------------
  | ERROR DISPLAY
  |--------------------------------------------------------------------------
  |
  |
 */

    echo '<style> .database_erroryzx{ width:0 100px;margin: 10px;
            padding-top:1pc;padding-bottom:1pc;padding-left:2pc;padding-right:2pc;
            background:#e32b4a;color:#fff;font-size:16px; }</style>';

    $debug_handler = 'testing';

    if($debug_handler == 'testing' || $debug_handler == 'development')
    {
        function errorHandler($errno, $errstr, $errfile, $errline) {

            if (!(error_reporting() & $errno)) {
                // This error code is not included in error_reporting, so let it fall
                // through to the standard PHP error handler
                return false;
            }

            switch ($errno) {
                case E_STRICT:
                    echo '<div class="database_erroryzx">';
                    echo("<b>Strict error</b> $errstr at $errfile : $errline \n");
                    echo '</div>';
                    break;

                case E_NOTICE:
                case E_USER_NOTICE:
                    echo '<div class="database_erroryzx">';
                    echo("<b>Notice error</b> $errstr at $errfile : $errline \n");
                    echo '</div>';
                    break;

                case E_DEPRECATED:
                case E_USER_DEPRECATED:
                    echo '<div class="database_erroryzx">';
                    echo("<b>Deprecated error</b> $errstr at $errfile : $errline \n");
                    echo '</div>';
                    break;

                case E_WARNING:
                case E_USER_WARNING:
                case E_COMPILE_WARNING:
                case E_RECOVERABLE_ERROR:

                    echo '<div class="database_erroryzx">';
                    echo("<b>Warning error </b>$errstr at $errfile : $errline \n");
                    echo '</div>';
                    break;
                    
                case E_PARSE:
                case E_ERROR:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                    echo '<div class="database_erroryzx">';
                    echo("<b>Fatal error</b> $errstr at $errfile : $errline \n");
                    echo '</div><br/>';
                    break;

                default:
                    echo '<div class="database_erroryzx">';
                    exit("Unknown error</b> at $errfile : $errline \n");
                    echo '</div><br/>';
            }
        }
        function log_exception( Exception $e )
        {
            $message = "Type: " . get_class( $e ) . "; Message: {$e->getMessage()}; File: {$e->getFile()}; Line: {$e->getLine()};";
            echo '<div class="database_erroryzx">';
            echo $message;
            echo '</div><br/>';
        }
        set_error_handler("errorHandler");
        set_exception_handler( "log_exception" );
        
        

    }else if($debug_handler == 'production'){
        error_reporting(0);
        echo '<div class="database_erroryzx" style="background:#3273a8;text-align:center;font-size:24px;">';
        echo "Oops! Something went wrong! We're looking into it!";
        echo '</div><br/>';
    }else{
        // error handler function
        function errorHandler($errno, $errstr) {
            echo "Error: [$errno] $errstr";
        }
        
        // set error handler
        set_error_handler("errorHandler");
        
    }