import { Component, OnInit, ViewChild } from '@angular/core';
import { DataTableDirective } from 'angular-datatables';
import Swal from 'sweetalert2';
import { PromoService } from '../../services/promo.service';

@Component({
  selector: 'app-list-promo',
  templateUrl: './list-promo.component.html',
  styleUrls: ['./list-promo.component.scss']
})
export class ListPromoComponent implements OnInit {

  @ViewChild(DataTableDirective)
  dtElement: DataTableDirective;
  dtInstance: Promise<DataTables.Api>;
  dtOptions: any;

  showLoading: boolean;
  listPromo: any;
  titleForm: string;
  promoId: number;
  showForm: boolean;
  photo_url:string;
  photo:string;
  filter: {
    name: '',
  };

  constructor(
    private promoService: PromoService,
  ) { }

  ngOnInit(): void {
    this.showForm = false;
    this.setDefaultFilter();
    this.getPromo();
  }

  setDefaultFilter() {
    this.filter = {
      name: '',
    }
  }

  getPromo() {
    this.dtOptions = {
      serverSide: true,
      processing: true,
      ordering: false,
      pageLength: 25,
      ajax: (dtParams: any, callback) => {
        const params = {
          ...this.filter,
          per_page: dtParams.length,
          page: (dtParams.start / dtParams.length) + 1,
        };

        this.promoService.getPromo(params).subscribe((res: any) => {
          const { list, meta } = res.data;

          let number = dtParams.start + 1;
          list.forEach(val => (val.no = number++));
          this.listPromo = list;

          callback({
            recordsTotal: meta.total,
            recordsFiltered: meta.total,
            data: [],
          });

        }, (err: any) => {

        });
      },
    };
  }

  reloadDataTable(): void {
    this.dtElement.dtInstance.then((dtInstance: DataTables.Api) => {
      dtInstance.draw();
    });
  }

  formCreate() {
    this.showForm = true;
    this.titleForm = 'Tambah Promo';
    this.promoId = 0;
  }

  formUpdate(promo) {
    this.showForm = true;
    this.titleForm = 'Edit Promo: ' + promo.name;
    this.promoId = promo.id;
  }

  deletePromo(promoId) {
    Swal.fire({
      title: 'Apakah kamu yakin ?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#34c38f',
      cancelButtonColor: '#f46a6a',
      confirmButtonText: 'Ya, Hapus data ini !',
    }).then((result) => {
      if (!result.value) return false;

      this.promoService.deletePromo(promoId).subscribe((res: any) => {
        this.reloadDataTable();
      });
    });
  }

}
