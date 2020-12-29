import { Component, OnInit } from '@angular/core';
import { ClientService } from '../services/client.service';

@Component({
  selector: 'app-doctor-schedule',
  templateUrl: './doctor-schedule.component.html',
  styleUrls: ['./doctor-schedule.component.scss']
})
export class DoctorScheduleComponent implements OnInit {

  constructor(
    private clientService: ClientService,
  ) { }

  ngOnInit(): void {
  }

  logout() {
    this.clientService.logout();
  }

}
