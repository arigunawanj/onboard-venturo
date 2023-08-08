import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { UserRoutingModule } from './user-routing.module';
import { FormsModule } from '@angular/forms';
import { FormUserComponent } from './components/form-user/form-user.component';
import { ListUserComponent } from './components/list-user/list-user.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';


@NgModule({
  declarations: [ 
    FormUserComponent, ListUserComponent
  ],
  imports: [
    CommonModule,
    UserRoutingModule,
    FormsModule,
    NgbModule
  ]
})
export class UserModule { }
