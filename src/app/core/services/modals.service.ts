import { Injectable } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { IAlert } from '@core/interfaces';
import { GeneralComponent } from '@shared/components/modals/general/general.component';
import { VerificarIdentidadComponent } from '@shared/components/modals/verificar-identidad/verificar-identidad.component';


@Injectable({
  providedIn: 'root'
})
export class ModalsService {

  constructor(private dialog: MatDialog) {}

  openModalGeneral(data: IAlert) {
    if(!data.textButtonDefault){
      data.textButtonDefault = 'Aceptar'
    }
    const dialogRef = this.dialog.open(GeneralComponent, {
      width: '450px',
      panelClass:"custom-alert",
      data
    });

    return dialogRef.afterClosed();
  }

  closeModalGeneral() {
    this.dialog.closeAll();
  }

  openValidarIdentidad(){
    const dialogRef = this.dialog.open(VerificarIdentidadComponent, {
      width: '850px',
      panelClass:"custom-alert"
    });

    return dialogRef.afterClosed();
  }
}
