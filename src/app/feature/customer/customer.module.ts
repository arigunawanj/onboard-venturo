import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { CustomerRoutingModule } from './customer-routing.module';
import { FormCustomerComponent } from './components/form-customer/form-customer.component';
import { ListCustomerComponent } from './components/list-customer/list-customer.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { FormsModule } from '@angular/forms';
import { DataTablesModule } from 'angular-datatables';
import { SharedModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    FormCustomerComponent,
    ListCustomerComponent
  ],
  imports: [
    CommonModule,
    CustomerRoutingModule,
    NgbModule,
    FormsModule,
    DataTablesModule,
    SharedModule
  ]
})
export class CustomerModule { }
