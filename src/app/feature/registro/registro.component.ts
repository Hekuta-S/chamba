import { Component, DestroyRef, inject, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AbstractControl, FormsModule, ReactiveFormsModule, FormGroup, FormControl, Validators } from '@angular/forms';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';

import { HeaderComponent } from '@shared/components/header/header.component';
import { confirmPasswordValidator, customPasswordValidator } from '@core/utils/custom-validators';
import { ReqListarCuentasModel } from '@core/models/http';
import { IRegistroForm } from '@core/interfaces';
import { HttpService } from '@core/services/http.service';
import { ModalsService } from '@core/services/modals.service';
import { DataService } from '@core/services/data.service';


const CUSTOM_IMPORTS = [HeaderComponent]
const ANGULAR_MODULES = [CommonModule, FormsModule, ReactiveFormsModule]

@Component({
  selector: 'app-registro',
  standalone: true,
  imports: [CUSTOM_IMPORTS, ANGULAR_MODULES],
  templateUrl: './registro.component.html',
  styleUrl: './registro.component.css'
})
export class RegistroComponent {
  isOpenDropdown = false;
  isDisabledDropDown = true;
  isFormSubmited = false;
  viewPass1:boolean = false;
  viewPass2:boolean = false;

  register: FormGroup = new FormGroup({});

  private readonly _destroyRef = inject(DestroyRef)

  constructor(private http: HttpService, private modal: ModalsService, private dataShared:DataService){
    this.register = new FormGroup({
      tipoDocumento: new FormControl("7", [Validators.required]),
      documento: new FormControl("", [Validators.required]),
      correo: new FormControl("", [Validators.email, Validators.required]),
      contrasena: new FormControl(null, [Validators.required, customPasswordValidator()]),
      confirmContrasena: new FormControl("", [Validators.required]),
      terminos: new FormControl(false, [Validators.required, Validators.requiredTrue]),
    }, { validators : [confirmPasswordValidator()] })
  }

  ngOnInit():void {
    this.reqVersiones();
  }

  reqVersiones(){
    this.http.get('api/index.php/v1/core/movil/VersionesApp.json?version=2.0&plataforma=web').pipe(takeUntilDestroyed(this._destroyRef)).subscribe({
      next: (response) => {
        if(response.error == 0){
          const responseService = response
          const token = responseService.response.token;
          this.dataShared.setData({token});
        }  
      },
      error:(err) =>{
        console.error(err)
      }
    })
  }

  get f(): { [key: string]: AbstractControl } {
    return this.register.controls;
  }

  reqListarCuentas(){
    const dataFormulario = this.register.value as IRegistroForm
    const data = new ReqListarCuentasModel(dataFormulario.documento, dataFormulario.tipoDocumento)
    this.http.post('M3/General/listarCuentas/', data).pipe(takeUntilDestroyed(this._destroyRef)).subscribe({
        next: (res) => {
          const response = res.response
          if(response.infoEvidente && response.infoEvidente.registradoCliente){
            const modal = this.modal.openModalGeneral({titulo:"", body:"Usuario registrado.", status:"SUCCESS", textButtonDefault:'Disfrutar Claro TV+'});
            modal.pipe(takeUntilDestroyed(this._destroyRef)).subscribe(
            (result:string) => {
              if(result == "ok"){
                location.href = "https://www.miclaroapp.com.co/"
              }
            })
            
            return;
          }

          if(response.lista.length === 0){
            this.modal.openModalGeneral({titulo:"", body:"El usuario no tiene cuentas asociadas", status:"SUCCESS", textButtonDefault:'Aceptar'});
            
            return;
          }


          this.modal.openValidarIdentidad();
        },
        error:(err) =>{
          console.error(err)
        }
    })
  }

  onSubmit() {
    this.isFormSubmited = true
    if(this.register.valid){
      this.reqListarCuentas();
    }
  }

  openDropdown(){
    if (this.isDisabledDropDown) {
      return;
    }
    this.isOpenDropdown = !this.isOpenDropdown;
  }

  getTextTypeDocument(type:string){
    let textTypeDocument = "";
    switch (type) {
      case "7":
        textTypeDocument = "Permiso por Protección Temporal"
      break;
      default:
        textTypeDocument = ""
      break;
    }

    return textTypeDocument
  }
}
