import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class Api {
  private url = 'http://localhost:8000/api';

  constructor(private http: HttpClient) {}

  getStats(): Observable<any>{
    return this.http.get(`${this.url}/stats`);
  }

  getList(): Observable<any>{
    return this.http.get(`${this.url}/list`);
  }

  

}
