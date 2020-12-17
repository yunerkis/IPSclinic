import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { BehaviorSubject} from 'rxjs'; 
import { Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class ClientService {

  url = environment.url;
  dni = new BehaviorSubject('');
  msg = new BehaviorSubject('');
  session = new BehaviorSubject([]);

  constructor(
    private router: Router,
    private http: HttpClient,
  ) { }

  getSessionsClient(dni) {

    return this.http.get(`${this.url}/api/v1/clients/session/${dni['dni']}`).subscribe(
      res => {
        if (res['data'].client == null) {
          this.msg.next('Este usuario no se encuentra disponible, consultar IPS');
        } else {
          this.session.next(res['data'].sessions);
          this.dni.next(res['data'].client.dni);
          this.router.navigate(['/appointment']);
        }
      }, data => {
        console.log(data);
      });
  }
}
