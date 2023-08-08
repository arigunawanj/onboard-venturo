import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TestDirectiveComponent } from './components/test-directive/test-directive.component';
import { FormsModule } from '@angular/forms';
import { ItemDetailComponent } from 'src/app/item-detail/item-detail.component';
import { ItemSwitchComponents } from 'src/app/item-switch.component';



@NgModule({
  declarations: [
    TestDirectiveComponent,
    ItemDetailComponent,
    ItemSwitchComponents
  ],
  imports: [
    CommonModule,
    FormsModule
  ]
})
export class TestModule { }
