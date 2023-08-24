import { Injectable } from '@angular/core';
import { LandaService } from 'src/app/core/services/landa.service';

@Injectable({
  providedIn: 'root'
})
export class DiskonService {

  constructor(private landaService: LandaService) {}

  getDiscount(arrParameter = {}) {
    return this.landaService.DataGet("/v1/discount", arrParameter);
  }
  getDiscountById(id) {
    return this.landaService.DataGet('/v1/discount/' + id);
  }

  createDiscount(payload) {
    return this.landaService.DataPost("/v1/discount", payload);
  }

  updateDiscount(payload) {
    return this.landaService.DataPut("/v1/discount", payload);
  }
}
