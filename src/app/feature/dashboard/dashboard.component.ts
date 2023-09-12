import { Component, OnInit } from "@angular/core";
import { DashboardService } from "./services/dashboard.service";

@Component({
  selector: "app-dashboard",
  templateUrl: "./dashboard.component.html",
  styleUrls: ["./dashboard.component.scss"],
})
export class DashboardComponent implements OnInit {
  total: {
    today: 0,
    yesterday: 0,
    last_month: 0,
    this_month: 0,
  }

  public barChartOptions = {
    scaleShowVerticalLines: false,
    responsive: true,
    legend: {
      display: false,
    },
    scales: {
      yAxes: [{
        ticks: {
          callback: function (value) {
            return 'Rp ' + new Intl.NumberFormat('de-DE').format(value)
          }
        }
      }]
    },
    tooltips: {
      callbacks: {
        label: function (tooltipItem) {
          return 'Rp ' + new Intl.NumberFormat('de-DE').format(tooltipItem.yLabel);
        },
        labelColor: function () {
          return {
            borderColor: '#C7E9ED',
            backgroundColor: '#C7E9ED'
          };
        },
        labelTextColor: function () {
          return '#FFF';
        }
      }
    }
  };
  public barChartLabels = [];
  public barChartData = [
    { data: [], label: 'false', backgroundColor: '' },
  ];

  constructor(
    private dashboardService: DashboardService
  ) {
    this.setDefaultTotal();
    this.getSummaries();
    this.getTotalPerYear();
  }

  setDefaultTotal() {
    this.total = {
      today: 0,
      yesterday: 0,
      last_month: 0,
      this_month: 0,
    }
  }

  getSummaries() {
    this.dashboardService.getSummaries().subscribe((resp: any) => {
      this.total = resp.data;
    }, (err: any) => {

    })
  }

  getTotalPerYear() {
    this.dashboardService.getTotalPerYear().subscribe((resp: any) => {
      this.barChartLabels = resp.data.label;
      this.barChartData = [
        { data: resp.data.data, label: 'false', backgroundColor: '#C7E9ED' }
      ]
    }, (err: any) => {

    })
  }

  ngOnInit(): void {
  }

}
