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
export class FormCustomerComponent implements OnInit{
  constructor(
    private customerService: CustomerService,
    private landaService: LandaService
  ) { }

  ngOnInit(): void {
  }

  @Input() customerId: number;
  @Output() afterSave = new EventEmitter<boolean>();
  readonly MODE_CREATE = 'add';
  readonly MODE_UPDATE = 'update';

  activeMode: string;

  formModel: {
    id: number,
    name: string,
    email: string,
    date_of_birth: string,
    phone_number: string,
    is_verified: number,
    photo: string,
    photo_url: string
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

  getCroppedImage($event) {
    this.formModel.photo = $event;
  }

  ngOnChanges(changes: SimpleChange) {
    this.resetForm();
  }

  resetForm() {
    this.formModel = {
      id: 0,
      name: '',
      email: '',
      date_of_birth: '',
      phone_number: '',
      is_verified: null,
      photo: '',
      photo_url: '',
    }

    if (this.customerId != 0) {
      this.activeMode = this.MODE_UPDATE;
      this.getCustomer(this.customerId);
      return true;
    }

    this.activeMode = this.MODE_CREATE;
  }

}
