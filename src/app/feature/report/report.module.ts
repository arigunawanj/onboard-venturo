import { NgModule } from "@angular/core";
import { CommonModule } from "@angular/common";
import { FormsModule } from "@angular/forms";
import { NgSelectModule } from "@ng-select/ng-select";
import { SharedModule } from "src/app/shared/shared.module";
import { SalesPromoComponent } from './components/sales-promo/sales-promo.component';
import { NgbModule } from "@ng-bootstrap/ng-bootstrap";
import { CoreModule } from "src/app/core/core.module";
import { SalesTransactionComponent } from './components/sales-transaction/sales-transaction.component';
import { DataTablesModule } from "angular-datatables";

@NgModule({
  declarations: [
    SalesPromoComponent,
    SalesTransactionComponent
  ],
  imports: [
    CommonModule,
    FormsModule,
    NgSelectModule,
    CommonModule,
    SharedModule,
    NgbModule,
    CoreModule,
    NgSelectModule,
    DataTablesModule
  ],
})
export class ReportModule {}
