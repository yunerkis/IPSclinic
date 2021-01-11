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
  msglog: any;

  minDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), this.currentDate.getDate());
  maxDate = new Date(this.nextDate.getFullYear(), this.nextDate.getMonth(), this.nextDate.getDate());

  today = new Date();
  time = this.today.getHours() + ":" + this.today.getMinutes() + ":" + this.today.getSeconds();

  constructor(
    private router: Router,
    private clientService: ClientService,
    public dialog: MatDialog
  ) { }

  openDialog() {
    this.dialog.closeAll();
    const dialogRef = this.dialog.open(ModalComponent);

    dialogRef.afterClosed().subscribe(result => {
      console.log(`Dialog result: ${result}`);
    });
  }

  filterDates = (event: any) => {
    const date = event.getDay();
    if (date == 0) {

      return false;
    }

    return true;
  };

  ngOnInit(): void {
    this.onSelect(this.minDate);

    this.clientService.client.subscribe( res => {
      this.client = res;
      if (Object.keys(this.client).length  != 0) {
        this.dateStart = res['session'].date;
        this.timeStart = res['session'].time_start;
      } 
    });

    if (Object.keys(this.client).length  == 0) {
      this.router.navigate(['']);
    }
  }

  onSelect(event) {
    this.selectedDate = event;
    this.date = new Date(event).getFullYear()+'-'+("0" + (new Date(event).getMonth()+1)).slice(-2)+'-'+("0" + (new Date(event).getDate())).slice(-2);
    this.clientService.getSessionsSchedule(this.date, this.time).subscribe(res => {
      this.schedules = res['data'];
    })
  }

  session(schedule, doctorObj, hours) {
    
    let session = {
      'dni': this.client.dni,
      'date': this.date,
      'time': schedule,
      'doctor_id':doctorObj.id 
    };
    
    this.clientService.storeSession(session).subscribe( 
      res => {
        if (res) {
          this.msglog = {
            'type': 'success',
            'doctor': doctorObj.first_names+' '+doctorObj.last_names,
            'time': hours
          };
          this.clientService.modal.next(this.msglog)
          this.openDialog();
        }
      }, data => {
          this.msglog = {
            'type' : 'Error',
            'msg': 'Error, por favor volver a cargar la página.'
          };
          this.clientService.modal.next(this.msglog)
          this.openDialog();
          console.log(data);
      });
  }

}
