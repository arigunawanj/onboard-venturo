<?php

namespace App\Helpers\Diskon;

use Throwable;
use App\Helpers\Venturo;
use App\Models\DiskonModel;

class DiskonHelper extends Venturo {
    private $diskon;

  public function __construct()
  {
      $this->diskon = new DiskonModel();
  }

  public function create(array $payload): array
  {
      try {
          $diskon = $this->diskon->store($payload);

          return [
              'status' => true,
              'data' => $diskon
          ];
      } catch (Throwable $th) {
          return [
              'status' => false,
              'error' => $th->getMessage()
          ];
      }
  }

  public function delete(string $id): bool
  {
      try {
          $this->diskon->drop($id);

          return true;
      } catch (Throwable $th) {
          return false;
      }
  }

  public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
  {
      $categories = $this->diskon->getAll($filter, $itemPerPage, $sort);

      return [
          'status' => true,
          'data' => $categories
      ];
  }

  public function getById(string $id): array
  {
      $diskon = $this->diskon->getById($id);
      if (empty($diskon)) {
          return [
              'status' => false,
              'data' => null
          ];
      }

      return [
          'status' => true,
          'data' => $diskon
      ];
  }

  public function update(array $payload, string $id): array
  {
      try {
          $this->diskon->edit($payload, $id);

          $diskon = $this->getById($id);

          return [
              'status' => true,
              'data' => $diskon['data']
          ];
      } catch (Throwable $th) {
          return [
              'status' => false,
              'error' => $th->getMessage()
          ];
      }
  }
}
