<?php
namespace App\Helpers\User;

use App\Helpers\Venturo;
use App\Models\UserRole;
use Throwable;

class RoleHelper extends Venturo
{
    private $userRole;

    public function __construct()
    {
        $this->userRole = new UserRole();
    }

    /**
     * method untuk menginput data baru ke tabel user_auth
     *
     * @author Ari Gunawan <arigunawanjatmiko@gmail.com>
     *
     * @param array $payload
     *                       $payload['name'] = string
     *
     * @return array
     */
    public function create(array $payload): array
    {
        try {
            $role = $this->userRole->store($payload);

            return [
                'status' => true,
                'data' => $role
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    /**
     * Menghapus data user dengan sistem "Soft Delete"
     * yaitu mengisi kolom deleted_at agar data tsb tidak
     * keselect waktu menggunakan Query
     *
     * @param integer $id id dari tabel user_auth
     *
     * @return bool
     */
    public function delete(string $id): bool
    {
        try {
            $this->userRole->drop($id);

            return true;
        } catch (Throwable $th) {
            return false;
        }
    }

    /**
     * Mengambil data user dari tabel user_auth
     *
     * @author Wahyu Agung <wahyuagung26@gmail.com>
     *
     * @param array $filter
     *                      $filter['name'] = string
     *                      $filter['email'] = string
     * @param integer $itemPerPage jumlah data yang ditampilkan, kosongi jika ingin menampilkan semua data
     * @param string $sort nama kolom untuk melakukan sorting mysql beserta tipenya DESC / ASC
     *
     * @return array
     */
    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $roles = $this->userRole->getAll($filter, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $roles
        ];
    }

    /**
     * Mengambil 1 data user dari tabel user_auth
     *
     * @param integer $id id dari tabel user_auth
     *
     * @return array
     */
    public function getById(string $id): array
    {
        $role = $this->userRole->getById($id);
        if (empty($role)) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $role
        ];
    }

    /**
     * method untuk mengubah user pada tabel user_auth
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     *
     * @param array $payload
     *                       $payload['name'] = string
     *                       $payload['email] = string
     *                       $payload['password] = string
     *
     * @return array
     */
    public function update(array $payload, string $id): array
    {
        try {
            $this->userRole->edit($payload, $id);

            $role = $this->getById($id);

            return [
                'status' => true,
                'data' => $role['data']
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    /**
     * Upload file and remove payload when photo is not exist
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     *
     * @param array $payload
     * @return array
     */
    
}