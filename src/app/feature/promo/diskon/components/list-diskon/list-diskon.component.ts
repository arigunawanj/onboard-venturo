import { Component, EventEmitter,  OnInit, Output, ViewChild } from '@angular/core';
import { DataTableDirective } from 'angular-datatables';
import { CustomerService } from 'src/app/feature/customer/services/customer.service';
import { LandaService } from 'src/app/core/services/landa.service';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { DiskonService } from '../../services/diskon.service';
import { PromoService } from 'src/app/feature/promo/services/promo.service';

@Component({
  selector: 'app-list-diskon',
  templateUrl: './list-diskon.component.html',
  styleUrls: ['./list-diskon.component.scss']
})
export class ListDiskonComponent implements OnInit {
  readonly PROMO_DISCOUNT = 'diskon';
  readonly MODE_CREATE = 'create';
  readonly MODE_UPDATE = 'update';

  @ViewChild(DataTableDirective)
  dtElement: DataTableDirective;
  dtInstance: Promise<DataTables.Api>;
  dtOptions: any;

  discountId:number;
  customerId: number;
  @Output() afterSave = new EventEmitter<boolean>();

  showLoading: boolean;
  check:boolean;
  listCustomers: any;
  promo: any;
  totalActiveDiscount: number;
  titleModal: string;
  showForm: boolean;
  payload: {
    id: string,
    customer_id: string,
    promo_id: string,
    is_status: number,
  }
  customers: [];
  filter: {
    name: any
  };
  listDiscount: any

  constructor(
    private discountService: DiskonService,
    private customerService: CustomerService,
    private promoService: PromoService,
    private landaService: LandaService,
    private modalService: NgbModal
  ) { }

  ngOnInit(): void {
    this.showForm = false;
    this.setDefaultFilter();
    this.getPromo();
    this.getCustomers();
    this.getListCustomer();
    this.getDiscount();
  }

  setDefaultFilter() {
    this.filter = {
      name: ''
    }
  }

  getDiscount(){
    this.discountService.getDiscount().subscribe((res:any)=>{
      const { list } = res.data;
      this.listDiscount = list;

      /**
       * Baris kode dibawah untuk menambahkan thead dan tfoot
       */
      const tfoot = document.querySelector('tfoot tr');
      const thead = document.querySelector('thead tr');
      this.promo.forEach(promo => {

        const discountHeader = document.createElement('th');
        discountHeader.className = "vertical-middle text-center head-id-" + promo.id;
        discountHeader.innerText = promo.name;
        thead.appendChild(discountHeader);

        this.totalActiveDiscount = 0;

        this.listDiscount.forEach(discount => {
          if(discount.promo_id == promo.id && discount.is_status == 1){
            this.totalActiveDiscount += 1;
          }
        });

        const discountFooter = document.createElement('td')
        discountFooter.className = "vertical-middle text-center align-middle total-id-"+promo.id;
        discountFooter.innerText = String(this.totalActiveDiscount);
        tfoot.appendChild(discountFooter);

        let totalHeadClass = document.querySelectorAll(".head-id-"+ promo.id);
        let totalClass = document.querySelectorAll(".total-id-"+ promo.id);
        if(totalClass.length > 1){
          totalClass[0].remove();
        }
        if(totalHeadClass.length > 1){
          totalHeadClass[0].remove();
        }
      });
    }, err => {
      console.log(err);
    });
  }
  
  getListCustomer() {
    this.dtOptions = {
      serverSide: true,
      processing: true,
      ordering: false,
      pageLength: 25,
      ajax: (dtParams: any, callback) => {
        const params = {
          ...this.filter,
          per_page: dtParams.length,
          page: (dtParams.start / dtParams.length) + 1,
        };
  
        this.customerService.getCustomers(params).subscribe((res: any) => {
          const { list, meta } = res.data;
  
          let number = dtParams.start + 1;
          list.forEach(val => (val.no = number++));
          this.listCustomers = list;
          
  
          callback({
            recordsTotal: meta.total,
            recordsFiltered: meta.total,
            data: [],
          });
  
        }, (err: any) => {
  
        });
      },
    };
  }

  getPromo(){
    this.promoService.getPromo({status: this.PROMO_DISCOUNT}).subscribe((res: any) => {
      this.promo = res.data.list;
    }, err => {
      console.log(err);
    })
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

  filterByCustomer(customers) {
    let customersName = [];
    customers.forEach(val => (customersName.push(val.name)));
    if (!customersName) return false;
  
    this.filter.name = customersName.join(',');

    this.reloadDataTable();
  }

  isPromoActive(customerId: number, promoId: number): boolean {
    const matchingDiscount = this.listDiscount.find(discount =>
        discount.customer_id === customerId && discount.promo_id === promoId);

    return matchingDiscount ? matchingDiscount.is_status === 1 : false;
  }

  onCheckboxChange(event: any, customerId: number, promoId: number) {
    const matchingDiscount = this.listDiscount.find(discount =>
        discount.customer_id === customerId && discount.promo_id === promoId);
    if (matchingDiscount) {
        this.onChangeEvent(event, matchingDiscount.id, customerId, promoId, this.MODE_UPDATE);
    } else {
        this.onChangeEvent(event, '', customerId, promoId, this.MODE_CREATE);
    }
  }

  onChangeEvent($event, id, customer_id, promo_id, action){
    if(action == this.MODE_UPDATE){
      this.payload = {
        id: id,
        customer_id: customer_id,
        promo_id: promo_id,
        is_status: $event.target.checked ? 1:0,
      };

      this.discountService.updateDiscount(this.payload).subscribe((res: any) => {
        this.landaService.alertSuccess('Berhasil', res.message);
        this.afterSave.emit();
        this.getDiscount();
      }, err =>{
        this.landaService.alertError('Mohon Maaf', err.error.errors);
      })
    }else if(action == this.MODE_CREATE){
      this.payload = {
        id:'',
        customer_id: customer_id,
        promo_id: promo_id,
        is_status: $event.target.checked ? 1:0,
      }

      this.discountService.createDiscount(this.payload).subscribe((res: any) => {
        this.landaService.alertSuccess('Berhasil', res.message);
        this.afterSave.emit();
        this.getDiscount();
      }, err =>{
        this.landaService.alertError('Mohon Maaf', err.error.errors);
      })
    }
  }

  reloadDataTable(): void {
    this.dtElement.dtInstance.then((dtInstance: DataTables.Api) => {
      dtInstance.draw();
    });
  }

  formCreate() {
    this.showForm = true;
    this.titleModal = 'Tambah Discount';
    this.discountId = 0;
  }

  formUpdate(customer, modalId) {
    this.showForm= true;
    this.titleModal = 'Edit Customer: ' + customer.name;
    this.customerId = customer.id;
    this.modalService.open(modalId, { size: 'lg', backdrop: 'static' });
  }

}
