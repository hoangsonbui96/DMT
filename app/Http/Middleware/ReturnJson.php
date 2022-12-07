<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\Admin\AdminController;

class ReturnJson
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        $response = $next($request);
        $response_data = $response->getOriginalContent();
        if (isset($response_data) && $response_data != null && is_array($response_data)){
            if (array_key_exists('error', $response_data)) {
                if (is_array($response_data['error'])) {
                    foreach ($response_data['error'] as $value) {
                        $error = $value;
                        break;
                    }
                } else {
                    $error = $response_data['error'];
                }
            } elseif (array_key_exists('errors', $response_data)) {
                if (is_array($response_data['errors'])) {
                    foreach ($response_data['errors'] as $value) {
                        $error = $value;
                        break;
                    }
                } else {
                    $error = $response_data['errors'];
                }
            } else {
                $error = null;
            }
            if (array_key_exists('success', $response_data)) {
                $success = $response_data['success'];
            } else {
                $success = null;
            }
            if (array_key_exists('status_code', $response_data)) {
                $status_code = $response_data['status_code'];
            } else {
                $status_code = $response->status();
            }
            if (array_key_exists('data', $response_data)) {
                $data = $response_data['data'];
            } else {
                $data = $response_data && array_key_exists('success', $response_data) == false && array_key_exists('error', $response_data) == false ? $response_data : null;
            }
            if (array_key_exists('role', $response_data) && $response_data['role'] != null) {
                $role = $response_data['role'];
            } elseif (array_key_exists('role_key', $response_data)) {
                $role = AdminController::getRoleByScreen($response_data['role_key']);
            } elseif (isset($data) && array_key_exists('role_key', $data)) {
                $role = AdminController::getRoleByScreen($data['role_key']);
            } else {
                $role = [];
            }
            $new_response_data = AdminController::responseApi($status_code, $error, $success, $data, $role);
            $response->setData($new_response_data->getOriginalContent());

        } else {
            $new_response_data = AdminController::responseApi(200);
            $response->setData($new_response_data->getOriginalContent());
        }

        return $response;
    }
}
