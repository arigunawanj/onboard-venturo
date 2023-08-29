import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ListSaleComponent } from './components/list-sale/list-sale.component';
import { FormSaleComponent } from './components/form-sale/form-sale.component';
import { DataTablesModule } from 'angular-datatables';
import { CoreModule } from 'src/app/core/core.module';
import { SharedModule } from 'src/app/shared/shared.module';
import { NgSelectModule } from '@ng-select/ng-select';
import { FormsModule } from '@angular/forms';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { CustomerModule } from '../customer/customer.module';
import { ProductModule } from '../product/product.module';
import { CKEditorModule } from '@ckeditor/ckeditor5-angular';
import { SaleRoutingModule } from './sale-routing.module';



@NgModule({
  declarations: [
    ListSaleComponent,
    FormSaleComponent
  ],
  imports: [
    CommonModule,
    DataTablesModule,
    SharedModule,
    CoreModule,
    CKEditorModule,
    NgSelectModule,
    FormsModule,
    NgbModule,
    CustomerModule,
    SaleRoutingModule,
    ProductModule,

  ]
})
export class SaleModule { }
