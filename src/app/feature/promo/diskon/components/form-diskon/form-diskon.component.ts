import {
  Component,
  EventEmitter,
  Input,
  OnInit,
  Output,
  SimpleChange,
} from "@angular/core";
import { NgbModal } from "@ng-bootstrap/ng-bootstrap";
import { CustomerService } from "src/app/feature/customer/services/customer.service";
import { LandaService } from "src/app/core/services/landa.service";
import { DiskonService } from "../../services/diskon.service";
import { PromoService } from "../../../services/promo.service";

@Component({
  selector: 'app-form-diskon',
  templateUrl: './form-diskon.component.html',
  styleUrls: ['./form-diskon.component.scss']
})
export class FormDiskonComponent {
  readonly PROMO_DISCOUNT = "diskon";
  readonly MODE_CREATE = "add";
  readonly MODE_UPDATE = "update";

  @Input() discountId: number;
  @Output() afterSave = new EventEmitter<boolean>();

  activeMode: string;
  customers: [];
  promo = [];
  filterDiskon = [];
  showLoading: boolean;
  formModel: {
    id: string;
    customer_id: string;
    promo_id: string;
    // status: any,
    photo: string;
    photo_url: string;
    nominal_percentage: number;
  };

  constructor(
    private modalService: NgbModal,
    private discountService: DiskonService,
    private customerService: CustomerService,
    private promoService: PromoService,
    private landaService: LandaService
  ) {}

  ngOnInit(): void {}

  createCustomer(modalId) {
    this.modalService.open(modalId, { size: "lg", backdrop: "static" });
  }

  ngOnChanges(changes: SimpleChange) {
    this.resetForm();
  }

  getCroppedImage($event) {
    this.formModel.photo = $event;
  }

  resetForm() {
    this.getPromo();
    this.getCustomers();
    this.formModel = {
      id: "",
      customer_id: "",
      promo_id: "",
      // status:[],
      photo: "",
      photo_url: "",
      nominal_percentage: 0,
    };

    if (this.discountId != 0) {
      this.activeMode = this.MODE_UPDATE;
      this.getDiscountById(this.discountId);
      return true;
    }
    this.activeMode = this.MODE_CREATE;
  }

  getCustomers(name = "") {
    this.showLoading = true;
    this.customerService.getCustomers({ name: name }).subscribe(
      (res: any) => {
        this.customers = res.data.list;
        // console.log(this.customers)
        this.showLoading = false;
      },
      (err) => {
        console.log(err);
      }
    );
  }

  getPromo(name = "") {
    this.showLoading = true;
    this.promoService
      .getPromo({ name: name, status: this.PROMO_DISCOUNT })
      .subscribe(
        (res: any) => {
          res.data.list.forEach((val) => {
            console.log(val.photo_url);
            if (val.status === "voucher") {
              return;
            }
            this.promo.push(val);
          });
          this.filterDiskon = this.promo;
          console.log(this.filterDiskon);
          this.showLoading = false;
        },
        (err) => {
          console.log(err);
        }
      );
  }

  setSelectedPromo($event) {
    this.formModel.nominal_percentage = $event.nominal_percentage;
    this.formModel.photo_url = $event.photo_url;
    this.formModel.photo = $event.photo;
    console.log(this.formModel.nominal_percentage);
  }

  getDiscountById(discountId) {
    this.discountService.getDiscountById(discountId).subscribe(
      (res: any) => {
        this.formModel = res.data;
        this.setSelectedPromo(this.formModel);
      },
      (err) => {
        console.log(err);
      }
    );
  }

  save() {
    switch (this.activeMode) {
      case this.MODE_CREATE:
        this.insert();
        break;
      case this.MODE_UPDATE:
        this.update();
        break;
    }
  }

  insert() {
    this.discountService.createDiscount(this.formModel).subscribe(
      (res: any) => {
        this.landaService.alertSuccess("Berhasil", res.message);
        this.afterSave.emit();
      },
      (err) => {
        this.landaService.alertError("Mohon Maaf", err.error.errors);
      }
    );
  }

  update() {
    this.discountService.updateDiscount(this.formModel).subscribe(
      (res: any) => {
        this.landaService.alertSuccess("Berhasil", res.message);
        this.afterSave.emit();
      },
      (err) => {
        this.landaService.alertError("Mohon Maaf", err.error.errors);
      }
    );
  }
}
