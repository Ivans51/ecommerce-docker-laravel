<?php

namespace App\Http\Controllers;

use App\Utils\Config;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class BaseController extends Controller {

  /**
   * @var array
   */
  private $config;

  public function __construct() {
    $this->config = (new Config())->getEnv();
  }

  /**
   * success response method.
   *
   * @param $result
   * @param $message
   * @return JsonResponse
   */
  public function sendResponse($result, $message): JsonResponse {
    $response = [
      'success' => true,
      'data'    => $result,
      'message' => $message,
    ];

    return response()->json($response);
  }


  /**
   * return error response.
   *
   * @param $error
   * @param array $errorMessages
   * @param int $code
   * @return JsonResponse
   */
  public function sendError($error, array $errorMessages = [], int $code = 404): JsonResponse {
    $response = [
      'success' => false,
      'message' => $error,
    ];

    if (!empty($errorMessages)) {
      $response['data'] = $errorMessages;
    }

    return response()->json($response, $code);
  }

  /**
   * @param Exception $error
   * @return RedirectResponse
   */
  public function exceptionError(\Throwable $error): RedirectResponse {
    if ($error instanceof QueryException) {
      return back()->withErrors([
        'message' => $this->config['APP_ENV'] !== 'Local' ? $error->getMessage() : 'Base data error',
      ]);
    } else {
      return back()->withErrors([
        'message' => 'something is wrong',
      ]);
    }
  }
}
