import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';

interface IDataShared {
  token:string
}

@Injectable({
  providedIn: 'root'
})
export class DataService {
  private data = new BehaviorSubject<any>(null);
  getData$ = this.data.asObservable();
  
  constructor() { }

  setData(data: any) {
    this.data.next(data)
  }
}
