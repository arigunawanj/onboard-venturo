<?php

namespace App\Helpers\Report;

use App\Helpers\Venturo;
use App\Models\SalesModel;
use DateInterval;
use DatePeriod;
use DateTime;

class SalesCustomerHelper extends Venturo
{
    private $dates;
    private $endDate;
    private $sales;
    private $startDate;
    private $total;
    private $totalPerDate;

    public function __construct()
    {
        $this->sales = new SalesModel();
    }

    public function get($startDate, $endDate, $customerId = [])
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;

        $sales = $this->sales->getSalesByCustomer($startDate, $endDate, $customerId);

        // dd($sales->toArray());
        return [
            'status'     => true,
            'data'       => $this->reformatReport($sales, $startDate, $endDate),
            'dates'          => array_values($this->dates),
            'total_per_date' => array_values($this->totalPerDate),
            'grand_total'    => $this->total
        ];
    }

    private function convertNumericKey($salesDetail)
    {
        $indexSales = 0;

        foreach ($salesDetail as $sales) {
            $list[$indexSales] = [
                'customer_id'    => $sales['customer_id'],
                'customer_name'  => $sales['customer_name'],
                'customer_total' => $sales['customer_total'],
                'transactions'   => array_values($sales['transactions']),
            ];
            $indexSales++;
        }

        unset($salesDetail);

        return $list ?? [];
    }

    /**
     * get list date between start and end date
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    private function getPeriode()
    {
        $begin = new DateTime($this->startDate);
        $end   = new DateTime($this->endDate);
        $end   = $end->modify('+1 day');

        $interval = DateInterval::createFromDateString('1 day');
        $period   = new DatePeriod($begin, $interval, $end);

        foreach ($period as $dt) {
            $date         = $dt->format('Y-m-d');
            $dates[$date] = [
                'date_transaction' => $date,
                'total_buy'      => 0,
            ];

            $this->setDefaultTotal($date);
            $this->setSelectedDate($date);
        }

        return $dates ?? [];
    }

    private function setDefaultTotal(string $date)
    {
        $this->totalPerDate[$date] = 0;
    }

    private function setSelectedDate(string $date)
    {
        $this->dates[] = $date;
    }

    private function reformatReport($list)
    {
        $list        = $list->toArray();
        $periods     = $this->getPeriode();
        $salesDetail = [];

        foreach ($list as $sales) {
            foreach ($sales['details'] as $detail) {
                // Skip if relation to product is not found
                if (empty($detail['product'])) {
                    continue;
                }

                $date                   = date('Y-m-d', strtotime($sales['date']));
                $customerId             = $sales['m_customer_id'];
                $customerName           = $sales['customer']['name'];
                $totalBuy               = $detail['price'] * $detail['total_item'];
                $listTransactions       = $salesDetail[$customerId]['transactions'] ?? $periods;
                $subTotal               = $salesDetail[$customerId]['transactions'][$date]['total_buy'] ?? 0;
                $totalPerCustomer       = $salesDetail[$customerId]['customer_total'] ?? 0;

                $salesDetail[$customerId] = [
                    'customer_id'    => $customerId,
                    'customer_name'  => $customerName,
                    'customer_total' => $totalPerCustomer + $totalBuy,
                    'transactions'   => $listTransactions,
                ];



                $salesDetail[$customerId]['transactions'][$date] = [
                    'date_transaction' => $date,
                    'total_buy'      => $totalBuy + $subTotal
                ];

                $this->totalPerDate[$date] = ($this->totalPerDate[$date] ?? 0) + $totalBuy;
                $this->total               = ($this->total ?? 0) + $totalBuy;
            }
        }

        // dd($salesDetail);

        return $this->convertNumericKey($salesDetail);
    }
}
