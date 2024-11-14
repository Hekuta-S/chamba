import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { NgxSpinnerModule } from "ngx-spinner";

const ANGULAR_IMPORTS = [RouterOutlet];
const PLUGIN_MODULES = [NgxSpinnerModule];
@Component({
  selector: 'app-root',
  standalone: true,
  imports: [PLUGIN_MODULES, ANGULAR_IMPORTS],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent {
  title = 'registro-ppt';
}
