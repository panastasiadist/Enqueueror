<?php
declare(strict_types=1);

namespace panastasiadist\Enqueueror;

class Logger
{
    private $output_filepath = '';

    private $log_structures = array();

    private function log( string $message, $level = 'debug' )
    {
        $this->log_structures[] = array(
            'message' => $message,
            'date' => date( 'Y-m-d H:i:s' ),
            'level' => $level,
        );

        $this->flush();
    }

    public function __construct( string $output_filepath )
    {
        $this->output_filepath = $output_filepath;
    }

    public function debug( string $message )
    {
        $this->log( $message, 'debug' );
    }

    public function info( string $message )
    {
        $this->log( $message, 'info' );
    }

    public function warn( string $message )
    {
        $this->log( $message, 'warn' );
    }

    public function error( string $message )
    {
        $this->log( $message, 'error' );
    }

    public function flush()
    {
        $text_lines = array();

        foreach ( $this->log_structures as $structure ) {
            $text_lines[] = '[' . $structure[ 'date' ] . ' -> ' . strtoupper( $structure[ 'level' ] ) . ']: ' . $structure[ 'message' ];
        }

        $text = implode( PHP_EOL, $text_lines ) . PHP_EOL;
        
        file_put_contents( $this->output_filepath, $text, FILE_APPEND );

        $this->log_structures = array();
    }
}