<?php

namespace Drupal\tinymce\Controller;

use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Implementing our example JSON api.
 */
class TinyMCEController extends ControllerBase {

  /**
   * Upload file retrieved through a POST request on the route.
   *
   * @param Request $request
   * @return JsonResponse
   */
  public function upload(Request $request): JsonResponse {

    // Check that we retrieve a multipart/form-data element or return an error.
    if (strpos($request->headers->get('Content-Type'), 'multipart/form-data;') !== 0) {
      $res = new JsonResponse();
      $res->setStatusCode(400, 'Please submit multipart/form-data');
      return $res;
    }

    // Retrieve file content.
    reset ($_FILES);
    $tmpFile = current($_FILES);
    $data = file_get_contents($tmpFile['tmp_name']);

    // Prepare saving.
    $destinationFolder = 'public://tinymce/';
    $destinationFile = $destinationFolder . $tmpFile['name'];

    // Attempt to save the file and the folder if it does not exist.
    if (\Drupal::service('file_system')->prepareDirectory($destinationFolder, FileSystemInterface::CREATE_DIRECTORY)) {
      $file = file_save_data($data, $destinationFile, FILE_EXISTS_REPLACE);
    }

    // Prepare the json output for the editor.
    $path = file_create_url($destinationFile);
    $response['location'] = $path;

    return new JsonResponse($response);
  }

}
