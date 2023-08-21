import {
  Component,
  Input,
  OnInit,
  Output,
  EventEmitter,
  SimpleChange,
} from "@angular/core";
import { CustomerService } from '../../services/customer.service';
import { LandaService } from "src/app/core/services/landa.service";

@Component({
  selector: 'app-form-customer',
  templateUrl: './form-customer.component.html',
  styleUrls: ['./form-customer.component.scss']
})
export class FormCustomerComponent {
  readonly MODE_CREATE = "add";
  readonly MODE_UPDATE = "update";

  @Input() customerId: number;
  @Output() afterSave = new EventEmitter<boolean>();

  activeMode: string;
  formModel: {
    id: number;
    name: string;
    email: string;
    phone_number: string;
    photo: string;
    photo_url: string;
  };

  constructor(
    private customerService: CustomerService,
    private landaService: LandaService
  ) {}

  ngOnInit(): void {}

  ngOnChanges(changes: SimpleChange) {
    this.resetForm();
  }

  getCroppedImage($event) {
    this.formModel.photo = $event;
  }

  resetForm() {
    this.formModel = {
      id: 0,
      name: "",
      email: "",
      phone_number: "",
      photo: "",
      photo_url: "",
    };

    if (this.customerId > 0) {
      this.activeMode = this.MODE_UPDATE;
      this.getCustomer(this.customerId);
      return true;
    }

    this.activeMode = this.MODE_CREATE;
  }

  getCustomer(customerId) {
    this.customerService.getCustomerById(customerId).subscribe((res: any) => {
      this.formModel = res.data;
    }, err => {
      console.log(err);
    });
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
    this.customerService.createCustomer(this.formModel).subscribe((res: any) => {
      this.landaService.alertSuccess('Berhasil', res.message);
      this.afterSave.emit();
    }, err => {
      this.landaService.alertError('Mohon Maaf', err.error.errors);
    });
  }


  update() {
    this.customerService.updateCustomer(this.formModel).subscribe((res: any) => {
      this.landaService.alertSuccess('Berhasil', res.message);
      this.afterSave.emit();
    }, err => {
      this.landaService.alertError('Mohon Maaf', err.error.errors);
    });
  }
}
