import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';

import { FormUserComponent } from './components/form-user/form-user.component';
import { ListUserComponent } from './components/list-user/list-user.component';
import { DataTablesModule } from 'angular-datatables';
import { SharedModule } from 'src/app/shared/shared.module';

import { UserRoutingModule } from './user-routing.module';


@NgModule({
  declarations: [ 
    FormUserComponent, ListUserComponent
  ],
  imports: [
    CommonModule,
    FormsModule,
    DataTablesModule,
    NgbModule,
    UserRoutingModule,
    SharedModule,
  ]
})
export class UserModule { }
