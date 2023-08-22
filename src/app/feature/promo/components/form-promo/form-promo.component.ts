import { Component, OnInit, Input, Output, EventEmitter, SimpleChange } from '@angular/core';
import * as ClassicEditor from '@ckeditor/ckeditor5-build-classic';

import { LandaService } from 'src/app/core/services/landa.service';
import { PromoService } from '../../services/promo.service';

@Component({
  selector: 'app-form-promo',
  templateUrl: './form-promo.component.html',
  styleUrls: ['./form-promo.component.scss']
})
export class FormPromoComponent implements OnInit {

  readonly DEFAULT_STATUS = 'voucher';
  readonly DEFAULT_EXPIRED = 1;
  readonly MODE_CREATE = 'add';
  readonly MODE_UPDATE = 'update';

  @Input() promoId: number;
  @Output() afterSave = new EventEmitter<boolean>();

  configEditor = ClassicEditor;
  activeMode: string;
  categories: [];
  showLoading: boolean;
  formModel: {
    id: number,
    name: string,
    status: string,
    nominal_percentage: number,
    nominal_rupiah: number,
    photo: string,
    photo_url: string,
    expired_in_day: number,
    term_conditions: string
  }

  constructor(
    private promoService: PromoService,
    private landaService: LandaService,
  ) { }

  ngOnInit(): void { }

  ngOnChanges(changes: SimpleChange) {
    this.resetForm();
  }

  getCroppedImage($event) {
    this.formModel.photo = $event;
  }

  resetForm() {
    this.formModel = {
      id: 0,
      name: '',
      status: this.DEFAULT_STATUS,
      nominal_percentage: null,
      nominal_rupiah: null,
      photo: '',
      photo_url: '',
      expired_in_day: this.DEFAULT_EXPIRED,
      term_conditions: ''
    }

    if (this.promoId != 0) {
      this.activeMode = this.MODE_UPDATE;
      this.getPromoById(this.promoId);
      return true;
    }

    this.activeMode = this.MODE_CREATE;
  }

  getPromoById(promoId) {

    this.promoService.getPromoById(promoId).subscribe((res: any) => {
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
    this.promoService.createPromo(this.formModel).subscribe((res: any) => {
      this.landaService.alertSuccess('Berhasil', res.message);
      this.afterSave.emit();
    }, err => {
      this.landaService.alertError('Mohon Maaf', err.error.errors);
    });
  }

  update() {
    this.promoService.updatePromo(this.formModel).subscribe((res: any) => {
      this.landaService.alertSuccess('Berhasil', res.message);
      this.afterSave.emit();
    }, err => {
      this.landaService.alertError('Mohon Maaf', err.error.errors);
    });
  }

}
