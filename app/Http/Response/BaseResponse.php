<?php


namespace App\Http\Response;


use Illuminate\Contracts\Support\Responsable;

class BaseResponse implements Responsable
{
    public function __construct(
        public array $data = [],
        public bool $success = true,
        public string $message = "Success"
        )
    {

    }

    public function toResponse($request): \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        return response()->json([
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data
        ]);
    }
}
