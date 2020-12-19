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
  client = new BehaviorSubject({});
  msg = new BehaviorSubject('');
 
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
          this.client.next({
            'dni':res['data'].client.dni,
            'session': res['data'].sessions
          });
          this.router.navigate(['/appointment']);
        }
      }, data => {
        console.log(data);
      });
  }

  getSessionsSchedule(date) {
    return this.http.get(`${this.url}/api/v1/schedule/?date=${date}`);
  }

  storeSession(session) {
    return this.http.post(`${this.url}/api/v1/clients/session`, session).subscribe( 
      res => {
        if (res) {
          this.msg.next(`Su cita fue creada con exito para ${session['date']} de ${session['schedule']}`);
        }
      }, data => {
        this.msg.next(`Error volver a cargar la pagina`);
          console.log(data);
      });
  }
}
