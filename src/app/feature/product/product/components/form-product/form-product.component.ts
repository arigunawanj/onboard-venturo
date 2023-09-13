import {
  Component,
  EventEmitter,
  Input,
  Output,
  SimpleChange,
} from "@angular/core";
import { LandaService } from "src/app/core/services/landa.service";
import { ProductService } from "../../services/product.service";
import * as ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import { CategoryService } from "../../../category/services/category.service";

@Component({
  selector: "app-form-product",
  templateUrl: "./form-product.component.html",
  styleUrls: ["./form-product.component.scss"],
})
export class FormProductComponent {
  readonly DEFAULT_TYPE = 'Toping';
  readonly MODE_CREATE = 'add';
  readonly MODE_UPDATE = 'update';

  @Input() productId: number;
  @Output() afterSave = new EventEmitter<boolean>();

  configEditor = ClassicEditor;
  activeMode: string;
  categories: [];
  allCategories=[];
  showLoading: boolean;
  formModel: {
    id: string,
    name: string,
    product_category_id: number,
    price: string,
    description: string,
    photo: string,
    photo_url: string,
    is_available: number,
    details: any,
    details_deleted: any,
  }

  constructor(
    private productService: ProductService,
    private categoriService: CategoryService,
    private landaService: LandaService,
  ) { }

  ngOnInit(): void {}


  getAllCategories(){
    this.productService.getCategory().subscribe((res:any)=>{
      this.allCategories= res.data.list;
    }, err=>{
      console.log(err);
    });
  }

  ngOnChanges(changes: SimpleChange) {
    this.resetForm();
  }

  getCategories(name = '') {
    this.showLoading = true;
    this.categoriService.getCategories({ name: name }).subscribe((res: any) => {
      this.categories = res.data.list;
      this.showLoading = false;
    }, err => {
      console.log(err);
    });
  }

  getCroppedImage($event) {
    this.formModel.photo = $event;
  }

  resetForm() {
    this.getCategories();
    this.getAllCategories();
    this.formModel = {
      id: '',
      name: '',
      product_category_id: 0,
      price: '',
      description: '',
      photo: '',
      photo_url: '',
      is_available: null,
      details: [],
      details_deleted: [],
    }

    if (this.productId != 0) {
      this.activeMode = this.MODE_UPDATE;
      this.getProduct(this.productId);
      return true;
    }

    this.activeMode = this.MODE_CREATE;
  }

  getProduct(productId) {
    this.productService.getProductById(productId).subscribe((res: any) => {
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
    this.productService.createProduct(this.formModel).subscribe((res: any) => {
      this.landaService.alertSuccess('Berhasil', res.message);
      this.afterSave.emit();
    }, err => {
      this.landaService.alertError('Mohon Maaf', err.error);
    });
  }

  update() {
    this.productService.updateProduct(this.formModel).subscribe((res: any) => {
      this.landaService.alertSuccess('Berhasil', res.message);
      this.afterSave.emit();
    }, err => {
      this.landaService.alertError('Mohon Maaf', err.error.errors);
    });
  }

  addDetail() {
    let val = {
      is_added: true,
      description: '',
      type: '',
      price: 0,
      m_product_id:''
    }
    this.formModel.details.push(val);
  }

  removeDetail(details, paramIndex) {
    details.splice(paramIndex, 1);
    if (details[paramIndex]?.id) {
      this.formModel.details_deleted.push(details[paramIndex]);
    }
  }

  changeDetail(details) {
    if (details?.id) {
      details.is_updated = true;
    }
  }


}