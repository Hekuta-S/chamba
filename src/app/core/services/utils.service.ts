import { Injectable } from '@angular/core';
import { NgxSpinnerService } from "ngx-spinner";

@Injectable({
  providedIn: 'root'
})
export class UtilsService {

  constructor(private spinner: NgxSpinnerService) { }

  showSpinner(){
    this.spinner.show();
  }

  hideSpinner(){
    this.spinner.hide();
  }
}
