import { Component } from '@angular/core';
import { LoaderComponent } from '@shared/components/loader/loader.component';

const CUSTOM_IMPORTS = [LoaderComponent];

@Component({
  selector: 'app-verificar-identidad',
  standalone: true,
  imports: [CUSTOM_IMPORTS],
  templateUrl: './verificar-identidad.component.html',
  styleUrl: './verificar-identidad.component.css'
})
export class VerificarIdentidadComponent {

}
