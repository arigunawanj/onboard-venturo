import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { DataTablesModule } from 'angular-datatables';
import { NgSelectModule } from '@ng-select/ng-select';
import { SharedModule } from 'src/app/shared/shared.module';
import { CoreModule } from 'src/app/core/core.module';
import { CKEditorModule } from '@ckeditor/ckeditor5-angular';


import { CustomerModule } from '../customer/customer.module';
import { ListPromoComponent } from './components/list-promo/list-promo.component';
import { FormPromoComponent } from './components/form-promo/form-promo.component';
import { ListVoucherComponent } from './voucher/components/list-voucher/list-voucher.component';
import { FormVoucherComponent } from './voucher/components/form-voucher/form-voucher.component';
import { FormDiskonComponent } from './diskon/components/form-diskon/form-diskon.component';
import { ListDiskonComponent } from './diskon/components/list-diskon/list-diskon.component';

@NgModule({
  declarations: [ListPromoComponent, FormPromoComponent, ListVoucherComponent, FormVoucherComponent, FormDiskonComponent, ListDiskonComponent],
  imports: [
    CommonModule,
    FormsModule,
    NgbModule,
    DataTablesModule,
    NgSelectModule,
    SharedModule,
    CoreModule,
    CKEditorModule,
    CustomerModule,
    
  ]
})
export class PromoModule {}
