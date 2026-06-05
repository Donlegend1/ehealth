<?php

if (! function_exists('apiResponse')) {
    /**
     * Return a standardized JSON response.
     *
     * @param bool $success
     * @param string|null $message
     * @param mixed $data
     * @param int $statusCode
     * @param array $extra
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    function apiResponse(
        bool $success,
        ?string $message = null,
        mixed $data = null,
        int $statusCode = 200,
        array $extra = [],
        array $headers = []
    ) {
        $response = [
            'success' => $success,
        ];

        if ($message !== null) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        if (!empty($extra)) {
            $response = array_merge($response, $extra);
        }

        return response()->json($response, $statusCode, $headers);
    }
}

if (! function_exists('apiSuccess')) {
    /**
     * Return a standardized success JSON response.
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $statusCode
     * @param array $extra
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    function apiSuccess(
        mixed $data = null,
        ?string $message = null,
        int $statusCode = 200,
        array $extra = [],
        array $headers = []
    ) {
        return apiResponse(
            success: true,
            message: $message,
            data: $data,
            statusCode: $statusCode,
            extra: $extra,
            headers: $headers
        );
    }
}

if (! function_exists('apiError')) {
    /**
     * Return a standardized error JSON response.
     *
     * @param string $message
     * @param int $statusCode
     * @param array $extra
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    function apiError(
        string $message,
        int $statusCode = 400,
        array $extra = [],
        array $headers = []
    ) {
        return apiResponse(
            success: false,
            message: $message,
            data: null,
            statusCode: $statusCode,
            extra: $extra,
            headers: $headers
        );
    }
}
