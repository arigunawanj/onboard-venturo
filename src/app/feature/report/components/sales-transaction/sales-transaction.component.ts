import { Component, ViewChild } from '@angular/core';
import { DataTableDirective } from 'angular-datatables';
import { SalesService } from '../../services/sales.service';
import { CustomerService } from 'src/app/feature/customer/services/customer.service';
import { ProductService } from 'src/app/feature/product/product/services/product.service';
import * as FileSaver from 'file-saver';

@Component({
  selector: 'app-sales-transaction',
  templateUrl: './sales-transaction.component.html',
  styleUrls: ['./sales-transaction.component.scss']
})
export class SalesTransactionComponent {
  filter: {
    start_date: string,
    end_date: string,
    customer_id: any,
    product_id: any
  }
  showLoading: boolean;
  @ViewChild(DataTableDirective)
  dtElement: DataTableDirective;
  dtInstance: Promise<DataTables.Api>;
  dtOptions: any;

  listSaleTransactions: any;
  customers: [];
  products: [];

  page: number;
  pageSize: number;
  collectionSize: number;


  constructor(
    private salesService: SalesService,
    private customerService: CustomerService,
    private productService: ProductService
  ) { }

  ngOnInit(): void {
    this.resetFilter();
    this.getSaleTransactions();
    this.getCustomers();
    this.getProducts();
  }

  resetFilter() {
    this.filter = {
      start_date: null,
      end_date: null,
      customer_id: [],
      product_id: []
    }
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

  getProducts(name = '') {
    this.showLoading = true;
    this.productService.getProducts({ name: name }).subscribe((res: any) => {
      this.products = res.data.list;
      this.showLoading = false;
    }, err => {
      console.log(err);
    });
  }

 getFileCSV() {
    this.salesService.getReportCSV(this.filter).subscribe(
      (data) => {
        // Handle the CSV data here
        // Save the CSV file using FileSaver.js
        const blob = new Blob([data], { type: 'text/csv' });
        FileSaver.saveAs(blob, 'Repost Sales.csv');
      },
      (error) => {
        console.error('Error:', error);
      }
    );
  }

 getFilePDF() {
    this.salesService.getReportPDF(this.filter).subscribe(
      (data) => {
        // Handle the PDF data here
        // Save the PDF file using FileSaver.js
        const blob = new Blob([data], { type: 'text/pdf' });
        FileSaver.saveAs(blob, 'Report Sales.pdf');
      },
      (error) => {
        console.error('Error:', error);
      }
    );
  }

  getSaleTransactions() {
    this.dtOptions = {
      serverSide: true,
      processing: true,
      ordering: false,
      pageLength: 5,
      ajax: (dtParams: any, callback) => {
        const params = {
          ...this.filter,
          per_page: dtParams.length,
          page: dtParams.start / dtParams.length + 1,
        };

        this.salesService.getSalesTransaction(params).subscribe(
          (res: any) => {
            const { list, meta } = res.data;

            let number = dtParams.start + 1;
            list.forEach((val) => {
              val.no = number++;
            });
            this.listSaleTransactions = list;

            callback({
              recordsTotal: meta.total,
              recordsFiltered: meta.total,
              data: [],
            });
          },
          (err: any) => { }
        );
      },
    };
  }

  countSubTotal($itemPrice, $itemCount) {
    return $itemPrice * $itemCount;
  }

  reloadDataTable(): void {
    this.dtElement.dtInstance.then((dtInstance: DataTables.Api) => {
      dtInstance.draw();
    });
  }

  setFilterPeriod($event) {
    this.filter.start_date = $event.startDate;
    this.filter.end_date = $event.endDate;
    this.reloadDataTable();
  }

  setFilterCustomer(customers) {
    this.filter.customer_id = this.generateSafeParam(customers);
    this.reloadDataTable();
  }

  setFilterPromo(promos) {
    this.filter.product_id = this.generateSafeParam(promos);
    this.reloadDataTable();
  }

  generateSafeParam(list) {
    let paramId = [];
    list.forEach(val => (paramId.push(val.id)));
    if (!paramId) return '';
    console.log(paramId);

    return paramId.join(',')
  }
}
