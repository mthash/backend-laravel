<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const   HTTP_OK                 = 200;
    const   HTTP_SERVER_ERROR       = 500;
    const   HTTP_NOT_FOUND          = 404;
    const   HTTP_NO_CONTENT         = 204;
    const   HTTP_BAD_REQUEST        = 400;
    const   HTTP_UNAUTHORIZED       = 401;
    const   HTTP_FORBIDDEN          = 403;
    const   HTTP_CREATED            = 201;
    const   HTTP_UNPROCESSABLE      = 422;

    public function webResponse ($data, int $httpCode = self::HTTP_OK, ?string $message = null, ?string $trace = null)
    {
        $response['code']           = $httpCode;
        $response['body']           = $data;
        $response['message']        = $message;

        if (!empty ($trace)) $response['trace'] = $trace;

        if ($data instanceof \Throwable)
        {
            $response['code']           = self::HTTP_SERVER_ERROR;
            $response['body']           = null;
            $response['message']        = $data->getMessage();
            $response['trace']          = $data->getTraceAsString();

            if ($data instanceof \ValidationException)
            {
                $response['code']       = self::HTTP_UNPROCESSABLE;
                $response['errors']     = $response['body'];
                $response['body']       = null;
            }
        }

        if ($httpCode == self::HTTP_UNPROCESSABLE)
        {
            $response['errors']     = $response['body'];
            $response['body']       = null;
        }

//        $this->response->setStatusCode($response['code']);
//        $this->response->setContent('application/json');
//        $this->response->setJsonContent($response);
//        $this->response->send();
//        exit;
        return response()->json($response, $response['code']);
    }
}
