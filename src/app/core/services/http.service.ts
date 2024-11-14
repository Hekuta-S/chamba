import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { IhttpOptions, ImodelResponseInterface } from '@core/interfaces';
import { catchError, Observable, throwError } from 'rxjs';
import { ModalsService } from './modals.service';

import { environment } from '../../environments/environment';
import { DataService } from './data.service';

@Injectable({
  providedIn: 'root'
})
export class HttpService {

  private BASE_URL = environment.dominio;

  private PATH = environment.path;
  
  private HTTP_OPTIONS: IhttpOptions = {
    "Cache-Control": "max-age=31536000",
    "Content-Type": "application/json"
  };

  constructor(private httpClient: HttpClient, private modal: ModalsService, private dataShared: DataService) { }

  post(method: string, params: any): Observable<ImodelResponseInterface> {
    const url = this.BASE_URL + this.PATH;

    const data = {
      data : params
    }

    const headers = this.headers();

    const payload = {
      Metodo: method,
      Params: data,
      Headers: headers
    };

    payload.Headers.push();

    const payloadEncript = btoa(encodeURIComponent(JSON.stringify(payload)));

    params = payloadEncript;

    return this.httpClient.post<ImodelResponseInterface>(url, params).pipe(
      catchError((error: any) => {
        this.modal.openModalGeneral({body: error.response, titulo: "Error", status: "ERROR" });
        return throwError(() => error);
      })   
    );
  }

  put(method: string, params: any): Observable<ImodelResponseInterface> {
    const url = this.BASE_URL + this.PATH + method;
    return this.httpClient.put<ImodelResponseInterface>(url, params).pipe(
      catchError((error: any) => {
        this.modal.openModalGeneral({body: error.response, titulo: "Error", status: "ERROR", });
        return throwError(() => error);
      })
    );
  }

  get(method: string): Observable<ImodelResponseInterface> {
    const payloadEncript = btoa(method);
    const url = this.BASE_URL + this.PATH + '?' + payloadEncript;
    return this.httpClient.get<ImodelResponseInterface>(url, {headers:this.HTTP_OPTIONS}).pipe(
      catchError((error: any) => {
        this.modal.openModalGeneral({body: error.response, titulo: "Error", status: "ERROR", });
        return throwError(() => error);
      })
    );
  }

  delete(method: string, id: string): Observable<ImodelResponseInterface> {
    const url = `${this.BASE_URL}${this.PATH}${method}/${id}`;
    return this.httpClient.delete<ImodelResponseInterface>(url, {headers:this.HTTP_OPTIONS}).pipe(
      catchError((error: any) => {
        this.modal.openModalGeneral({body: error.response, titulo: "Error", status: "ERROR", });
        return throwError(() => error);
      })
    );
  }

  private headers(): string[] {
    const headers = [
      'Cache-Control:max-age=31536000',
      'X-MC-SO: WEB', 
      'X-MC-ENCBODY: 1', 
      'X-MC-MAIL: ',
      'X-MC-LINE: 0', 
      'X-MC-LOB: 0'
    ];
    this.dataShared.getData$.subscribe(data => {
      if(data){
        const token = data.token ?? '';
        headers.push('X-SESSION-ID:' + token)
      }
    });
    return headers;
  }
}
