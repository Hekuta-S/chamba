import { Component, Inject } from '@angular/core';
import { IAlert } from '@core/interfaces';

import {MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';
import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';

const ANGULAR_MODULES = [CommonModule, HttpClientModule];

@Component({
  selector: 'app-general',
  standalone: true,
  imports: [ANGULAR_MODULES],
  templateUrl: './general.component.html',
  styleUrl: './general.component.css'
})
export class GeneralComponent {
  constructor(public dialogRef: MatDialogRef<GeneralComponent>,@Inject(MAT_DIALOG_DATA) public data:IAlert = {
    titulo: '',
    status: '',
    body: '',
    textButtonDefault: '',
    textButtonSecondary: ''
  }) { }

  primaryButtonEvent(){
    this.dialogRef.close("ok")
  }
}
