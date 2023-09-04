<?php

namespace App\Helpers\Promo;

use Throwable;
use App\Models\Promo;
use App\Helpers\Venturo;

class PromoHelper extends Venturo {
    const PROMO_PHOTO_DIRECTORY = 'foto-promo';

    private $promo;

    public function __construct()
    {
        $this->promo = new Promo();
    }

    /**
     * method untuk menginput data baru ke tabel m_promo
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     *
     * @param array $payload
     *                       $payload['name'] = string
     *                       $payload['status'] = string
     *                       $payload['expired_in_day'] = number
     *                       $payload['nominal_percentage'] = number
     *                       $payload['nominal_rupiah'] = number
     *                       $payload['term_conditions'] = string
     *                       $payload['photo'] = string
     *
     * @return array
     */
    public function create(array $payload): array
    {
        try {
            $payload = $this->uploadAndGetPayload($payload);
            $promo = $this->promo->store($payload);

            return [
                'status' => true,
                'data' => $promo
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    /**
     * Menghapus data promo dengan sistem "Soft Delete"
     * yaitu mengisi kolom deleted_at agar data tsb tidak
     * keselect waktu menggunakan Query
     *
     * @param integer $id id dari tabel m_promo
     *
     * @return bool
     */
    public function delete(string $id): bool
    {
        try {
            $this->promo->drop($id);

            return true;
        } catch (Throwable $th) {
            return false;
        }
    }

    /**
     * Mengambil data promo dari tabel m_promo
     *
     * @author Wahyu Agung <wahyuagung26@gmail.com>
     *
     * @param array $filter
     *                      $filter['name'] = string
     *                      $filter['status'] = string
     * @param integer $itemPerPage jumlah data yang ditampilkan, kosongi jika ingin menampilkan semua data
     * @param string $sort nama kolom untuk melakukan sorting mysql beserta tipenya DESC / ASC
     *
     * @return array
     */
    public function getAll(array $filter, int $itemPerPage = 0, string $sort = '')
    {
        $promo = $this->promo->getAll($filter, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $promo
        ];
    }

    /**
     * Mengambil 1 data promo dari tabel m_promo
     *
     * @param integer $id id dari tabel m_promo
     *
     * @return array
     */
    public function getById(string $id): array
    {
        $promo = $this->promo->getById($id);
        if (empty($promo)) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $promo
        ];
    }

    /**
     * method untuk mengubah promo pada tabel m_promo
     *
     * @author Wahyu Agung <wahyuagung26@email.com>
     *
     * @param array $payload
     *                       $payload['name'] = string
     *                       $payload['status'] = string
     *                       $payload['expired_in_day'] = number
     *                       $payload['nominal_percentage'] = number
     *                       $payload['nominal_rupiah'] = number
     *                       $payload['term_conditions'] = string
     *                       $payload['photo'] = string
     *
     * @return array
     */
    public function update(array $payload, string $id): array
    {
        try {
            $payload = $this->uploadAndGetPayload($payload);
            $this->promo->edit($payload, $id);

            $promo = $this->getById($id);

            return [
                'status' => true,
                'data' => $promo['data']
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    private function uploadAndGetPayload(array $payload)
    {
        /**
         * Jika dalam payload terdapat base64 foto, maka Upload foto ke folder public/uploads/foto-product
         */
        if (!empty($payload['photo'])) {
            $fileName = $this->generateFileName($payload['photo'], 'PROMO_' . date('Ymdhis'));
            $photo = $payload['photo']->storeAs(self::PROMO_PHOTO_DIRECTORY, $fileName, 'public');
            $payload['photo'] = $photo;
        } else {
            unset($payload['photo']); // Jika foto kosong, hapus dari array agar tidak diupdate
        }

        return $payload;
    }

}
