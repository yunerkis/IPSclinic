import { Component, OnInit } from '@angular/core';
import { ClientService } from '../services/client.service';
import { Router } from '@angular/router';
import { MatDialog } from '@angular/material/dialog';
import { ModalComponent } from '../modal/modal.component';

@Component({
  selector: 'app-appointment',
  templateUrl: './appointment.component.html',
  styleUrls: ['./appointment.component.scss']
})
export class AppointmentComponent implements OnInit {

  client: any = [];
  date: any;
  dateStart: any;
  timeStart: any;
  currentDate = new Date();
  nextDate = new Date(new Date().setDate(this.currentDate.getDate() + 2));
  selectedDate: any;
  schedules: any = [];
  msglog = '';

  minDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), this.currentDate.getDate());
  maxDate = new Date(this.nextDate.getFullYear(), this.nextDate.getMonth(), this.nextDate.getDate());

  constructor(
    private router: Router,
    private clientService: ClientService,
    public dialog: MatDialog
  ) { }

  openDialog() {
    const dialogRef = this.dialog.open(ModalComponent);

    dialogRef.afterClosed().subscribe(result => {
      console.log(`Dialog result: ${result}`);
    });
  }

  ngOnInit(): void {
    this.clientService.client.subscribe( res => {
      this.client = res;
      if (Object.keys(this.client).length  != 0) {
        this.dateStart = res['session'].date;
        this.timeStart = res['session'].time_start;
      } 
    });

    this.clientService.msg.subscribe(
      res => {this.msglog = res;}
    );

    if (Object.keys(this.client).length  == 0) {
      this.router.navigate(['']);
    }
  }

  onSelect(event) {
    this.selectedDate = event;
    this.date = new Date(event).getFullYear()+'-'+(new Date(event).getMonth()+1)+'-'+new Date(event).getDate();
    this.clientService.getSessionsSchedule(this.date).subscribe(res => {
      this.schedules = res['data'];
    })
  }

  session(schedule) {
    let session = {
      'dni': this.client.dni,
      'date': this.date,
      'time_start': schedule[1][0],
      'time_end': schedule[1][1],
      'schedule': schedule[0],
    };

    this.clientService.storeSession(session);
  }

}
