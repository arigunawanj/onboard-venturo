import { DataTableDirective } from 'angular-datatables';
import { CustomerService } from './../../../customer/services/customer.service';
import { Component, OnInit, ViewChild } from '@angular/core';
import { SalesService } from '../../services/sales.service';
import { LandaService } from 'src/app/core/services/landa.service';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-sales-customer',
  templateUrl: './sales-customer.component.html',
  styleUrls: ['./sales-customer.component.scss']
})
export class SalesCustomerComponent implements OnInit {
  filter: {
    start_date: string,
    end_date: string,
    customer_id: any,
  }

  meta: {
    dates: [],
    total_per_date: [],
    grand_total: 0
  };

  sales = [{
    no: 0,
    customer_name: '',
    customer_total: 0,
    transactions: [
      { total_buy: 0 }
    ]
  }];

  customers = [];
  showLoading: boolean;

  constructor(
    private salesService: SalesService,
    private customerService: CustomerService,
    private landaService: LandaService
  ) { }

  ngOnInit(): void {
    this.resetFilter();
    this.getCustomers();
  }

  resetFilter() {
    this.filter = {
      start_date: null,
      end_date: null,
      customer_id: null
    }

    this.meta = {
      dates: [],
      total_per_date: [],
      grand_total: 0
    }

    this.showLoading = false;
  }

  setFilterPeriod($event) {
    this.filter.start_date = $event.startDate;
    this.filter.end_date = $event.endDate;
  }

  setFilterCustomer($event) {
    this.filter.customer_id = $event.id;
  }

  getCustomers(name = '') {
    this.showLoading = true;
    this.customerService.getCustomers({ name: name }).subscribe((res: any) => {
      this.customers = res.data.list;
      this.showLoading = false;
    }, err => {
      console.log(err);
    });
  }

  reloadSales() {
    this.runFilterValidation();

    this.salesService.getSalesCustomer(this.filter).subscribe((res: any) => {
      const { data, settings } = res;
      let number = 1;
      data.forEach((val) => (val.no = number++));
      this.sales = data;
      this.meta = settings;
    });
  }

  runFilterValidation() {
    if (!this.filter.start_date || !this.filter.end_date) {
      Swal.fire({
        title: 'Terjadi Kesalahan',
        text: 'Silahkan isi periode penjualan terlebih dahulu',
        icon: 'warning',
        showCancelButton: false
      });
      throw new Error("Start and End date is required");
    }
  }
  
}
