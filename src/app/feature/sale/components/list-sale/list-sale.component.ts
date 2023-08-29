import { Component, OnInit } from '@angular/core';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { CustomerService } from 'src/app/feature/customer/services/customer.service';
import { ProductService } from 'src/app/feature/product/product/services/product.service';
import { DiskonService } from 'src/app/feature/promo/diskon/services/diskon.service';
import { PromoService } from 'src/app/feature/promo/services/promo.service';
import { VoucherService } from 'src/app/feature/promo/voucher/services/voucher.service';


@Component({
  selector: 'app-list-sale',
  templateUrl: './list-sale.component.html',
  styleUrls: ['./list-sale.component.scss']
})
export class ListSaleComponent implements OnInit {
  page: number;
  pageSize: number;
  collectionSize: number;
  discount: any;
  titleModal: string;
  voucher: any;
  customers: any;
  productId: number;
  products: any;
  showLoading: boolean;
  chosenMenu: any = [];
  chosenCustomer: any;
  subtotal: number = 0;
  tax: number = 0;
  changeFlag: boolean
  menu: any;
  filter: {
    customer_id: any,
    is_activate: number
  };
  filter_product: {
    name: any,
    is_available: '1',
  }


  constructor(
    private customerService: CustomerService,
    private productService: ProductService,
    private promoService: PromoService,
    private voucherService: VoucherService,
    private discountService: DiskonService,
    private modalService: NgbModal
  ) { }

  ngOnInit(): void {
    this.setDefaultFilter();
    this.getProducts();
    this.getCustomers();
    // this.refreshCountries();
  }

  filterByProducts(product) {
    let productName = [];
    product.forEach(val => (productName.push(val.name)));
    if (!productName) return false;

    this.filter_product.name = productName.join(',');
    // this.getProducts();
  }

  getProducts() {
    this.productService.getProducts(this.filter_product).subscribe((res: any) => {
      res.data.list.forEach(product => {
        product.isChoices = false;
      });
      this.products = res.data.list;
      this.collectionSize = res.data.list.length;
      this.page = 1;
      this.pageSize = 15;
      this.refreshCountries();
    }, (err: any) => {
      console.log(err);
    });
  }

  setDefaultFilter() {
    this.filter = {
      customer_id: null,
      is_activate: 0
    }

    this.filter_product = {
      name: '',
      is_available: '1',
    }
  }

  dataCustomer: any;
  addCustomer() {
    this.filter.customer_id = this.chosenCustomer;
    this.filter.is_activate = 1;
    this.getVoucher();
    this.getDiscount();

    this.customerService.getCustomerById(this.chosenCustomer).subscribe((res: any) => {
      this.dataCustomer = res.data;
    })
  }

  addMenu(product) {
    product.total_item = 1;
    this.chosenMenu.push(product);
    product.isChoices = true;
  }

  removeMenu(product) {
    product.total_item = 0;
    product.isChoices = false;
  }

  returnMenu(product) {
    product.total_item = 0;
    const index = this.chosenMenu.indexOf(product);
    if (index !== -1) {
      this.chosenMenu.splice(index, 1);
    }
    product.isChoices = false;
  }

  getVoucher() {
    const params = {
      ...this.filter
    }
    this.voucherService.getVoucher(params).subscribe((res: any) => {
      const { list, meta } = res.data;
      let bestVoucher = list[0];
      for (let i = 0; i < list.length; i++) {
        if (list[i].nominal_rupiah > bestVoucher.nominal_rupiah) {
          bestVoucher = list[i];
        }
      }
      this.voucher = bestVoucher;
      if (this.voucher == undefined) {
        this.voucher = {
          id: null,
          nominal_rupiah: 0,
          promo_name: null
        };
      }
    })
  }

  getDiscount() {
    const params = {
      ...this.filter
    }
    this.discountService.getDiscount(params).subscribe((res: any) => {
      const { list, meta } = res.data;
      let bestDiscount = list[0];
      for (let i = 0; i < list.length; i++) {
        if (list[i].nominal_percentage > bestDiscount.nominal_percentage) {
          bestDiscount = list[i];
        }
      }
      if (bestDiscount == undefined) {
        this.discount = {
          id: null,
          nominal_percentage: 0,
          promo_name: null
        };
      } else {
        this.getPromo(bestDiscount);
      }
    })
  }

  getPromo(discount) {
    this.promoService.getPromoById(discount.promo_id).subscribe(
      (res: any) => {
        this.discount = res.data;
      },
      (err) => {
        console.log(err);
      }
    );
  }

  productsItems: any;
  refreshCountries() {
    this.productsItems = this.products.map((product, i) => ({ id: i + 1, ...product })).slice(
      (this.page - 1) * this.pageSize,
      (this.page - 1) * this.pageSize + this.pageSize,
    );
  }

  getCustomers(name = '') {
    this.showLoading = true;
    this.customerService.getCustomers({ name: name }).subscribe((res: any) => {
      this.customers = res.data.list;
      this.showLoading = false;
    }, err => {
      console.log(err);
    });
  }

  updateProduct(modalId, product) {
    this.titleModal = 'Edit Menu: ' + product.name;
    this.productId = product.id
    this.modalService.open(modalId, { size: 'xl', backdrop: 'static' });
  }

  resetPage() {
    this.setDefaultFilter();
    this.chosenMenu = [];
    this.chosenCustomer = 0;
    this.dataCustomer = null;
    this.ngOnInit();
  }

}
