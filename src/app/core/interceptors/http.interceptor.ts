import { HttpInterceptorFn, HttpRequest, HttpEvent, HttpResponse, HttpHeaders } from '@angular/common/http';
import { HttpErrorResponse } from '@angular/common/http';
import { inject } from '@angular/core';
import { Router } from '@angular/router';

import { throwError, catchError, finalize, map } from 'rxjs';

import { UtilsService } from '@core/services/utils.service';
import { ImodelResponseInterface } from '@core/interfaces';


export const httpInterceptor: HttpInterceptorFn = (req: HttpRequest<any>, next) => {
  let modifiedReq = req.clone();

  return next(modifiedReq).pipe(
    map((event: HttpEvent<any>) => {
      if (event instanceof HttpResponse) {
        return handleResponse(event);
      }
      return event;
    }),
    catchError((error: HttpErrorResponse) => {
      return handleHttpError(error);
    })
  );
};

export const loadingSpinnerInterceptorFunctional: HttpInterceptorFn = (req, next) => {
  const loadingService = inject(UtilsService);
  loadingService.showSpinner();

  return next(req).pipe(
    finalize(() => {
      loadingService.hideSpinner();
    })
  );
};

const handleResponse = (response: HttpResponse<any>): HttpResponse<any> => {
  const body = response.body as ImodelResponseInterface;

  if (body && body.error && body.response) {
    if(body.error == "1"){
      throw new HttpErrorResponse({
        error: body,
        status: 200,
        statusText: body.response
      });
    }
  }
  return response.clone(body.response);
};

const handleHttpError = (error: HttpErrorResponse) => {
  let errorMsg = 'Error en la conexión con el servidor';

  if (error.error && error.error.error && error.error.response) {
    errorMsg = error.error.response || 'Error en la conexión con el servidor'
  }

  return throwError(() => ({
    error: 1,
    response: errorMsg
  }));
};