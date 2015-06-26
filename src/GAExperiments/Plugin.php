<?php
namespace GAExperiments;

class Plugin extends Pimple {
    
    public function run(){

        foreach( $this->keys() as $key ){ // Loop on contents
            $content = $this[$key];

            if( is_object( $content ) ){
                $reflection = new \ReflectionClass( $content );
                if( $reflection->hasMethod( 'run' ) ){
                    $content->run(); // Call run method on object
                }
            }
        }
    }
}
