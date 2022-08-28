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
  viewModal = new BehaviorSubject({});

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

  getClientList(search?)
  {
    let token = localStorage.getItem('token');

    const headers = new HttpHeaders({
      'Authorization': 'Bearer ' + token,
      'Access-Control-Allow-Origin': '*'
    })

    if (search) {

      search = `?search=${search?.search}`;
    } else {

      search = '';
    }

    return this.http.get(`${this.url}/api/v1/clients${search}`, {headers: headers});
  }

  getSessionsSchedule(date, time) {
    return this.http.get(`${this.url}/api/v1/doctors?date=${date}&time=${time}`);
  }

  storeSession(session) {
    return this.http.post(`${this.url}/api/v1/clients/session`, session);
  }

  getClientResults(dni) {
    return this.http.get(`${this.url}/api/v1/client/results/${dni}`);
  }

  login(credentials) {
    return this.http.post(`${this.url}/api/v1/login`, credentials).subscribe(
      res => {
        localStorage.setItem('token', res['data'].access_token);  
        localStorage.setItem('permission', res['data'].id);

        if (res['data'].id == 1) {
          this.router.navigate(['/list']);
        } else {
          this.router.navigate(['/upload']);
        }    
      }, error => {
        Swal.fire(
          'Error',
          error.error.data,
          'error'
        )
      });
  }

  listUserDoctor()
  {
    let token = localStorage.getItem('token');

    const headers = new HttpHeaders({
      'Authorization': 'Bearer ' + token,
      'Access-Control-Allow-Origin': '*'
    })

    return this.http.get(`${this.url}/api/v1/users`, {headers: headers});
  }

  storeUserDoctor(credentials)
  {
    let token = localStorage.getItem('token');

    const headers = new HttpHeaders({
      'Authorization': 'Bearer ' + token,
      'Access-Control-Allow-Origin': '*'
    })

    return this.http.post(`${this.url}/api/v1/doctors/create/user`, credentials, {headers: headers}).subscribe(
      res => {  
        Swal.fire(
          'Creado',
          'Usuario Creado con exito',
          'success'
        ).then(() => {
          location.reload();
        });   
      }, error => {
        Swal.fire(
          'Error',
          'el usuario ya existe',
          'error'
        )
      });
  }

  destroyUserDoctor(id)
  {
    let token = localStorage.getItem('token');

    const headers = new HttpHeaders({
      'Authorization': 'Bearer ' + token,
      'Access-Control-Allow-Origin': '*'
    })

    return this.http.delete(`${this.url}/api/v1/users/${id}`, {headers: headers}).subscribe(
      res => {  
        Swal.fire(
          'Eliminado',
          'Usuario eliminado',
          'success'
        ).then(() => {
          location.reload();
        });   
      }, error => {
        Swal.fire(
          'Error',
          'el usuario no existe',
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

  uploadClientResult(file, id) {

    let token = localStorage.getItem('token');
    
    const headers = new HttpHeaders({
      'Authorization': 'Bearer ' + token,
      'Access-Control-Allow-Origin': '*'
    })
    
    return this.http.post(`${this.url}/api/v1/results/upload/${id}`, file,{headers: headers});
  }

  getDoctorList() {
    let token = localStorage.getItem('token');

    const headers = new HttpHeaders({
      'Authorization': 'Bearer ' + token,
      'Access-Control-Allow-Origin': '*'
    })

    return this.http.get(`${this.url}/api/v1/doctors/list`, {headers: headers});
  }

  storeDoctor(data) {

    let token = localStorage.getItem('token');

    const headers = new HttpHeaders({
      'Authorization': 'Bearer ' + token,
      'Access-Control-Allow-Origin': '*'
    })

    return this.http.post(`${this.url}/api/v1/doctors`, data,{headers: headers}).subscribe(
      res => { 
        this.router.navigate(['/schedule']); 
        Swal.fire(
          'Guardado',
          'Doctor guardado con exito',
          'success'
        )   
      }, error => {
        Swal.fire(
          'Error',
          error.error.data,
          'error'
        )
      });
  }

  updateDoctor(id, data) {

    let token = localStorage.getItem('token');

    const headers = new HttpHeaders({
      'Authorization': 'Bearer ' + token,
      'Access-Control-Allow-Origin': '*'
    })

    return this.http.put(`${this.url}/api/v1/doctors/${id}`, data,{headers: headers}).subscribe(
      res => { 
        this.router.navigate(['/schedule']); 
        Swal.fire(
          'Guardado',
          'Doctor guardado con exito',
          'success'
        )   
      }, error => {
        Swal.fire(
          'Error',
          error.error.data,
          'error'
        )
      });
  }

  getDoctor(id) {

    let token = localStorage.getItem('token');

    const headers = new HttpHeaders({
      'Authorization': 'Bearer ' + token,
      'Access-Control-Allow-Origin': '*'
    });

    return this.http.get(`${this.url}/api/v1/doctors/${id}`, {headers: headers});
  }

  deleteDoctor(id) {

    let token = localStorage.getItem('token');

    const headers = new HttpHeaders({
      'Authorization': 'Bearer ' + token,
      'Access-Control-Allow-Origin': '*'
    });

    return this.http.delete(`${this.url}/api/v1/doctors/${id}`, {headers: headers});
  }

  destroyResult(id)
  {
    let token = localStorage.getItem('token');

    const headers = new HttpHeaders({
      'Authorization': 'Bearer ' + token,
      'Access-Control-Allow-Origin': '*'
    })

    return this.http.delete(`${this.url}/api/v1/results/${id}`, {headers: headers}).subscribe(
      res => {  
        Swal.fire(
          'Eliminado',
          'resultado eliminado',
          'success'
        ).then(() => {
          location.reload();
        });   
      }, error => {
        Swal.fire(
          'Error',
          'el resultado no existe',
          'error'
        )
      });
  }
}
