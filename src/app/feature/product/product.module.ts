import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ListCategoryComponent } from './category/components/list-category/list-category.component';
import { ListProductComponent } from './product/components/list-product/list-product.component';



@NgModule({
  declarations: [
    ListCategoryComponent,
    ListProductComponent
  ],
  imports: [
    CommonModule
  ]
})
export class ProductModule { }
