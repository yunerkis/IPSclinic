import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../environments/environment';
import { BehaviorSubject} from 'rxjs'; 
import { Router } from '@angular/router';
import Swal from 'sweetalert2';

@Injectable({
  providedIn: 'root'
})
export class ClientService {

  url = environment.url;
  client = new BehaviorSubject({});
  msg = new BehaviorSubject('');
  modal = new BehaviorSubject({});

  errors = new BehaviorSubject('');
 
  constructor(
    private router: Router,
    private http: HttpClient,
  ) { }

  getSessionsClient(dni, time) {
    return this.http.get(`${this.url}/api/v1/clients/session/${dni['dni']}?time=${time}`).subscribe(
      res => {
        if (res['data'].client == null) {
          Swal.fire(
            'Error',
            'Este usuario no se encuentra disponible, consultar IPS',
            'error'
          )
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

  getSessionsSchedule(date, time) {
    return this.http.get(`${this.url}/api/v1/doctors/?date=${date}&time=${time}`);
  }

  storeSession(session) {
    return this.http.post(`${this.url}/api/v1/clients/session`, session);
  }

  login(credentials) {
    return this.http.post(`${this.url}/api/v1/login`, credentials).subscribe(
      res => {
        localStorage.setItem('token', res['data'].access_token);  
        this.router.navigate(['/list']);    
      }, error => {
        Swal.fire(
          'Error',
          error.error.data,
          'error'
        )
      });
  }

  logout() {
    localStorage.removeItem('token');
    this.router.navigate(['/admin']); 
  }

  getListSessions() {
    let token = localStorage.getItem('token');

    const headers = new HttpHeaders({
      'Authorization': 'Bearer ' + token,
      'Access-Control-Allow-Origin': '*'
    })

    return this.http.get(`${this.url}/api/v1/clients/sessions`, {headers: headers});
  }

  cancelSession(id) {

    let token = localStorage.getItem('token');

    const headers = new HttpHeaders({
      'Authorization': 'Bearer ' + token,
      'Access-Control-Allow-Origin': '*'
    })

    return this.http.delete(`${this.url}/api/v1/clients/session/cancel/${id}`, {headers: headers});
  }

  uploadClientExcel(file) {

    let token = localStorage.getItem('token');
    
    const headers = new HttpHeaders({
      'Authorization': 'Bearer ' + token,
      'Access-Control-Allow-Origin': '*'
    })
    
    return this.http.post(`${this.url}/api/v1/clients/imports`, file,{headers: headers});
  }

  getDoctorList() {
    let token = localStorage.getItem('token');

    const headers = new HttpHeaders({
      'Authorization': 'Bearer ' + token,
      'Access-Control-Allow-Origin': '*'
    })

    return this.http.get(`${this.url}/api/v1/doctors/list`, {headers: headers});
  }

  deleteDoctor(id) {

    let token = localStorage.getItem('token');

    const headers = new HttpHeaders({
      'Authorization': 'Bearer ' + token,
      'Access-Control-Allow-Origin': '*'
    })

    return this.http.delete(`${this.url}/api/v1/doctors/${id}`, {headers: headers});
  }
}
