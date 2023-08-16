import { ImageCropperModule } from 'ngx-image-cropper';
import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { PageTitleComponent } from './page-title/page-title.component';
import { FormsModule } from '@angular/forms';
import { UploadImageComponent } from './upload-image/upload-image.component';

@NgModule({
  declarations: [PageTitleComponent, UploadImageComponent],
  imports: [
    CommonModule,
    FormsModule,
    ImageCropperModule
  ],
  exports: [
    PageTitleComponent,
    UploadImageComponent
  ]
})
export class SharedModule { }
