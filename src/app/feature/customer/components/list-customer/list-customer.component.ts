import { Component, OnInit, ViewChild } from "@angular/core";
import { NgbModal } from "@ng-bootstrap/ng-bootstrap";
import { DataTableDirective } from "angular-datatables";
import Swal from "sweetalert2";
import { CustomerService } from "../../services/customer.service";

@Component({
  selector: 'app-list-customer',
  templateUrl: './list-customer.component.html',
  styleUrls: ['./list-customer.component.scss']
})
export class ListCustomerComponent implements OnInit {
  listCustomers: any;
  titleModal: string;
  customerId: number;

  showLoading: boolean;
  @ViewChild(DataTableDirective)
  dtElement: DataTableDirective;
  dtInstance: Promise<DataTables.Api>;
  dtOptions: any;

  customers: [];

  filter: {
    name: "";
    customer_id: any;
    is_verified: "";
  };

  constructor(
    private customerService: CustomerService,
    private modalService: NgbModal
  ) {}

  getCustomer() {
    this.dtOptions = {
      serverSide: true,
      processing: true,
      ordering: false,
      pageLength: 10,
      ajax: (dtParams: any, callback) => {
        const params = {
          ...this.filter,
          per_page: dtParams.length,
          page: dtParams.start / dtParams.length + 1,
        };

        this.customerService.getCustomers(params).subscribe(
          (res: any) => {
            const { list, meta } = res.data;

            let number = dtParams.start + 1;
            list.forEach((val) => {
              val.no = number++;
            });
            this.listCustomers = list;

            callback({
              recordsTotal: meta.total,
              recordsFiltered: meta.total,
              data: [],
            });
          },
          (err: any) => {}
        );
      },
    };
  }

  getCustomersList(name = "") {
    this.showLoading = true;
    this.customerService.getCustomers({ name: name }).subscribe(
      (res: any) => {
        this.customers = res.data.list;
        this.showLoading = false;
      },
      (err) => {
        console.log(err);
      }
    );
  }

  reloadDataTable(): void {
    this.dtElement.dtInstance.then((dtInstance: DataTables.Api) => {
      dtInstance.draw();
    });
  }

  createCustomer(modalId) {
    this.titleModal = "Tambah Customer";
    this.customerId = 0;
    this.modalService.open(modalId, { size: "lg", backdrop: "static" });
  }

  updateCustomer(modalId, customer) {
    this.titleModal = "Edit Customer: " + customer.name;
    this.customerId = customer.id;
    this.modalService.open(modalId, { size: "lg", backdrop: "static" });
  }

  deleteCustomer(customerId) {
    Swal.fire({
      title: "Apakah kamu yakin ?",
      text: "Customer ini tidak dapat membeli setelah kamu menghapus datanya",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#34c38f",
      cancelButtonColor: "#f46a6a",
      confirmButtonText: "Ya, Hapus data ini !",
    }).then((result) => {
      if (!result.value) return false;

      this.customerService.deleteCustomer(customerId).subscribe((res: any) => {
        this.getCustomer();
      });
    });
  }

  setDefault() {
    this.filter = {
      name: "",
      customer_id: "",
      is_verified: null,
    };
  }

  filterByCustomer(customers) {
    let customersId = [];
    customers.forEach((val) => customersId.push(val.id));
    if (!customersId) return false;

    this.filter.customer_id = customersId.join(",");
    this.reloadDataTable();
  }

  ngOnInit(): void {
    this.setDefault();
    this.getCustomer();
    this.getCustomersList();
  }

  
}
