<?php

namespace App\Exceptions;

use Exception;

class RatingAlreadyExistException extends Exception
{

    public $message;
    public $data;

    public function __construct($message,$data = null, Exception $previous = NULL)
    {
        $this->message = $message;
        $this->data = $data;
    }


    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        return false;
    }
 
    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render()
    {
        return response()->conflict($this->message);
    }
}
