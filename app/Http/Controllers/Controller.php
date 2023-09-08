<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function trueResponse($message, $data = null, $meta = null)
    {
        $data = $this->format($data);
        $meta = $this->format($meta);

        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data,
            'meta'    => $meta,
            'error'   => []
        ], Response::HTTP_OK);
    }

    protected function falseResponse($message, $errors = null, $list = false)
    {
        $errors = $this->format($errors);

        $data = (object)[];
        if ($list) {
            $data = [];
        }

        return response()->json([
            'status'  => false,
            'message' => $message,
            'data'    => $data,
            'meta'    => (object)[],
            'error'   => $errors
        ], Response::HTTP_OK);
    }

    protected function format($var)
    {
        if (is_array($var) && sizeof($var) === 0) [];
        elseif (!$var) $var = (object)[];
        return $var;
    }

    protected function metaPagination($data)
    {
        if (!$data) {
            $data = new LengthAwarePaginator(0, 0, 10);
        }

        return [
            'pagination' => [
                'total'         => $data->total(),
                'count'         => $data->count(),
                'per_page'      => $data->perPage(),
                'current_page'  => $data->currentPage(),
                'total_pages'   => $data->lastPage(),
                'has_more_page' => $data->hasMorePages(),
            ],
        ];
    }
}
