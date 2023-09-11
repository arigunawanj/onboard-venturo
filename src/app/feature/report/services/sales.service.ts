import { Injectable } from "@angular/core";
import { LandaService } from "src/app/core/services/landa.service";

@Injectable({
  providedIn: "root",
})
export class SalesService {
  constructor(private landaService: LandaService) {}

  getSalesPromo(arrParameter = {}) {
    return this.landaService.DataGet("/v1/report/sales-promo", arrParameter);
  }

  getSalesTransaction(arrParameter = {}) {
    return this.landaService.DataGet('/v1/sale-transaction', arrParameter);
  }

  getReportPDF(arrParameter = {}) {
    return this.landaService.DataDownload("/v1/download/sale-report-pdf", arrParameter);
  }

  getReportCSV(arrParameter = {}) {
    return this.landaService.DataDownload("/v1/download/sale-report-csv", arrParameter);
  }

  getSalesMenu(arrParameter = {}) {
    return this.landaService.DataGet('/v1/report/sales-menu', arrParameter);
  }
  
}
