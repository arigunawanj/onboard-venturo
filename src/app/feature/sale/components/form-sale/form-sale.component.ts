import {
  Component,
  EventEmitter,
  Input,
  OnInit,
  Output,
  SimpleChange,
} from "@angular/core";
import { NgbModal } from "@ng-bootstrap/ng-bootstrap";
import { LandaService } from "src/app/core/services/landa.service";
import { SaleService } from "../../services/sale.service";
import { CustomerService } from "src/app/feature/customer/services/customer.service";
import * as moment from "moment";

@Component({
  selector: "app-form-sale",
  templateUrl: "./form-sale.component.html",
  styleUrls: ["./form-sale.component.scss"],
})
export class FormSaleComponent implements OnInit {
  readonly dateFormat = 'yyyy-MM-DD hh:mm:ss'

  @Input() customer: any;
  @Input() menu: any;
  @Input() voucher: any;
  @Input() discount: any;
  @Input() callbackFunction: ((product: any) => void)
  @Output() afterSave = new EventEmitter<boolean>();

  customer_data: any;
  titleModal: any;
  customerId: number;
  formModel: {
    customer_id: number,
    voucher_id: number,
    discount_id: number,
    voucher_nominal: number,
    date: string,
    details: any
  }
  subtotal: number;
  tax: number;

  constructor(
    private saleService: SaleService,
    private landaService: LandaService,
    private modalService: NgbModal
  ) { }

  ngOnInit(): void {
    this.setDeafultVoucher();
    this.setDefaultDiscount();
  }

  ngOnChanges(simple: SimpleChange) {
  }

  updateCustomer(modalId) {
    this.titleModal = 'Edit Customer: ' + this.customer.name;
    this.customerId = this.customer.id
    this.modalService.open(modalId, { size: 'lg', backdrop: 'static' });
  }

  setDeafultVoucher() {
    this.voucher = {
      id: null,
      nominal_rupiah: 0,
      promo_name: null
    }
  }

  setDefaultDiscount() {
    this.discount = {
      id: null,
      nominal_percentage: 0,
      promo_name: null
    }
  }

  penguranganItem(menu, index) {
    menu.total_item--;
    if (menu.total_item == 0) {
      this.menu.splice(index, 1);
      this.callbackFunction(menu);
    }
  }

  penambahanItem(menu) {
    menu.total_item++;
  }

  isiForm() {
    this.formModel = {
      customer_id: this.customer.id,
      voucher_id: this.voucher.id,
      discount_id: this.discount.id,
      voucher_nominal: this.voucher.nominal_rupiah,
      date: moment().format(this.dateFormat),
      details: this.menu.map(menu => {
        return {
          is_added: true,
          product_id: menu.id,
          product_detail_id: menu.details.length == 0 ? null : menu.details[0].id,
          total_item: menu.total_item,
          price: menu.price,
          discount_nominal: menu.price * menu.total_item * this.discount.nominal_percentage / 100
        }
      })
    }

    if (this.menu.length === 0) {
      this.landaService.alertError('Mohon Maaf', 'Pilih Menu Terlebih Dahulu');
      return;
    }

    let menuTotal = 0;

    this.menu.map(menu => {
      menuTotal += (menu.price * menu.total_item)
    })

    let finalTax = (menuTotal * 111 / 100) - this.voucher.nominal_rupiah;
    console.log(finalTax);
    if (finalTax < 0) {
      this.landaService.alertError('Mohon Maaf', 'Harga Minus. Silahkan cari voucher lain');
    } else {
      this.saleService.createSale(this.formModel).subscribe((res: any) => {
        this.landaService.alertSuccess('Berhasil', res.message);
        this.resetForm();
        this.afterSave.emit();
      }, err => {
        this.landaService.alertError('Mohon Maaf', err.error.errors);
      });

    }

  }

  subtotalFunction(listMenu) {
    let tempSubtotal = 0;
    listMenu.forEach(val => {
      tempSubtotal = tempSubtotal + (val.price * val.total_item)
    });
    return tempSubtotal;
  }

  taxFunction(listMenu) {
    return this.subtotalFunction(listMenu) * 11 / 100;
  }

  discountNominal(listMenu) {
    if (this.discount != null) {
      // console.log('tes'+this.discount.nominal_percentage)
      return this.subtotalFunction(listMenu) * this.discount.nominal_percentage / 100;
    }
    return 0;
  }

  resetForm() {
    this.menu = [];
    this.customer = [];
    this.tax = 0;
    this.subtotal = 0;
    this.ngOnInit();
  }
}
