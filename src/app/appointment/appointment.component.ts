import { Component, OnInit } from '@angular/core';
import { ClientService } from '../services/client.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-appointment',
  templateUrl: './appointment.component.html',
  styleUrls: ['./appointment.component.scss']
})
export class AppointmentComponent implements OnInit {

  dni = ''

  constructor(
    private router: Router,
    private clientService: ClientService,
  ) { }

  ngOnInit(): void {
    this.clientService.dni.subscribe( res => {
      this.dni = res
    });

    if (this.dni == '') {
      this.router.navigate(['']);
    }
  }

}
